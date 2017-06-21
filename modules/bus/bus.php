<?php
	## Bus
$busmss =  BusMS::getList();

if(!is_array($busmss) || count($busmss) == 0){
	die('no server defined');
}
	 
if(isset($_GET['busms_id'])){
	$busms = new BusMS(safeGet($_GET['busms_id']));
	if(isset($_GET['sms_id'])){
		$sms = new SMS(safeGet($_GET['sms_id']));
	}
} else {
	if(isset($_GET['sms_id'])){
		$sms = new SMS(safeGet($_GET['sms_id']));
		$sms_bus = $sms->getSettings('busms_server_ip');
		foreach($busmss as $b){
			if($b->ip == $sms_bus){
				$busms = new BusMs($b->id);
			}
		}
	} else {
		$busms = $busmss[0];
	}
}

	// New fees 
if(isset($_GET['new_fees'])){
	$group_id = safeGet($_GET['group_id']);
	if(isset($_GET['save'])){
		$_POST['group_id'] = safeGet($_GET['group_id']);	
		echo  Bus::saveNewFees($_POST);
	} else {
		echo Bus::loadNewFeesForm($group_id);
	}
} elseif(isset($_GET['del_fees'])){
	echo Bus::deleteFees($_POST['fees_id']);
	
}elseif(isset($_GET['group_id'])){
	echo Bus::loadGroupLayout(safeGet($_GET['group_id']));

} else {
	echo Bus::loadMainLayout();
}
		
?>