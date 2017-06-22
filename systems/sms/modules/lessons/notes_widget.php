<?php
/***************************** Home *****************************************/
$list = '';
$where = array();
if(in_array($_SESSION['group'], array('student', 'parent'))){
	$where[] = "(".DB_year.".schedules_date.con='student' AND ".DB_year.".schedules_date.con_id=".$_SESSION['user_id'].")";
	$std_id = $_SESSION['std_id'];
	$parents =  getParentsArr('student', $std_id);
} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
	$class_id = $_SESSION['cur_class'];
	$parents =  getParentsArr('class', $class_id);
}

foreach($parents as $array){
	$con =$array[0];
	$con_id= $array[1];
	$where[] = "(".DB_year.".schedules_date.con='$con' AND ".DB_year.".schedules_date.con_id=$con_id)";
}
$notes = do_query_array("SELECT schedules_lessons.*, schedules_notes.* , schedules_date.date
	FROM schedules_notes, schedules_lessons , schedules_date
	WHERE schedules_notes.lesson_id=schedules_lessons.id
	AND schedules_lessons.rec_id=schedules_date.id
	AND schedules_date.date<".time()."
	AND schedules_date.date>".(time()- 1296000)."
	AND (".implode(' OR ', $where).") 
	ORDER BY schedules_date.date DESC
	LIMIT 20", DB_year);
	if(count($notes) > 0){
		$home_notes_list = '';
		$notes_trs = array();
		foreach ($notes as $note) {
			$note->shared_attr  = ($note->shared == '1') ? 'shared="1"' : '';
			$home_notes_list .= fillTemplate("modules/lessons/templates/notes_list.tpl", $note);
		}
		$list = $home_notes_list;
	}  else{
		$list = $lang['no_notes'];
	}

$widget = write_html('fieldset', '', 
	write_html('legend', '', $lang['notes']).
	write_html('table', 'width="100%" class="result fixed notes_list"', $list)
);
?>