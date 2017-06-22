<?php
/** Incomes Report
*
*/
class IncomesReport{
	
	public function __construct($year=''){
		global $this_system;
		$this->year = $year!= '' ? $year : $_SESSION['year'];
		$y = getYear($this->year);
		$this->begin_date = $y->begin_date;
		$this->end_date = $y->end_date;		
	}
	
	public function loadMainLayout(){
		$layout = new Layout();
		$incomes_trs = array();
		$expenses_trs = array();
		$mains = MainAccounts::getMainAccounts();
		foreach($mains as $main){
			if(in_array(substr(strval($main->code), 0, 1), array('3', '4')) && strlen($main->code) == 2){
				$acc = new Accounts($main->code);
				if(substr(strval($main->code), 0, 1) == 4){
					$incomes_trs = array_merge($incomes_trs, $this->builAccountTable($acc));
				} else {
					$expenses_trs = array_merge($expenses_trs, $this->builAccountTable($acc));
				}
			}
			
		}
		$incomes = IncomesReport::getTotal(4);
		$expenses = IncomesReport::getTotal(3);
		$layout->total_incomes = numberToMoney($incomes->egp_credit - $incomes->egp_debit);
		$layout->total_expenses = numberToMoney($expenses->egp_debit - $expenses->egp_credit);
		$layout->incomes_trs = implode('', $incomes_trs);
		$layout->expenses_trs = implode('', $expenses_trs);
		$layout->total_net = numberToMoney($incomes->egp_credit - $incomes->egp_debit - ($expenses->egp_debit - $expenses->egp_credit));
		return fillTemplate('modules/balance/templates/incomes_report.tpl', $layout);
		
	}
	
	public function builAccountTable($acc){
		$currencys= $acc->getCurrencys();
		$out = array();
		$first = true;
		foreach($currencys as $cur){
			$multipler = 1;
			$sql = "SELECT SUM(transactions_rows.debit * transactions_rows.rate) AS egp_debit, 
				SUM(transactions_rows.credit * transactions_rows.rate) AS egp_credit, 
				SUM(transactions_rows.debit) AS debit, 
				SUM(transactions_rows.credit) AS credit
				FROM transactions, transactions_rows 
				WHERE transactions_rows.trans_id=transactions.id
				AND transactions_rows.year>=$this->year
				AND transactions.currency='$cur'
				AND transactions_rows.main_code LIKE '$acc->main_code%'";
			$trans = do_query_obj($sql);
			$value = ( substr($acc->main_code, 0, 1) == '4' ?
				$trans->credit - $trans->debit
			:
				$trans->debit - $trans->credit
			);
			$egp_value = ( substr($acc->main_code, 0, 1) == '4' ?
				$trans->egp_credit - $trans->egp_debit
			:
				$trans->egp_debit - $trans->egp_credit
			);
			$out[]= write_html('tr', '',
				($first ? 
					write_html('td', 'rowspan="'.count($currencys).'"', write_html('h5', '', $acc->title)).
					write_html('td', 'rowspan="'.count($currencys).'" align="center"', $acc->full_code)
				:'').
				write_html('td', 'align="center"', $cur).
				write_html('td', '', ($cur!='EGP' ? numberToMoney($value) :'')).
				write_html('td', 'align="center"', numberToMoney($egp_value))
			);
			$first = false;
		}
		
		/*$subs = $acc->getSubs();
		$sub_incomes = array();
		foreach($subs as $sub){
			$sub_acc = new Accounts(Accounts::fillZero('main', $sub->main).Accounts::fillZero('sub', $sub->sub));
			$sub_currencys= $sub_acc->getCurrencys();
			foreach($sub_currencys as $cur){
				$tots = $sub_acc->getTotal($cur, $begin, $end);
				$tot = $tots[0];
				$sub_incomes[$cur]= ( $tot->debit - $tot->credit) * $multipler; // incomes
			}
			$first = true;
			foreach($sub_incomes as $cur=>$total){
				$out[] = write_html('tr', '',
					($first==true ? 
						write_html('td', 'rowspan="'.count($sub_incomes).'"', write_html('h6', '', $sub_acc->title)).
						write_html('td', 'rowspan="'.count($sub_incomes).'" align="center"', $sub_acc->full_code)
					:'').
					write_html('td', 'align="center"', $cur).
					write_html('td', 'align="center"', $total).
					write_html('td', '', '&nbsp;')
				);
				$first = false;
			}
		}*/
		return $out;
	}
	
	
	public function getTotal($acc_code='3'){ ///$acc_code = 3 OR 4
		$sql = "SELECT SUM(transactions_rows.debit * transactions_rows.rate) AS egp_debit, 
			SUM(transactions_rows.credit * transactions_rows.rate) AS egp_credit, 
			SUM(transactions_rows.debit) AS debit, 
			SUM(transactions_rows.credit) AS credit
			FROM transactions, transactions_rows 
			WHERE transactions_rows.trans_id=transactions.id
			AND transactions.date>=$this->begin_date 
			AND transactions.date<=$this->end_date
			AND transactions_rows.main_code LIKE '$acc_code%'";
		return do_query_obj($sql);			
	}
}