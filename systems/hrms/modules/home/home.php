<?php
// Menue
$widget = array();

$widget['hr'] = array( 
	'lp' => array(),
	'rp' => array()
);

$widget['superadmin'] = array( 
	'lp' => array( ),
	'rp' => array('system')
);

/********************************************/
$cur_widgets = $widget[$_SESSION['group']];

$layout = new stdClass();
$layout->menu = fillTemplate('modules/home/templates/home_menu.tpl', array());
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
