<?php
/*** Calendar Class **/
class Calendars  {
	private $thisTemplatePath = 'modules/calendar/templates';
	
	public $con, $con_id, $begin_day, $end_day, $week_end;

	public function __construct($con='', $con_id=''){
		global $sms;
		if($con=='') $this->con = $_SESSION['group'];
		if($con_id=='') $this->con_id = $_SESSION['user_id'];
		$year = do_query_obj("SELECT * FROM years WHERE year=".$_SESSION['year'], DB_student);
		$this->begin_day = $year->begin_date;
		$this->end_day = $year->end_date;
		$this->week_end = explode(',' , $sms->getSettings('weekend'));
	}
	
	public function loadMainLayout(){
		global $lang, $sms;
		$this->getEvents();	
		$total_active_days = $this->getTotalWorkingDay();
		$out = write_html('div', 'class="ui-corner-top ui-widget-header reverse_align" style="position:relative"', 
			write_html('h2', 'class="big_title"', $lang['calender'].' '.$_SESSION['year'].' - '. ($_SESSION['year']+1)).
			write_html('ul', 'style="position:absolute;  top:0px; list-style:none; padding:5px; margin:0; font-size:80%"',
				write_html('li', '', 
					write_html('b', '',  $lang['no_total_hours_class_by_year'].' ').
					round($total_active_days * $sms->getSettings('hours_by_day')).' '.$lang['hours']
				).
				write_html('li', '', 
					write_html('b', '',  $lang['no_total_days_class_by_year'].' ').
					round($total_active_days).' '.$lang['days']
				)
			)
		).
		write_html('div', 'class="ui-corner-bottom ui-widget-content transparent_div"',
			write_html('div', 'class="toolbox"',
				(getPrvlg('cal_event_edit') || getPrvlg('cal_holiday_edit') ? write_html('a', 'action="addEvents"', write_icon('plus').$lang['add']) : '' ).
				write_html('a', 'action="print_pre" rel="#module_calendar" plugin="print"', write_icon('print').$lang['print'])
			).
			$this->viewByYear()
		);
		return $out;
	}
	
	public function getTotalWorkingDay($date_begin='', $date_end=''){
		global $sms;
		if($date_begin=='') $date_begin= $this->begin_day;
		$year_begin = $sms->getYearSetting('begin_date');
		if($date_end=='') $date_end = $this->end_day;
		$all = ($date_end - $date_begin) / (60*60*24);
		$hols = $this->getHolidays();
		$count_hol = 0;
		foreach($hols as $hol){
			if($date_begin<=$hol && $hol<=$date_end){
				$count_hol++;
			}
		}
		return $all - $count_hol;
		/*$counter =0;
		$b_d = date('d', $this->begin_day);
		$b_m = date('m', $this->begin_day);
		$b_y = date('Y', $this->begin_day);
		
		$cur_date = $this->begin_day;
		$i=1;
		while($cur_date <= $this->end_day){
			if(!in_array(date('w', $cur_date) , $this->week_end)){
				$holiday = do_query_array( "SELECT id FROM holidays WHERE dates=$cur_date", $sms->db_year);
				if(!$holiday || count($holiday) == 0){
					$counter++;
				}
				$cur_date = mktime(0, 0, 0, $b_m, ($b_d+$i), $b_y);
				$i++;
			}
		}
		return $counter;*/
	}
	
	public function viewByYear(){
		global $lang, $sms;
		$months= array();
		$begin_month = 8;
		$end_month = 7;
		
		for($i = 0 ; $i<12; $i++){
			$m = $begin_month + $i;
			$months[] = mktime(0,0,0, $m,1,$_SESSION['year']);
		}
		$table_html = '<table cellspacing="1" border="0" class="calender">
			<thead>
				<tr>';
					foreach($months as $month){
						$table_html .= write_html('th', '', date('M - Y', $month));
					}
				$table_html .= '</tr>
			</thead>
			<tbody>
				<tr>';
					foreach($months as $month){
						$table_html .= write_html('td', '', $this->viewByMonth($month));
					}
				$table_html .= '</tr>
			</tbody>
			<tfoot>
				<tr>';
					$working_days_array = array();
					foreach($months as $month){
						$working_days_array[$month]= $this->getTotalWorkingDay(
							mktime(0,0,0, date('m', $month) ,1, date('Y', $month)), 
							mktime(0,0,0, date('m', $month)+1,-1, date('Y', $month))
						);
						$table_html .= write_html('th', '', $lang['hours'].': '.round( $working_days_array[$month] * $sms->getSettings('hours_by_day')));
						
					}
				$table_html .= '</tr>
				
				<tr>';
					foreach($months as $month){
						$table_html .= write_html('th', '', $lang['days'].': '.round($working_days_array[$month]) );
					}
				$table_html .= '</tr>
			</tfoot>
		</table>';
		return $table_html;
	}

