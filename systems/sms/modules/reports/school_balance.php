<?php

// Note: add select status to display
function getCountStudents($con, $con_id){
	$item = new StudentsList($con, $con_id);
	$item->stats = array('1');
	return $item->getCount();
}

$etabs = Etabs::getList();
$count_all_std = 0;
$count_all_class = 0;
$trs = array();
$serial = 1;
$etab_arr  = array();
foreach($etabs as $etab){
	$etab_id = $etab->id;
	$etab_name = $etab->getName();
	$std_by_etab = 0;
	$class_by_etab = 0;
	$levels = $etab->getLevelList(); 
	foreach($levels as $level){
		$level_id = $level->id;
		$level_name = $level->getName();
		$std_by_level = getCountStudents('level', $level->id, true);
		$std_by_etab = $std_by_etab + $std_by_level;
		$class_by_level = count($level->getClassList());
		$class_by_etab = $class_by_etab + $class_by_level;
		$count_all_std = $count_all_std + $std_by_level;
		$count_all_class =$count_all_class +$class_by_level;
		$trs[] = write_html('tr', 'style="font-weight:bold;"',
			write_html('td', '', $serial).
			write_html('td', '', $level->getName()).
			write_html('td', '', $class_by_level).
			write_html('td', '', $std_by_level )
		);
		$serial++;
		$classes = $level->getClassList();
		foreach($classes as $class){
			$trs[] = write_html('tr', 'class="class_tr hidden"',
				write_html('td', '', '').
				write_html('td', '', $class->getName()).
				write_html('td', '', '').
				write_html('td', '', getCountStudents('class', $class->id, (array(1,3))))
			);
		}
	}
	$trs[] = write_html('tr', 'style="font-weight:bold"',
		write_html('td', 'class="ui-state-default" colspan="2"', $lang['total'].' '.$lang['level'].' '. $etab_name).
		write_html('td', 'class="ui-state-default"', $class_by_etab).
		write_html('td', 'class="ui-state-default"', $std_by_etab)
	);
}
$trs[] = write_html('tr', 'style="font-weight:bold; font-size:14px"',
	write_html('td', 'class="ui-state-default" colspan="2"', $lang['total_school']).
	write_html('td', 'class="ui-state-default"', $count_all_class).
	write_html('td', 'class="ui-state-default"', $count_all_std)
);
	
$thead = write_html('thead', '', 
	write_html('tr', '',
		write_html('th', 'width="20"', $lang['ser']).
		write_html('th', '', $lang['level']).
		write_html('th', '', $lang['count_classes']).
		write_html('th', '', $lang['count_student'])
	)
);

$school_balance = write_html('h2', '', $lang['school_balance_title'].' '. $_SESSION['year'].'/'. ($_SESSION['year']+1)).
write_html('table', 'class="tablesorter"', 
	$thead.
	write_html('tbody', '', implode($trs, ''))
);
?>