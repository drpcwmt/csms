<?php
/** Assets acounts
*
*/

class Assets extends Accounts{
	
	public function __construct($code){
		global $this_system;
		parent::__construct($code);
	}
	
	public function getDamageAcc(){
		if(substr($this->main_code,0,2) == '11'){
			 $tot_damage_acc = new Accounts(Accounts::fillZero('main', substr_replace($this->main_code, '22',0,2)).Accounts::fillZero('sub', $this->sub_code));
			return $tot_damage_acc;
		} else {
			return false;
		}
	}

	public function getBalance($tr_only=false){
		$year = $_SESSION['year'];
		$lines = array();
		$acc_arr = array();
		
		$start = do_query_array("SELECT SUM(debit) AS debit, SUM(credit) AS credit, currency FROM start_balance WHERE year=$year AND main_code = '$this->main_code' AND sub_code = '$this->sub_code' GROUP BY currency", $this->accms->database, $this->accms->ip );
		$trans = do_query_array("SELECT SUM(transactions_rows.debit) AS debit, SUM(transactions_rows.credit) AS credit, transactions.currency FROM transactions, transactions_rows WHERE year=$year AND transactions_rows.trans_id=transactions.id AND transactions_rows.main_code = '$this->main_code' AND transactions_rows.sub_code = '$this->sub_code' GROUP BY transactions.currency", $this->accms->database, $this->accms->ip );
		
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
		$balance = new Layout();
		$balance->template = 'modules/accounts/templates/accounts_balance.tpl';
		$balance->balance_rows = implode('', $lines);
		return $balance->_print();			
	}	

}