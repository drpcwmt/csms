<?php

if(isset($_GET['convert'])){
	$answer['result']= NumberToMoney(safeGet($_GET['convert']) * Currency::convertRate(safeGet($_GET['from']), safeGet($_GET['to'])));
	$answer['error'] = '';
	echo json_encode($answer);
} elseif(isset($_GET['widget_data'])){
	ini_set('max_execution_time', 5);
	error_reporting(0);
	ini_set("display_errors", 0);
	$currency['error'] = '';
	$currency['usdegp'] = Currency::convertRate('USD', "EGP");
	$currency['euregp'] = Currency::convertRate('EUR', "EGP");
	$currency['last_sync'] = unixToDate(mktime(0,0,0, date('m'), date('d'), date('Y')));
	print json_encode($currency);
}
?>