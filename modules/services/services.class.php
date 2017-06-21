<?php
/** Services
*
*/

class services{

	public function __construct($service_id){
		if($service_id != ''){		
			$service = do_query_obj("SELECT * FROM services WHERE id=$service_id", DB_year);	
			if(isset($service->mat_id)){ 
				foreach($service as $key =>$value){
					$this->$key = $value;
				}
				$this->color = $this->getMaterialColor($this->mat_id);
			} else {
				//throw new Exception('id Not Found');
			}	
		} else {
			//throw new Exception('id Not Found');
		}
			
	}
	
	public function getName(){
		global $lang;
		if(!isset($this->name)){
			$field = 'name_'.$_SESSION['dirc'];
			$service = do_query_obj("SELECT ".DB_student.".materials.$field FROM ".DB_student.".materials, ".DB_year.".services WHERE ".DB_year.".services.id=$this->id AND ".DB_year.".services.mat_id=".DB_student.".materials.id", DB_year);
			if($service->$field != ''){
				$this->name = $service->$field;
			} else {
				$this->name = $lang['undefined'];
			}
		}
		return $this->name;
	}
	
	public function getGradding(){
		if($this->gradding != ''){
			return new Gradding(false, $this->gradding);
		} else {
			return new Gradding	($this->level_id);
		}
	}

	public function loadLayout(){	
		global $lang;	
			// tab to show
		$tabs = array();
		if(in_array($_SESSION['group'], array('parent', 'student'))){
			if(MS_codeName=='sms_elearn'){
				$tabs[] = 'timeline';
				$tabs[] = 'books';
				$tabs[] = 'homeworks';				
			}
			$tabs[] = 'notes';
			$tabs[] = 'documents';
		} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
			if(MS_codeName=='sms_elearn'){
				$tabs[] = 'timeline';
				$tabs[] = 'books';
				$tabs[] = 'planedTimeline';
				$tabs[] = 'homeworks';
			}
			$tabs[] = 'notes';
			$tabs[] = 'skills';
			$tabs[] = 'documents';
			$tabs[] = 'settings';
		} else {
			if(MS_codeName=='sms_elearn'){
				$tabs[] = 'books';
				$tabs[] = 'planedTimeline';
			}
			$tabs[] = 'notes';
			$tabs[] = 'documents';
			$tabs[] = 'settings';
			$tabs[] = 'skills';
		}
		
