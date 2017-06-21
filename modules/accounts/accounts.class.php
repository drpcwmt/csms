<?php
/** Accounts
*
*/
class Accounts{
	
	public function __construct($code){
		global $this_system;
		$this->accms = $this_system->getAccms();
		$this->exists = true;
		if(strlen($code)<6 || (strlen($code)==10 && intval(substr($code,5,10))==0)){
			$this->type = "main";	
			$code = Accounts::removeZero($code);
			$acc = do_query_obj("SELECT * FROM codes WHERE code='$code'", $this->accms->database, $this->accms->ip );
			if($acc != false && $acc->code != ''){
				$this->exists = true;
				$this->main_code = $code;
				$this->sub_code	= 0;
				$this->full_code = Accounts::fillZero('main', $code).'00000';
				$this->title = $acc->title;
				$this->level = $acc->level;
				$this->currency = $this->accms->getSettings('def_currency');
				$this->notes = $acc->notes;
			} else {
				$this->exists = false;
				//echo "SELECT * FROM codes WHERE code='$code'";
			}
		} else {
			$this->type = "sub";
			$this->main_code = Accounts::removeZero(substr($code,0,5));
			$this->sub_code = intval(substr($code,5,10));
			$this->full_code = $code;
			$acc = do_query_obj("SELECT * FROM sub_codes WHERE main='$this->main_code' AND sub='$this->sub_code'", $this->accms->database, $this->accms->ip );
			if($acc != false && $acc->main != ''){
				$this->exists = true;
				$this->level = '';
				$this->title = $acc->title;
				$this->notes = $acc->notes;
				$this->cc = $acc->group_id;;
				$this->currency = $acc->currency!= '' ? $acc->currency : $this->accms->getSettings('def_currency');
				$this->damage = $acc->damage;
			} else {
				$this->title = $this->full_code;
				//echo "SELECT * FROM sub_codes WHERE main='$this->main_code' AND sub='$this->sub_code'";
				$this->exists = false;
			}
			
		}
	}
	
	
	public function loadLayout($extra_tab=array()){
		global $lang;
		$layout = new Layout($this);
		$layout->account_balance = $this->getBalance();
		if($this->type == 'main'){
		//	$layout->sub_table = $this->getSubsTable();
			$layout->main_code = Accounts::fillZero('main', $this->main_code);
			$layout->sub_code = Accounts::fillZero('sub', $this->sub_code);
			$layout->template ='modules/accounts/templates/accounts_main_layout.tpl';
		} else {
			$layout->extra_tab_li = '';
			$layout->extra_tab_div = '';
			foreach($extra_tab as $item){
				$layout->extra_tab_li .= $item['li'];
				$layout->extra_tab_div .= $item['div'];
			}
			if(substr(strval($this->main_code), 0, 2) == '11'){
				$layout->extra_tab_li .= write_html('li', '', write_html('a', 'href="#damage_tab"', $lang['damages']));
				$layout->extra_tab_div .= write_html('div', 'id="damage_tab"',
					Damages::LoadAccount($this->full_code)
				);
			}
				
			$layout->main_code_only = 'hidden';
			$layout->main_code = Accounts::fillZero('main', $this->main_code);
			$layout->sub_code = Accounts::fillZero('sub', $this->sub_code);
			$layout->currency_opts = Currency::getOptions($this->currency);
			$layout->transactions_table = $this->getTransactionsTable();
			$layout->ccs_opts = write_select_options(CostcentersGroup::getListOpts(), $this->cc, false);
			$layout->template = 'modules/accounts/templates/accounts_data.tpl';	
		}
		return $layout->_print();
	}

	public function getSubs($recursive=true){
		$sql = "SELECT * FROM sub_codes 
			WHERE main ='$this->main_code' ".
			($recursive ? " OR  main LIKE '$this->main_code%'" : '').
			" ORDER BY main ASC ,sub ASC";
		return do_query_array($sql, $this->accms->database, $this->accms->ip );	
	}
	
