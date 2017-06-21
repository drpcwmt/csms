<?php
## New student script
if(!getPrvlg('std_add')){write_html_error($lang['restrict_accses']);};


if(isset($_POST['step'])){
	$step = new Layout();
	$step->template = 'modules/students/templates/new_std-step-'.$_POST['step'].'.tpl';
	
	if($_POST['step'] == 1 ){ // initial

		/*$levels = do_query_resource("SELECT name_".$_SESSION['dirc'].", id FROM levels", $this_system->database, $this_system->ip) ;
		$levels_arr = array();
		while($level = mysql_fetch_assoc($levels)){
			$levels_arr[$level['id']] = $level['name_'.$_SESSION['dirc']];
		}*/
		$levels_arr = objectsToArray(Levels::getList());	
		$step->levels_opts = write_select_options($levels_arr, '', false);
		
		$all_year = getYearsArray();
		$year_select = array();
		foreach($all_year as $year){
			if($year>=$_SESSION['year']){
				$year_select[$year] = $year.'/'.($year+1);
			}
		}
		$step->years_opts = write_select_options($year_select, $_SESSION['year']+1, false);
		
		echo $step->_print();
	
		/*echo '<input type="hidden" id="wizardSteps" value="1" />'.
		write_html('div', 'id="newStudentForm"',
			write_html('div', 'id="newStudentDiv"', 
				write_html('div', 'id="step-1" class="ui-widget-content items"',
					write_html('form', 'id="first_step_form"',
						write_html( 'div', 'class="ui-state-highlight" style="padding:5px; margin:20px 40px 0px 40px;"',
							write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
								write_html('tr', '',
									write_html('td', 'width="100" valign="middel" style="text-align:right"', 
										write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['level'])
									).
									write_html('td', 'valign="middel"',
										write_html_select('name="level" class="combobox" id="level_select" ', $levels_arr, '')
									)
								).
								write_html('tr', '',
									write_html('td', 'width="100" valign="middel" style="text-align:right"', 
										write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['year'])
									).
									write_html('td', 'valign="middel"',
										write_html_select('name="year" class="combobox"', $year_select, $_SESSION['year'])
									)
								)
							).
							write_html('ul', 'style="list-style:none"',
								write_html('li', '', 
									write_html('h3', '',
										'<input type="radio" name="insertType" value="1" checked onclick=" $(\'#sugNameDiv\').fadeOut(500); $(\'#level_select option:first\').attr(\'selected\', \'selected\')" />'.
										$lang['new_file']
									)
								).
								write_html('li', '', 
									write_html('h3', '',
										'<input type="radio" name="insertType" value="3" onclick="$(\'#level_select\').removeAttr(\'disabled\'); $(\'#sugNameDiv\').fadeIn(500)" />'.
										$lang['new_std_reinscription']
									).
									write_html('table', ' id="sugNameDiv" class="hidden" width="100%" border="0" cellspacing="0" cellpadding="0"',
										write_html('tr', '',
											write_html('td', 'width="100" valign="middel" style="text-align:right"', 
												write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['student_name'])
											).
											write_html('td', 'valign="middel"',
												'<input type="text" id="newSugName"  class="input_double"/><input type="hidden" id="id" name="id" class="autocomplete_value" />'
											)
										)
									)		
								)
							)
						).
						write_html('fieldset', 'style="margin:20px 40px" class="ui-state-highlight"',
							write_html('legend', '', $lang['inscrip_ime']).
							write_html('ul', 'style="list-style:none"',
								write_html('li', '', 
									write_html('h3', '',
										'<input type="radio" value="1" name="status" checked="checked"/>'.
										$lang['inscrip_ime']
									)
								).
								write_html('li', '', 
									write_html('h3', '',
										'<input type="radio" value="2" name="status" />'.
										$lang['waiting_list']
									)
								).
								write_html('table', 'id="join_table" style="margin:0px 30px" cellspacing="0"',
									write_html('tr', '',
										write_html('td', 'width="120" valign="middel" ', 
											write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['join_date'])
										).
										write_html('td', 'valign="middel"',
											'<input type="text" class="datepicker mask-date ui-corner-right ui-state-default" name="join_date" id="join_date" value="'.unixToDate(time()).'" />'
										)
									)
								)
							)
						)
					)
				).
				write_html('div', 'id="step-2" class="ui-widget-content items"', '').
				write_html('div', 'id="step-3" class="ui-widget-content items"', '').
				write_html('div', 'id="step-4" class="ui-widget-content items"', '').
				write_html('div', 'id="step-5" class="ui-widget-content items"', '')
			)
		);*/		
	}
	
	// STEP 2
	if($_POST['step'] ==2){ // student infos
		if(isset($_POST['id']) && $_POST['id'] !='' && $_POST['insertType'] == '0'){
			$answer = array();
			$answer['error']='';
			$student = new Students($_POST['id']);
			$answer['id'] = $student->id;
			//$student_content = $student->loadDataLayout();
			if(isset($student->parent_id)){
				$parents = new Parents($student->parent_id, $sms);
				if($parents != false){
					$answer['html'] = $parents->loadDataLayout();
				} else {
					$answer['html'] = Parents::newParents();
				}
			} else {
				$answer['html'] = Parents::newParents();
			}
		} else {
			$answer = json_decode(Students::saveStudent($_POST));
			if($answer->error ==''){
				//$student_content = students::newStudent();
				$answer->html = Parents::newParents();
			} else {
				$answer->html = 'ERROR';
			}
		}
		echo json_encode($answer);
		/*$out = write_html('div', 'class="tabs"',
			write_html('ul', '',
				write_html('li', '', write_html('a', 'href="#student-infos"',$lang['personel_infos'])).
				write_html('li', '', write_html('a', 'href="#parents-infos" ',$lang['parents']))
			).
			write_html('div', 'id="student-infos"', $student_content).
			write_html('div', 'id="parents-infos"', $parents_content)
		);
		echo $out;*/
		exit;
	}
		
	// step 3 add student infos
	if($_POST['step'] == 3){
		$student = new Students($_POST['id']);
		$student->parent_id=$_POST['parent_id'];
		echo $student->loadDataLayout(true);
		exit;
	}
		
	// step 3 spcify class
	if($_POST['step'] == 4){
		$DB_year = Db_prefix.$_POST['year'];
		$level_id = $_POST['level'];
		$query = do_query_array("SELECT id FROM classes WHERE level_id=$level_id", $DB_year);		
		$class_ids = array();
		foreach($query as $row){
			$class_ids[] = $row->id;
		}
		$classes = sortArrayByArray($class_ids, getItemOrder('classes'));
		$class_tbody = '';
		$DB_student = $this_system->database;
		foreach($classes as $class_id){
			$statment = "SELECT $DB_student.student_data.id FROM $DB_student.student_data, $DB_year.classes_std WHERE $DB_year.classes_std.class_id=$class_id AND $DB_year.classes_std.std_id = $DB_student.student_data.id AND $DB_student.student_data.%s=%d";
			$count_boys = count(do_query_array(sprintf($statment, 'sex', 1), $this_system->database, $this_system->ip));
			$count_girls = count(do_query_array(sprintf($statment, 'sex', 2), $this_system->database, $this_system->ip));
			$count_muslim = count(do_query_array(sprintf($statment, 'religion', 1), $this_system->database, $this_system->ip));
			$count_chris = count(do_query_array(sprintf($statment, 'religion', 2), $this_system->database, $this_system->ip));
			
			$field = "name_".$_SESSION['dirc'];
			$r = do_query_obj("SELECT $field FROM classes WHERE id=$class_id", $DB_year);	
			$class_name = $r->$field;
			
			$class_tbody .= write_html('tr', '',
				write_html('td', '', '<input type="radio" name="class_id" value="'.$class_id.'" onclick="loadNewStdGroup('.$class_id.')"/>').
				write_html('td', '', $class_name).
				write_html('td', '', $count_boys + $count_girls).
				write_html('td', '', $count_boys).
				write_html('td', '', $count_girls).
				write_html('td', '', $count_muslim).
				write_html('td', '', $count_chris)
			);
		}
		echo write_html('form', 'id="new_std_class_form" style="padding:5px"',
			write_html('div', 'class="ui-widget-header ui-corner-top"',
				write_html('h3', 'class="title_wihte"', $lang['class'])
			).
			write_html('div', 'class="ui-widget-content ui-corner-bottom" style="padding:5px"',
				write_html('table', 'class="tablesorter"', 
					write_html('thead', '',
						write_html('tr', '',
							write_html('th', 'width="16"', '&nbsp;').
							write_html('th', '', $lang['class']).
							write_html('th', 'width="60"', $lang['total']).
							write_html('th', 'width="60"', $lang['boys']).
							write_html('th', 'width="60"', $lang['girls']).
							write_html('th', 'width="60"', $lang['muslim']).
							write_html('th', 'width="60"', $lang['christian'])
						)
					).
					write_html('tbody', '', $class_tbody)
				)
			).
			write_html('div', 'id="groups_div"', '')
		);
	}
	
	if($_POST['step'] == 'groups'){
		$level_id = $_POST['level_id'];
		$class_id = $_POST['class_id'];
		/*
		$class = new Classes($class_id);
		$groups = $class->getGroups();
		
		$DB_student = DB_student;
		$DB_year = Db_prefix.$_POST['year'];
		$group_tbody = '';
		
		$groups = do_query_resource("SELECT * FROM groups WHERE parent='class' AND parent_id=$class_id", $DB_year);
		if(mysql_num_rows($groups) > 0){
			foreach($groups as $group){
				$students = $group->getStudents();
				$service_name = '';
				if($group->service_id != '' ){
					$service = new Services( $group->service_id);
					$service_name = $service->getName();
				}
				$group_tbody .= write_html('tr', '',
					write_html('td', '', '<input type="checkbox" name="group_id[]" value="'.$group['id'].'" />').
					write_html('td', '', $group['name']).
					write_html('td', '', $service_name).
					write_html('td', '', $group['comments']).
					write_html('td', '', count($students))
				);
			}
		}*/
		
		$group_table = Groups::getListTable('class', $class_id);
		echo write_html('div', 'style="padding:10px"',
			write_html('div', 'class="ui-widget-header ui-corner-top"',
				write_html('h3', 'class="title_wihte"', $lang['groups'])
			).
			write_html('div', 'class="ui-widget-content ui-corner-bottom" style="padding:5px"',
				$group_table
			)
		);
	}
		
	// STEP 5  Submit class and group and set student materials 
	if($_POST['step'] == '5'){
		$answer = array();
		$year = $_POST['year'];
		$DB_year = Db_prefix.$_POST['year'];
		$std_id = $_POST['id'];
		$student = new Students($std_id);
		$class_id = $_POST['class_id'];
		$class= new Classes($class_id, $year);
		$level_id = $_POST['level'];
		$level = new Levels($level_id);
		$status = '1';
		$join_date = dateToUnix($_POST['join_date']);
		$materials = array();
		$student->status = $status;
		//$accms= $sms->getAccms();
		$safems= $sms->getSafems();
		$count_materials = 0;
		// update student status
		do_query_edit("UPDATE student_data SET status=$status, join_date='$join_date' WHERE id=$std_id", $this_system->database, $this_system->ip);
		
		if($_POST['class_id'] != ''){
			do_query_edit("DELETE FROM classes_std WHERE std_id=$std_id", $DB_year);
			if(do_query_edit("INSERT INTO classes_std (class_id, std_id, new_stat) VALUES ($class_id, $std_id, 2)", $DB_year)){
				if(MS_codeName != 'sms_basic'){
					$class_services = $class->getServices();
					if($class_services != false && count($class_services) > 0){
						foreach($class_services as $service){
							if(!in_array($service, $materials)){
								$materials[] = $service;
							}
						}
						//print_r($class_services);
					} else {
						$level_services = $level->getServices();
						
						if(count($level_services) > 0){
							foreach($level_services as $service){
								$service_id = $service->id;
								do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES ($class_id, $service_id)", $DB_year);
								if(!in_array($service, $materials)){
									$materials[] = $service;
								}
							}
						}
					}
					
					// Set services
					$count_materials = count($materials);
					if($count_materials > 0 ){
						foreach($materials as $service){
							$service_id = $service->id;
							if($service->optional == 0){
								do_query_edit("INSERT INTO materials_std (std_id, services) VALUES ($std_id, $service_id)", $DB_year);
							}
						}
					}
					
					// SET Group
					if(isset($_POST['group_id'])){
						foreach($_POST['group_id'] as $group_id){
							$group = new Groups($group_id);
							$group_service = $group->service_id;
							do_query_edit("INSERT INTO groups_std (group_id, std_id) VALUES ($group_id, $std_id)", $DB_year);
							if($group->service_id != '' && !in_array($group_service, $materials)){
								$count_materials++;
								do_query_edit("INSERT INTO materials_std (std_id, services) VALUES ($std_id, $group->service_id)", $DB_year);
							}
						}
					}
				}
			}
		}
		
		$new_student_summary = write_html('div', 'id="newStd_wizard_finish" class="ui-state-highlight ui-corner-all" style="padding:7px; margin:10px"', 
			write_html('table', 'width="100%" border="0" cellspacing="0" cellpadding="0"',
				write_html('tr', '',
					write_html('td', 'width="120" valign="middel" ', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['id'])
					).
					write_html('td', 'valign="middel" colspan="2"',
						write_html('div', 'class="ui-widget-content ui-corner-right fault_input"',
							$std_id
						)
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120" valign="middel" ', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
					).
					write_html('td', 'valign="middel" colspan="2"',
						write_html('div', 'class="ui-widget-content ui-corner-right fault_input" style="width:300px"',
							$student->getName()
						)
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120" valign="middel" ', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['level'])
					).
					write_html('td', 'valign="middel" colspan="2"',
						write_html('div', 'class="ui-widget-content ui-corner-right fault_input"',
							$level->getName()
						)
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120" valign="middel" ', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['class'])
					).
					write_html('td', 'valign="middel" colspan="2"',
						write_html('div', 'class="ui-widget-content ui-corner-right fault_input"',
							$class->getName()
						)
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120" valign="middel" ', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['materials'])
					).
					write_html('td', 'valign="middel" colspan="2"',
						write_html('div', 'class="ui-widget-content ui-corner-right fault_input"',
							$count_materials
						)
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120" valign="middel" ', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['join_date'])
					).
					write_html('td', 'valign="middel" colspan="2"',
						write_html('div', 'class="ui-widget-content ui-corner-right fault_input"',
							$_POST['join_date']
						)
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120" valign="middel" ', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['status'])
					).
					write_html('td', 'valign="middel" colspan="2"',
						write_html('div', 'class="ui-widget-content ui-corner-right fault_input"',
							$lang['registred']
						)
					)
				)
			)
		);
		if($status == 1){
			$m = systemMessages::sendInscriptionMsg($student, $new_student_summary);
		}
		echo $new_student_summary;
		exit;
	}
}
