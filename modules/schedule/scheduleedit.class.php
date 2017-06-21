<?php
/*** Schedule Edit
*
*/

class scheduleEdit {
	public $con = '',
	$con_id = '',
	$type = '',
	$date_begin=false,
	$date_end = false,
	$static_array = array('prof', 'hall', 'tool');
	
	public function __construct($con, $con_id){
		global $MS_settings, $lang;
		$this->con = $con;
		$this->con_id = $con_id;
		$this->first_day_week =  $MS_settings['first_day'];
		$this->day_time_begin =  $MS_settings['day_time_begin'];
		$this->day_time_end =  $MS_settings['day_time_end'];
		$weekend_str = $MS_settings['weekend'];
		$this->week_ends = strpos($weekend_str, ',') !== false ? explode(',', $weekend_str) : array($weekend_str);
		$this->begin_date =  getYearSetting('begin_date');
		$this->end_date =  getYearSetting('end_date');
	}

	public function loadEditLayout(){
		global $thisTemplatePath, $lang, $days_name_arr;
		$editLayout = new stdClass();
		$defDayVal = array();
		for($i=0; $i<7; $i++){
			
			if(in_array($i, $this->week_ends)){
				$editLayout->{"day_".$i."_class_active"} = '';
			} else {
				$def_day_val[] = $i;
				$editLayout->{"day_".$i."_class_active"} = 'ui-state-active';
			}
			$editLayout->{"day_".$i."_name"} = $days_name_arr[$i+1];
		}
			// Copy
		$sql = "SELECT DISTINCT con 
			FROM schedules_date, schedules_times
			WHERE schedules_date.id= schedules_times.rec_id";
		$copy_query = do_query_resource( $sql, DB_year);
		$first_con = '';
		$avaible_cons = '';
		while($copy_type = mysql_fetch_assoc($copy_query)){
			$copy_con = $copy_type['con'];
			if($first_con == '') { $first_con = $copy_con;}
			$avaible_cons .= '<option  value="'.$copy_con.'">'.$lang[$copy_con].'</option>';
		}
		$editLayout->avaible_cons = $avaible_cons;
		$editLayout->first_con_ids = $this->getSelectCon($first_con);
		$editLayout->con  = $this->con;
		$editLayout->con_id = $this->con_id;
		$editLayout->def_day_val = implode(',', $def_day_val);
		$editLayout->day_time_begin = $this->day_time_begin;
		$editLayout->day_time_end = $this->day_time_end;
		return fillTemplate("$thisTemplatePath/schedules_edit.tpl", $editLayout);
	}
	
	// Sessions	
	public function addSequense($post, $date){
		$this_conflict = false;
		$con = $post['con'];
		$con_id = $post['con_id'];
		$days  = (strpos($post['def_day'], ',') !== false) ? explode(',', $post['def_day']) : array($post['def_day']);
		$lesson_time_end = timeToUnix($post['lesson_time_end']);
		$lesson_time_begin = timeToUnix($post['lesson_time_begin']);
		$qt = do_query_obj( "SELECT id FROM schedules_date WHERE con='$con' AND con_id = $con_id AND date=$date", DB_year);
		if(isset($qt->id)){
			$time_rec_id = $qt->id;
			// check if time conflict before continue 
			$chk_sql = "SELECT rec_id FROM schedules_times 
			WHERE rec_id = $time_rec_id
			AND (
				(`begin`<=$lesson_time_begin AND `end`>$lesson_time_begin AND `end`<$lesson_time_end)
				OR (`begin`>$lesson_time_begin AND `begin`<$lesson_time_end AND `end`>=$lesson_time_end)
				OR (`begin`>=$lesson_time_begin AND `end`<=$lesson_time_end)
				OR (`begin`<$lesson_time_begin AND `end`>$lesson_time_end)
			)";
			//echo $chk_sql;
			if( count(do_query_array($chk_sql, DB_year))> 0){
				return false;
			}
		} else {
			do_query_edit( "INSERT INTO schedules_date (con, con_id, date) VALUES ('$con', $con_id, $date) ", DB_year);
			$time_rec_id = mysql_insert_id();
		}
		
		$rec = do_query_obj("SELECT MAX(lesson_no) FROM schedules_times WHERE rec_id=$time_rec_id", DB_year);
		if($post['break_type'] == 'c'){
			$lesson_no = $rec->{'MAX(lesson_no)'} + 1 ;
		} else {
			$lesson_no = 0;
		}
		$active = 1;
		if(do_query_edit("INSERT INTO schedules_times (begin, end, rec_id, lesson_no, type, active) VALUES (".$lesson_time_begin. ", " .$lesson_time_end . ", $time_rec_id, " .$lesson_no.", '".$post['break_type']."', $active)", DB_year)){
			return true;
		} else {
			return false;
		}
	}
	
