<?php
if($prvlg->_chk('permissionout_read') == false){die(write_error($lang['restrict_accses']));};

if(isset($_GET['permissionoutbyjob'])){
	$date = isset($_GET['date']) ? dateToUnix(safeGet('date')) : '';
	echo Permissionout::loadDailyPermissionout($date, safeGet('permissionoutbyjob'));

} elseif(isset($_GET['update'])){
	echo json_encode(Permissionout::updatePermis($_POST));
	
} elseif(isset($_GET['new_form'])){
	if($prvlg->_chk('permissionout_edit')){
		echo Permissionout::loadPermissionoutForm(safeGet('job_id'));
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['delete'])){
	if($prvlg->_chk('permissionout_edit')){
		echo json_encode(Permissionout::deletePermissionout($_POST['id']));
	} else {
		print(json_encode(array('error'=>$lang['no_privilege'])));
	}
} elseif(isset($_GET['save'])){
	if($prvlg->_chk('permissionout_edit')){
		if(Permissionout::savePermissionout($_POST)){
			echo json_encode(array('error'=>''));
		} else {
			echo json_encode(array('error'=>$lang['error_updating']));
		}
	} else {
		print(json_encode(array('error'=>$lang['no_privilege'])));
	}
} else {
	echo Permissionout::loadMainLayout();
}
?>
