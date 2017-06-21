<?php
/** SafeMS global
*
*/

class SafeMS extends CSMS {
	public $type = 'safems';
	
	public function __construct($id=''){
		if($id != ''){
			parent::__construct($id);
		} else {
			if(MS_codeName == 'safems'){
				$this->ip = '127.0.0.1';
				$this->url= '';
				$this->database = SAFEMS_Database;
				parent::__construct($this);
				
			} else {
				global $this_system;
				$this->url = $this_system->getSettings('safems_server_name');
				$this->ip =$this_system->getSettings('safems_server_ip');
				$this->database = SAFEMS_Database;	
				$this->id = $this_system->getSettings('this_acc_code');
				parent::__construct($this);		
			}
		}
		$this->full_code = $this->getSettings('this_acc_code');
	}
	
	public function getAccount(){
		if(!isset($this->account)){
			$this->account = new Accounts($this->full_code);
		}
		return $this->account;
	}
	
	public function getTransactions($begin, $end, $tr_only=false, $main='', $sub=''){
		global $lang;
		$day = (60*60*24);
		$this_account = $this->getAccount();
		$sql_debit = "SELECT value AS debit, currency, date, notes, to_main_code, to_sub_code, id, cc, type AS payment_type FROM ingoing WHERE date>=$begin AND date<".($end+$day);
		$sql_credit = "SELECT value AS credit, currency, date, notes, from_main_code, from_sub_code, id, cc, type AS payment_type FROM outgoing WHERE date>=$begin AND date<".($end+$day);
		
		$sql_debit .= " AND (from_main_code=$this_account->main_code AND from_sub_code=$this_account->sub_code)";
		$sql_credit .= " AND (to_main_code=$this_account->main_code AND to_sub_code=$this_account->sub_code)";
		
		if($main!=''){
			$sql_debit .= " AND to_main_code='$main' AND to_sub_code='$sub'";
			$sql_credit .= " AND from_main_code='$main' AND from_sub_code='$sub'";
		}
		$rows_debit = do_query_array($sql_debit, $this->database, $this->ip);
		$rows_credit = do_query_array($sql_credit, $this->database, $this->ip);
		$layout = new Layout();
		$layout->template = 'modules/safems/templates/transactions_table.tpl';
		$year = getYear();

		$layout->transactions_trs ='';
			// Debits
		foreach($rows_debit as $debit){
			$account = new Accounts(Accounts::fillZero('main', $debit->to_main_code).Accounts::fillZero('sub', $debit->to_sub_code));
			$debit->account_name = $account->title;
			$debit->account_code = $account->full_code;
			$debit->datetime = date('d/m/Y h:m', $debit->date);
			$cc = new CostcentersGroup($debit->cc);
			$debit->cc_name = $cc->title;
			$debit->payment_type = $lang[$debit->payment_type];
			$debit->type = "ingoing";
			$layout->transactions_trs .= fillTemplate('modules/safems/templates/transactions_table_rows.tpl', $debit);	
		}
			// Credits
		foreach($rows_credit as $credit){
			$account = new Accounts(Accounts::fillZero('main', $credit->from_main_code).Accounts::fillZero('sub', $credit->from_sub_code));
			$credit->account_name = $account->title;
			$credit->account_code = $account->full_code;
			$credit->datetime = date('d/m/Y h:m', $credit->date);
			$cc = new CostcentersGroup($credit->cc);
			$credit->cc_name = $cc->title;
			$credit->payment_type = $lang[$credit->payment_type];
			$credit->type = "outgoing";
			$layout->transactions_trs .= fillTemplate('modules/safems/templates/transactions_table_rows.tpl', $credit);	
		}
		$layout->total_table = $this->getTotalNetTable($begin, $end, 'cash');
		if($tr_only){
			return $layout->_print();
		} else {
			$full_layout = new Layout($this);
			$full_layout->template = 'modules/safems/templates/transactions_layout.tpl';
			$full_layout->title = $lang['cash_report'];
			$full_layout->submit_action= 'submitSafeTransTable';
			$full_layout->begin_date = $begin;
			$full_layout->end_date = $end;
			$full_layout->tables = $layout->_print();		
			return $full_layout->_print();
		}
	}
	
