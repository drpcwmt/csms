<?php
## Reports

if(isset($_GET['list'])){
	$con = safeGet($_GET['con']);
	$con_id = safeGet($_GET['con_id']);
	$list = new StudentsList($con, $con_id);
	if($sms->getSettings('class_list_template') != false){
		$list->list_template = $sms->getSettings('class_list_template');
	}
	echo $list->createTable();

} elseif(isset($_GET['file']) && $_GET['file'] != ''){
	$std_id = safeGet('std_id');
	$report = new CertReport($std_id, safeGet('lang'));
	if($_GET['file'] == 'cert_radiation'){
		echo $report->loadRadiationCert();
	} elseif($_GET['file'] == 'cert_scolarity'){
		echo $report->loadScolarityCert();
	} 
	
} elseif(isset($_GET['balance'])){
	echo $sms->loadSchoolBalance();

} elseif(isset($_GET['statics'])){
	echo $sms->loadSchoolStatics();

}  elseif(isset($_GET['ministryreport'])){
	echo SchoolReport::loadMinistreyBalance();

} elseif(isset($_GET['req']) && $_GET['req'] == 'quit'){
	$reason = isset($_GET['reason']) && $_GET['reason']!='all'  ? $_GET['reason'] : '';
	$level_id = isset($_GET['level_id']) && $_GET['level_id']!='all'  ? safeGet($_GET['level_id']) : '';
	echo SchoolReport::loadQuitList($reason, $level_id);

}elseif(isset($_GET['req']) && $_GET['req'] == 'reservation'){
	//$reason = isset($_GET['reason']) && $_GET['reason']!='all'  ? $_GET['reason'] : '';
	$level_id = isset($_GET['level_id']) && $_GET['level_id']!='all'  ? safeGet($_GET['level_id']) : '';
	echo $sms->loadReservationList($level_id);
	//echo SchoolReport::loadSuspentionList($reason, $level_id);

}elseif(isset($_GET['redoubling'])){
	$level_id = isset($_GET['level_id']) && $_GET['level_id']!='all'  ? safeGet($_GET['level_id']) : '';
	echo SchoolReport::loadRedoublingReport($level_id);

}elseif(isset($_GET['waiting'])){
	$level_id = isset($_GET['level_id']) && $_GET['level_id']!='all'  ? safeGet($_GET['level_id']) : '';
	echo SchoolReport::loadWaitingReport($level_id);

} elseif(isset($_GET['reg_report'])){
	if(isset($_GET['level_id'])){
		echo $sms->loadRegistrationReport(safeGet('level_id'), safeGet('sex'), safeGet('order'));
	} else {
		$form = new Layout();
		$form->template = 'modules/sms/templates/students_reg_form.tpl';
		$levels = $sms->getLevelList();
		$form->levels_opts = write_select_options(objectsToArray($levels));
		
		echo $form->_print().
		write_html('div', 'id="reg_result_div"',
			$sms->loadRegistrationReport()
		);
	}

}


