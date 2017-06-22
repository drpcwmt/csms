<?php
if($prvlg->_chk('bonus_read') == false){die(write_error($lang['restrict_accses']));};

if(isset($_GET['bonusbyjob'])){
	$date = isset($_GET['date']) ? dateToUnix(safeGet('date')) : '';
	echo Bonus::loadDailyBonus($date, safeGet('bonusbyjob'));
	
} elseif(isset($_GET['new_form'])){
	if($prvlg->_chk('bonus_edit')){
		echo Bonus::loadBonusForm();
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['delete'])){
	if($prvlg->_chk('bonus_edit')){
		echo json_encode(Bonus::deleteBonus($_POST['id']));
	} else {
		print(json_encode(array('error'=>$lang['no_privilege'])));
	}
} elseif(isset($_GET['save'])){
	if($prvlg->_chk('bonus_edit')){
		if(Bonus::saveBonus($_POST)){
			echo json_encode(array('error'=>''));
		} else {
			echo json_encode(array('error'=>$lang['error_updating']));
		}
	} else {
		print(json_encode(array('error'=>$lang['no_privilege'])));
	}
} else {
	echo Bonus::loadMainLayout();
}
?>