	public function addMultiSessions($post){
		global $lang;
		$conflict = 0;
		$days  = (strpos($post['def_day'], ',') !== false) ? explode(',', $post['def_day']) : array($post['def_day']);
		if($post['type'] == 'special' && isset($post['b_date']) && $post['b_date'] != ''){
			$cur_date = dateToUnix($post['b_date']);
			$end_date =  ($post['e_date'] != '') ? dateToUnix($post['e_date']) : $cur_date;
			if($cur_date <= $end_date){
				$d = date('d', $cur_date);
				$m = date('m', $cur_date);
				$y = date('Y', $cur_date);
				$i =1;
				while($cur_date <= $end_date){
					if(in_array(date('w', $cur_date), $days)){
						if($this->addSequense($post, $cur_date) == false){
							$conflict++;
						}
					}
					$cur_date = mktime(0, 0, 0, $m, ($d+$i), $y);
					$i++;
				}
			}
		} else {
			foreach( $days as $i){
				if(in_array($i, $days)){
					if($this->addSequense($post, $i) == false){
						$conflict++;
					}
				}
			}
		}
		
		if($conflict == 0){
			return json_encode(array('error' => ''));
		} else {
			return json_encode(array('error' => $conflict.' '.$lang['lesson_time_confict']));
		}
	}

	public function autoGenerateSessions($post){
		global $lang;
		$error = 0;
		$con = $post['con'];
		$con_id = $post['con_id'];
		$begin = timeToUnix($post['day_time_begin']);
		$end = timeToUnix($post['day_time_end']);
		$day_secs = $end - $begin;
		if($post['breaks']!= '' && $post['breaks']!= ''){
			$breaks_time = 0;
			for($b=1; $b<=$post['breaks']; $b++){
				$breaks_time =  $breaks_time + ($post["break_time".$b] * 60); 
			}
		} else {
			$breaks_time = 0;	
		}
		$working_secs = $day_secs - $breaks_time;
		$count_sessions = floor($working_secs /($post['time'] * 60) );
		$break_after = ($post['breaks']!= '' && $post['breaks']!= '') ? round($count_sessions / ($post['breaks'] + 1)) : 0;

		// begin
		if($post['type'] == 'default'){
			$interval_begin = 0;
			$interval_end = 7;
			$looper = 1;
		} else {
			$interval_begin = dateToUnix($post['b_date']);
			$interval_end = dateToUnix($post['e_date']);
			$looper = (60*60*24);
		}
		
		$applied_day = $post['def_day'] != '' ? explode(',', $post['def_day']) : array();
		for($day=$interval_begin; $day<=$interval_end; $day+=$looper){ 
			if(($day<7 && in_array($day, $applied_day)) || in_array(date('w', $day), $applied_day)){
				$qt = do_query( "SELECT id FROM schedules_date WHERE con='$con' AND con_id = $con_id AND date=$day", DB_year);
				if($qt['id'] != ''){
					$time_rec_id = $qt['id'];
					// clean old data if exists
					do_query_edit("DELETE FROM schedules_times WHERE rec_id =$time_rec_id", DB_year);
					//$this->clearlessonAttributes($con, $con_id, false);
				} else {
					do_query_edit( "INSERT INTO schedules_date (con, con_id, date) VALUES ('$con', $con_id, $day) ", DB_year);
					$time_rec_id = mysql_insert_id();
				}
				$count_break =1;
				$last_session_end = $begin;

				for($i=1; $i<=$count_sessions; $i++){
					$session_begin = $last_session_end;
					$lesson_no = $i;
					$session_end = $session_begin + ($post['time']*60);
					$type='c';
					do_query_insert('schedules_times', 'rec_id, begin, end, type, lesson_no, active', "$time_rec_id, $session_begin, $session_end, '$type', $lesson_no, 1", DB_year);
					$last_session_end = $session_end;
					
					if($i == $count_break * $break_after && $count_break<=$post['breaks']){
						$lesson_no = 0;
						$session_end = $last_session_end + ($post["break_time".$count_break] *60);
						$type='b';
						do_query_insert('schedules_times', 'rec_id, begin, end, type, lesson_no, active', "$time_rec_id, $last_session_end, $session_end, '$type', $lesson_no, 1", DB_year);
						$last_session_end = $session_end;
						$count_break++;
					} 
				}
				
				// add lesson is there is time left in the day
				$time_left =  $working_secs % ($post['time'] * 60);
				if($time_left > (15*60)){
					$session_begin = $last_session_end;
					$lesson_no = $i+1;
					$session_end = $end;
					$type='c';
					do_query_insert('schedules_times', 'rec_id, begin, end, type, lesson_no, active', "$time_rec_id, $session_begin, $session_end, '$type', $lesson_no, 1", DB_year);
				}
			}
		}
		if($error == 0){
			return json_encode(array('error' => ''));
		} else {
			return json_encode(array('error' => $lang['error_auto_generate']));
		}
	}
	