	public function getCurrencys(){
		if(!isset($this->currencys)){
			$this->currencys = array($this->currency);
			if($this->type == 'sub'){
				$start_bal = do_query_array("SELECT DISTINCT currency FROM start_balance WHERE  main_code=$this->main_code AND sub_code=".$this->sub_code, $this->accms->database, $this->accms->ip );
			} else {
				$start_bal = do_query_array("SELECT DISTINCT currency FROM start_balance WHERE  main_code LIKE '$this->main_code%'", $this->accms->database, $this->accms->ip );
			}
			foreach($start_bal as $s){
				if(!in_array($s->currency, $this->currencys)){
					$this->currencys[] = $s->currency;
				}
			}
			$trans_sql = "SELECT DISTINCT transactions.currency AS currency FROM transactions, transactions_rows WHERE transactions_rows.trans_id=transactions.id";
			if($this->type == 'sub'){
				$trans_sql .= " AND transactions_rows.main_code=$this->main_code AND transactions_rows.sub_code=$this->sub_code";		
			} else {
				$trans_sql .= " AND transactions_rows.main_code LIKE '$this->main_code%'";
			}
		 
			$transactions = do_query_array($trans_sql, $this->accms->database, $this->accms->ip );
			foreach($transactions as $t){
				if(!in_array($t->currency, $this->currencys)){
					$this->currencys[] = $t->currency;
				}
			}
		}
		return $this->currencys;
	}
	
	public function getStartBalance($cur=''){
		global $this_system;
		$year = $_SESSION['year'];
		if($cur != ''){
			$out = new stdClass();
			$out->currency = $cur;
			$start_bal = do_query_obj("SELECT credit, debit FROM start_balance WHERE year=$year AND main_code=$this->main_code AND sub_code=$this->sub_code AND currency='$cur'", $this->accms->database, $this->accms->ip );	
			if($start_bal!= false){	
				$out->credit = $start_bal->credit ;
				$out->debit = $start_bal->debit;
			} else {
				$out->credit = 0 ;
				$out->debit = 0;
			}
		} else {
			$start_bal =  do_query_array("SELECT credit, debit, currency FROM start_balance WHERE year=$year AND main_code=$this->main_code AND sub_code=$this->sub_code", $this->accms->database, $this->accms->ip );	
			if($start_bal!= false){	
				$out = $start_bal;
			} else {
				$bal = new stdClass();
				$bal->currency = $this_system->getSettings('def_currency');
				$bal->credit = 0 ;
				$bal->debit = 0;
				$out = array($bal);
			}
		}
		return $out;
	}
	
