<?php
## SMS
## groups
if(isset($_GET['con']) && $_GET['con'] != 'all'){
	$con = $_GET['con'];
	$con_id = $_GET['con_id'];
	$sql = "SELECT * FROM groups WHERE parent ='$con' AND parent_id=$con_id";
	$rel = $con.'-'.$con_id;
} else {
	$con = 'all';
	$con_id = '';
	$rel = 'all';
	$sql = "SELECT * FROM groups WHERE parent ='all'";
}


// update and insert hall infos
if(isset($_POST['id'])){ 
	if(getPrvlg('resource_edit_groups')){
		$commom_fields = getTableFields( 'groups', DB_year);
		$affect_common_value = array();
		if($_POST['id'] != ''){
			foreach($_POST as $key => $value){
				if(in_array($key, $commom_fields)){
					if(strpos($key, '_date') !== false){
						if($value != ''){
							$affect_common_value[] = "$key=".GetSQLValueString(dateToUnix($value), "int");
						} else {
							$affect_common_value[] = "$key=NULL";
						}
					} else {
						$affect_common_value[] = "$key=".GetSQLValueString($value, "text");
					}
				}
			}
			if(do_query_edit( "UPDATE groups SET ".implode($affect_common_value, ', ')." WHERE id=".$_POST['id'], DB_year)){
				$id = $_POST['id'];
			}
		} else{
			$affect_common_fields = array();
			foreach($_POST as $key => $value){
				if(in_array($key, $commom_fields)){
					$affect_common_fields[] = $key;
					if(strpos($key, '_date') !== false){
						if($value != ''){
							$affect_common_value[] = GetSQLValueString(dateToUnix($value), "int");
						} else {
							$affect_common_value[] = "NULL";
						}
					} else {
						$affect_common_value[] = GetSQLValueString($value, "text");
					}
				}
			}	
			if(do_query_edit ( "INSERT INTO groups (".implode($affect_common_fields, ',').") VALUES ( ". implode($affect_common_value, ',').")", DB_year)){
				$id = mysql_insert_id();
			}
		}
		
		$answer = array();
		if(isset($id) && $id != ''){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = "Error";
		}
		echo  json_encode($answer);	
	} else {
		echo json_encode(array('error' => $lang['no_privilege']));
	}
	exit;	
}

if(isset($_GET['new'])){
	if(getPrvlg('resource_edit_groups')){
		require_once("scripts/services_functions.php");
		if($con == 'class' || $con == 'level'){
			$level_id=$con=='level' ? $con_id : getLevelByClass($con_id);
			$services=getLevelService($level_id);	
		}
		echo write_html('form', 'id="new_resource_form" class="ui-state-highlight ui-corner-all unprintable"',
			'<input type="hidden" name="id" id="group_id"  />'.	
			'<input type="hidden" name="parent" id="parent" value="'.$con.'"  />'.	
			'<input type="hidden" name="parent_id" id="parent_id"  value="'.$con_id.'" />'.	
			write_html('table', 'width="100%" border="0" cellspacing="0"',
				write_html('tr', '',
					write_html('td', ' width="120" valign="middel"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
					).
					write_html('td', '',
						'<input type="text" id="group_name" name="name"  />'
					)
				).
				($con == 'class' || $con == 'level'?
					write_html('tr', '',
						write_html('td', ' width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['services'])
						).
						write_html('td', '',
							write_html_select('service_id', $services, '')
						)
					)
				: '').
				write_html('tr', '',
					write_html('td', ' width="120" valign="middel"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['resp'])
					).
					write_html('td', '',
						'<input type="text" id="new_employer_name" class="input_double required" />'.
						'<input type="hidden" class="autocomplete_value" name="resp" id="new_prof_id" />'
					)
				)
			)
		);
	} else {
		echo $lang['no_privilege'];
	}
	exit;	
}

// delete Hall 
if(isset($_GET['delete']) && $_GET['delete'] != '' ){
	if(getPrvlg('resource_edit_groups')){
		$group_id = $_GET['delete'];
		$answer = array();
		if(do_query_edit("DELETE FROM groups WHERE id=$hall_id", DB_year)){
			$answer['id'] = $group_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = "Error";
		}
		echo  json_encode($answer);
	} else {
		echo json_encode(array('error' => $lang['no_privilege']));
	}
	exit;
}

