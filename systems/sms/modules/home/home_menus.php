<?php
$etabs = $sms->getEtabList();
$etab_arr = objectsToArray($etabs);

$grades = $sms->getLevelList();
$grades_arr = objectsToArray($grades);

$classes = Classes::getList();
$classes_arr = objectsToArray($classes);

$groups = Groups::getList();
$groups_arr = objectsToArray($groups);


$menu_item = array();
$menu_item['student'] = array('services', 'schoollife', 'marks', 'schedules');
$menu_item['parent'] = array('studentdata', 'services', 'schoollife', 'marks', 'schedules');
$menu_item['admin'] = array('studentsearch', 'parentsearch', 'schoollife', 'reports', 'fees', 'applications');
$menu_item['superadmin'] = array('studentsearch', 'parentsearch', 'schoollife', 'reports', 'fees', 'applications');
$menu_item['principal'] = array('studentsearch', 'parentsearch', 'schoollife', 'reports', 'fees');
$menu_item['coordinator'] = array('studentsearch', 'parentsearch', 'schoollife', 'reports', 'fees');
$menu_item['supervisor'] = array('studentlist', 'services', 'parentsearch', 'schoollife', 'marks', 'schedules');
$menu_item['prof'] = array('studentlist', 'services', 'parentsearch', 'schoollife', 'marks', 'schedules');

/**************************************  Student data **************************************/
$menus = $menu_item[$_SESSION['group']];
foreach($menus as $str){
	$lis[] = write_menu_list($str);	
}

$home_menus = write_html('ul', 'id="home_menus" class="nav"', implode('', $lis));

function write_menu_item($title, $attr,  $content='',$icon=false){
	return write_html('li','',
		write_html('a', 'class="ui-state-default hoverable" '.$attr, $title). 
		($icon != false ?  write_icon("triangle-1-$icon") : '').
		$content
	);
}

function write_menu_list($str){
	global $lang, $prvlg;
	$user_group = $_SESSION['group'];
	$out = '';
	switch($str){
		case 'services':
			return write_service_items();
		break;
		case 'schoollife':
			return write_schoolife_items();
		break;	
		case 'fees':
			if($prvlg->_chk('read_std_fees') || $prvlg->_chk('read_std_fees_stat')){
				return write_menu_item($lang['school_fees'], 'module="fees" action="openLateList" level_id=""');
			} else {
				return '';
			}
		break;
		case 'applications':
			return write_menu_item($lang['applications'], 'module="applications" action="openApplications"');
		break;
		case 'marks':
			if($prvlg->_chk('mark_read')){
				if(in_array($user_group, array('student', 'parent'))){
					$con = 'student';
					$con_id = $_SESSION['std_id'];
				} else {
					$con = $_SESSION['cur_con'];
					$con_id = $_SESSION['cur_class'];
				}
				return write_menu_item($lang['marks'], 'module="marks" action="openMarks" con="'.$con.'" con_id="'.$con_id.'"');
			} else {
				return '';	
			}
		break;
		case 'schedules':
			if(in_array($user_group, array('student', 'parent'))){
				return write_menu_item($lang['my_schedule'], 'module="schedule" action="openSchedule" con="student" con_id="'.$_SESSION['std_id'].'"');
			} elseif($user_group == 'prof'){
				$my_schedule = write_menu_item($lang['my_schedule'], 'module="schedule" action="openSchedule" con="prof" con_id="'.$_SESSION['user_id'].'"');
				$class_schedule = write_menu_item($lang['class_schedule'], 'module="schedule" action="openSchedule" con="'.$_SESSION['cur_con'].'" con_id="'.$_SESSION['cur_class'].'"');
				return write_menu_item($lang['schedule'] , '', write_html('ul', '', $my_schedule.$class_schedule), 's');
				
			} elseif($user_group == 'supervisor'){
				return write_menu_item($lang['schedule'], 'module="schedule" action="openSchedule" con="'.$_SESSION['cur_con'].'" con_id="'.$_SESSION['cur_class'].'"');
			}
		break;
		case 'studentdata':
			return write_menu_item($lang['personel_info'], 'module="student" action="openStudent" std_id="'.$_SESSION['std_id'].'"');
		break;
		case 'studentsearch':
			return write_search_student_items();
		break;
		case 'parentsearch':
			return write_search_parent_items();
		break;
		case 'reports':
			return write_reports_items();
		break;
		case 'studentlist':
			return write_menu_item($lang['students_lists'], 'module="reports" action="openHomeStudentList" con="class" conid="'.$_SESSION['cur_class'].'"');
		break;
		
	}
}

