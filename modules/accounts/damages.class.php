<?php
/** Damages Report
*
*/
class Damages{
	public function __construct($year=''){
		global $this_system;
		$this->year = $year!= '' ? $year : $_SESSION['year'];
		$y = getYear($this->year);
		$this->begin_date = $y->begin_date;
		$this->end_date = $y->end_date;		
	}

	static function loadMainLayout(){
		global $lang;
		$layout = new Layout();
		$year = $_SESSION['year'];
		$layout->tree = AccountsTree::getTreeList(2, array('111'), 'openAccDamages');
		$mains = do_query_array("SELECT code FROM codes WHERE level=2 AND code LIKE '11%'");
		$total = 0;
		$trs = array();
		foreach($mains as $main){
			$acc= new MainAccounts($main->code);
			$damage = do_query_obj("SELECT SUM(value) AS tot FROM damages WHERE main LIKE '$main->code%' AND year=$year");
			$trs[] = write_html('tr', '',
				write_html('td', '', $acc->full_code).
				write_html('td', '', $acc->title).
				write_html('td', '', numberToMoney($damage->tot))
			);
			$total += $damage->tot;
		}
		$layout->template = 'modules/balance/templates/damages_main.tpl';
		$layout->trs = implode('', $trs);
		$layout->tfoot = write_html('tr', '',
			write_html('th', '', '&nbsp;').
			write_html('th', '', $lang['total']).
			write_html('th', '', numberToMoney($total))
		);
		return $layout->_print();
	}

	static function loadMainAccount($main){
		global $lang;
		$layout = new Layout();
		$layout->template = 'modules/balance/templates/damages.tpl';
		$layout->rows = '';
		$main_acc = new MainAccounts($main);
		$layout->acc_title = $main_acc->title; 
		$subs = $main_acc->getSubs();
		$year = $_SESSION['year'];
		$full_totals = array();
		$total_year = array();
		foreach($subs as $sub){
			$acc = new Accounts(Accounts::fillZero('main', $sub->main).Accounts::fillZero('sub', $sub->sub));
			$currencys = $acc->getCurrencys();
			foreach($currencys as $cur){
				if(!isset($full_totals[$cur])){
					$full_totals[$cur] = 0;
				}
				$start = do_query_obj("SELECT * FROM start_balance WHERE year=".$_SESSION['year']." AND main_code='$acc->main_code' AND sub_code='$acc->sub_code' AND currency='$cur'",  MySql_Database);
				$damage_code = substr_replace($acc->full_code, '22',0,2);
				$damage_acc = new Accounts($damage_code);

				$damage = do_query_obj("SELECT * FROM start_balance WHERE year=$year AND main_code='$damage_acc->main_code' AND sub_code='$damage_acc->sub_code' AND currency='$cur'", MySql_Database);

				$saved = do_query_obj("SELECT * FROM damages WHERE year=$year AND main=$acc->main_code AND sub=$acc->sub_code AND currency='$cur'");

				$trans = do_query_obj("SELECT SUM(transactions_rows.debit*transactions_rows.rate) AS debit, SUM(transactions_rows.credit*transactions_rows.rate) AS credit FROM transactions, transactions_rows WHERE transactions_rows.main_code=$acc->main_code AND transactions_rows.sub_code=$acc->sub_code AND transactions_rows.year=$year AND transactions_rows.trans_id=transactions.id AND transactions.currency='$cur'");
				
				$acc_value = ($start != false ? $start->debit: 0) + $trans->debit - $trans->credit;
				if($saved != false){
					$this_year_damage = $saved->value;
					$total = $this_year_damage + ($damage!=false ? $damage->credit : 0);
					$net =  $acc_value - $total > 0 ?  $acc_value - $total : 0;
					if($net == 0){
						$total = $acc_value - ($damage!=false ? $damage->credit : 0);
					}
				} else {
					if($damage== false || $acc_value - $damage->credit > 0){
						$cur_year_damage = Damages::calcDamage($sub);
						$start_value = $start!=false? ($start->debit * $acc->damage/100) : 0;
						$this_year_damage = $cur_year_damage + $start_value;
						$total = $this_year_damage + ($damage!=false ? $damage->credit : 0);
						$net =  $acc_value - $total > 0 ?  $acc_value - $total : 0;
						if($net == 0){
							$total = $acc_value - ($damage!=false ? $damage->credit : 0);
						}
					} else {
						$this_year_damage = 0;
						$total= 0;
						$net = 0;
					}
				}
				if(!isset($total_year[$cur])){
					$total_year[$cur] = 0;
					$full_totals[$cur] = 0;
				}
				$total_year[$cur] += $this_year_damage;
				$full_totals[$cur] += $total;
				$acc->start = numberToMoney($acc_value);
				$acc->start_damage = numberToMoney($damage!=false ? $damage->credit : 0);
				$acc->trans = round($this_year_damage,2);
				$acc->currency = $cur;
				$acc->percent = $acc->damage;
				$acc->total = numberToMoney($total);
				$acc->net = $net> 0 ?  write_html('b','', numberToMoney($net)) : write_html('b', 'style="color:red"', 0);
				$layout->rows .= fillTemplate("modules/balance/templates/damages_rows.tpl", $acc);
			}
		}	
		foreach($full_totals as $cur =>$tot){
			$tfoots[] = write_html('tr', '',
				write_html('th', 'colspan="3"', $lang['total']).
				write_html('th', '', $cur).
				write_html('th', '', '&nbsp;').
				write_html('th', '', '&nbsp;').
				write_html('th', '',  numberToMoney($total_year[$cur])).
				write_html('th', '', '&nbsp;').
				write_html('th', '', numberToMoney($tot)).
				write_html('th', '', '&nbsp;')
			);
		}
		$layout->tfoot = implode('', $tfoots);
		return $layout->_print();
	}
	
