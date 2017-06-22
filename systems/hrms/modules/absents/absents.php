<?php
## Absents 
if($prvlg->_chk('absents_read') == false){die(write_error($lang['restrict_accses']));};

if(isset($_GET['insert'])){
	echo json_encode(Absents::insertAbs($_POST['emp_id'], dateToUnix($_POST['date'])));

} elseif(isset($_GET['update'])){
	echo json_encode(Absents::updateAbs($_POST));

} elseif(isset($_GET['delete'])){
	echo json_encode(Absents::deleteAbs($_POST['id']));

} elseif(isset($_GET['report'])){
	echo Absents::loadAbsentReport(safeGet('job_id'), safeGet('month'));
	
	
} elseif(isset($_GET['getlist'])){
	$emp_id = safeGet('emp_id');
	echo Absents::getList($emp_id);

} elseif(isset($_GET['emp_id'])){
	if(isset($_GET['period'])){
		include('employers_absents.php');	
	} else {
		$emp = new Employers(safeGet('emp_id'));
		echo Absents::getEmpYearAbs($emp);
	}
} elseif(isset($_GET['absbyjob'])){
	$date = isset($_GET['date']) ? dateToUnix(safeGet('date')) : '';
	echo Absents::loadDailyAbsent($date, safeGet('absbyjob'));
	
} else {
	echo Absents::loadMainLayout();
}
?>