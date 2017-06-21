<?php
// Menue
$widget = array();
$widget['student'] = array( 
	'lp' => array( 'notes', 'todo', 'messages'),
	'rp' => array('schedule')
);

$widget['parent'] = array( 
	'lp' => array('messages'),
	'rp' => array()
);

$widget['prof'] = array( 
	'lp' => array('class_summary', 'notes', 'todo', 'messages'),
	'rp' => array('schedule')
);

$widget['supervisor'] = array( 
	'lp' => array('class_summary', 'notes', 'todo', 'messages'),
	'rp' => array('schedule')
);

$widget['principal'] = array( 
	'lp' => array(  'messages', 'new_year'),
	'rp' => array()
);

$widget['coordinator'] = array( 
	'lp' => array(  'messages', 'new_year'),
	'rp' => array()
);

$widget['admin'] = array( 
	'lp' => array(  'messages', 'new_year'),
	'rp' => array()
);

$widget['superadmin'] = array( 
	'lp' => array( 'messages', 'new_year'),
	'rp' => array('system', 'connections')
);

/********************************************/
$cur_widgets = $widget[$_SESSION['group']];

$layout = new Layout();
include('home_menus.php');
$layout->menu = $home_menus;
$layout->date = date("D d/m/Y");
$layout->rp_width = count($cur_widgets['rp']) > 0 ? '25%' : '0';
$layout->lpl_widgets = '';
$layout->lpr_widgets = '';
$i = 0;
foreach($cur_widgets['lp'] as $widget){
	if($widget == 'class_summary'){
		include('class_summary.php');
	} elseif($widget == 'homework'){
	//	include('modules/lms/homework_widget.php');
	} elseif($widget == 'notes'){
		include('modules/lessons/notes_widget.php');
	} else {
		if(file_exists("modules/$widget/$widget"."_widget.php")){
			include("modules/$widget/$widget"."_widget.php");
		} else {
		//echo "modules/$widget/$widget"."_widget.php";
	}
	}
	$i++;
	if($i % 2 == 0){
		$layout->lpr_widgets .= $widget;
	} else {
		$layout->lpl_widgets .= $widget;
	}
}

$layout->rp_widgets = '';
foreach($cur_widgets['rp'] as $widget){
	if(file_exists("modules/$widget/$widget"."_widget.php")){
		include("modules/$widget/$widget"."_widget.php");
		$layout->rp_widgets .= $widget;
	}
}

$home_output = fillTemplate("modules/home/templates/home.tpl", $layout);

if(isset($_GET['module']) && safeGet($_GET['module']) == 'home'){
	echo $home_output;
}
?>