	public function getBalance($tr_only=false){
		$year = $_SESSION['year'];
		$lines = array();
		$acc_arr = array();
		
		if($this->type =='main'){
			$start = do_query_array("SELECT SUM(debit) AS debit, SUM(credit) AS credit, currency FROM start_balance WHERE year=$year AND main_code LIKE '$this->main_code%' GROUP BY currency", $this->accms->database, $this->accms->ip );
			$trans = do_query_array("SELECT SUM(transactions_rows.debit) AS debit, SUM(transactions_rows.credit) AS credit, transactions.currency FROM transactions, transactions_rows WHERE year=$year AND transactions_rows.trans_id=transactions.id AND transactions_rows.main_code LIKE '$this->main_code%' GROUP BY transactions.currency", $this->accms->database, $this->accms->ip );
		} else {
			$start = do_query_array("SELECT SUM(debit) AS debit, SUM(credit) AS credit, currency FROM start_balance WHERE year=$year AND main_code = '$this->main_code' AND sub_code = '$this->sub_code' GROUP BY currency", $this->accms->database, $this->accms->ip );
			$trans = do_query_array("SELECT SUM(transactions_rows.debit) AS debit, SUM(transactions_rows.credit) AS credit, transactions.currency FROM transactions, transactions_rows WHERE year=$year AND transactions_rows.trans_id=transactions.id AND transactions_rows.main_code = '$this->main_code' AND transactions_rows.sub_code = '$this->sub_code' GROUP BY transactions.currency", $this->accms->database, $this->accms->ip );
		}
		
		if($start != false){
			foreach($start as $acc){
				$acc_arr[$acc->currency]['start_debit'] = $acc->debit;
				$acc_arr[$acc->currency]['start_credit'] = $acc->credit;
			}
		}
		if($trans != false){
			foreach($trans as $acc){
				$acc_arr[$acc->currency]['trans_debit'] = $acc->debit;
				$acc_arr[$acc->currency]['trans_credit'] = $acc->credit;
			}
		}
		foreach($acc_arr as $cur=>$values){
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
			$lines[] = write_html('tr', '',
				write_html('td', '', $cur).
				write_html('td', '', numberToMoney($values['start_debit'])).
				write_html('td', '', numberToMoney($values['start_credit'])).
				write_html('td', '', numberToMoney($values['trans_debit'])).
				write_html('td', '', numberToMoney($values['trans_credit'])).
				write_html('td', '', numberToMoney($total_debit)).
				write_html('td', '', numberToMoney($total_credit)).
				write_html('td', '', numberToMoney($funds_debdit)).
				write_html('td', '', numberToMoney($funds_credit))
			);
		}
		if($tr_only){
		//	echo implode('', $lines);
			return implode('', $lines);
		}
		$balance = new stdClass();
		$balance->balance_rows = implode('', $lines);
		return fillTemplate('modules/accounts/templates/accounts_balance.tpl', $balance);			
	}	
	
	public function getTransactionsTable($begin='', $end='', $row_only=false){
		global $lang;
		$layout = new Layout($this);
		$layout->begin_date = $begin !='' ? dateToUnix($begin) : getYearSetting('begin_date');
		$layout->end_date = $end!='' ? dateToUnix($end) : getYearSetting('end_date');
		
		$sql = "SELECT transactions.*, 
			SUM(transactions_rows.debit) AS debit, 
			SUM(transactions_rows.credit) AS credit, 
			GROUP_CONCAT(transactions_rows.notes SEPARATOR '<br/>') AS notes 
			FROM transactions, transactions_rows 
			WHERE transactions_rows.main_code='$this->main_code'
			AND transactions_rows.sub_code=$this->sub_code 
			AND transactions.date>=$layout->begin_date 
			AND transactions.date<=$layout->end_date 
			AND transactions.id=transactions_rows.trans_id
			GROUP BY transactions_rows.trans_id
			ORDER BY transactions.id DESC";
		$trans = do_query_array($sql, $this->accms->database, $this->accms->ip);
		if($trans != false && count($trans) > 0){
			$layout->trs = '';
			foreach($trans as $tran){
				$row = new Layout($tran);
				$row->template = 'modules/accounts/templates/trans_row.tpl';
				//$row->notes = str_replace('<br/>', '&#13;&#10;', $tran->notes);
				$layout->trs .= $row->_print();
			}
			if($row_only){
				return $layout->trs;
				$layout->form_hidden='hidden';
			} 			
			$layout->tfoot = '';
			$totals = $this->getTotal('', $layout->begin_date, $layout->end_date);
			foreach($totals as $tr){
				$layout->tfoot .= write_html('tr', '',
					write_html('th', 'class="{sorter:false}"', '&nbsp;').
					write_html('th', '', '&nbsp;').
					write_html('th', '', $tr->debit).
					write_html('th', '', $tr->credit).
					write_html('th', '', $tr->currency). 
					write_html('th', '', unixToDate($layout->end_date)).
					write_html('th', '', '&nbsp;')
				);
			}
			$layout->template = 'modules/accounts/templates/trans_table.tpl';
			
			return $layout->_print();
		} else {
			if($row_only){
				return write_html('tr', '',
					write_html('td', 'colspan="7"', write_error($lang['result_not_found']))
				);
			} else {
				$layout->template = 'modules/accounts/templates/trans_table.tpl';
				$layout->trs = write_html('tr', '',
					write_html('td', 'colspan="7"', write_error($lang['result_not_found']))
				);
				return $layout->_print();
			}
		}
	}
	