	public function viewByMonth($month){
		global $lang;
		$month_active_days =0;
		$table_html = '<ul cellpadding="1" cellspacing="1" border="0">';
			for($i=1; $i<= 31; $i++){
				$m = date('m', $month);
				$y = date('Y', $month);
				$cur = mktime(0,0,0, $m, $i, $y);
				$table_html .= $this->viewByDay($cur);
			}
		$table_html .= '</ul>';
		return $table_html;
	}
	
	public function viewByDay($cur){
		global $days_name_arr, $lang;
		if(in_array(date('w', $cur), $this->week_end)){
			$td1 = date('d', $cur) .' '. $days_name_arr[date('w', $cur) + 1];
			$td2 = '&nbsp;';
			$class = 'h_weekend';
		} elseif(in_array($cur, $this->getHolidays())){ // holiday
			$class = 'h_holidays';
		} elseif($cur < $this->begin_day || $cur > $this->end_day){
			$class = 'h_blank';		
		} else {
			$class = 'h_active';
		}
		
		$dayEvents = cEvents::getEventsByDay($cur, $_SESSION['group'] , $_SESSION['user_id']);
		/*if(isset($this->events)  && $this->events!= false && count($this->events) > 0 ){
			foreach($this->events as $ev){
				if(($ev->begin_date == $cur && $ev->end_date== '') || ($ev->begin_date <= $cur && $cur <= $ev->end_date) ){
					$dayEvents[] = new Events($ev->id);
				}
			}
		}*/
		
		// Events
		if( count($dayEvents) > 0 ){
			$tooltip = '<table class="result tooltip" cellspacing="2"><tbody>';
				$count_ev = 0;
				foreach($dayEvents as $event){
					$count_ev++;
					$tooltip .= '<tr>
						<td>'.$event->getName().'</td>
						<td class="event_time">'.unixToTime($event->begin_time).'</td>
						<td class="event_time">'.unixToTime($event->end_time).'</td>
					</tr>';
				}
				$tooltip .= '</tbody></table>';
			$flag = write_html('button', 'class="ui-state-highlight mini_circle_button"', write_icon('flag')).
			write_html('em', 'class="count"',  $count_ev);
		} else {
			$tooltip = '';
			$flag='';
		}
								
		return write_html('li', 'class="'.$class.'"', 
			write_html('a', 'action="openCalendarDay" day="'.unixToDate($cur).'"  class="hand hoverable"',
				date('d', $cur) .' '. write_html('span', 'class="dayname"', $days_name_arr[date('w', $cur) + 1]).
				$flag.
				$tooltip
			)
		);

	}
	
	public function getHolidays(){
		global $sms;
		
		if(!isset($this->holidays)){
			$holidays= array();
			$con = $_SESSION['group'];
			$con_id= $_SESSION['user_id'];
			$where = cEvents::GetEventUserCon($_SESSION['group'], $_SESSION['user_id']);					
			$hols = do_query_array( 
				"SELECT * FROM holidays 
				WHERE ".implode(' OR ', $where)
			, $sms->db_year);
			
			foreach($hols as $hol){
				$holidays[] = $hol->dates;
			}
			$this->holidays = $holidays;
		}
		return $this->holidays;
	}
	
