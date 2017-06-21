<?php
/** Accounts Tree
*
*/
class AccountsTree{
	static function loadMainLayout(){
		$layout = new stdClass();
		$menu = new stdClass();
		$ccs = Costcenters::getList();
		$menu->cc_lis = '';
		foreach($ccs as $cc){
			$menu->cc_lis .= write_html('li', '',
				write_html('a', 'action="openCC" rel="'.$cc->id.'" class="ui-state-default hoverable"', $cc->title)
			);
		}
		$layout->menu = fillTemplate('modules/accounts/templates/accounts_menu.tpl', $menu);
		return  fillTemplate('modules/accounts/templates/main_layout.tpl', $layout);
	}
	
	static function loadTreeLayout(){
		$layout = new stdClass();
		$main_accs = MainAccounts::getMainAccounts();
		$layout->tree = AccountsTree::getTree(0, false, $main_accs);
		return fillTemplate('modules/accounts/templates/tree_layout.tpl', $layout);	
	}

	static function printTree(){
		global $lang;
		$layout = new Layout();
		$main_accs = MainAccounts::getMainAccounts();
		foreach($main_accs as $acc){
			$trs[] = write_html('tr', '',
				write_html('td', '', $acc->title).
				write_html('td', '', Accounts::fillZero('main',$acc->code) . '00000')
			);
		}
		$answer['error'] = '';
		$answer['html'] = write_html('table', 'class="result"', 
			write_html('thead', '',
				write_html('tr', '',
					write_html('th', '', $lang['title']).
					write_html('th', '', $lang['code'])
				)
			).
			write_html('tbody', '',
				implode('', $trs)
			)
		);
		return json_encode($answer); 
	}
	
	static function getTree($level=0, $parent=false, $main_accs){
		global $lang, $prvlg;
		$out = array();
		foreach($main_accs as $index=>$acc){
			if( $acc->level == $level ){
				if($parent == false || strpos($acc->code, $parent) === 0){
					unset($main_accs[$index]);
					$out[] = write_html('h3', '', 
						write_html('a', 'action="openMainAccount" rel="'.$acc->code.'"', 
							$acc->title.
							($level < 5 && $prvlg->_chk('main_acc_add')?
								write_html('button', 'class="mini_circle_button ui-state-default hoverable" module="accounts" action="newMain" rel="'.$acc->code.'" level="'.$level.'" title="'.$lang['new'].'"', write_html('b', '','+'))
							: '')
						)
					).
					write_html('div', '', 
						AccountsTree::getTree($level+1, $acc->code, array_values($main_accs))
					);
				}
			}
		}
		return write_html('div', 'class="accordion"', 
			implode('', $out)
		);
	}
	
	static function getTreeList($level=0, $parent, $action=''){
		global $lang;
		global $this_system;
		$accms = $this_system->getAccms();
		$sql = "SELECT * FROM codes WHERE level ='$level'";
		if($parent != false && is_array($parent)){
			$sql .= ' AND (';
			foreach($parent as $p){
				$b = substr(strval($p), $level-1, 1);
				$e = substr_replace($p, intval($b)+1, $level-1,1);
				$psql[] = " ( code LIKE '$p%' OR (code<$e AND code>$p))";
			}
			$sql .= implode(' OR ', $psql).")";
		}
		//echo $sql;
		$accs = do_query_array($sql, $accms->database, $accms->ip);
		$out = array();
		foreach($accs as $acc){
			$out[] = write_html('h3', '', 
				write_html('a', 'action="'.$action.'" rel="'.$acc->code.'"', $acc->title)
			).
			write_html('div', '', 
				AccountsTree::getTreeList($level+1, array($acc->code), $action)
			);
		}
		return write_html('div', 'class="accordion"', 
			implode('', $out)
		);
	}

	static function loadSearchLayout(){
		global $this_system;
		$accms = $this_system->getAccms();
		$cur_year = $_SESSION['year'];
		$year = do_query_obj("SELECT begin_date, end_date FROM years WHERE year=$cur_year", $accms->database, $accms->ip);
		$layout = new stdClass();
		$layout->begin_date = $year->begin_date;
		$layout->end_date = $year->end_date;
		return  fillTemplate('modules/accounts/templates/accounts_search.tpl', $layout);
	}

