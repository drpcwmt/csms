<?php
## Payments

if(isset($_GET['newpayment'])){
	$layout = array('date'=>unixToDate(time()));
	echo fillTemplate("modules/payments/templates/payments.tpl", $layout);
} elseif(isset($_GET['savepayment'])){
	$_POST['user'] = $_SESSION['user_id'];
	$result = do_insert_obj($_POST, 'payments', MySql_Database);
	$answer = array();
	if($result!==false){
		$answer['id'] = $result;
		$answer['error'] = "";
	} else {
		global $lang;
		$answer['id'] = "";
		$answer['error'] = $lang['error_updating'];
	}
	echo json_encode($answer);
}

?>