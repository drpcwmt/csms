<?php
/** Student Repprts
*
*/

class CertReport{
	private $thisTemplatePath = 'modules/reports/templates';

	public function __construct($std_id, $cur_lang=''){
		global $MS_settings;
		if($cur_lang == ''){
			$this->lang = $_SESSION['lang'];
		} else {
			$this->lang = $cur_lang;
		}
		$student = new Students($std_id);
		$this->student = $student;
		if($cur_lang == $_SESSION['lang'] && $cur_lang!='ar'){
			$this->student_name = $student->getName();
			$this->student_name_ar = $student->getName(true);
		} else {
			$this->student_name = $student->getName(true);
			$this->student_name_ar = $student->getName();
		}
		$this->birth_date = $student->birth_date;
		$this->today = unixToDate(time());
		$this->nationality = $student->nationality;
		$this->nationality_ar =$student->nationality_ar;
		$this->school_name = $MS_settings['school_name'];
	}
	
	public function loadRadiationCert(){
		$student = $this->student;
		if( !isset($student->id)){
			return write_error($lang['cant_find_std_code']);
		} elseif($student->status == 1){
			return write_error($lang['error-std_registred']);
		} elseif($student->status == 2){
			return write_error($lang['error-std_waiting']);
		} elseif($student->status == 3){
			return write_error($lang['error-std_suspended']);
		} elseif($student->status == 5){
			return write_error($lang['error-std_graduated']);
		} else {
			$layout = new Layout($this);
			
			$years = do_query_array("SELECT * FROM years ORDER BY year DESC", MySql_Database);
			$out = array();
			foreach($years as $year){
				$database = Db_prefix.$year->year;
				if(do_query_array("SHOW DATABASES LIKE '$database'",  MySql_Database)!= false){
					$chk_class = do_query_obj("SELECT class_id FROM classes_std WHERE std_id=$student->id", $database);
					if($chk_class!=false){
						$class = do_query_obj("SELECT level_id FROM classes WHERE id=$chk_class->class_id", $database);
						$out[$year->year] = $class->level_id; 
					}
				}
			}
			ksort($out);
			$first_level = reset($out);
			$level = new Levels($first_level);
			$layout->first_level = $level->name_ltr;
			$layout->first_level_ar = $level->name_rtl;
			$first_key = key($out);
			$layout->first_year = $first_key.'/'.$first_key+1;
			$layout->first_y = $first_key;
			
			krsort($out);
			$last_level = reset($out);
			$level = new Levels($last_level);
			$layout->last_level = $level->name_ltr;
			$layout->last_level_ar = $level->name_rtl;
			$first_key = key($out);
			$layout->last_year = $first_key.'/'.$first_key+1;
			$layout->last_y = $first_key;
			$layout->template = $this->thisTemplatePath."/rad_cert-$this->lang".".tpl";
			return $layout->_print();
		}
	}
	
	public function loadScolarityCert(){
		$student = $this->student;
		if( !isset($student->id)){
			return write_error($lang['cant_find_std_code']);
		} elseif($student->status == 0){
			return write_error($lang['error-std_unregistred']);
		} elseif($student->status == 2){
			return write_error($lang['error-std_waiting']);
		} elseif($student->status == 3){
			return write_error($lang['error-std_suspended']);
		} elseif($student->status == 5){
			return write_error($lang['error-std_graduated']);
		} else {
			$layout = new Layout($this);
			if($this->lang == $_SESSION['lang']){
				$layout->student_name = $student->getName();
			} else {
				$layout->student_name = $student->getName(true);
			}
			$layout->year = $_SESSION['year'].'/'.($_SESSION['year'] +1);
			$level = $student->getLevel();
			$layout->level_ltr = $level->name_ltr;
			$layout->level_rtl = $level->name_rtl;
			$layout->template = $this->thisTemplatePath."/sch_cert-$this->lang".".tpl";
			return $layout->_print();
		}
	}

	
}