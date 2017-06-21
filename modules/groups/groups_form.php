<?php
## group Form

// Post
if(isset($_POST['id'])){
	$answer = array();
	if($_POST['id'] != ''){
		$group_id = $_POST['id'];
		$editable = getPrvlg('create_groups') || $_SESSION['user_id'] == $_POST['resp'] ? true : false; 
		if($editable){
			if(UpdateRowInTable("groups", $_POST, "id=$group_id", DB_year) != false){
				if($_POST['tot_con'] != ''){
					do_query_edit("DELETE FROM groups_std WHERE group_id=$group_id", DB_year);
				}
				$answer['id'] = $group_id;
				$answer['error'] = '' ;
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		} else { 
			$answer['error'] = $lang['not_enough_privilege'] ;
		}	
		
	} else {
		$editable = getPrvlg('create_groups');
		if($editable ){
			if(insertToTable("groups", $_POST, DB_year) != false){
				$group_id =  mysql_insert_id();
				$answer['id'] = $group_id;
				$answer['error'] = '';
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		} else { 
			$answer['error'] = $lang['not_enough_privilege'] ;
		}		
	}
	
	if($_POST['tot_con'] != ''){
		$stds = strpos( $_POST['tot_con'], ',') !== false ? explode(',', $_POST['tot_con']) : array($_POST['tot_con']);
		do_query_edit("INSERT INTO groups_std (group_id, std_id) VALUES ($group_id, ".implode( "), ($group_id," , $stds ).")", DB_year);
		if(isset($_POST['service_id']) && $_POST['service_id'] != ''){
			$service_id = $_POST['service_id'];
			foreach($stds as $std_id){
				$chk_std_service = do_query_resource("SELECT services FROM materials_std WHERE std_id=$std_id AND services=$service_id", DB_year);
				if( $chk_std_service == false || mysql_num_rows($chk_std_service) ==0){
					do_query_edit("INSERT INTO materials_std (services, std_id) VALUES( $service_id, $std_id)", DB_year);
				}
			}
		}
	}
	
	setJsonHeader();
	print json_encode($answer);
	exit;
}

/////////// Form

if(isset($_GET['group_id'])){
	$group_id = $_GET['group_id'];
	$group = do_query("SELECT * FROM groups WHERE id=$group_id", DB_year);
	if($group == false || $group['id'] == ""){
		die(write_error($lang['cant_find_group']));
	} else {
		$seek = true;
		$stds_array = array();
		$stds = do_query_resource("SELECT ".DB_student.".student_data.id, ".DB_student.".student_data.name, ".DB_student.".student_data.name_ar FROM ".DB_student.".student_data, ".DB_year.".groups_std WHERE ".DB_year.".groups_std.group_id=$group_id AND ".DB_year.".groups_std.std_id=".DB_student.".student_data.id AND ".DB_student.".student_data.status=1 ORDER BY sex, name", MySql_Database);
		while($std = mysql_fetch_assoc($stds)){
			$stds_array[] = $std['id'];	
		}
	}
	$editable = getPrvlg('create_groups') || $_SESSION['user_id'] == $group['resp'] ? true : false; 
} else {
	$seek = false;
	$editable = getPrvlg('create_groups');
}

// Subject
$parent_obj = $sms->getAnyObjById(($seek ? $group['parent'] : $_GET['parent']), ($seek ? $group['parent_id'] : $_GET['parent_id']));
$services = $parent_obj->getServices();


if(isset($all_services) && $all_services != false){
	foreach($all_services as $service_id){
		 $thisSer = new services($service_id);
		if($this->chkServiceIsOption() == true){
			$services[] = $thisSer;
		}
	}
}


// The Form
$group_form =write_html('form', 'class="ui-state-highlight ui-corner-all unprintable" id="group_form"',
	'<input type="hidden" name="id"  value="'.($seek ? $group['id'] : '').'"/>'.
	'<input type="hidden" name="parent"  value="'.($seek ? $group['parent'] :  $_GET['parent']).'"/>'.	
	'<input type="hidden" name="parent_id"  value="'.($seek ? $group['parent_id'] : $_GET['parent_id']).'"/>'.
	'<input type="hidden" name="tot_con" id="tot_con"  value="'.($seek ? implode(',', $stds_array) :'').'"/>'.
	'<input type="hidden" name="editable" id="group_editable"  value="'.($editable ? 1 : 0).'"/>'.	
	write_html('table', 'width="100%" border="0" cellspacing="0"',
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" class="reverse_align"', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
			).
			write_html('td', '',
				($seek == false ? 
					write_html('span', 'style="float:left" class="ui-state-default fault_input"', $parent_obj->getName().'-')
				:'').
				'<input type="text" name="name"'.($seek ? 'value="'.$group['name'].'" class="input_double required"' : 'class="required"' ).'" />'
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" class="reverse_align"', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['resp'])
			).
			write_html('td', '',
				'<input type="text" value="'.$resp->getName().'" class="sug_emp input_double '.($seek && $group['resp'] == ''  ? 'ui-state-error' : '').'" />'.			
				'<input type="hidden" name="resp"  class="autocomplete_value" value="'.($seek ? $group['resp'] : $_SESSION['user_id'] ).'"/>'
			)
		).
		($seek == false || $group['service_id'] != '' ?
			write_html('tr', '',
				write_html('td', 'width="120" valign="middel" class="reverse_align"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['material'])
				).
				write_html('td', '',
					write_html_select('class="combobox" name="service_id"',objectsToArray( $services), ($seek ? $group['service_id'] : '' ))
				)
			)
		: '').
		write_html('tr', '',
			write_html('td', 'width="120" valign="top" class="reverse_align"', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['notes'])
			).
			write_html('td', '',
				write_html('textarea', 'name="comments"', ($seek ? $group['comments'] : '')	)		
			)
		)
	)
);

