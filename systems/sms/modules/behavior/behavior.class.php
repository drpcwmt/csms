<?php
/** Behaviors
*
*/

class Behavior{
	public $con='', $con_id='';
	
	public function __construct($con, $con_id){
		global $sms;
		$this->con = $con;
		$this->con_id = $con_id;
		$this->obj = $sms->getAnyObjById($con, $con_id);
	}
	
	public function getBehavior($date_begin, $date_end, $pattern_id='', $sanction='all'){
		$date_begin = ($date_begin!='' && $date_begin!= false) ? $date_begin : getYearSetting('begin_date');
		$date_end = ($date_end!='' && $date_end!= false) ? $date_end : getYearSetting('end_date');
		$stds = array();
		$object = $this->obj;
		$students = $object->getStudents();
		$stds = array_keys($students);		
		if($stds != false && count($stds) > 0){
			$sql ="SELECT * FROM behavior WHERE (std_id=".implode(' OR std_id=', $stds).") ".
			($date_begin == $date_end ? " AND date=$date_begin" : "AND date>=$date_begin AND date <=$date_end");
			
			if($pattern_id != ''){
				$sql .= " AND pattern=$pattern_id";
			}
			if($sanction != 'all'){
				if($sanction != false){
					$sql .= " AND sanction='$sanction'";	
				}
			}
			$sql .= " ORDER BY std_id, date ASC";
			$behaviors = do_query_array($sql, DB_year);
			if(count($behaviors) > 0){
				return $behaviors;
			} else {
				return array();
			}
		}
	}
	
		
	
	
	public function loadStdLayout($type='', $value=''){
		global $lang;
		$student = new Students($this->con_id);
		$layout = new Layout();
		$layout->name = $student->getName();
		$layout->std_id= $student->id;
		$layout->template = 'modules/behavior/templates/behavior_students.tpl';
		$interval = Calendars::getDateInterval($type, $value);
		$behaviors = $this->getBehavior($interval['begin'], $interval['end']);
		$layout->std_total_behavior = count($behaviors);
		$sql = "SELECT id FROM behavior WHERE std_id=$student->id AND date<=".$interval['begin']." AND date>=".$interval['end'];

		$calendar = new Calendars('student', $student->id);
		$TotalWorkingDay = round($calendar->getTotalWorkingDay($interval['begin'], $interval['end']));							

		$checked = write_icon('checked');
		$i = 1;
		$trs = array();
		foreach($behaviors as $behavior){
			$user = new Employer($behavior->user_id);
			$student = new Students($behavior->std_id);
			$class = $student->getClass();
			$trs[] = write_html('tr', '',
				write_html('td', 'align="center"', $i).
				write_html('td', '', $behavior->pattern).
				write_html('td', '', unixToDate($behavior->date)).
				write_html('td', 'align="center"', $behavior->lesson_no).
				write_html('td', '', $behavior->sanction).
				write_html('td', '', $user->getName()).
				write_html('td', '', $behavior->comments).
				write_html('td', '',
					($behavior->msg == 1 ? write_icon('mail-closed') : '&nbsp;')
				)
			);	
			$i++;
		}
		$layout->list = write_html('table', 'class="tablesorter"',
			write_html('thead', '',
				write_html('tr', '', 
					write_html('th', 'width="30"', $lang['ser']).
					write_html('th', 'width="30"', $lang['behavior']).
					write_html('th', 'width="120"', $lang['date']).
					write_html('th', 'width="60"', $lang['lesson_no']).
					write_html('th', '', $lang['sanction']).
					write_html('th', '', $lang['user']).
					write_html('th', '', $lang['notes'])
				)
			).
			write_html('tbody', '',
				implode('', $trs)
			)
		);
		return $layout->_print();
	}
	
	static function loadMainLayout(){
		global $sms;
		$layout = new Layout();
		$layout->template = 'modules/behavior/templates/main_layout.tpl';
		$layout->date = unixToDate(time());
		$behav  = new Behavior('school', '');
		$behaviors = $behav->getBehavior(mktime(0,0,0,date('m'), date('d'), date('Y')), mktime(0,0,0,date('m'), (date('d')+1), date('Y')));
		$trs = array();
		foreach($behaviors as $behavior){
			$user = new Employers($behavior->user_id);
			$student = new Students($behavior->std_id);
			$class = $student->getClass();
			$trs[] = write_html('tr', '',
				write_html('td', 'align="center"', $i).
				write_html('td', '', $student->getName()).
				write_html('td', '', $classs->getName()).
				write_html('td', '', $behavior->pattern).
				write_html('td', '', unixToDate($behavior->date)).
				write_html('td', 'align="center"', $behavior->lesson_no).
				write_html('td', '', $behavior->sanction).
				write_html('td', '', $user->getName()).
				write_html('td', '', $behavior->comments).
				write_html('td', '',
					($behavior->msg == 1 ? write_icon('mail-closed') : '&nbsp;')
				)
			);	
			$i++;
		}
		$layout->daily_list = implode('', $trs);
		return $layout->_print();
	}
	
	static function getPatterns(){
		global $sms;
		$patterns = do_query_array( "SELECT * FROM behaviors", $sms->database);
		return $pattern;
	}

	static function getSanctions(){
		global $sms;
		$out = array();
		$patterns = do_query_array( "SELECT DISTINCT sanction FROM behavior", $sms->db_year);
		foreach($patterns as $p){
			$out[] = $p->sanction;
		}
		return $out;
	}
	
	static function loadAddForm(){
		$form = new Layout();
		$form->date = unixToDate(time());
		$form->template = 'modules/behavior/templates/add_form.tpl';
		return $form->_print();
	}
		
}
?>