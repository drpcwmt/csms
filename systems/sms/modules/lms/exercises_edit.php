<?php
## Exercises edit

$seek = false;
$cur_book = '';
$cur_chapter = '';
$exercise = new stdClass();
$exercise->chapter_id_options =write_select_options(array(), '');

if(isset($_GET['exercise_id'])){
	$exercise_id = safeGet($_GET['exercise_id']);
	$exercise = do_query_obj("SELECT * FROM exercise WHERE id=$exercise_id", LMS_Database);
	if($exercise->id != ''){
		$seek = true;
		$cur_book = $exercise->book_id;
		$cur_chapter = $exercise->chapter_id;
		$exercise->chapter_id_options =write_select_options(getBookChapers($cur_book), $cur_chapter);
	}
} else {
	$exercise_id = 'new';
	if(isset($_GET['service_id'])){
		$service_id = safeGet($_GET['service_id']);
	}
	
	if(!isset($exercise_id ) || !isset($service_id )){
		die("error undefined subject");
	}
}

$exercise_toolbox = array();
$exercise_toolbox[] = array(
	"tag" => "a",
	"attr"=> 'action="openQuestionBank" type="question" serviceid="'.$service_id.'"',
	"text"=> $lang['questions_bank'],
	"icon"=> "help"
);

$exercise->add_question_toolbox = createToolbox($exercise_toolbox);
$exercise->book_id_options =write_select_options(getServiceBooks($service_id), $cur_book);

$exercise_edit_html  = fillTemplate("$thisTemplatePath/exercises_edit.tpl", $exercise);
?>