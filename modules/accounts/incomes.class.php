<?php
/** Incomes acounts
*
*/

class Incomes extends Accounts{
	
	public function __construct($code){
		global $this_system;
		parent::__construct($code);
	}
	

	public function getBalance($tr_only=false){
		$year = $_SESSION['year'];
		$lines = array();
		$acc_arr = array();
		
		$trans = do_query_array("SELECT SUM(transactions_rows.debit) AS debit, SUM(transactions_rows.credit) AS credit, transactions.currency FROM transactions, transactions_rows WHERE year=$year AND transactions_rows.trans_id=transactions.id AND transactions_rows.main_code = '$this->main_code' AND transactions_rows.sub_code = '$this->sub_code' GROUP BY transactions.currency", $this->accms->database, $this->accms->ip );
		
		if($trans != false){
			foreach($trans as $acc){
				$acc_arr[$acc->currency]['trans_debit'] = $acc->debit;
				$acc_arr[$acc->currency]['trans_credit'] = $acc->credit;
			}
		}
		foreach($acc_arr as $cur=>$values){
			$values['trans_debit'] = isset($values['trans_debit']) ? $values['trans_debit'] : 0;
			$values['trans_credit'] = isset($values['trans_credit']) ? $values['trans_credit'] : 0;
			
			$total_debit = $values['start_debit'] + $values['trans_debit'];
			$total_credit = $values['start_credit'] + $values['trans_credit'];

			$lines[] = write_html('tr', '',
				write_html('td', '', $cur).
				write_html('td', '', numberToMoney($values['trans_debit'])).
				write_html('td', '', numberToMoney($values['trans_credit']))
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