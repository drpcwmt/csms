<?php
// Menue

$widget = array();
$widget['superadmin'] = array( 
	'lp' => array('commands'),
	'rp' => array('system')
);

$widget['supervisor'] = array( 
	'lp' => array('commands', 'messages'),
	'rp' => array()
);

$widget['user'] = array( 
	'lp' => array('commands'),
	'rp' => array('messages')
);


/********************************************/
$cur_widgets = $widget[$_SESSION['group']];

$layout = new stdClass();
//include('home_menus.php');
//$layout->menu = $home_menus;
$layout->date = date("D d/m/Y");
$layout->rp_width = count($cur_widgets['rp']) > 0 ? '25%' : '0';

$layout->lpr_widgets = '';
$layout->lpl_widgets ='';
$layout->full_width = write_html('tr', '',
	write_html('td', 'colspan="2"',
		write_html('div', 'id="home_commands" class="double"',
			write_html('fieldset', '',
				write_html('legend', '', $lang['new_order']).
				Commands::newForm()
			)
		)
	)
);

$i = 0;
foreach($cur_widgets['lp'] as $widget){
	if(file_exists("modules/$widget/$widget"."_widget.php")){
		include("modules/$widget/$widget"."_widget.php");
	}
	$i++;
	if($i % 2 == 0){
		$layout->lpr_widgets .= $widget;
	} else {
		$layout->lpl_widgets .= $widget;
	}
}



$home_output = fillTemplate("modules/home/templates/home.tpl", $layout);

if(isset($_GET['module']) && safeGet($_GET['module']) == 'home'){
	echo $home_output;
}
?>
