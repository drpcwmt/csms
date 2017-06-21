<?php
## ui layout;
require_once('main_nav.php');
require_once('block_tools.php');
include_once('modules/home/home.php');

function getBlockToolsModuleLink(){
	global $lang;
	return write_html('div', 'class="ui-corner-bottom ui-widget-content "',
		write_html('span', 'title="'.$lang['profil'].'" class="hand hoverable clickable ui-corner-all ui-state-default mainnav_loader"  module="profil"', 
			'<img src="assets/img/profil.png" border="0" width="32" height="32" />'
		).
		(getPrvlg('setting_read') ? 
			write_html('span','title="'.$lang['general'].', '.$lang['users'].', '.$lang['layout'].'..." class="hand hoverable clickable ui-corner-all ui-state-default mainnav_loader" module="settings" after="iniSettings"', 
				'<img src="assets/img/settings.png" border="0" width="32" height="32" />'
			)
		:'').
		write_html('span', 'title="'.$lang['logout'].'" class="hoverable ui-corner-all ui-state-default"',
			write_html('a', 'href="modules/login/logout.php" title="'.$lang['logout'].'"', 
				'<img src="assets/img/logout.png" border="0" width="32" height="32" />'
			)
		)
	);
}

function getBlockToolsSelectLi($tool_name, $tool_select){
	global $lang;
	return write_html('table', 'border="0" cellspacing="0" cellpadding="0"', 
		write_html('tr', '',
			write_html('td', 'width="70" valign="middel" class="reverse_align"', 
				write_html('label', 'class="label ui-widget-header ui-corner-left" style="padding:4px 3px 3px"', $tool_name)
			).
			write_html('td', 'valign="top"', $tool_select)
		)
	);
}

$layout = new Layout();
$layout->select_tool = (isset($tool_name) ? getBlockToolsSelectLi($tool_name, $tool_select): '');

$layout->nav_buttons = getBlockToolsModuleLink();

$layout->main_nav = $main_nav;
$layout->logo_path = $this_system->getLogo();
$layout->top_td = write_html('h3', 'style="margin:15px 5px 5px;"', $user_name).
	write_html('h5', ' class="block_tool_h5"', 
		$lang[$_SESSION['group']]
	);

$layout->home_output = $home_output;
$layout->template = "ui/templates/layout.tpl";
$layout_table = $layout->_print();
?>