	public function copySessions($post){
		global $lang;
		$error = 0;
		$con = $post['con'];
		$con_id = $post['con_id'];
		$copy_con = $post['copycon'];
		$copy_conid = $post['copyconid'];
		
		if($post['type'] == 'default'){
			$interval_begin = 0;
			$interval_end = 7;
			$looper = 1;
		} else {
			$interval_begin = dateToUnix($post['b_date']);
			$interval_end = dateToUnix($post['e_date']);
			$looper = (60*60*24);
		}
		
		for($day=$interval_begin; $day<=$interval_end; $day+=$looper){ 
			$qt = do_query( "SELECT id FROM schedules_date WHERE con='$con' AND con_id = $con_id AND date=$day", DB_year);
			if($qt['id'] != ''){
				$time_rec_id = $qt['id'];
				// clean old data if exists
				do_query_edit("DELETE FROM schedules_times WHERE rec_id =$time_rec_id", DB_year);
				do_query_edit("DELETE FROM schedules_lessons WHERE rec_id =$time_rec_id", DB_year);
			} else {
				do_query_edit( "INSERT INTO schedules_date (con, con_id, date) VALUES ('$con', $con_id, $day) ", DB_year);
				$time_rec_id = mysql_insert_id();
			}
			$org_row = do_query_obj("SELECT id, date FROM schedules_date WHERE con='$copy_con' AND con_id=$copy_conid", DB_year);
			$org_time_sql = "INSERT INTO schedules_times (begin, end, lesson_no, type, active, rec_id) 
			SELECT begin, end, lesson_no, type, active, $time_rec_id FROM schedules_times WHERE rec_id=".$org_row->id;
			//echo $org_time_sql;
			if(!do_query_edit($org_time_sql, DB_year)){
				$error++;
			}	
		}
			
		if($error > 0){
			$answer['error'] =  $lang['error-copy_structure'];
		} else {
			$answer['error'] = '';
		}
		return json_encode($answer);
	}

	public function getSelectCon($con){ 
		global $sms;
		$out = '';
		if($con != false && $con!=''){
			$sql = "SELECT DISTINCT con_id 
			FROM schedules_date, schedules_times
			WHERE schedules_date.con ='$con'
			AND schedules_date.id= schedules_times.rec_id";
			$rows = do_query_array( $sql, DB_year);
			foreach($rows as $row){
				$out .= '<option value="'.$row->con_id.'">'.$sms->getAnyNameById($con, $row->con_id).'</option>';
			}
		}
		return $out;
	}

	public function joinSessions($post){
		global $lang;
		$error = false;
		$con = $post['con'];
		$con_id = $post['con_id'];
		$sessions = (strpos($post['sessions'], ',') !== false) ? explode(',', $post['sessions']) : array($post['sessions']);
		$date = '';
		foreach($sessions as $s){
			$d = explode('-', $s);
			if($date != ''  && $date != $d[0]){
				$error = $lang['join_error-same_date'];
			} else {
				$date = $d[0];
			}
			$lesson_nos[] = $d[1];
		}
		sort($lesson_nos);
		for($x=0; $x<(count($lesson_nos)-1); $x++){	
			if(($lesson_nos[$x] + 1) != $lesson_nos[($x + 1)]){
				$error = $lang['join_error-non_sequential'];
			}
		}
		if(!$error){
			$first_lesson_no = $lesson_nos[0];
				// get times
			$schedule = new schedule($con, $con_id);
			$sessions = $schedule->getSessionsStructure($date);
			$begin = 0 ; $end = 0; $type=''; $active=1;
			foreach($sessions as $session){
				if(in_array($session->lesson_no , $lesson_nos)){
					$begin = $begin==0 || $session->begin < $begin ? $session->begin : $begin;
					$end = $end==0 || $session->end > $end ? $session->end : $end;
					$type = $type == '' ? $session->type : $type;					
				} else {
					$default_sessions[] = $session;
				}
			}

			$rec_id = $this->getRecId($con, $con_id, $date, true);
				// clean old values
			do_query_edit("DELETE FROM schedules_times WHERE rec_id=$rec_id", DB_year);
				// Reconstruct day 
			foreach($default_sessions as $session){
				do_query_edit("INSERT INTO schedules_times (begin, end, rec_id, lesson_no, type, active) VALUES ( $session->begin, $session->end, $rec_id, $session->lesson_no, '$session->type', $session->active)", DB_year);
			}
				// insert new values
			if(do_query_edit("INSERT INTO schedules_times (begin, end, rec_id, lesson_no, type, active) VALUES ( $begin, $end, $rec_id, $first_lesson_no, '$type', $active)", DB_year) == false){
				$error = $lang['error_updating'];
			}
		}
		
		if($error == false){
			$days = array($date => $schedule->loadDay($date));
			return json_encode(array('error' => '', 'days' => $days));
		} else {
			return json_encode(array('error' => $error));
		}
	}

