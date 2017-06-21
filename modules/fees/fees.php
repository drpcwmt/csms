<?php
## School Fees Plugin

if(!isset($_GET['sms_id'])|| $_GET['sms_id']==''){
	if($this_system->type=='sms'){
		$sms = $this_system;
	} else {
		die('Undefined sms');
	}
} else {
	$sms = new SMS(safeGet($_GET['sms_id']));
}

if($sms->getSettings('ig_mode') == '1'){
	include_once('config/ig_config.php');
}

$safems = $sms->getSafems();

$busms = $sms->getBusms();

// Notes
if(isset($_GET['notes'])){
	if(isset($_GET['save'])){
		$_POST['sms_id'] = $sms->id;
		$_POST['user_id'] = $_SESSION['user_id'];
		echo json_encode_result(do_insert_obj($_POST, 'students_notes', $safems->database, $safems->ip)!=false);
	} elseif(isset($_GET['delete'])){
		$note = do_query_obj("SELECT user_id FROM students_notes WHERE id=".$_POST['id'], $safems->database, $safems->ip);
		if($note->user_id == $_SESSION['user_id'] || $_SESSION['group'] == 'superadmin'){
			echo json_encode_result(do_delete_obj("id=".$_POST['id'], 'students_notes', $safems->database, $safems->ip)!=false);
		} else {
			echo json_encode_result(array('error'=>$lang['no_privilege']));
		}
	}
	
// Bank sheet	
} elseif(isset($_GET['bank_sheet'])){
	$date_id = isset($_GET['date_id']) ? safeGet('date_id') : '';
	echo Fees::getBankSheet($date_id);
	
// Payments	
} elseif(isset($_GET['new_payment'])){
	if($prvlg->_chk('edit_std_fees')){
		$nts = new I18N_Arabic('Numbers');
		if(isset($_GET['options'])){
			$out = array('error'=>'');
			$std_id = safeGet($_GET['std_id']);
			$student = new Students($std_id, $sms);
			$year = $_GET['year'];
			$ccid = $sms->getCC();
				// dates
			$rows = do_query_array("SELECT * FROM school_fees WHERE std_id=$student->id AND year=$year AND paid<value AND cc=$ccid", $safems->database, $safems->ip);
			$date_array = array(''=>' ');
			$out_acc = array(''=>' ');
			foreach($rows as $r){
				$fees = new Fees($r->fees_id);
				$acc = $fees->getAccount();
				$out_acc[Accounts::fillZero('main', $acc->main_code)] = $acc->title;
				$date = do_query_obj("SELECT title FROM school_fees_dates WHERE id=$r->date_id", $sms->database, $sms->ip);
				$date_array[$r->date_id] =  $date->title;
			}
			$out['dates'] = write_select_options( $date_array, '');
				// fees
			$out['fees'] = write_select_options( $out_acc, '');
	
			echo json_encode($out);
		} elseif(isset($_GET['save'])){
			$recete = Fees::addPayment($_POST);
			if($recete !== false ){
				$answer['recete'] = $recete;
				$answer['error'] = "";
			} else {
				$answer['error'] = 'Error';
			}
			echo json_encode($answer);
		}
	} else {
		echo '{"error": "'.$lang['no_privilege'].'"}';
	}
	// New fees 
} elseif(isset($_GET['new_fees'])){
	$con = safeGet($_GET['con']);
	$con_id = safeGet($_GET['con_id']);
	if(isset($_GET['save'])){
		if($prvlg->_chk('edit_std_fees')){
			$_POST['con'] = safeGet($_GET['con']);
			$_POST['con_id'] = safeGet($_GET['con_id']);	
			echo  Fees::saveNewFees($_POST);
		} else {
			echo '{"error": "'.$lang['no_privilege'].'"}';
		}
	} else {
		if($prvlg->_chk('read_std_fees')){
			echo Fees::loadNewFeesForm($con, $con_id);
		} else {
			echo write_error($lang['no_privilege']);
		}
	}
} elseif(isset($_GET['save_fees'])){
	if($prvlg->_chk('edit_std_fees')){
		$con = safeGet($_GET['con']);
		$con_id = safeGet($_GET['con_id']);
		echo json_encode(Fees::saveFees($con, $con_id, $_POST));
	} else {
		echo '{"error": "'.$lang['no_privilege'].'"}';
	}
		
} elseif(isset($_GET['del_fees'])){
	if($prvlg->_chk('edit_std_fees')){
		echo Fees::deleteFees($_POST['fees_id']);
	} else {
		echo '{"error": "'.$lang['no_privilege'].'"}';
	}
	
	// Reservations
} elseif(isset($_GET['reserved'])){
	$schoolFees = new SchoolFees($sms);
	echo $schoolFees->loadReservationTable();
	
	// Profil manager
} elseif(isset($_GET['profils'])){
	if(isset($_GET['savestd'])){
		if($prvlg->_chk('edit_std_profil')){
			$student = new Students($_POST['std_id']);
			echo json_encode($student->saveProfil($_POST['profil_id']));
		} else {
			echo json_encode_result(array('error'=>$lang['no_privilege']));			
		}
		
	} else {
		if($prvlg->_chk('edit_std_fees')){			
			if(isset($_GET['delete'])){
				echo json_encode(Profils::_delete($_POST['profil_id']));
				
			} elseif(isset($_GET['save'])){
				$result = true;
				if($profil = Profils::saveProfil($_POST)){
					$profil_id = $profil['profil_id'];
					if(isset($_GET['new'])){
						// Insert student
						if(isset($_GET['std_id'])){
							$student = new Students(safeGet('std_id'));
							$student->saveProfil($profil_id);
						}
					}
					$results = true;
				} else {
					$results = false;
				}
				
				if($result!=false){
					$answer = $profil;
					$answer['error'] = "";
					$answer['id'] = $profil_id;
				} else {
					$answer['error'] = $lang['error_updating'];
				}
				echo json_encode($answer);	
				
			} elseif(isset($_GET['new'])){
				echo Profils::newProfil($_GET['std_id']);
			} elseif(isset($_GET['profil_id'])){
				$profil = new Profils(safeGet($_GET['profil_id']), $sms);
				echo $profil->loadLayout();
			}
		} else {
			echo write_error($lang['no_privilege']);
		}
	}
	
} elseif(isset($_GET['calcFees'])) {
	if($prvlg->_chk('edit_std_fees')){
		$std = new Students(safeGet($_GET['std_id']), $sms);
		if($std->generatePayments()){
			$paid = do_query_obj("SELECT SUM(value) as total, SUM(paid) as paid FROM school_fees WHERE std_id=$std->id AND cc=$sms->ccid AND year=".$_SESSION['year']." AND currency='".$MS_settings['def_currency']."'", $safems->database, $safems->ip);
			$answer['total'] = $paid->total;
			$answer['paid'] = $paid->paid;
			$answer['error'] = "";
		} else {
			$answer['error'] = $lang['error_updating'];
		}
	} else {
		$answer['error'] = $lang['no_privilege'];
	}
	echo json_encode($answer);	


} elseif(isset($_GET['loaddates'])) {
	if($prvlg->_chk('read_std_fees')){
		$con = safeGet($_GET['con']);
		$con_id = safeGet($_GET['con_id']);
		if($con!=''){
			$table = Fees::loadDatesLayout($sms->getAnyObjById($con, $con_id));
		} else {
			$table = Fees::loadDatesLayout($sms);
		}
		echo write_html('form', '',
			'<input type="hidden" name="con" value="'.$con.'" />
			<input type="hidden" name="con_id" value="'.$con_id.'" />'.
			$table
		);
	} else {
		echo write_error($lang['no_privilege']);
	}


} elseif(isset($_GET['savedates'])){
	if($prvlg->_chk('edit_std_fees')){
		$_POST['con'] = safeGet($_GET['con']);
		$_POST['con_id'] = safeGet($_GET['con_id']);
		if(Fees::SavePaymentsDate($_POST)){
			$answer['error'] = "";
		} else {
			$answer['error'] = $lang['error_updating'];
		}
	} else {
		echo $answer['error'] = $lang['no_privilege'];
	}
	echo json_encode($answer);

} elseif(isset($_GET['deletedates'])){
	echo json_encode_result(do_delete_obj('id='.$_POST['date_id'], 'school_fees_dates', $sms->database, $sms->ip));
	
// Book Fees		
} elseif(isset($_GET['book_fees'])){
	$bookFees = new BookFees($sms);
	if(isset($_GET['level_id'])){
		echo $bookFees->getLevelFeesLayout(new Levels(safeGet('level_id'), $sms), $_SESSION['year']);		
	} else {
		echo $bookFees->loadMainlayout();
	}
	
// Bus Fees
} elseif(isset($_GET['bus_fees'])){
	$busFees = new BusFees($sms);
	if(isset($_GET['group_id'])){
		echo $busFees->getGroupFeesLayout(safeGet('group_id'), $_SESSION['year']);		
	} else {
		echo $busFees->loadMainLayout();
	}
	// Save payment settlement dates and values	
} elseif(isset($_GET['con'])){
	$con = safeGet($_GET['con']);
	$con_id = safeGet($_GET['con_id']);
		// load payment date and value
	if(isset($_GET['loadpayments'])){
		if($prvlg->_chk('read_std_fees')){
			echo Fees::getPaymentsLayout($con, $con_id);
		} else {
			echo write_error($lang['no_privilege']);
		}
		// save payment date and value
	} elseif(isset($_GET['savepayments'])){
		if($prvlg->_chk('edit_std_fees')){
			global $lang;
			$result = true;
			$post = $_POST;
			$post['con'] = safeGet($_GET['con']);
			$post['con_id'] = safeGet($_GET['con_id']);	
			if(isset($_POST['payment_calendar'])) {
				if(Fees::SavePaymentsValues($post) == false){
					$result = false;
				}
			}
			if($result){
				$result = Fees::SavePaymentsDate($post);
			} 	
			if($result){
				$answer['error'] = "";
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		} else {
			$answer['error'] = $lang['no_privilege'];
		}
		echo json_encode($answer);
		
			// Browse level & classes & student
	} elseif(isset($_GET['browse'])){
		$schoolFees = new SchoolFees($sms);
		if($prvlg->_chk('read_std_fees')){
			if($con == 'school'){
				echo $schoolFees->browseFees();
			} elseif($con == 'level'){
				//$level = new Levels($con_id, $sms);
				echo $schoolFees->browseLevel($con_id);
			} elseif($con =='class'){
				//$class= new Classes($con_id, '', $sms);
				echo $schoolFees->browseClass($con_id);
			}elseif($con =='student'){
				$student= new Students($con_id, $sms);
				echo $student->loadMainLayout();
			}
		} else {
			echo write_error($lang['no_privilege']);
		}
	} else {
		$schoolFees = new SchoolFees($sms);
		if($con == 'school'){
			echo $schoolFees->getFeesSettings();
		} elseif($con == 'level'){
			$level = new Levels($con_id, $sms);
			echo $level->loadFeesLayout(isset($_GET['year'])? safeGet('year') : '');
		} elseif($_GET['con'] == 'student'){
			$student = new Students($con_id, $sms);
			$studentFees = new StudentFees($student);
			echo $studentFees->loadFeesLayout(isset($_GET['year'])? safeGet('year') : '');
		}
	}
} elseif(isset($_GET['update_std_ser_fees'])){
	$result= false;
	if(do_update_obj(
		array($_POST['field']=>$_POST['value']), 
		"std_id=".$_POST['std_id']." AND services=".$_POST['service_id']." AND exam='".$_POST['exam']."'",
		'materials_std',
		$sms->db_year, $sms->ip
	)){
		$student = new Students($_POST['std_id'], $sms);
		$studentFees = new StudentFees($student);
		$studentFees->generateServiceFees($_SESSION['year']);
		$result = true;
	}
	echo json_encode_result($result);

} elseif(isset($_GET['reloadRegistrationtable'])){
	$student = new Students(safeGet('std_id'), $sms);
	$studentFess = new StudentFees($student);
	echo $studentFess->loadStudentRegistrationTable( $_SESSION['year']);
	
} elseif(isset($_GET['pay_form'])){
	if(isset($_GET['save'])){
		$nts = new I18N_Arabic('Numbers');
		$nts->setFeminine(1);
		$std_id =$_POST['std_id'];
		$student = new Students($std_id, $sms);
		$studentFees = new StudentFees($student);
		$exam =$_POST['exam'];
		$service_id = isset($_POST['service_id']) ? $_POST['service_id'] : '';
		$group = isset($_POST['group']) ? $_POST['group'] : '';
		$year = isset($_POST['year']) ? $_POST['year'] : '';
		$type = isset($_POST['fees']) ? $_POST['fees'] : '';
		$bank = $_POST['payment_mode'] == 'cash' ? '' : $_POST['bank'];
		$value = $_POST['value'];
		if($value >0){
			echo json_encode_result($studentFees->saveIgPayment( $exam, $service_id, $_POST['value'], $_POST['date'], $bank, $type, $group, $year));
		} else {
			echo json_encode_result(array('error' => "Input value is not valid"));
		}
	} else {
		$student = new Students(safeGet('std_id'), $sms);
		$studentFees = new StudentFees($student);
		$service_id = (isset($_GET['service_id']) ? safeGet('service_id') : '');
		$group = (isset($_GET['group']) ? safeGet('group') : '');
		echo $studentFees->newPayForm(safeGet('type'), $service_id, safeGet('exam'), safeGet('paid'), $group);
	}
} elseif(isset($_GET['refund_std_service'])){
	if(isset($_GET['save'])){
		$nts = new I18N_Arabic('Numbers');
		$nts->setFeminine(1);
		$std_id = $_POST['std_id'];
		$student = new Students($std_id, $sms);
		$studentFees = new StudentFees($student);
		echo json_encode_result($studentFees->refundIgPayment($_POST));
	} else {
		$student = new Students(safeGet('std_id'), $sms);
		$studentFees = new StudentFees($student);
		$service_id = (isset($_GET['service_id']) ? safeGet('service_id') : '');
		echo $studentFees->newRefundForm( $service_id, safeGet('exam'));
	}
// Splitting 
} elseif(isset($_GET['splitting'])){
	if(isset($_GET['pay'])){
		if(isset($_GET['save'])){
			echo json_encode_result(Splitting::savePay($_POST));
		} else {
			echo Splitting::payForm($_GET['split_id']);
		}
	} elseif(isset($_GET['refund'])){
		if(isset($_GET['save'])){
			echo json_encode_result(Splitting::saveRefund($_POST));
		} else {
			echo Splitting::refundForm($_GET['split_id']);
		}
	} elseif(isset($_GET['save'])){
		echo json_encode_result(Splitting::_save($_POST));
	} elseif(isset($_GET['remove'])){
		echo json_encode_result(Splitting::_delete($_POST));
	} else {
		$splitting = new Splitting(new Students(safeGet('std_id'), $sms));
		if(isset($_GET['new'])){
			echo $splitting->addForm();
		} else {
			echo $splitting->loadLayout();
		}
	}

// Splitting 
} elseif(isset($_GET['remarking'])){
	
	if(isset($_GET['new'])){
		$remarking = new Remarking(new Students(safeGet('std_id'), $sms));
		echo $remarking->addForm();
	} elseif(isset($_GET['save'])){
		echo json_encode_result(Remarking::_save($_POST));
	} else {
		$remarking = new Remarking(new Students(safeGet('std_id'), $sms));
		echo $remarking->loadLayout();
	}

// Default	
} else {
	$schoolFees = new SchoolFees($sms);
	if(isset($_GET['totals'])){
		echo $schoolFees->getTotals();
	
		// Late List
	} elseif(isset($_GET['late_list'])){
		if($prvlg->_chk('read_std_fees') || $prvlg->_chk('read_std_fees_stat')){
			$level_id = '';
			$fees_acc = '' ;
			$year = isset($_GET['year']) ? safeGet('year') : $_SESSION['year'];
			$date = isset($_GET['date']) ?  safeGet('date') : '';
			if(isset($_GET['level_id']) && $_GET['level_id']!= ' '){
				$level_id = safeGet('level_id');
			} 
			if(isset($_GET['fees_acc']) && $_GET['fees_acc']!= ' '){
				$fees_acc = safeGet('fees_acc');
			}
			echo $schoolFees->getLateList($level_id, $fees_acc, isset($_GET['level_id']) || isset($_GET['fees_acc']), $year, isset($_GET['none_paid']), $date);
		} else {
			echo write_error($lang['no_privilege']);
		}
	} else {
		echo $schoolFees->loadMainLayout();
	}
}
?>