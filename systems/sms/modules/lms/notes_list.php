<?php
## services deltails homeworks
require_once('scripts/services_functions.php');

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
	$sql = "SELECT ".DB_year.".schedules_notes.*, ".DB_year.".schedules_lessons.services, ".DB_year.".schedules_lessons.id AS lesson_id , ".DB_year.".schedules_date.*
	FROM ".DB_year.".schedules_notes, ".DB_year.".schedules_lessons, ".DB_year.".schedules_date
	WHERE ".DB_year.".schedules_notes.lesson_id = ".DB_year.".schedules_lessons.id
	AND ".DB_year.".schedules_lessons.rec_id= ".DB_year.".schedules_date.id
	AND ".DB_year.".schedules_date.date<".time()."
	AND (".implode(' OR ', $where).") ";
	if(isset($_GET['service_id'])){
		$sql .= "AND ".DB_year.".schedules_lessons.services=$service_id ";
	}
	$sql .= "ORDER BY ".DB_year.".schedules_date.date DESC LIMIT 30";
	
} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
	$editable =check_user_lesson_privilege(false, $service_id);
	$sql = "SELECT ".DB_year.".schedules_notes.*, ".DB_year.".schedules_lessons.services, ".DB_year.".schedules_lessons.id AS lesson_id, ".DB_year.".schedules_date.*
	FROM ".DB_year.".schedules_notes, ".DB_year.".schedules_lessons, ".DB_year.".schedules_date
	WHERE ".DB_year.".schedules_notes.lesson_id = ".DB_year.".schedules_lessons.id
	AND ".DB_year.".schedules_lessons.rec_id= ".DB_year.".schedules_date.id
	AND ".DB_year.".schedules_date.con='class'
	AND ".DB_year.".schedules_date.con_id='".$_SESSION['cur_class']."'
	AND ".DB_year.".schedules_lessons.services=$service_id
	ORDER BY ".DB_year.".schedules_date.date DESC LIMIT 30";
}

$notes= do_query_resource($sql, DB_year);
$notes_list = '';
$trs = '';
while($hw = mysql_fetch_assoc($notes)){
	$service = getService($hw['services']);
	$trs .= write_html('tr', '',
		($editable ? 
			write_html('td', 'valign="top"', 
				write_html('button', 'onclick="openNote(\''.($hw['id']!= ''? 'lms' : 'write').'\','.$hw['id'].', '.$hw['lesson_id'].')"  class="ui-state-default hoverable"','<span class="ui-icon ui-icon-newwin"></span>')
			)
		: '').
		write_html('td', 'valign="top"', getMaterialNameById($service['material'])).
		write_html('td', 'valign="top"', unixToDate($hw['date'])).
		write_html('td', 'valign="top"', unixToDate($hw['content']))
	);
}
if($trs != ''){
	$notes_list = write_html('fieldset', 'class="ui-widget-content ui-corner-all"',
		write_html('legend', 'class="ui-corner-all ui-widget-header"', $lang['homework']).
		write_html('table', 'class="tablesorter"',
			write_html('thead', '',
				write_html('tr', '',
					($editable ? 
						write_html('th', 'width="26" style="background-image:none"','&nbsp;')
					: '').
					write_html('th', '', $lang['material']).
					write_html('th', '', $lang['date']).
					write_html('th', '', $lang['content'])
				)
			).
			write_html('tbody', '', $trs)
		)
	);
}