	static function getTotalBalances(){
		global $lang;
		global $this_system;
		$accms = $this_system->getAccms();
		$year = $_SESSION['year'];
		$accounts = MainAccounts::getMainAccounts();
		$balance = new Layout();
		$balance->report_year = "$year / ". ($year+1) ;
		$balance->balance_rows= '';
		foreach($accounts as $main){
			$acc = new MainAccounts($main->code);			
			$balance->balance_rows .= $acc->getSubsTable(true, false);
		}
		$start = do_query_array("SELECT SUM(debit) AS debit, SUM(credit) AS credit, currency FROM start_balance  WHERE year=$year GROUP BY currency", $accms->database, $accms->ip);
		$trans = do_query_array("SELECT SUM(transactions_rows.debit) AS debit, SUM(transactions_rows.credit) AS credit, transactions.currency FROM transactions, transactions_rows WHERE year=$year AND transactions_rows.trans_id=transactions.id GROUP BY transactions.currency", $accms->database, $accms->ip);
		
		$tfoot_arr = array();
		$balance->tfoot ='';
		if($start != false){
			foreach($start as $acc){
				$tfoot_arr[$acc->currency]['start_debit'] = $acc->debit;
				$tfoot_arr[$acc->currency]['start_credit'] = $acc->credit;
			}
		}
		if($trans != false){
			foreach($trans as $acc){
				$tfoot_arr[$acc->currency]['trans_debit'] = $acc->debit;
				$tfoot_arr[$acc->currency]['trans_credit'] = $acc->credit;
			}
		}
		foreach($tfoot_arr as $cur=>$values){
			$values['start_debit'] = isset($values['start_debit']) ? $values['start_debit'] : 0;
			$values['start_credit'] = isset($values['start_credit']) ? $values['start_credit'] : 0;
			$values['trans_debit'] = isset($values['trans_debit']) ? $values['trans_debit'] : 0;
			$values['trans_credit'] = isset($values['trans_credit']) ? $values['trans_credit'] : 0;
			
			$total_debit = $values['start_debit'] + $values['trans_debit'];
			$total_credit = $values['start_credit'] + $values['trans_credit'];
			
			$dif = $total_debit - $total_credit;
			if($dif > 0){
				$funds_debdit = $dif;
				$funds_credit = '';
			} else {
				$funds_debdit = '';
				$funds_credit = $dif * -1;
			}
			$balance->tfoot .= write_html('tr', '',
				write_html('th', 'class="unprintable"', '&nbsp;').
				write_html('th', '', '&nbsp;').
				write_html('th', '', '&nbsp;').
				write_html('th', '', $cur).
				write_html('th', '', numberToMoney($values['start_debit'])).
				write_html('th', '', numberToMoney($values['start_credit'])).
				write_html('th', '', numberToMoney($values['trans_debit'])).
				write_html('th', '', numberToMoney($values['trans_credit'])).
				write_html('th', '', numberToMoney($total_debit)).
				write_html('th', '', numberToMoney($total_credit)).
				write_html('th', '', numberToMoney($funds_debdit)).
				write_html('th', '', numberToMoney($funds_credit))
			);
		}
		$egp_start_q = do_query_obj("SELECT SUM(debit * rate) AS debit, SUM(credit * rate) AS credit, currency FROM start_balance  WHERE year=$year", $accms->database, $accms->ip);

		$egp_trans_q = do_query_obj("SELECT SUM(transactions_rows.debit * transactions_rows.rate) AS debit, SUM(transactions_rows.credit * transactions_rows.rate) AS credit FROM transactions, transactions_rows WHERE year=$year AND transactions_rows.trans_id=transactions.id", $accms->database, $accms->ip);
		$egp_start = new stdClass();
		$egp_start->debit = $egp_start_q!= false ? $egp_start_q->debit : 0;			
		$egp_start->credit = $egp_start_q!= false ? $egp_start_q->credit : 0;
		$egp_trans = new stdClass();
		$egp_trans->debit = $egp_trans_q!= false ? $egp_trans_q->debit : 0;	
		$egp_trans->credit = $egp_trans_q!= false ? $egp_trans_q->credit : 0;
				
		$egp_total_debit = $egp_start->debit + $egp_trans->debit;
		$egp_total_credit = $egp_start->credit + $egp_trans->credit;
		
		$dif = $egp_total_debit - $egp_total_credit;
		if($dif > 0){
			$egp_funds_debdit = $dif;
			$egp_funds_credit = '';
		} else {
			$egp_funds_debdit = '';
			$egp_funds_credit = $dif * -1;
		}
		$balance->tfoot .= write_html('tr', '',
			write_html('th', 'class="unprintable"', '&nbsp;').
			write_html('th', '', $lang['total_egp']).
			write_html('th', '', '&nbsp;').
			write_html('th', '', 'EGP').
			write_html('th', '', numberToMoney($egp_start->debit)).
			write_html('th', '', numberToMoney($egp_start->credit)).
			write_html('th', '', numberToMoney($egp_trans->debit)).
			write_html('th', '', numberToMoney($egp_trans->credit)).
			write_html('th', '', numberToMoney($egp_total_debit)).
			write_html('th', '', numberToMoney($egp_total_debit)).
			write_html('th', '', numberToMoney($egp_funds_debdit)).
			write_html('th', '', numberToMoney($egp_funds_credit))
		);
		$balance->tfoot = write_html('tfoot', '', $balance->tfoot);
		$balance->template = 'modules/accounts/templates/total_balance_table.tpl';
		return $balance->_print();
		
	}
	
}