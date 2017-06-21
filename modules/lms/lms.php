<?php
## LMS main file
require_once('scripts/lms_functions.php');

$thisTemplatePath = "modules/lms/templates";

if(MSEXT_lms != true){
	die(write_error('extention_not_installed'));
}


// Autocomplete
if(isset($_GET['autocomplete'])){
	if($_GET['autocomplete'] == 'summary'){
		include('summary_autocomplete.php');
	}
	exit;
}
	
// Books
if(isset($_GET['books'])){
	require_once('modules/services/services.class.php');
	require_once('modules/lms/books.class.php');
	require_once('modules/lms/chapters.class.php');
	require_once('modules/lms/units.class.php');
	
	if(isset($_GET['list'])){
		$service_id = safeGet($_GET['service_id']);
		echo Books::bookListLayout($service_id);
	} elseif(isset($_GET['book'])){
		$book = new Books(safeGet($_GET['book_id']));
		echo $book->loadLayout(); 
	} elseif(isset($_GET['summary_list'])){
		$chapter = new Chapters(safeGet($_GET['chapter_id']));
		echo $chapter->getUnitsTable(); 
	}
	if(isset($_GET['books_list'])){
		include('books_list.php');
	} else {
		include('books.php');
	}
	exit;
}

// Summarys
if(isset($_GET['summary'])){
	if(isset($_GET['summary_list'])){
		include('summarys_list.php');
	} else {
		include('summarys.php');
	}
	exit;
}

// Homework
if(isset($_GET['homeworks'])){
	include('homework.php');
	exit;
}

if(isset($_GET['homeworks_list']) ){
	include('homeworks_list.php');
	echo $homeworks_div;	
	exit;
}

// Service details
if(isset($_GET['details'])){
	include('services_details.php');
	exit;
}

/*if(isset($_GET['books_list']) ){
	include('books_list.php');
	echo $books_html;
	exit;
}*/


if(isset($_GET['questions']) ){
	include('questions.php');
	//echo $books_div;	
	exit;
}

if(isset($_GET['exercises']) ){
	include('exercises.php');
	//echo $books_div;	
	exit;
}

if(isset($_GET['details']) && $_GET['details'] != ''){
	$file = $_GET['details'];
	include("services_details_$file.php");
	echo $out_div;
	exit;
}


?>