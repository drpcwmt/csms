<?php
$links= array(
	'home' =>  array(
		'module' =>'home', 
		'title' => $lang['home'], 
		'icon' => 'home.png', 
		'after' => 'initiateHomeScreen',
		'tooltip' => $lang['dashboard']
	),
	'calender' => array(
		'module' =>'calendar', 
		'title' => $lang['calender'], 
		'icon' => 'calender.png', 
		'after' => '',
		'tooltip' => $lang['calender'].', '. $lang['events'].'...'
	)
);

// MSGMS link
if(MS_codeName!='sms_basic'){
	$curUserUnreadMsg = Messages::countUnread();
	
	$links['msg'] = array(
		'module' =>'messages', 
		'title' => $lang['msg'] . write_html('span', 'id="count_new_msgs"', ($curUserUnreadMsg > 0 ? '('.$curUserUnreadMsg.')' : '')), 
		'icon' => 'mail_'.($curUserUnreadMsg > 0 ? 'full' : 'empty').'.png', 
		'after' => ''
	);
	$links['docs'] = array(
		'module' =>'documents', 
		'title' => $lang['documents'], 
		'icon' => 'docs.png', 
		'after' => 'initDocumentModule',
		'tooltip' => $lang['documents'] .', '. $lang['shared'].', '.$lang['librarys'].'...' 
	);
}

// DOCS link
if(MS_codeName=='sms_elearn'){
	$links['docs'] = array(
		'module' =>'documents', 
		'title' => $lang['documents'], 
		'icon' => 'docs.png', 
		'after' => 'initDocumentModule',
		'tooltip' => $lang['documents'] .', '. $lang['shared'].', '.$lang['librarys'].'...' 
	);
}

// Resources link
if(getPrvlg('resource_read%')){
	$links['resources'] = array(
		'module' =>'resources', 
		'title' => $lang['resources'], 
		'icon' => 'resources.png', 
		'after' => '',
		'tooltip' => $lang['profs'].', '.$lang['classes'].', '.$lang['materials'].'...' 
	);
}

// Mobile Devices Link

if(MS_codeName=='sms_elearn' && in_array($_SESSION['group'], array('prof', 'supervisor'))){
	$links['m'] = array(
		'module' =>'m', 
		'title' => $lang['tablet'], 
		'icon' => 'tablet.png', 
		'after' => '',
		'tooltip' => $lang['mobile_devices'].'...' 
	);
}

// Library link
if($this_system->getSettings('libms_server')==1 && in_array($_SESSION['group'], array('prof', 'supervisor', 'student'))){
	$links['library'] = array(
		'module' =>'library', 
		'title' => $lang['library'], 
		'icon' => 'library.png', 
		'after' => '',
		'tooltip' => $lang['library'].'...' 
	);
}

foreach($links as $but){
	if($but == $links['home']){
		$but['active'] = "ui-state-active";
	}
	$buttons[] = fillTemplate('ui/templates/man_nav_but.tpl', $but);
}

$main_nav = write_html('ul', 'class="main_nav"', implode('', $buttons));
?>
