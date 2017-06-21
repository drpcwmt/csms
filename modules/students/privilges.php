<?php
// Student Privilegs

$privilges = array(
	'std_read',
	'std_edit',
	'parents_read',
	'parents_edit'
);
if($this_system->type=='sms'){
	$privilges[] = 'std_add';
	$privilges[] = 'std_login_infos';
	$privilges[] = 'parents_login_infos';
}
?>