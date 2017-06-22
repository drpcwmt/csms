<?php
## Books and Chapters

if(isset($_GET['newbook'])){
	setJsonHeader();
	$title = isset($_POST['title']) ? $_POST['title'] : $_POST['val'];
	$service_id = $_POST['service_id'];
	if(mysql_num_rows(do_query_resource("SELECT id FROM books WHERE title='$title' AND service_id='$service_id'", LMS_Database)) < 1){
		if(do_query_edit("INSERT INTO books (title, service_id) VALUES ( '$title', $service_id)" , LMS_Database)){
			$answer['id'] = mysql_insert_id();
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $sql;
		}
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['book_exists'];
	}
	print json_encode($answer);
	exit;
}

if(isset($_GET['editbook'])){
	setJsonHeader();
	$title = isset($_POST['title']) ? $_POST['title'] : $_POST['val'];
	$book_id = $_POST['book_id'];
	if(do_query_edit("UPDATE books SET title='$title' WHERE id=$book_id" , LMS_Database)){
		$answer['id'] = $book_id ;
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $sql;
	}
	print json_encode($answer);
	exit;
}

if(isset($_GET['deletebook'])){
	setJsonHeader();
	$book_id = $_POST['book_id'];
	if(do_query_edit("DELETE FROM books WHERE id=$book_id" , LMS_Database)){
		do_query_edit("DELETE FROM chapters WHERE book_id=$book_id", LMS_Database);
		do_query_edit("DELETE lessons_summary FROM lessons_summary, summarys WHERE summarys.id=lessons_summary.summary_id AND summarys.book_id=$book_id", LMS_Database);
		do_query_edit("DELETE summarys_attachs FROM summarys_attachs, summarys WHERE summarys.id=summarys_attachs.summary_id AND summarys.book_id=$book_id", LMS_Database);
		do_query_edit("DELETE FROM summarys WHERE book_id=$book_id", LMS_Database);
		$answer['id'] = $book_id ;
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $sql;
	}
	print json_encode($answer);
	exit;
}


if(isset($_GET['newchapter'])){
	setJsonHeader();
	$title = isset($_POST['title']) ? $_POST['title'] : $_POST['val'];
	$book_id = $_POST['book_id'];
	if(mysql_num_rows(do_query_resource("SELECT id FROM chapters WHERE title='$title' AND book_id='$book_id'", LMS_Database)) < 1){
		if(do_query_edit("INSERT INTO chapters (title, book_id) VALUES ( '$title', $book_id)" , LMS_Database)){
			$answer['id'] = mysql_insert_id();
			$answer['error'] = "";
			$answer['title'] = $title;
			$answer['book_id'] = $book_id;
		} else {
			$answer['id'] = "";
			$answer['error'] = $sql;
		}
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['chapter_exists'];
	}
	print json_encode($answer);
	exit;
}

if(isset($_GET['editchapter'])){
	setJsonHeader();
	$title = isset($_POST['title']) ? $_POST['title'] : $_POST['val'];
	$chapter_id = $_POST['chapter_id'];
	if(do_query_edit("UPDATE chapters SET title='$title' WHERE id=$chapter_id" , LMS_Database)){
		$answer['id'] = $chapter_id;
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $sql;
	}
	print json_encode($answer);
	exit;
}

if(isset($_GET['deletechapter'])){
	setJsonHeader();
	$chapter_id = $_POST['chapter_id'];
	if(do_query_edit("DELETE FROM chapters WHERE id=$chapter_id" , LMS_Database)){
		do_query_edit("DELETE lessons_summary FROM lessons_summary, summarys WHERE summarys.id=lessons_summary.summary_id AND summarys.chapter_id=$chapter_id", LMS_Database);
		do_query_edit("DELETE summarys_attachs FROM summarys_attachs, summarys WHERE summarys.id=summarys_attachs.summary_id AND summarys.chapter_id=$chapter_id", LMS_Database);
		$answer['id'] = $chapter_id;
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $sql;
	}
	print json_encode($answer);
	exit;
}

if(isset($_GET['reloadchapters']) && $_GET['book_id']!= 'undefined'){
	$book_id = safeGet($_GET['book_id']);
	echo write_select_options(getBookChapers($book_id), '', true);
//	echo json_encode(['error'=> '', "chapters"=>getBookChapers($book_id)]);
	exit;
}

?>
