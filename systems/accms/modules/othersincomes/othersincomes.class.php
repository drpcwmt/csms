<?php
/** Other Incomes
*
*/
class OthersIncomes{
	
	public static $expenses_main_acc = '39';
	public static $incomes_main_acc = '49';
	
	
	public function __construct($id=''){
		global $lang;
		$this->exists = false;
		if($id != ''){	
			$income = do_query_obj("SELECT * FROM others_incomes WHERE id=$id", MySql_Database);	
			if(isset( $income->id )){
				foreach($income as $key =>$value){
					$this->$key = $value;
				}
				$this->exists = true;
			}	
		}	
	}

	public function loadLayout(){
		global $lang;
		$layout = new Layout($this);
		$layout->template = "modules/othersincomes/templates/income_layout.tpl";
		$parent_code = substr($this->expenses_acc,2,3);
		$year = getYear();
		$rows = array();
		$expenses_acc = new Accounts($this->expenses_acc);
		$total_expenses = do_query_array("SELECT SUM(transactions_rows.debit * transactions_rows.rate) AS egp_debit, 
			SUM(transactions_rows.credit * transactions_rows.rate) AS egp_credit, 
			SUM(transactions_rows.debit) AS debit, 
			SUM(transactions_rows.credit) AS credit,
			transactions.currency
			FROM transactions, transactions_rows 
			WHERE transactions_rows.trans_id=transactions.id
			AND transactions_rows.year>=$year->year 
			AND transactions_rows.main_code = '$expenses_acc->main_code'
			AND transactions_rows.sub_code = '$expenses_acc->sub_code'
			GROUP BY transactions.currency");
		foreach($total_expenses as $tot){
			$rows[$tot->currency]['expense'] = $tot->debit - $tot->credit;
		}

		$incomes_acc = new Accounts($this->incomes_acc);
		$total_incomes = do_query_array("SELECT SUM(transactions_rows.debit * transactions_rows.rate) AS egp_debit, 
			SUM(transactions_rows.credit * transactions_rows.rate) AS egp_credit, 
			SUM(transactions_rows.debit) AS debit, 
			SUM(transactions_rows.credit) AS credit,
			transactions.currency
			FROM transactions, transactions_rows 
			WHERE transactions_rows.trans_id=transactions.id
			AND transactions_rows.year>=$year->year 
			AND transactions_rows.main_code = '$incomes_acc->main_code'
			AND transactions_rows.sub_code = '$incomes_acc->sub_code'
			GROUP BY transactions.currency");
		foreach($total_incomes as $tot){
			$rows[$tot->currency]['income'] = $tot->credit - $tot->debit;
		}
		$trs = array();
		foreach($rows as $cur=>$array){
			$exp = isset($array['expense']) ? $array['expense'] : 0;
			$inc = isset($array['income']) ? $array['income'] : 0;
			$trs[] = write_html('tr', '',
				write_html('td', '', $cur).
				write_html('td', '', $exp).
				write_html('td', '', $inc).
				write_html('td', '', $inc - $exp)
			);
		}
		
		$layout->totals_trs = implode('', $trs);		
		
		
		$details = new Layout($this);
		$details->template = "modules/othersincomes/templates/details.tpl";
		$details->type_opts = write_select_options(OthersIncomes::getTypes(), '49'.$parent_code, false);
		$details->cc_opts = write_select_options(CostCentersGroup::getListOpts(), $this->cc, false);
		$details->expenses_code_main = Accounts::fillZero('main', $expenses_acc->main_code);
		$details->expenses_code_sub = Accounts::fillZero('sub', $expenses_acc->sub_code);
		$details->expenses_name = $expenses_acc->title;
		$details->incomes_code_main = Accounts::fillZero('main', $incomes_acc->main_code);
		$details->incomes_code_sub = Accounts::fillZero('sub', $incomes_acc->sub_code);
		$details->incomes_name = $incomes_acc->title;
		$layout->settings_tab = $details->_print();
		
		$layout->transactions_trs = '';
		$expsense_trs = $expenses_acc->getTransactionsTable(unixToDate($year->begin_date), unixToDate($year->end_date), true);
		$incomes_trs = $incomes_acc->getTransactionsTable(unixToDate($year->begin_date), unixToDate($year->end_date), true);
		$layout->transactions_trs = (strpos($expsense_trs, 'ui-state-error')===false ? $expsense_trs: '').(strpos( $incomes_trs, 'ui-state-error')===false ? $incomes_trs: '');
		
		return $layout->_print();
	}
	
	static function getTypes(){
		$out = array();
		$main_acc = OthersIncomes::$incomes_main_acc;
		$accs = do_query_array("SELECT * FROM codes WHERE code LIKE '$main_acc%' AND level=2");
		foreach($accs as $acc){
			$out[$acc->code] = $acc->title;
		}
		return $out;
	}

	static function getList(){
		return do_query_array("SELECT * FROM others_incomes WHERE status=1");
		
	}
	
	static function loadMainLayout(){
		$layout = new Layout();
		$layout->template = "modules/othersincomes/templates/main_layout.tpl";
		
		$layout->incomes_list = '';
		$incomes = OthersIncomes::getList();
		if($incomes != false){
			$first = true;
			foreach($incomes as $inc){
				$layout->incomes_list .= write_html( 'li', 'income_id="'.$inc->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openIncome"', 
					write_html('text', 'class="holder-income'.$inc->id.'"',
						$inc->title
					)
				);	
				if($first){
					$first_income = new OthersIncomes($inc->id);
					$layout->incomes_content = $first_income->loadLayout();
					$first = false;
				}
			}
		}
		return $layout->_print();
	}
	
	static function newIncomeForm(){
		$layout = new Layout();
		$layout->type_opts = write_select_options(OthersIncomes::getTypes(), '', false);
		$layout->cc_opts = write_select_options(CostCentersGroup::getListOpts(), '17', false);
		$layout->template = "modules/othersincomes/templates/details.tpl";
		return $layout->_print();
	}

	static function _save($post){
		global $lang;
		$result = new stdClass();;
		if(isset($post['id']) && $post['id'] != ''){
			$post['expenses_acc'] =$post['expenses_code_main'].$post['expenses_code_sub'];
			$post['incomes_acc'] =$post['incomes_code_main'].$post['incomes_code_sub'];
			if( do_update_obj($post, 'id='.$post['id'], 'others_incomes') != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(!isset($post['id']) || $post['id'] == ''){
			// without accounts
			if($post['expenses_code_main'] == ''){
				$sub = do_query_obj("SELECT sub FROM sub_codes WHERE main LIKE '".$post['parent']."%'  ORDER BY sub DESC LIMIT 1");
				if(isset($sub->sub)){
					$new_sub = $sub->sub +1;
				} else {
					$new_sub = 1;
				}
				$expenses_main_acc = substr_replace($post['parent'], '39',0,2);
				$expenses_acc = array('precode'=> Accounts::removeZero( $expenses_main_acc),
					'acc_code_sub'=> $new_sub,
					'title'=> $lang['expenses'].' / '.$post['title'],
					'group_id'=> $post['cc']
				);
				$result = json_decode(Accounts::saveNewAcc($expenses_acc));
				$incomes_main_acc = $post['parent'];
				if($result->error == ''){
					$incomes_acc = array('precode'=> Accounts::removeZero( $incomes_main_acc),
						'acc_code_sub'=> $new_sub,
						'title'=> $lang['incomes'].' / '.$post['title'],
						'group_id'=> $post['cc']
					);
					$result = json_decode(Accounts::saveNewAcc($incomes_acc));
				}
				$post['expenses_acc'] = Accounts::fillZero('main', $expenses_main_acc).Accounts::fillZero('sub', $new_sub);
				$post['incomes_acc'] =  Accounts::fillZero('main', $incomes_main_acc).Accounts::fillZero('sub', $new_sub);
			} else {
				$post['expenses_acc'] =$post['expenses_code_main'].$post['expenses_code_sub'];
				$post['incomes_acc'] =$post['incomes_code_main'].$post['incomes_code_sub'];
			}
			if($id = do_insert_obj($post, 'others_incomes')){
				$result->error = '';
			} else {
				$result->error = $lang['error'];
			}
		}

		if($result->error == ''){
			$answer['id'] = $id;
			$answer['title'] = $post['title'];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
}
