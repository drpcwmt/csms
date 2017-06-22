<?php
##LibMS 
## Borrow function
function getPatronName($con, $con_id){
	$std = explode('-', $con_id);
	if($con == 'std'){
		$out = getStudentNameById($std[1], $std[0]);
	} elseif($con == 'emp' ) {
		$out = getEmployerNameById($std[1], $std[0]);
	} else {
		echo $con;
	}
//	echo $out;
	return $out;
}

function getBorrowInfo($book_id,  $serial){
	$borrow = do_query("SELECT * FROM borrow 
		WHERE book_id=$book_id
		AND serial=$serial
		AND return_date IS NULL
		ORDER BY borrow_date DESC
		LIMIT 1 ", LIBMS_Database);
	if($borrow['id'] != ''){
		$out = array();
		$out['patron_name'] = getPatronName($borrow['con'], $borrow['con_id']);
		$out['id'] = $borrow['id'];
		$out['book_id'] = $borrow['book_id'];
		$out['serial'] = $borrow['serial'];
		$out['isbn'] = getIsbnFromId($borrow['book_id']);
		$out['date'] = $borrow['borrow_date'];
		$out['con_id'] = $borrow['con_id'];
		$out['con'] = $borrow['con'];
		$out['stat'] = $borrow['stat'];
		$out['date_max'] = $borrow['max_date'];
		$out['return_date'] = $borrow['return_date'];
		return $out;
	} else {
		return false;
	}
}

function getBorrowInfoById($borrow_id){
	$borrow = do_query("SELECT * FROM borrow WHERE id=$borrow_id ", LIBMS_Database);
	if($borrow['id'] != ''){
		$out = array();
		$out['patron_name'] = getPatronName($borrow['con'], $borrow['con_id']);
		$out['id'] = $borrow['id'];
		$out['book_id'] = $borrow['book_id'];
		$out['serial'] = $borrow['serial'];
		$out['isbn'] = getIsbnFromId($borrow['book_id']);
		$out['date'] = $borrow['date'];
		$out['con_id'] = $borrow['con_id'];
		$out['con'] = $borrow['con'];
		$out['stat'] = $borrow['stat'];
		$out['date_max'] = $borrow['date_max'];
		return $out;
	} else {
		return false;
	}
}

function isAvaible($book_id,  $serial){
	$borrow = do_query_resource("SELECT id FROM borrow 
		WHERE book_id=$book_id
		AND serial=$serial
		AND return_date IS NULL", LIBMS_Database);
	if($borrow != false && mysql_num_rows($borrow) > 0){
		return false;
	} else {
		return true;
	}
}
?>