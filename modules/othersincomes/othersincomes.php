<?php
 ## othersincomes

if(isset($_GET['open_income'])){
	$income = new OthersIncomes(safeGet('open_income'));
	echo $income->loadLayout();
} elseif(isset($_GET['newincome'])){
	if($prvlg->_chk('add_activity')){
		echo OthersIncomes::newIncomeForm(); 
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['saveincome'])){
	if($prvlg->_chk('edit_activity') || $prvlg->_chk('edit_activity_price')){
		echo OthersIncomes::_save($_POST); 
	} else {
		echo write_error($lang['no_privilege']);
	}
	
} elseif(isset($_GET['addmember'])){
	if($prvlg->_chk('add_activity_member')){
		if(isset($_GET['save'])){
			echo json_encode_result(OthersIncomes::saveMember($_POST));
		} else {
			echo OthersIncomes::newMember(safeGet('act_id'));	
		}
	} else {
		echo write_error($lang['no_privilege']);
	}
	
} elseif(isset($_GET['remove_member'])){
	if($prvlg->_chk('add_activity_member')){
		echo json_encode_result(OthersIncomes::removeMember($_POST));
	} else {
		echo json_encode_result(array('error'=>$lang['no_privilege']));
	}
} elseif(isset($_GET['reload_members'])){
	$activity = new OthersIncomes(safeGet('act_id'));
	echo $activity->getMembersTable();
	
} elseif(isset($_GET['newpay'])){
	if(isset($_GET['save'])){
		$nts = new I18N_Arabic('Numbers');
		$nts->setFeminine(1);
		echo OthersIncomes::savePay($_POST);
	} else {
		echo OthersIncomes::newPayForm(safeGet('act_id'), safeGet('std_id'), safeGet('cc_id'));	
	}
} elseif(isset($_GET['refundpay'])){
	if(isset($_GET['save'])){
		$nts = new I18N_Arabic('Numbers');
		$nts->setFeminine(1);
		echo OthersIncomes::refundPay($_POST);
	}
} elseif(isset($_GET['sync_activitys'])){
	echo json_encode_result(OthersIncomes::syncActivitys());
} else {
	echo OthersIncomes::loadMainLayout(); 
}
?>