	public function resizeSessions($post){
		global $lang;
		$error = false;
		$con = $post['con'];
		$con_id = $post['con_id'];
		if(strpos($post['sessions'], ',') !== false){
			$error = $lang['select_only one_session'];
		} else {
			$d = explode('-', $post['sessions']);
			$date = $d[0];
			$lesson_no = $d[1];
		}
		if(!$error){
				// get times
			$schedule = new schedule($con, $con_id);
			$sessions = $schedule->getSessionsStructure($date);
			foreach($sessions as $session){
				if($session->lesson_no  == $lesson_no){
					$session->begin = timeToUnix($post['begin']);
					$session->end = timeToUnix($post['end']);
				}
			}
			
			$rec_id = $this->getRecId($con, $con_id, $date, true);
				// clean old values
			do_query_edit("DELETE FROM schedules_times WHERE rec_id=$rec_id", DB_year);
				// Reconstruct day 
			foreach($sessions as $session){
				if(do_query_edit("INSERT INTO schedules_times (begin, end, rec_id, lesson_no, type, active) VALUES ( $session->begin, $session->end, $rec_id, $session->lesson_no, '$session->type', $session->active)", DB_year) == false){
					$error = $lang['error_updating'];
				}
			}
		}
		
		if($error == false){
			$days = array($date => $schedule->loadDay($date));
			return json_encode(array('error' => '', 'days' => $days));
		} else {
			return json_encode(array('error' => $error));
		}
	}
	
	// Reset
	public function deleteSessions($post){
		$errors = 0;
		$con = $post['con'];
		$con_id = $post['con_id'];
		if($post['del_radio'] == 'selc'){
			$sessions = (strpos($post['sessions'], ',') !== false) ? explode(',', $post['sessions']) : array($post['sessions']);
			foreach($sessions as $s){
				$d = explode('-', $s);
				$date = $d[0];
				$lesson_no = $d[1];
				$sql_req_id = do_query_obj("SELECT id FROM schedules_date WHERE con='$con' AND con_id=$con_id and date=$date", DB_year);
				if(isset($sql_req_id->id) && $sql_req_id->id != ''){
					$rec_id = $sql_req_id->id;
					if(!do_query_edit( "DELETE FROM schedules_times WHERE rec_id =$rec_id AND lesson_no=$lesson_no", DB_year)){
						$errors++;	
					} else {
						//echo "DELETE FROM schedules_times WHERE rec_id =$rec_id AND lesson_no=$lesson_no";
						if(!do_query_edit( "DELETE FROM schedules_lessons WHERE rec_id = $rec_id AND lesson_no=$lesson_no", DB_year)){
							$errors++;
						} 
					}
				}
			}
		} else {
			if($post['del_radio'] == 'all'){
				$sql = "SELECT * FROM schedules_date WHERE con='$con' AND con_id=$con_id";
			} elseif($post['del_radio'] == 'spf'){
				$sql = "SELECT * FROM schedules_date WHERE con='$con' AND con_id=$con_id AND date > ".dateToUnix($post['bd'])." AND date < ".dateToUnix($post['ed']);
			} elseif($post['del_radio'] == 'exp'){
				$sql = "SELECT * FROM schedules_date WHERE con='$con' AND con_id=$con_id AND date >7";
			}
			$records = do_query_array( $sql, DB_year);
			foreach($records as $rec){
				$rec_id = $rec->id;
				if(!do_query_edit( "DELETE FROM schedules_times WHERE rec_id =$rec_id", DB_year)){
					$errors++;	
				} else {
					$this->clearlessonAttributes($rec->con, $rec->con_id, $rec->date) ;
				}
			} 
		}
		return $errors > 0 ? json_encode(array('error' => $lang['error_updating'])) : json_encode(array('error' => ''));
	}
	
