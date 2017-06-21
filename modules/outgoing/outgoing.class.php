<?php
/** Outgoing
*
*/
class Outgoing{
	
	public $user_id, $year;
	
	public function __construct($id=''){
		if($id != '' && !is_array($id)){
			$outgoing = do_query_obj("SELECT * FROM outgoing WHERE id=$id", MySql_Database);	
			if(isset($outgoing->id)){
				foreach($outgoing as $key =>$value){
					$this->$key = $value;
				}
				$this->from_acc = new Accounts(Accounts::fillZero('main', $this->from_main_code). Accounts::fillZero('sub', $this->from_sub_code));
				$this->to_acc = new Accounts(Accounts::fillZero('main', $this->to_main_code). Accounts::fillZero('sub', $this->to_sub_code));
				$this->recete_notes = $this->notes;
			}			
		} elseif(is_array($id)){
			$data = $id;
			$this->user_id = $_SESSION['user_id'];
			$this->year = date('Y', getYearSetting('begin_date'));
			$this->date = isset($id['date']) ? $id['date'] : time();
			$this->from_acc = $data['from_acc'];
			$this->from_main_code = $data['from_acc']->main_code;
			$this->from_sub_code = $data['from_acc']->sub_code;
			$this->to_acc = $data['to_acc'];
			$this->to_main_code = $data['to_acc']->main_code;
			$this->to_sub_code = $data['to_acc']->sub_code;
			$this->cc = $data['ccid'];
			$this->value = $data['value'];
			$this->currency = $data['currency'];
			$this->type = $data['type'];
			$this->notes = $data['notes'];
			$this->recete_notes = isset($data['recete_notes']) ? $data['recete_notes'] : $data['notes'];
		}
	}
	
	public function _save(){
		$this->id = do_insert_obj($this, 'outgoing', MySql_Database);
		return $this->id;
	}
	
	static function addNewPayment($post){
		global $this_system, $lang;
		$result = true;
		if(isset($post['from_sub']) && $post['from_sub'] != ''){
			$full_code =  Accounts::fillZero('main', $post['from_main']). Accounts::fillZero('sub', $post['from_sub']);
			$from_acc = new Accounts( $full_code );
		} elseif(isset($post['from']) && $post['from'] != ''){
			$from_acc = new Accounts( $post['from'] );
		} 
		$post['from_acc'] = $from_acc;

		if(isset($post['to']) && $post['to'] != ''){
			$to_acc = new Accounts($post['to']);			
		} else {
			if($post['type'] == 'cash'){
				$to_acc = new Accounts($this_system->getSettings('this_acc_code'));
			} else {
				$to_acc = new Accounts($post['bank']);
			}
		}
		if(!isset($post['notes']) || $post['notes']==''){
			$post['notes'] = $to_acc->title.(isset($post['name']) && $post['name']!='' ? ' - '.$post['name'] : '') ;
			$post['recete_notes'] = $to_acc->title;
		} else {
			$post['recete_notes'] = $post['notes'];
		}
		
		$post['to_acc'] = $to_acc;
		if( $to_acc->full_code != $this_system->getSettings('this_acc_code')){
			$pre_outgoing = $post;
			$pre_outgoing['from_acc'] = $to_acc;
			if($post['type'] == 'cash'){
				$pre_outgoing['to_acc'] = new Accounts($this_system->getSettings('this_acc_code'));
			} else {
				$pre_outgoing['to_acc'] = new Accounts($post['bank']);
			}
			$pre = new Outgoing($pre_outgoing);
			$pre->_save();
		}
		
					
		$outgoing = new Outgoing($post);
		$outgoing->id = $outgoing->_save();

		return $outgoing != false ? (isset($pre) ? $pre : $outgoing) : false;
	}
	
	public function getRecete(){
		global $lang, $this_system, $nts, $hrms;		
		$user = new Employers( $this->user_id, $hrms);
		$recete = new layout($this);
		$recete->template = 'modules/outgoing/templates/recete.tpl';
		$recete->user_name = $user->getName();
		$recete->date = date('d/m/Y h:m', $this->date);
		$recete->no = $this->id;
		$recete->years = $this->year.' / '. ($this->year+1);
		$this_from = $this->from_acc;
		$recete->from_name =  $this_from->full_code != $this_system->getSettings('this_acc_code') ? $this_from->title : $lang['cash_client'];
		$recete->from_code = $this->from_acc->full_code != $this_system->getSettings('this_acc_code') ? $this->from_acc->full_code : '&nbsp;';
		$recete->receipt_title = substr( $recete->from_code ,0,2) != '161' ? $lang['receipt_receiving'] : $lang['receipt_deposit'];
		$recete->recete_notes = $this->recete_notes;
		$recete->payment_type = $this->type;
		$recete->safe_code = $this_system->getSettings('this_acc_code');
		//$recete->title = $this->getName();
		$val = intval($this->value);
		$recete->str_value = $nts->int2str($val). ' '. $this->currency;		
		return $recete->_print();
	}
		
