<?php
## LESSONS main file
$thisTemplatePath = "modules/lessons/templates";

//require_once('scripts/files_functions.php');
//require_once('scripts/hrms_functions.php');

if(MS_codeName == 'classic' || MS_codeName == 'elearning'){
//	require_once('scripts/msgms_functions.php');
//	require_once('scripts/services_functions.php');
}
if(MSEXT_lms){
	require_once('scripts/lms_functions.php');
}

// Dettach Summarry
if(isset($_GET['dettachsummary'])){
	if(isset($_POST['id']) && $_POST['id'] != ''){
		$sum_id =$_POST['id'];
		$lesson_id=$_POST['lesson_id'];
		if(do_query_edit("DELETE FROM lessons_summary WHERE lesson_id=$lesson_id AND summary_id=$sum_id", LMS_Database)){
			$answer['id'] = $sum_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['error_updating'];
	}
	print json_encode($answer);
	exit;
}

if(isset($_GET['home']) && isset($_GET['notes'])){
	include('notes_list.php');
	exit;
}



/************************ Notes ****************/	
if(isset($_GET['notes'])){
	if(isset($_GET['notes_list'])){
		include('lessons_notes_list.php');
		echo $notes_list;
	} else {
		include('lessons_notes.php');
	}
	exit;	
}

if(isset($_REQUEST['lesson_id']) && $_REQUEST['lesson_id']!=''){
	$lesson = new Lessons(safeGet($_REQUEST['lesson_id']));
	if(safeGet($_GET['curdate']) != $lesson->date){
		$con = $lesson->con;
		$con_id = $lesson->con_id;
		$lesson_no = $lesson->lesson_no;
		$date = safeGet($_GET['curdate']);
		$lesson = Lessons::searchSession($con, $con_id, $date, $lesson_no);
	}	
	$editable = $lesson->is_editable();
	
	$name = $sms->getAnyNameById($lesson->con, $lesson->con_id);
	$service = new services($lesson->services);
	$material_name = $service->getName();
	
	echo $lesson->loadLayout();
}


