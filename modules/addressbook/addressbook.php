<?php
## AddressBook

if(isset($_GET['sys_id']) && $_GET['sys_id']!=''){
	$conx = new Connections(safeGet('sys_id'));
	if($conx->type == 'sms'){
		$sys = new SMS($conx->id);
	} elseif($conx->type == 'hrms'){
		$sys = new Hrms($conx->id);
	}
} else {
	if($this_system->type == 'sms' || $this_system->type == 'hrms'){
		$sys = $this_system;
	} else {
		if($_GET['con'] == 'emp'){
			$sys = $this_system->getHrms();
		}
	}
}

if(isset($_GET['new'])){
	echo AddressBook::_new(safeGet('con'), safeGet('con_id'), $sys);
} elseif(isset($_GET['save'])){
	echo json_encode_result(AddressBook::_save($_POST, $sys));
} elseif(isset($_GET['edit'])){
	echo AddressBook::_edit(safeGet('address_id'), $sys);
} elseif(isset($_GET['copy'])){
	echo json_encode_result(AddressBook::_copy(safeGet('con'), safeGet('con_id'), $_POST['from'], $sys));
}elseif(isset($_GET['delete'])){
	echo json_encode_result(AddressBook::_delete($_POST['id'], $sys));
} else {
	$con = safeGet('con');
	$con_id = safeGet('con_id');
	$addressbook = new AddressBook($con, $con_id, $sys);
	echo $addressbook->getList();
}
?>
