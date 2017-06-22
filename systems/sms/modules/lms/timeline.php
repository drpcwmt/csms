<?php
## Time line
require_once('scripts/mysql_pdo.php');
require_once('modules/employers/employers.class.php');
require_once("scripts/templates.class.php");

if(in_array($_SESSION['group'], array('student', 'parent'))){
	$cons = getParentsArr('student', $_SESSION['std_id']);
} else{
	$cons = getChildsArr('class', $_SESSION['cur_class']);
	$cons[] = array('class', $_SESSION['cur_class']);
}

$service_id = safeGet($_GET['service_id']);
$limitMax = 4;
$limitBegin = isset($_GET['cur']) && $_GET['cur']!=0 ? safeGet($_GET['cur']): 0;
$db_student = DB_student;
$db_year = DB_year;
$db_lms = LMS_Database;
$fieldName = 'name_'.$_SESSION['dirc'];

$tables = array("$db_year.schedules_date", "$db_year.schedules_lessons", "$db_year.services", "$db_student.materials");
$select = array("$db_year.schedules_date.*", "$db_year.schedules_lessons.*", "$db_student.materials.$fieldName AS service_name");
$where = array(
	"$db_year.schedules_date.date>7",
	"$db_year.schedules_lessons.rec_id=schedules_date.id",
	"$db_year.schedules_lessons.services=$service_id",
	"$db_year.services.id=$db_year.schedules_lessons.services",
	"$db_student.materials.id= $db_year.services.mat_id"
);

$cons_str= array();
foreach($cons as $array){
	$con =$array[0];
	$con_id= $array[1];
	$cons_str[] = "($db_year.schedules_date.con='$con' AND $db_year.schedules_date.con_id='$con_id')";
}
$where[] = "(".implode(' OR ', $cons_str) .' )';

$order = "$db_year.schedules_date.date DESC";
$limit = "$limitBegin, $limitMax";

	// Filter by book
if(isset($_GET['book_id'])) {
	$select[] = "$db_lms.summarys.*";
	$tables[] = "$db_lms.lessons_summary";
	$tables[] = "$db_lms.summarys";
	$where[] = "$db_lms.summarys.book_id=".safeGet($_GET['book_id']);
	$where[] = "$db_year.schedules_lessons.id=$db_lms.lessons_summary.lesson_id";
	$where[] = "$db_lms.lessons_summary.summary_id=$db_lms.summarys.id";
}

$sql = createQuery($select, $tables, $where, $order, $limit);

$lessons = do_query_array($sql, $db_year);

if(count($lessons) > 0){
	foreach($lessons as $lesson){	
		// begin lesson page
		$lesson_id = $lesson->id;
		$thumb = new Template("$thisTemplatePath/lesson_thumb.tpl");
		foreach ($lesson as $key => $value) {
			$thumb->set($key, $value);
		}
		$prof = new Employers($lesson->prof);
		$thumb->set('prof_name', $prof->getName());
		$thumb->set('date', unixToDate($lesson->date));
		$thumb->set('con_name', getAnyNameById($lesson->con, $lesson->con_id));
	
			// Summarys
		$summarys_layout = '';
		$summarys_query = createQuery(
			array("summarys.id", "summarys.title", "books.title AS book_name", "chapters.title AS chapter_name"), 
			array('lessons_summary', 'summarys', "books", "chapters"), 
			array(
				'lessons_summary.summary_id=summarys.id', 
				"lessons_summary.lesson_id=$lesson_id",
				"summarys.book_id=books.id",
				"summarys.chapter_id=chapters.id"
			)
		);
		$summarys= do_query_array($summarys_query, $db_lms);
		if(count($summarys) > 0){
			$lessons_summarys_list = '';
			foreach ($summarys as $summary) {
				$lessons_summarys_list .= fillTemplate("$thisTemplatePath/summary_list.tpl", $summary);
			}
			$summarys_layout = write_html('fieldset', 'class="summary_list"',
				write_html('legend', '', $lang['summary']).
				write_html('table', 'width="100%" class="result fixed"', $lessons_summarys_list)
			);
		} 
		$thumb->set('summarys_fieldset', $summarys_layout);
	
			// Homeworks
		$homeworks_layout = '';
		$homeworks_query = createQuery(
			array("homeworks.*"), 
			array('homeworks'), 
			array("homeworks.lesson_id=$lesson_id")
		);
		$homeworks= do_query_array($homeworks_query, $db_lms);
		if(count($homeworks) > 0){
			$lessons_homeworks_list = '';
			foreach ($homeworks as $homework) {
				$lessons_homeworks_list .= fillTemplate("$thisTemplatePath/homeworks_list.tpl", $homework);
			}
			$homeworks_layout = write_html('fieldset', 'class="homework_list"',
				write_html('legend', '', $lang['homeworks']).
				write_html('table', 'width="100%" class="result fixed"', $lessons_homeworks_list)
			);
		} 
		$thumb->set('homeworks_fieldset', $homeworks_layout);
	
			// Notes
		$notes_layout = '';
		$notes_query = createQuery(
			array("schedules_notes.*"), 
			array('schedules_notes'), 
			array("schedules_notes.lesson_id=$lesson_id")
		);
		$notes= do_query_array($notes_query, $db_year);
		if(count($notes) > 0){
			$notes_trs = array();
			foreach ($notes as $note) {
				$lessons_notes_list = new Template("$thisTemplatePath/lessons_notes_list.tpl");
				$lessons_notes_list->set('content', $note->content);
				$lessons_notes_list->set('id', $note->id);
				if($note->shared == 1){
					$lessons_notes_list->set('note_shared_con', write_icon('transferthick-e-w', $lang['shared']));
				}
				$notes_trs[] = $lessons_notes_list;
			}
			$notes_layout = write_html('fieldset', '',
				write_html('legend', '', $lang['notes']).
				write_html('table', 'width="100%" class="result fixed notes_list"',  Template::merge($notes_trs))
			);
		} 
		$thumb->set('notes_fieldset', $notes_layout);
	
		// Create the lesson thumb
		$thumbsTemplates[] = $thumb;
	}

	$sessions = Template::merge($thumbsTemplates);
	if(count($lessons) < $limitMax){
		$sessions .= write_html('div', 'class="timeline_end scroll-content-item faded ui-widget-content ui-corner-all"', 
			write_html('h1', '', $lang['end'])
		);
	}
} else {
	if(isset($_GET['cur'])){
		$sessions = write_html('div', 'class="timeline_end scroll-content-item faded ui-widget-content ui-corner-all"', 
			write_html('h1', '', $lang['end'])
		);
	} else {
		$sessions = write_html('div', 'class="timeline_end scroll-content-item faded ui-widget-content ui-corner-all"',
			write_html('h1', '', $lang['no_lesson'])
		);
	}
}

	//send  update ajax lesson
if(isset($_GET['cur'])){
	echo $sessions;
	exit;
}

$timeline['service_id'] = $service_id;
$timeline['sessions'] = $sessions;
echo fillTemplate("$thisTemplatePath/timeline.tpl", $timeline); 
echo write_script("iniTimeline($service_id)");
//print_r($lessons);
//
//$now = (time() - $begin);
//echo 'FINISH in :'. $now.'<br>';


?>