// toolbox
$student_list_toolbox = write_html('div', 'class="toolbox"',
	($editable ? 
		write_html('a', 'onclick="addGroupStd(this)"', write_icon('plus').$lang['add_manual']).
		write_html('a', 'onclick="addGroupStdAuto(this)"', write_icon('plus').$lang['add_auto'])
	 : '' ).
	($seek ? 
		write_html('a', 'action="print_pre" rel="$(this).parents(\'.ui-dialog\')" plugin="print"', write_icon('print').$lang['print']).
		write_html('a', 'action="exportTable" rel="$(this).parents(\'.ui-dialog\')" plugin="xml"',write_icon('disk'). $lang['export'])
	: '')
);

// student list
$student_list_trs = '';
if($seek){
	$stds = do_query_resource("SELECT ".DB_student.".student_data.id, ".DB_student.".student_data.name, ".DB_student.".student_data.name_ar FROM ".DB_student.".student_data, ".DB_year.".groups_std WHERE ".DB_year.".groups_std.group_id=$group_id AND ".DB_year.".groups_std.std_id=".DB_student.".student_data.id AND ".DB_student.".student_data.status=1 ORDER BY sex, name", MySql_Database);
	$tot_std =  0;
	$serial = 1;
	if($stds != false ){
		$tot_std = mysql_num_rows($stds);
		while($std = mysql_fetch_assoc($stds)){
			$student_list_trs .= write_html('tr', '',
				(getPrvlg('std_read') ?
					write_html('td', 'class="unprintable"',
						write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" onclick="openStudentInfos('. $std['id'].')"',  write_icon('person'))
					)
				: '').
				($editable ? 
					write_html('td', 'class="unprintable"',
						write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" onclick="removeStdFromGroup('. $std['id'].', this)"',  write_icon('close'))
					)
				: '').
				write_html('td', 'style="text-align:left"',  $_SESSION['lang'] == 'ar' ? $std['name_ar'] : $std['name'])
			);
			$serial++;
		}
	}
}
		
$student_list = ($seek ? 
	write_html('div', 'class="showforprint hidden"',
		write_html('table', 'width="100%" cellspacing="0" border="0"',
			write_html('tr', '', write_html('td', 'colspan="2"', write_html('em', '', $lang['year'].': '. $_SESSION['year'].'/'.($_SESSION['year']+1) ))).
			write_html('tr', '', 
				write_html('td', '',  $lang['group'].': '.$group['name']).
				write_html('td', '', $lang[$group['parent']].': '. getAnyNameById($group['parent'], $group['parent_id']))
			).
			write_html('tr', '', write_html('td', 'colspan="2"',  $lang['resp'].': '. $resp>getName()))
		)
	).
	write_html('h3', '' , $lang['total_std'].': '. $tot_std)
: "" ).
write_html('table', 'class="tablesorter student_list_tale"',
	write_html('thead','',
		write_html('tr', '',
			(getPrvlg('std_read') ?
				write_html('th', 'width="20" class="unprintable" style="background-image:none"', '&nbsp;')
			: '').
			($editable ? 
				write_html('th', 'width="20" class="unprintable" style="background-image:none"', '&nbsp;')
			: '').
			write_html('th', 'style="text-align:left"',$lang['name'])
		)
	).
	write_html('tbody', '', $student_list_trs)
);
?>