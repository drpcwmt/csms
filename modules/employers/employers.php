<?php
## employers activity
if(isset($_GET['hrms_id'])){
	$hrms = new HrMS(safeGet($_GET['hrms_id']));
} else {
	$hrms = new HrMS();
}

// Set privileged jobs
$cur_jobs = Jobs::getList(true);
$cur_jobs_array = objectsToArray($cur_jobs);

if(isset($_GET['new'])){
	if($prvlg->_chk('emp_add')){
		echo Employers::_new();
	} else {
		echo write_error($lang['error_no_privilege']);
	}
} elseif(isset($_GET['save'])){
	echo json_encode_result(Employers::_save($_POST));
} elseif(isset($_GET['id'])){
	$employer = new Employers(safeGet($_GET['id']));
	if(array_key_exists($employer->job_code, $cur_jobs_array)){
		echo $employer->loadLayout();
	} else {
		echo write_error($lang['no_privilege']);
	}
} elseif(isset($_GET['autocomplete'])){
	$value = trim($_GET['term']);
	$where = isset($_GET['w']) ? 'AND ('.str_replace(';', ' AND ', str_replace('-', '=', $_GET['w'])).')' : "";
	print Employers::getAutocomplete( $value, $where);

} elseif(isset($_GET['browse'])){
	$job = new Jobs(safeGet('job_id'));
	echo write_html('form', '', $job->getEmpTable(isset($_GET['cc']) ? safeGet('cc') : 0, isset($_GET['action']) ? safeGet('action') : ''));
	
} elseif(isset($_GET['jobs'])){
	if(isset($_GET['job_id'])){
		$job_id = safeGet($_GET['job_id']);
		$job = new Jobs($job_id);
		echo $job->loadLayout();
	}
} else {
	echo Employers::loadMainLayout();
}

?>