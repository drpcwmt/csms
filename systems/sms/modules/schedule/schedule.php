<?php
## SMS 
## Schedule
## con => level, class, group, students

$thisTemplatePath = "modules/schedule/templates";
//$static_array = array('prof', 'hall', 'tools');

/***************** DEFAULT body***************************************/
if(isset($_REQUEST['con'])){
	$con = $_REQUEST['con'];
	$con_id =  $_REQUEST['con_id'];	

	if(($con=='level' && getPrvlg('schedule_level_read')) 
		|| ($con=='class' && getPrvlg('schedule_class_read')) 
		|| ($con=='group' && getPrvlg('schedule_group_read')) 
		|| ($con=='student' && (getPrvlg('schedule_std_read') || $con_id=$_SESSION['user_id']) ) 
		|| ($con=='prof' && (getPrvlg('resource_read_profs') || $con_id=$_SESSION['user_id']) ) 
		|| ($con=='hall' && getPrvlg('resource_read_halls')) 
		|| ($con=='tools' && getPrvlg('resource_read_tools')) ){
		$read = true;
	} else {
		echo write_error($lang['no_privilege']);
		exit;
	}

	$schedule = new schedule($con, $con_id);
	
	if( ($con=='level' && getPrvlg('schedule_level_write')) 
		|| ($con=='class' && getPrvlg('schedule_class_write')) 
		|| ($con=='group' && getPrvlg('schedule_group_write')) 
		|| ($con=='student' && getPrvlg('schedule_std_write')) 
		|| ($con=='prof' && getPrvlg('resource_edit_profs')) 
		|| ($con=='hall' && getPrvlg('resource_edit_halls'))
		|| ($con=='tools' && getPrvlg('resource_edit_tools'))){
		$schedule->update_auth = true;
	} else {
		$schedule->update_auth = false;
	}

	if(isset($_REQUEST['edit'])){
		if($schedule->update_auth == true){
			$scheduleEdit = new scheduleEdit($con, $con_id);
			
			if(isset($_GET['copyfrom'])){
				echo $scheduleEdit->getSelectCon(safeGet($_GET['getcon']));
				exit;
			} elseif(isset($_GET['join'])){
				echo $scheduleEdit->joinSessions($_POST);
				exit;
			} elseif(isset($_GET['resize'])){
				echo $scheduleEdit->resizeSessions($_POST);
				exit;
			} elseif(isset($_GET['submitsessions'])){
				echo $scheduleEdit->addMultiSessions($_POST);
				exit;
			} elseif(isset($_GET['autogensession'])){
				echo $scheduleEdit->autoGenerateSessions($_POST);
				exit;
			} elseif(isset($_GET['submitcopy'])){
				echo $scheduleEdit->copySessions($_POST);
				exit;
				// Reset
			} elseif(isset($_GET['reset'])){
				if(isset($_POST['del_type']) && $_POST['del_type'] == 'lessons'){
					echo $scheduleEdit->deleteLessons($_POST);
				} else if(isset($_POST['del_type']) && $_POST['del_type'] == 'sessions'){
					echo $scheduleEdit->deleteSessions($_POST);
				}
				exit;
			} elseif(isset($_GET['attrlessonform'])){
				echo $scheduleEdit->attrLessonLayout(safeGet($_GET['sessions']));
				exit;
			} elseif(isset($_GET['submitlesson'])){
				echo $scheduleEdit->submitLesson($_POST);
				exit;
			} elseif(isset($_GET['reloadservices'])){
				echo write_select_options(
					$scheduleEdit->getAvaibleService(safeGet($_GET['con']), safeGet($_GET['con_id'])),
					'', 
					false
				);
				exit;
			} elseif(isset($_GET['reloadprofs'])){
				$seq = explode('-', safeGet($_GET['sessions']));
				$session = new stdClass();
				$session->con = safeGet($_GET['con']);
				$session->con_id = safeGet($_GET['con_id']);
				$session->date = $seq[0];
				$session->lesson_no =  $seq[1];
				echo write_select_options(
					$scheduleEdit->getAvaibleProfs($session, safeGet($_GET['service']), false), 
					$scheduleEdit->getServiceProf($session->con, $session->con_id, safeGet($_GET['service'])), 
					false
				);
				exit;
			} elseif(isset($_GET['reloadhalls'])){
				$seq = explode('-', safeGet($_GET['session']));
				$session = new stdClass();
				$session->con = safeGet($_GET['con']);
				$session->con_id = safeGet($_GET['con_id']);
				$session->date = $seq[0];
				$session->lesson_no =  $seq[1];
				if($_GET['con'] == 'class'){
					$def_hall = getClassDefRoom(safeGet($_GET['con_id']));
				} else {
					$def_hall = '';
				}
				$halls = $scheduleEdit->getAvaibleHalls($session, isset($_GET['avaible']));

				echo  write_select_options($halls, $def_hall, false);
				exit;
			}
				// Defaule layout
			$schedule->editable = true;
			if(isset($_GET['hide_lessons'])){
				$schedule->hide_lessons = true;
			}
			if(isset($_GET['hide_ds'])){
				$schedule->default = false;
			}
			if(isset($_GET['hide_is'])){
				$schedule->inherited = false;
			}
			if(isset($_GET['b_date']) && $_GET['b_date'] != ''){
				$schedule->spc_date_begin = dateToUnix(safeGet($_GET['b_date']));
				if(isset($_GET['e_date'])  && $_GET['e_date'] != ''){
					$schedule->spc_date_end = dateToUnix(safeGet($_GET['e_date']));
				}
			}
			if(isset($_GET['reload'])){
				if(isset($_GET['b_date']) && $_GET['type'] != 'default'){
					$first_time_in_year = first_week_route($schedule->date_begin, $schedule->first_day_week);
					$week_day_beg = first_week_route(dateToUnix(safeGet($_GET['b_date'])), $schedule->first_day_week);
					$week_no = round(($week_day_beg - $first_time_in_year) / 604800)+1;
				} else {
					$week_no = safeGet($_GET['week_no']);
				}
				echo $schedule->loadWeek($week_no);
			} else {
					$schedule->week_no = 0;
				$out = write_html('table', 'class="layout scope" width="100%"',
					write_html('tr', '', 
						write_html('td', 'width="300" valign="top"', 
							$scheduleEdit->loadEditLayout()
						).
						write_html('td', 'valign="top"  class="schedule_preview"', 
							$schedule->loadSchedule()
						)
					)
				);
				echo $out;
			}
		} else {
			echo write_error($lang['no_provilege']);
		}
	}elseif(isset($_GET['load_week'])){
		echo $schedule->loadWeek(safeGet($_GET['week_no']));
	} else {
		echo $schedule->loadSchedule();
	}
	exit;
}

?>