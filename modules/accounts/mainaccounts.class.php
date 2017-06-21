<?php
/** Accounts Tree
*
*/

class MainAccounts extends Accounts{
	
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
				$this->notes = $acc->notes;
			} else {
				$this->exists = false;
				//echo "SELECT * FROM codes WHERE code='$code'";
			}
		}
	}

	public function getSubs($recursive=true){
		$sql = "SELECT * FROM sub_codes 
			WHERE main ='$this->main_code' ".
			($recursive ? " OR  main LIKE '$this->main_code%'" : '').
			" ORDER BY main ASC ,sub ASC";
		return do_query_array($sql, $this->accms->database, $this->accms->ip );	
	}

	public function getSubsTable($tr_only = false, $recursive=true){
		$accs = $this->getSubs($recursive);
		$year = $_SESSION['year'];
		$main_code = $this->main_code;
		$lines = array();
		$passed = array();
		foreach($accs as $a){ 
			$acc = getAccount($a->main, $a->sub);
			//$acc = new Accounts($a->full_code);
			$sql =  "SELECT SUM(transactions_rows.credit) AS credit, 
				SUM(transactions_rows.debit) AS debit,
				transactions.currency
				FROM transactions, transactions_rows 
				WHERE transactions_rows.year=$year 
				AND transactions_rows.main_code = '$acc->main_code'
				AND transactions_rows.sub_code=$acc->sub_code 
				AND transactions_rows.trans_id= transactions.id 
				GROUP BY transactions.currency";
			$rows = do_query_array($sql, $this->accms->database, $this->accms->ip );
			
			if($rows != false && count($rows) > 0){
				foreach($rows as $row){
					$start_bal = $acc->getStartBalance($row->currency);	
					$acc->start_bal_credit = $start_bal!= false ? $start_bal->credit : 0;
					$acc->start_bal_debit = $start_bal!= false ? $start_bal->debit : 0;			
					$acc->transactions_credit =  $row->credit;
					$acc->transactions_debit = $row->debit;
					$acc->total_debit = $acc->start_bal_debit + $acc->transactions_debit;
					$acc->total_credit = $acc->start_bal_credit + $acc->transactions_credit;
				
					$dif = $acc->total_debit - $acc->total_credit;
					if($dif >= 0){
						$acc->final_bal_debit = $dif;
						$acc->final_bal_credit = '';
					} else {
						$acc->final_bal_debit = '';
						$acc->final_bal_credit = $dif *-1;
					}
					$lines[] = write_html('tr', '',
						(!in_array($acc->full_code, $passed)?
							write_html('td', 'rowspan="'.count($rows).'" width="20" class="unprintable"',
								write_html('button', 'class="ui-state-default hoverable circle_button" action="openSubAcc" code="'.$acc->full_code.'" module="accounts"', write_icon('extlink'))
							).
							write_html('td', 'rowspan="'.count($rows).'"', $acc->title).
							write_html('td', 'rowspan="'.count($rows).'"', $acc->full_code)
						: '').
						
						write_html('td', '', $row->currency).
						
						write_html('td', '', numberToMoney($acc->start_bal_debit)).
						write_html('td', '', numberToMoney($acc->start_bal_credit)).
						write_html('td', '', numberToMoney($acc->transactions_debit)).
						write_html('td', '', numberToMoney($acc->transactions_credit)).
						write_html('td', '', numberToMoney($acc->total_debit)).
						write_html('td', '', numberToMoney($acc->total_credit)).
						write_html('td', '', numberToMoney($acc->final_bal_debit)).
						write_html('td', '', numberToMoney($acc->final_bal_credit))
					);
					$passed[] = $acc->full_code;
				}
			} else {
				$start_bal = $acc->getStartBalance();
				foreach($start_bal as $bal){
					$acc->start_bal_credit = $bal->credit;
					$acc->start_bal_debit = $bal->debit;			
					$acc->total_debit = $bal->debit;
					$acc->total_credit = $bal->credit;			
					$lines[] = write_html('tr', '',
						(!in_array($acc->full_code, $passed)?
							write_html('td', 'rowspan="'.count($start_bal).'" width="20" class="unprintable"',
								write_html('button', 'class="ui-state-default hoverable circle_button" action="openSubAcc" code="'.$acc->full_code.'" module="accounts"', write_icon('extlink'))
							).
							write_html('td', 'rowspan="'.count($start_bal).'"', $acc->title).
							write_html('td', 'rowspan="'.count($start_bal).'"', $acc->full_code)
						: '').
						write_html('td', '', $bal->currency).
						write_html('td', '', numberToMoney($acc->start_bal_debit)).
						write_html('td', '', numberToMoney($acc->start_bal_credit)).
						write_html('td', '', '').
						write_html('td', '', '').
						write_html('td', '', numberToMoney($acc->total_debit)).
						write_html('td', '', numberToMoney($acc->total_credit)).
						write_html('td', '', numberToMoney($acc->total_debit)).
						write_html('td', '', numberToMoney($acc->total_credit))
					);

					$passed[] = $acc->full_code;
				}
			}
		}
		if($tr_only){
			return implode('', $lines);
		}
		$table = new Layout($this);
		$table->template = 'modules/accounts/templates/accounts_subs_table.tpl';
		$table->acc_name = $this->title;
		$table->report_year = "$year / ". ($year+1) ;
		$table->full_code = $this->full_code;
		$table->balance_rows = implode('', $lines);
		return $table->_print();
	}

	static function newMainCode($parent, $level){
		global $this_system;
		$accms = $this_system->getAccms();
		$layout = new Layout();
		$layout->acc_code_main =  Accounts::removeZero($parent);
		$layout->currency_combobox = 'hidden';
		$layout->start_bal_hidden = 'hidden';
		$layout->cc_hidden = 'hidden';
		$layout->level = $level+1;
		$existed = do_query_array( "SELECT * FROM codes WHERE level ='".($level+1)."' AND code LIKE '$parent%'", $accms->database, $accms->ip);
		$value = $existed != false ? count($existed)+1 : 1;		
		$count = strlen($value);
		for($i=1; $i<(5-strlen($parent)); $i++){
			$value = $value.'0';
		}
		$layout->value = $value;
		$layout->damage_hidden = 'hidden';
		$layout->template = 'modules/accounts/templates/accounts_new.tpl';
		return $layout->_print();
	}
	
	static function saveNewMain($post){
		global $lang, $this_system;
		$accms = $this_system->getAccms();
		$post['code'] =Accounts::removeZero( $post['precode'].$post['acc_code_sub']);
		$chk_exist = do_query_array("SELECT code FROM codes WHERE code ='".$post['code']."' AND level=".$post['level'], $accms->database, $accms->ip);
		$answer = array();
		$answer['error'] = "";
		if($chk_exist != false && count($chk_exist) > 0){
			$answer['error'] = $lang['account_allready_exists'];
		} else {
			if(do_insert_obj($post, 'codes', $accms->database, $accms->ip) !=false){
				$answer['title'] = $post['title'];
				
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		}
		return $answer;
	}

	static function saveMainCode($post){
		global $this_system;
		$accms = $this_system->getAccms();
		$post['main_code'] = Accounts::removeZero($post['main_code']);
		if(do_update_obj(array('title'=>$post['title'], 'notes'=>$post['notes']), "code='".$post['main_code']."'", "codes", $accms->database, $accms->ip) != false){
			$answer['id'] = $post['code'];
			$answer['error'] = '' ;
		} else {
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function getMainAccounts(){
		global $this_system;
		$accms = $this_system->getAccms();
		$sql = "SELECT * FROM codes ORDER BY code ASC";
		return do_query_array($sql, $accms->database, $accms->ip);
	}
	
}