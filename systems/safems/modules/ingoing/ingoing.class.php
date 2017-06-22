<?php
/** Ingoing
*
*/
class Ingoing{
	public $user_id, $year;
	
	public function __construct($id=''){
		if($id != '' && !is_array($id)){
			$ingoing = do_query_obj("SELECT * FROM ingoing WHERE id=$id", MySql_Database);	
			if(isset($ingoing->id)){
				foreach($ingoing as $key =>$value){
					$this->$key = $value;
				}
				$this->from_acc = new Accounts(Accounts::fillZero('main', $this->from_main_code). Accounts::fillZero('sub', $this->from_sub_code));
				$this->to_acc = new Accounts(Accounts::fillZero('main', $this->to_main_code). Accounts::fillZero('sub', $this->to_sub_code));
				$this->recete_notes = $this->notes;
			}			
		} elseif(is_array($id)){
			$data = $id;
			$this->user_id = $_SESSION['user_id'];
			$this->year = isset($data['year']) ? $data['year'] : date('Y', getYearSetting('begin_date'));
			$this->date = isset($data['date']) ? $data['date'] : time();
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
		global $lang;
		$this->id = do_insert_obj($this, 'ingoing');
		if(substr($this->to_main_code ,0,3) == '151' && $this->to_acc->exists == false){
			$sms = new SMS($this->cc);
			$student = new Students($this->to_sub_code, $sms);
			$this->to_acc = $student->getAccount();
			/*$new_acc = array(
				'precode'=> $this->to_main_code,
				'acc_code_sub'=> $this->to_sub_code,
				'title'=> $lang['student'].'/ '.$student->name_ar,
				'group_id'=> $this->cc
			);
			$result = json_decode(Accounts::saveNewAcc($new_acc));
			if($result->error == ''){
				$this->to_acc = new Accounts( Accounts::fillZero('main', $this->to_main_code). Accounts::fillZero('sub', $this->to_sub_code));	
			}*/
		}
		return $this->id;
	}
	
	static function addNewPayment($post){
		global $this_system, $lang, $nts;
		//print_r($post);
		$result = true;
		if(isset($post['to_sub']) && $post['to_sub'] != ''){
			$full_code =  Accounts::fillZero('main', $post['to_main']). Accounts::fillZero('sub', $post['to_sub']);
			$to_acc = new Accounts( $full_code );
		} elseif(isset($post['to']) && $post['to'] != ''){
			$to_acc = new Accounts( $post['to'] );
		} 
		$post['to_acc'] = $to_acc;

		if(isset($post['from_sub']) && $post['from_sub'] != ''){ // Others ingoings
			//$full_code =  Accounts::fillZero('main', $post['from_main']). Accounts::fillZero('sub', $post['from_sub']);
			$from_acc = getAccount( $post['from_main'], $post['from_sub'] );
			
		} elseif(isset($post['from']) && $post['from'] != $this_system->getSettings('this_acc_code')) { // Like student ??
			$from_acc = new Accounts($post['from']);
			
		} else {
			if($post['type'] == 'cash'){
				$from_acc = new Accounts($this_system->getSettings('this_acc_code'));
			} else {
				$from_acc = new Accounts($post['bank']);
			}
		}
					
		$post['from_acc'] = $from_acc;
		if(!isset($post['notes']) || $post['notes']==''){
			$post['notes'] = $to_acc->title.(isset($post['name']) && $post['name']!='' ? ' - '.$post['name'] : '') ;
		}
		if(!isset($post['recete_notes']) || $post['recete_notes']==''){
			$post['recete_notes'] = $post['notes'];
		}
		if( substr($from_acc->full_code ,0,2) != '16' ){
			$pre_ingoing = $post;
			$pre_ingoing['to_acc'] = $from_acc;
			if($post['type'] == 'cash'){
				$pre_ingoing['from_acc'] = new Accounts($this_system->getSettings('this_acc_code'));
			} else {
				$pre_ingoing['from_acc'] = new Accounts($post['bank']);
			}
			$pre = new Ingoing($pre_ingoing);
			$pre->_save();
		}
		$ingoing = new Ingoing($post);
		$ingoing->id = $ingoing->_save();
	//	$ingoing->addToSyncQueues();
		if(isset($pre)){
			return $pre->id != false ? $pre : false;
		} else {
			return $ingoing->id != false ? $ingoing : false;
		}
	}
	
	public function getRecete(){
		global $lang, $this_system, $nts, $hrms;
		$user = new Employers($this->user_id, $hrms);
		$recete = $this;
		$recete->user_name = $user->getName();
		$recete->date = date('d/m/Y h:m', $this->date);
		$recete->no = $this->id;
		$recete->years = $this->year.' / '. ($this->year+1);
		$this_to = $this->to_acc;
		$recete->to_name =  isset($this->to_name) ? $this->toname : ($this_to->full_code != $this_system->getSettings('this_acc_code') ? $this_to->title : $lang['cash_client']);
		$recete->to_code = $this->to_acc->full_code != $this_system->getSettings('this_acc_code') ? $this->to_acc->full_code : '&nbsp;';
		$recete->recete_notes = $this->recete_notes;
		$recete->payment_type = $this->type;
		$recete->safe_code = $this_system->getSettings('this_acc_code');
		//$recete->title = $this->getName();
		$val = intval($this->value);
		$recete->str_value = $nts->int2str($val). ' '. $lang[strtolower($this->currency)];	
		if($this->to_acc->full_code== '2300000012'){
			$recete->header = 'header2.jpg';
		} else{
			$recete->header = 'header.jpg';
		}
		return fillTemplate('modules/ingoing/templates/recete.tpl', $recete);
	}
	 
		
	static function loadMainLayout(){		
		$layout = new stdClass();
		$search_form = new stdClass();
		$smss = SMS::getList();				
		$menu = new stdClass();
		$menu->school_lis = '';
		foreach($smss as $sms){
			$menu->school_lis .= write_html('li', '',
				write_html('a', 'class="ui-state-default hoverable" module="fees" action="openSchoolFees" school_id="'.$sms->id.'"', $sms->code)
			);
		}
		
		$layout->menu = fillTemplate('modules/ingoing/templates/ingoing_menu.tpl', $menu);
		return  fillTemplate('modules/ingoing/templates/main_layout.tpl', $layout);
	}
	
	static function loadAdmisionLayout(){
		global $this_system, $lang;
		$layout = new  Layout();
		$layout->template = 'modules/ingoing/templates/admission.tpl';
		$layout->banks_opts = Banks::getOptions($this_system->getSettings('def_bank'));
		$layout->def_currency = $this_system->getSettings('def_currency');
		$smss = SMS::getList();
		$layout->schools_opts = write_select_options(Costcenters::getListOpts(), $this_system->getSettings('cc_group_id'), false);
		$layout->cur_date = unixToDate(time());
		$admission_code = $this_system->getSettings('admission_acc');
		$adv_admission_code = $this_system->getSettings('admission_acc_adv');
		$year = getNowYear();
		$layout->to_opts = write_html('option', 'value="'.$admission_code.'"', $year->year.'/'.($year->year+1)).
			write_html('option', 'selected="selected" value="'.$adv_admission_code.'"', ($year->year+1).'/'.($year->year+2));
		//$layout->notes = $lang['admission'];
		$first_sms = reset($smss);
		$layout->from_main = $first_sms->getAccCode();
		$layout->type = 'admission';
		return $layout->_print();
		
	}
	
	static function loadLayout($type){
		global $this_system, $lang;
		$layout = new  Layout();
		$layout->banks_opts = Banks::getOptions($this_system->getSettings('def_bank'));
		$layout->def_currency = $this_system->getSettings('def_currency');
		$smss = SMS::getList();
		$layout->schools_opts = write_select_options(Costcenters::getListOpts(), $this_system->getSettings('cc_group_id'), false);
		$layout->cur_date = unixToDate(time());
		if($type == 'applications'){
			$income_code = $this_system->getSettings('application_acc');
		//	$layout->from_code = $this_system->getSettings('this_acc_code');
			$options = write_html('select', 'class="combobox" name="to"', Custodys::getOptions('2'));
			$applic_acc = new Accounts($income_code);
			$layout->notes = 'ملفات';
		} else {
			if($type == 'book_insur'){ 
				$income_code = $this_system->getSettings('insur_book_acc');
			} elseif($type == 'lockers_rent'){ 
				$income_code = $this_system->getSettings('insur_locker_acc');
			}
			$first_sms = reset($smss);
			$layout->to_input = '<input type="hidden" name="to" value="[@income_code]" />';
			$options = '<input class="input_double" name="name" type="text">
				<input name="from_sub" class="autocomplete_value required" type="hidden" value="">
				<input name="from_main" type="hidden" value="'.$first_sms->getAccCode().'">';
		}
		$layout->option_tr = write_html('tr', '',
			write_html('td', '', 
				write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align"', $lang['name'])).
			write_html('td', '', $options)
		);
		$layout->this_code = $this_system->getSettings('this_acc_code');
		$income_acc = new Accounts($income_code);
		$layout->income_code = $income_code; //Accounts::removeZero($applcation_fees_code->getCode());
		$layout->type = $type;
		$layout->title = $lang[$type];
		return fillTemplate('modules/ingoing/templates/income_layout.tpl', $layout);
	}
	
	static function othersLayout(){
		global $this_system, $lang;
		$layout = new Layout();
		$layout->template = 'modules/ingoing/templates/ingoing_others.tpl';
		$search_form = new Layout();
		$layout->date = unixToDate(time());
		$layout->banks_opts = Banks::getOptions($this_system->getSettings('def_bank'));		
		$layout->schools_opts = write_select_options(CostcentersGroup::getListOpts(), $this_system->getSettings('cc_group_id'), false);
		$layout->currency_opts = Currency::getOptions($this_system->getSettings('def_currency'));
		$layout->from_code = $this_system->getSettings('this_acc_code');
		return  $layout->_print();
	}
	
	static function getList($type, $dates, $rows_only, $direction="ingoing"){
		global $this_system;
		$layout = new stdClass();
		$layout->type = $type;
		$layout->begin_date = isset($dates['begin_date']) ? $dates['begin_date'] : $this_system->getYearSetting('begin_date');
		$layout->end_date = isset($dates['end_date']) ? $dates['end_date'] : $this_system->getYearSetting('end_date');
		$codes = array();
		if($type == 'admission'){
			$codes[] = $this_system->getSettings('admission_acc');
			$codes[] = $this_system->getSettings('admission_acc_adv');
		} elseif($type == 'applications'){
			$accs = Custodys::getList(2);
			foreach($accs as $cust){
				$codes[] = $cust->full_code;
			}
		} elseif($type == 'book_insur'){
			$codes[] = $this_system->getSettings('insur_book_acc');
		} elseif($type == 'lockers_rent'){
			$codes[] = $this_system->getSettings('insur_locker_acc');
		}
		$rows = array();
		foreach($codes as $code){
			$acc = new Accounts($code);
			if($direction=='ingoing'){
				$new_rows = do_query_array("SELECT * FROM ingoing WHERE to_main_code='$acc->main_code' AND to_sub_code='$acc->sub_code' AND date>=$layout->begin_date AND date<$layout->end_date", $this_system->database, $this_system->ip);
			} else {
				$new_rows = do_query_array("SELECT * FROM outgoing WHERE from_main_code='$acc->main_code' AND from_sub_code='$acc->sub_code' AND date>=$layout->begin_date AND date<$layout->end_date", $this_system->database, $this_system->ip);
			}
			if($new_rows!= false){
				$rows = array_merge($rows, $new_rows);
			}
		}
		$layout->app_list_trs = '';
		$i = 1;
		$total =0;
		foreach($rows as $row){
			//$cc = do_query_obj("SELECT code FROM connections WHERE type='sms' AND ccid=$row->cc", $this_system->database, $this_system->ip);
			if($type == 'applications'){
				$display_acc = getAccount($row->to_main_code, $row->to_sub_code);
			} else{
				if( $direction=='outgoing'){
					$display_acc = getAccount($row->to_main_code, $row->to_sub_code);
				} else {
					$display_acc = getAccount($row->from_main_code, $row->from_sub_code);
				}
			}
			$cc = new CostcentersGroup($row->cc);
			$layout->app_list_trs .= write_html('tr', '',
				write_html('td', '', $i).
				write_html('td', '',unixToDate($row->date)).
				write_html('td', '', $display_acc->title).
				write_html('td', '', $cc->title).
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
		return fillTemplate('modules/ingoing/templates/incomes_list.tpl', $layout);
	}
	
}