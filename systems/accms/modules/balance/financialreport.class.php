<?php
/** FinancialReport
*
*/
class FinancialReport{
	public $mains = array();
	public $begin, $end;
	public $currency = 0;
	
	public function __construct($cc_id='', $year=''){
		$this->year = $year!= '' ? $year : $_SESSION['year'];
		$this->cc = $cc_id;
		
		$this->begin = getYearSetting('begin_date');
		$this->end = getYearSetting('end_date');
		$mains = MainAccounts::getMainAccounts();
		foreach($mains as $main){
			if(strlen($main->code) == 2){
				$this->mains[] = new Accounts($main->code);
			}
			/*if(strlen($main->code) == 3){
				$this->sub_mains[] = new Accounts($main->code);
			}*/
			$this->sub_mains = array();
		}
		
	}
	
	public function loadLayout(){
		global $this_system;
		$layout = new Layout();
		$layout->assets_items = '';
		$layout->dues_items = '';
		$layout->ownership_item = '';
		$layout->total_assets = 0;
		$layout->total_dues = 0;
		$layout->template = 'modules/balance/templates/financial_report.tpl';
		foreach($this->mains as $main){
			$first = true;
			$curs = $main->getCurrencys();
			foreach($curs as $cur){
				$trans_debit_egp = 0;
				$trans_credit_egp = 0;
				$trans_credit = 0;
				$trans_debit = 0;
				$sql = "SELECT SUM(transactions_rows.debit) AS debit, 
					SUM(transactions_rows.credit) AS credit
					FROM transactions, transactions_rows 
					WHERE transactions_rows.trans_id=transactions.id
					AND transactions_rows.year=$this->year 
					AND transactions.currency='$cur'
					AND transactions_rows.main_code LIKE '$main->main_code%'";
				$trans = do_query_obj($sql);
				$start_sql = "SELECT SUM(debit) AS debit, 
					SUM(credit) AS credit
					FROM start_balance
					WHERE year=$this->year
					AND main_code LIKE '$main->main_code%'
					AND currency = '$cur'";
				$start = do_query_obj($start_sql);
				$value = ( substr($main->main_code, 0, 1) == '2' ?
					$start->credit + $trans->credit - $start->debit - $trans->debit
				:
					$start->debit + $trans->debit - $start->credit - $trans->credit
				);
				$rate = Currency::convertRate($cur, 'EGP', $this->end);

				$egp_value = $value * $rate;
				$item = write_html('tr', '',
					($first ? 
						write_html('td', 'rowspan="'.count($curs).'" valign="top"', 
							write_html('h5', '', $main->title)
						)
					: '').
					write_html('td', 'align="center" width="60"', 
						write_html('b', '', $cur)
					).
					write_html('td', 'align="center"  width="100"', 
						($cur != 'EGP' ?
							write_html('b', '', numberToMoney($value))
						: '')
					).
					write_html('td', 'align="center"  width="100"', 
						write_html('b', '',numberToMoney($egp_value))
					)
				);
				$first = false;
				
				
				if(substr($main->main_code, 0, 1) == '1'){
					$layout->assets_items .= $item;
					$layout->total_assets += $egp_value;
				} elseif(substr($main->main_code, 0, 1) == '2'){ 
					$layout->dues_items .= $item;
					$layout->total_dues += $egp_value ;
				}
			}
			$subs = $this->getSubs($main->main_code);
			
			if(substr($main->main_code, 0, 1) == '1'){
				$layout->assets_items .= $subs;
				//$layout->total_assets += $egp_value;
			} elseif(substr($main->main_code, 0, 1) == '2'){ 
				$layout->dues_items .= $subs;
				//$layout->total_dues += $egp_value ;
			}
		}
		// Net Profit
		$layout->profit = numberToMoney($this->getNetProfit());
		$layout->total_assets = round($layout->total_assets);
		$layout->total_dues = round($layout->total_dues + $this->getNetProfit());
		
		
		return $layout->_print();
	}
	
	public function getSubs($main_code){
		global  $this_system;
		$items = array();
		foreach($this->sub_mains as $sub){
			if(strlen($sub->main_code) == 3 && substr($sub->main_code, 0, 2) == $main_code){
				$acc = new Accounts($sub->main_code);
				$first = true;
				$curs = $acc->getCurrencys();
				foreach($curs as $cur){
					$sql = "SELECT SUM(transactions_rows.debit) AS debit, 
						SUM(transactions_rows.credit) AS credit
						FROM transactions, transactions_rows 
						WHERE transactions_rows.trans_id=transactions.id
						AND transactions_rows.year=$this->year 
						AND transactions.currency='$cur'
						AND transactions_rows.main_code LIKE '$acc->main_code%'";
					$trans = do_query_obj($sql);
					$start_sql = "SELECT SUM(debit) AS debit, 
						SUM(credit) AS credit
						FROM start_balance
						WHERE year=$this->year
						AND main_code LIKE '$acc->main_code%'
						AND currency = '$cur'";
					$start = do_query_obj($start_sql);
					$value = ( substr($acc->main_code, 0, 1) == '2' ?
						$start->credit + $trans->credit - $start->debit - $trans->debit
					:
						$start->debit + $trans->debit - $start->credit - $trans->credit
					);
					$rate = Currency::convertRate($cur, 'EGP', $this->end);
					$egp_value = $value * $rate;
					$items[] = write_html('tr', '',
						($first ? 
							write_html('td', 'rowspan="'.count($curs).'" valign="top"', 
								write_html('h6', '', $acc->title)
							)
						: '').
						write_html('td', 'align="center" width="60"', 
							write_html('b', '', $cur)
						).
						write_html('td', 'align="center"  width="100"', 
							($cur != 'EGP' ?
								numberToMoney($value)
							: '')
						).
						write_html('td', 'align="center"  width="100"', 
							numberToMoney( $egp_value)
						)
					);
					$first = false;
				}
			}
		}
		return implode('', $items);
	}
	
	public function getNetProfit(){
		$report = new IncomesReport($this->year);
		$incomes = $report->getTotal(4);
		$depenses = $report->getTotal(3);
		
		return $incomes->egp_credit - $incomes->egp_debit - $depenses->egp_debit + $depenses->egp_credit;
	}
		
		
		
} 