<?php
## question banks
//require_once("scripts/services_functions.php");

$exer_opt = new stdClass();
if(isset($_REQUEST['service_id'])){
	$exer_opt->service_id = safeGet($_REQUEST['service_id']);
} 

if(isset($_REQUEST['book_id']) && $_REQUEST['book_id'] != ''){
	$exer_opt->book_id = safeGet($_REQUEST['book_id']);
}
if(isset($_REQUEST['chapter_id']) && $_REQUEST['chapter_id'] != ''){
	$exer_opt->chapter_id = safeGet($_REQUEST['chapter_id']);
}
if(isset($_REQUEST['summary_id']) && $_REQUEST['summary_id'] != ''){
	$exer_opt->summary_id = safeGet($_REQUEST['summary_id']);
}
if(isset($_REQUEST['exercise_id']) && $_REQUEST['exercise_id'] != ''){
	$exer_opt->exercise_id = safeGet($_REQUEST['exercise_id']);
}

$exercise = new Exercises($exer_opt);

if(isset($_GET['preview'])){
	//echo $data;
	echo $exercise->loadExercise(false);
} else {
	echo $exercise->loadEditExercise();
}

exit;

?>