	static function loadAccount($acc_code){
		global $lang;
		$acc = getAccount($acc_code);
		$tot_damage = $acc->getDamageAcc();
		$year = getYear($_SESSION['year']);
		$layout = new Layout($tot_damage);
		$total = 0;
		$layout->template = 'modules/balance/templates/acc_damage.tpl';
		$trs = array();
			// Start Balance
		$starts = do_query_array("SELECT * FROM start_balance WHERE main_code=$acc->main_code AND sub_code=$acc->sub_code AND year=$year->year");
		foreach($starts as $start){
			$trs[] = write_html('tr', '',
				write_html('td', '', numberToMoney($start->debit)).
				write_html('td', '', unixToDate($year->begin_date)).
				write_html('td', '', $lang['start_balance']).
				write_html('td', '', ($year->end_date - $year->begin_date) / 86400).
				write_html('td', '', $start->currency).
				write_html('td', '', ($start->currency!='EGP' ? $start->debit*$acc->damage/100 : '&nbsp;')).
				write_html('td', '', ($start->debit*$acc->damage/100)*$start->rate)
			);
			$total += ($start->debit*$acc->damage/100)*$start->rate;
		}
			// Transactions
		$trans = do_query_array("SELECT transactions.*,transactions_rows.* FROM transactions,transactions_rows WHERE transactions_rows.main_code=$acc->main_code AND transactions_rows.sub_code=$acc->sub_code AND transactions_rows.year=$year->year AND transactions_rows.trans_id=transactions.id");
		
		foreach($trans as $t){
			$val = $t->debit > 0 ? $t->debit : ($t->credit * -1);
			$damg = (($year->end_date - $t->date) / ($year->end_date-$year->begin_date))* ($acc->damage/100) * $val;
			$trs[] = write_html('tr', '',
				write_html('td', '', numberToMoney($val)).
				write_html('td', '', unixToDate($t->date)).
				write_html('td', '', $t->notes).
				write_html('td', '',($year->end_date - $t->date) / 86400).
				write_html('td', '', $t->currency).
				write_html('td', '', ($t->currency!='EGP' ? $damg : '&nbsp;')).
				write_html('td', '', numberToMoney($damg*$t->rate))
			);
			$total += $damg * $t->rate;
		}
		
		$layout->trs = implode('', $trs);
		
		$layout->total = numberToMoney($total);
		
		$new_total = $total;
		$tfoot_trs = array();
		$starts_damage = do_query_array("SELECT * FROM start_balance WHERE main_code=$tot_damage->main_code AND sub_code=$tot_damage->sub_code AND year=$year->year");
		foreach($starts_damage as $start){
			$tfoot_trs[] = write_html('tr', '',
				write_html('th', 'colspan="4"', $tot_damage->title).
				write_html('th', '', $start->currency).
				write_html('th', '', ($start->currency!='EGP' ? numberToMoney($start->credit) : '&nbsp;')).
                write_html('th', '', numberToMoney($start->credit*$start->rate))
			);
			$new_total += $start->credit*$start->rate;
		}
		$layout->damage_start_trs = implode('', $tfoot_trs);
		$layout->final_total = numberToMoney($new_total);
		return $layout->_print();
	}
	
