<?php
/** Cost Center main Class
*
*/
class Costcenters{
	public $id, $title, $notes;
	
	public function __construct($id){
		global $accms;
		if($id == '0'){
			global $lang;
			$this->id =  0;
			$this->title = 'General';
			$this->notes =  '';
		}
		$cc_q = do_query_obj("SELECT * FROM cc WHERE id='$id'", $accms->database, $accms->ip);
		if($cc_q != false && $cc_q->id != ''){
			$this->id =  $cc_q->id;
			$this->title = $cc_q->title;
			$this->notes =  $cc_q->notes;
		} 
		$this->accms = $accms;
	}
	
	public function getName(){
		return $this->title;
	}
	
	public function getSMS(){
		$sms_conx = do_query_obj("SELECT id FROM connections WHERE ccid=$this->id AND type='sms'", $this->accms->database, $this->accms->ip);
		if($sms_conx!=false && isset($sms_conx->id)){
			return new SMS($sms_conx->id);
		} else {
			return false;
		}
	}
	
	public function loadLayout($begin_date='', $end_date=''){
		global $accms;
		$layout = new Layout($this);
		$layout->begin_date = getYearSetting('begin_date');
		$layout->end_date = getYearSetting('end_date');
		$sms_conx = do_query_obj("SELECT * FROM connections WHERE ccid=$this->id AND type='sms'", $accms->database, $accms->ip);
		$layout->sms_id = $sms_conx->id;
		$systems = do_query_array("SELECT * FROM connections WHERE ccid=$this->id ORDER BY type ASC", $accms->database, $accms->ip);
		$layout->system_table = Connections::loadConxTable($systems);
		$layout->template = 'modules/costcenters/templates/costcenter_details.tpl';
		//$layout->transactions_table = Settlements::getCcTransaction($this->id, $begin_date, $end_date);
		return $layout->_print();
	}
		
	public function getClientsCount(){
		$clients = do_query_array("SELECT sub FROM sub_code WHERE main='151".$this->id."'", MySql_Database);
		return !$clients ? 0 : count($clients);
	}
	
	static function getList(){
		$ccs = do_query_array("SELECT * FROM cc", MySql_Database);
		foreach($ccs as $cc){
			$out[] = new Costcenters($cc->id);
		}
		return $out;
	}

	static function getListOpts(){
		$out = array();
		$ccs = do_query_array("SELECT id, title FROM cc", MySql_Database);
		foreach($ccs as $cc){
			$out[$cc->id] = $cc->title;	
		}
		
		return $out;
	}