	public function getTotal($cur='', $begin='', $end=''){
		if($cur != '0'){
			$sql = "SELECT SUM(transactions_rows.debit) AS debit, 
				SUM(transactions_rows.credit) AS credit, 
				transactions.currency AS currency
				FROM transactions, transactions_rows 
				WHERE transactions_rows.trans_id=transactions.id
				AND transactions.date>=$begin 
				AND transactions.date<=$end ";
		} else {
			$sql = "SELECT SUM(transactions_rows.debit * transactions_rows.rate) AS debit, 
				SUM(transactions_rows.credit * transactions_rows.rate) AS credit, 
				transactions.currency AS currency
				FROM transactions, transactions_rows 
				WHERE transactions_rows.trans_id=transactions.id
				AND transactions.date>=$begin 
				AND transactions.date<=$end ";
		}
		
		if($this->type == 'sub'){
			$sql .= " AND transactions_rows.main_code = '$this->main_code' AND transactions_rows.sub_code=$this->sub_code";		
		} else {
			$sql .= " AND (transactions_rows.main_code LIKE '$this->main_code%' OR transactions_rows.main_code = '$this->main_code')";
		}

		if($cur != '' && $cur != '0'){ 
			$sql .= " AND transactions.currency='$cur'";
		} elseif($cur != '0'){
			$sql .= " GROUP BY transactions.currency";
			
		}
		//echo $sql;
		return do_query_array($sql , $this->accms->database, $this->accms->ip );
		
	}
	
