<?php
// Menue
$widget = array();

$widget['superadmin'] = array( 
	'lp' => array(),
	'rp' => array('system', 'connections')
);

$widget['cashier'] = array( 
	'lp' => array(),
	'rp' => array()
);

/********************************************/
$cur_widgets = $widget[$_SESSION['group']];

$layout = new stdClass();
$layout->menu = '';
$layout->date = date("D d/m/Y");
$layout->rp_width = count($cur_widgets['rp']) > 0 ? '25%' : '0';
$layout->lpl_widgets = '';
$layout->lpr_widgets = '';
$i = 0;
$modules = scandir('modules');
foreach($modules as $module){
	if(!in_array($module, $cur_widgets['lp']) && !in_array($module, $cur_widgets['rp'])){
		if(file_exists("modules/$module/$module"."_widget.php") && $module != 'system'){
			include("modules/$module/$module"."_widget.php");
			$i++;
			if($i % 2 == 0){
				$layout->lpr_widgets .= $widget;
			} else {
				$layout->lpl_widgets .= $widget;
			}
		}
	}
}

$layout->rp_widgets = '';
foreach($cur_widgets['rp'] as $module){
	if(file_exists("modules/$module/$module"."_widget.php")){
		include("modules/$module/$module"."_widget.php");
		$layout->rp_widgets .= $widget;
	}
}

$home_output = fillTemplate("modules/home/templates/home.tpl", $layout);

if(isset($_GET['module']) && safeGet($_GET['module']) == 'home'){
	echo $home_output;
}
?>
