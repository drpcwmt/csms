<?php
if(isset($_GET['updateform'])){
		if(getPrvlg("resource_edit_principals")){
			$principal = new Principals(safeGet($_GET['itemid']));
			echo $principal->updateForm();
		} else {
			echo write_error($lang['no_privilege']);	
		}
	}
		// delete principal Levels
	elseif(isset($_GET['dellevel'])){
		$answer['error'] = '';
		if(getPrvlg("resource_edit_principals")){
			$id = $_POST['principal_id'];
			$level_id =  $_POST['level_id'];
			if(!do_query_edit("DELETE FROM principals WHERE id=$id AND levels=$level_id", $sms->database, $sms->ip)){
				$answer['error'] = 'Error';
			}
		} else {
			$answer['error'] = $lang['no_privilege'];
		}
		echo json_encode($answer);
	} else {
		echo Resources::loadItemsLayout($resource_type);
	}
?>