	static function saveSubCode($post){
		global $this_system;
		$accms = $this_system->getAccms();
		$old_acc = new Accounts($post['code']);
		if(!isset($post['currency'])){ $post['currency'] = $this_system->getSettings('def_currency'); }
		$post['main'] = Accounts::removeZero($post['main_code']);
		$post['sub'] = intval($post['sub_code']);
		
		if($post['main']!=$old_acc->main_code || $post['sub']!=$old_acc->sub_code){
			$chk_exist = do_query_obj("SELECT  * FROM sub_codes WHERE main='".$post['main']."' AND sub=".$post['sub']." ORDER BY sub DESC LIMIT 1", $accms->database, $accms->ip);
			if($chk_exist != false){
				$post['sub'] = $chk_exist->sub +1;	
			}
		}
		
		if(do_update_obj( $post, "main='".$old_acc->main_code."' AND sub=".$old_acc->sub_code, "sub_codes", $accms->database, $accms->ip) != false){
			if($post['main']!=$old_acc->main_code || $post['sub']!=$old_acc->sub_code){
				$arr = array(
					'main_code'=>$post['main'],
					'sub_code'=>$post['sub']
				);
				do_update_obj( $arr, "main_code='".$old_acc->main_code."' AND sub_code=".$old_acc->sub_code, "start_balance", $accms->database, $accms->ip);
				do_update_obj( $arr, "main_code='".$old_acc->main_code."' AND sub_code=".$old_acc->sub_code,"transactions_rows", $accms->database, $accms->ip);
			}
			$answer['main'] = Accounts::fillZero('main',$post['main']);
			$answer['sub'] = Accounts::fillZero('sub',$post['sub']);
			$answer['error'] = '' ;
		} else {
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
		
	static function newAccount($parent, $level=false){
		global $this_system, $lang;
		$accms = $this_system->getAccms();
		$layout = new Layout();
		$layout->acc_code_main = Accounts::fillZero('main', $parent);
		$existed = do_query_obj( "SELECT sub FROM sub_codes WHERE main='".Accounts::removeZero($parent)."' ORDER BY sub DESC LIMIT 1", $accms->database, $accms->ip);
		$value = $existed != false && $existed->sub != '' ? $existed->sub +1 : 1;
		$layout->value = Accounts::fillZero('sub', $value);
		$layout->ccs_opts = write_select_options(CostcentersGroup::getListOpts(), '', false);
		$layout->currency_hidden = '';
		$layout->currency_opts = Currency::getOptions($accms->getSettings('def_currency'));
		
		if(in_array(substr($parent,0,1), array('1'))){
			$layout->damage_hidden = '';
		} else {
			$layout->damage_hidden = 'hidden';
		}
		
		$layout->template = 'modules/accounts/templates/accounts_new.tpl';
		return $layout->_print();
	}
	
	static function saveNewAcc($post){
		global $lang, $this_system;
		$accms = $this_system->getAccms();
		$main_code =  Accounts::removeZero($post['precode']);
		$sub_code = Accounts::removeZero($post['acc_code_sub']);
		$chk_exist = do_query_array("SELECT sub FROM sub_codes WHERE main ='$main_code' AND sub='$sub_code'", $accms->database, $accms->ip);
		if($chk_exist != false && count($chk_exist) > 0){
			$answer['error'] = $lang['account_allready_exists'];
		} else {
			$ins = array(
				'main'=> $main_code,
				'sub'=> $sub_code,
				'title'=> $post['title'],
				'currency'=> isset($post['currency']) ? $post['currency'] : $accms->getSettings('def_currency'),
				'damage'=> isset($post['damages']) ? $post['damages'] : '0',
				'ccid'=> $post['group_id'],
				'notes'=> isset($post['notes']) ? $post['notes']: ''
			);
			if(do_insert_obj($ins, 'sub_codes', $accms->database, $accms->ip) !=false){
				// start balance
				if(substr($main_code,0,1)=== 1 || substr($main_code,0,1)=== 2){
						$start = array(
							'main_code'=> $main_code,
							'sub_code'=> $sub_code,
							'credit'=> isset($post['type']) && $post['type']=='credit' ? $post['value'] : 0,
							'debit'=> isset($post['type']) && $post['type']=='debit' ? $post['value'] : 0,
							'year'=> $_SESSION['year'],
							'currency'=> isset($post['currency']) ? $post['currency'] : $accms->getSettings('def_currency')
						);
						do_insert_obj($start, 'start_balance', $accms->database, $accms->ip);
					
					// Assets
					if(isset($post['damage_acc']) && $post['damages'] > 0){
						// Damages
						$damage = array(
							'main'=> substr_replace($main_code, '34',0,2),
							'sub'=> $sub_code,
							'title'=> $lang['damage_expense'].$post['title'],
							'currency'=>isset($post['currency']) ? $post['currency'] : $accms->getSettings('def_currency'),
							'cc'=> $post['cc'],
							'notes'=> ''
						);
						do_insert_obj($damage, 'sub_codes', $accms->database, $accms->ip);
						
						$total_damage = array(
							'main'=> substr_replace($main_code, '22',0,2),
							'sub'=> $sub_code,
							'title'=> $lang['damage_total'].$post['title'],
							'currency'=> isset($post['currency']) ? $post['currency'] : $accms->getSettings('def_currency'),
							'cc'=> $post['cc'],
							'notes'=> ''
						);
						do_insert_obj($total_damage, 'sub_codes', $accms->database, $accms->ip);
						
						$start_damage = array(
							'main_code'=> substr_replace($main_code, '22',0,2),
							'sub_code'=> $sub_code,
							'credit'=> $post['damage_total'],
							'debit'=>  0,
							'year'=> $_SESSION['year']
						);
						do_insert_obj($start, 'start_balance', $accms->database, $accms->ip);
					}
				}
				$full_code = Accounts::fillZero('main', $main_code).Accounts::fillZero('sub', $sub_code);
				$answer['error'] = "";
				$answer['tr'] =  write_html('tr', '',
					write_html('td', '" width="20" class="unprintable"',
						write_html('button', 'class="ui-state-default hoverable circle_button" action="openSubAcc" code="'.$full_code.'" module="accounts"', write_icon('extlink'))
					).
					write_html('td', '', $post['title']).
					write_html('td', '', $full_code).
					write_html('td', '', isset($post['currency']) ? $post['currency'] : $accms->getSettings('def_currency')).
					write_html('td', '', numberToMoney(isset($post['type']) && $post['type']=='debit' ? $post['value'] : 0)).
					write_html('td', '', numberToMoney(isset($post['type']) && $post['type']=='credit' ? $post['value'] : 0)).
					write_html('td', '', '&nbsp;').
					write_html('td', '', '&nbsp;').
					write_html('td', '', numberToMoney(isset($post['type']) && $post['type']=='debit' ? $post['value'] : 0)).
					write_html('td', '', numberToMoney(isset($post['type']) && $post['type']=='credit' ? $post['value'] : 0)).
					write_html('td', '', numberToMoney(isset($post['type']) && $post['type']=='debit' ? $post['value'] : 0)).
					write_html('td', '', numberToMoney(isset($post['type']) && $post['type']=='credit' ? $post['value'] : 0))
				);
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		}
		return json_encode($answer);
	}
	
	static function fillZero($type, $value){
		$count = strlen($value);
		for($i=0; $i<(5-$count); $i++){
			$value = $type=='main'? $value.'0' : '0'.$value;
		}
		return $value;
	}
	
	static function removeZero($value){
		if(intval($value)>0 && substr(strval($value), -1) == '0'){
		 	$value = substr($value, 0, -1);
			return Accounts::removeZero($value);
		} else {
			return $value;
		}
	}
	
	static function autocomplete($term, $param=''){
		global $this_system;
		$accms = $this_system->getAccms();
		include_once('scripts/I18N/Arabic.php');
		$Arabic = new I18N_Arabic('KeySwap');
		$out = array();

		$accounts = do_query_array("SELECT * FROM sub_codes
		 WHERE (title LIKE '$term%'
		 OR title LIKE '%$term%'
		 OR title LIKE '".$Arabic->swapEa($term)."%'
		 OR title LIKE '%".$Arabic->swapEa($term)."%')
		 $param",  $accms->database, $accms->ip);
		foreach($accounts as $acc){
		//	print_r($acc);
			$main_code = substr($acc->main,0,2);
			$main_acc = new Accounts($main_code);
			$out[] = array('title'=>$acc->title .' - '. $main_acc->title, 'main_code'=> Accounts::fillZero('main', $acc->main), 'sub_code'=> Accounts::fillZero('sub', $acc->sub));
		} 
		return json_encode($out);
	}
	
	public function _delete(){
		global $lang;
		$year = getYear();
		$acc = new Accounts($this->full_code);
		$result = true;
		if($this->type =='main'){
			$subs = $this->getSubs(true);
			if($subs != false && count($subs > 0)){
				foreach($subs as $sub){
					$sub_acc = new Accounts(Accounts::fillZero('main', $sub->main).Accounts::fillZero('sub',$sub->sub));
					if($sub_acc->_delete() == false){
						$result = false;
						break;
					}
				}
			}
			if($result){
				return do_delete_obj("code=$this->main_code", 'codes', $this->accms->database, $this->accms->ip);
			} else {
				return $lang['error'];
			}
				
		} else {
			$start_bal = $acc->getStartBalance();
			if(count($start_bal) > 0 && ($start_bal[0]->credit>0 || $start_bal[0]->debit>0)){
				$result = false;
				$error = $lang['acc_start_bal_exists'];
			}
			$sql = "SELECT transactions.id FROM transactions, transactions_rows 
				WHERE transactions_rows.main_code='$this->main_code'
				AND transactions_rows.sub_code=$this->sub_code 
				AND transactions.date>=$year->begin_date
				AND transactions.id=transactions_rows.trans_id LIMIT 1";
			$trans =  do_query_obj($sql, $this->accms->database, $this->accms->ip);
			if($trans!==false){
				$result = false;
				$error = $lang['acc_trans_exists'];
			}
			if($result){
				return do_delete_obj("main=$this->main_code AND sub=$this->sub_code", 'sub_codes', $this->accms->database, $this->accms->ip);
			} else {
				return $error;
			}
		}
	}
}