	public function deleteLessons($post){
		global $lang;
		$errors = 0;
		$cells = array();
		$con = $post['con'];
		$con_id = $post['con_id'];
		if($post['del_radio'] == 'selc'){
			$sessions = (strpos($post['sessions'], ',') !== false) ? explode(',', $post['sessions']) : array($post['sessions']);
			foreach($sessions as $s){
				$d = explode('-', $s);
				$date = $d[0];
				$lesson_no = $d[1];
				if($this->clearlessonAttributes($con, $con_id, $date, $lesson_no) == false){
					$errors++;
				} else {
					$cells[$s] = schedule::reloadCell($con, $con_id, $date, $lesson_no, true);
				}
			}
		} else {
			if($post['del_radio'] == 'all'){
				if($this->clearlessonAttributes($con, $con_id, false) == false){
					$errors++;
				}
			} elseif($post['del_radio'] == 'spf'){
				if($this->clearlessonAttributes($con, $con_id, array(dateToUnix($post['bd']), dateToUnix($post['ed']))) == false){
					$errors++;
				}
			} elseif($post['del_radio'] == 'exp'){
				if($this->clearlessonAttributes($con, $con_id, 'exp') == false){
					$errors++;
				}
			}
		}
		if($errors > 0){
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		} else {
			$answer['id'] = "";
			$answer['error'] = '';
			if(count($cells) > 0){
				foreach($cells as $seq => $cell){
					$answer['cells'][$seq] = $cell;
				}
			}
		}	
		return json_encode($answer);		
	}
	
