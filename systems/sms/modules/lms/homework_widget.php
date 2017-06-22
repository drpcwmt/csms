<?php
## homework Widget

$out = '';
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
$sql = "SELECT ".LMS_Database.".homeworks.*, ".DB_year.".schedules_lessons.services, ".DB_year.".schedules_lessons.id AS lesson_id 
FROM ".LMS_Database.".homeworks, ".DB_year.".schedules_lessons, ".DB_year.".schedules_date
WHERE ".LMS_Database.".homeworks.lesson_id = ".DB_year.".schedules_lessons.id
AND ".DB_year.".schedules_lessons.rec_id= ".DB_year.".schedules_date.id
AND ".DB_year.".schedules_date.date<".time()."
AND ".DB_year.".schedules_date.date>".(time()- 1296000)."
AND (".implode(' OR ', $where).") 
ORDER BY ".LMS_Database.".homeworks.date DESC";

$homeworks= do_query_array($sql, LMS_Database);
if(count($homeworks) > 0){
	$lessons_homeworks_list = '';
	foreach ($homeworks as $homework) {
		$lessons_homeworks_list .= fillTemplate("modules/lms/templates/homeworks_list.tpl", $homework);
	}
	$out .=write_html('table', 'width="100%" class="result fixed homework_list"', $lessons_homeworks_list);
} else {
	$out .= $lang['no_homework_found'];
}
$widget = write_html('fieldset', '', 
	write_html('legend', '', $lang['homeworks']).
	$out
);
?>