function write_service_items(){
	global $lang;
	$materials_lis = '';
	$lis = array();
	if(in_array($_SESSION['group'], array('student', 'parent', 'prof', 'supervisor'))){
		$menu_service = array();
		if($_SESSION['group'] == 'student' || $_SESSION['group'] == 'parent'){
			$student = new Students($_SESSION['std_id']);
			$menu_service = $student->getServices();
		} else{
			if($_SESSION['group'] == 'prof'){
				$prof = new Profs($_SESSION['user_id']);
				$menu_service = $prof->getServices($_SESSION['cur_con'], $_SESSION['cur_class']);
			//	print_r($menu_service);
			} else {
				$supervisor = new Supervisors($_SESSION['user_id']);
				$super_service = $student->getServices();
				$class = new Classes($_SESSION['cur_class']);
				$class_service = $class->getServices();
				$menu_service = array_intersect($super_service, $class_service);
			}
		}
	
		if(isset($menu_service) && $menu_service!=false && count($menu_service) > 0){
			foreach($menu_service as $service){
				$lis[] = write_menu_item($service->getName(), ' module="services" action="openService" serviceid="'.$service->id.'" title="'.$service->getName().'"');
			}
		}
		
		$materials_lis =  write_menu_item($lang['materials'], '', write_html('ul', '', implode('', $lis)), 's');
	}
	return $materials_lis;
}

function write_schoolife_items(){
	global $lang, $prvlg;
	$abs_prvlg = $prvlg->_chk('att_absent_read');
	$behave_prvlg = $prvlg->_chk('read_behavior');
	$permis_prvlg = $prvlg->_chk('att_permis_read');
	$li_schoollife = '';
	$lis = array();
	if($abs_prvlg || $behave_prvlg || $permis_prvlg){
		if(in_array($_SESSION['group'], array('student', 'parent'))){
			if($abs_prvlg){
				$lis[] =write_menu_item($lang['attendance'], 'module="absents" action="openAbsents" stdid="'.$_SESSION['std_id'].'"');
			} 
			if($behave_prvlg){
				$lis[] =write_menu_item($lang['behavior'], 'module="behavior" action="openBehaviors" stdid="'.$_SESSION['std_id'].'"');
			} 
		} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
			if($abs_prvlg){
				$lis[] =write_menu_item($lang['attendance'], 'module="absents" action="openAbsents" classid="'.$_SESSION['cur_class'].'"');
			} 
			if($behave_prvlg){
				$lis[] =write_menu_item($lang['behavior'], 'module="behavior" action="openBehaviors" classid="'.$_SESSION['cur_class'].'"');
			} 
			if($permis_prvlg){
				$lis[] =write_menu_item($lang['permition_out'], 'module="absents" action="openOutPermissions" classid="'.$_SESSION['cur_class'].'"');
			} 
		} else {
			if($abs_prvlg){
				$lis[] = write_menu_item($lang['attendance'], 'module="absents" action="openAbsents"');
			} 
			if($behave_prvlg){
				$lis[] = write_menu_item($lang['behavior'], 'module="behavior" action="openBehaviors"');
			} 
			if($permis_prvlg){
				$lis[] = write_menu_item($lang['permition_out'], 'module="absents" action="openOutPermissions"');
			} 
		}
		
		$li_schoollife =  write_menu_item($lang['school_life'], '', write_html('ul', '', implode('', $lis)), 's');
	}
	
	return $li_schoollife;
}

