<?php
## questions Bank

$bank_opts =new stdClass();
if(isset($_REQUEST['service_id'])){
	$bank_opts->service_id = safeGet($_REQUEST['service_id']);
} 
if(isset($_REQUEST['type'])){
	$bank_opts->type = safeGet($_REQUEST['type']);
} 

if(isset($_REQUEST['book_id'])){
	$bank_opts->book_id = safeGet($_REQUEST['book_id']);
}
if(isset($_REQUEST['chapter_id'])){
	$bank_opts->chapter_id = safeGet($_REQUEST['chapter_id']);
}
if(isset($_REQUEST['summary_id'])){
	$bank_opts->summary_id = safeGet($_REQUEST['summary_id']);
}

$questionBank = new questionBank($bank_opts);

	// display question
if(isset($_GET['question_id']) && safeGet($_GET['question_id']) != "new"){
	$question_id = safeGet($_GET['question_id']);
	echo $questionBank->loadQuestion($question_id);
	// dispaly new question 
} elseif(isset($_GET['question_id']) && safeGet($_GET['question_id']) == "new"){
	echo $questionBank->loadQuestion();
	// Submit question
} elseif(isset($_GET['submit_question'])){
	echo $questionBank->submitQuestion($_POST, $bank_opts->type);
	// Delete question
} elseif(isset($_GET['delete_question'])){
	echo $questionBank->deleteQuestion($_POST['id'], $bank_opts->type);
	// load search results
} elseif(isset($_GET['search'])){
	echo $questionBank->loadSearchResults();
	// load main interface
} else {
	echo $questionBank->loadSearchView();
}
?>