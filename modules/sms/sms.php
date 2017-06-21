<?php
if(!isset($_GET['sms_id'])|| $_GET['sms_id']==''){
	if($this_system->type=='sms'){
		$sms = $this_system;
	} else {
		die('Undefined SMS');
	}
} else {
	$sms = new SMS(safeGet($_GET['sms_id']));
}


if(isset($_GET['balance'])){
	$year = isset($_GET['year']) ? safeGet('year') : $_SESSION['year'];
	$inc_classe = isset($_GET['inc_classe']);
	
	echo $sms->loadSchoolBalance($year, $inc_classe);

} elseif(isset($_GET['statics'])){
	echo $sms->loadSchoolStatics();

//	echo $sms->getLateList();
} elseif(isset($_GET['import'])){
	if($prvlg->_chk('edit_std_fees')){
		$file = $_GET['import'];
		echo Fees::readImportFile($file);
	}
} else {
	// Default school Layout
	$schoolFees = new SchoolFees($sms);
	echo $schoolFees->loadMainLayout();
}


?>