// Hall infos
if(isset($_GET['itemid'])){
	require_once('scripts/hrms_functions.php');
	$item_id = $_GET['itemid'];
	$group = do_query("SELECT * FROM groups WHERE id=$item_id", DB_year);
	$stds = getStudentIdsByGroup($item_id);
	$student_list_trs = '';
	$tot_std =  0;
	if($stds != false ){
		$tot_std = count($stds);
		foreach($stds as $std_id){
			$student_list_trs .= write_html('tr', '',
				(getPrvlg('std_read') ?
					write_html('td', 'class="unprintable"',
						write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" onclick="openStudentInfos('. $std_id.')"',  write_icon('person'))
					)
				: '').
				write_html('td', '',  getStudentNameById($std_id)).
				($con != 'class' ?
					write_html('td', '',  getClassNameById(getClassIdFromStdId($std_id)))
				: '')
			);
		}
	}
	
	$item_infos = write_html('div', 'class="ui-corner-all ui-widget-content" style="padding:5px"',
		write_html('div', 'class="toolbox"',
			(getPrvlg('resource_edit_halls') ? 
				write_html('a', 'onclick="updateGroup('.$item_id.')"', write_icon('disk').$lang['save']).
				write_html('a', 'onclick="addGroupMembers('.$item_id.')"', write_icon('plus').$lang['add_members'])
			: '' ).
			write_html('a', 'action="print_pre" rel="#group-infos" plugin="print"', write_icon('print').$lang['print'])
		).
		write_html('h2', 'class="title"', $group['name']).
		write_html('div', 'class="tabs"',
			write_html('ul', '', 
				write_html('li', '', write_html('a', 'href="#group-infos"', $lang['infos'])).
				write_html('li', '', write_html('a', 'href="index.php?module=schedule&con=group&con_id='.$item_id.'"', $lang['schedule']))
			).			
			write_html('div', 'id="group-infos"', 
			write_html('div', 'class="showforprint hidden"',
				write_html('h3', '', write_html('em', '', $lang['year'].': '. $_SESSION['year'].'/'.($_SESSION['year']+1))).
				write_html('h2', '', $lang['group'].': '.$group['name'])
			).
			write_html('form', 'id="group-form" class="ui-state-highlight ui-corner-all unprintable"',
				'<input type="hidden" name="id" id="group_id"  value="'.$group['id'].'"/>'.	
				write_html('table', 'width="100%" border="0" cellspacing="0"',
					write_html('tr', '',
						write_html('td', ' width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
						).
						write_html('td', '',
							'<input type="text" id="hall_name" name="name" value="'.$group['name'].'" />'
						)
					).
					($con == 'class' || $con == 'level'?
						write_html('tr', '',
							write_html('td', ' width="120" valign="middel"', 
								write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['services'])
							).
							write_html('td', '',
								write_html_select('service_id', $services, '')
							)
						)
					: '').
					write_html('tr', '',
						write_html('td', ' width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['resp'])
						).
						write_html('td', '',
							'<input type="text" id="new_employer_name" class="input_double required" value="'.getEmployerNameById($group['resp']).'" />'.
							'<input type="hidden" class="autocomplete_value" name="resp" id="new_prof_id" value="'.$group['resp'].'" />'
						)
					)
				)
			).
			write_html('fieldset', '', 
				write_html('legend', '', $lang['members']).
				write_html('table', 'class="tablesorter" cellspacing="1"',
					write_html('thead','',
						write_html('tr', '',
							(getPrvlg('std_read') ?
								write_html('th', 'width="20" class="unprintable" style="background-image:none"', '&nbsp;')
							: '').
							write_html('th', '',$lang['name']).
							($con != 'class' ?
								write_html('th', '',  $lang['class'])
							: '')
						)
					).
					write_html('tbody', '', $student_list_trs)
				)
			)
		)
		)
	);
}

// Title
$title = $lang['groups'];


// Groups list
$list = '<input id="group_parent" type="hidden" value="'.$con.'" />'.
'<input id="group_parent_id" type="hidden" value="'.$con_id.'" />'.
write_html('div', 'class="showforprint hidden"',
	write_html('h3', '', write_html('em', '', $lang['year'].': '. $_SESSION['year'].'/'.($_SESSION['year']+1))).
	write_html('h2', '', $lang['groups_list'].': ')
).
'<ul class="list_menu listMenuUl sortable" rel="groups_'.$rel.'">';
$array_groups = array();
$groups = do_query_resource($sql, DB_year);
while($group = mysql_fetch_assoc($groups)){
	$array_groups[$group['id']] = $group['name'];
}

$list_array = sortArrayByArray($array_groups, getItemOrder('groups'));
foreach($list_array as $id => $name){
	$list .= write_html( 'li', 'val="'.$id.'" class="hoverable clickable ui-stat-default ui-corner-all" onclick="openResourceInfos( '.$id.')"', 
		$name
	);
}

$list .= '</ul>';
?>