<?php
## lessons Note


if(isset($_GET['submitnote'])){
	$lesson = new Lessons($_POST['lesson_id']);
	if($lesson->is_editable()){
		$note_arr = array(
			'lesson_id' => $_POST['lesson_id'],
			'content' => $_POST['content'],
			'owner_id' => $_SESSION['user_id'],
			'shared' => isset($_POST['shared']) && $_POST['shared'] == 1 ? 1 : 0
		);
			
		if($_POST['id'] !=''){ // edit homework
			$note_arr['id'] = $_POST['id'];
			if(UpdateRowInTable("schedules_notes", $note_arr, "id=".$_POST['id'], DB_year)){
				$note_id = $_POST['id'];
				$result = true;
			}
		} else { // new homework
			if(insertToTable("schedules_notes", $note_arr, DB_year)){
				$note_id = mysql_insert_id();
				$result = true;
			}
		}
			
		$answer = array();
		if($result){
			$answer['id'] = $note_id;
			$note_arr['id'] = $note_id;
			$note_arr['shared_attr']  = ($note_arr['shared'] == '1') ? 'shared="1"' : '';
			$answer['html'] = fillTemplate("$thisTemplatePath/notes_list.tpl", $note_arr); 
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['no_privilege'];
	}
	print json_encode($answer);
	exit;
}

if(isset($_GET['deletenote'])){
	require_once('modules/lessons/lessons.class.php');
	$lesson = new Lessons(safeGet($_POST['lesson_id']));
	if($lesson->is_editable()){
		if(isset($_POST['id']) && $_POST['id'] != ''){
			$note_id =$_POST['id'];
			if(do_query_edit("DELETE FROM schedules_notes WHERE id=$note_id", DB_year)){
				$answer['id'] = $note_id;
				$answer['error'] = "";
			} else {
				$answer['id'] = "";
				$answer['error'] = $lang['error_updating'];
			}
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['no_privilege'];
	}
	print json_encode($answer);
	exit;
}

?>