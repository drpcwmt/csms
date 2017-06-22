<?php
/** permissionout Class 
*
*
*/

class Permissionout{
	
	static function loadMainLayout(){
		global $this_system, $lang, $prvlg;
		$layout = new layout();
		$layout->template = 'modules/permissionout/templates/main_layout.tpl';
		$jobs = Jobs::getList();
		$first_job = reset($jobs);
		$layout->job_id = $first_job->id;
		$layout->job_list = '';
		$layout->job_opts = '';
		$first = true;
		foreach($jobs as $job){
			$layout->job_list .= write_html( 'li', 'job_id="'.$job->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openPermissionoutByJob"', 
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
		$layout->daily_trs = Permissionout::loadDailyPermissionout('', $first_job->id);
		$layout->report_trs = Permissionout::loadPermissionoutReport();
		$layout->add_permissionout_hidden = $prvlg->_chk('permissionout_edit') ? '' : 'hidden';
		return $layout->_print();
	}

	static function loadPermissionoutForm($job_id){
		global $lang;
		$layout = new Layout();
		$job = new Jobs($job_id);
		$profil = $job->getProfil();
		$layout->begin_time = $profil->begin_time;
		$layout->end_time = $profil->end_time;
		$layout->today = unixToDate(time());
		$layout->template = 'modules/permissionout/templates/permissionout_form.tpl';
		
		return $layout->_print();	
	}
	
	static function savePermissionout($post){
		$emp = new Employers($post['emp_id']);
		/*$profil = $emp->getProfil();
		if($profil->permis_allowness > 0){
			if($profil->permissionout_per == 'hours'){
				$value = round((timeToUnix($post['end']) - timeToUnix($post['begin'])) / 3600) * $profil->permis_allowness;
			} else {
				
			}
		} else {
			$value = 0;
		}*/
		$insert = array(
			'emp_id'=>$emp->id,
			'day'=> dateToUnix($post['date']),
			'notes'=>$post['notes'],
			'begin'=> timeToUnix($post['begin']),
			'end'=> timeToUnix($post['end']),
			'value' => $post['value']
		);
		if($permissionout_id = do_insert_obj($insert, 'permissionout', $emp->hrms->database, $emp->hrms->ip)){
			return true;
		} else {
			return false;
		}
	}
	
	static function loadDailyPermissionout($date='', $job_id=''){
		global $this_system, $prvlg;
		$trs = array();
		if($date == ''){
			$date = dateToUnix(unixToDate(time()));
		}
		if($job_id != ''){
			$sql = "SELECT permissionout.* FROM permissionout, employer_data WHERE employer_data.job_code=$job_id AND employer_data.id=permissionout.emp_id AND permissionout.day=$date";
		} else {
			$sql = "SELECT * FROM permissionout WHERE day=$date";
		}
		
		$permissionouts = do_query_array($sql, $this_system->database, $this_system->ip);
		foreach($permissionouts as $permissionout){
			$emp = new Employers($permissionout->emp_id);
			$old_permis = count(do_query_array("SELECT day FROM permissionout WHERE emp_id=$permissionout->emp_id", $this_system->database, $this_system->ip));
			$profil = $emp->getProfil();
			$row = new Layout($permissionout);
			$row->template = 'modules/permissionout/templates/permissionout_row.tpl';
			$row->name = $emp->getName();
			$row->job_title = $emp->position;	
			$row->begin = unixToTime($permissionout->begin);
			$row->end = unixToTime($permissionout->end);
			$row->count = $old_permis;
			$row->value = $prvlg->_chk('salary_read') ? $permissionout->value: write_icon('cancel');
			$row->permissionout_remove_hidden = $prvlg->_chk('permissionout_edit') ? '' : 'hidden';
			$trs[] = $row->_print();
		}
		return implode('', $trs);
	}
	
	static function deletePermissionout($permissionout_id){
		if(do_delete_obj("id=$permissionout_id", 'permissionout')){
			return array('error'=>'');
		} else {
			return array('error'=> $lang['error']);
		}
	}
	
	static function loadPermissionoutReport($job_id='', $month=''){
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
			FROM permissionout
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

	}

	static function updatePermis($post){
		$id = $post['id'];
		if(do_update_obj($post, "id=$id", 'permissionout')){
			$answer= array('error'=>'');
			if(isset($post['value'])){
				$answer['value'] = $post['value'];
			}
			return $answer;
		} else {
			return array('error'=> $lang['error']);
		}
	}
}