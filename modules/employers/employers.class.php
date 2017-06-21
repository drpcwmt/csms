<?php
/** Employers 
*
*
*/

class Employers{		
	public function __construct($id, $hrms=''){
		if($hrms==''){
			global $hrms;
		}
		$this->hrms = $hrms;
		if($id != '' && $id != false){
			$employer = do_query_obj("SELECT * FROM employer_data WHERE id=$id", $hrms->database, $hrms->ip);
			if(isset($employer->id)){ 
				foreach($employer as $key =>$value){
					$this->$key = $value;
				}
				$insur = do_query_obj("SELECT * FROM insurance WHERE emp_id=$this->id", $hrms->database, $hrms->ip);
				if(isset($insur->emp_id)){ 
					foreach($insur as $key =>$value){
						$this->$key = $value;
					}
				} else {
					$this->insur_no ='';
				}
				$salary = do_query_obj("SELECT * FROM salary WHERE emp_id=$this->id", $hrms->database, $hrms->ip);
				if(isset($salary->emp_id)){ 
					foreach($salary as $key =>$value){
						$this->$key = $value;
					}
				} else {
					$this->basic = 0;
					$this->basic_cur = $hrms->getSettings('def_currency');
					$this->var = 0;	
					$this->var_cur = $hrms->getSettings('def_currency');
					$this->allowances = 0;	
					$this->allowances_cur = $hrms->getSettings('def_currency');
					$this->profil_id = $this->job_code;
				}
			} else {
				//throw new Exception('id Not Found: ');
			}
		} else {
			//throw new Exception('id Not Found');
		}
	}
	
	public function getName(){
		if(!isset($this->getName)){
			$this->getName = $_SESSION['lang'] == 'ar' ? $this->name_rtl : $this->name_ltr;
		}
		return $this->getName;
	}
	
	public function getAccCode(){
		$main_code = Accounts::fillZero('main', '152'.$this->school.$this->job_code);
		$sub_code = Accounts::fillZero('sub', $this->id);
		return $main_code . strval($sub_code);
	}