	public function getBankTransactions($begin, $end, $tr_only=false){
		global $lang;
		$day = (60*60*24);
		$this_account = $this->getAccount();
		$sql_debit = "SELECT value AS debit, currency, date, notes, to_main_code, to_sub_code, from_main_code, from_sub_code, id, cc, type AS payment_type FROM ingoing WHERE date>=$begin AND date<".($end+$day);
		$sql_credit = "SELECT value AS credit, currency, date, notes, from_main_code, from_sub_code, to_main_code, to_sub_code, id, cc, type AS payment_type FROM outgoing WHERE date>=$begin AND date<".($end+$day);
		
		$sql_debit .= " AND from_main_code LIKE '161%'";
		$sql_credit .= " AND to_main_code LIKE '161%'";

		$rows_debit = do_query_array($sql_debit, $this->database, $this->ip);
		$rows_credit = do_query_array($sql_credit, $this->database, $this->ip);
		$layout = new Layout();
		$layout->template = 'modules/safems/templates/transactions_table.tpl';
		$layout->transactions_trs ='';
			// Debits
		foreach($rows_debit as $debit){
			$account = new Accounts(Accounts::fillZero('main', $debit->to_main_code).Accounts::fillZero('sub', $debit->to_sub_code));
			$debit->account_name = $account->title;
			$debit->account_code = $account->full_code;
			$debit->datetime = date('d/m/Y h:m', $debit->date);
			$cc = new CostcentersGroup($debit->cc);
			$debit->cc_name = $cc->title;
			$debit->type = "ingoing";
			$bank =  new Accounts(Accounts::fillZero('main', $debit->from_main_code).Accounts::fillZero('sub', $debit->from_sub_code));
			$debit->notes = $bank->title. ' - '. $debit->notes;
			$layout->transactions_trs .= fillTemplate('modules/safems/templates/transactions_table_rows.tpl', $debit);	
		}
			// Credits
		foreach($rows_credit as $credit){
			$account = new Accounts(Accounts::fillZero('main', $credit->from_main_code).Accounts::fillZero('sub', $credit->from_sub_code));
			$credit->account_name = $account->title;
			$credit->account_code = $account->full_code;
			$credit->datetime = date('d/m/Y h:m', $credit->date);
			$cc = new CostcentersGroup($credit->cc);
			$credit->cc_name = $cc->title;
			$credit->type = "outgoing";
			$bank =  new Accounts(Accounts::fillZero('main', $credit->to_main_code).Accounts::fillZero('sub', $credit->to_sub_code));
			$credit->notes = $bank->title. ' - '. $credit->notes;
			$layout->transactions_trs .= fillTemplate('modules/safems/templates/transactions_table_rows.tpl', $credit);	
		}

		if($tr_only){
			return $layout->_print().$this->getTotalNetTable($begin, $end, 'visa');
		} else {
			$full_layout = new Layout();
			$full_layout->template = 'modules/safems/templates/transactions_layout.tpl';
			$full_layout->title = $lang['banks_report'];
			$full_layout->submit_action= 'submitBankTransTable';
			$full_layout->begin_date = $begin;
			$full_layout->end_date = $end;
			$full_layout->tables = $layout->_print().$this->getTotalNetTable($begin, $end, 'visa');		
			return $full_layout->_print();
		}
	}
	
	public function getAnyObjById($con , $con_id){
		$result = false;
		try{
			switch($con){
				case  "accounts" :
					$result = new Account($con_id);
				break;
				case  "fees" :
					$result = new Fees($con_id);
				break;
				default :
					$result = new Employers($con_id, $this->getHrms());
				break;
			}
			return $result;
		} catch (Exception $e){
			//echo $e;
			return false;
		}
	
	}
	
	public function loadLayout(){
		global $lang;
		$tr_only = true;
		if(isset($_GET['date'])){
			$begin_date =  dateToUnix($_GET['date']);
			$end_date = $begin_date+(60*60*24);
		} elseif(isset($_GET['begin_date'])){
			$begin_date =  dateToUnix($_GET['begin_date']);
			$end_date = isset($_GET['end_date']) || $_GET['begin_date'] == $_GET['end_date'] ? dateToUnix($_GET['end_date']) : $begin_date+(60*60*24);
		} else {
			$begin_date =  mktime(0,0,0, date('m'), date('d'), date('Y'));
			$end_date = $begin_date;
			$tr_only = false;
		}

		$extra = array();
		$extra[] = array(
			'li'=> write_html('li', '', write_html('a', 'href="#transaction_report-'.$this->getAccount()->full_code.'"', $lang['transaction_report'])),
			'div'=> write_html('div', 'id="transaction_report-'.$this->getAccount()->full_code.'"', 
				$this->getTransactions($begin_date, $end_date, $tr_only)
			)
		);	
		$extra[] = array(
			'li'=> write_html('li', '', write_html('a', 'href="index.php?module=safems&daily&safems_id='.$this->id.'"', $lang['daily_report'])),
			'div'=> ''
		);	
		$acc = $this->getAccount();
		return $acc->loadLayout($extra);
	}
	
