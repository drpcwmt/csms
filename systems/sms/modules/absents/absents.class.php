<?php
/** Absents
*
*/

class absents{
	public $con='', $con_id='';
	
	public function __construct($con, $con_id){
		global $MS_settings;
		$this->con = $con;
		$this->con_id = $con_id;
		$this->first_day_week =  $MS_settings['first_day'];
		$this->day_time_begin =  $MS_settings['day_time_begin'];
		$this->day_time_end =  $MS_settings['day_time_end'];
		$weekend_str = $MS_settings['weekend'];
		$this->week_ends = strpos($weekend_str, ',') !== false ? explode(',', $weekend_str) : array($weekend_str);
	}
	
	public function getAbsents($date_begin, $date_end){
		$date_begin = ($date_begin!='' && $date_begin!= false) ? $date_begin : getYearSetting('begin_date');
		$date_end = ($date_end!='' && $date_end!= false) ? $date_end : getYearSetting('end_date');
		$stds = array();
		if($this->con=='student'){
			$stds[] = $this->con_id;
		} else {
			$stds = getStdIds($this->con, $this->con_id);
		}
		
		if($stds != false && count($stds) > 0){
			$sql ="SELECT * FROM absents WHERE (con_id=".implode(' OR con_id=', $stds).") ".
			($date_begin == $date_end ? " AND day=$date_begin" : "AND day>=$date_begin AND day <=$date_end").
			" ORDER BY con_id, day ASC";
			$trs = array();
			$absents = do_query_array($sql, DB_year);
			if(count($absents) > 0){
				return $absents;
			} else {
				return array();
			}
		}
	}
	
	public function loadRate(){
		global $lang;
		$form = new Layout($this);
		$all_classes = classes::getList();
		$classes=array('0'=>$lang['all']);
		foreach($all_classes as $class){
			$classes[$class->id] = $class->getName();
		}
		//$cur_class_id = $all_classes[0]->id;
		$form->con_id_opts  = write_select_options( $classes, '', false);
		$form->periods_opts = $this->getPeriods();
		$form->template = 'modules/absents/templates/absents_rate.tpl';
		$form->rate_table = $this->loadRateTable();
		$form->rate_chart = $this->getRateChart();
		return $form->_print();
			
	}
	