	public function getTel($all=false){
		$rows = do_query_array("SELECT * FROM phonebook WHERE con='emp' AND con_id=$this->id", $this->hrms->database, $this->hrms->ip);
		echo 
		$rows = sortArrayOfObjects($rows, $this->hrms->getItemOrder('phonebook-emp-'.$this->id), 'id');
		print_r($rows);
		if( $all==false ){
			$first = reset($rows);
			return isset($first->tel) ? $first->tel : '';
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $r){
					$out[] = $r->tel;
				} 
			}
			return $out;
		}
	}

	/*public function getTel(){
		$out = array();
		if($this->mobil != ''){
			$out[] = $this->mobil;
		} 
		if($this->tel != ''){
			$out[] = $this->tel;
		} 
		
		return  $out;
	}*/
	
	public function getJob(){
		if(!isset($this->job)){
			$this->job = new Jobs($this->job_code);
		} 
		return $this->job;
	}
	
	public function getProfil(){
		if(!isset($this->profil)){
			$salary = do_query_obj("SELECT * FROM salary WHERE emp_id=$this->id", $this->hrms->database, $this->hrms->ip);
			if(isset($salary->profil_id) && $salary->profil_id !=''){
				$profil = new SalaryProfil($salary->profil_id);
			} else {
				$p = do_query_obj("SELECT id FROM jobs_profil WHERE con='job' AND con_id=$this->job_code",$this->hrms->database, $this->hrms->ip);
				$profil = new SalaryProfil(isset($p->id) ? $p->id : '');
			}
			if(isset($salary->emp_id)){
				foreach($salary as $key =>$value){
					if(($value != '' || $value != 0)){
						$profil->$key = $value;
					}
				}
			} else {
				$profil = new SalaryProfil('');
				$profil->basic = 0;
				$profil->var = 0;
				$profil->allowances = 0;
				$profil->salary_from = '0';
				$profil->abs_conv_value = 0;
				$profil->absent_conv = 0;
				
			}
			$this->profil =$profil;
		}
		return $this->profil;
	}
		
	
	
	public function loadLayout(){
		global $this_system, $lang, $prvlg;
		$layout =new Layout($this);
		//$schools = Costcenters::getList();
		$layout->school_options = write_select_options(CostcentersGroup::getListOpts(), $this->school);
		
		$layout->jobs_opts = write_select_options(Jobs::getListOpts(), $this->job_code);
		$layout->join_date = unixToDate($this->join_date);
		// Sex 
		$layout->sex_1_checked = $this->sex == 1 ? 'checked="checked"' : '';
		$layout->sex_2_checked = $this->sex == 2 ? 'checked="checked"' : '';
		$layout->{'military_stat-'.$this->military_stat} = 'selected="selected"';
		$layout->{'social_stat-'.$this->social_stat} = 'selected="selected"';
		$layout->{'id_type-'.$this->id_type} = 'selected="selected"';
		$layout->{'school_type-'.$this->school_type} = 'checked="checked"';
		$layout->editable = getPrvlg('emp_edit') ? '1' : 0 ;
		// Religion 
		$layout->religion_1_checked = $this->religion == 1 ? 'checked="checked"' : '';
		$layout->religion_2_checked = $this->religion == 2 ? 'checked="checked"' : '';
		// thumb
		$layout->img_div = $this->getThumb();
		

		if($this->sex == '2') {
			$layout->military_stat_hidden = 'style="display:none"';
		}
		// Finicial
		if($prvlg->_chk('salary_read')){
			$profil = $this->getProfil();
			$salary_from = $profil->salary_from != '' ? $profil->salary_from : '0';
			$layout->{'salary_from-'.$salary_from} = 'selected="selected"';
			$layout->basic_cur_lis = Currency::getOptions(isset($profil->basic_cur) && $profil->basic_cur !='' ? $profil->basic_cur : $this_system->getSettings('def_currency'));
			$layout->var_cur_lis = Currency::getOptions(isset($profil->var_cur) && $profil->var_cur!='' ? $profil->var_cur : $this_system->getSettings('def_currency'));
			$layout->allowances_cur_lis = Currency::getOptions(isset($profil->allowances_cur) && $profil->allowances_cur!='' ? $profil->allowances_cur : $this_system->getSettings('def_currency'));
			$layout->basic = $profil->basic;
			$layout->var = $profil->var;
			$layout->allownces = $profil->allowances;
			$layout->profils_opts =  SalaryProfil::getOptions(isset($profil->id) && $profil->id!='' ? $profil->id : '');
		} else {
			$layout->salary_tab = 'hidden';
		}	
		// Evaluation
		if($prvlg->_chk('read_emp_evaluation')){
			$layout->evaluation_div = $this->getEvaluation();
		}
		$layout->template = 'modules/employers/templates/employers_layout.tpl';
		return $layout->_print();
	}

	public function getThumb(){
		global $lang;
		$img_div = '';
		if(file_exists("attachs/files/$this->id/folder.jpg")){
			$path = 'attachs/files/'.$this->id.'/folder.jpg"';
		} else {
			if($this->sex == 2){
				$path = 'assets/img/employer_f.png';
			} else {
				$path = 'assets/img/employer_m.png';
			}
		}
		if($this->status == 0 ){
			$img_div .= '<img src="assets/img/desinscripted.png" style="position:absolute"  width="48" />';
		}
	
		$img_div .= write_html('a', 'class="hand" title="'.$lang['change'].'" onclick="changEmpThumb('.$this->id.')"',
			'<img src="'.$path.'" alt="'.$this->getName().'" name="id_thumb" width="128" height="128" id="thumb-emp-'.$this->id.'" border="0" />'
		);
		return $img_div;
	}
	public function getAddress($all=false, $lang=''){
		$rows = do_query_array("SELECT * FROM addressbook WHERE con='emp' AND con_id=$this->id", $this->hrms->database, $this->hrms->ip);
		$rows = sortArrayOfObjects($rows, $this->hrms->getItemOrder('addressbook-emp-'.$this->id), 'id');
		if($lang == ''){ $lang = $_SESSION['lang'];}
		if( $all==false ){
			$first = reset($rows);
			if(isset($first->address_ar)){
				if($lang == 'ar'){
					return $first->address_ar.' - '. 
						$first->region_ar.' '. 
						$first->city_ar.' '.
						$first->country_ar.'<br/>'.
						$first->landmark.' '.
						$first->zip;
				} else {
					return $first->address.' - '. 
						$first->region.' '. 
						$first->city.' '.
						$first->country;
				}
			} else {
				return '';
			}
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $row){
					if($lang == 'ar'){
						$out[] = $row->address_ar.' - '. 
							$row->region_ar.' '. 
							$row->city_ar.' '.
							$row->country_ar.'<br/>'.
							$row->landmark.' '.
							$row->zip;
					} else {
						$out[] = $row->address.' - '. 
							$row->region.' '. 
							$row->city.' '.
							$row->country;
					}
				} 
			}
			return $out;
		}
	}
	
	public function isAbsent($date = ''){
		if($date == ''){
			$date = dateToUnix(unixToDate(time()));
		}
		$q = do_query_obj("SELECT id FROM absents WHERE emp_id=$this->id AND day=$date");
		return $q!=false && isset($q->id);
	}
	
	public function getAbsents($begin_date='', $end_date='', $type=''){
		if($begin_date == ''){
			$begin_date = mktime(0,0,0,date('m'), 1, date('Y'));
			$end_date = mktime(0,0,0, date('m')+1, -1, date('Y'));
		}
		$sql = "SELECT * FROM absents WHERE emp_id=$this->id AND day>=$begin_date AND day<=$end_date";
		if($type != ''){
			$sql .=" AND ". $type;
		}
		return do_query_array($sql);
	}
	
	public function getDiscounts($begin_date='', $end_date='', $type=''){
		if($begin_date == ''){
			$begin_date = mktime(0,0,0,date('m'), 1, date('Y'));
			$end_date = mktime(0,0,0, date('m')+1, -1, date('Y'));
		}
		$sql = "SELECT * FROM discounts WHERE emp_id=$this->id AND day>=$begin_date AND day<=$end_date";
		if($type != ''){
			$sql .=" AND ". $type;
		}
		return do_query_array($sql);
	}
	
	public function getEvaluation(){
		$report = new Layout($this);
		$report->template = 'modules/employers/templates/evaluation.tpl';
		$report->emp_name = $this->getName();
		$report->absents_div = Absents::getEmpYearAbs($this);
		
		return $report->_print();
	}
	
	static function _new(){
		global $lang, $this_system;
		$layout = new Layout();
		$layout->template = 'modules/employers/templates/employers_layout.tpl';
		$layout->editable= 1;
		$layout->absents_tab = 'hidden';
		$layout->quit_date_field = 'hidden';
		//$layout->account_date_field = 'hidden';
		$layout->join_date = unixToDate(time());
		$layout->school_options = write_select_options(CostcentersGroup::getListOpts());
		/*$schools = Costcenters::getList();
		foreach($schools as $school){
			$schs[$school->id] = $school->title;
		}
		$layout->school_options = write_select_options($schs);*/
		$currency_opts = Currency::getOptions($this_system->getSettings('def_currency'));
		$layout->profils_opts =  SalaryProfil::getOptions('');
		$layout->eveluation_tab = 'hidden';
		$layout->acc_tab = 'hidden';
		$layout->basic_cur_lis = $currency_opts;
		$layout->var_cur_lis = $currency_opts;
		$layout->allowances_cur_lis = $currency_opts;
		$layout->basic = 0;
		$layout->var = 0;
		$layout->allowances = 0;
		
		$layout->jobs_opts = write_select_options(Jobs::getListOpts());
		return $layout->_print();
	}
	
	
	static function _save($post){
		global $hrms, $prvlg, $lang;
		$result = false;
		$post['status'] = ($post['quit_date'] != '' &&  dateToUnix($post['quit_date']) != 0) ? 0 : 1;
		if($prvlg->_chk('emp_edit')){
			if(isset($post['id']) && $post['id'] != ''){
				if( do_update_obj($post, 'id='.$post['id'], 'employer_data', $hrms->database, $hrms->ip) != false){
					$result = true;
					$id = $post['id'];
				}
			} elseif(isset($post['id'])){
				$job =  new Jobs($post['job_code']);
				$profil = $job->getProfil();
				$post['basic'] =  isset($post['basic']) && $post['basic'] != '' ? $post['basic'] : $profil->basic;
				$post['var'] =  isset($post['var']) && $post['var'] != '' ? $post['var'] : $profil->var;
				$id = do_insert_obj($post, 'employer_data', $hrms->database, $hrms->ip);
				$result = $id != false ? true : false;
			}
			
			if($result!=false){
				$post['emp_id'] = $id;
					// insurance
				if(do_query_obj("SELECT * FROM insurance WHERE emp_id=$id", $hrms->database, $hrms->ip) != false){
				//	echo dateToUnix($post['insur_date']);
					do_update_obj($post, "emp_id=$id", 'insurance', $hrms->database, $hrms->ip);
				} else {
					do_insert_obj($post, 'insurance', $hrms->database, $hrms->ip);
				}
					// salary
				if(isset($post['basic']) && $prvlg->_chk('salary_edit')){
					if(do_query_obj("SELECT * FROM salary WHERE emp_id=$id", $hrms->database, $hrms->ip) != false){
						do_update_obj($post, "emp_id=$id", 'salary', $hrms->database, $hrms->ip);
					} else {
						do_insert_obj($post, 'salary', $hrms->database, $hrms->ip);
					}
				}
				
					// bank_acc
				if(do_query_obj("SELECT * FROM bank_acc WHERE emp_id=$id", $hrms->database, $hrms->ip) != false){
					do_update_obj($post, "emp_id=$id", 'bank_acc', $hrms->database, $hrms->ip);
				} else {
					do_insert_obj($post, 'bank_acc', $hrms->database, $hrms->ip);
				}
	
	
				$answer['id'] = $id;
				$answer['error'] = "";
			} else {
				global $lang;
				$answer['id'] = "";
				$answer['error'] = $lang['error_updating'];
			}
		} else {
			$answer['error'] = $lang['no_privilege'];
		}
		return $answer;
	}

	static function loadMainLayout(){
		$layout = new Layout();
		$layout->menu = fillTemplate('modules/employers/templates/employers_menu.tpl', array());
		$jobs = Jobs::getList();
		$layout->job_list = '';
		$first = true;
		$jobs_layout = new Layout();
		$first_job = reset($jobs);
		$jobs_layout->job_list = '';
		foreach($jobs as $job){
			$jobs_layout->job_list .=write_html( 'li', 'job_id="'.$job->id.'" itemid="'.$job->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openJob"', 
				write_html('text', 'class="holder-job-'.$job->id.'"',
					$job->getName()
				)
			);
			$first = false;
		}
		$jobs_layout->emp_list = $first_job->loadLayout();
		$jobs_layout->template = 'modules/employers/templates/jobs_list.tpl';
		
		$layout->jobs =$jobs_layout->_print();
		return  fillTemplate('modules/employers/templates/main_layout.tpl', $layout);
	}

	static function getAutocomplete( $value, $where){
		global $hrms, $lang;
		$Arabic = new I18N_Arabic('KeySwap');
		//echo $Arabic->swapEa($value);
		$cur_name = $_SESSION['dirc'] =='rtl' ? 'name_rtl' : 'name_ltr';
		$sql = "SELECT id, $cur_name, status FROM employer_data WHERE 
		(
			name_rtl = '$value' 
			OR name_rtl LIKE '$value%'
			OR name_ltr ='$value' 
			OR name_ltr LIKE '$value%'
			OR name_ltr LIKE '".strtolower($value)."%'
			OR name_ltr LIKE '".ucfirst($value)."%'
			OR name_ltr LIKE '".ucwords($value)."%'
			OR name_ltr LIKE '".strtoupper($value)."%'
			OR name_ltr LIKE '".$Arabic->swapEa($value)."%'
			OR name_rtl LIKE '".$Arabic->swapEa($value)."%'
		)
		$where
		LIMIT 12";
		$out = array();
		$emp_ids = do_query_array( $sql, $hrms->database, $hrms->ip);
		$t = count($emp_ids);
		if($emp_ids != false && $t>0){
			foreach($emp_ids as $emp ){
				
				$employer = new Employers($emp->id);
				$label = str_replace($value, write_html('b', 'style="color:red"', $value), $emp->$cur_name);
				
				if(!in_array($emp->status, array('1'))){
					$label.= write_html('b', 'style="color:red"', '*');
				}
				$out[] = array('id'=>$emp->id, 'name'=> $emp->$cur_name, 'label'=> $label);
			}
		} else {
			$out[] = array('error' => $lang['cant_find_emp']);	
		}
		
		return json_encode($out);
	}
	
	
}

?>