	static function loadSearchForm(){
		$layout = new Layout();
		$layout->begin_date = mktime(0,0,0, date('m'), date('d'), date('Y'));
		$layout->end_date = mktime(0,0,0, date('m'), date('d'), date('Y'));
		$layout->template = 'modules/safems/templates/transactions_search.tpl';
		return $layout->_print();
	}
	
	static function submit_search($post){
		global $lang, $safems;
		$begin = dateToUnix($post['begin_date']);
		$end = dateToUnix($post['end_date']);
		$main= Accounts::removeZero($post['acc_code_main']);
		$sub= intval($post['acc_code_sub']);		
		$day = (60*60*24);
		$sql_debit = "SELECT value AS debit, currency, date, notes, to_main_code, to_sub_code, from_main_code, from_sub_code, id, cc, type AS payment_type FROM ingoing WHERE date>=$begin AND date<".($end+$day);
		$sql_credit = "SELECT value AS credit, currency, date, notes, from_main_code, from_sub_code, to_main_code, to_sub_code, id, cc, type AS payment_type FROM outgoing WHERE date>=$begin AND date<".($end+$day);
		
		$sql_debit .= " AND to_main_code=$main AND to_sub_code=$sub";
		$sql_credit .= " AND from_main_code=$main AND from_sub_code=$sub";
		$rows_debit = do_query_array($sql_debit, $safems->database, $safems->ip);
		$rows_credit = do_query_array($sql_credit, $safems->database, $safems->ip);
		$layout = new Layout();
		$layout->template = 'modules/safems/templates/transactions_table.tpl';
		$layout->form_hidden = "hidden";
		$acc = new Accounts($post['acc_code_main'].$post['acc_code_sub']);
		$layout->title = $acc->title;
		$layout->begin_date = $begin;
		$layout->end_date = $end;
		$layout->transactions_trs ='';
		$totals = array();
			// Debits
		foreach($rows_debit as $debit){
			if(!isset($totals[$debit->currency])){
				$totals[$debit->currency] = 0;
			}
			$totals[$debit->currency] += $debit->debit;
			$account = new Accounts(Accounts::fillZero('main', $debit->to_main_code).Accounts::fillZero('sub', $debit->to_sub_code));
			$debit->account_name = $account->title;
			$debit->account_code = $account->full_code;
			$debit->datetime = date('d/m/Y h:m', $debit->date);
			$cc = new CostcentersGroup($debit->cc);
			$debit->cc_name = $cc->title;
			$debit->type = "ingoing";
			$bank =  new Accounts(Accounts::fillZero('main', $debit->from_main_code).Accounts::fillZero('sub', $debit->from_sub_code));
			$debit->notes = $bank->title. ' - '. $debit->notes;
			$layout->transactions_trs .= fillTemplate('modules/safems/templates/transactions_table_rows.tpl', $debit);	
		}
			// Credits
		foreach($rows_credit as $credit){
			if(!isset($totals[$credit->currency])){
				$totals[$credit->currency] = 0;
			}
			$totals[$credit->currency] -= $credit->credit;
			$account = new Accounts(Accounts::fillZero('main', $credit->from_main_code).Accounts::fillZero('sub', $credit->from_sub_code));
			$credit->account_name = $account->title;
			$credit->account_code = $account->full_code;
			$credit->datetime = date('d/m/Y h:m', $credit->date);
			$cc = new CostcentersGroup($credit->cc);
			$credit->cc_name = $cc->title;
			$credit->type = "outgoing";
			$bank =  new Accounts(Accounts::fillZero('main', $credit->to_main_code).Accounts::fillZero('sub', $credit->to_sub_code));
			$credit->notes = $bank->title. ' - '. $credit->notes;
			$layout->transactions_trs .= fillTemplate('modules/safems/templates/transactions_table_rows.tpl', $credit);	
		}
		foreach($totals as $cur=>$value){
			$layout->transactions_trs .= write_html('tr', '',
				write_html('th', '', '').
				write_html('th', 'colspan="2"',
				 numberToMoney($value)
				).
				write_html('th', '', $cur).
				write_html('th', 'colspan="5"', $lang['total'])
			);
		}
		
		return $layout->_print();
	}
	