	public function loadRateTable($type='', $value=''){
		global $lang, $sms;
		$interval = absents::getDateInterval($type, $value);
		if($this->con == 'class'){
			if($this->con_id == '0'){ // All
				$classes = Classes::getList();
				if($type == ''){ // all year
					$begin_date = getYearSetting('begin_date');
					$end_date = getYearSetting('end_date');
					$ths = array();
					$trs = array();
					$months = array();
					for($i =0; $i<10; $i++){
						$m = date('m', $begin_date)+$i;
						$b = mktime(0,0,0,$m, 1, $_SESSION['year']);
						$months[] = $b;
						//$e = mktime(0,0,0,$m+1 , 1, $_SESSION['year']);
						$month = date('m', $b);
						$ths[] = write_html('th', '', $lang["months_$month"]);
					}
					foreach($classes as $class){
						$calendar = new Calendars('class', $class->id);
						$class_absent = new absents('class', $class->id);
						$tds = array();
						foreach($months as $m){
							$month = date('m', $m);
							$interval = absents::getDateInterval('month', $month);	
							$TotalWorkingDay = round($calendar->getTotalWorkingDay($interval['begin'], $interval['end']));							
							$total_class = count($class_absent->getAbsents($interval['begin'], $interval['end']));
							$divider = ($TotalWorkingDay > 0 ? $TotalWorkingDay:1) * count($class->getStudents());
							$tds[] = write_html('td', '',
								(time()>$m ?
									round(100 - ($total_class * 100 / $divider),2).' %'
								:'')
							);	
						}
						$trs[] = write_html('tr', '',
							write_html('td', '', $class->getName()).
							implode('', $tds)
						);	
					}
					
					return write_html('table', 'class="tablesorter"',
						write_html('thead', '', 
							write_html('tr', '',
								write_html('th', '', '&nbsp;').
								implode('', $ths)
							)
						).
						write_html('tbody', '', 
							implode('', $trs)
						)
					);
				} else { // by interval
					$calendar = new Calendars('class', $this->con_id);
					$TotalWorkingDay = round($calendar->getTotalWorkingDay($interval['begin'], $interval['end']));
					
					foreach($classes as $class){
						$divider = ($TotalWorkingDay > 0 ? $TotalWorkingDay:1) * count($class->getStudents());
						$class_absent = new absents('class', $class->id);
						$total_class = count($class_absent->getAbsents($interval['begin'], $interval['end']));
						$trs[]= write_html('tr', '',
							write_html('td', 'class="unprintable"  align="center"','&nbsp;').
							write_html('td', '', $class->getName()).
							write_html('td', '', $total_class).
							write_html('td', '', round(($total_class * 100 / $divider ),2).' % ').
							write_html('td', '', round(100 - ($total_class * 100 / $divider),2).' % ')
						);
					}
					$class_table = new Layout();
					$class_table->rows = implode('', $trs);
					$class_table->template = 'modules/absents/templates/class_rate_table.tpl';				
					return $class_table->_print();
				}
			} else {
				$class = new Classes($this->con_id, '', $sms);
				$calendar = new Calendars('class', $this->con_id);
				if($type != ''){ // specfied period for specified class
					$TotalWorkingDay = round($calendar->getTotalWorkingDay($interval['begin'], $interval['end']));
					$class_total = 0; 
					$students = $class->getStudents();
					$trs = array();
					foreach($students as $student){
						$std_absent = new absents('std', $student->id);
						$std_id = $student->id;
						$total_std = count($std_absent->getAbsents($interval['begin'], $interval['end']));
						$trs[]= write_html('tr', '',
							write_html('td', 'class="unprintable"  align="center"',
								write_html('button', 'module="students" std_id="'.$student->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
							).
							write_html('td', '', $student->getName()).
							write_html('td', '', $total_std).
							write_html('td', '', round(($total_std * 100 / ($TotalWorkingDay > 0 ? $TotalWorkingDay:1)),2).' % ').
							write_html('td', '', round(100 - ($total_std * 100 / ($TotalWorkingDay >0?$TotalWorkingDay:1)),2).' % ')
						);
						$class_total = $class_total + $total_std;
					}
				} else { // all year for specified class
					$begin_date = getYearSetting('begin_date');
					$end_date = getYearSetting('end_date');
					$ths = array();
					$trs = array();
					$months = array();
					$class_absent = new absents('class', $class->id);
					for($i =0; $i<10; $i++){
						$m = date('m', $begin_date)+$i;
						$b = mktime(0,0,0,$m, 1, $_SESSION['year']);
						$e = mktime(0,0,0,$m+1 , 1, $_SESSION['year']);
						$month = date('m', $b);
						$TotalWorkingDay = round($calendar->getTotalWorkingDay($b, $e));							
						$divider = ($TotalWorkingDay > 0 ? $TotalWorkingDay:1) * count($class->getStudents());
						$total_month = count($class_absent->getAbsents($b, $e));
						$trs[]= write_html('tr', '',
							write_html('td', 'class="unprintable"  align="center"',$month).
							write_html('td', '', $lang["months_$month"]).
							write_html('td', '', $total_month).
							write_html('td', '', round(($total_month * 100 / $divider ),2).' % ').
							write_html('td', '', round(100 - ($total_month * 100 / $divider ),2).' % ')
						);
					}
				}
				$class_table = new Layout();
				$class_table->rows = implode('', $trs);
				$class_table->template = 'modules/absents/templates/class_rate_table.tpl';				
				return $class_table->_print();
			}
			
		} elseif($this->con == 'student'){ 
		
		}
	}
	
	public function getRateChart($type='', $value=''){
		global $lang, $sms;
		$interval = absents::getDateInterval($type, $value);
		$calendar = new Calendars($this->con, $this->con_id);
		$TotalWorkingDay = round($calendar->getTotalWorkingDay($interval['begin'], $interval['end']));
		if($type == 'term' && $value!=0){
			$term = new Terms($value);
			$title = $term->title;	
		} elseif($type == 'month' && $value!=0){
			$title = $lang["months_$value"];
		} else {
			$title = $lang['year'].': '.$_SESSION['year'].' / '. ($_SESSION['year']+1);
		}
		return write_html('h3', 'class="title"', $sms->getAnyNameById($this->con, $this->con_id)).
			write_html('h3', 'class="title"', $title).
			write_html('h4', '', $lang['total_workin_day'].': '.$TotalWorkingDay).
			write_html('div', 'id="chartDiv_absrate"', 
				'<a action="enlargeChart"><img class="hand" src="index.php?module=absents&chart&con='.$this->con.'&con_id='.$this->con_id.'&'.time().'.png"  width="320" height="120" /></a>'
		);
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
	
	public function getPeriods(){
		global $lang;
		$arr = array("0"=>$lang['all']);
		if($this->con_id != ''){
			$terms_arr = Terms::getTermsSelect($this->con, $this->con_id);
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
	
	public function loadStdLayout($type='', $value='', $form=true){
		global $lang;
		$student = new Students($this->con_id);
		$layout = new Layout();
		$layout->name = $student->getName();
		$layout->std_id= $student->id;
		$layout->template = 'modules/absents/templates/absents_students.tpl';
		$layout->chart = $this->getRateChart($type, $value);
		$layout->periods_opts = $this->getPeriods();
		$layout->absent_std_form = fillTemplate("modules/absents/templates/absents_students_form.tpl", $layout);
		$interval = Absents::getDateInterval($type, $value);
		$absents = $this->getAbsents($interval['begin'], $interval['end']);
		$layout->std_total_abs = count($absents);
		$sql = "SELECT id FROM absents WHERE con_id=$student->id AND day<=".$interval['begin']." AND day>=".$interval['end'];
		$ills = do_query_array($sql." AND ill=1", DB_year);
		$justf = do_query_array($sql." AND justify=1", DB_year);
		$layout->ill_abs_days = $ills!=false ? count($ills) : 0;
		$layout->justify_abs_days = $justf!=false ? count($justf) : 0;
		$calendar = new Calendars('student', $student->id);
		$TotalWorkingDay = round($calendar->getTotalWorkingDay($interval['begin'], $interval['end']));							

		$layout->std_rate = round(100 - ((count($absents) / ($TotalWorkingDay > 0 ? $TotalWorkingDay:1)) * 100), 2) . ' %';
		$checked = write_icon('check');
		$i = 1;
		$trs = array();
		foreach($absents as $abs){
			$trs[] = write_html('tr', '',
				write_html('td', 'align="center"', $i).
				write_html('td', '', unixToDate($abs->day)).
				write_html('td', 'align="center"', ($abs->justify == 1 ? $checked : '')).
				write_html('td', 'align="center"', ($abs->ill == 1 ? $checked : '')).
				write_html('td', '', $abs->comments)
			);	
			$i++;
		}
		$layout->list = write_html('table', 'class="tablesorter"',
			write_html('thead', '',
				write_html('tr', '', 
					write_html('th', 'width="30"', $lang['ser']).
					write_html('th', 'width="120"', $lang['date']).
					write_html('th', 'width="60"', $lang['justification']).
					write_html('th', 'width="60"', $lang['ill']).
					write_html('th', '', $lang['notes'])
				)
			).
			write_html('tbody', '',
				implode('', $trs)
			)
		);
		return $layout->_print();
	}
	
	public function getStdAbsentTable(){
		
	}
}
?>

