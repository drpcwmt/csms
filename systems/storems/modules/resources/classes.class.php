<?php
/** Classes
*
*/
class Classes{

	public function __construct($id, $year='', $sms=''){
		global $lang;
		if($sms == '' ){
			global $sms;
		}
		$this->sms = $sms;
		if($id != ''){	
			$this->year = $year != '' ? $year : $_SESSION['year'];
			$class = do_query_obj("SELECT * FROM classes WHERE id=$id", Db_prefix.$this->year, $sms->ip);	
			if(isset( $class->id )){
				foreach($class as $key =>$value){
					$this->$key = $value;
				}
			}	
		}	
	}
	
	public function getName($other_lang = false){
		if(isset($this->name_ltr)){
			if($other_lang == false){
				return $_SESSION['lang'] == 'ar' ? $this->name_rtl : $this->name_ltr ;
			} else {
				return $_SESSION['lang'] == 'ar' ? $this->name_ltr : $this->name_rtl ;
			}
		} else {
			return false;
		}
	}
	
	public function getDefProf(){
		if(!isset($this->defProf)){
			$this->defProf = new Profs($this->resp, $this->sms);
		}
		return $this->defProf;
	}
	
	public function getLevel(){
		if(!isset($this->level)){
			require_once('levels.class.php');
			$this->level = new Levels($this->level_id, $this->sms);
		}
		return $this->level;
	}
	
	public function getEtab(){
		$level = $this->getLevel();
		return $level->getEtab();
	}
	
	public function getServices(){
		$sms =$this->sms;
		if(!isset($this->services)){
			$out= array();
			if($sms->getSettings('ig_mode') == '1'){
				$services= do_query_array("SELECT id FROM services_ig", $this->sms->db_year, $this->sms->ip);
				if(count($services) > 0 ){
					foreach($services as $service){
						$out[]= new servicesIG($service->id, $this->sms);
					}
				}
			} else {
				$services= do_query_array("SELECT services FROM materials_classes WHERE class_id =".$this->id, $this->sms->db_year, $this->sms->ip);
				if(count($services) > 0 ){
					foreach($services as $service){
						$out[]= new services($service->id, $this->sms);
					}
				}
			}
			$this->services = Services::orderService($out);
		} 
		return $this->services;

	}
	
	public function getGroups(){
		if(!isset($this->groups)){
			$out = array();
			$grs = do_query_array("SELECT id FROM groups WHERE parent='class' AND parent_id=$this->id", $this->sms->db_year, $this->sms->ip);
			foreach($grs as $gr){
				$out[] = new Groups($gr->id, $this->sms);
			}
			$this->groups = $out;
		}
		return $this->groups;
	}
	
	public function getStudentsIds(){ // DEpricated
		if(!isset($this->studentsIds)){
			$db_year =$this->sms->db_year;
			$db_student = $this->sms->database;
			$field_order_name = $_SESSION['lang'] == 'ar' ? "name_ar" : "name";
			$sql = "SELECT $db_student.student_data.id FROM $db_year.classes_std, $db_student.student_data WHERE $db_year.classes_std.class_id=$this->id AND $db_year.classes_std.std_id=$db_student.student_data.id";
			$year = getYear();
			$where_stat = array();
			$now = time();
			
			$sql .= " ORDER BY $db_student.student_data.sex, $db_student.student_data.$field_order_name";
		//	$sql .= " ORDER BY $db_student.student_data.cand_no, $db_student.student_data.$field_order_name";
			$students = do_query_array($sql , $this->sms->database, $this->sms->ip);	
			
			$this->studentsIds = $students;
		}
		return $this->studentsIds;
	}

	public function getStudents($all_stat=false){ 
		if(!isset($this->students)){
			//$students = $this->getStudentsIds(true);
			$students = $this->getStudentsIds();
			$year_begin =$this->sms->getYearSetting('begin_date');	
			$std_arr =array();
			if(count($students) > 0){
				foreach($students as $std){
					$student = new Students($std->id, $this->sms);
					$std_stat = $student->getStatus();
				//	if($student->join_date < $this->sms->getYearSetting('end_date') || $student->join_date == ''){;
					if($all_stat===true){
						$std_arr[] =  $student;
					} elseif(is_array($all_stat)&& in_array($std_stat, $all_stat)){
						$std_arr[] =  $student;
					} 
				}
			}
			$this->students = $std_arr;
		}
		return $this->students;
	}
	