	public function getTotalNet($begin, $end){
		global $safems, $accms;
		$acc = $this->getAccount();
		$year = getYear();
		$total = array();
		$starts = do_query_array("SELECT debit, credit, currency FROM start_balance WHERE main_code=$acc->main_code AND sub_code=$acc->sub_code GROUP BY currency AND year=$year->year", $accms->database, $accms->ip);

		// start balance
		if($starts != false){
			foreach($starts as $start){
				if( ($start->debit - $start->credit) > 0){
					if(!isset($total[$start->currency])){
						$total[$start->currency] = 0;
					}
					$total[$start->currency] += $start->debit - $start->credit;
				}
			}
		}

		// ingoing
		$ins = do_query_array("SELECT SUM(value) AS tot, currency FROM ingoing WHERE from_main_code=$acc->main_code AND from_sub_code=$acc->sub_code AND date>=$begin AND date<$end AND year=$year->year GROUP BY currency", $safems->database, $safems->ip);
		foreach($ins as $in){
			if(!isset($total[$in->currency])){
				$total[$in->currency] = 0;
			}
			$total[$in->currency] += $in->tot;
		}
		
		// OUtgoing
		$outs = do_query_array("SELECT SUM(value) AS tot, currency FROM outgoing WHERE to_main_code=$acc->main_code AND to_sub_code=$acc->sub_code AND date>=$begin AND date<$end AND year=$year->year GROUP BY currency", $safems->database, $safems->ip);
		foreach($outs as $out){
			if(!isset($total[$out->currency])){
				$total[$out->currency] = 0;
			}
			$total[$out->currency] -= $out->tot;
		}
		return $total;
	}
	
	public function getTotalNetTable($begin, $end, $type){
		global $lang;
		$this_account = $this->getAccount();
		$layout = new Layout();
		$layout->template = 'modules/safems/templates/transactions_total.tpl';
		$year = getYear();
		$end = $end+(60*60*24);
		$curs_sql =do_query_array("SELECT currency FROM ingoing UNION SELECT currency FROM outgoing", $this->database, $this->ip);
		$layout->trs ='';
		$year = getNowYear();
		$cur_year_begin = mktime(0,0,0,date('m', $year->begin_date),date('d', $year->begin_date), date('Y', $begin));
		if($cur_year_begin>$begin){
			$cur_year_begin = mktime(0,0,0,date('m', $year->begin_date),date('d', $year->begin_date), (date('Y', $begin)-1));
		}
		if($type == 'cash'){
			 $filter_in = "AND from_main_code=$this_account->main_code AND from_sub_code=$this_account->sub_code";
			 $filter_out = "AND to_main_code=$this_account->main_code AND to_sub_code=$this_account->sub_code";
			foreach($curs_sql as $c){
				$cur = $c->currency;
				// start balance 
				$start = do_query_obj("SELECT debit, credit FROM start_balance WHERE main_code=$this_account->main_code AND sub_code=$this_account->sub_code AND currency='$cur' AND year=$year->year", $this->database, $this->ip);
				$start_balance = $start != false ? $start->debit - $start->credit : 0;
				// ingoing
				$ins_before = do_query_obj("SELECT SUM(value) AS tot FROM ingoing WHERE type='$type' AND date>=$cur_year_begin AND date<$begin AND currency='$cur' ".$filter_in, $this->database, $this->ip);
				$ins = do_query_obj("SELECT SUM(value) AS tot FROM ingoing WHERE type='$type' AND date>=$begin AND date<$end AND currency='$cur' ".$filter_in, $this->database, $this->ip);
				// OUtsgoing
				$outs_before = do_query_obj("SELECT SUM(value) AS tot FROM outgoing WHERE type='$type' AND date>=$cur_year_begin AND date<$begin AND currency='$cur' ".$filter_out, $this->database, $this->ip);
				$outs = do_query_obj("SELECT SUM(value) AS tot FROM outgoing WHERE type='$type' AND `date`>=$begin AND `date`<$end AND currency='$cur' ".$filter_out, $this->database, $this->ip);
				$ins_total_before =  $ins_before != false ? $ins_before->tot : 0;
				$outs_total_before =  $outs_before != false ? $outs_before->tot : 0;
				$ins_total =  $ins != false ? $ins->tot : 0;
				$outs_total =  $outs != false ? $outs->tot : 0;
	
				$layout->trs .= write_html('tr', '',
					write_html('td', '', $cur).
					write_html('td', '', numberToMoney($start_balance + $ins_total_before - $outs_total_before)).
					write_html('td', '', numberToMoney($ins_total)).
					write_html('td', '', numberToMoney($outs_total)).
					write_html('td', '', numberToMoney($ins_total - $outs_total)).
					write_html('td', '', numberToMoney($start_balance + $ins_total_before - $outs_total_before + $ins_total - $outs_total))
				);
				
			}
		} else {
			$filter_in = " AND from_main_code LIKE '161%'";
			foreach($curs_sql as $c){
				$cur = $c->currency;
				$ins = do_query_obj("SELECT SUM(value) AS tot FROM ingoing WHERE type!='cash' AND date>=$begin AND date<$end AND currency='$cur' ".$filter_in, $this->database, $this->ip);
				$ins_total =  $ins != false ? $ins->tot : 0;
	
				$layout->trs .= write_html('tr', '',
					write_html('td', '', $cur).
					write_html('td', '', '&nbsp;').
					write_html('td', '', numberToMoney($ins_total)).
					write_html('td', '', '&nbsp;').
					write_html('td', '',  numberToMoney($ins_total)).
					write_html('td', '', '&nbsp;')
				);
				
			}
		}
		return $layout->_print();
	}

