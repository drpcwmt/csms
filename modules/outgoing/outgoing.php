<?php
## Outgoing
if(isset($_GET['sms_id'])){
	$sms = new SMS(safeGet($_GET['sms_id']));

	if($sms->getSettings('ig_mode') == '1'){
		include_once('config/ig_config.php');
	}

}


if(isset($_GET['deposit'])){
	if(isset($_GET['list'])){
		$dates = array();
		$dates['begin_date'] = isset($_GET['begin_date']) ? dateToUnix($_GET['begin_date']) : getYearSetting('begin_date');
		$dates['end_date'] = isset($_GET['end_date']) ?dateToUnix($_GET['end_date']) : getYearSetting('end_date');
		echo Outgoing::getList($income_code, $dates, isset($_GET['begin_date']));
	} elseif(isset($_GET['save'])){
		$post = $_POST;
		$post['date'] = isset($post['date']) ? dateToUnix($post['date']) : time();
		$nts = new I18N_Arabic('Numbers');
		$nts->setFeminine(1);
		//$post['ccid'] = '0';
		$post['type'] = 'cash';
		$post['ccid'] = '0';
		$outgoing = Outgoing::addNewPayment($post);
		if($outgoing != false){
			$recete = $outgoing->getRecete();
			echo json_encode(array('error'=>'', 'recete' => $recete));
		} else {
			echo json_encode_result(false);
		}
	} else {
		echo Outgoing::loadDepositForm();	
	}
} elseif(isset($_GET['others'])){
	if(isset($_GET['autocomplete'])){
		print Outgoing::OthersAutocomplete($_GET['term']);
	} elseif(isset($_GET['save'])){
		$nts = new I18N_Arabic('Numbers');
		$nts->setFeminine(1);
		$post = $_POST;
		$post['date'] = isset($post['date']) ? dateToUnix($post['date']) : time();
		$outgoing = Outgoing::addNewPayment($post);
		if($outgoing != false){
			$recete = $outgoing->getRecete();
			echo json_encode(array('error'=>'', 'recete' => $recete));
		} else {
			echo json_encode_result(false);
		}
	} else {
		echo Outgoing::othersLayout();
	}
	// refund
} elseif(isset($_GET['refund'])){
	$refund = new RefundSchoolFees($sms);
	if(isset($_GET['save'])){
		$nts = new I18N_Arabic('Numbers');
		$answer = array();
		$outgoing= $refund->doRefundItem($_POST);
		if($outgoing !== false){
			$answer['recete'] = $outgoing->getRecete();
			$answer['error'] = '';
		} else {
			$answer['error'] = 'Error';
		}
		echo json_encode($answer);

	} elseif(isset($_GET['refund_table'])){
		echo $refund->loadRefund(safeGet('std_id'), safeGet('year'));	

	} else {
		echo $refund->loadMainLayout();
	}
	
} elseif(isset($_GET['refund_service'])){
	$refund = new RefundSchoolFees($sms);
	if(isset($_GET['save'])){
		$nts = new I18N_Arabic('Numbers');
		echo json_encode_result($refund->doRefundService($_POST));
	}
	
} elseif(isset($_GET['print_recete'])){
	$nts = new I18N_Arabic('Numbers');
	$outgoing = new Outgoing(safeGet('print_recete'));
	echo $outgoing->getRecete();

} else {
	echo Outgoing::loadMainLayout();
}
?>
