<?php
## student Activity


if(isset($_GET['sms_id']) && $this_system->type!='sms'){
	 $sms = new SMS(safeGet($_GET['sms_id']));
}

// New student Form
if(isset($_POST['step'])){
	if(getPrvlg('std_add')){
		include('students_new.php');
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['cand_to_id'])){
	$cand_no = $_POST['cand_no'];
	$std = do_query_obj("SELECT id FROM student_data WHERE cand_no=$cand_no", $sms->database, $sms->ip);
	if(isset($std->id)){
		echo json_encode_result(array('id'=>$std->id, 'error'=>''));
	} else {
		echo json_encode_result(array('error'=>$lang['student_not_found']));
	}
} elseif(isset($_GET['history'])){
	if(getPrvlg('std_read')){
		$std_id = safeGet($_GET['std_id']);
		require_once('history.class.php');
		$history = new History($std_id);
		echo $history->loadFinalResultsTable();			
	
	} else {
		echo write_error($lang['no_previlege']);
	}
} elseif(isset($_GET['student_autocomplete'])){
	$value = trim($_GET['term']);
	$stats = isset($_GET['w']) ? explode(',', safeGet('w')) : array();
	print Students::getAutocompleteStudent( $value, $stats);

} elseif(isset($_GET['bus_card'])){
	$student = new Students($_GET['std_id'], $sms);
	echo $student->getBusCard();

} elseif(isset($_GET['std_id'])){
	if(getPrvlg('std_read')){
		$student = new Students(safeGet($_GET['std_id']));
		if(in_array($student->status, array('1', '3'))){
			$all_classes = Classes::getList();
		//	if(in_array($student->getClass(), $all_classes)){
				echo $student->loadMainLayout();
			/*} else {
				echo write_error($lang['no_std_privilege']);
			}*/
		} else {
			echo $student->loadMainLayout();
		}
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['save_student'])){
	if( ($_POST['std_id'] != '' && getPrvlg('std_edit')) ||  ($_POST['std_id'] == '' && getPrvlg('std_add')) ){
		echo students::saveStudent($_POST);
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['suspension'])){
	if(getPrvlg('std_edit')){
		if(isset($_GET['save'])){
			echo Students::saveSuspension($_POST);
		} else {
			echo Students::suspension(safeGet($_GET['id']));
		}
	} else {
		echo write_error($lang['no_previlege']);
	}

} elseif(isset($_GET['inscription'])){
	if(getPrvlg('std_edit')){
		if(isset($_POST['id']) &&  isset($_POST['join_date']) && $_POST['id'] != ''){
			echo Students::saveInscription($_POST);
		} else {
			echo Students::inscription(safeGet($_GET['id']));
		}
	} else {
		echo write_error($lang['no_previlege']);
	}
	// stiudent Search engin
} elseif(isset($_GET['listform'])){
	$engine = new StudentsList('', '');
	echo $engine->createWizard();
	
} elseif(isset($_POST['savereq'])){
	$answer = StudentsList::saveQuery($_POST);
	print json_encode($answer);
	
} elseif( isset($_GET['saved_req'])){
	$engine = new StudentsList('', '');
	echo $engine->getSavedRequest();

} elseif( isset($_POST['del_req'])){
	$engine = new StudentsList('', '');
	print json_encode($engine->deleteQuery($_POST['del_req']));

} elseif( isset($_GET['req'])){
	$engine = new StudentsList('', '');
	echo $engine->loadMainLayout($engine->loadQuery(safeGet('req')));

} elseif(isset($_POST['fields']) || isset($_POST['selected_fields'])){
	$engine = new StudentsList('', '');
	echo $engine->loadMainLayout($engine->getQueryFromPost($_POST));

} elseif(isset($_GET['stdfp'])){
	include('students_from_parents.php');

} elseif(isset($_GET['del_result'])){
	$std_id = $_POST['std_id'];
	if(do_query_edit("DELETE FROM final_result WHERE std_id=$std_id", DB_year)){
		echo json_encode(array('error' => ''));
	}
}




?>