function write_search_student_items(){
	global $etabs, $grades, $classes, $groups, $lang, $prvlg, $this_system;
	$search_by_name = write_menu_item($lang['by_name'], 'module="students" action="openSeachStudentByName"');
	$search_by_id = write_menu_item($lang['by_id'], 'module="students" action="openSeachStudentById"');
	$search_by_cand = write_menu_item($lang['cand_no'], 'module="students" action="openSeachStudentByCandNo"');
	$search_item = write_menu_item(
		$lang['search'], '', 
		write_html('ul', '', 
			$search_by_name. $search_by_id. ($this_system->getSettings('ig_mode') == '1' ? $search_by_cand : '')
		), 'e'
	);
	
	$etabs_items =array();
	$grades_items = array ();
	$classes_items  =array();
	$groups_items = array();
	
	$add_item = $prvlg->_chk('std_add') ? write_menu_item($lang['new_student'], 'module="students" action="addNewStudent"') : '';
	
	$list_items = array();
	if(in_array($_SESSION['group'], array('admin', 'superadmin', 'pricipal', 'coordinator'))){
		$list_items[] =  write_menu_item($lang['saved_lists'], 'module="students" action="getSavedProced"');
		$list_items[] =  write_menu_item($lang['all_std'], 'module="students" action="getStudentlist"');
	}
	if(in_array($_SESSION['group'], array('superadmin', 'admin'))){
		foreach ($etabs as $etab){
			$etabs_items[] =  write_menu_item($etab->getName(), 'module="students" action="getStudentlist" field="'.DB_student.'.etablissement.id" rel="'.$etab->id.'"');
		}
		$list_items[] =  write_menu_item($lang['by_etab'], '', write_html('ul', 'class="scrollable"', implode('', $etabs_items)), 'e');
	}
	foreach ($grades as $grade){
		$grades_items[] =  write_menu_item($grade->getName(), 'module="students" action="getStudentlist" field="'.DB_student.'.levels.id" rel="'.$grade->id.'"');
	}
	$list_items[] =  write_menu_item($lang['by_level'], '', write_html('ul', 'class="scrollable"',implode('', $grades_items)), 'e');

	foreach ($classes as $class){
		$classes_items[] =  write_menu_item($class->getName(), 'module="students" action="getStudentlist" field="'.DB_year.'.classes.id" rel="'.$class->id.'"');
	}
	$list_items[] =  write_menu_item($lang['by_class'], '', write_html('ul', 'class="scrollable"',implode('', $classes_items)), 'e');

	foreach ($groups as $group){
		$groups_items[] =  write_menu_item($group->getName(), 'module="students" action="getStudentlist" field="'.DB_year.'.groups.id" rel="'.$group->id.'"');
	}
	$list_items[] =  write_menu_item($lang['by_group'], '', write_html('ul', 'class="scrollable"',implode('', $groups_items)), 'e'); 
	
	$student_list =  write_menu_item($lang['students_lists'], '', write_html('ul', '',implode('', $list_items)), 'e');
	
	return write_menu_item(
		$lang['students'],
		'', 
		write_html('ul', '', 
			$add_item.
			$search_item.
			$student_list
		),
		's'
	);
	
}

