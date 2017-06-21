<?php
function transferStudent($old_db, $new_db, $service=false, $marks=true){
	$alph = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$levels = getItemOrder('levels');
	$new_order = array();
	$error = false;
	/*if(count($levels) == 0){
		$lvs = get_grade_list(true);
	}
	print_r($levels);
	$levels = array();
	foreach($lvs as $key => $value){
		$levels[] = $key	;
	}*/
	
	// Baby classes
	$first_level = do_query("SELECT * FROM levels WHERE id=".$levels[0], DB_student);
	$first_lvl_classes = getClassesByLevel($levels[0]);
	$count_class= 0;
	foreach($first_lvl_classes as $class_id){
		$class_name_en = $first_level['name_ltr'].'-'.$alph[$count_class];
		$class_name_ar = $first_level['name_rtl'].'-'.$alph[$count_class];
		$class = do_query("SELECT * FROM classes WHERE id=$class_id", $old_db);
		$room_no = $class['room_no'];
		if(do_query_edit("INSERT INTO classes (name_ltr, name_rtl, level_id, room_no) VALUES ('$class_name_en', '$class_name_ar', ".$levels[0].", '$room_no')", $new_db)){
			$this_class_id = mysql_insert_id();
			$new_order[] = $this_class_id;
			if(MS_codeName!= 'sms_basic' && $service!= false){
				$class_service_arr = array();
				$level_services = do_query_resource("SELECT id FROM services WHERE level_id=".$levels[0], $new_db);
				while($ser = mysql_fetch_assoc($level_services)){
					$class_service_arr[] = $ser['id'];
				}
				if(count($class_service_arr) > 0) {
					if(!do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES ($this_class_id, ".implode("), ($this_class_id,", $class_service_arr).")", $new_db)){
						$error = true;
					}
				}
			}
		} else {
			$error = true;
		}

	}

	// All other class except the last one
	for($i=0; $i< count($levels)-1; $i++){
		$level_id = $levels[$i];
		$next_level_id = $levels[$i+1];
		$count_class=0;
		$next_level = do_query("SELECT * FROM levels WHERE id=$next_level_id", DB_student);
		$classes = getClassesByLevel($level_id);
		foreach($classes as $class_id){
			$class_name_en = $next_level['name_ltr'].'-'.$alph[$count_class];
			$class_name_ar = $next_level['name_rtl'].'-'.$alph[$count_class];
			$count_class++;
			if(do_query_edit("INSERT INTO classes (name_ltr, name_rtl, level_id) VALUES ('$class_name_en', '$class_name_ar', ".$next_level_id.")", $new_db)){
				$new_class_id = mysql_insert_id();
				$new_order[] = $new_class_id;
				// insert class service if service is true
				if(MS_codeName!= 'sms_basic' && $service!= false){
					$class_service_arr = array();
					$level_services = do_query_resource("SELECT id FROM services WHERE level_id=$next_level_id", $new_db);
					while($ser = mysql_fetch_assoc($level_services)){
						$class_service_arr[] = $ser['id'];
					}
					if(count($class_service_arr) > 0) {
						if(!do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES ($new_class_id, ".implode("), ($new_class_id,", $class_service_arr).")", $new_db)){
							$error = true;
						}
					}
				}
				// chk if studebt exist before continue
				$stds = getStudentIdsByClass($class_id);
				if($stds != false && count($stds) > 0){
					foreach($stds as $std_id ){
						if($marks){
							$mark_q = do_query("SELECT result FROM final_result WHERE std_id=$std_id", $old_db);
							$std_result = $mark_q['result'];
							if( $std_result == 1 || $std_result == ''){
								do_query_edit("INSERT INTO classes_std (class_id, std_id, new_stat) VALUES ('$new_class_id', '$std_id', 1)", $new_db);	
								$std_class = $new_class_id;
							} elseif( $std_result == 0){
								$red_class = do_query("SELECT id FROM classes WHERE level_id=$level_id LIMIT 1", $new_db);
								$red_class_id = $red_class['id'];
								do_query_edit("INSERT INTO classes_std (class_id, std_id, new_stat) VALUES ('$red_class_id', '$std_id', 0)", $new_db);
								$std_class = $red_class_id;
							} 
						} else {
							do_query_edit("INSERT INTO classes_std (class_id, std_id, new_stat) VALUES ('$new_class_id', '$std_id', 1)", $new_db);	
							$std_class = $new_class_id;
						}
						if(MS_codeName!= 'sms_basic' && $service!= false){
							$service_arr = array();
							$services = do_query_resource("SELECT services.id FROM materials_classes, services, classes 
								WHERE classes.id=$std_class 
								AND classes.id=materials_classes.class_id 
								AND classes.level_id=services.level_id 
								AND materials_classes.services=services.id 
								AND services.optional!=1", $new_db);	
							while($ser = mysql_fetch_assoc($services)){
								$service_arr[] = $ser['id'];
							}
							if(!do_query_edit("INSERT INTO materials_std (std_id, services) VALUES ($std_id, ".implode("), ($std_id,", $service_arr).")", $new_db)){
								$error = true;
							}
						}
					}
				}
			}
		}
	}
	return (!$error ? true : false);	
}

function copy_db_data($old_table, $new_table){
	$error = false;
	$old = do_query_resource("SELECT * FROM $old_table", MySql_Database);
	$fields =  getTableFields( $old_table);
	while($row = mysql_fetch_assoc($old)){
		$values = array();
		foreach($fields as $f){
//			$old[$f] = addslashes($old[$f]);
//			$old[$f] = ereg_replace("\n","\\n",$old[$f]);
			if ($row[$f] != '') { 
				$values[] = '"'.$row[$f].'"' ; 
			} else { 
				$values[] = '""'; 
			}
		}
		if(!do_query_edit("INSERT INTO $new_table VALUES (".implode(", ",$values).");", MySql_Database)){
			$error = true;
		}
	}
	return (!$error ? true : false);
}

function generateClassServices($DB_year){
	$error = false;
	$level = get_grade_list(true);
	foreach($level as $level_id => $level_name){
		$service_arr = array();
		$services = do_query_resource("SELECT id FROM services WHERE level_id=$level_id", $DB_year);
		while($service = mysql_fetch_assoc($services)){
			$service_arr[] = $service['id'];
		}
		$classes = do_query_resource("SELECT id FROM classes WHERE level_id=$level_id", $DB_year);
		while($class = mysql_fetch_assoc($classes)){
		echo $level_name;
			$class_id = $class['id'];
			if(!do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES ($class_id, ".implode("), ($class_id,", $service_arr).")", $DB_year)){
				$error = true;
			}
				echo "INSERT INTO materials_classes (class_id, services) VALUES ($class_id, ".implode("), ($class_id,", $service_arr).")";
		}
	}	
	return !$error ? true : false;
}

function generatOptionalGroup($DB_year){
	$error = false;
	$DB_student = DB_student;
	$mats_arr[] = array();
	$mats_q = do_query_resource("SELECT lang_1, lang_2, lang_3 FROM student_data", DB_student);
	while($mat = mysql_fetch_assoc($mats_q)){
		if(!in_array($mat['lang_1'], $mats_arr)){ $mats_arr[] = $mat['lang_1'];}
		if(!in_array($mat['lang_2'], $mats_arr)){ $mats_arr[] = $mat['lang_2'];}
		if(!in_array($mat['lang_3'], $mats_arr)){ $mats_arr[] = $mat['lang_3'];}
	}
	$levels = do_query_resource("SELECT id FROM levels");
	while($level = mysql_fetch_assoc($levels)){
		$level_id=$level['id'];
		$optional_services = array();
		$field = 'name_'.$_SESSION['dirc'];
		$services = do_query_resource("SELECT $DB_year.services.id AS serviceid, $DB_student.materials.id  AS matid, $DB_student.materials.$field  AS name
			FROM $DB_student.materials, $DB_year.services
			WHERE $DB_year.services.level_id=$level_id 
			AND $DB_year.services.optional=1
			AND $DB_year.services.mat_id=$DB_student.materials.id", $DB_year
		);
		while($service = mysql_fetch_assoc($services)){
			$optional_services[$service['serviceid']] = array($service['matid'], $service['name']);
		}
		$classes = do_query_resource("SELECT id FROM classes WHERE level_id=$level_id", $DB_year);
		while($class = mysql_fetch_assoc($classes)){
			$class_id = $class['id'];
			foreach($optional_services as $service_id=> $mat){
				$service_name = $mat[1];
				$mat_id = $mat[0];
				$chk = do_query("SELECT id FROM `groups` WHERE parent='class' AND parent_id=$class_id AND service_id=$service_id", $DB_year);
				$group_id= false;
				if($chk['id'] != ''){
					$group_id = $chk['id'];
				} else {
					$group_id = do_query_insert( 'groups', "name, parent, parent_id, service_id", "'$service_name', 'class', $class_id, $service_id", $DB_year);
				}
				if($group_id != false){
					$students = do_query_resource(
						"SELECT DISTINCT $DB_year.classes_std.std_id 
						FROM $DB_year.classes_std, $DB_student.student_data
						WHERE $DB_year.classes_std.class_id=$class_id 
						AND $DB_year.classes_std.std_id=$DB_student.student_data.id 
						AND($DB_student.student_data.status=1 OR $DB_student.student_data.status=3)
						AND ( 
							$DB_student.student_data.lang_1=$mat_id
							OR $DB_student.student_data.lang_2=$mat_id
							OR $DB_student.student_data.lang_3=$mat_id
						)", $DB_year);
					$values = array();
					while($std = mysql_fetch_assoc($students)){
						$std_id =$std['std_id'];
						$chk_std_gr = do_query_resource("SELECT * FROM groups_std WHERE group_id=$group_id AND std_id=$std_id", $DB_year);
						if(mysql_num_rows($chk_std_gr) == 0){
							$values[] = "($group_id, $std_id)";
							do_query_edit("INSERT INTO materials_std (std_id, services ) VALUES ($std_id, $service_id)", $DB_year);
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

function generatReligionGroup($db, $material_arr){
	$error = false;
	$levels = getItemOrder('levels');
	$main_db = DB_student;
	$islamic = new Materials($material_arr[0]);
	$islamic_name = $islamic->getName();
	$christian = $material_arr[1];
	$christian = new Materials($material_arr[1]);
	$christian_name = $christian->getName();

	$islm_group_sql_arr = array();
	$chris_group_sql_arr = array();
	$service_sql_arr = array();
	
	foreach($levels as $level_id){
		$level = new Levels($level_id);
		$classes = $level->getClass();
		if($classes !== false) {
			foreach($classes as $class){
				$chk_islm = do_query("SELECT id FROM groups WHERE parent='class' AND parent_id=$class->id AND service_id=$islamic->id", $db);
				if($chk_islm['id'] != ''){
					$isl_group_id = $chk_islm['id'];
				} else {
					$isl_group_id = do_query_insert( 'groups', "name, parent, parent_id, service_id", "'$islamic_name', 'class', $class->id, $islamic->id", $db);
				}
				
				$chk_chris = do_query("SELECT id FROM `groups` WHERE parent='class' AND parent_id=$class->id AND service_id=$christian->id", $db);
				if($chk_chris['id'] != ''){
					$chris_group_id = $chk_chris['id'];
				} else {
					$chris_group_id = do_query_insert( 'groups', "name, parent, parent_id, service_id", "'$christian_name', 'class', $class->id, $christian->ser", $db);
				}
				
				$isl_sql  = "SELECT DISTINCT $main_db.student_data.id
				FROM $main_db.student_data, $db.classes_std 
				WHERE $main_db.student_data.religion=1
				AND $main_db.student_data.id = $db.classes_std.std_id
				AND $db.classes_std.class_id = $class->id";
				$isl_query = do_query_resource($isl_sql, $main_db);
				while($row = mysql_fetch_assoc($isl_query)){
					$std_id = $row['id'];
					if(mysql_num_rows(do_query_resource("SELECT * FROM materials_std WHERE std_id=$std_id AND services=$islamic->id", $db))==0){
						$service_sql_arr[] = "($std_id, $islamic_ser)";
					}
					if(mysql_num_rows(do_query_resource("SELECT * FROM groups_std WHERE std_id=$std_id AND group_id=$isl_group_id", $db))==0){
						$islm_group_sql_arr[] = "($isl_group_id, $std_id)";
					}
				}
				$chris_sql  = "SELECT DISTINCT $main_db.student_data.id
				FROM $main_db.student_data, $db.classes_std 
				WHERE $main_db.student_data.religion=2
				AND $main_db.student_data.id = $db.classes_std.std_id
				AND $db.classes_std.class_id = $class->id";
				$chris_query = do_query_resource($chris_sql, $main_db);
				while($row = mysql_fetch_assoc($chris_query)){
					$std_id = $row['id'];
					if(mysql_num_rows(do_query_resource("SELECT * FROM materials_std WHERE std_id=$std_id AND services=$christian->id", $db))==0){
						$service_sql_arr[] = "($std_id, $christian_ser)";
					}
					if(mysql_num_rows(do_query_resource("SELECT * FROM groups_std WHERE std_id=$std_id AND group_id=$chris_group_id", $db))==0){
						$chris_group_sql_arr[] = "($chris_group_id, $std_id)";
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

function copyTerms($old_db, $new_db, $post){
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

function copyScheduleStructure($old_db, $new_db){
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
?>