	static function calcDamage($acc, $year=''){
		$total = 0;
		if($year==''){
			$year = getYear();
		}
		$trans = do_query_array("SELECT transactions.*,transactions_rows.* FROM transactions,transactions_rows WHERE transactions_rows.main_code=$acc->main AND transactions_rows.sub_code=$acc->sub AND transactions_rows.year=$year->year AND transactions_rows.trans_id=transactions.id");
		foreach($trans as $t){
			$val = $t->debit > 0 ? $t->debit : ($t->credit * -1);
			$damg = (($year->end_date - $t->date) / ($year->end_date-$year->begin_date))* ($acc->damage/100) * $val;
			$trs[] = write_html('tr', '',
				write_html('td', '', numberToMoney($val)).
				write_html('td', '', unixToDate($t->date)).
				write_html('td', '', $t->notes).
				write_html('td', '',($year->end_date - $t->date) / 86400).
				write_html('td', '', $t->currency).
				write_html('td', '', ($t->currency!='EGP' ? $damg : '&nbsp;')).
				write_html('td', '', numberToMoney($damg*$t->rate))
			);
			$total += $damg * $t->rate;
		}
		return $total;
	}
	
	static function saveDamages($post){
		$year = $_SESSION['year'];
		$result = true;
		for($i=0; $i<count($post['acc']); $i++){
			$acc = new Accounts($post['acc'][$i]);
			$currency = $post['currency'][$i];
			if(do_query_obj("SELECT * FROM damages WHERE year=$year AND main=$acc->main_code AND sub=$acc->sub_code AND currency='$currency'")!= false){
				$update = array('value'=>$post['values'][$i]);
				if(!do_update_obj($update, "year=$year AND main=$acc->main_code AND sub=$acc->sub_code AND currency='$currency'", 'damages')){
					$result = false;
				}
			} else {
				$insert = array(
					'year'=>$year,
					'main'=>$acc->main_code,
					'sub'=>$acc->sub_code,
					'value'=>$post['values'][$i],
					'currency'=>$post['currency'][$i]
				);
				if(!do_insert_obj($insert, 'damages')){
					$result = false;
				}
			}
		}
		return $result;
	}
	
	static function createTransaction(){
		global $this_system;
		$year =getYear();
		$rows = do_query_array("SELECT * FROM damages WHERE value>0 AND year=$year->year");
		$trans = array();
		$trans['approve'] = 1;
		$trans['user_id'] = 0;
		$trans['date'] = $year->end_date;
		$trans['currency'] = $this_system->getSettings('def_currency');
		$curs = do_query_array("SELECT DISTINCT currency FROM damages WHERE year=$year->year");
		$rates = array();
		foreach($curs as $cur){
			$rates[$cur->currency] = Currency::convertRate($cur->currency, $trans['currency'], $year->end_date);
		}
		foreach($rows as $row){
			$acc = new Accounts(Accounts::fillZero('main', $row->main).Accounts::fillZero('sub', $row->sub));
			$damage_acc = substr_replace($acc->main_code, '22',0,2);
			$expense = substr_replace($acc->main_code, '34',0,2);
			$trans['debit'][] =$row->value * $rates[$row->currency];
			$trans['credit'][]= '';
			$trans['acc_code_main'][]= $expense;
			$trans['acc_code_sub'][]= $row->sub;
			$trans['acc_code_cc'][]= $acc->cc;
			$trans['year'][]= $year->year;
			$trans['notes'][]= $row->currency != 'EGP' ? $row->value.' '.$row->currency.' '. $rates[$row->currency] : '';
			$trans['rate'][]= $rates[$row->currency];
			
			$trans['debit'][] = '';
			$trans['credit'][]= $row->value;
			$trans['acc_code_main'][]= $damage_acc;
			$trans['acc_code_sub'][]= $row->sub;
			$trans['acc_code_cc'][]= $acc->cc;
			$trans['year'][]= $year->year;
			$trans['notes'][]= $row->currency != 'EGP' ? $row->value.' '.$row->currency.' '. $rates[$row->currency]: '';
			$trans['rate'][]= $rates[$row->currency];
		}
		return Settlements::_save($trans);
	}
}
	