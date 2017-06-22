<?php
## Summarys

if(isset($_GET['reload_attachs'])){
	$summary_id = safeGet($_GET['reload_attachs']);
	$lesson = do_query("SELECT books.service_id FROM books, chapters, summarys WHERE summarys.id=$summary_id AND summarys.chapter_id=chapters.id AND chapters.book_id=books.id", LMS_Database);
	$service_id = $lesson['service_id'];
	$editable = Services::check_user_service_privilege($service_id);
	echo Documents::loadAttachList('summary', $summary_id, $editable);
	exit;
}

/************* Posts ***********************/
if(isset($_GET['submitsummary'])){
	print json_encode(Summarys::_save($_POST)); 
	exit;
}

if(isset($_GET['deletesummary'])){
	if(isset($_POST['id']) && $_POST['id'] != ''){
		$sum_id =$_POST['id'];
		$answer = Summarys::_delete($sum_id);
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['error_updating'];
	}
	print json_encode($answer);
	exit;
}


/******************** Body ***********************/

if(isset($_GET['summary_id'])){ // Edit summary anywhere
	$summary_id = safeGet($_GET['summary_id']);
	$summary = new Summarys($summary_id);
	if(isset($_GET['write'])){
		if($summary->is_editable()){
			echo $summary->edit();
		} else {
			echo write_error($lang['no_privilege']);
		}
	} else {
		echo $summary->read();
	}
	exit;
} elseif(isset($_GET['new'])){
	$newLayout = new StdClass();
	if(isset($_GET['lesson_id'])){ // new summary from lesson
		$lesson_id = safeGet($_GET['lesson_id']);
		$newLayout->lesson_id_field = '<input type="hidden" name="lesson_id" value="'.$lesson_id.'"/>';	
		$lesson_ser = do_query("SELECT services FROM schedules_lessons WHERE id=$lesson_id", DB_year);
		$cur_service = $lesson_ser['services'];
		$cur_book = '';
		$cur_chapter = '';
	} elseif(isset($_GET['chapter_id'])){ // new summary from books
		$cur_chapter = safeGet($_GET['chapter_id']);
		$lms = do_query("SELECT books.*, chapters.* FROM books, chapters WHERE chapters.book_id=books.id AND chapters.id=$cur_chapter", LMS_Database);
		$cur_service = $lms['service_id'];
		$cur_book = $lms['book_id'];
	} elseif(isset($_GET['service_id'])) {
		$cur_service = safeGet($_GET['service_id']);
		$book = do_query("SELECT books.id AS book_id, chapters.id AS chapter_id FROM books, chapters WHERE books.service_id=$cur_service AND books.id=chapters.book_id ORDER BY books.id ASC LIMIT 1", LMS_Database);
		$cur_book = $book['book_id'];
		$cur_chapter = $book['chapter_id'];
	}

	$editable = Services::check_user_service_privilege($cur_service);
	
	$newLayout->service_id = $cur_service;
	$newLayout->book_id_options =write_select_options(getServiceBooks($cur_service), $cur_book);
	$newLayout->chapter_id_options =write_select_options(getBookChapers($cur_book), $cur_chapter);
	$newLayout->class_id = 'summary_form-new';
	echo fillTemplate("$thisTemplatePath/summary_edit.tpl", $newLayout); 
}
?>
