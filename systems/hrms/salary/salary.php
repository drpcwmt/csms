<?php
## salary

if(isset($_GET['profil'])){
	if(isset($_GET['new'])){
		echo SalaryProfil::newProfil();
	} elseif(isset($_GET['save'])){
		echo SalaryProfil::_save($_POST);
	} elseif(isset($_GET['delete'])){
		echo json_encode_result(SalaryProfil::_delete($_POST['profil_id']));
	} elseif(isset($_GET['newelement'])){
		echo SalaryProfil::newElementForm($_GET['type']);
	} elseif(isset($_GET['saveelmnt'])){
		echo json_encode(SalaryProfil::saveNewElemnt($_POST));
	} elseif(isset($_GET['delelmnt'])){
		echo json_encode(SalaryProfil::deleteElemnt($_POST));
	}elseif(isset($_GET['profil_id'])){
		$profil = new SalaryProfil(safeGet('profil_id'));
		echo $profil->loadLayout();
	} else {
		echo SalaryProfil::loadMainLayout();
	}
} elseif(isset($_GET['salary_report'])){
	if(!isset($_GET['profil_id'])){
		$profils = SalaryProfil::getList();
		$profil_id = $profils[0]->id;
	} else {
		$profil_id = safeGet('profil_id');
	}
	$month = isset($_GET['month']) ? safeGet('month') : date('m');
	$cc = isset($_GET['cc']) && $_GET['cc']!= ' ' ? safeGet('cc') : '';
	$payment_mode = isset($_GET['payment_mode']) ? safeGet('payment_mode') : 0;
//	$dates = Absents::getDateInterval('month', $month);
//	$profil = new SalaryProfil($profil_id);
//	echo $profil->getEmpTable($dates['begin'], $dates['end']);
	echo Salary::loadSalaryReport($profil_id, $month, $cc, $payment_mode);

} elseif(isset($_GET['salary_editor'])){
	if(isset($_GET['save'])){
		echo Salary::saveSalaryEditor($_POST);
	} else {
		if(!isset($_GET['job_id'])){
			$jobs = Jobs::getList();
			$job_id = $jobs[0]->id;
		} else {
			$job_id = safeGet('job_id');
		}
		echo Salary::salaryEditor($job_id, (isset($_GET['cc']) ? safeGet('cc') : ''));
	}
	
} elseif(isset($_GET['insur_report'])){
	if(!isset($_GET['job_id'])){
		$jobs = Jobs::getList();
		$job_id = $jobs[0]->id;
	} else {
		$job_id = safeGet('job_id');
	}
	$month = isset($_GET['month']) ? safeGet('month') : date('m');
	echo Salary::loadInsurReport($job_id, $month);

} elseif(isset($_GET['gain_tax_report'])){
	if(!isset($_GET['job_id'])){
		$jobs = Jobs::getList();
		$job_id = $jobs[0]->id;
	} else {
		$job_id = safeGet('job_id');
	}
	$month = isset($_GET['month']) ? safeGet('month') : date('m');
	echo Salary::loadGainTaxReport($job_id);

} elseif(isset($_GET['sheet'])){
	$emp = new Employers(safeGet('emp_id'));
	$salary = new Salary($emp);
	$month = isset($_GET['month']) ? safeGet('month') : date('m');
	echo $salary->getSheet($month);
	
} elseif(isset($_GET['approve'])){
	if(isset($_GET['save'])){
		
	} else {
		echo Salary::TotalTable();
	}
	
} else {
	echo Salary::loadMainLayout();
} 