	static function loadMainLayout(){		
		$layout = new stdClass();
		$search_form = new stdClass();
		$smss = SMS::getList();				
		$menu = new stdClass();
		$menu->school_lis = '';
		foreach($smss as $sms){
			$menu->school_lis .= write_html('li', '',
				write_html('a', 'class="ui-state-default hoverable" action="openRefund" sms_id="'.$sms->id.'"', $sms->code)
			);
		}
		
		$layout->menu = fillTemplate('modules/outgoing/templates/outgoing_menu.tpl', $menu);
		return  fillTemplate('modules/outgoing/templates/main_layout.tpl', $layout);
	}
	
	static function loadDepositForm(){
		global $this_system, $lang;
		$accms = new Accms();
		$layout = new  Layout();
		$layout->to_code = $this_system->getSettings('this_acc_code');
		$layout->from_opts = '';
		$money_acc = do_query_array("SELECT * FROM sub_codes WHERE main like '16%'",$accms->database, $accms->ip);
		foreach($money_acc as $acc){
			$fullcode = Accounts::fillZero('main', $acc->main).Accounts::fillZero('sub', $acc->sub);
			$layout->from_opts .=write_html('option', 'value="'.$fullcode.'"', $acc->title);
		}
		$layout->currency_opts = Currency::getOptions($this_system->getSettings('def_currency'));
		$layout->template = 'modules/outgoing/templates/deposit_layout.tpl';
		return $layout->_print();

	}
	
	static function loadLayout($type){
		global $this_system, $lang;
		$layout = new  Layout();
		$layout->schools_opts = '';
		$smss = SMS::getList();
		$layout->banks_opts = Banks::getOptions();
				
		$layout->schools_opts = '';
		$first_sms = false;
		foreach($smss as $sms){
			$layout->schools_opts .= write_html('option', 'value="'.$sms->getCC().'" '.(!$first_sms? 'selected="selected"': ''), $sms->getCode());
			if(!$first_sms) {
				$first_sms = $sms;
			}
		}

		if($type == 'applications'){
			$income_code = $this_system->getSettings('application_acc');
			$layout->from_code = $this_system->getSettings('this_acc_code');
		} elseif($type == 'admission'){ 
			$income_code = $this_system->getSettings('admission_acc');
		} elseif($type == 'book_insur'){ 
			$income_code = $this_system->getSettings('insur_book_acc');
		} elseif($type == 'lockers_rent'){ 
			$income_code = $this_system->getSettings('insur_locker_acc');
		}
		$layout->this_code = $this_system->getSettings('this_acc_code');
		$layout->income_code = $income_code; //Accounts::removeZero($applcation_fees_code->getCode());
		$layout->from_main = $first_sms->getAccCode();
		$layout->title = $lang[$type];
		$layout->def_currency = $this_system->getSettings('def_currency');
		return fillTemplate('modules/outgoing/templates/income_layout.tpl', $layout);
	}
	
	static function othersLayout(){
		global $this_system, $lang;
		$layout = new stdClass();
		$search_form = new stdClass();
		$smss = SMS::getList();
				
		/*$layout->schools_opts = write_html('option', 'value="Costcenters"', $lang['general']);
		foreach($smss as $sms){
			$layout->schools_opts .= write_html('option', 'value="'.$sms->getCC().'"', $sms->getCode());
		}*/
		$layout->schools_opts = write_select_options(CostcentersGroup::getListOpts(), $this_system->getSettings('cc_group_id'), false);
		$layout->currency_opts = Currency::getOptions($this_system->getSettings('def_currency'));
		$layout->date = unixToDate(time());
		$this_acc = new Accounts($this_system->getSettings('this_acc_code'));
		$layout->to_code = $this_system->getSettings('this_acc_code');
		return  fillTemplate('modules/outgoing/templates/outgoing_others.tpl', $layout);
	}
	
	static function getList($code, $dates, $rows_only){
		global $this_system;
		$layout = new stdClass();
		$layout->income_code = $code;
		$layout->begin_date = isset($dates['begin_date']) ? $dates['begin_date'] : $this_system->getYearSetting('begin_date');
		$layout->end_date = isset($dates['end_date']) ? $dates['end_date'] : $this_system->getYearSetting('end_date');
		
		$acc = new Accounts($code);
		$rows = do_query_array("SELECT * FROM outgoing WHERE to_main_code='$acc->main_code' AND to_sub_code='$acc->sub_code' AND date>=$layout->begin_date AND date<=$layout->end_date", MySql_Database);
		$layout->app_list_trs = '';
		$i = 1;
		$total =0;
		foreach($rows as $row){
			$cc = do_query_obj("SELECT code FROM connections WHERE type='sms' AND ccid=$row->cc", SAFEMS_Database);
			//$cc = new CostCenters($row->cc);
			$layout->app_list_trs .= write_html('tr', '',
				write_html('td', '', $i).
				write_html('td', '',unixToDate($row->date)).
				write_html('td', '', $cc->code).
				write_html('td', '', $row->notes).
				write_html('td', '', $row->value.' '. $this_system->getSettings('def_currency'))
			);
			$i++;
			$total += $row->value;
		}
		$layout->total = $total;
		
		if($rows_only){
			return $layout->app_list_trs;
		}
		return fillTemplate('modules/outgoing/templates/incomes_list.tpl', $layout);
	}
	
}