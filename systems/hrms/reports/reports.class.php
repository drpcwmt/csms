<?php
/** Reports 
*
*
*/

class Reports{

	static function loadMainLayout($job_id='', $current_month='', $cc=''){
		global $lang;
		$layout = new Layout();
		if($current_month==''){
			$current_month = date('m');
		}
		$year = getYear();
		$first_month = date('m', $year->begin_date);
		$months = array('0'=>$lang["months_0"]);
		for($i=0; $i<12; $i++){
			$m = $first_month+$i;
			$stamp = mktime(0,0,0, $m, 1, date('Y'));
			$nm = date('m', $stamp);
			$months[$nm] = $lang["months_$nm"];
		}
		$layout->months_opts = write_select_options($months, $current_month);
		
		$jobs = Jobs::getList($cc);
		if($job_id == ''){
			$first_job = reset($jobs);
			$job_id = $first_job->id;
		} 
		$layout->jobs_opts = write_select_options(Jobs::getListOpts(), $job_id);
		$layout->cc_opts = write_select_options(CostcentersGroup::getListOpts(), $cc, true);
		$layout->table = Reports::getRewardDiscountPerMonth($job_id, $current_month, $cc);
		return  fillTemplate('modules/reports/templates/main_layout.tpl', $layout);
	}
	
	
	static function getRewardDiscountPerMonth($job_id, $month, $cc=''){
		global $prvlg;
		$layout = new Layout();
		$layout->template = 'modules/reports/templates/reward_discount_table.tpl';
		$job = new Jobs($job_id);
		$layout->finc_prvlg = $prvlg->_chk('salary_read') ? '' : 'hidden';
		$emps = $job->getEmps($cc);
		$date = Reports::getDateInterval($month);
		$trs= array();
		$ser = 1;
		$year = getYear();
		foreach($emps as $emp){
			$row = new Layout($emp);
			$profil = $emp->getProfil();
			$row->template = 'modules/reports/templates/reward_discount_row.tpl';
			$row->finc_prvlg = $layout->finc_prvlg;
			// Day value
			$row->day_value = $profil->getEmpDayValue($emp);

			// earliear conv abs
			$early_abs = do_query_obj("SELECT COUNT(id) AS total FROM absents WHERE emp_id=$emp->id AND day<$date->begin AND day>$year->begin_date AND ill=0");
			if($early_abs->total<= $profil->absent_conv){
				$row->earlier_absents_conv = $early_abs->total;
			} else {
				$row->earlier_absents_conv =$profil->absent_conv;
				$row->earlier_absents_others = $early_abs->total - $profil->absent_conv;
			}
			
			// Earlier abs ill
			$early_abs_ill = do_query_obj("SELECT COUNT(id) AS total FROM absents WHERE emp_id=$emp->id AND day<$date->begin AND day>$year->begin_date AND ill=1");
			$row->earlier_absents_ill = $early_abs_ill->total;
				// current minth absents
			$abs = do_query_obj("SELECT SUM(value) AS value, COUNT(value) AS total FROM absents WHERE emp_id=$emp->id AND day>=$date->begin AND day<$date->end");
			$row->absents = $abs->value;
			$row->count_absents = $abs->total;

			
			// Earlier permitsions
			$early_permis = do_query_obj("SELECT COUNT(id) AS total FROM permissionout WHERE emp_id=$emp->id AND day<$date->begin AND day>$year->begin_date");
			$row->earlier_permission = $early_permis->total;
			// current month permission
			$cur_permis = do_query_obj("SELECT SUM(value) AS value, COUNT(id) AS total FROM permissionout WHERE emp_id=$emp->id AND day>=$date->begin AND day<$date->end");
			$row->cur_permission = $cur_permis->total;
			$row->cur_permission_val = $cur_permis->value;

			// current monts discount days
			$discounts = do_query_obj("SELECT SUM(cash) AS cash, SUM(value) AS days FROM discounts WHERE emp_id=$emp->id AND day>=$date->begin AND day<$date->end");
			$row->discount_days = $discounts->days;
			$row->discount_cash = $discounts->cash;

			$row->total_discount = (($abs->value+$cur_permis->value+$discounts->days) * $row->day_value) + $discounts->cash;
			// Overtime
			$overtime = do_query_obj("SELECT COUNT(id) AS total, SUM(value) AS value FROM overtime WHERE emp_id=$emp->id AND day>=$date->begin AND day<$date->end");
			$row->overtime = $overtime->total;
			$row->overtime_val = $overtime->value;

			// reward
			$bonus = do_query_obj("SELECT SUM(cash) AS cash, SUM(value) AS value FROM bonus WHERE emp_id=$emp->id AND day>=$date->begin AND day<$date->end");
			$row->bonus_days = $bonus->value;
			$row->bonus_cash = $bonus->cash;

			// total reward
			$row->total_reward = ($bonus->value * $row->day_value) + $bonus->cash +$overtime->value;
			
			
			$row->ser = $ser;
			$row->acc_code = $emp->getAccCode();
			$row->emp_name = $emp->getName();
			$trs[] = $row->_print();
			$ser++;
		}
		$layout->trs = implode('', $trs);
		return $layout->_print();
			
	}
	
	static function getDateInterval($month=''){
		global $lang;
		$year = getYear();
		if($month != '0'){
			$begin_date = mktime(0,0,0, $month, 1, $year->year);
			if($begin_date > $year->begin_date && $begin_date<=$year->end_date){
				$end_date = mktime(0,0,0, $month+1 , 1, $year->year);
			} else{
				$begin_date = mktime(0,0,0, $month, 1, $year->year+1);
				$end_date = mktime(0,0,0, $month+1 , 1, $year->year+1);
			}
		}
		$out = new stdClass();
		$out->begin = $begin_date;
		$out->end = $end_date;
		$out->title =$lang['month'].': '.$lang["months_$month"];
		return $out;
	}

}
?>