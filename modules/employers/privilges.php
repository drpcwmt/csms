<?php
// Employers Privilegs

$privilges = array(
	'emp_read',
	'emp_edit'
);
if($this_system->type == 'hrms'){
	$privilges[] ='emp_add';
	$privilges[] ='read_emp_evaluation';
}
?>