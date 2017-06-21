<?php

$user_id = $_SESSION['user_id'];
$user_group = $_SESSION['group'];

$user = $this_system->getAnyObjById($user_group, $user_id);
if($user == false){
	$user = new Employers( $user_id);
}
$user_name = $user->getName();


	// admin superadmin principal accounting
if(in_array($user_group, array("admin", "superadmin", 'principal', 'coordinator'))){ // admins
	$all_year = getYearsArray();
	$year_select = array();
	foreach($all_year as $year){
		$year_select[$year] = $year.'/'.($year+1);
	}
	$tool_name = $lang['year'];
	$tool_select = write_html('a', 'class="icon_button hoverable ui-state-default" module="new_year" action="startNewYear" title="'.$lang['new_year'].'"', write_icon('plus')).
	(count($year_select) > 0 ?
		write_html_select('name="year" class="combobox" update="changeYear" id="session_year"', $year_select, $_SESSION['year'])
	: 
		write_html('div', 'class="ui-widget-content ui-corner-right fault_input"', $lang['not_defined'])
	);
	
	// Profs Supervisors
} elseif(in_array($user_group, array("prof", "supervisor"))){ // profs and supervisors
	$class_select = array();
	$classes = $user->getClassList();
	if($classes != false && count($classes) > 0){
		foreach($classes as $class){
			$class_select[$class->id] = $class->getName();
		}
		$tool_name = $lang['class'];
		if(!isset($_SESSION['cur_class'])){
			$_SESSION['cur_con'] = 'class';
			if($user_group == 'prof'){
				$cur_class = $user->getCurClass();
				if($cur_class == false){
					$cur_class = $class->id;
				} 					
			} else {
				$cur_class = $class->id;
				
			}
			$_SESSION['cur_class'] = $cur_class;
		}
	}
	
	if(count($classes) == 0){
		$tool_select = write_html('div', 'class="ui-widget-content ui-corner-right fault_input"', $lang['not_defined']);
	} elseif(count($classes) === 1){
		$cur_class = reset($classes);
		$_SESSION['cur_class'] = $cur_class->id;
		$tool_select = write_html('div', 'class="ui-widget-content ui-corner-right fault_input"', $cur_class->getName());
	} else {
		$tool_select = write_html_select('id="cur_class" class="combobox" update="changeCurClass"', $class_select, $_SESSION['cur_class']);
	}	
	
	// Parents
} elseif($user_group == 'parent'){
	$childrens = $user->getChildrens();
	$childrens_select = array();
	if(!isset($_SESSION['std_id'])){
		$first_child = $childrens[0];
		$_SESSION['std_id'] = $first_child->id;
	} 
		// write class name for the selected student
	$student = new Students($_SESSION['std_id']);
	$class_name = $student->getClass()->getName();
	
	foreach($childrens as $child){
		$childrens_select[$child->id] = $child->getName();
	}
	$tool_name = $lang['student'];
	$tool_select = write_html_select('id="cur_std" class="combobox" update="changeCurStd"', $childrens_select, $_SESSION['std_id']);

	// Students	
} elseif($user_group == 'student'){
	$_SESSION['std_id'] = $user_id;
	$student = new Students($user_id);
	$class_name = $student->getClass()->getName();
} 

/************************************************/

/*$block_tools = write_html('ul', 'id="tools_ul"',
	write_html('li', 'class="ui-corner-bottom ui-widget-header" style="width:355px; text-align:center"',
		write_html('h3', 'style="margin:15px 5px 5px;"', $user_name).
		(in_array($user_group, array("prof", "supervisor", "student", "parent")) ?
			write_html('h5', ' class="block_tool_h5"', 
				$_SESSION['year'].' / '. ($_SESSION['year']+1).
				(in_array($user_group, array("student", "parent")) ?
					' / '.$class_name
				: '')
			)
		: 
			write_html('h5', ' class="block_tool_h5"', $lang[$_SESSION['group']])
		)
	).
	getBlockToolsModuleLink().
	(isset($tool_name) ? 
		getBlockToolsSelectLi($tool_name, $tool_select)
	: '')
);*/
/************************************************/
?>