	public  function getEvents(){
		global $sms;
		if(!isset($this->events)){
			$con_arr = cEvents::GetEventUserCon($_SESSION['group'], $_SESSION['user_id']);
			$sql = "SELECT DISTINCT events.* FROM events, events_con 
			WHERE events.id = events_con.event_id
			AND (
				(events.begin_date>=$this->begin_day AND events.begin_date<=$this->end_day)
				OR (events.begin_date=$this->begin_day AND events.end_date='')
			)
			AND (
				(events.user_id=".$_SESSION['user_id'].")
				OR ".implode(' OR ', $con_arr)."
			)";

			//echo $sql; exit;
			$this->events = do_query_array($sql, $sms->db_year);
		}
		return $this->events;
	}

	public function getDate($date){
		global $lang, $sms, $prvlg;
		$editable = $prvlg->_chk('cal_event_edit');
		$user_id = $_SESSION['user_id'];
		$cons = array();
		$cons = cEvents::GetEventUserCon($_SESSION['group'], $_SESSION['user_id']);
		/*$cons[] = "con='etabs' AND con_id=0"; // all school
		$cons[] = "con='".$_SESSION['group']."' AND con_id=".$user_id; // this user
		$cons[] = "con='".$_SESSION['group']."' AND con_id=0"; // this user group
		if($_SESSION['group'] == 'parent'){
			$cons[] = "con='student' AND con_id=".$_SESSION['std_id'];
		}
		if(in_array($_SESSION['group'], array('parent', 'student'))){
			$std_id = $_SESSION['std_id'];
			$parents = getParentsArr('student', $std_id);
			$where = array();
			if($parents != false && count($parents) > 0){
				foreach($parents as $array){
					$con =$array[0];
					$con_id= $array[1];
					$cons[] = "(con='$con' AND con_id=$con_id)";
				}
			}
		} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
			$obj = getAnyobjById($_SESSION['group'], $_SESSION['user_id']);
			$classes = $obj->getClassList();
			if($classes != false && count($classes) > 0){
				foreach($classes as $class){
				$parents = getParentsArr('class', $class->id);
					$where = array();
					if($parents != false && count($parents) > 0){
						foreach($parents as $array){
							$con =$array[0];
							$con_id= $array[1];
							$cons[] = "(con='$con' AND con_id=$con_id)";
						}
					}
				}
			}				
		}*/

		if( in_array(date('w', $date), explode(',', $sms->getSettings('weekend')))){
			$day_div = write_html('div', 'class="ui-corner-all ui-state-highlight" align="center"',
				write_html('h2', '', $lang['weekend'])
			);
		} elseif($date> getYearSetting('end_date')){
			$day_div = write_html('div', 'class="ui-corner-all ui-state-highlight" align="center"',
				write_html('h2', '', $lang['holiday'])
			);
		} else {
			$holidays_trs = array();
			$sql = "SELECT * FROM holidays WHERE dates=$date AND (".implode(' OR ', $cons).")";
			$holidays = do_query_array($sql, $sms->db_year);
			if($holidays != false && count($holidays) > 0){
				foreach($holidays as $holiday){
					$holidays_trs[] = write_html('tr', '',
						($editable ? 
							write_html('td', 'class="bg_none unprintable" width="24"',
								write_html('button', 'action="deleteHoliday" holidayid="'.$holiday->id.'" class="ui-state-default ui-corner-all circle_button hand hoverable"',
									write_icon('close')
								)
							)
						:'').
						write_html('td', '', $holiday->comments != '' ? $holiday->comments : $lang['holiday']).
						write_html('td', '', $sms->getAnyNameById($holiday->con, $holiday->con_id))
					);
				}
			}
			$day_div = write_html('fieldset', 'class="ui-corner-all ui-widget-content" style="padding:5px"',
				write_html('legend', '',$lang['holiday']).
				write_html('table', 'class="result"',
					write_html('tbody', '', implode('', $holidays_trs))
				)
			);
		}	
		
		// Events
		$events_trs = array();
		$day_events  = cEvents::getEventsByDay($date, $_SESSION['group'], $_SESSION['user_id']);
		if($day_events != false && count($day_events) > 0){
			foreach($day_events as $event){
				$cons  = $event->getCons();
				foreach($cons as $con){
					$events_trs[] = write_html('tr', '',
						($editable ?
							write_html('td', 'class="bg_none unprintable" width="24"',
								write_html('button', 'action="deleteEvent" eventid="'.$event->id.'" class="ui-state-default ui-corner-all circle_button hand hoverable"',
									write_icon('close')
								)
							).
							write_html('td', 'class="bg_none unprintable" width="24"',
								write_html('button', 'action="editEvent" eventid="'.$event->id.'" class="ui-state-default ui-corner-all circle_button hand hoverable"',
									write_icon('pencil')
								)
							)
						:'').
						write_html('td', '', $event->getName()).
						write_html('td', '', $event->comments).
						write_html('td', '', $sms->getAnyNameById($con->con, $con->con_id))
					);
				}
			}
		}
					
		$events_div = write_html('fieldset', 'class="ui-corner-all ui-widget-content" style="padding:5px"',
			write_html('legend', '',$lang['events']).
			write_html('table', 'class="result"',
				write_html('tbody', '', implode('', $events_trs))
			)
		);
		
		
		return ($editable ?
			write_html('div', 'class="toolbox"',
				write_html('a', 'action="addEvents" date="'.$date.'"', $lang['add'].write_icon('plus'))
			)
		: '').
		'<input type="hidden" name="day" value="'.unixToDate($date).'" />'.
		$day_div.
		$events_div;
	}
	
	
	static function chkHoliday($date, $con, $con_id){
		global $sms;
		$parents = getParentsArr($con, $con_id);
		$where = array();
		if($parents != false && count($parents) > 0){
			foreach($parents as $array){
				$con =$array[0];
				$con_id= $array[1];
				$where[] = "(con='$con' AND con_id=$con_id)";
			}
		}
		$holidays = do_query_array( 
			"SELECT * FROM holidays 
			WHERE dates = $date
			AND(
				(con IS NULL AND con_id IS NULL)
				OR (con='$con' AND con_id=$con_id)
				OR (con='$con' AND con_id=0)
				OR (con='etab' AND con_id=0)".
				(count($where)>0 ? " OR ".implode(' OR ', $where) : '').
			')'
		, $sms->db_year);
		
		if($holidays != false && count($holidays) > 0){
			return true;
		} else {
			return false;
		}
	}
	
	static function getDateInterval($type='', $value=''){
		global $lang;
		if($type == 'term'){
			$term_id =  $value;
			//$selected = 't='.$term_id;
			if($term_id != 0){
				$term = do_query_obj("SELECT title, begin_date, end_date FROM terms WHERE id=$term_id", DB_year);
				$begin_date = $term->begin_date;
				$end_date = $term->end_date;
				$title = $lang['term']. ': '.$term->title;
			} else {
				$title =  $lang['months_0'];
				$begin_date = getYearSetting('begin_date');
				$end_date = getYearSetting('end_date');
			}
		} elseif($type == 'month'){
			$month = $value;
			$selected = "m=$month";
			$cur_year = $_SESSION['year'];
			if(time() < mktime(0,0,0, $month, 1, $cur_year)){
				$s_year = $_SESSION['year'] - 1;
			} else{
				$s_year = $_SESSION['year'];
			}
			$title =  $lang['month'].': '.$lang["months_$month"];
			$begin_date = mktime(0,0,0, $month, 1, $s_year);
			$end_date = mktime(0,0,0, $month+1 , 1, $s_year);
		} else {		
			$begin_date = getYearSetting('begin_date');
			$end_date = getYearSetting('end_date');
		}
		
		return array('begin'=>$begin_date, 'end'=>$end_date);
	}
	
	static function getPeriods($con='', $con_id=''){
		global $lang;
		$arr = array("0"=>$lang['all']);
		if($con_id != ''){
			$terms_arr = Terms::getTermsSelect($con, $con_id);
			foreach($terms_arr as $id=>$name){
				$arr["t=$id"] = $name;
			}
		}
		$f = array_merge($arr, getPassedMonths());
		$opts = array();
		foreach( $f as $value => $name){
			$opts[] = write_html('option', 'value="'.$value.'"', $name);
		}
		
		return implode('', $opts);
	}

}

