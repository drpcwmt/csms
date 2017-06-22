<?php
## Members
require_once('members_functions.php');
$dialog_mode = isset($_GET['dialog']) ? true : false;

if(isset($_GET['class_id'])){
	$c = explode('-', $_GET['class_id']);
	$class_id = $c[1];
	$school_code = $c[0];
	require_once('class_list.php');
	if ( !$dialog_mode ){
		echo write_html('div', 'class="ui-corner-top ui-widget-header reverse_align" style="padding:5px"',
			write_html('h3', 'class="title_wihte"', $lang['class_list'].': '.getClassNameById($class_id, $school_code))
		).
		write_html('div', 'class="ui-corner-bottom ui-widget-content"',
			$class_list_toolbox.
			$class_list	
		);
	} else {
		echo $class_list;
	}
	exit;
}

if(isset($_GET['stdid'])){
	$id = $_GET['stdid'];
	$server = isset($_GET['server']) ? $_GET['server'] : $MS_settings['sms_server_code'];
	$type = 'std';
	$member_type = "sms";
	$member_name = getStudentNameById($id, $server);
}elseif(isset($_GET['empid'])){
	require_once('scripts/hrms_functions.php');
	$id = $_GET['empid'];
	$server = isset($_GET['server']) ? $_GET['server'] : $MS_settings['hrms_server_code'];
	$type = 'emp';
	$member_type = "hrms";
	$member_name = getEmployerNameById($id, $server);
}
$profiler_data = "q=$type&server=$server&id=$id";


require_once('members_form.php');
echo write_html('div', 'class="tabs"',
	write_html('ul', '',
		write_html('li', '', write_html('a', 'href="#member_data"', $lang['borrows'])).
		write_html('li', '', write_html('a', 'href="index.php?module=profiler&'.$profiler_data.'"', $lang['personel_infos']))
	).
	write_html('div', 'id="member_data"', 
		write_html('h2', 'class="title"', $member_name).
		write_html('fieldset', '',
			write_html('legend', '', $lang['borrows']).
			$member_form
		).
		write_html('fieldset', '',
			write_html('legend', '', $lang['history']).
			write_html('div', 'id="member_history"', '')
		)
	)
);
?>