	public function getProfs(){

		return array($this->getDefProf()); // Add other profs !!!!!
	}
	
	public function getPrincipals(){
		$level = $this->getLevel();
		return $level->getPrincipals();
	}
	
	public function loadLayout(){
		global $lang, $prvlg;
		$layout = new Layout($this);
		$layout->class_name = $this->getName();
		$employer = $this->resp != '' ? new Profs($this->resp) : false;
		$layout->resp_name = $employer != false ? $employer->getName() : '';
		$levels = Levels::getList(true);
		foreach($levels as $level){
			$level_arr[$level->id] = $level->getName();
		}
		$layout->levels_select = write_select_options( $level_arr, $this->level_id, false);
		$halls = Halls::getList();
		$halls_arr = array();
		foreach($halls as $hall){
			$halls_arr[$hall->id] = $hall->getName();
		}
		$layout->halls_select = write_select_options( $halls_arr, $this->room_no, false);
		$student_list = new StudentsList('class', $this->id, $this->sms);
		if($this->sms->getSettings('class_list_template') != false){
			$student_list->list_template = $this->sms->getSettings('class_list_template');
		}
		//$student_list->stats = array('1','3');
		$layout->year = $_SESSION['year'].' / '.($_SESSION['year'] +1);
		$layout->student_list_table = $student_list->createTable();
		$layout->total_students = $student_list->getCount();
		// fees
		if($this->sms->getSettings('safems_server')== 1 && ($prvlg->_chk('read_std_fees') || $prvlg->_chk('read_std_fees_stat'))){
			$schoolFees = new SchoolFees($this->sms);
			$layout->extra_tabs_lis = write_html('li', '', write_html('a', 'href="#class_school_fees"',$lang['accounting']));
			$layout->extra_tabs_divs = write_html('div', 'id="class_school_fees"', 
				$schoolFees->browseClass($this->id)
			);
		}
		$layout->resources_toolbox =Resources::getItemsToolbox('classes', $this->id);
		$layout->editable = $prvlg->_chk('resource_edit_classes') ? '1' : '0';
		$layout->template = 'modules/resources/templates/classes.tpl';
		return $layout->_print();
	}
	
	static function getList($privilege = true){
		global $sms;
		if($privilege){
			if($_SESSION['group'] ==  'prof'){
				$prof = new Profs($_SESSION['user_id'], $sms);
				return  $prof->getClassList();
			} elseif( $_SESSION['group'] == 'principal'){
				$principal = new Principals($_SESSION['user_id'], $sms);
				return  $principal->getClassList();
			} elseif( $_SESSION['group'] == 'coordinator'){
				$coordinator = new Coordinators($_SESSION['user_id'], $sms);
				return  $coordinator->getClassList();
			} elseif( $_SESSION['group'] == 'supervisor'){
				$supervisor = new Supervisors($_SESSION['user_id'], $sms);
				return  $supervisor->getClassList();
			} elseif( $_SESSION['group'] == 'student'){
				$student = new Students($_SESSION['user_id'], $sms);
				return  array($student->getClass());
			}
		}
		$classes = array();
		$query = do_query_array("SELECT id FROM classes", $sms->db_year, $sms->ip);	
		foreach($query as $class ){
			$classes[] = new Classes($class->id, '',  $sms);
		}

		return sortArrayOfObjects($classes, $sms->getItemOrder('classes-'.$_SESSION['year']), 'id');
	}
	
	static function _save($post){
		global $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'classes', $sms->db_year, $sms->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id']) && $post['id'] == ''){
			$result = do_insert_obj($post, 'classes', $sms->db_year, $sms->ip);
			$id = $result;
		}

		if($result!=false){
			$answer['id'] = $id;
			$answer['title'] = $post['name_'.$_SESSION['dirc']];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}

