<?php
/** Absents Class 
*
*
*/

class Absents{
	
	static function loadMainLayout(){
		global $this_system, $lang, $prvlg;
		$layout = new layout();
		$layout->template = 'modules/absents/templates/main_layout.tpl';
		$jobs = Jobs::getList();
		$first_job = reset($jobs);
		$layout->job_id = $first_job->id;
		$layout->job_list = '';
		$layout->job_opts = '';
		$layout->over = $this_system->getSettings('long_absents');
		$first = true;
		foreach($jobs as $job){
			$layout->job_list .= write_html( 'li', 'job_id="'.$job->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openAbsByJob"', 
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
		$layout->daily_trs = Absents::loadDailyAbsent('', $first_job->id);
		$layout->report_trs = Absents::loadAbsentReport();
		$layout->cash_hidden = $prvlg->_chk('salary_read') ? '' : 'hidden';
		$layout->add_absent_hidden = $prvlg->_chk('absents_edit') ? '' : 'hidden';
		return $layout->_print();
	}
	
	static function loadDailyAbsent($date='', $job_id=''){
		global $this_system, $prvlg;
		$longAbs_setiing = $this_system->getSettings('long_absents');
		$trs = array();
		if($date == ''){
			$date = dateToUnix(unixToDate(time()));
		}
		if($job_id != ''){
			$sql = "SELECT absents.* FROM absents, employer_data WHERE employer_data.job_code=$job_id AND employer_data.id=absents.emp_id AND absents.day=$date";
		} else {
			$sql = "SELECT * FROM absents WHERE day=$date";
		}
		
		$absents = do_query_array($sql);
		foreach($absents as $absent){
			$emp = new Employers($absent->emp_id);
			$i = 0;
			$d =  0;
			$longAbs = 0;
			while($i < $longAbs_setiing){
				$day = mktime(0,0,0, date('m', $date), (date('d', $date) - $d), date('Y', $date));				
				if(!in_array(date('D', $day) , array('Fri','Sat'))){
					$i++;
					if( $emp->isAbsent($day)){
						$longAbs++;
					}
				}
				$d++;
			}
			$emp_absents = $emp->getAbsents($this_system->getYearSetting('begin_date'), $this_system->getYearSetting('end_date'));
			//if($sum < $profil->absent_ill){
			$row = new Layout($absent);
			$row->template = 'modules/absents/templates/absents_row.tpl';
			$row->name = $emp->getName();
			$row->job_title = $emp->position;	
			$row->approved_chk = $absent->approved==1 ? 'checked="checked"' : '';
			$row->approved_chk .= $prvlg->_chk('absents_edit')? '' : 'disabled';
			$row->ill_chk = $absent->ill==1 ? 'checked="checked"' : '';
			$row->ill_chk .= $prvlg->_chk('absents_edit')? '' : 'disabled';
			$row->value_disabled = $prvlg->_chk('absents_edit')? '' : 'disabled';
			$row->longAbs=  $longAbs >= $longAbs_setiing ? '<span class="ui-icon ui-icon-check"  title="+3"></span>' : '&nbsp;';
			$row->edit_absent_hidden = $prvlg->_chk('absents_edit') ? '' : 'hidden';
			$trs[] = $row->_print();
		}
		return implode('', $trs);
	}
	
	static function calcAbsentValue($employer, $date, $type='conv', $approved=false){
		global $this_system;
		$profil = $employer->getProfil();
		$sql = "SELECT count(value) AS total FROM absents WHERE emp_id=$employer->id AND day>=".$this_system->getYearSetting('begin_date')." AND day<=".$this_system->getYearSetting('end_date');
		if($type=="ill"){
			$sql .=" AND ill=1";
		} else {
			$sql .=" AND ill!=1";
		}
			
		$sum = do_query_obj($sql, $employer->hrms->database, $employer->hrms->ip);
		if($sum->total < $profil->{'absent_'.$type} || $approved){
			$value = 0;
		} else {
			if($type == 'conv'){
				$value = 2;
				$today = date('D', $date);
				if(in_array($today, array('Sun', 'Thu'))){
					$value = 3;
				}
			} else {
				$value = 1;
			}
			
		}
		return $value;
	}
	
	static function insertAbs($emps, $day){
		global $lang;
		$result = true;
		foreach($emps as $emp){
			$employer = new Employers($emp);
			$chk = do_query_obj("SELECT id FROM absents WHERE emp_id=$emp AND day=$day", $employer->hrms->database, $employer->hrms->ip);
			if(!isset($chk->id)){
				$value = Absents::calcAbsentValue($employer, $day);
				$ins = array('emp_id'=>$emp, 'day'=> $day, 'value'=>$value);
				if(!do_insert_obj($ins, 'absents')){
					$result = false;
				}
			}
		}
		if($result){
			return array('error'=>'');
		} else {
			return array('error'=> $lang['error']);
		}
	}
	
	static function updateAbs($post){
		$id = $post['id'];
		if(!isset($post['value'])&& !isset($post['commentss'])){
			$day = do_query_obj("SELECT * FROM absents WHERE id=$id");
			$emp = new Employers($day->emp_id);
			
			$post['value'] = Absents::calcAbsentValue($emp, $day->day, isset($post['ill']) && $post['ill']==1 ? 'ill' : 'conv',  isset($post['approved']) && $post['approved']==1);
		}
		if(do_update_obj($post, "id=$id", 'absents')){
			$answer= array('error'=>'');
			if(isset($post['value'])){
				$answer['value'] = $post['value'];
			}
			return $answer;
		} else {
			return array('error'=> $lang['error']);
		}
	}
	
	static function getDateInterval($type='', $value=''){
		global $this_system;
		$begin_date = $this_system->getYearSetting('begin_date');
		$end_date = $this_system->getYearSetting('end_date');
		if($type == 'month'){
			$month = $value;
			$cur_year = $_SESSION['year'];
			if($begin_date < mktime(0,0,0, $month, 1, $cur_year) && mktime(0,0,0, $month, 1, $cur_year) <$end_date){
				$begin_date = mktime(0,0,0, $month, 1, $cur_year);
				$end_date = mktime(0,0,0, $month+1 , -1, $cur_year);
			} else {
				$begin_date = mktime(0,0,0, $month, 1, $cur_year+1);
				$end_date = mktime(0,0,0, $month+1 , -1, $cur_year+1);
			}
		}
		
		return array('begin'=>$begin_date, 'end'=>$end_date);
	}
	
	static function getEmpYearAbs($emp){
		global $this_system, $lang;
		$begin_date = $this_system->getYearSetting('begin_date');
		$end_date = $this_system->getYearSetting('end_date');
		
		$profil = $emp->getProfil();
		$max_conv = $profil->absent_conv;
		$conv_abs = 0;
		for($i =0; $i<12; $i++){
			$m = date('m', $begin_date)+$i;
			$b = mktime(0,0,0,$m, 1, $_SESSION['year']);
			$e = mktime(0,0,0,$m+1, -1, $_SESSION['year']);
			$month = date('m', $b);
			$ths[] = write_html('th', 'class="rotate"', write_html('div', '', write_html('span', '', $lang["months_$month"])));
			$all_absents =  count($emp->getAbsents($b, $e));
			$ill_absents = count($emp->getAbsents($b, $e, 'ill=1'));
			$approv_absents = count($emp->getAbsents($b, $e, 'approved=1'));
			$total_tds[] = write_html('td', 'align="center"',$all_absents);
			$ill_tds[] = write_html('td', 'align="center"', $ill_absents);
			$approv_tds[] = write_html('td', 'align="center"', $approv_absents);
		}
		$table = new Layout($emp);
		$table->template = 'modules/absents/templates/emp_year_abs.tpl';
		$table->current_year = $_SESSION['year'] .'/'.($_SESSION['year']+1);
		$table->emp_name = $emp->getName();
		$table->ths = implode('', $ths);
		$table->total_tds = implode('', $total_tds);
		$table->ill_tds = implode('', $ill_tds);
		$table->approv_tds = implode('', $approv_tds);
		
		$table->total_all = count($emp->getAbsents($begin_date, $end_date));
		$table->ill_total =  count($emp->getAbsents($begin_date, $end_date, 'ill=1'));
		$table->approv_total =count($emp->getAbsents($begin_date, $end_date, 'approved=1'));
		return $table->_print();
	}
	
	static function deleteAbs($abs_id){
		if(do_delete_obj("id=$abs_id", 'absents')){
			return array('error'=>'');
		} else {
			return array('error'=> $lang['error']);
		}
	}
	
	static function loadAbsentReport($job_id='', $month=''){
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
			$values = do_query_obj("SELECT SUM(value) as total FROM absents WHERE emp_id=$emp->id ANd day>=$begin AND day<=$end", $emp->hrms->database, $emp->hrms->ip);
			$profil = $emp->getProfil();
			$cash = $values->total * $profil->getEmpDayValue($emp);

			$trs[] = write_html('tr', '',
				write_html('td', ' class="unprintable"', 
					write_html('button', ' module="employers" empid="'.$emp->id.'" action="openEmployer" class="ui-state-default hoverable circle_button"', write_icon('person'))
				).
				write_html('td', '', $emp->getName()).
				write_html('td', '', $emp->position).
				write_html('td', 'align="center"', count($emp->getAbsents($begin, $end, 'ill=1'))).
				write_html('td', 'align="center"', count($emp->getAbsents($begin, $end, 'ill!=1'))).
				write_html('td', 'align="center"', count($emp->getAbsents($begin, $end))).
				write_html('td', 'align="center"', $values->total).
				($prvlg->_chk('salary_read') ? 
					write_html('td', 'align="center"', $cash)
				: '')
			);
		}
		return implode('', $trs);	

	}
	
	static function getList($emp_id){
		global $lang;
		$year = getYear();
		$rows = do_query_array("SELECT * FROM absents WHERE emp_id=$emp_id AND day>=$year->begin_date AND day<=$year->end_date");
		if($rows!= false){
			foreach($rows as $row){
				$trs[] = write_html('tr', '',
					write_html('td', '', 
						write_html('button', 'class="circle_button hoverable ui-state-default" action="deleteAbs" abs_id="'.$row->id.'"', 
							write_icon('trash')
						)
					).
					write_html('td', '', unixToDate($row->day)).
					write_html('td', '', $row->ill == '1' ? write_icon('check') : '&nbsp;').
					write_html('td', '', $row->approved == '1' ? write_icon('check') : '&nbsp;').
					write_html('td', '', $row->comments )
				);
			}
		}
		
		return write_html('table', 'class="tablesorter"',
			write_html('thead', '', 
				write_html('tr', '',
					write_html('th', 'class="{sorter:false} unprintable" width="20"', '&nbsp;').
					write_html('th','width="100"', $lang['date']).
					write_html('th','width="20"', $lang['ill']).
					write_html('th','width="20"', $lang['approved']).
					write_html('th','', $lang['comments'])
				)
			).
			write_html('tbody', '',
				implode('', $trs)
			)
		);
	}
		
}