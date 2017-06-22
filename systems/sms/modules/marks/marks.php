<?php
## SMS 
## Marks
## con => level, class, group, students
/***************** DEFAULT body***************************************/
if(isset($_REQUEST['con'])){
	$con = $_REQUEST['con'];
	$con_id =  $_REQUEST['con_id'];	

	if(getPrvlg('mark_read')){
		$read = true;
	} else {
		echo write_error($lang['no_privilege']);
		exit;
	}

	$marks = new marks($con, $con_id);
	
	if( getPrvlg('mark_edit') && !in_array($_SESSION['group'], array("student", "parent"))){
		$marks->update_auth = true;
	} else {
		$marks->update_auth = false;
	}


	if(isset($_REQUEST['edit'])){
		if($marks->update_auth == true){
			$marksEdit = new MarksEdit($con, $con_id);
			// reload exam table
			if(isset($_GET['reloadexamtable'])){
				echo $marksEdit->createMarksTable(0, true);
			// preset exams
			} elseif(isset($_GET['exams'])){
				if(isset($_GET['form'])){
					if(isset($_GET['cells']) && $_GET['cells'] != ''){
						echo Exams::presetExamsForm($con, $con_id, explode(',', safeGet($_GET['cells'])));
					} else{
						echo write_error('cells is not defined');
					}
				} elseif(isset($_GET['savepreset'])){
					echo Exams::savePreset($_POST);
				}
			// reset marks
			} elseif(isset($_GET['reset'])){
				if(isset($_GET['form'])){
					echo $marksEdit->resetForm();
				} elseif(isset($_GET['save'])){
					if( $marksEdit->resetSave($_POST)){
						echo write_html('form', 'style="margin-bottom:10px"', $marksEdit->getEditTermsForm());
					}
				}
			// update marks settings 
			} elseif(isset($_GET['save_edit'])){
				echo $marksEdit->saveSettings($_POST);
			// Addons
			} elseif(isset($_GET['addons'])){
				if(isset($_GET['form'])){
					echo $marksEdit->addonForm();
				} elseif(isset($_GET['save'])){
					echo $marksEdit->saveAddon($_POST);
				}
				
			} else {
			// default edit layout
				echo $marksEdit->loadEditLayout();
			}
		} else {
			echo write_error($lang['no_privilege']);
		}
	
	
	
	
	} elseif(isset($_GET['exams_chart'])){
		include('chart_exams.php');
		
	} elseif(isset($_GET['appreciation'])){
		$appreciation = new appreciations($con, $con_id);
		if(isset($_GET['submitappr'])){
			echo $appreciation->saveAppr($_POST);
		} elseif(isset($_GET['reload'])){
			$appreciation->cur_service = $_GET['service_id'] != 0 ? new services(safeGet($_GET['service_id'])): false;
			$appreciation->cur_term = new terms(safeGet($_GET['term_id']));
			echo $con == 'student' ?  $appreciation->createStudentApprTable() : $appreciation->createClassApprTable();
		} else {
			echo $appreciation->loadLayout();
		}
	
	
	
	
	} elseif(isset($_GET['gpa'])){
		
		if(isset($_GET['chart'])){
			$std_id = $con_id;
			include('gpa_charts.php');
		} 
		else {
			if($con == 'student'){ 
				$gpa = new GPA($con_id);
				if(isset($_GET['reload'])){
					$max_years = safeGet($_GET['years']);
					echo implode('', $gpa->getYears($max_years));
				} else {
					echo $gpa->loadLayout();
				}
			} else {
				// Con = class
				echo '';
			}
		}
	
	
	
	
	} elseif(isset($_GET['reports'])){
		$reports = new Reports($con, $con_id, new Terms(safeGet('term_id')));
		
		if(isset($_GET['tools'])){
			echo $reports->getGenerateTools();
		/*} elseif(isset($_GET['pdf_form'])){
			if(getPrvlg('mark_print')){
				echo write_html('form', 'id="cert_tools_form" target="_blank" method="POST" action="index.php?plugin=pdf" class="ui-corner-all ui-state-highlight"',
					$reports->getGenerateOptions()
				);
			} else {
				echo write_error($lang['no_privilege']);
			}
		}*/ 
		
		
		
	} elseif(isset($_GET['preview'])){
			echo $reports->generate(safeGet('term_id'));
			
	} elseif(isset($_GET['download_cert'])){
			if(getPrvlg('mark_print')){
				$reports->downloadCertificates(safeGet($_GET['cur_term']));
			} else {
				echo write_error($lang['no_privilege']);
			}
		} elseif( isset($_GET['getList'])){
			if($_GET['overwrite'] !=1){
				$term = new Terms($_POST['cur_term']);
				$filename= 'certificate-'.$_SESSION['year'].'-term'.$term->term_no.'.pdf';
				$stds = getStdIds($con, $con_id);
				foreach($stds as $std_id){	
					$filepath = "attachs/files/$std_id/";
					if(!file_exists($filepath. $filename)){
						$std_ids[] = $std_id;
					}
				}	
				print(json_encode(array('error'=>'', 'stdids'=>implode(',', $std_ids))));		
			} else {
				print(json_encode(array('error'=>'', 'stdids'=>getStdIds($con, $con_id))));
			}
		} elseif(isset($_GET['submit_tools'])){
			$tool = $_POST['tool'];
			$error = false;
			if($tool == 'delete'){
				$reports->deleteCertificate(safeGet($_GET['cur_term']));
			} elseif($tool == 'generate'){
				$report = new Reports('student', $con_id);
				$answer = $report->saveReport(safeGet('cur_term'), $_POST, true);
				//print_r($answer);
				if($answer['error'] != ''){
					echo json_encode_result($answer['error']);
				} else {
					echo json_encode_result(true);
				}
			} elseif($tool == 'print'){
				$stds = getStdIds($con, $con_id);
				foreach($stds as $std_id){	
					$report = new Reports('student', $std_id);
					echo $report->saveReport(safeGet('cur_term'), $_POST, false);
				}
			}
		} 
		else {
			echo $reports->loadLayout();
		}
		
	} elseif(isset($_GET['skills'])){
		if(isset($_GET['results'])){
			$skill = new Skills(safeGet('skill_id'));
			echo $skill->LoadResultsForm(safeGet('con'), safeGet('con_id'), safeGet('term_id'));
			
		} elseif(isset($_GET['save_results'])){
			echo Skills::saveResults($_POST);
			
		} elseif(isset($_GET['list'])){
			$out = array();
			$obj = $sms->getAnyObjById($con, $con_id);
			$students = $obj->getStudents();
			foreach($students as $student){
				$std_services = $student->getServices();
				if(object_in_array(new Services(safeGet('services')), $std_services ))	{
					$out[] = $student->id;
				}
			}
			echo json_encode_result(array('stdids'=>implode(',', $out)));
			
		} elseif(isset($_GET['skill_report'])){
			$student = new Students(safeGet('con_id'));
			$service = new Services(safeGet('services'));
			$term = new Terms(safeGet('terms'));
			$report = new Reports($con, $con_id, $term);
			
			echo $report->write_report_page(Skills::SkillReport($student, $service, $term), true, false);
		} elseif(isset($_GET['terms']) || isset($_GET['services'])){
			if($_GET['con'] == 'class'){
				echo Skills::createClassSkillsTable(safeGet('con_id'), new Terms(safeGet('terms')), new Services(safeGet('services')));
			} elseif($_GET['con'] == 'student'){
				$student = new Students(safeGet('con_id'));
				echo write_html('h2', '', $student->getName()).
				Skills::createStudentSkillsTable(safeGet('con_id'), new Terms(safeGet('terms')), new Services(safeGet('services')));
			} 
		} else {
		//	$marks = new Marks(safeGet('con'), safeGet('con_id'));
			echo Skills::loadMainLayout(safeGet('con'), safeGet('con_id'));
		}
	
	
	} else {
		if(isset($_GET['loadexam'])){
			$exam = exams::searchExam(safeGet($_GET['service_id']), safeGet($_GET['term_id']), safeGet($_GET['exam_no']), $con, $con_id);
			echo $exam->loadLayout();
		} elseif(isset($_GET['reload_marks'])){
			$marks = new marks($con, $con_id);
			echo $marks->createMarksTable(safeGet($_GET['term_id']), false);
		} elseif(isset($_GET['subexam'])){
			echo exams::submitExam($_POST);
		} elseif(isset($_GET['approve_term'])){
			echo terms::approveExam(safeGet($_POST['term_id']));
		} elseif(isset($_GET['unapprove_term'])){
			echo terms::unApproveTerm(safeGet($_POST['term_id']));
		} elseif(isset($_GET['approve_exam'])){
			echo exams::approveExam(safeGet($_POST['exam_id']));
		} elseif(isset($_GET['unapprove_exam'])){
			echo exams::unApproveExam(safeGet($_POST['exam_id']));
		} else {
			// default Layout
			echo $marks->loadLayout();
		}
	}
} elseif(isset($_GET['gradding'])){
	include('marks_gradding.php');
		
}
?>