	static function loadMainLayout(){
		global $lang;
		$year = $_SESSION['year'];
		$ccs = Costcenters::getList();
		$rows = array();
		$total_incomes = 0;
		$total_expenses = 0;
		$total_stds = 0;
		if($ccs != false && count($ccs)>0){
			foreach($ccs as $cc){
				$incomes = $cc->getTotal(4);
				$expenses = $cc->getTotal(3);
				$cc->incomes = numberToMoney($incomes);
				$cc->expenses = numberToMoney($expenses);
				$cc->net = numberToMoney($incomes - $expenses);
				$sms = $cc->getSMS();
				$count_std = $sms != false ? $sms->countStudent(array('1','3')) : 0;
				$cc->count_std = $count_std;
				$rows[] = fillTemplate('modules/costcenters/templates/costcenter_list_rows.tpl', $cc);
				$total_incomes += $incomes;
				$total_expenses += $expenses;
				$total_stds += $count_std;
			}
		}
		$layout= new stdClass();
		$layout->rows = implode('', $rows);
		$layout->tfoot = write_html('tr', '',
			write_html('th', 'width="20" class="unprintable"', '&nbsp;').
			write_html('th', '', '&nbsp;').
			write_html('th', '', $lang['total']).
			write_html('th', '', numberToMoney($total_expenses)).
			write_html('th', '', numberToMoney($total_incomes)).
			write_html('th', '', numberToMoney($total_incomes - $total_expenses)).
			write_html('th', '', $total_stds).
			write_html('th', '', '&nbsp;')
		); 
		
		$layout->groups_list = costcentersGroup::getListTr();
		$ccs = Costcenters::getList();
		$ths = array();
		foreach($ccs as $cc){
			$ths[] = write_html('th', 'width="80"', $cc->title);
		}
		$layout->members_ths = implode('', $ths);
		
		return fillTemplate('modules/costcenters/templates/costcenters_main.tpl', $layout);
	}

	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'cc', MySql_Database) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(!isset($post['id']) || $post['id'] == ''){
			$result = do_insert_obj($post, 'cc', MySql_Database);
			$id = $result;
		}

		if($result!=false){
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
	
	static function loadNewForm(){
		$layout = new stdClass();
		return fillTemplate('modules/costcenters/templates/costcenter_details.tpl', $layout);	
	}
	
		
	public function getGroups(){
		if(!isset($this->groups)){
			global $accms;
			$cc_q = do_query_array("SELECT group_id FROM cc_groups_divisions WHERE cc_id='$this->id'", $accms->database, $accms->ip);
			$this->groups = array();
			if($cc_q != false){
				foreach($cc_q as $cc){
					$this->groups[] = new CostcentersGroup($cc->group_id);
				}
			} 
		}
		return $this->groups;
	}
	public function getAccounts(){
		if(!isset($this->accounts)){
			global $accms;
			$accs = array();
			$groups = $this->getGroups();
			foreach($groups as $gr){
				$where[] = "ccid=$gr->id";
			}
			$this->accounts = do_query_array("SELECT * FROM sub_codes WHERE ".implode(' OR ', $where), $accms->database, $accms->ip);
		}
		return $this->accounts;
	}
	
	public function getAccountPart($group_id){
		global $accms;
			$tot = do_query_obj("SELECT SUM(value) as val FROM cc_groups_divisions WHERE group_id='$group_id'", $accms->database, $accms->ip);
			$this_val = do_query_obj("SELECT value FROM cc_groups_divisions WHERE group_id='$group_id' AND cc_id='$this->id'", $accms->database, $accms->ip);
			return $this_val->value / $tot->val;
	}
	
	public function getIncomeReport(){
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
		$incomes = $this->getTotal(4);
		$expenses = $this->getTotal(3);
		$layout->total_incomes = numberToMoney($incomes );
		$layout->total_expenses = numberToMoney($expenses );
		$layout->incomes_trs = implode('', $incomes_trs);
		$layout->expenses_trs = implode('', $expenses_trs);
		$layout->total_net = numberToMoney($incomes - $expenses);
		return fillTemplate('modules/balance/templates/incomes_report.tpl', $layout);
	}
	
	public function builAccountTable($acc){
		$year = getYear();
		$currencys= $acc->getCurrencys();
		$out = array();
		$first = true;
		$income_report = new IncomesReport();
		$tot = $income_report->getTotal($acc->main_code);
		$groups = $this->getGroups();

		foreach($currencys as $cur){
			$sql = "SELECT (transactions_rows.debit * transactions_rows.rate) AS egp_debit, 
				(transactions_rows.credit * transactions_rows.rate) AS egp_credit, 
				transactions_rows.debit AS debit, 
				transactions_rows.credit AS credit,
				transactions_rows.cc
				FROM transactions, transactions_rows 
				WHERE transactions_rows.trans_id=transactions.id
				AND transactions_rows.year=$year->year
				AND transactions.currency='$cur'
				AND transactions_rows.main_code LIKE '$acc->main_code%'";
			$where = array();
			foreach($groups as $gr){
				$where[] = "transactions_rows.cc=$gr->id";
			}
			$sql .= " AND (".implode(' OR ', $where)." )";
			$trans = do_query_array($sql);
			$total_value = 0;
			$total_egp_value = 0;
			foreach($trans as $t){
				$multipler = $this->getAccountPart($t->cc);
				 $value = ( substr($acc->main_code, 0, 1) == '4' ?
					$t->credit - $t->debit
				:
					$t->debit - $t->credit
				) ;
				$total_value += $value * $multipler;
				
				$egp_value = ( substr($acc->main_code, 0, 1) == '4' ?
					$t->egp_credit - $t->egp_debit
				:
					$t->egp_debit - $t->egp_credit
				) ;
				$total_egp_value += $egp_value * $multipler;
			}
			$out[]= write_html('tr', '',
				($first ? 
					write_html('td', 'rowspan="'.count($currencys).'"', write_html('h5', '', $acc->title)).
					write_html('td', 'rowspan="'.count($currencys).'" align="center"', $acc->full_code)
				:'').
				write_html('td', 'align="center"', $cur).
				write_html('td', '', ($cur!='EGP' ? numberToMoney($total_value) :'')).
				write_html('td', 'align="center"', numberToMoney($total_egp_value))				
			);
			$first = false;
		}
		return  $out;
	}

	public function getTotal($acc_code='3'){ ///$acc_code = 3 OR 4
		$year = getYear();
		$acc = new Accounts($acc_code);
		$sql = "SELECT (transactions_rows.debit * transactions_rows.rate) AS egp_debit, 
			(transactions_rows.credit * transactions_rows.rate) AS egp_credit, 
			transactions_rows.cc
			FROM transactions, transactions_rows 
			WHERE transactions_rows.trans_id=transactions.id
			AND transactions_rows.year=$year->year
			AND transactions_rows.main_code LIKE '$acc->main_code%'";
		$where = array();
		$groups = $this->getGroups();
		$total_egp_value = 0;
	//	print_r($groups);
		if(count($groups) > 0){
			foreach($groups as $gr){
				$where[] = "transactions_rows.cc=$gr->id";
			}
			$sql .= " AND (".implode(' OR ', $where)." )";
			$trans = do_query_array($sql);
			foreach($trans as $t){
				$multipler = $this->getAccountPart($t->cc);
				$egp_value = ( substr($acc->main_code, 0, 1) == '4' ?
					$t->egp_credit - $t->egp_debit
				:
					$t->egp_debit - $t->egp_credit
				);
				$total_egp_value += ($egp_value * $multipler);
			}
		}
		return $total_egp_value;	
	}
}
?>	