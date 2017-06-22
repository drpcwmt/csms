<?php
## golobals
## sortable backend
## PARM : itemOrder
## Set order in the main database for especial table

if(isset($_GET['sort'])){
	$error = '';
	$itemOrder = $_POST['itemOrder'];
	$items  = $_POST['items'];
	$chk = do_query("SELECT * FROM items_order WHERE items='$items'", MySql_Database);
	if($chk['items'] != ''){
		if(!do_query_edit("UPDATE items_order SET `order`='$itemOrder' WHERE `items`='$items'", MySql_Database)){
			$error = $lang['error_updating'];
		}
	} else {
		if(!do_query_edit("INSERT INTO items_order (`items`, `order`) VALUES ('$items', '$itemOrder')", MySql_Database)){
			$error = $lang['error_updating'];
		}
	}
	setJsonHeader();
	echo "{\"error\" : \"$error\", \"item\" : \"$items\"}";
	exit;
}
?>