<?php
require_once('books_functions.php');
$dialog_mode = isset($_GET['dialog']) ? true : false;


// add serial to book
if(isset($_POST['addserials'])){
	$book_id = $_POST['book_id'];
	$count = $_POST['count'];
	$curent = do_query("SELECT * FROM book_serials WHERE book_id=$book_id ORDER BY serial DESC LIMIT 1", LIBMS_Database);
	$next = $curent['serial'] +1 ;
	$data= array();
	$today = dateToUnix(date('d').'/'. date('m').'/'. date('Y'));
	for($i = 0; $i<$count; $i++){
		$serial = $next+$i;
		$data[] = "($book_id, $serial, 5, $today)";
	}
	if(!do_query_edit("INSERT INTO book_serials (book_id, serial, stat, purshase_date) VALUES ".implode(', ', $data), LIBMS_Database)){
		$error = "cant update database";
	}
	setJsonHeader();
	
	if( !isset($error)){
		$answer['id'] = $book_id;
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $error;
	}
	print json_encode($answer);
	exit;
}

// Book serials
if(isset($_GET['serialtable'])){
	$book_id = $_GET['book_id'];
	require_once('scripts/libms_functions.php');
	require_once('modules/borrow/borrow_functions.php');
	require_once('books_serials.php');
	echo ( !$dialog_mode ? $serial_toolbox : '').$book_serial;
	exit;
}

if( isset($_GET['newbook']) ){
	require_once('books_details.php');
	echo write_html('div', 'class="ui-corner-top ui-widget-header reverse_align"',
		write_html('h3', 'class="title_wihte"',  $lang['new_book'])
	).
	write_html('div', 'class="ui-corner-bottom ui-widget-content module_content" style="padding:5px"',
		write_html('form', 'id="book_detail_form"',
			$book_toolbox.
			$book_details
		)
	);
	exit;
}
// get = new, id? isbn
if(isset($_GET['book_id'])|| isset($_GET['isbn']) ){
	require_once('scripts/libms_functions.php');
	require_once('modules/borrow/borrow_functions.php');
	require_once('books_details.php');
	require_once('books_serials.php');
	echo write_html('div', 'class="tabs"',
		write_html('ul', '', 
			write_html('li', '', 
				write_html('a', 'href="#book_details_div"', $lang['details'])
			).
			($seek ? 
				write_html('li', '', 
					write_html('a', 'href="#book_serials_div"', $lang['serials'])
				)
			: '')
		).
		write_html('div', 'id="book_details_div"',
			write_html('form', 'id="book_detail_form"',
				( !$dialog_mode ? $book_toolbox : '').
				$book_details
			)
		).
		($seek ? 
			write_html('div', 'id="book_serials_div"',
				( !$dialog_mode ? $serial_toolbox : '').
				$book_serial
			)
		: '')
	);
	exit;
}

// response with book info in json format		
if(isset($_POST['book_info'])){
	if(isset($_POST['isbn']) && $_POST['isbn'] != ''){
		$sql = "SELECT books.*, cats.def_borrow_limit FROM books, cats WHERE books.cat=cats.id AND books.isbn ='".$_POST['isbn']."'";
	} elseif(isset($_POST['book_id']) && $_POST['book_id'] != ''){
		$sql = "SELECT books.*, cats.def_borrow_limit FROM books, cats WHERE books.cat=cats.id AND books.id ='".$_POST['book_id']."'";
	} 

	$book = do_query($sql, LIBMS_Database);
	$cat = getCatFromBook($book['id']);
	if($book['id'] != ''){
		if($book['def_borrow_limit'] == '0' || $book['def_borrow_limit'] == ''){
			$book['def_borrow_limit'] = $MS_settings['def_borrow_limit'];
		}
		$book['cat_name'] = $cat['cat'];
		$book['cat_sub_name'] = $cat['cat_sub'];
		$out =$book;
		$out['error'] = '';
	} else {
		$out = array('error' => $lang['cant_find_book']);
	}
	setJsonHeader();
	print json_encode($out);
	exit;
}