function write_search_parent_items(){
	global $etabs, $grades, $classes, $groups, $lang, $prvlg;
	$etabs_items = array();
	$grades_items = array();
	$classes_items = array();
	$groups_items = array();
	
	if($prvlg->_chk('parents_read')){
		$parent_lis = array();
		$parent_lis[] = write_menu_item($lang['all'], 'module="parents" action="getParentlist" field="all"');
		$parent_lis[] = write_menu_item($lang['by_name'], 'module="parents" action="openParentSeachByName"');
		$parent_lis[] = write_menu_item($lang['by_id'], 'module="parents" action="openParentSeachById"');
		$list_items = array();
		
		if($prvlg->_chk('resource_read_levels')){
			if(in_array($_SESSION['group'], array('superadmin', 'admin'))){
				foreach ($etabs as $etab){
					$etabs_items[] =  write_menu_item($etab->getName(), 'module="parents" action="getParentlist" field="etab" rel="'.$etab->id.'"');
				}
				$list_items[] =  write_menu_item($lang['by_etab'], '', write_html('ul', 'class="scrollable"', implode('', $etabs_items)), 'e');
			}
	
			foreach ($grades as $grade){
				$grades_items[] =  write_menu_item($grade->getName(), 'module="parents" action="getParentlist" field="grade" rel="'.$grade->id.'"');
			}
			$list_items[] =  write_menu_item($lang['by_level'], '', write_html('ul', 'class="scrollable"',implode('', $grades_items)), 'e');
		}
		
		foreach ($classes as $class){
			$classes_items[] =  write_menu_item($class->getName(), 'module="parents" action="getParentlist" field="class" rel="'.$class->id.'"');
		}
		$list_items[] =  write_menu_item($lang['by_class'], '', write_html('ul', 'class="scrollable"',implode('', $classes_items)), 'e');

		foreach ($groups as $group){
			$groups_items[] =  write_menu_item($group->getName(), 'module="parents" action="getParentlist" field="group" rel="'.$group->id.'"');
		}
		$list_items[] =  write_menu_item($lang['by_group'], '', write_html('ul', 'class="scrollable"',implode('', $groups_items)), 'e'); 
	
		$parent_lis[] =  write_menu_item($lang['parent_list'], '', write_html('ul', '',implode('', $list_items)), 'e');
		
		return write_menu_item(
			$lang['parents'],
			'', 
			write_html('ul', '', 
				implode('',$parent_lis)
			),
			's'
		);
	} else {
		return '';
	}
}

function write_reports_items(){
	global $lang;
	$reports_lis = array();
	$reports_lis[] = write_menu_item($lang['school_statics'], 'module="reports" action="openSchoolStatic"');
	$reports_lis[] = write_menu_item($lang['school_balance'], 'module="reports" action="schoolBalance"');
	$reports_lis[] = write_menu_item($lang['ministry_balance'], 'module="reports" action="ministryBalance"');
	$reports_lis[] = write_menu_item($lang['redoubling_report'], 'module="reports" action="openRedoublingList"');
	$reports_lis[] = write_menu_item($lang['waiting_list'], 'module="reports" action="openWaitingList"');
	$reports_lis[] = write_menu_item($lang['reg_report'], 'module="reports" action="openRegistrationReport"');
	$reports_lis[] = write_menu_item($lang['reservations'], 'module="reports" action="openSuspendedList"');
	// Quited list
	$quited_reasons = array();
	$quited_reasons[] = write_menu_item($lang['all'], 'module="reports" action="openQuitList" reason="all"');
	$quited = SchoolReport::getQuitReasons();
	foreach($quited as $reason ){
		if($reason->suspension_reason != ''){
			$quited_reasons[] = write_menu_item($reason->suspension_reason, 'module="reports" action="openQuitList" reason="'.$reason->suspension_reason.'"');
		}
	}
	$reports_lis[] =  write_menu_item($lang['quit_list'], '', write_html('ul', '',implode('', $quited_reasons)), 'e');
	/*// Suspended list
	$suspended_reasons = array();
	$suspended_reasons[] = write_menu_item($lang['all'], 'module="reports" action="openSuspendedList" reason="all"');
	$suspended = SchoolReport::getSuspensionReasons();
	foreach($suspended as $reason){
		if($reason->suspension_reason != ''){
			$suspended_reasons[] = write_menu_item($reason->suspension_reason, 'module="reports" action="openSuspendedList" reason="'.$reason->suspension_reason.'"');
		}
	}
	$reports_lis[] =  write_menu_item($lang['suspended_list'], '', write_html('ul', '',implode('', $suspended_reasons)), 'e');*/
	
	return write_menu_item(
		$lang['reports'],
		'', 
		write_html('ul', '', 
			implode('',$reports_lis)
		),
		's'
	);
}

?>