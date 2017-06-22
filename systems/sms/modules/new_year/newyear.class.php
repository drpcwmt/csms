<?php
/** New Year
*
*/
class NewYear{
	
	
	
	static function datesScreen(){
		$screen = new Layout();;
		$screen->this_year = $_SESSION['year']+1;
		$screen->next_year = ($_SESSION['year']+2);
		$levels = do_query_obj("SELECT COUNT(*) AS count FROM levels", DB_student);
		$screen->count_levels = $levels->count;
		$profs = do_query_obj("SELECT COUNT(*) AS count FROM profs", DB_student);
		$screen->count_profs = $profs->count;
		$principals = count(do_query_array("SELECT DISTINCT id FROM principals", DB_student));
		$screen->count_principals = $principals;
		$supervisors = do_query_obj("SELECT COUNT(*) AS count FROM supervisors", DB_student);
		$screen->count_supervisors = $supervisors->count;
		$halls = do_query_obj("SELECT COUNT(*) AS count FROM halls", DB_student);
		$screen->count_halls = $halls->count;
		
		return fillTemplate("modules/new_year/templates/screen_dates.tpl", $screen);
		
	}
	
	static function transfereScreen(){
		$screen = new Layout();
		if(MS_codeName!= 'sms_basic'){
				// marks approved
			$chk_approve = do_query_array("SELECT id FROM terms WHERE approved!=1", DB_year);
			$mark_approved = count($chk_approve) > 0 ? false : true;
			//$screen->transfer_std_attr = $mark_approved ? 'disabled="disabled"' : 'checked="checked"';

			$screen->pro_opt_hidden = '';
			
			$materials = Materials::getList();
			$opts = array();
			foreach($materials as $mat){
				$opts[$mat->id] = $mat->getName();
			}
			$screen->mat_opts = write_select_options($opts);
			
			$levels = Levels::getList();
			$first_level = $levels[0];
			$terms = Terms::getTermsByCon('level', $first_level->id);
			$terms_trs = array();
			if($terms != false && count($terms) > 0){
				foreach($terms as $term){
					$terms_trs[] = write_html('tr', '',
						write_html('td', '', $term->getName()).
						write_html('td', '', '<input name="begin_date[]" class="datepicker mask-date" value="'.unixToDate(NewYear::getNextYear($term->begin_date)).'" />').
						write_html('td', '', '<input name="end_date[]" class="datepicker mask-date" value="'.unixToDate(NewYear::getNextYear($term->end_date)).'" />')
					);	
				}
				$screen->terms_trs = implode('', $terms_trs);
			}
		
		} else {
			$screen->pro_opt_hidden = 'hidden';
			$screen->transfer_std_attr = 'disabled="disabled"';
		}
		
		return fillTemplate("modules/new_year/templates/screen_transfer.tpl", $screen);
	}
	
	static function createDB($post){
		global $lang;
		$result = true;
		$old_year = $_SESSION['year'];
		$new_year = ($_SESSION['year']+1);
		$chk_db_exist = do_query("SELECT year FROM years WHERE year=$new_year");
		if($chk_db_exist['year'] != ''){
			die( write_error($lang['db_already_exists']));
		}
	//	do_query_edit("UPDATE settings SET value='2' WHERE key_name='system_stat'");
		if(do_query_edit("INSERT INTO years ( year, user_name, begin_date, end_date) VALUES ($new_year, 'csms', ".datetounix($post['begin_date']).", ".datetounix($post['end_date']).")",DB_student)){
			do_query_edit('CREATE DATABASE '.Db_prefix.$new_year,DB_student);
			// create the database
			require_once('csms_sms_year.sql.php');
			$statments = explode(';', $new_year_sql);
			$new_db = Db_prefix.$new_year;
			foreach($statments as $sql){	
				if(trim($sql) != ''){	
					if(!do_query_edit(trim($sql), $new_db)){
						$result = false;
					}
				}
			}
		} 
		return $result;
	}
	