	public function clearlessonAttributes($con, $con_id, $date, $lesson_no=false){
		$error  = false;
		$cons = array("(schedules_date.con='$con' AND schedules_date.con_id=$con_id)");
		$childs = getChildsArr($con, $con_id);
		foreach($childs as $array){
			$cons[] = "(schedules_date.con='".$array[0]."' AND schedules_date.con_id=".$array[1].")";
		}
		
		$sql = "DELETE schedules_lessons.* FROM schedules_lessons, schedules_date 
			WHERE (".
				implode(' OR ', $cons).
			") 
			AND schedules_date.id=schedules_lessons.rec_id".
			($lesson_no != false ? " AND schedules_lessons.lesson_no=$lesson_no" : '');
			
		if($date == 'exp'){
			$sql .= ' AND schedules_date.date>7 ' ;
		} elseif(is_array($date)){
			$begin_date = $date[0];
			$end_date = $date[1];
			$sql .= ' AND schedules_date.date>=$begin_date AND schedules_date.date <= $end_date' ;
		} elseif($date != false){
			$sql .= ' AND schedules_date.date='.$date ;
		}
		
		return do_query_edit( $sql,DB_year);
	}

	// Attribute lesson
	public function attrLessonLayout($lessons_id){
		global $thisTemplatePath, $sms;
		$lessonsReq = strpos($lessons_id, ',') !== false ? explode(',', $lessons_id) : array($lessons_id);
		
		$attrLesson = new stdClass();
		$attrLesson->sessions_str = $lessons_id;
		
		foreach($lessonsReq as $s){
			$seq = explode('-', $s);
			$session = new stdClass();
			$session->con = $this->con;
			$session->con_id = $this->con_id;
			$session->date = $seq[0];
			$session->lesson_no =  $seq[1];
			$schedule = new schedule($this->con, $this->con_id);
			$lessons = $schedule->getSessionLessons($session, $session->date );
			// check if all selected 
			$lessons_contents = array();
			if($lessons != false && count($lessons) > 0) { 
				$seek = true;
				$week_no =1;
				foreach($lessons as $lesson){
					$lesson->week_no =$week_no;
					if(!isset($chk_rule)){
						$rl = explode('/', $lesson->rule);
						$frequency = $rl[1];
						$chk_service = $lesson->rule; 
						$chk_prof = $lesson->rule; 
						$chk_hall = $lesson->rule;  
						$chk_frequency = $frequency; 
					}
					if($chk_service != $lesson->rule || $chk_prof != $lesson->rule || $chk_hall != $lesson->rule || strpos($lesson->rule, '/'.$chk_frequency) === false){
						$all_equal = false;
					}
					$lesson->master_con = $this->con.'-'.$this->con_id;
					$lesson->avaible_student_options  = write_select_options($this->getAvaibleCons($this->con, $this->con_id),$lesson->con.'-'.$lesson->con_id, false);
					$lesson->avaible_services_options = write_select_options($this->getAvaibleService($lesson->con, $lesson->con_id), $lesson->services, false);
					
					$lesson->avaible_profs_options = write_select_options($this->getAvaibleProfs($lesson->services, false), $lesson->prof, false);
					$lesson->avaible_halls_options = write_select_options($this->getAvaibleHalls($session, false), $lesson->hall, false);
					$con_name = $sms->getAnyNameById($lesson->con, $lesson->con_id);
					if(!isset($lessons_contents[$con_name])){
						$lessons_contents[$con_name] = array();
					}
						// record the current frequency
					$frequency = explode('/', $lesson->rule); 
					$f = $frequency[1];
					
						// hide or display group division
					if($lesson->con == $this->con ){
						$lesson->group_hide_class = 'hidden';
						$attrLesson->{"division-1_class"} = "ui-state-active";
						$attrLesson->{"division-2_class"} = "";
					} else {
						$attrLesson->{"division-1_class"} = "";
						$attrLesson->{"division-2_class"} = "ui-state-active";
					}

					$lessons_contents[$con_name][] = fillTemplate("$thisTemplatePath/schedule_attr_lesson.tpl", $lesson);
					$week_no++;
				}
			} else {
				$lesson = new stdClass();
				$lesson->master_con = $this->con.'-'.$this->con_id;
				$lesson->avaible_student_options  = write_select_options($this->getAvaibleCons($this->con, $this->con_id),'', false);
				$lesson->avaible_services_options = write_select_options($this->getAvaibleService($session->con, $session->con_id), '', false);
				if($session->con == 'class'){
					$def_hall = getClassDefRoom(safeGet($_GET['con_id']));
				} else {
					$def_hall = '';
				}
				$lesson->avaible_halls_options = write_select_options($this->getAvaibleHalls($session, false), $def_hall, false);
				$lesson->rule = '1/1';
				$lesson->group_hide_class = 'hidden';
				$lesson->week_no = 1;
				$lessons_contents[$session->con][] = fillTemplate("$thisTemplatePath/schedule_attr_lesson.tpl", $lesson);
				$attrLesson->{"division-1_class"} = "ui-state-active";
				$attrLesson->{"division-2_class"} = "";
				$f =1;
			}
			
			$content ='';
			foreach($lessons_contents as $con_name => $con_weeks){
				$content .= write_html('tr', 'class="division_weeks_tr"',
					write_html('td', 'width="30" align="center" style="vertical-align:bottom"', write_html('h3', ' class="rotated"', $con_name)).
					'<td>'.implode('</td><td>', $con_weeks).'</td>'
				);	
			}
		} 
		$attrLesson->lessons_content =  $content ;

			// Frequency
		$freqs = array(1, 2,4);
		foreach($freqs as $fs){
			if($fs == $f){
				$attrLesson->{"frequency-".$fs."_class"} = "ui-state-active";
			} else {
				$attrLesson->{"frequency-".$fs."_class"} = " ";
			}
		}
			// division
		if(in_array($this->con, array('student', 'group'))){
			$attrLesson->division_tr_class = 'hidden';
		}
		return fillTemplate("$thisTemplatePath/schedule_attribute.tpl", $attrLesson);
	}
	
	public function getAvaibleService($con, $con_id){
		global $sms;
		$avaible_services = array('0'=>'');
		$obj = $sms->getAnyObjById($con, $con_id);
		$services = $obj->getServices();
		
		//print_r($services);
		foreach($services as $service){
			
			//if($service->schedule == 1){
				$avaible_services[$service->id] =$service->getName();
			//}
		}
		return $avaible_services;
	}
	
	public function getAvaibleCons($con, $con_id){
		global $sms;
			// groups which the lesson will be applied
		$avaible_group = array();
		$avaible_group[$con.'-'.$con_id] = $sms->getAnyNameById($con, $con_id);
		if(in_array($con, array('class', 'level'))){
			if($con == 'level'){
				$child_groups = getGroupsIdsByLevel($con_id);
				$child_classes = getClassesByLevel($con_id);
			} elseif($con == 'class'){
				$child_groups = getGroupsIdsByClass($con_id);
			}
			if(isset($child_classes) && count($child_classes) >0){
				foreach($child_classes as $child_id){
					$class = new Classes($child_id);
					$avaible_group["class-".$child_id] = $class->getName();
				}
			}
			if(isset($child_groups) && $child_groups !== false){
				foreach($child_groups as $group_id){
					$group = new Groups($group_id);
					$avaible_group["group-".$group_id] = $group->getName();
				}
			}
		}
		return $avaible_group;
	}
	
	public function getAvaibleProfs($session, $service = false, $chk_avaible = false){
		$profs= array();
		if($service == false){
			$prs = do_query_array( "SELECT * FROM profs", DB_student);
			foreach($prs as $p){
				$prof = new Profs($p->id);
				$profs[$p->id] = $prof->getName();
			}
		} else {
			if($chk_avaible == false){
				$prs = do_query_array( "SELECT DISTINCT id FROM profs_materials WHERE services=$service", DB_student);
				foreach($prs as $row){
					$prof = new Profs($row->id);
					$profs[$row->id] = $prof->getName();
				}
			} else {
				$db_year = DB_year;
				$db_student = DB_student;
				$con =  $session->con;
				$con_id =  $session->con_id;
				$date =  $session->date;
				$def_date = date('w', $date);
				$lesson_no =  $session->lesson_no;
				$times = $this->getSessionTime($con, $con_id, $lesson_no, $date);
			//	print_r($times);
				$t_begin = $times->begin;
				$t_end = $times->end;
				$s = "SELECT DISTINCT $db_year.schedules_lessons.prof
				FROM $db_year.schedules_date, $db_year.schedules_lessons, $db_year.schedules_times, $db_student.profs_materials
				WHERE $db_student.profs_materials.services=$service
				AND $db_student.profs_materials.id=$db_year.schedules_lessons.prof
				AND $db_year.schedules_date.id = $db_year.schedules_times.rec_id
				AND $db_year.schedules_date.id = $db_year.schedules_lessons.rec_id
				AND (
					$db_year.schedules_date.date = $date
					OR $db_year.schedules_date.date=$def_date
				)
				AND (
					($db_year.schedules_times.begin < $t_begin AND $db_year.schedules_times.end <= $t_begin)
					OR ($db_year.schedules_times.begin >= $t_end AND $db_year.schedules_times.end > $t_end)
				)";
						//echo $s;
				$prs = do_query_array( $s, DB_year);
				foreach($prs as $p){
					$profs[$p->prof] = getEmployerNameById($p->prof);
				}
			}
		}
		return $profs;
	}
	
	public function getProfAvaibilty($id, $sessions, $service){
		$d =explode('-', $sessions);
		$date = $d[0];
		$lesson_no = $d[1];
		$avaible= true;
		$con = $this->con;
		$con_id = $this->con_id;
	
		// check if prof have another lesson in the same squences
		$times = $this->getSessionTime($con, $con_id, $lesson_no, $date);
	//	print_r($times);exit;
		$t_begin = $times->begin;
		$t_end = $times->end;
		$s = "SELECT  schedules_times.*, schedules_lessons.* 
		FROM schedules_lessons, schedules_times , schedules_date
		WHERE schedules_date.date = $date	
		AND schedules_date.id =  schedules_lessons.rec_id 
		AND schedules_lessons.prof = '$id'
		AND schedules_date.id = schedules_times.rec_id 
		AND schedules_times.lesson_no =  schedules_lessons.lesson_no
		AND (
			(schedules_times.begin >= $t_begin AND schedules_times.end <= $t_end)
			OR (schedules_times.begin < $t_begin AND schedules_times.end > $t_begin AND schedules_times.end < $t_end)
			OR (schedules_times.begin > $t_begin AND schedules_times.begin < $t_end AND schedules_times.end > $t_end)
			OR (schedules_times.begin < $t_begin AND schedules_times.end > $t_end)
		)";
		$r = do_query_resource( $s, DB_year);
		$t = mysql_num_rows($r);
		if($t > 0){
			$avaible= false;
			//echo $s;
		} 
		
		// check if sequence is in avaible prof time
		$s = "SELECT begin FROM schedules_static_time 
		WHERE con='prof' 
		AND con_id=$id 
		AND date=$date 
		AND	(
			(begin >= $t_begin AND end <= $t_end)
			OR (begin <= $t_begin AND begin <= $t_end)
			OR (begin >= $t_begin AND begin <= $t_end)
		)";
		$time_prof = do_query_resource($s, DB_year);
		if(mysql_num_rows($time_prof) > 0){
			$avaible= false;
		//	echo 'prof isnt avaible on this time';
		}	
	
		return $avaible;
	}
	
	public function getAvaibleHalls($session, $chk_avaible = true ){
		$halls =array();
		if($chk_avaible == false){
			$halls_query = do_query_array( "SELECT * FROM halls");
			foreach($halls_query as $row){
				$halls[$row->id] = $row->name;
			}
		} else {
			//chek if hall is allready reserved
			$con =  $session->con;
			$con_id =  $session->con_id;
			$date =  $session->date;
			$def_date = date('w', $date);
			$lesson_no =  $session->lesson_no;
			$times = $this->getSessionTime($con, $con_id, $lesson_no, $date);
		//	print_r($times);
			$t_begin = $times->begin;
			$t_end = $times->end;
			$s = "SELECT DISTINCT schedules_lessons.hall
			FROM schedules_date, schedules_lessons, schedules_times 
			WHERE schedules_date.id = schedules_times.rec_id
			AND schedules_date.id = schedules_lessons.rec_id
			AND (
				schedules_date.date = $date
				OR schedules_date.date=$def_date
			)
			AND (
				(schedules_times.begin < $t_begin AND schedules_times.end <= $t_begin)
				OR (schedules_times.begin >= $t_end AND schedules_times.end > $t_end)
			)";
					//echo $s;
			$ha = do_query_array( $s, DB_year);
			$all = Halls::getList();
			foreach($all as $h){
				if(!in_array($h->id, $ha)){
					$hall = new Halls($h->id);
					$halls[$h->hall] = $hall->getName();
				}
			}
		}
		return $halls;
	}

	public function getServiceProf($con, $con_id, $service){
		// check if a prof have been attributed to serve this subject for the current Con
		$sql = "SELECT schedules_lessons.prof FROM schedules_lessons, schedules_date 
		WHERE schedules_date.con='$con' 
		AND schedules_date.con_id=$con_id
		AND schedules_date.id=schedules_lessons.rec_id
		AND schedules_lessons.services=$service";
		$lesson = do_query_obj($sql, DB_year);
		if(isset($lesson->prof) && $lesson->prof != ''){
			$prof =  $lesson->prof;
			return $prof;
		} else {
			return false;
		}
	}
	
	public function submitLesson($post){
		global $lang;
		$answer = array();
		$errors = 0;
		$squences = strpos($post['sessions'], ',') !== false ? explode(',', $post['sessions']) : array($post['sessions']); 
		$cells = array();		
		// clear session
		foreach($squences as $seq ){
			if( $post['type'] != 'default'){
				$s = explode('-', $seq);
				$beg_date = $s[0];
				$lesson_no = $s[1];
				$def_date = date('w', $beg_date);
				$c_date = first_route($beg_date, $def_date);
				while($c_date <= dateToUnix($post['e_date'])){
					$this->clearlessonAttributes($post['con'], $post['con_id'], $c_date, $lesson_no);
					$c_date = $c_date + 604800;
				}
			} else {
				$s = explode('-', $seq);
				$date = $s[0];
				$lesson_no = $s[1];
				$this->clearlessonAttributes($post['con'], $post['con_id'], $date, $lesson_no)	;
			}
		}
			
		for($i=0; $i<count($post['lesson_con_id']); $i++){
			$con_str = explode('-', $post['lesson_con_id'][$i]);
			$con = $con_str[0];
			$con_id = $con_str[1];
			$rule = $post['rule'][$i];
			$profs = $post['profs'][$i]; 
			$service = $post['service'][$i]; 
			$halls = isset($post['halls'][$i]) ? $post['halls'][$i] : ''; 
			
			foreach($squences as $seq ){
				if($post['type'] != 'default'){
					$s = explode('-', $seq);
					$beg_date = $s[0];
					$lesson_no = $s[1];
					$def_date = date('w', $beg_date);
					$c_date = first_route($beg_date, $def_date);
					while($c_date <= dateToUnix($post['e_date'])){
						if($this->update_lessons($c_date.'-'.$lesson_no, $con, $con_id, $service, $profs, $halls, $rule) == false){
							$errors++;
						} else {
							$cells[$seq] = schedule::reloadCell($this->con, $this->con_id, $c_date, $lesson_no, true);
						}
						$c_date = $c_date + 604800;
					}
					
				} else {
					if($this->update_lessons($seq, $con, $con_id, $service, $profs, $halls, $rule) == false){
						$errors++;
					} else {
						$s = explode('-', $seq);
						$date = $s[0];
						$lesson_no = $s[1];
						$cells[$seq] = schedule::reloadCell($this->con, $this->con_id, $date, $lesson_no, true);
					}
				}
				
			}
		}
		
		if($errors > 0){
			$answer['id'] = "";
			$answer['error'] = $errors .' '. $lang['error-attr_lesson'];
		} else {
			$answer['id'] = "";
			$answer['error'] = '';
			foreach($cells as $seq => $cell){
				$answer['cells'][$seq] = $cell;
			}
		}	
		return json_encode($answer);
	}
	
	public function update_lessons( $squences, $con, $con_id, $mats, $profs, $halls, $rule){
		$d =explode('-', $squences);
		$date = $d[0];
		$lesson_no = $d[1];
		// select rec_id for the current lesson in the date
		$rec_id = $this->getRecId($con, $con_id, $date, true);

		$sql = "INSERT INTO schedules_lessons  (rec_id, lesson_no, services, prof, hall, rule) VALUES ($rec_id, $lesson_no, '$mats', '$profs', '$halls', '$rule')";
		if(do_query_edit( $sql, DB_year)){
			return true;
		}else {
			return false;
		}
	}
	
	public function getRecId($con, $con_id, $date, $insert=false){
		$sql = "SELECT schedules_date.*
		FROM schedules_date
		WHERE schedules_date.con='$con'
		AND schedules_date.con_id = $con_id 
		AND schedules_date.date =$date";
		$qu = do_query_obj( $sql, DB_year);
		if(isset($qu->id)) {
			//echo $sql;
			return $qu->id;
		} else {
			if($insert) {
				if(do_query_edit( "INSERT INTO schedules_date (con, con_id, date) VALUES('$con', $con_id, $date)", DB_year)){
					return mysql_insert_id();
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	// Others
	public function getSessionTime($con, $con_id, $lesson_no, $date){
		$schedule = new schedule($con, $con_id);
		$sessions = $schedule->getSessionsStructure($date);
		foreach($sessions as $session){
			if($session->lesson_no == $lesson_no){
				return $session;
			}
		} 
		return false;
	}
	
}