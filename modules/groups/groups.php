<?php
## Groups

$con = isset($_GET['con']) ? safeGet($_GET['con']) : false;
$con_id = isset($_GET['con_id']) ? safeGet($_GET['con_id']) : false;





if(isset($_GET['autoimport'])){
	include('groups_import.php');	
}

/*if(isset($_GET['new'])){
	if($prvlg->_chk('create_groups')){
		echo Groups::newForm(safeGet('parent'), safeGet('parent_id'));
	} else {
		echo write_error($lang['no_privilege']);
	}
	exit;
	include('groups_form.php');
	echo $group_form.
	$student_list_toolbox.
	$student_list;
	exit;
}*/

if(isset($_GET['list'])){
	if(isset($_GET['reload'])){
		echo Groups::getListTable($con, $con_id);
	} else {
		$parent = $sms->getAnyObjById($con, $con_id);
		echo  write_html('div', 'class="toolbox"',
			(getPrvlg('create_groups') ? 
				write_html('a', 'action="newGroup" parent="'.$con.'" parent_id="'.$con_id.'"', $lang['new']. write_icon('document'))
				: ''
			).
			write_html('a', 'onclick="print_pre(\'#groups-'.$con.'-'.$con_id.'\')"', $lang['print_list'].write_icon('print')).
			write_html('a', 'onclick="saveAsPdf(\'#groups-'.$con.'-'.$con_id.'\')"', $lang['save_as_pdf'].write_icon('disk')).
			write_html('a', 'onclick="exportTable(\'#groups-'.$con.'-'.$con_id.'\')"',write_icon('disk'). $lang['export'])
		).
		write_html('div', 'id="groups-'.$con.'-'.$con_id.'"',
			write_html('h3', 'class="hidden showforprint"', $parent->getName()).
			write_html('h3', 'class="hidden showforprint" align="center"', $lang['groups']).
			write_html('div', 'id="group_list_div"',
				Groups::getListTable($con, $con_id)
			)
		);
	}
} elseif(isset($_GET['new'])){
	if($prvlg->_chk('create_groups')){
		if(isset($_GET['save'])){
			
		} else {
			echo Groups::newForm(safeGet('parent'), safeGet('parent_id'));
		}
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['save'])){
	$auth = false;
	if(isset($_POST['id']) && $_POST['id'] != ''){
		$group_id = safeGet($_POST['id']);
		$group = new Groups($group_id);
		if($group->editable){
			$auth = true;
		}
	} else {
		if(getPrvlg('create_groups')){
			$auth = true;
		}
	}
	if($auth){
		echo json_encode_result(Groups::_save($_POST));
	} else {
		echo json_encode(array( 'error' => $lang['no_privilege']));
	}
} elseif(isset($_GET['delete'])){
	$group_id = safeGet($_POST['id']);
	$group = new Groups($group_id);
	if($group->editable){
		echo Groups::_delete($group_id);
	} else {
		echo json_encode(array( 'error' => $lang['no_privilege']));
	}

} elseif(isset($_GET['group_id'])){
	$group_id = safeGet($_GET['group_id']);
	$group = new Groups($group_id, $sms);
	echo $group->loadLayout();
} elseif(isset($_GET['etab_id'])){
	echo Groups::loadMainLayout(new Etab(safeGet('etab_id'), $sms));
} else {
	echo Groups::loadMainLayout();
}
?>