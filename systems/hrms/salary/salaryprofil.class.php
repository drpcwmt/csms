<?php
/** Salary Profils 
*
*
*/

class SalaryProfil{
				
	public function __construct($id){
		if($id != '' && $id != false){
			global $this_system;
			$profil = do_query_obj("SELECT * FROM jobs_profil WHERE id=$id");
			if(isset($profil->id)){ 
				foreach($profil as $key =>$value){
					$this->$key = $value;
				}
			} else {
				//throw new Exception('id Not Found: ');
			}
		} else {
			//throw new Exception('id Not Found');
		}
	}
	
	public function getProfilElmnts(){
		$out = array();
		$out['debit'] = array();
		$out['credit'] = array();
		$profils = do_query_array("SELECT * FROM salary_pr_el WHERE profil_id=$this->id");
		foreach($profils as $profil){
			if($profil->type == 'debit'){
				$out['debit'][] = $profil->elmnt_id;
			} else {
				$out['credit'][] =  $profil->elmnt_id;
			}
		}
		return $out;
	}
	
	public function getEmpDayValue($emp, $eq=''){
		if($eq == ''){
			$eq = $this->abs_conv_value;
		}
		$calculator = new Calculator($emp);
		return $calculator->calculate($eq);
	}
	
	public function loadLayout(){
		global $hrms;
		$layout_settings = new Layout($this);
		$layout_settings->basic_cur_lis = Currency::getOptions(isset($this->basic_cur) && $this->basic_cur !='' ? $this->basic_cur : '');
		$layout_settings->var_cur_lis = Currency::getOptions(isset($this->var_cur) && $this->var_cur!='' ? $this->var_cur : '');
		$layout_settings->template = 'modules/salary/templates/profil_data.tpl';
		$elements = $this->getProfilElmnts();
		$layout_settings->profil_id = $this->id;
		$layout_settings->debit_ul = '';
		$layout_settings->credit_ul = '';
		if(count($elements['debit']) > 0){
			foreach($elements['debit'] as $elmnt){
				$ele = do_query_obj("SELECT * FROM salary_profil_elmnt WHERE id=$elmnt");
				$elem = new Layout($ele);
				$elem->profil_id = $this->id;
				$elem->template = 'modules/salary/templates/elemt.tpl';
				$layout_settings->debit_ul .= $elem->_print();
			}
		}
		if(count($elements['credit']) > 0){
			foreach($elements['credit'] as $elmnt){
				$ele = do_query_obj("SELECT * FROM salary_profil_elmnt WHERE id=$elmnt");
				$elem = new Layout($ele);
				$elem->profil_id = $this->id;
				$elem->template = 'modules/salary/templates/elemt.tpl';
				$layout_settings->credit_ul .= $elem->_print();
			}
		}
		/*$layout = new Layout($this);
		$layout->template = 'modules/salary/templates/profil_layout.tpl';
		$dates = Absents::getDateInterval('month', date('m'));
		$begin = $dates['begin'];
		$end = $dates['end'];
		$layout->emps_table = $this->getEmpTable($begin, $end);
		$layout->settings = $layout_settings->_print();
		$layout->credit_th = $list_credit_th;
		$layout->debit_th = $list_debit_th;*/
		
		return $layout_settings->_print();
	}
	
	public function getEmployers($cc=''){
		$out = array();
		if($cc == ''){
			$emps = do_query_array("SELECT emp_id FROM salary WHERE profil_id=$this->id ORDER BY emp_id ASC");
		} else {
			$emps = do_query_array("SELECT salary.emp_id FROM employer_data,salary WHERE employer_data.id=salary.emp_id AND employer_data.school=$cc AND salary.profil_id=$this->id ORDER BY salary.emp_id ASC");
		}
		foreach($emps as $e){
			$em = new Employers($e->emp_id);
			if(isset($em->id)){
				$out[] = $em;
			}
		}
		return $out;
	}
	