	static function _delete($id){
		global $sms;
		if(do_query_edit("DELETE FROM classes WHERE id=$id", $sms->db_year, $sms->ip)){
			do_query_edit("DELETE FROM classes_std WHERE class_id=$id", $sms->db_year, $sms->ip);
			do_query_edit("DELETE FROM materials_classes WHERE class_id=$id", $sms->db_year, $sms->ip); 
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
		
	static function _new(){
		global $lang;
		$new_class = new Layout();;
		$new_class->levels_options = write_select_options( objectsToArray(Levels::getList(true)), '', false);
		$new_class->halls_options = write_select_options( objectsToArray(Halls::getList(true)), '', false);
		return fillTemplate('modules/resources/templates/classes_new.tpl', $new_class);
	}
	
	/************************** School Fees ************************/
	/*public function loadFeesLayout(){
		global $lang, $this_system, $prvlg;
		$layout = new Layout($this);
		$year = $_SESSION['year'];
		$safems = $this_system->getSafems();
		$layout->class_name = $this->getName();
		$students = $this->getStudents();
		$dates = $this->getLevel()->getDates($year);
		$row = array();
		$profils = array('0'=> $lang['normal']);
		$profils_arr = Profils::getList();
		if(count($profils_arr) > 0){
			foreach($profils_arr as $prof){
				$profils[$prof->id] = $prof->title;
			}
		}
		foreach($students as $std){
			$total = 0;
			$profil = $std->getProfil();
		//	print_r($profil);
			$profil_id = $profil != false && isset($profil->id) ? $profil->id :'';
			$parent = $std->getParent();
			$son_of_employe = ($parent->father_emp == 1 || $parent->mother_emp ==1) ? true : false;
			$year = $_SESSION['year'];
			//$account = new Accounts($std->getAccCode());
			$sms = $std->sms;
			$ccid = $sms->getCC();
			$sql = "SELECT SUM(value) AS total, SUM(paid) as paid FROM school_fees WHERE std_id='$std->id' AND cc=$ccid AND year = $year AND currency='".$this_system->getSettings('def_currency')."'";
			$paid = do_query_obj($sql, $safems->database, $safems->ip);
			$bros = do_query_array("SELECT id FROM student_data WHERE parent_id='$std->parent_id' AND id!=$std->id AND status=1", $this->sms->database, $this->sms->ip);
			$bus_code = $std->getBus();
			$tr = write_html('td', 'class="unprintable"', 
				write_html('button', 'class="circle_button ui-state-default hoverable" action="openStudent" std_id="'.$std->id.'" sms_id="'.$this->sms->id.'"',
					write_icon('person')
				)
			).
			write_html('td', '', $std->getName()).
			write_html('td', '', unixToDate($std->join_date)).
			write_html('td', 'align="center"', 
				$son_of_employe ? write_icon('check') : ''
			).
			write_html('td', 'align="center"', 
				count($bros)>0 ? count($bros) : ''
			).
			write_html('td', 'align="center"', 
				$std->locker!='' ? write_icon('check') : ''
			).
			write_html('td', 'align="center"', 
				$bus_code!='' ? $bus_code : ''
			).
			write_html('td', '',
				write_html_select('name="profil[]" rel="'.$std->id.'" sms_id="'.$this->sms->id.'" update="updateStdProdil" class="combobox" '.($prvlg->_chk('edit_std_fees')?'':'disabled="disabled"'), $profils, $profil_id)
			);			
			
			$tr .= write_html('td', 'align="center" class="total"', write_html('b', '',  $paid->total)).
				write_html('td', 'align="center" class="paid"', write_html('b', '',  $paid->paid )).
				write_html('td', 'align="center" class="diff"', write_html('b', '', $paid->total- $paid->paid )).
				write_html('td', 'class="unprintable"', 
				($prvlg->_chk('edit_std_fees') ? 
					write_html('button', 'class="circle_button ui-state-default hoverable" action="calcFees" std_id="'.$std->id.'" sms_id="'.$this->sms->id.'"',
						write_icon('refresh')
					)
				: '')
				);
			$row[] = write_html('tr', '', $tr);
		}
		$layout->student_rows = implode('', $row);
		
		$layout->payments_ths ='';
		foreach($dates as $d){
			$layout->payments_ths .= write_html('th', '', $d->title);
		}
		
		$layout->total_students = count($students);
		$layout->class_name = $this->getName();
				
		
		unset($layout->sms);
		unset($layout->level);
		unset($layout->studentsIds);
		unset($layout->students);
		return fillTemplate('modules/fees/templates/class_fees.tpl', $layout);
	}*/
	
}

?>