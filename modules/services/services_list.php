<?php
## SMS
## services list

## included in classes, groups, profs, and students 
## so it can Manage service for each table
## operation display, assing, remove

require_once('scripts/hrms_functions.php');
/**************************** Variables ***************************/

$inherited = false;

switch($con){
	case  "prof" :
		$cur_service = getProfService($con_id);
	break;
	case  "supervisor" :
		$cur_service = getSupervisorServices($con_id);
	break;
	case  "level" :
		$cur_service = getLevelService($con_id);
	break;	
	case  "class" :
		$cur_service = getClassService($con_id);
		if($cur_service == false){
			$level_services = getLevelService(getLevelByClass($con_id));
			foreach($level_services as $lvl_srv){
				do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES ($con_id, $lvl_srv)", DB_year);
			}
			$inherited = true;
			$cur_service = $level_services;
		} 
	break;	
	case  "student" :
		$class_id = getClassIdFromStdId($con_id);
		$level_id = ($class_id) ? getlevelByClass($class_id) : false;
		$cur_service = getStudentService($con_id);
		$level_services = getLevelService($level_id) ;
		if($cur_service == false){
			$class_services = getClassService($class_id);
			if($class_services != false){
				foreach($class_services as $lvl_srv){
					if(chkServiceIsOption($lvl_srv) == false){
						$cur_service[] = $lvl_srv;
						do_query_edit("INSERT INTO $table ($ref, services) VALUES ($con_id, $lvl_srv)", DB_year);
					}
				}
				
				$class_groups = do_query_resource("SELECT groups.service_id FROM groups, groups_std WHERE groups.parent='class' AND groups.parent_id=$class_id AND groups.service_id IS NOT NULL AND groups.id=groups_std.group_id AND groups_std.std_id=$con_id", DB_year);
				while($group = mysql_fetch_assoc($class_groups)){
					if(!in_array($group['service_id'], $cur_service)){
						$cur_service[] = $group['service_id'];
						do_query_edit("INSERT INTO $table ($ref, services) VALUES ($con_id, ".$group['service_id'].")", DB_year);
					}
				}
				$inherited = true;
			} else {
				if($level_services != false){
					foreach($level_services as $lvl_srv){
						do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES ($class_id, $lvl_srv)", DB_year);
						if(chkServiceIsOption($lvl_srv) == false){
							$cur_service[] = $lvl_srv;
							do_query_edit("INSERT INTO $table ($ref, services) VALUES ($con_id, $lvl_srv)", DB_year);
						}
					}
					$inherited = true;
				}
			}
			
			$level_groups = do_query_resource("SELECT groups.service_id FROM groups, groups_std WHERE groups.parent='level' AND groups.parent_id=$level_id AND groups.service_id IS NOT NULL AND groups.id=groups_std.group_id AND groups_std.std_id=$con_id", DB_year);
			while($group = mysql_fetch_assoc($level_groups)){
				if(!in_array($group['service_id'], $cur_service)){
					$cur_service[] = $group['service_id'];
					do_query_edit("INSERT INTO $table ($ref, services) VALUES ($con_id, ".$group['service_id'].")", DB_year);
				}
			}

		}
		$cur_service =  getStudentService($con_id);
	break;

}


/******************************** Default Body *********************************/
// the ajaxed tbody
$service_list ='';
if($readable){
	$tbody = '';
	if(isset($cur_service) &&  $cur_service!= false){
		foreach($cur_service as $ser_id){
			$serv = getService($ser_id);
			$tbody .= write_html('tr', 'id="service-'.$ser_id.'"',
				write_html('td', 'class="unprintable"',
					write_html('button', 'type="button" class="ui-state-default hoverable" style="width:24px; height:24px" val="'.$ser_id.'" onclick="openService(this)"',  write_icon('extlink'))
				).
				($editable ? 
					write_html('td', 'class="unprintable"',
						write_html('button', 'type="button" class="ui-state-default hoverable" style="width:24px; height:24px" onclick="deleteService('. $ser_id.', \''.$con.'\', '.$con_id.')"',  write_icon('close'))
					)
				:'').
				write_html('td', 'align="center" valign="top"', getMaterialNameById($serv['material'])).
				write_html('td', 'align="center" valign="top"', getLevelNameById($serv['level_id']))
			);
		}
	}
	
	// in case of reload fetsh the tody and exit
	if(isset($_GET['reload'])){
		die($tbody);
	}
	
	if($inherited){
		echo write_html('div', 'class="ui-state-error ui-corner-all" style="padding:10px; margin:5px"', $lang['alert-inherited_materials']);
	}

	$service_list =  ($editable ? 
		write_html('div', 'class="toolbox"',
			write_html('a', 'onclick="addService(\''.$con.'\', '.$con_id.')"', write_icon('plus').$lang['add']).
			write_html('a', 'action="print_pre" rel="#service_div" plugin="print"', write_icon('print').$lang['print']).
			write_html('a', 'action="exportTable" rel="#service_div" plugin="xml"',write_icon('disk'). $lang['export'])
		)
	: '').
	write_html('div', 'id="service_div"',
		write_html('div', 'class="showforprint hidden"', 
			write_html('h2', '', $lang[$con].': '. getAnyNameById($con, $con_id)).
			write_html('h3', '', write_html('em', '', $lang['materials_list']))
		).
		write_html('form', 'id="con_materials_form"', 
			write_html('table', 'cellspacing="1" class="tablesorter"',
				write_html('thead', '',
					write_html('tr', '',
						($editable? '<th width="20" class="unprintable">&nbsp;</th>' : '').
						write_html('th', 'width="20" class="unprintable"', '&nbsp').
						write_html('th', '',$lang['materials']).
						write_html('th', 'width="200"', $lang['level'])
					)
				).
				write_html('tbody', 'id="service_level_tbody"', $tbody)
			)
		)
	);
}
?>