<?php
## LIBMS 
## book functions
// functions
function createCatsList(){
	$name = 'name';
	$cats = do_query_resource("SELECT * FROM cats ORDER BY $name ASC", LIBMS_Database);
	$cats_arr = array();
	if(mysql_num_rows($cats) > 0) {
		while($cat = mysql_fetch_assoc($cats)){
			$cats_arr[$cat['id']] = $cat[$name];	
		}
		return $cats_arr;
	} else {
		return false;
	}
}

function createCatsSubsList($cat_id){
	if($cat_id == '' || $cat_id == false ){
		$cats = createCatsList();
		if($cats == false){
			return false;
		} else {
			foreach($cats as $key => $value){
				$cat_id = $key;
				break;
			}
		}
		
	} 
	if($cat_id == '' || $cat_id == false ){
		return false;
	} else {
		$name = 'name';
		$subs = do_query_resource("SELECT * FROM cats_sub WHERE cat_id=$cat_id ORDER BY $name ASC", LIBMS_Database);
		$subs_arr = array();
		if(mysql_num_rows($subs) > 0) {
			while($sub = mysql_fetch_assoc($subs)){
				$subs_arr[$sub['id']] = $sub[$name];	
			}
			return $subs_arr;
		} else {
			return false;
		}
	}
}

function getCategoryNameById($cat_id){
	if($cat_id != '' || $cat_id != false){
		$cat = do_query("SELECT name FROM cats WHERE id=$cat_id", LIBMS_Database);
		if($cat != false && $cat['name'] != ''){
			return $cat['name'];
		} else {
			return false;
		}
	}  else {
		return false;
	}
}

function getTotalBooks($field, $field_id){
	$books = do_query_resource(
	"SELECT book_serials.serial
	FROM books, book_serials 
	WHERE books.id = book_serials.book_id
	AND books.$field=$field_id", LIBMS_Database);
	return mysql_num_rows($books);
}

function getLostBooks($field, $field_id){
	$books = do_query_resource(
	"SELECT book_serials.serial 
	FROM books, book_serials, borrow
	WHERE books.id = book_serials.book_id
	AND books.$field=$field_id
	AND borrow.book_id=books.id
	AND book_serials.serial=borrow.serial
	AND borrow.return_stat=-1", LIBMS_Database);
	return mysql_num_rows($books);
}

function getBadBooks($field, $field_id){
	$books = do_query_resource(
	"SELECT book_serials.serial 
	FROM books, book_serials 
	WHERE books.id = book_serials.book_id
	AND books.$field=$field_id
	AND book_serials.stat<2", LIBMS_Database); 
	return mysql_num_rows($books);
}

function getOutBooks($field, $field_id){
	$books = do_query_resource(
	"SELECT book_serials.serial 
	FROM books, book_serials, borrow
	WHERE books.id = book_serials.book_id
	AND books.$field=$field_id
	AND borrow.book_id=books.id
	AND book_serials.serial=borrow.serial
	AND borrow.return_stat IS NULL", LIBMS_Database);
	return mysql_num_rows($books);
}

?>