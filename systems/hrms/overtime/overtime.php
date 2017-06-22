<?php
if($prvlg->_chk('overtime_read') == false){die(write_error($lang['restrict_accses']));};

if(isset($_GET['overtimebyjob'])){
	$date = isset($_GET['date']) ? dateToUnix(safeGet('date')) : '';
	echo Overtime::loadDailyOvertime($date, safeGet('overtimebyjob'));
	
} elseif(isset($_GET['new_form'])){
	if($prvlg->_chk('overtime_edit')){
		echo Overtime::loadOvertimeForm(safeGet('job_id'));
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['delete'])){
	if($prvlg->_chk('overtime_edit')){
		echo json_encode(Overtime::deleteOvertime($_POST['id']));
	} else {
		print(json_encode(array('error'=>$lang['no_privilege'])));
	}
} elseif(isset($_GET['save'])){
	if($prvlg->_chk('overtime_edit')){
		if(Overtime::saveOvertime($_POST)){
			echo json_encode(array('error'=>''));
		} else {
			echo json_encode(array('error'=>$lang['error_updating']));
		}
	} else {
		print(json_encode(array('error'=>$lang['no_privilege'])));
	}
} else {
	echo Overtime::loadMainLayout();
}
?>