		$i = 0;
		$titles = array();
		$details_div = array();
		foreach($tabs as $tab){
			switch ($tab) {
				case 'notes':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=lessons&notes_list&service_id='.$this->id.'"', $lang['notes'])
					);
				break;
				case 'books':					
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=lms&books&list&service_id='.$this->id.'"', $lang['books'])
					);
				break;
				case 'documents': 
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=documents&type=services&service_id='.$this->id.'"', $lang['documents'])
					);
				break;
				case 'homeworks':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=lms&homeworks_list&service_id='.$this->id.'"', $lang['homeworks'])
					);
				break;
				case 'timeline':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=lms&timeline&service_id='.$this->id.'"', $lang['timeline'])
					);
				break;
				case 'planedTimeline':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=lms&planedTimeline&service_id='.$this->id.'"', $lang['planedTimeline'])
					);

				break;
				case 'skills':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=services&skills&service_id='.$this->id.'"', $lang['skills'])
					);

				break;
				case 'settings':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="#settings_details_tab"', $lang['settings'])
					);
					$layout = $this;
					if($layout->schedule == 0 ){
						$layout->schedule_off = 'checked="checked"' ;
						$layout->schedule_on = '' ;
					} else {
						$layout->schedule_off = '' ;
						$layout->schedule_on = 'checked="checked"' ;
					}
					if($layout->mark == 0 ){
						$layout->mark_off = 'checked="checked"' ;
						$layout->mark_on = '' ;
					} else {
						$layout->mark_off = '' ;
						$layout->mark_on = 'checked="checked"' ;
					}
					if($layout->optional == 0 ){
						$layout->optional_off = 'checked="checked"' ;
						$layout->optional_on = '' ;
					} else {
						$layout->optional_off = '' ;
						$layout->optional_on = 'checked="checked"' ;
					}
					if($layout->bonus == 0 ){
						$layout->bonus_off = 'checked="checked"' ;
						$layout->bonus_on = '' ;
					} else {
						$layout->bonus_off = '' ;
						$layout->bonus_on = 'checked="checked"' ;
					}
					$gradin_opts = array("" => $lang['not_used']);
					$graddings = do_query_array( "SELECT * FROM gradding ORDER BY name ASC", DB_student);
					foreach($graddings  as $gradding){
						$gradin_opts[$gradding->id] = $gradding->name;
					}
					$layout->gradin_opts = write_select_options($gradin_opts, $this->gradding);
					$details_div[] = write_html('div', 'id="settings_details_tab"' , 
						fillTemplate('modules/services/templates/services_settings.tpl', $layout)
					);
				break;
			}
		}
		
		return write_html('h3', 'class="title hidden showforprint"', $this->getName()).
		write_html('div', 'class="tabs"', 
			write_html('ul', '', implode('', $titles)).
			implode('', $details_div)
		);
	}
	
	static public function getServiceId($level_id, $mat_id){
		$service = do_query_obj("SELECT id FROM services WHERE mat_id=$mat_id AND level_id=$level_id", DB_year);
		if($service->id !=""){
			return new services($service->id);
		} else { 
			return false;
		}
	}
	
	

		// check if Service is optional
	static public function chkServiceIsOption($service_id){
		$chk_optional = do_query_obj("SELECT optional FROM services WHERE id=$service_id", DB_year);
		if($chk_optional->optional == 1){
			return true;
		} else {
			return false;
		}
	}

	
		// function to check if user can edit lesson and lms function
	static public function check_user_service_privilege($service_id){
		if(!in_array($_SESSION['group'], array('prof', 'supervisor'))){
			return getPrvlg('lesson_edit');
		} else {
			if($_SESSION['group'] == 'prof'){
				if($service_id != false){
					$lessons= do_query_array("SELECT DISTINCT services FROM schedules_lessons WHERE prof=".$_SESSION['user_id'], DB_year);
					$prof_actual_service = array();
					foreach($lessons as $lesson){
						$prof_actual_service[] = $lesson->services;
					}
					return in_array($service_id, $prof_actual_service);
				} else {
					return false;
				}
			} elseif($_SESSION['group'] == 'supervisor'){
				return in_array($service_id, getSupervisorServices($_SESSION['user_id']));
			}
		}
	}

	public function getStudents(){
		$stds = do_query_array("SELECT std_id FROM materials_std WHERE services =".$this->id, DB_year);
		$out = array();
		foreach($stds as $std_id){
			$out[] = $std_id->std_id;
		}
		return $out;
	}
	
	public function getProfs($active=true){
		$profs = array();
		if($active == true){
			$sql = "SELECT DISTINCT schedules_lessons.prof FROM schedules_lessons 
			WHERE schedules_lessons.services=$this->id ";
			$rows = do_query_array( $sql, DB_year);
		} else {
			$sql = "SELECT DISTINCT id FROM profs WHERE services=$this->id"; 
			$rows = do_query_array( $sql, DB_student);
		}
		if($rows != false && count($rows) > 0){
			foreach($rows as $prof){
				$profs[] = new Profs($prof->prof);
			}
		}
		return $profs ;
	}

	public function getSupervisors(){
		$supers = array();
		$sql = "SELECT DISTINCT id FROM supervisors WHERE services=$this->id"; 
		$rows = do_query_array( $sql, DB_student);
		if($rows != false && count($rows) > 0){
			foreach($rows as $super){
				$supers[] = new Supervisors($super->id);
			}
		}
		return $supers ;
	}

	static function getMaterialColor($mat_id){
		if($mat_id != ''){
			$r = do_query_obj("SELECT color FROM materials WHERE id = ".$mat_id, MySql_Database);	
			return $r->color;
		} else {
			return false;
		}
	}
	
	public function getSubs(){
		global $this_system;
		$material = new Materials($this->mat_id);
		return $material->getSubs();
	}
	
	public function getSkills($sub_id, $term_id=false){
		global $sms, $lang;
		$skills = do_query_array("SELECT * FROM materials_skills WHERE sub_id=$sub_id");
		if($term_id == false){
			return $skills;
		} else {
			if($skills !=false){
				$where = array();
				$main_db = $sms->database;
				$year_db = $sms->db_year;
				$sql = "SELECT $main_db.materials_skills.* FROM $main_db.materials_skills, $year_db.services_skills_terms 
					WHERE $year_db.services_skills_terms.term_id=$term_id 
					AND $year_db.services_skills_terms.skill_id = $main_db.materials_skills.id
					AND $main_db.materials_skills.sub_id=$sub_id";
				return do_query_array($sql);
			} else {
				return array();
			}
		}
	}
	
	public function skillsForm(){
		global $this_system, $lang, $prvlg;
		$subs = $this->getSubs();
		$items = array();
		$terms = Terms::getTermsByCon('level', $this->level_id);
		if($subs != false){
			foreach($subs as $sub){
				//$service = new Services($sub->service_id);
				$trs = array();
				$skills = $this->getSkills($sub->id);
				if($skills != false && count($skills) > 0 && $terms  != false){
					foreach($skills as $skill){
						$selected_terms = array();
						$selected = do_query_array("SELECT term_id FROM services_skills_terms WHERE skill_id=$skill->id", $this_system->db_year);
						foreach($selected as $s){
							$selected_terms[] = $s->term_id;
						}
						$radios = array();
						$row = new Layout($skill);
						foreach($terms as $term){
							$radios[] = '<input type="checkbox" name="term_id" value="'.$term->id.'" id="skill_term-'.$term->id.'-'.$skill->id.'" '.(in_array($term->id, $selected_terms) ? 'checked="checked"' : '').' update="updateSkillTerm" skill_id="'.$skill->id.'" />'.
							write_html('label', 'for="skill_term-'.$term->id.'-'.$skill->id.'"', $term->term_no);
						}
						$trs[] = write_html('tr', '',
							write_html('td', '', $skill->title).
							write_html('td', 'align="center"', implode('', $radios))
						);
						/*$row->terms_radio = write_html('span', 'class="buttonSet"', implode('', $radios));
						$row->template = "modules/services/templates/skills_settings_rows.tpl";
						$trs[] = $row->_print();*/	
					}
				}
				$items[] = write_html('h3', '', 
					write_html('a', '',
						$sub->title.
						($prvlg->_chk('resource_edit_materials') ? 
							write_html('button', 'type="button" module="resources"  action="addSkill" sub_id="'.$sub->id.'" class="ui-state-default hoverable circle_button rev_float" ', write_html('b', '', '+'))
						: '')
					)
					
				).
				write_html('div', '',
					write_html('table', 'class="tablesorter"',
						write_html('thead', '',
							write_html('tr', '',
								write_html('th', '', $lang['title']).
								write_html('th', 'width="120"', $lang['terms'])
							)
						).
						write_html('tbody', '',	
							implode('', $trs)
						)
					)
				);
			}
		}
		
		$form = new Layout($this);
		$form->template = "modules/services/templates/skills_form.tpl";
		$form->items = implode('', $items);
		return $form->_print();
	}
	
	static function saveSub($post){
		global $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'services_subs', $sms->db_year, $sms->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id']) && $post['id'] == ''){
			$result = do_insert_obj($post, 'services_subs', $sms->db_year, $sms->ip);
			$id = $result;
		}

		if($result!=false){
			$answer['id'] = $id;
			$answer['item'] = write_html('h3', '', 
				write_html('a', 'href="#"',
					$_POST['title'].
					write_html('button', 'type="button" action="addSkill" sub_id="'.$id.'" class="ui-state-default hoverable circle_button"', write_html('b', '', '+'))
				)
			).
			write_html('div', '','');
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function newSkill($sub_id){
		$layout = new Layout();
		$layout->template = "modules/services/templates/skills.tpl";
		$layout->sub_id = $sub_id;
		return $layout->_print();
	}

	static function saveSkill($post){
		global $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'services_skills', $sms->db_year, $sms->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id']) && $post['id'] == ''){
			$result = do_insert_obj($post, 'services_skills', $sms->db_year, $sms->ip);
			$id = $result;
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
	
	static public function orderService($services){
		$out = array();
		$materials =  getItemOrder('materials');
		foreach($materials as $mat_id){ 
			for($x=0 ; $x<count($services); $x++){
				if(isset($services[$x])){
					
					$service = $services[$x];					
					if($service->mat_id == $mat_id){
						$out[] = $service;
						unset($services[$x]);
					} 
				}
			}
		}
		
		if(count($services) > 0){
			$out = array_merge($out, $services);
		}
		
		return $out;
				
	}
	
	static function searchService($mat_id, $level_id){
		global $sms;
		if($service = do_query_obj("SELECT id FROM services WHERE mat_id=$mat_id AND level_id=$level_id", $sms->db_year)){
			return new services($service->id);	
		} else {
			return false;
		}
	}
	
/*

	static public function getServiceNameById($service_id){
		if($service_id != ''){
			$field = 'name_'.$_SESSION['dirc'];
			$service = do_query_obj("SELECT ".DB_student.".materials.$field FROM ".DB_student.".materials, ".DB_year.".services WHERE ".DB_year.".services.id=$service_id AND ".DB_year.".services.mat_id=".DB_student.".materials.id", DB_year);
			if($service->$field != ''){
				return $service->$field;
			} else {
				return false;
			}
		}
	}
		// level services
	static public function getLevelService($level_id){
		if($level_id != false && $level_id != ''){
			$out= array();
			$level_mat= do_query_array("SELECT id FROM services WHERE level_id =".$level_id, DB_year);
			foreach($level_mat as $service){
				$out[]= new services($service->id);
			}
			return services::orderService($out);
		} else {
			return false;
		}
	}

		// class services
	static public function getClassService($class_id){
		if($class_id != false && $class_id != ''){
			$out= array();
			$mats= do_query_array("SELECT services FROM materials_classes WHERE class_id =".$class_id, DB_year);
			if(count($mats) > 0){
				foreach($mats as $service_id ){
					$out[] = new services($service_id->services);
				}
				return services::orderService($out);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

		// group services
	static public function getGroupServices($group_obj, $inherited=false){
		if($group_obj->id != false && $group_obj->id != ''){
			$out= array();
			$mats= do_query_obj("SELECT service_id FROM groups WHERE id =".$group_obj->id, DB_year);
			if($mats->service_id != ''){
				$out[]= new services($mats->service_id);
			}
			if($inherited){
				if($group_obj->parent == 'class'){
					$parent_services = services::getClassService($group_obj->parent_id);
				} elseif($group_obj->parent == 'level'){
					$parent_services = services::getLevelService($group_obj->parent_id);
				}
				foreach($parent_services as $service){
					if($service->optional == 0){
						$out[]  = $service;
					}
				}
			}
			return services::orderService($out);
		} else {
			return false;
		}
	}

	// student services
	static public function getStudentService($std_id, $write=false){
		if($std_id != false && $std_id != ''){
			$out= array();
			$mats= do_query_array("SELECT services FROM materials_std WHERE std_id =".$std_id, DB_year);
			if(count($mats) > 0){
				foreach($mats as $row){
					$out[] = new services($row->services);
				}
			} else {
				if($write){// insert
					$new_services = array();
						//group services
					$group_services = do_query_array("SELECT groups.services FROM `groups`, groups_std WHERE `groups`.id =groups_std.group_id AND `groups`.id=$group_id AND std_id=$std_id", $DB_year);
					if(count($chk_std_gr) > 0){
						$new_services = $group_services->service;
						$out[] = new services($group_services->service);
					}
						// class service
					$class_id = getClassIdFromStdId($std_id);
					$class_services = services::getClassService($class_id);
					foreach($class_services as $service){
						if($service->optional == 0){
							$new_services[]  = $service->id;
							$out[] = new services($service->id);
						}
					}
					if(count($new_services) > 0){
						do_query_edit("INSERT INTO materials_std (std_id, services) VALUES ($std_id, ".implode("), ($std_id,", $new_services).")",DB_year);
					}
				}
			}
		//	print_r(services::orderService($out));exit;
			return services::orderService($out);	
		}else {
			return false;
		}
	}

	static public function getSupervisorServices($super_id){
		$out= array();
		$mats= do_query_array("SELECT services FROM supervisors WHERE services!=0 AND id =".$super_id, DB_student);
		if(count($mats) > 0){
			foreach($mats as $row){
				$out[] = new services( $row->services);
			}
			return services::orderService($out);
		} else {
			return false;
		}
	}
	
	static public function getProfService($prof_id){ // service that prof can teach
		$out= array();
		$mats= do_query_array("SELECT services FROM profs_materials WHERE id =".$prof_id, DB_student);
		if(count($mats) > 0){
			foreach($mats as $row ){
				$out[] = new services($row->services);
			}
			return services::orderService($out);
		} else {
			return false;
		}
	}*/
	
}
?>