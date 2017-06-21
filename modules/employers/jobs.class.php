<?php
/** Jobs 
*
*
*/

class Jobs{
	
	public function __construct($id, $hrms=''){
		if($hrms==''){
			global $hrms;
		}
		$this->hrms = $hrms;
		if($id != '' && $id != false){	
			$job = do_query_obj("SELECT * FROM jobs WHERE id=$id", $hrms->database, $hrms->ip);	
			if(isset( $job->id )){
				foreach($job as $key =>$value){
					$this->$key = $value;
				}
			}	
		}	
	}
	
	public function getName($other_lang = false){
		if(isset($this->name_en)){
			if($other_lang == false){
				return $_SESSION['lang'] == 'ar' ? $this->name_ar : $this->name_en ;
			} else {
				return $_SESSION['lang'] == 'ar' ? $this->name_en : $this->name_ar ;
			}
		} else {
			return false;
		}
	}
	
	public function getEmps($cc=''){
		$out = array();
		$emps = do_query_array("SELECT * FROM employer_data WHERE job_code=$this->id AND status=1".($cc!='' ? " AND school=$cc" : ''), $this->hrms->database, $this->hrms->ip);	
		foreach($emps as $emp){
			$out[] = new Employers($emp->id);
		}
		return $out;
	}
	
	public function getProfil(){
		if(!isset($this->profil)){
			$profil = do_query_obj("SELECT id FROM jobs_profil WHERE con='job' AND con_id=$this->id", $this->hrms->database, $this->hrms->ip);
			$this->profil = new SalaryProfil(isset($profil->id) ? $profil->id : '');
		}
		return $this->profil;
	}
	
	public function loadLayout(){
		global $lang;
		$layout = new Layout($this);
		$layout->template ='modules/employers/templates/jobs_layout.tpl';
		$layout->job_name = $this->getName();
		$ccs = do_query_array("SELECT DISTINCT (school) FROM employer_data WHERE job_code=$this->id ORDER BY CAST(school AS SIGNED) ASC", $this->hrms->database, $this->hrms->ip);
		$firsst = true;
		$tabs = array();
		$tabs_divs= array();
		foreach($ccs as $cc){
			$group = new CostcentersGroup($cc->school);
			$tabs[] = write_html('li', '', write_html('a', 'href="#job-'.$cc->school.'-tab"', $group->getName()));
			$tabs_divs[] = write_html('div', 'id="job-'.$cc->school.'-tab"',
   				write_html('div', 'class="toolbox"',
        			write_html('a', 'action="print_tab"', $lang['print'].write_icon('print'))
				).
				write_html('h2', 'class="title hidden showforprint"', $this->getName().' - '. $group->getName()).
				write_html('h3', 'class="default_align"', $lang['count'].': '.count($this->getEmps($cc->school))).
				$this->getEmpTable($cc->school)
			);
		}
		$layout->tabs = implode('', $tabs);
		$layout->tabs_divs = implode('', $tabs_divs);
		return $layout->_print();
	}
		
	
	public function getEmpTable($cc='', $action='open'){
		$emps = $this->getEmps($cc);
		$table = new layout();
		$table->trs = '';
		$i=1;
		foreach($emps as $emp){
			$emp_layout = new Layout($emp);
			$emp_layout->template = 'modules/employers/templates/jobs_list_rows.tpl';
			if($action=='open'){
				$emp_layout->action = write_html('button', ' module="employers" empid="'.$emp->id.'" action="openEmployer" class="ui-state-default hoverable circle_button"', write_icon('person'));
			} else {
				$emp_layout->action = '<input type="checkbox" name="emp_id[]" value="'.$emp->id.'" />';
			}
			$emp_layout->ser = $i;
			$emp_layout->emp_name = $emp->getName();
			$emp_layout->full_code = $emp->getAccCode();
			$table->trs .=$emp_layout->_print();
			$i++;
		}
		$table->template = 'modules/employers/templates/jobs_list_table.tpl';
		return $table->_print();

	}
			
	static function getList($prvlg=true){
		global $hrms, $this_system;
		$out = array();
		if($prvlg && $this_system->type=='hrms'){
			$jobs = do_query_array("SELECT job AS id FROM privilege_cc WHERE user_id=".$_SESSION['user_id'], $hrms->database, $hrms->ip);
		} else {
			$jobs = do_query_array("SELECT id FROM jobs", $hrms->database, $hrms->ip);			
		}
		foreach($jobs as $job){
			$out[] = new Jobs($job->id);	
		}
		
		return sortArrayOfObjects($out, $hrms->getItemOrder('jobs'), 'id');
	}
	
	static function getListOpts(){
		global $hrms;
		$out = array();
		$field = $_SESSION['lang'] == 'ar' ? 'name_ar' : 'name_en';
		$jobs = do_query_array("SELECT id, $field FROM jobs", $hrms->database, $hrms->ip);
		foreach($jobs as $job){
			$out[$job->id] = $job->$field;	
		}
		
		return $out;
	}
}