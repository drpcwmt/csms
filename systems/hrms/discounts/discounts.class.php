<?php
/** Discounts Class 
*
*
*/

class Discounts{
	
	static function loadMainLayout(){
		global $this_system, $lang, $prvlg;
		$layout = new layout();
		$layout->template = 'modules/discounts/templates/main_layout.tpl';
		$jobs = Jobs::getList();
		$first_job = reset($jobs);
		$layout->job_id = $first_job->id;
		$layout->job_list = '';
		$layout->job_opts = '';
		$first = true;
		foreach($jobs as $job){
			$layout->job_list .= write_html( 'li', 'job_id="'.$job->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openDiscountByJob"', 
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
		$layout->daily_trs = Discounts::loadDailyDiscounts('', $first_job->id);
		$layout->report_trs = Discounts::loadDiscountsReport();
		$layout->cash_hidden = $prvlg->_chk('salary_read') ? '' : 'hidden';
		$layout->add_discounts_hidden = $prvlg->_chk('discounts_add') ? '' : 'hidden';
		return $layout->_print();
	}

	static function loadDiscountForm(){
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
		$layout->template = 'modules/discounts/templates/discount_form.tpl';
		
		return $layout->_print();	
	}
	
	static function saveDiscount($post){
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
			if(!do_insert_obj($insert, 'discounts', $emp->hrms->database, $emp->hrms->ip)){
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
					if(!do_insert_obj($insert, 'discounts', $emp->hrms->database, $emp->hrms->ip)){
						$error = true;
					}
				}
			}
		}
		return $error == false ? true : false;
	}
	
	static function loadDailyDiscounts($date='', $job_id=''){
		global $this_system, $prvlg;
		$trs = array();
		if($date == ''){
			$date = dateToUnix(unixToDate(time()));
		}
		if($job_id != ''){
			$sql = "SELECT discounts.* FROM discounts, employer_data WHERE employer_data.job_code=$job_id AND employer_data.id=discounts.emp_id AND discounts.day=$date";
		} else {
			$sql = "SELECT * FROM discounts WHERE day=$date";
		}
		
		$discounts = do_query_array($sql);
		foreach($discounts as $discount){
			$emp = new Employers($discount->emp_id);
	//		$emp_absents = $emp->getDiscounts($this_system->getYearSetting('begin_date'), $this_system->getYearSetting('end_date'));
			$row = new Layout($discount);
			$row->template = 'modules/discounts/templates/discount_row.tpl';
			$row->name = $emp->getName();
			$row->comments = $discount->comments;
			$row->job_title = $emp->position;	
			$row->disc_remove_hidden = $prvlg->_chk('discounts_remove') ? '' : 'hidden';
			$trs[] = $row->_print();
		}
		return implode('', $trs);
	}
	
	static function deleteDiscount($dis_id){
		if(do_delete_obj("id=$dis_id", 'discounts')){
			do_delete_obj("dis_id=$dis_id", 'discount_paid');
			return array('error'=>'');
		} else {
			return array('error'=> $lang['error']);
		}
	}

	static function loadDiscountsReport($job_id='', $month=''){
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
			$sql = "SELECT
			SUM(discounts.value) as value, 
			SUM(discounts.value) AS days, 
			SUM(discounts.cash) AS cash, 
			COUNT(discounts.id)as count,
			GROUP_CONCAT(discounts.comments SEPARATOR '<br/>') AS notes 
			FROM discounts
			WHERE discounts.emp_id=$emp->id
			AND discounts.day>=$begin 
			AND discounts.day<=$end";
//echo $sql;
			$values = do_query_obj($sql, $emp->hrms->database, $emp->hrms->ip);
			$profil = $emp->getProfil();
			$cash = $values->cash != '' ? $values->days : ($prvlg->_chk('salary_read') ? $values->days * $profil->getEmpDayValue($emp) : '');
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

	}
}