	public function getEmpTable($begin, $end, $cc='', $payment_mode=''){
		global $lang;
		$emps = $this->getEmployers($cc);
		$elements = $this->getProfilElmnts();
		$list_credit_th = '';
		$list_debit_th = '';
		if(count($elements['debit']) > 0){
			foreach($elements['debit'] as $elmnt){
				$ele = do_query_obj("SELECT * FROM salary_profil_elmnt WHERE id=$elmnt");
				$list_debit_th .= write_html('th', '', $ele->name);
			}
		}
		if(count($elements['credit']) > 0){
			foreach($elements['credit'] as $elmnt){
				$ele = do_query_obj("SELECT * FROM salary_profil_elmnt WHERE id=$elmnt");
				$list_credit_th .= write_html('th', '', $ele->name);
			}
		}
		$trs = array();
		$list_credit_td = '';
		$list_debit_td = '';
		foreach($emps as $emp){
			if( $emp->status=='1' && ($payment_mode=='0' || $payment_mode==$emp->salary_from)){ 
				$total_debit=array();
				$total_credit = array();
				$row = new Layout($emp);
				$row->template = 'modules/salary/templates/salary_emp_table_row.tpl';
				$row->name = $emp->getName();
				$row->code = $emp->getAccCode();
				$row->credit_td = '';
				$row->debit_td = '';
				$calculator = new Calculator($emp);
				$calculator->begin= $begin;
				$calculator->end = $end;
				if(count($elements['debit']) > 0){
					foreach($elements['debit'] as $elmnt){
						$ele = do_query_obj("SELECT * FROM salary_profil_elmnt WHERE id=$elmnt");
						$value = $calculator->calculate($ele->value);
						$row->debit_td .= write_html('td', '', $value);
						if(!array_key_exists($ele->currency, $total_debit)){
							$total_debit[$ele->currency] = 0;
						}
						$total_debit[$ele->currency] += $value;
					}
				}
				if(count($elements['credit']) > 0){
					foreach($elements['credit'] as $elmnt){
						$ele = do_query_obj("SELECT * FROM salary_profil_elmnt WHERE id=$elmnt");
						$value = $calculator->calculate($ele->value);
						$row->credit_td .= write_html('td', '', $value);
						if(!array_key_exists($ele->currency, $total_credit)){
							$total_credit[$ele->currency] = 0;
						}
						$total_credit[$ele->currency] += $value;
					}
				}
				if(count($total_credit) >1 || count($total_debit) > 1){
					$first = true;
					foreach($total_credit as $cur => $value){
						$trs[] = write_html('tr', '',
							''
						);
					}
					
				} else {
					$row->total_salary_credit = reset ($total_credit);
					$row->total_salary_debit = reset ($total_debit);
					$row->salary_net = numberToMoney($row->total_salary_credit - $row->total_salary_debit);
					$trs[] = $row->_print();
				}
			}
		}
		if(count($trs) > 0){
			$layout = new Layout($this);
			$layout->credit_th = $list_credit_th;
			$layout->debit_th = $list_debit_th;
			$layout->template = 'modules/salary/templates/salary_emp_table.tpl';
			$layout->trs = implode('', $trs);
			return $layout->_print();
		} else {
			return write_error(write_html('h2', '',$lang['emps_not_found']));
		}
		
	}
	
	static function getList(){
		global $hrms;
		$profils = do_query_array("SELECT * FROM jobs_profil", $hrms->database, $hrms->ip);
		$out = array();
		foreach($profils as $profil){
			$out[] = new SalaryProfil($profil->id);
		}
		return sortArrayOfObjects($out, $hrms->getItemOrder('profils'), 'id');
	}
	
	static function getOptions($selected=''){
		global $MS_settings;
		$profils = SalaryProfil::getList();
		$selec = array();
		foreach($profils as $profil){
			$selec[$profil->id] = $profil->title;
		}
		return write_select_options( $selec, $selected, false);
	}
	
