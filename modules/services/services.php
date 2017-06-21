<?php
## SMS 
## Services

/********************** Materials **********************/
// insert new service
if(isset($_GET['new'])){
	if(getPrvlg('resource_edit_materials')){
		if(isset($_POST['level_id']) && isset($_POST['mat_id'])){
			$mat_id = $_POST['mat_id'];
			$level_id = $_POST['level_id'];
			$mat = do_query("SELECT * FROM materials WHERE id=$mat_id", DB_student);
			do_query_edit("INSERT INTO services (mat_id, level_id, schedule, mark, optional, bonus) 
			VALUES($mat_id , $level_id, ".$mat['schedule'].", ".$mat['mark'].", ".$mat['optional'].", ".$mat['bonus'].")", DB_year);
			$service_id = mysql_insert_id();
			if( $service_id !=''){
				$answer['id'] = $service_id;
				$answer['schedule'] = $mat['schedule'];
				$answer['mark'] = $mat['mark'];
				$answer['optional'] =$mat['optional'];
				$answer['bonus'] = $mat['bonus'];
				$answer['error'] = "";
			} else {
				$answer['id'] = "";
				$answer['error'] = "Error";
			}
		}
	} else {
		$answer['error'] = $lang['no_privilege'];
	}
	echo  json_encode($answer);		
	exit;
}
	// update service
if(isset($_GET['update'])){
	$answer = array();
	if(getPrvlg('resource_edit_materials')){
		if( do_update_obj($_POST, 'id='.$_POST['id'], 'services', Db_prefix.$_SESSION['year'])){
			$answer['id'] = $_POST['id'];
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = "Error";
		}
	} else {
		$answer['error'] = $lang['no_privilege'];
	}
	echo  json_encode($answer);		
	exit;
}

/***********************************************/
/*if(isset($_GET['setoption'])){
	$answer = array();
	$mat_id = $_POST['matid'];
	$option = $_POST['option'];
	$value = $_POST['value'];
	if(!do_query_edit("UPDATE services SET $option=$value WHERE mat_id=$mat_id", DB_year)){
		$answer['error'] = $lang['error_updating'];	
	} else {
		$answer['error'] = '';
	}
	echo  json_encode($answer);		
	exit;
}
	// Material services list
if(isset($_GET['mat_services'])){
	if(getPrvlg('resource_read_materials')){
		require_once("services_materials.php");
		echo $mat_service;
	} else {
		echo write_error($lang['no_privilege']);
	}
	exit;
}
*/
/********************** Cons ***************************/
if(isset($_GET['con'])){
	$con = $_GET['con'];
	$con_id = $_GET['con_id'];
	switch($con){
		case 'supervisor':
			$readable = getPrvlg('resource_read_supervisors');
			$editable = getPrvlg('resource_edit_supervisors');
			$table = "supervisors";
			$ref = "id";
			$db = MySql_Database;
		break;
		case 'prof': 
			$readable = getPrvlg('resource_read_profs');
			$editable = getPrvlg('resource_edit_profs');
			$table = "profs_materials";
			$ref = "id";
			$db = MySql_Database;
		break;
		case 'level':
			$readable = getPrvlg('resource_read_levels');
			$editable = getPrvlg('resource_edit_levels');
			$table = "services";
			$ref = "level_id";
			$db = DB_year;
		break;
		case 'class':
			$readable = getPrvlg('resource_read_classes');
			$editable = getPrvlg('resource_edit_classes');
			$table = "materials_classes";
			$ref = "class_id";
			$db = DB_year;
		break;
		case 'group':
			$readable = getPrvlg('resource_read_groups');
			$editable = getPrvlg('resource_edit_groups');
			$table = "materials_groups";
			$ref = "group_id";
			$db = DB_year;
		break;
		case 'student':
			$readable = getPrvlg('std_read');
			$editable = getPrvlg('std_edit');
			$table = "materials_std";
			$ref = "std_id";
			$db = DB_year;
		break;
		case 'material':
			$readable = getPrvlg('resource_read_materials');
			$editable = getPrvlg('resource_edit_materials');
			$table = "services";
			$ref = "id";
			$db = DB_year;
		break;
	}
} else {
	$readable = false;
	$editable = false;
}

// delete service for con
if(isset($_GET['delete'])){
	if($editable){
		$service_id = $_GET['id'];
		if($con == 'material'){
			$sql = "DELETE FROM $table WHERE id=$service_id";
		} else {
			$sql = "DELETE FROM $table WHERE ".($con=="level" ? "id" : "services")."=$service_id AND $ref=$con_id";
		}
		if(do_query_edit($sql, $db)){
			$answer['id'] = $service_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = "Error";
		}
		echo  json_encode($answer);		
	} else {
		echo json_encode(array('error' => $lang['no_privilege']));
	}
	exit;
}


// services list
if(isset($_GET['list'])){
	$manager = new ServicesManager(safeGet($_GET['con']), safeGet($_GET['con_id']));
	if(isset($_GET['lvlbymat'])){
		echo $manager->getLevelByMat(safeGet($_GET['lvlbymat']));
		
	} elseif(isset($_GET['matbylvl'])){
		echo $manager->getMatByLevel(safeGet($_GET['matbylvl']));

	} elseif(isset($_GET['igservice'])){
		echo ServicesIG::assignServiceForm(safeGet($_GET['con']), safeGet($_GET['con_id']),safeGet('material'));
		
	} elseif(isset($_GET['add_form'])){
		if($sms->getSettings('ig_mode') == '0'){
			echo $manager->assignServiceForm();
		} else {
			echo ServicesIG::assignServiceForm(safeGet($_GET['con']), safeGet($_GET['con_id']));
		}
		
	} elseif(isset($_GET['add_service'])){
	//	$manager = new ServicesManager($_POST['con'], $_POST['con_id']);
		if($sms->getSettings('ig_mode') == '0'){
			$editale = $manager->editable;
			if($editable){
				echo ServicesManager::_save($_POST);
			} else {
				echo json_encode(array('error' => $lang['no_privilege']));
			}	
		} else {
			echo json_encode_result(ServicesIG::addStudentSubject($_POST));
		}
	} else {
		if($sms->getSettings('ig_mode') == '0'){
			echo $manager->loadLayout();
		} else {
			echo ServicesIG::getConSubjects(safeGet($_GET['con']), safeGet($_GET['con_id']), isset($_GET['exam']) ? safeGet('exam') : '');
		}
	}
}

if(isset($_GET['details'])){
	$service_id = safeGet($_GET['service_id']);
	
	if($_GET['details'] == 'timeline'){
		include('timeline.php');
	} else {
		if($sms->getSettings('ig_mode') == '1'){
			$service = new ServicesIG($service_id);
			echo $service->loadLayout();
		} else {
			$service = new Services($service_id);
			echo $service->loadLayout();
		}
	}
}

if(isset($_GET['skills'])){
	if(isset($_GET['updateskillterm'])){
		$skill_id = $_POST['skill_id'];
		$term_id = $_POST['term_id'];
		echo json_encode_result(do_insert_obj(array('skill_id'=>$skill_id, 'term_id'=>$term_id), 'services_skills_terms', $sms->db_year));
	} elseif(isset($_GET['deleteskillterm'])){
		$skill_id = $_POST['skill_id'];
		$term_id = $_POST['term_id'];
		echo json_encode_result(do_delete_obj("skill_id=".$skill_id." AND term_id=$term_id", 'services_skills_terms', $sms->db_year));
	} else {
		$service_id = safeGet($_GET['service_id']);
		$service = new Services($service_id);
		echo $service->skillsForm();
	}
}

if(isset($_GET['ig'])){
	if(isset($_GET['edit'])){
		echo json_encode_result(ServicesIg::_save($_POST));	
		
	} elseif(isset($_GET['add'])){
		$id = do_insert_obj($_POST, 'services_ig', $sms->db_year, $sms->ip);
		echo json_encode_result(array('id'=>$id));
		
	} elseif(isset($_GET['remove_std_service'])){
		$std_id = $_POST['std_id'];
		$exam = $_POST['exam'];
		$service_id = isset($_POST['service_id']) ? $_POST['service_id'] : '';
		echo json_encode_result(ServicesIG::removeStdService($std_id, $exam, $service_id));
		
	} elseif(isset($_GET['fees'])){
		if(isset($_GET['save'])){
			$serviceIg = new ServicesIG($_POST['id']);
			echo json_encode_result($serviceIg->saveFees($_POST));
		} else {
			$serviceIg = new ServicesIG(safeGet('id'));
	 		echo $serviceIg->loadFeesLayout(safeGet('lvl'), safeGet('mat_id'));	
		}
	}
}
?>