	static function transferStudent($old_db, $new_db, $service=false, $marks=false){
		$cur_year = $_SESSION['year'];
		$_SESSION['year'] = $old_db;
		$alph = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$levels = getItemOrder('levels');
		$new_order = array();
		$error = false;
		// Create new Classes
		// Baby classes
		$first_level = new Levels($levels[0]);
		$first_lvl_classes = $first_level->getClassList($old_db);
		$count_class= 0;
		foreach($first_lvl_classes as $class){
			$new_class=new stdClass();
			$new_class->name_ltr = $first_level->name_ltr.'-'.$alph[$count_class];
			$new_class->name_rtl = $first_level->name_rtl.'-'.$alph[$count_class];
			$new_class->level_id = $levels[0];
			if($new_class_id = do_insert_obj($new_class, 'classes', $new_db)){
				$new_order[] = $new_class_id;
			}
			$count_class++;
		}
		for($i=0; $i< count($levels)-1; $i++){
			$count_class=0;
			$level_id = $levels[$i];
			$level = new Levels($level_id);
			$classes = $level->getClassList($old_db);
			$next_level_id = $levels[$i+1];
			$next_level = new Levels($next_level_id);
			
			foreach($classes as $class){
				$new_class = new stdClass();
				$new_class->name_ltr = $next_level->name_ltr.'-'.$alph[$count_class];
				$new_class->name_rtl = $next_level->name_rtl.'-'.$alph[$count_class];
				$new_class->level_id = $next_level_id;
				$count_class++;
				if($new_class_id = do_insert_obj($new_class, 'classes', $new_db)){
					$new_order[] = $new_class_id;
				}
			}
		}

		
		// All other class except the last one
		for($i=0; $i< count($levels)-1; $i++){
			$level_id = $levels[$i];
			$level = new Levels($level_id);
			$classes = $level->getClassList($old_db);
			$next_level_id = $levels[($i+1)];
			$next_level = new Levels($next_level_id);
		//	echo $level->getName().'-'.$next_level->getName().'<br />';
			$newClasses = $next_level->getClassList(str_replace(Db_prefix, '', $new_db));
			$x=0;

			foreach($classes as $class){
				$new_class = $newClasses[$x];
				$new_class_id = $new_class->id;	
				$x++;			
				// insert class service if service is true
				if(MS_codeName!= 'sms_basic' && $service!= false){
					$class_service_arr = array();
					$level_services = $next_level->getServices();
					foreach($level_services as $ser){
						$class_service_arr[] = $ser->id;
					}
					if(count($class_service_arr) > 0) {
						if(!do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES ($new_class_id, ".implode("), ($new_class_id,", $class_service_arr).")", $new_db)){
							$error = true;
						}
					}
				}
				// chk if studebt exist before 
				$stds = $class->getStudents(array(1,3));
				if($stds != false && count($stds) > 0){
					foreach($stds as $std ){
						if(MS_codeName!= 'sms_basic' && $marks!= false){
							$mark_q = do_query_obj("SELECT result FROM final_result WHERE std_id=$std->id", Db_prefix.$old_db);
							$std_result = $mark_q->result;
							if( $std_result == 1){
								do_query_edit("INSERT INTO classes_std (class_id, std_id, new_stat) VALUES ('$new_class_id', '$std->id', 1)", $new_db);	
							} elseif( $std_result == 0){
								$red_class = do_query_obj("SELECT id FROM classes WHERE level_id=$level->id LIMIT 1", $new_db);
								do_query_edit("INSERT INTO classes_std (class_id, std_id, new_stat) VALUES ('$red_class->id', '$std->id', 0)", $new_db);
							} 
						} else {
							do_query_edit("INSERT INTO classes_std (class_id, std_id, new_stat) VALUES ('$new_class_id', '$std->id', 1)", $new_db);	
						}
						
						if(MS_codeName!= 'sms_basic' && $service!= false){
							$service_arr = array();
							foreach($level_services as $ser){
								if($ser->optional!=1 || $ser->id==$std->lang_1 || $ser->id==$std->lang_2 || $ser->id==$std->lang_3){
									$service_arr[] = $ser->id;
								}
							}
							if(!do_query_edit("INSERT INTO materials_std (std_id, services) VALUES ($std->id, ".implode("), ($std->id,", $service_arr).")", $new_db)){
								$error = true;
							}
						}
					}
				}
			}
		}
		
		// update Class Order
		do_update_obj(array('order'=>implode(',', $new_order)), "items='classes'", 'items_order', DB_student);
		
		// FInal gradde
		$final_level = new Levels($levels[count($levels)-1]);
		$graduating = $final_level->getStudents();
		$year = getYear();
		/*foreach($graduating as $std){
			do_update_obj(array('status'=>5, 'quit_date'=>$year->end_date), 'id='.$std->id, 'student_data', DB_student);
		}*/
		$_SESSION['year'] = $cur_year;
		return (!$error ? true : false);	
	}

