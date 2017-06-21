<?php
/** Client main Class
*
*/
class Clients{
	
	public $main_code = '151';
	
	public function __construct($id){
		$clients = do_query_obj("SELECT * FROM sub_codes WHERE main='$this->main_code' OR main LIKE '$this->main_code%'", MySql_Database);
		if($cc_q != false && $cc_q->id != ''){
			$this->id =  $cc_q->id;
			$this->title = $cc_q->title;
			$this->notes =  $cc_q->notes;
		} 
	}
	
	
	public function loadLayout($begin_date='', $end_date=''){
		global $MS_settings;
		$layout = $this;
		$layout->begin_date = getYearSetting('begin_date');
		$layout->end_date = getYearSetting('end_date');
		$layout->servers_tab = $this->getConnections();
		//$layout->transactions_table = Accounts::getTransaction($this->id, $begin_date, $end_date);
		return fillTemplate('modules/clients/templates/clients_details.tpl', $layout);
	}
	
	static function loadMainLayout(){		
		$layout = new stdClass();
		
		$search = new stdClass();
		$search->acc_code_main="15";
		$layout->search_form = fillTemplate('modules/accounts/templates/accounts_search.tpl', $search);
		
		$layout->costcenters_lis = '';
		$ccs = Costcenters::getList();
		foreach($ccs as $cc){
			$layout->costcenters_lis .= write_html('li', '',
				write_html('a', 'href="index.php?module=clients&cc='.$cc->id.'"', $cc->title)
			);
		}
		return fillTemplate('modules/clients/templates/clients_main.tpl', $layout);
	}
	
	static function loadCostCenter($ccid){
		$year = $_SESSION['year'];
		$main_code = '151';
		$transactions = do_query_obj("SELECT SUM(credit) AS credit, SUM(debit) AS debit FROM transactions_rows WHERE year=$year AND main_code LIKE '$main_code%' AND cc=$ccid", MySql_Database);
		$layout = new stdClass();
		
		$layout->credit = $transactions!= false ? $transactions->credit : 0;
		$layout->debit = $transactions!= false ? $transactions->debit : 0;
		$layout->total = $transactions->debit - $transactions->credit;
		$clients = do_query_array("SELECT * FROM sub_codes WHERE group_id=$ccid AND (main=$main_code OR main LIKE '$main_code%')", MySql_Database);
		$layout->clients_trs = '';
		foreach($clients as $c){
			$trans = do_query_obj("SELECT SUM(credit) AS credit, SUM(debit) AS debit FROM transactions_rows WHERE year=$year AND main_code LIKE '$main_code%' AND sub_code=$c->sub AND cc=$ccid", MySql_Database);
			$row = new stdClass();
			$row->id = $c->sub;
			$row->code = Accounts::fillZero('main',$c->main). Accounts::fillZero('sub', $c->sub);
			$row->title = $c->title;
			$row->debit = $trans->debit;
			$row->credit= $trans->credit;
			$row->total = $trans->debit - $trans->credit;
			$layout->clients_trs .= fillTemplate("modules/clients/templates/clients_list_rows.tpl", $row);
		}

		return fillTemplate('modules/clients/templates/clients_costcenters.tpl', $layout);
	}

	static function loadOthers(){
		$year = $_SESSION['year'];
		$main_code = '152';
		$transactions = do_query_obj("SELECT SUM(credit) AS credit, SUM(debit) AS debit FROM transactions_rows WHERE year=$year AND main_code LIKE '$main_code%'", MySql_Database);
		$layout = new stdClass();
		
		$layout->credit = $transactions!= false ? $transactions->credit : 0;
		$layout->debit = $transactions!= false ? $transactions->debit : 0;
		$layout->total = $transactions->debit - $transactions->credit;
		$clients = do_query_array("SELECT * FROM sub_codes WHERE (main=$main_code OR main LIKE '$main_code%')", MySql_Database);
		$layout->clients_trs = '';
		foreach($clients as $c){
			$trans = do_query_obj("SELECT SUM(credit) AS credit, SUM(debit) AS debit FROM transactions_rows WHERE year=$year AND main_code LIKE '$main_code%' AND sub_code=$c->sub", MySql_Database);
			$row = new stdClass();
			$row->id = $c->sub;
			$row->code = Accounts::fillZero('main',$c->main). Accounts::fillZero('sub', $c->sub);
			$row->title = $c->title;
			$row->debit = $trans->debit;
			$row->credit= $trans->credit;
			$row->total = $trans->debit - $trans->credit;
			$layout->clients_trs .= fillTemplate("modules/clients/templates/clients_list_rows.tpl", $row);
		}

		return fillTemplate('modules/clients/templates/clients_costcenters.tpl', $layout);
	}

}

