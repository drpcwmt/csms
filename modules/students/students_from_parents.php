<?php
## SELECT STUDENT FROM parent

function createStudentTable($stds){
	global $lang;
	$std_list_html = "";
	foreach($stds as $std){
		$std_list_html .= write_html('tr', '',
			write_html('td', '',
				'<input type="checkbox" value="'.$std->id.'" name="std_id[]" />'
			).
			write_html('td', '', $std->getName())
		);
	}
	$student_list_table =write_html('table', 'class="tablesorter"', 
		write_html('thead', '',
			write_html('tr', '',
				write_html('th', 'width="20" style="background-image:none"', 
					'<input type="checkbox" title="'.$lang['select_all'].'" class="select_all" name="all" />'
				).
				write_html('th', '', $lang['name'])
			)
		).
		write_html('tbody', '',$std_list_html)	
	);

	return $student_list_table;
}


if(isset($_GET['group_id'])){
	$group_id = safeGet($_GET['group_id']);
	$group = new Groups($group_id, $sms);
	$stds = $group->getStudents();
	echo write_html('div', 'class="selClass" id="selGroupDiv-'.$group_id.'"', 
		 write_html('h2', 'class="title"', $group->getName()).
		 createStudentTable($stds)
	);

}elseif(isset($_GET['class_id'])){
	$class_id = safeGet($_GET['class_id']);
	$class = new Classes($class_id, $_SESSION['year'], $sms);
	$stds = $class->getStudents(array('1'));
	echo write_html('div', 'class="selClass" id="selClassDiv-'.$class_id.'"', 
		write_html('h2', 'class="title"', $class->getName()).
		createStudentTable($stds)
	);
	
} else {
	if(isset($_GET['level_id'])){
		$level = new Levels(safeGet($_GET['level_id']), $sms);
		$class_list = array();
		$classes = $level->getClassList();
	} else {
		$classes = Classes::getList();
	}
	
	$class_list_html ='';
	$class_no =0;
	if($classes != false && count($classes) > 0){
		foreach($classes as $class){
			if($class_no == 0){
				$cur_class_id = $class->id;
				$cur_class_name = $class->getName();
			}
			$groups = $class->getGroups();
			$groups_lis = '';
			if($groups != false && count($groups) > 0 ){
				foreach($groups as $group){
					$groups_lis .= write_html('li', 'class="hand hoverable clickable ui-corner-all" onclick="loadStdFromGroup('.$group->id.', this)"', 
						'<input type="checkbox" name="group[]" value="'.$group->id.'" />'.
						$group->getName()	
					);
				}
			}
			
			$class_list_html .= write_html('li', 'class="hand hoverable clickable ui-corner-all '.($class_no == 0 ? 'ui-state-active' : '').'" onclick="loadStdFromClass('.$class->id.', this)"', 
				'<input type="checkbox" name="class[]" value="'.$class->id.'" />'.
				write_html('a', 'class="ui-state-default rev_float mini_circle_button" onclick="expandList(this)"', write_icon('triangle-1-s')).
				$class->getName()
			).
			($groups > 0 ? 
				write_html('ul', 'class="hidden list_menu listMenuUl ui-corner-bottom ui-widget-content" style="margin:0px; padding:2px 8px"', $groups_lis)
			: '');
			$class_no++;
		}
		
		$cur_class = reset($classes);	
		$stds = $cur_class->getStudents(array('1'));
		$class_id = $cur_class->id;
		$class_name = $cur_class->getName();
		$student_list_table = createStudentTable($stds);
	} else {
		$student_list_table = '';
		$cur_class_id = '';
		$cur_class_name = '';
	}
	
	echo write_html('form', '', 
		write_html('table', 'class="layout"',
			write_html('tr', '',
				write_html('td', 'width="240" valign="top"',
					write_html('ul', 'class="list_menu listMenuUl" style="max-height:450px; overflow:auto"',  
						$class_list_html
					)
				).
				write_html('td', 'class="class_std_list" valign="top"', 
					write_html('div', 'class="selClass" id="selClassDiv-'.$cur_class_id.'"', 
						write_html('h2', 'class="title ui-state-highlight" style="padding:10px 20px; margin:0px"', $cur_class_name).
						$student_list_table
					)
				)
			)
		)
	);
}
?>
