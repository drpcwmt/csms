<?php
## services deltails homeworks
require_once('modules/services/services.class.php');

if(isset($_GET['service_id'])){
	$service_id = $_GET['service_id'];
}

if(in_array($_SESSION['group'], array('parent', 'student'))){
	$editable = false;
	$parents =  getParentsArr('student', $_SESSION['user_id']);
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
	AND (".implode(' OR ', $where).") ";
	if(isset($_GET['service_id'])){
		$sql .= "AND ".DB_year.".schedules_lessons.services=$service_id ";
	}
	$sql .= "ORDER BY ".LMS_Database.".homeworks.date DESC LIMIT 30";
} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
	
	$editable = Services::check_user_service_privilege( $service_id);
	$sql = "SELECT ".LMS_Database.".homeworks.*, ".DB_year.".schedules_lessons.services, ".DB_year.".schedules_lessons.id AS lesson_id 
	FROM ".LMS_Database.".homeworks, ".DB_year.".schedules_lessons, ".DB_year.".schedules_date
	WHERE ".LMS_Database.".homeworks.lesson_id = ".DB_year.".schedules_lessons.id
	AND ".DB_year.".schedules_lessons.rec_id= ".DB_year.".schedules_date.id
	AND ".DB_year.".schedules_date.con='class'
	AND ".DB_year.".schedules_date.con_id='".$_SESSION['cur_class']."'
	AND ".DB_year.".schedules_lessons.services=$service_id
	ORDER BY ".LMS_Database.".homeworks.date DESC LIMIT 30";
}

$homeworks= do_query_resource($sql, LMS_Database);
$homeworks_div = '';
$trs = '';
while($hw = mysql_fetch_assoc($homeworks)){
	$service = new Services($hw['services']);
	$trs .= write_html('tr', '',
		write_html('td', '', 
			write_html('button', 'onclick="editHomework(\''.($hw['exercise_id']!= ''? 'lms' : 'write').'\','.$hw['id'].', '.$hw['lesson_id'].')"  class="ui-state-default hoverable"','<span class="ui-icon ui-icon-newwin"></span>')
		).
		write_html('td', '', $service->getName()).
		write_html('td', '', unixToDate($hw['date'])).
		write_html('td', '', unixToDate($hw['answer_date']))
	);
}
if($trs != ''){
	$homeworks_div = write_html('fieldset', 'class="ui-widget-content ui-corner-all"',
		write_html('legend', 'class="ui-corner-all ui-widget-header"', $lang['homework']).
		write_html('table', 'class="tablesorter"',
			write_html('thead', '',
				write_html('tr', '',
					write_html('th', 'width="28" style="background-image:none"','&nbsp;').
					write_html('th', '', $lang['material']).
					write_html('th', '', $lang['date']).
					write_html('th', '', $lang['answer_date'])
				)
			).
			write_html('tbody', '', $trs)
		)
	);
}