	public function getDailyReport($table='ingoing', $day='', $cc= '', $cur_acc=''){
		global $lang;
		if($table==''){$table = 'ingoing';}
		if($day == '' ){
			$day = mktime(0,0,0,date('m'), date('d'), date('Y'));
		}
		if($cur_acc != ''){
			$acc_main_code = Accounts::removeZero(substr($cur_acc,0,4));
			$acc_sub_code = intval(substr($cur_acc,5,10));
		}
		$direct = $table == 'ingoing' ? 'to' : 'from';
		$end_day = $day+(60*60*24);
		$trs = array();
		$accounts = array();
		$ccs = array();
		$totals = array();
		$recetes = array();
		$tfoot = array();
		$rows =do_query_array( "SELECT
			SUM(value) AS total, 
			COUNT(id) AS ids, 
			".$direct."_main_code, ".$direct."_sub_code, 
			cc, 
			currency
			FROM $table
			WHERE date>=$day AND date<$end_day
			AND ".$direct."_main_code NOT LIKE '151%' ".
			($cc!='' ? "AND cc=$cc " :'').
			($cur_acc!='' ? "AND ".$direct."_main_code=$acc_main_code AND ".$direct."_sub_code=$acc_sub_code " :'').
			"GROUP BY CONCAT(".$direct."_main_code,'-',".$direct."_sub_code), cc, currency", $this->database, $this->ip);
			
		
		foreach($rows as $row){
			$acc = getAccount($row->{$direct."_main_code"}, $row->{$direct."_sub_code"});
			$ccg = new CostCentersGroup($row->cc);
			$trs[] = write_html('tr', '',
				write_html('td', '', 
					write_html('button', 'type="button" safems_id="'.(isset($this->id) ? $this->id :'').'" action="loadAccDailyTrans" main="'.$row->{$direct."_main_code"}.'" sub="'.$row->{$direct."_sub_code"}.'" class="circle_button ui-state-default hoverable"', write_icon('script'))
				).
				write_html('td', '', $acc->title).
				write_html('td', '', $ccg->getName()).
				write_html('td', '', $row->currency).
				write_html('td', '', $row->ids).
				write_html('td', '', numberToMoney($row->total))
			);
			$accounts[$acc->full_code] = $acc->title;
			if(!isset($totals[$row->currency])){
				$totals[$row->currency] = 0;
				$recetes[$row->currency] = 0;
			}
			$totals[$row->currency] += $row->total;
			$recetes[$row->currency] += $row->ids;
		}
		
		$layout = new Layout($this);
		$layout->template = 'modules/safems/templates/daily_report.tpl';
		$layout->safe_name = $this->getSettings('school_name_ar');
		$layout->day = unixToDate($day);
		$layout->account_opts = write_select_options($accounts, $cur_acc, true);
		$layout->cc_opts = write_select_options(CostCentersGroup::getListOpts(), $cc, true);
		$reports = array('ingoing'=>$lang['ingoing'], 'outgoing'=>$lang['outgoing']);
		$layout->table_opts = write_select_options($reports, $table, false);
		$layout->trs= implode('', $trs);
		
		// tfoot
		foreach($totals as $cur=>$val){
			$tfoot[] = write_html('tr', '',
				write_html('th', 'colspan="3"', $lang['total']).
				write_html('th', '', $cur).
				write_html('th', '', $recetes[$cur]).
				write_html('th', '', $val)
			);
		}
		$layout->tfoot = implode('', $tfoot);
		return $layout->_print();
	}
}

?>