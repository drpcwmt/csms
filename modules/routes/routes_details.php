<?php
$route_id = $_GET['route_id'];

// New Target Form
if(isset($_GET['new_target'])){
	$targs = do_query_resource("SELECT id FROM  routes_parcour WHERE route_id=$route_id", BUSMS_Database);
	if(mysql_num_rows($targs) == 0 ){
		$address = $lang['the_school'];
	} else { 
		$address = ''; 
	}
	
	$route_timmig_select = array('m' => $lang['morning'], 'e'=>$lang['evening'], 'o'=> $lang['others']);
	echo write_html('form', 'id="new_target_form" class="ui-state-highlight ui-corner-all"',
		write_html('table', '',
			write_html('tr', '',
				write_html('td', 'width="120"',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['address'])
				).
				write_html('td', '',
					'<input type="text" id="address" name="address" class="input_double require" value="'.$address.'"/>'
				)
			).
			write_html('tr', '',
				write_html('td', 'width="120"',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['time'])
				).
				write_html('td', '',
					'<input type="text" id="arrival_time" name="arrival_time" class="mask-time"/>'
				)
			).
			write_html('tr', '',
				write_html('td', 'width="120"',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['route_timming'])
				).
				write_html('td', '',
					write_html_select('name="timming" id="timming" class="combobox"', $route_timmig_select, '')
				)
			)
		)
	);
	exit;
}
// submit new Target
if(isset($_POST['address'])){
	setJsonHeader();
	$values = $_POST;
	$values['route_id']=$route_id;
	 if($target_id = insertToTable('routes_parcour', $values)){
		 echo "{\"error\" : \"\", \"id\" : \"$target_id\"}";
	} else {
		echo "{\"error\" : \"".$lang['error_updating']."\"}";
	}
	exit;	
}
// update Target time
if(isset($_POST['arrival_time'])){
	setJsonHeader();
	$target_id = $_POST['target_id'];
	if(do_query_edit("UPDATE routes_parcour SET arrival_time='".timeToUnix($_POST['arrival_time'])."' WHERE id=$target_id", BUSMS_Database)){
	 	echo "{\"error\" : \"\", \"id\" : \"$target_id\"}";
	} else {
		echo "{\"error\" : \"".$lang['error_updating']."\"}";
	}	
	exit;
}

// Delete target
if(isset($_POST['del_target'])){
	$target_id = $_POST['del_target'];
	if(do_query_edit("DELETE FROM routes_parcour WHERE id=$target_id", BUSMS_Database)){
	 	echo "{\"error\" : \"\", \"id\" : \"$target_id\"}";
	} else {
		echo "{\"error\" : \"".$lang['error_updating']."\"}";
	}	
	exit;
}

if(isset($_GET['reload_parcour'])){
	echo buildRouteParcourTable($route_id);
	exit;
}

// Deafault Body
require_once("scripts/hrms_functions.php");
## route Details
$route = do_query("SELECT * FROM routes WHERE id=$route_id", MySql_Database);
$route_m = do_query("SELECT MIN(arrival_time), MAX(arrival_time) FROM routes_parcour WHERE route_id=$route_id AND timming='m'",MySql_Database);
$route_e = do_query("SELECT MIN(arrival_time), MAX(arrival_time) FROM routes_parcour WHERE route_id=$route_id AND timming='e'",MySql_Database);
//$count_std = mysql_num_rows(do_query_resource("SELECT con_id FROM route_std WHERE route_id=$route_id", MySql_Database));

$select_bus = getBusList(true);
$select_bus[] = $route['bus_id'];

$members_trs ='';
$members = do_query_array("SELECT * FROM route_std WHERE route_id=$route_id", BUSMS_Database);
foreach($members as $member){
	if($member->con == 'std'){
		$student = new Students($member->con_id, $member->ms_id);
		$member_name = $student->getName();
		$type = $lang['student'];
	} else {
		$member_name = getEmployerNameById($members['con_id']);
		$type= $lang['employer'];
	}
	$members_trs .= write_html('tr', '', 
		write_html('td', ' class="unprintable"',
			write_html('button', 'title="'.$lang['delete'].'" class="ui-state-default hoverable" onclick="cfmDeleteMember('.$route_id.','.$member->con.','.$member->con_id.' )" style="width:24px; height:24px"',
				write_icon('close')
			)
		).
		write_html('td', '', $member_name).
		write_html('td', '', $type)
	);
}


echo write_html('div', 'class="tabs"',
	write_html('ul', '',
		write_html('li', '', write_html('a', 'href="#route_detail_div"', $lang['details'])).
		write_html('li', '', write_html('a', 'href="#members_div"', $lang['members']))
	).
	write_html('div', 'id="route_detail_div"',
		write_html('div', 'class="showforprint hidden"', 
			write_html('h2', '', $lang['the_route'].': ')
		).
		write_html('form', 'id="route_form" class="ui-corner-all ui-state-highlight"', 
			write_html('table', '',
				write_html('tr', '',
					write_html('td', 'width="120"',
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['no.'])
					).
					write_html('td', '',
						'<input type="text" id="id" name="id" value="'.$route['id'].'"/>'
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120"',
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['region'])
					).
					write_html('td', '',
						'<input type="text" id="region" name="region" value="'.$route['region'].'"/>'
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120"',
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['driver'])
					).
					write_html('td', '',
						'<input type="text" id="driver_name" class="input_double required" value="'.getEmployerNameById($route['driver_id']).'"/>'.
						'<input type="hidden" id="driver_id" name="driver_id"  class="autocomplete_value" value="'.$route['driver_id'].'"/>'.
						write_html('em', '', getEmployerTel($route['driver_id']))
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120"',
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['matron'])
					).
					write_html('td', '',
						'<input type="text" id="matron_name" class="input_double required" value="'.getEmployerNameById($route['matron_id']).'"/>'.
						'<input type="hidden" id="matron_id" name="matron_id"  class="autocomplete_value" value="'.$route['matron_id'].'"/>'.
						write_html('em', '', getEmployerTel($route['matron_id']))
					)
				).
				write_html('tr', '',
					write_html('td', 'width="120"',
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['bus_code'])
					).
					write_html('td', '',
						write_html_select('name="bus_id" id="bus_id" class="combobox"', $select_bus, $route['bus_id'])
					)
				)
			)
		).
		write_html('h2', '', $lang['parcour']).
		write_html('div', 'class="toolbox"',
			write_html('a', 'onclick="addTarget('.$route_id.')" ',$lang['add_address'].write_icon('plus')).
			write_html('a', 'class="print_but" rel="#route_detail_div"',$lang['print'].write_icon('print'))
		).
		write_html('div', 'id="route_parcour_div"',
			buildRouteParcourTable($route['id'])
		)
	).
	write_html('div', 'id="members_div"',
		write_html('div', 'class="toolbox"',
			write_html('a', 'onclick="addMember(\'std\','.$route_id.')" ',$lang['add_student'].write_icon('plus')).
			write_html('a', 'onclick="addMember(\'emp\','.$route_id.')" ',$lang['add_employer'].write_icon('plus'))
		).
		write_html('table', 'class="tablesorter"',
			write_html('thead', '',
				write_html('tr', '', 
					write_html('th', 'width="20" class="unprintable"', '&nbsp;').
					write_html('th', '', $lang['name'] ).
					write_html('th', 'width="120"', $lang['type'])
				)
			).
			write_html('tbody', '', $members_trs)
		)
	)
);

?>