// edit books
if(isset($_POST['id'] )){
	$fields = getTableFields('books');
	$affect_data_value=array();
	$affect_data_fields=array();
	$rel_table = array('cats', 'cats_sub', 'author', 'vendor');
	$value = $_POST;
	if($value['cat'] == ''){
		$cat = do_query("SELECT id FROM cats WHERE name='".$value['cat_name']."'");
		if($cat['id'] != ''){
			$value['cat'] = $cat['id'];
		} else {
			do_query_edit("INSERT INTO cats (name) VALUES ('".$value['cat_name']."')");
			$value['cat'] =  mysql_insert_id();
		}
	}
	
		
	if($value['cat_sub'] == ''){
		$sub = do_query("SELECT id FROM cats_sub WHERE name='".$value['cat_sub_name']."'");
		if($sub['id'] != ''){
			$value['cat_sub'] = $sub['id'];
		} else {
			do_query_edit("INSERT INTO cats_sub (name, cat_id, code) VALUES ('".$value['cat_sub_name']."', '".$value['cat']."', '".$value['cat_code']."')");
			$value['cat_sub'] =  mysql_insert_id();
		}
	} else {
		if(isset($_POST['sub_code'])){
			do_query_edit("UPDATE cats_sub SET code=".$_POST['sub_code']." WHERE id=".$value['cat_sub'], LIBMS_Database);
		}
	}
	
	if($value['author'] == ''){
		$author = do_query("SELECT id FROM author WHERE name='".$value['author_name']."'");
		if($author['id'] != ''){
			$value['author'] = $author['id'];
		} else {
			do_query_edit("INSERT INTO author (name) VALUES ('".$value['author_name']."')");
			$value['author'] =  mysql_insert_id();
		}
	}
	if($value['vendor'] == ''){
		$vendor = do_query("SELECT id FROM vendor WHERE name='".$value['vendor_name']."'");
		if($vendor['id'] != ''){
			$value['vendor'] = $vendor['id'];
		} else {
			do_query_edit("INSERT INTO vendor (name) VALUES ('".$value['vendor_name']."')");
			$value['vendor'] =  mysql_insert_id();
		}
	}

	setJsonHeader();
	if($_POST['id'] != ''){
		$book_id= $_POST['id'];
		if(UpdateRowInTable('books', $value, "id='$book_id'")){
			echo "{\"error\" : \"\", \"id\" : \"$book_id\"}";
		} else {
			echo "{\"error\" : \"Error while updating\"}";
		}
	} else {
		if($book_id = insertToTable('books', $value)){
			// Serials
			$counts = $value['count'] > 1 ? $value['count'] : 1 ;
			$today = dateToUnix(date('d').'/'. date('m').'/'. date('Y'));
			for($i=1; $i<=$counts; $i++){
				do_query_edit("INSERT INTO book_serials (book_id, serial, stat, purshase_date) VALUES ($book_id, $i, 5, $today)", LIBMS_Database);
			}
			echo "{\"error\" : \"\", \"id\" : \"$book_id\"}";
		} else {
			echo "{\"error\" : \"Error while inserting\"}";
		}
	}

	exit;
}

/*********************** CATEGORYS ***************************/
if(isset($_POST['codefromsub'])){
	if($sub_code = getCodeFromSubId($_POST['codefromsub'])){
		echo "{\"error\" : \"\", \"code\" : \"$sub_code\"}";
	} else {
		echo "{\"error\" : \"".$lang['not_found']."\"}";
	}
	exit;
}

if(isset($_GET['categorys']) || isset($_GET['newinsertedcat'])){
	require_once('categorys.php');
	echo $categorys_html;
	exit;
}

if(isset($_GET['catsubs'])){
	$cat_id = $_GET['cat_id'];
	echo write_html_select('id="sub_cat" class="combobox" title="'.$lang['sub_cat'].'" name="cat_sub"', createCatsSubsList($cat_id), '');
	exit;
}
	

?>