	static function newProfil(){
		global $hrms;
		$layout = new Layout();
		$layout->con = 'profil';
		$layout->basic_cur_lis = Currency::getOptions();
		$layout->var_cur_lis = Currency::getOptions();
		$layout->toolbox_placeholder = 'hidden';
		$layout->salary_sheet_fieldset = 'hidden';
		$layout->template = 'modules/salary/templates/profil_data.tpl';
		
		return $layout->_print();
	}
	
	static function _save($post){
		global $hrms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'jobs_profil', $hrms->database, $hrms->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id'])){
			$id = do_insert_obj($post, 'jobs_profil', $hrms->database, $hrms->ip);
			$result = $id != false ? true : false;
		}
		
		if($result!=false){

			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function _delete($id){
		if($id != '' ){
			if(do_delete_obj("id=$id", 'jobs_profil'	)){
				do_delete_obj("profil_id=$id", 'salary_profil_elemt');
				return true;
			}
		} else {
			return false;
		}
	}
	
	static function newElementForm($type){
		global $this_system;
	//	$elemts = do_query_array("SELECT salary_profil_elmnt.id, salary_profil_elmnt.name FROM salary_profil_elmnt, salary_pr_el WHERE salary_pr_el.elmnt_id=salary_profil_elmnt.id AND salary_pr_el.type='$type'");
		$elemts = do_query_array("SELECT salary_profil_elmnt.id, salary_profil_elmnt.name FROM salary_profil_elmnt");
		$select = array();
		foreach($elemts as $el){
			$select[$el->id] = $el->name;
		}
		$form = new Layout();
		$form->field = $type;
		$form->elmnts_select = write_html_select('name="elmnt_id"', $select);
		$form->currency_opts = Currency::getOptions($this_system->getSettings('def_currency'));
		$form->template = 'modules/salary/templates/salary_elmnt_form.tpl';
		return $form->_print();	
	}
		
	static function saveNewElemnt($post){
		$profil = new SalaryProfil($post['profil_id']);
		//$elements = $profil->getProfilElmnts();
		if(isset($post['value'])){
			$post['value'] = $post['type'] == 'fx' ? $post['value'] : $post['equation'];
			$elmnt_id = do_insert_obj($post, 'salary_profil_elmnt');
		} else {
			$elmnt_id = $post['elmnt_id'];
		}
		
		do_insert_obj( array('profil_id'=>$post['profil_id'], 'elmnt_id'=>$elmnt_id, 'type'=>$post['field']), 'salary_pr_el');

		$elem = new Layout($post);
		$elem->id = $elmnt_id;
		$elem->profil_id = $profil->id;
		$elem->template = 'modules/salary/templates/elemt.tpl';
		return array(
			'error' => '',
			'html' =>$elem->_print()
		);
	}
	
	static function deleteElemnt($post){
		$profil = new SalaryProfil($post['profil_id']);
		$elemts = $profil->getProfilElmnts();
		$elemt_id = $post['elemnt_id'];
		if(do_delete_obj("id=$elemt_id", 'salary_profil_elmnt')){
			do_delete_obj("elemt_id=$elemt_id", 'salary_pr_el');
			return array('error'=>'');
		} else {
			return array('error'=>'Error');
		}
	}
	
	static function loadMainLayout(){
		$layout = new Layout();
		$layout->template = 'modules/salary/templates/profils_main_layout.tpl';
		$profils = salaryProfil::getList();
		$layout->profils_list = '';
		$first = true;
		foreach($profils as $profil){
			$layout->profils_list .= write_html( 'li', 'profil_id="'.$profil->id.'" itemid="'.$profil->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openProfil"', 
				write_html('text', 'class="holder-profil-'.$profil->id.'"',
					$profil->title
				)
			);
			if($first){
				$profil = new SalaryProfil($profil->id);
				$layout->profil_layout = $profil->loadLayout();	
			}
			$first = false;
		}
		return $layout->_print();
	}
}
?>