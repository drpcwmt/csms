<?php

function write_menu_item($title, $attr,  $content='',$icon=false){
	return write_html('li','',
		write_html('a', 'class="ui-state-default hoverable" '.$attr, $title). 
		($icon != false ?  write_icon("triangle-1-$icon") : '').
		$content
	);
}

function buildSub($items, $con){
	$out = '';
	foreach($items as $item){
		$out .= write_menu_item($item->getName(), 'action="addEventCon" con="'.$con.'" confilter="'.$item->id.'"');
	}
	return $out;
}
// Etabs 
$etabs = Etabs::getList();
$etabs_list = write_html('ul', '', buildSub($etabs, 'etab'));
$levels = Levels::getList();
$levels_list = write_html('ul', '', buildSub($levels, 'level'));
$classes = Classes::getList();
$classes_list = write_html('ul', '', buildSub($classes, 'class'));


$menu_item = array();
$menu_item['student'] = array();
$menu_item['parent'] = array();
$menu_item['admin'] = array('all','prof', 'supervisor', 'principal', 'admin', 'student', 'parent');
$menu_item['superadmin'] =  array('all', 'prof', 'supervisor', 'principal', 'admin', 'student', 'parent');
$menu_item['principal'] = array('admin', 'student', 'parent', 'prof', 'supervisor', 'principal');
$menu_item['coordinator'] = array('admin', 'student', 'parent', 'prof', 'supervisor', 'principal');
$menu_item['supervisor'] = array('prof', 'admin', 'student', 'parent');
$menu_item['prof'] = array('prof', 'admin', 'student', 'parent');;

$li['all'] = write_menu_item(
	$lang['school'], 'action="addEventCon" con="etab" confilter="0"'
);

$li['student'] =  write_menu_item(
	$lang['students'], '',
	write_icon("triangle-1-e").
	write_html('ul', '', 
		write_menu_item($lang['by_name'], 'action="addEventCon" con="student" confilter="name"').
		write_menu_item($lang['browse'], 'action="addEventCon" con="student" confilter="browse"').
		write_menu_item(
			$lang['by_etab'],
			'',
			write_html('ul', '', buildSub($etabs, 'etab')),
			'e'
		).
		write_menu_item(
			$lang['by_level'],
			'',
			write_html('ul', '', buildSub($levels, 'level')),
			'e'
		).
		write_menu_item($lang['all'], 'action="addEventCon" con="student" confilter="0"')
	, 's')
);

$li['parent'] =  write_menu_item(
	$lang['parents'], '',
	write_html('ul', '', 
		write_menu_item($lang['by_name'], 'action="addEventCon" con="parent" confilter="name"').
		write_menu_item(
			$lang['by_level'],
			'',
			write_html('ul', '', buildSub($levels, 'parent-level')),
			'e').
		write_menu_item(
			$lang['by_class'],
			'',
			write_html('ul', '', buildSub($classes, 'parent-class')),
			'e').
		write_menu_item($lang['all'], 'action="addEventCon" con="parent" confilter="0"')
	)
, 'e');

$li['prof'] =  write_menu_item(
	$lang['profs'], '',
	write_icon("triangle-1-e").
	write_html('ul', '', 
		write_menu_item($lang['by_name'], 'action="addEventCon" con="prof" confilter="name"').
		write_menu_item($lang['by_class'], 'action="addEventCon" con="prof" confilter="class"').
		write_menu_item($lang['by_level'], 'action="addEventCon" con="prof" confilter="level"').
		write_menu_item($lang['by_etab'], 'action="addEventCon" con="prof" confilter="etab"').
		write_menu_item($lang['all'], 'action="addEventCon" con="prof" confilter="0"')
	, 'e')
);

$li['supervisor'] =  write_menu_item(
	$lang['supervisors'], '',
	write_icon("triangle-1-e").
	write_html('ul', '', 
		write_menu_item($lang['by_name'], 'action="addEventCon" con="supervisor" confilter="name"').
		write_menu_item($lang['by_etab'], 'action="addEventCon" con="supervisor" confilter="etab"').
		write_menu_item($lang['all'], 'action="addEventCon" con="supervisor" confilter="0"')
	, 'e')
);

$li['principal'] =  write_menu_item(
	$lang['principals'], '',
	write_icon("triangle-1-e").
	write_html('ul', '', 
		write_menu_item($lang['by_name'], 'action="addEventCon" con="principal" confilter="name"').
		write_menu_item($lang['by_level'], 'action="addEventCon" con="principal" confilter="level"').
		write_menu_item($lang['by_etab'], 'action="addEventCon" con="principal" confilter="etab"').
		write_menu_item($lang['all'], 'action="addEventCon" con="principal" confilter="0"')
	, 'e')
);

$li['admin'] =  write_menu_item(
	$lang['admins'], '',
	write_icon("triangle-1-e").
	write_html('ul', '', 
		write_menu_item($lang['by_name'], 'action="addEventCon" con="admin" confilter="name"').
		write_menu_item($lang['all'], 'action="addEventCon" con="admin" confilter="0"')
	, 'e')
);



$menus = $menu_item[$_SESSION['group']];
foreach($menus as $key){
	$out[] = $li[$key];
}

$con_menus = write_html('ul', 'id="calender_con_menus" class="nav"', implode('', $out));

?>