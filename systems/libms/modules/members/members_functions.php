<?php
## LibMS 
## students module functions

function getMemberNowBooks($type, $code){
	$query = do_query_resource("SELECT id FROM borrow WHERE con='$type' AND con_id='$code'
		AND return_date IS NULL", LIBMS_Database);
	return mysql_num_rows($query);
}

function getMemberTotalBorrows($type, $code){
	$query = do_query_resource("SELECT id FROM borrow WHERE con='$type' AND con_id='$code'", LIBMS_Database);
	return mysql_num_rows($query);
}

function getMemberBookLate($type, $code){
	$today = time();
	$query = do_query_resource("SELECT id FROM borrow WHERE con='$type' AND con_id='$code'
		AND max_date < $today
		AND return_date IS NULL", LIBMS_Database);
	return mysql_num_rows($query);
}

function getMemberBookLost($type, $code){
	$query = do_query_resource("SELECT id FROM borrow WHERE con='$type' AND con_id='$code'
		AND return_stat = -1
		AND return_date IS NOT NULL", LIBMS_Database);
	return mysql_num_rows($query);
}
?>
