<?php
if($prvlg->_chk('discounts_read') == false){
	die(write_error($lang['restrict_accses']));
};

if(isset($_GET['discountbyjob'])){
	$date = isset($_GET['date']) ? dateToUnix(safeGet('date')) : '';
	echo Discounts::loadDailyDiscounts($date, safeGet('discountbyjob'));
	
} elseif(isset($_GET['new_form'])){
	if($prvlg->_chk('discounts_add')){
		echo Discounts::loadDiscountForm();
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['delete'])){
	if($prvlg->_chk('discounts_remove')){
		echo json_encode(Discounts::deleteDiscount($_POST['id']));
	} else {
		print(json_encode(array('error'=>$lang['no_privilege'])));
	}
} elseif(isset($_GET['report'])){
	echo Discounts::loadDiscountsReport(safeGet('job_id'), safeGet('month'));
	
} elseif(isset($_GET['save'])){
	if($prvlg->_chk('discounts_add')){
		if(Discounts::saveDiscount($_POST)){
			echo json_encode(array('error'=>''));
		} else {
			echo json_encode(array('error'=>$lang['error_updating']));
		}
	} else {
		print(json_encode(array('error'=>$lang['no_privilege'])));
	}
} else {
	echo Discounts::loadMainLayout();
}

?>