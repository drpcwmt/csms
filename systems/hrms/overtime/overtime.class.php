<?php
/** overtime Class 
*
*
*/

class Overtime{
	
	static function loadMainLayout(){
		global $this_system, $lang, $prvlg;
		$layout = new layout();
		$layout->template = 'modules/overtime/templates/main_layout.tpl';
		$jobs = Jobs::getList();
		$first_job = reset($jobs);
		$layout->job_id = $first_job->id;
		$layout->job_list = '';
		$layout->job_opts = '';
		$first = true;
		foreach($jobs as $job){
			$layout->job_list .= write_html( 'li', 'job_id="'.$job->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openOvertimeByJob"', 
				write_html('text', 'class="holder-job-'.$job->id.'"',
					$job->getName()
				)
			);
			$layout->job_opts .= write_html( 'option', 'value="'.$job->id.'" '.($first ? 'selected="selected"' : ''),
				$job->getName()
			);
			$first = false;
		}

		$current_month = date('m');
		$first_month = 9;
		$months = array('0'=>$lang["months_0"]);
		for($i=0; $i<12; $i++){
			$m = $first_month+$i;
			$stamp = mktime(0,0,0, $m, 1, date('Y'));
			$nm = date('m', $stamp);
			$months[$nm] = $lang["months_$nm"];
		}
		$layout->months_select = write_select_options($months, $current_month);
		$layout->today = unixToDate(time());
		$layout->daily_trs = Overtime::loadDailyOvertime('', $first_job->id);
		$layout->report_trs = Overtime::loadOvertimeReport();
		$layout->add_overtime_hidden = $prvlg->_chk('overtime_edit') ? '' : 'hidden';
		return $layout->_print();
	}

	static function loadOvertimeForm($job_id){
		global $lang;
		$layout = new Layout();
		$job = new Jobs($job_id);
		$profil = $job->getProfil();
		$layout->begin_time = $profil->end_time;
		$layout->today = unixToDate(time());
		$layout->template = 'modules/overtime/templates/overtime_form.tpl';
		
		return $layout->_print();	
	}
	
	static function saveOvertime($post){
		$emp = new Employers($post['emp_id']);
		$profil = $emp->getProfil();
		if($profil->overtime > 0){
			if($profil->overtime_per == 'hours'){
				$value = round((timeToUnix($post['end']) - timeToUnix($post['begin'])) / 3600) * $profil->overtime;
			} else {
				$value = $profil->overtime;
			}
		} else {
			$value = 0;
		}
		$insert = array(
			'emp_id'=>$emp->id,
			'day'=> dateToUnix($post['date']),
			'notes'=>$post['notes'],
			'begin'=> timeToUnix($post['begin']),
			'end'=> timeToUnix($post['end']),
			'value' => $value
		);
		if($overtime_id = do_insert_obj($insert, 'overtime', $emp->hrms->database, $emp->hrms->ip)){
			return true;
		} else {
			return false;
		}
	}
	
	static function loadDailyOvertime($date='', $job_id=''){
		global $this_system, $prvlg;
		$trs = array();
		if($date == ''){
			$date = dateToUnix(unixToDate(time()));
		}
		if($job_id != ''){
			$sql = "SELECT overtime.* FROM overtime, employer_data WHERE employer_data.job_code=$job_id AND employer_data.id=overtime.emp_id AND overtime.day=$date";
		} else {
			$sql = "SELECT * FROM overtime WHERE day=$date";
		}
		
		$overtimes = do_query_array($sql);
		foreach($overtimes as $overtime){
			$emp = new Employers($overtime->emp_id);
			$profil = $emp->getProfil();
			$row = new Layout($overtime);
			$row->template = 'modules/overtime/templates/overtime_row.tpl';
			$row->name = $emp->getName();
			$row->job_title = $emp->position;	
			$row->begin = unixToTime($overtime->begin);
			$row->end = unixToTime($overtime->end);
			$row->value = $prvlg->_chk('salary_read') ? $overtime->value: write_icon('cancel');
			$row->overtime_remove_hidden = $prvlg->_chk('overtime_edit') ? '' : 'hidden';
			$trs[] = $row->_print();
		}
		return implode('', $trs);
	}
	
	static function deleteOvertime($overtime_id){
		if(do_delete_obj("id=$overtime_id", 'overtime')){
			return array('error'=>'');
		} else {
			return array('error'=> $lang['error']);
		}
	}
	
	static function loadOvertimeReport($job_id='', $month=''){
		global $prvlg;
		if($job_id==''){
			$jobs = Jobs::getList();
			$job = $jobs[0];
		} else {
			$job = new Jobs($job_id);
		}
		if($month == '0'){
			$dates = Absents::getDateInterval();
		} else {
			if($month ==''){
				$month = date('m');
			}
			$dates = Absents::getDateInterval('month', $month);
		}
		$begin = $dates['begin'];
		$end = $dates['end'];
		$trs = array();
		$emps = $job->getEmps();
		foreach($emps as $emp){
			$sql = "SELECT count(id) as count,
			SUM((end - begin) /3600) as hours, 
			SUM(value) as value
			FROM overtime
			WHERE emp_id=$emp->id
			AND day>=$begin 
			AND day<=$end";

			$row = do_query_obj($sql, $emp->hrms->database, $emp->hrms->ip);
			$profil = $emp->getProfil();
			//$cash = $values->cash != '' ? $values->days : ($prvlg->_chk('salary_read') ? $values->days * $profil->getEmpDayValue($emp) : '');
			$trs[] = write_html('tr', '',
				write_html('td', ' class="unprintable"', 
					write_html('button', ' module="employers" empid="'.$emp->id.'" action="openEmployer" class="ui-state-default hoverable circle_button"', write_icon('person'))
				).
				write_html('td', '', $emp->getName()).
				write_html('td', '', $emp->position).
				write_html('td', '', $row->count).
				write_html('td', '', round($row->hours)).
				write_html('td', 'align="center"', $row->value)
			);
		}
		return implode('', $trs);	

	}}