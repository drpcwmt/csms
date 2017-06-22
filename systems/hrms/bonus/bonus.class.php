<?php
/** Bonus Class 
*
*
*/

class Bonus{
	
	static function loadMainLayout(){
		global $this_system, $lang, $prvlg;
		$layout = new layout();
		$layout->template = 'modules/bonus/templates/main_layout.tpl';
		$jobs = Jobs::getList();
		$first_job = reset($jobs);
		$layout->job_id = $first_job->id;
		$layout->job_list = '';
		$layout->job_opts = '';
		$first = true;
		foreach($jobs as $job){
			$layout->job_list .= write_html( 'li', 'job_id="'.$job->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openBonusByJob"', 
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
		$layout->daily_trs = Bonus::loadDailyBonus('', $first_job->id);
		$layout->report_trs = Bonus::loadBonusReport();
		$layout->add_bonus_hidden = $prvlg->_chk('bonus_edit') ? '' : 'hidden';
		return $layout->_print();
	}

	static function loadBonusForm(){
		global $lang;
		$layout = new Layout();
		$current_month = date('m');
		$months = array();
		for($i=0; $i<12; $i++){
			$m = $current_month+$i;
			$stamp = mktime(0,0,0, $m, 1, date('Y'));
			$nm = date('m', $stamp);
			$months[$stamp] = $lang["months_$nm"];
		}
		$layout->months_select = write_select_options($months, $current_month);
		
		foreach($months as $stamp=>$name){
			$ths[] = write_html('th', 'align="center"', $name);
			$tds[] = write_html('td', '', 
				'<input type="hidden" name="stamps[]" value="'.$stamp.'" />'.
				'<input type="text" name="stamp_val[]" style="with:25px; text-align:center" />'
			);
		}
		$layout->ths = implode('', $ths);
		$layout->tds = implode('', $tds);
		$layout->today = unixToDate(time());
		$layout->template = 'modules/bonus/templates/bonus_form.tpl';
		
		return $layout->_print();	
	}
	
	static function saveBonus($post){
		$emp = new Employers($post['emp_id']);
		$profil = $emp->getProfil();
		if($post['value_type'] == 'value_by_day'){
			$value = $post['value'] * $profil->getEmpDayValue($emp);
		} else {
			$value =  $post['value'];
		}
		$error = false;
		if($post['paid_type'] == 'once'){
			$insert = array(
				'emp_id'=>$emp->id,
				'day'=> dateToUnix($post['date']),
				'comments'=>$post['comments'],
				'cash'=> $post['value_type'] == 'value_by_cash' ? $post['value'] : '',
				'value'=> $post['value_type'] == 'value_by_day' ? $post['value'] : '',
			);
			if(!do_insert_obj($insert, 'bonus', $emp->hrms->database, $emp->hrms->ip)){
				$error = true;
			}
		} else {
			for($i=0; $i<12; $i++){
				if($post['stamp_val'][$i]!=''){
					$insert = array(
						'emp_id'=>$emp->id,
						'day'=>$post['stamps'][$i],
						'comments'=>$post['comments'],
						'cash'=> $post['stamp_val'][$i]
					);
					if(!do_insert_obj($insert, 'bonus', $emp->hrms->database, $emp->hrms->ip)){
						$error = true;
					}
				}
			}
		}
		return $error == false ? true : false;
	}
	
	static function loadDailyBonus($date='', $job_id=''){
		global $this_system, $prvlg;
		$trs = array();
		if($date == ''){
			$date = dateToUnix(unixToDate(time()));
		}
		if($job_id != ''){
			$sql = "SELECT bonus.* FROM bonus, employer_data WHERE employer_data.job_code=$job_id AND employer_data.id=bonus.emp_id AND bonus.day=$date";
		} else {
			$sql = "SELECT * FROM bonus WHERE day=$date";
		}
		
		$bonus = do_query_array($sql);
		foreach($bonus as $bonus){
			$emp = new Employers($bonus->emp_id);
			$profil = $emp->getProfil();
	//		$emp_absents = $emp->getBonus($this_system->getYearSetting('begin_date'), $this_system->getYearSetting('end_date'));
			$row = new Layout($bonus);
			$row->template = 'modules/bonus/templates/bonus_row.tpl';
			$row->name = $emp->getName();
			$row->comments = $bonus->comments;
			$row->job_title = $emp->position;	
			$row->bonus_remove_hidden = $prvlg->_chk('bonus_edit') ? '' : 'hidden';
			if(intval($bonus->cash) == 0){
				$row->cash = $bonus->value * $profil->getEmpDayValue($emp);
			}
			$trs[] = $row->_print();
		}
		return implode('', $trs);
	}
	
	static function deleteBonus($bonus_id){
		if(do_delete_obj("id=$bonus_id", 'bonus')){
			return array('error'=>'');
		} else {
			return array('error'=> $lang['error']);
		}
	}
	
	static function loadBonusReport($job_id='', $month=''){
		global $prvlg;
		if($job_id==''){
			$jobs = Jobs::getList();
			$job = $jobs[0];
		} else {
			$job = new Jobs($job_id);
		}
		$emps = $job->getEmps();
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
		foreach($emps as $emp){
			$sql = "SELECT DISTINCT(bonus.id),
			SUM(bonus_paid.value) as value, 
			SUM(bonus.value) AS days, 
			SUM(bonus.cash) AS cash, 
			COUNT(DISTINCT bonus.id)as count,
			GROUP_CONCAT(bonus.comments SEPARATOR '<br/>') AS notes 
			FROM bonus, bonus_paid
			WHERE bonus.emp_id=$emp->id
			AND bonus.id = bonus_paid.bonus_id
			AND bonus_paid.month>=$begin 
			AND bonus_paid.month<$end
			AND bonus.day>=$begin 
			AND bonus.day<=$end";

			$values = do_query_obj($sql, $emp->hrms->database, $emp->hrms->ip);
			$profil = $emp->getProfil();
			//$cash = $values->cash != '' ? $values->days : ($prvlg->_chk('salary_read') ? $values->days * $profil->getEmpDayValue($emp) : '');
			$real_value = $values->cash + ($values->days * $profil->getEmpDayValue($emp)); 
			$total = $prvlg->_chk('salary_read') ? $real_value: write_icon('cancel');
			
			$trs[] = write_html('tr', '',
				write_html('td', ' class="unprintable"', 
					write_html('button', ' module="employers" empid="'.$emp->id.'" action="openEmployer" class="ui-state-default hoverable circle_button"', write_icon('person'))
				).
				write_html('td', 'style="vertical-align:top"', $emp->getName()).
				write_html('td', 'style="vertical-align:top"', $emp->position).
				write_html('td', 'style="vertical-align:top"', $values->count).
				write_html('td', 'style="vertical-align:top"', $values->notes).
				write_html('td', 'style="vertical-align:top" align="center"', $values->days).
				write_html('td', 'style="vertical-align:top" align="center"', $values->cash).
				write_html('td', 'style="vertical-align:top" align="center"', $values->value)
			);
		}
		return implode('', $trs);	

	}}