	static function generatOptionalGroup($DB_year){

		$error = false;
		$DB_student = DB_student;
		$mats_arr[] = array();
		$mats_q = do_query_array("SELECT lang_1, lang_2, lang_3 FROM student_data", DB_student);
		foreach($mats_q as $mat){
			if(!in_array($mat->lang_1, $mats_arr)){ $mats_arr[] = $mat->lang_1;}
			if(!in_array($mat->lang_2, $mats_arr)){ $mats_arr[] = $mat->lang_2;}
			if(!in_array($mat->lang_3, $mats_arr)){ $mats_arr[] = $mat->lang_3;}
		}
		$levels = Levels::getList();
		foreach($levels as $level){
			$optional_services = array();
			$field = 'name_'.$_SESSION['dirc'];
			$services = $level->getServices();
			foreach($services as $service){
				if($service->optional == 1)
				$optional_services[] = $service;
			}
			
			$classes =$level->getClassList();
			foreach($classes as $class){
				foreach($optional_services as $service){
					$service_name = $service->getName();
					$mat_id = $service->mat_id;
					$chk = do_query_obj("SELECT id FROM `groups` WHERE parent='class' AND parent_id=$class->id AND service_id=$service->id", $DB_year);
					$group_id= false;
					if($chk->id != ''){
						$group_id = $chk->id;
					} else {
						$new_group = new stdClass();
						$new_group->name = $service_name;
						$new_group->parent = 'class';
						$new_group->parent_id = $class->id;
						$new_group->service_id = $service->id;
						$group_id = do_insert_obj($new_group, 'groups', $DB_year);
					}
					if($group_id != false){
						$students = do_query_array(
							"SELECT DISTINCT $DB_year.classes_std.std_id  AS id
							FROM $DB_year.classes_std, $DB_student.student_data
							WHERE $DB_year.classes_std.class_id=$class->id 
							AND $DB_year.classes_std.std_id=$DB_student.student_data.id 
							AND($DB_student.student_data.status=1 OR $DB_student.student_data.status=3)
							AND ( 
								$DB_student.student_data.lang_1=$mat_id
								OR $DB_student.student_data.lang_2=$mat_id
								OR $DB_student.student_data.lang_3=$mat_id
							)", $DB_year);
						$values = array();
						foreach($students as $std){
							$chk_std_gr=do_query_array("SELECT * FROM groups_std WHERE group_id=$group_id AND std_id=$std->id", $DB_year);
							if(count($chk_std_gr) == 0){
								$values[] = "($group_id, $std->id)";
								if(do_query_obj("SELECT services FROM materials_std WHERE std_id=$std->id AND services=$service->id", $DB_year) == false){
									do_query_edit("INSERT INTO materials_std (std_id, services ) VALUES ($std->id, $service->id)", $DB_year);
								}
							}
						}
						if(count($values) > 0){
							if(!do_query_edit("INSERT INTO groups_std (group_id, std_id) VALUES ".implode(', ', $values), $DB_year)){
								$error = true;	
							}
						}
					} else {
						$error = true;
					}
				}
			}
		}
		return (!$error ? true : false);
	}
	
	static function generatReligionGroup($db, $material_arr, $level_id=false){
		$error = false;
		if($level_id != false){
			$levels = array(new Levels($level_id));
		} else {
			$levels = Levels::getList();
		}
		$main_db = DB_student;
		$islamic = new Materials($material_arr[0]);
		$islamic_name = $islamic->getName();
		$christian = $material_arr[1];
		$christian = new Materials($material_arr[1]);
		$christian_name = $christian->getName();
	
		$islm_group_sql_arr = array();
		$chris_group_sql_arr = array();
		$service_sql_arr = array();
		
		foreach($levels as $level){
			$level_id = $level->id;
			$classes = $level->getClassList();
			$islamic_service = do_query_obj("SELECT * FROM services WHERE level_id=$level->id AND mat_id=".$material_arr[0], $db);
			$christian_service = do_query_obj("SELECT * FROM services WHERE level_id=$level->id AND mat_id=".$material_arr[1], $db);

			if($classes !== false) {
				foreach($classes as $class){
					$chk_islm = do_query_obj("SELECT id FROM groups WHERE parent='class' AND parent_id=$class->id AND service_id=$islamic_service->id", $db);
					if($chk_islm->id != ''){
						$isl_group_id = $chk_islm->id;
					} else {
						$new_group = new stdClass();
						$new_group->name = $islamic_name;
						$new_group->parent = 'class';
						$new_group->parent_id = $class->id;
						$new_group->service_id = $islamic_service->id;
						$isl_group_id = do_insert_obj($new_group, 'groups', $db);
					}
					
					$chk_chris = do_query("SELECT id FROM `groups` WHERE parent='class' AND parent_id=$class->id AND service_id=$christian_service->id", $db);
					if($chk_chris['id'] != ''){
						$chris_group_id = $chk_chris['id'];
					} else {
						$new_group = new stdClass();
						$new_group->name = $christian_name;
						$new_group->parent = 'class';
						$new_group->parent_id = $class->id;
						$new_group->service_id = $christian_service->id;
						$chris_group_id = do_insert_obj($new_group, 'groups', $db);
					}
					
					$isl_sql  = "SELECT DISTINCT $main_db.student_data.id
					FROM $main_db.student_data, $db.classes_std 
					WHERE $main_db.student_data.religion=1
					AND $main_db.student_data.id = $db.classes_std.std_id
					AND $db.classes_std.class_id = $class->id";
					$isl_query = do_query_array($isl_sql, $main_db);
					foreach($isl_query as $std ){
						if(!do_query_obj("SELECT * FROM materials_std WHERE std_id=$std->id AND services=$islamic_service->id", $db)){
							$service_sql_arr[] = "($std->id, $islamic_service->id)";
						}
						if(!do_query_obj("SELECT * FROM groups_std WHERE std_id=$std->id AND group_id=$isl_group_id", $db)){
							$islm_group_sql_arr[] = "($isl_group_id, $std->id)";
						}
					}
					$chris_sql  = "SELECT DISTINCT $main_db.student_data.id
					FROM $main_db.student_data, $db.classes_std 
					WHERE $main_db.student_data.religion=2
					AND $main_db.student_data.id = $db.classes_std.std_id
					AND $db.classes_std.class_id = $class->id";
					$chris_query = do_query_array($chris_sql, $main_db);
					foreach($chris_query as $std){
						if(!do_query_obj("SELECT * FROM materials_std WHERE std_id=$std->id AND services=$christian_service->id", $db)){
							$service_sql_arr[] = "($std->id, $christian_service->id)";
						}
						if(!do_query_obj("SELECT * FROM groups_std WHERE std_id=$std->id AND group_id=$chris_group_id", $db)){
							$chris_group_sql_arr[] = "($chris_group_id, $std->id)";
						}
					}
				}
			}
		}	
		
		// insert student services
		if(count($service_sql_arr) > 0){
			do_query_edit("INSERT INTO materials_std (std_id, services ) VALUES ".implode(', ', $service_sql_arr), $db);
		}
		
		// create islamic Group
		if(count($islm_group_sql_arr) > 0){	
			if(!do_query_edit("INSERT INTO groups_std (group_id, std_id) VALUES ".implode(",", $islm_group_sql_arr), $db)){
				$error = true;	
			}
		}
	
		// create Christian Group
		if(count($chris_group_sql_arr) > 0){	
			if(!do_query_edit("INSERT INTO groups_std (group_id, std_id) VALUES ".implode(",", $chris_group_sql_arr), $db)){
				$error = true;	
			}
		}
		
		return (!$error ? true : false);
	}
	
	static function copyTerms($old_db, $new_db, $post){
		$error = false;
		$levels = getItemOrder('levels');
		foreach($levels as $level_id){
			for($i=1; $i<=count($post['begin_date']); $i++){
				$begin_date = dateToUnix($post['begin_date'][$i-1]);
				$end_date = dateToUnix($post['end_date'][$i-1]);
				$term = do_query("SELECT * FROM terms WHERE term_no=$i AND level_id=$level_id", $old_db);
				if($term['id']!=''){
					$new_sql = "INSERT INTO terms (id, term_no, title, begin_date, end_date, marks, level_id, exam_no, approved) VALUES (".$term['id'].", $i, '".$term['title']."', $begin_date, $end_date, '".$term['marks']."', $level_id, '" .$term['exam_no']."',0)";
					if(!do_query_edit($new_sql, $new_db)){
						$error = true;
					}
				}
			}
		}
		return $error ? false : true;
	}
	
	static function copyScheduleStructure($old_db, $new_db){
		$error = false;
		$levels = getItemOrder('levels');
		foreach($levels as $level_id){
			if(!do_query_edit("INSERT INTO $new_db.schedules_date SELECT $old_db.schedules_date.* FROM $old_db.schedules_date WHERE $old_db.schedules_date.con='level' AND $old_db.schedules_date.con_id=$level_id AND $old_db.schedules_date.date<=7", $old_db)){
				$error = true;
			}
			if(!do_query_edit("INSERT INTO $new_db.schedules_times SELECT $old_db.schedules_times.* FROM $old_db.schedules_times, $old_db.schedules_date WHERE $old_db.schedules_date.con='level' AND $old_db.schedules_date.con_id=$level_id AND $old_db.schedules_date.date<=7 AND $old_db.schedules_date.id=$old_db.schedules_times.rec_id", $old_db)){
				$error = true;
			}
		}
		
		return $error ? false : true;
	}

	static function SchoolFeesLayout($per=''){
		global $this_system;
		$per = $per != '' ? $per : $this_system->getSettings('fees_annual_increasment');
		
		$levels = Levels::getList();
		$trs =array();
		foreach($levels as $level){
			$fees = $level->getFees();
			$total_before = array();
			$total_after = array();
			foreach($fees as $fee){
				$cur = $fee->currency;
				if(!isset($total_before[$cur])){ $total_before[$cur] = 0;}
				if(!isset($total_after[$cur])){ $total_after[$cur] = 0;}
				$total_before[$cur] += $fee->debit;
				if($fee->increase ==1){
					$value = $fee->debit * (1 + ($per /100));
					$nearest = $this_system->getSettings('fees_annual_nearset');
					$total_after[$cur] += ceil($value / $nearest) * $nearest ;
				}	
			}
			$first = true;
			foreach($total_before as $cur=>$value){
				$trs[] = write_html('tr', '',
					($first ? write_html('td', 'rowspan="'.count($total_before).'"', $level->getName()) : '').
					write_html('td', '', $cur).
					write_html('td', '', $value).
					write_html('td', '', $total_after[$cur])
				);
			}
		}
		
		$layout = new Layout();
		$year =  $_SESSION['year'];
		$layout->old_year = $year.'/'.($year+1);
		$layout->new_year = ($year+1).'/'.($year+2);
		$layout->level_trs = implode('', $trs);
		$layout->percent = $per;
		$layout->template = "modules/new_year/templates/screen_fees.tpl";
		
		return $layout->_print();
		
	}
	
	/******************* Finalize **********************/
	static function addRepearters(){
		$layout= new Layout();
		$layout->repeater_table = SchoolReport::loadRedoublingReport('', true);
		$layout->template = "modules/new_year/templates/finalize_redouble.tpl";
		
		return $layout->_print();
			
	}

	static function addQuited(){
		$layout= new Layout();
		$layout->quit_table = SchoolReport::loadQuitList();
		$layout->template = "modules/new_year/templates/finalize_quit.tpl";
		
		return $layout->_print();
			
	}
	
	static function getWaitingList(){
		$layout= new Layout();
		$layout->waiting_table = SchoolReport::loadWaitingReport();
		$layout->template = "modules/new_year/templates/finalize_waiting.tpl";
		
		return $layout->_print();
	}
	
	static function removeStudents($stds, $year){
		if(count($stds) > 0){
			foreach($stds as $std){
				$where[] = "std_id=$std->id";
			}
			return do_query_edit("DELETE FROM classes_std WHERE ".implode(' OR ', $where), Db_prefix.$year);
		} else {
			return true;
		}
	}
	
	static function getNextYear($date){
		$d = date('d', $date);
		$m = date('m', $date);
		return mktime(0,0,0, $m, $d , $_SESSION['year']+1);
	}
}