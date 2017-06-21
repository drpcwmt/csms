<?php
/** School Reports
*
*/

class SchoolReport{
	private $thisTemplatePath = 'modules/reports/templates';
	
	static function loadSchoolBalance(){
		global $sms;
		return $sms->loadSchoolBalance();
	}
	
	static function loadSchoolStatics(){
		global $sms;
		return $sms->loadSchoolStatics();
	}

	static function getQuitReasons(){
		$prev_year = getYear($_SESSION['year']-1);
		$year_begin = ($prev_year!= false ? $prev_year->end_date : 0);
		//$next_year = do_query_obj("SELECT * FROM years WHERE year=".($_SESSION['year']+1), MySql_Database); 
		$year_end = getYearSetting('end_date');		
		$suspended = do_query_array("SELECT DISTINCT suspension_reason FROM student_data WHERE `status`=0 AND quit_date>=$year_begin AND quit_date<$year_end", MySql_Database);		
		return $suspended!= false ? $suspended : array();
	}

	static function loadQuitList($reason='', $level_id=''){
		global $lang, $sms;
		if($level_id != '' ){
			$level = new Levels($level_id);
		}
		$reason = urldecode($reason);
		$status = 0;
		$prev_year = getYear($_SESSION['year']-1);
		$year_begin = $prev_year->end_date;
		//$next_year = do_query_obj("SELECT * FROM years WHERE year=".($_SESSION['year']+1), MySql_Database); 
		$year_end = getYearSetting('end_date');		
		$sql = "SELECT *
			FROM student_data
			WHERE `status`=$status
			AND quit_date>=$year_begin 
			AND quit_date<$year_end";
		if($reason != '' && $reason != 'all' ){
			$sql .= " AND suspension_reason ='$reason'";
		}

		$stds = do_query_array($sql, $sms->database, $sms->ip);
		$students = array();
		foreach($stds as $std){
			if($level_id !=''){
				$s = new Students($std->id);
				$std_level = $s->getLevel();
				if($std_level->id == $level_id){
					$students[] = $s;
				}
			} else {				
				$students[] = new Students($std->id);				
			}
		} 
		$trs = array();
		$ser = 1;
		foreach($students as $student){
			$class = $student->getClass();
			$class_name = ($class != false ? $class->getName():'');
			$trs[] = write_html('tr', '',
				write_html('td', 'width="20" align="center"', $ser).
				write_html('td', 'width="20" class="unprintable"', 
					write_html('button', 'module="students" std_id="'.$student->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
				).
				write_html('td', '', $student->getName()).
				write_html('td', '',  $class_name).
				write_html('td', '',  unixToDate($student->quit_date)).
				($reason == '' || $reason == 'all' ?
					write_html('td', '', $student->suspension_reason)
				: '')
			);	
			$ser++;
		}		
		$select_level_arr = array();
		if(in_array($_SESSION['group'], array('superadmin', 'admin'))){ 
			$select_level_arr['all'] = $lang['all'];
		}
		$grades = Levels::getList();
		foreach($grades as $grade){
			$select_level_arr[$grade->id] = $grade->getName();
		}
		
		$out = write_html('form', 'class="ui-corner-all ui-state-highlight unprintable optional" style="padding:5px"',
			'<input type="hidden" name="reason" value="'.$reason.'" />'.
			write_html('table', 'cellspacing="0" class="optional"', 
				write_html('tr', '', 
					write_html('td', 'width="120"', write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align" ', $lang['level'])).
					write_html('td', '', 				
						write_html_select('name="level_id" class="combobox" update="changeQuitedLevel"', $select_level_arr, $level_id)
					)
				)
			)
		).
		($reason != '' && $reason != 'all' ?
			write_html('h2', '', $reason )
		:'').
		($level_id != ''?
			write_html('h2', '', $level->getName() )
		:'').
		write_html('div', 'id="suspension_table"',
			write_html('table', 'class="tablesorter"', implode('', $trs))
		);
		return $out;
	}
	
	
	/*static function getSuspensionReasons(){
		$year_end = getYearSetting('begin_date');
		$prev_year = do_query_obj("SELECT * FROM years WHERE year=".($_SESSION['year']-1), MySql_Database); 
		$year_begin = $prev_year->begin_date;
		$suspended = do_query_array("SELECT DISTINCT suspension_reason FROM student_data WHERE `status`=3 AND quit_date>$year_begin AND quit_date<$year_end", MySql_Database);		
		return $suspended!= false ? $suspended : array();
	}*/

	/*static function loadSuspentionList($reason='', $level_id=''){	
		global $lang, $MS_settings;
		if($level_id != '' ){
			$level = new Levels($level_id);
		}
		$status = 3;
		$year_begin = getYearSetting('begin_date');
		$next_year = do_query_obj("SELECT * FROM years WHERE year=".($_SESSION['year']+1), MySql_Database); 
		$year_end = $next_year != false ? $next_year->begin_date : getYearSetting('end_date');		
		$sql = "SELECT *
			FROM student_data
			WHERE `status`=$status
			AND quit_date>=$year_begin 
			AND quit_date<=$year_end";
		if($reason != '' && $reason != 'all' ){
			$sql .= " AND suspension_reason ='$reason'";
		}

		$stds = do_query_array($sql, MySql_Database);
		$students = array();
		foreach($stds as $std){
			if($level_id !=''){
				$s = new Students($std->id);
				$std_level = $s->getLevel();
				if($std_level->id == $level_id){
					$students[] = $s;
				}
			} else {				
				$students[] = new Students($std->id);				
			}
		} 
		$trs = array();
		$ser=0;
		foreach($students as $student){
			$class = $student->getClass();
			$class_name = ($class != false ? $class->getName():'');
			$ser++;
			$trs[] = write_html('tr', '',
				write_html('td', 'width="20"', $ser).
				write_html('td', 'width="20" class="unprintable"', 
					write_html('button', 'module="students" std_id="'.$student->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
				).
				write_html('td', '', $student->getName()).
				write_html('td', '',  $class_name).
				write_html('td', '',  unixToDate($student->quit_date)).
				write_html('td', '',  unixToDate($student->quit_date)).
				write_html('td', '',  unixToDate($student->suspension_till_date)).
				($reason == '' || $reason == 'all' ?
					write_html('td', '', $student->suspension_reason)
				: '')
			);	
		}		
		$select_level_arr = array();
		if(in_array($_SESSION['group'], array('superadmin', 'admin'))){ 
			$select_level_arr['all'] = $lang['all'];
		}
		$grades = Levels::getList();
		foreach($grades as $grade){
			$select_level_arr[$grade->id] = $grade->getName();
		}
		
		$out = write_html('form', 'class="ui-corner-all ui-state-highlight unprintable" style="padding:5px"',
			'<input type="hidden" name="reason" value="'.$reason.'" />'.
			write_html('table', 'cellspacing="0"', 
				write_html('tr', '', 
					write_html('td', 'width="120"', write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align" ', $lang['level'])).
					write_html('td', '', 				
						write_html_select('name="level_id" class="combobox" update="changeSuspensionLevel"', $select_level_arr, $level_id)
					)
				)
			)
		).
		($reason != '' && $reason != 'all' ?
			write_html('h2', '', $reason )
		:'').
		($level_id != ''?
			write_html('h2', '', $level->getName() )
		:'').
		write_html('div', 'id="suspension_table"',
			write_html('table', 'class="result"', implode('', $trs))
		);
		return $out;
	
	}*/

	static function loadMinistreyBalance(){
		global $lang, $MS_settings;
		$etabs = Etabs::getList();
		$tf_girl_m = 0;
		$tf_girl_c = 0;
		$tf_boy_m = 0;
		$tf_boy_c = 0;
		$tf_classes = 0;
		$tf_students = 0;
		$layout = new Layout();
		$levels = Levels::getList();
		$layout->rows = '';
		$first =true;
		foreach($levels as $level){
			if($first!=true){
				$total_girl_m = 0;
				$total_girl_c = 0;
				$total_boy_m = 0;
				$total_boy_c = 0;
				$student_list = new StudentsList('level', $level->id);
				$student_list->stats = array('1');
				$students= $student_list->getStudents();
				$count_std= $student_list->getCount();
				$count_classes = count($level->getClassList());
				foreach($students as $student){
					if($student->sex == 1 && $student->religion==1){
						$total_boy_m++;
					} elseif($student->sex == 1 && $student->religion == 2){
						$total_boy_c++;
					} elseif($student->sex == 2 && $student->religion == 1){
						$total_girl_m++;
					} elseif($student->sex == 2 && $student->religion == 2){
						$total_girl_c++;
					}
				}
				$tf_girl_m += $total_girl_m;
				$tf_girl_c += $total_girl_c;
				$tf_boy_m += $total_boy_m;
				$tf_boy_c += $total_boy_c;
				$tf_classes += $count_classes;
				$tf_students += count($students);
				$layout->rows .= write_html('tr', '',
					write_html('td', '', $level->getName()).
					write_html('td', 'align="center"', $count_classes).
					write_html('td', 'align="center"', $total_boy_m).
					write_html('td', 'align="center"', $total_boy_c).
					write_html('td', 'align="center"', $total_boy_m + $total_boy_c).
					write_html('td', 'align="center"', $total_girl_m).
					write_html('td', 'align="center"', $total_girl_c).
					write_html('td', 'align="center"', $total_girl_m + $total_girl_c).
					write_html('td', 'align="center"', $count_std)
				);
			}
			$first = false;
		}
		$layout->school_name = $MS_settings['school_name'];
		$layout->date = unixToDate(time());
		$layout->year = $_SESSION['year'].' / '. ( $_SESSION['year'] +1 );
		$layout->total = $tf_students;
		$layout->total_girl_m = $tf_girl_m;
		$layout->total_girl_c = $tf_girl_c;
		$layout->total_girl = $tf_girl_m + $tf_girl_c;
		$layout->total_boy_m = $tf_boy_m;
		$layout->total_boy_c = $tf_boy_c;
		$layout->total_boy = $tf_boy_m + $tf_boy_c;
		$layout->total_class = $tf_classes;
		$layout->template = 'modules/reports/templates/ministry_balance.tpl';
		return $layout->_print();
	}
	
	static function loadRedoublingReport($level_id='', $class_select=false){
		global $lang, $sms;
		$results = do_query_array("SELECT * FROM final_result WHERE result='0'", $sms->db_year);
		$trs= array();
		$next_year = $_SESSION['year']+1;
		$classes = array();
		if($results != false){
			$next_year_db = Db_prefix.$next_year;
			$query = do_query_obj("SHOW DATABASES LIKE '$next_year_db'");
			
			foreach($results as $row){
				$student = new Students($row->std_id);
				$class= $student->getClass();
				$level = $class->getLevel();
				if( $query != false){
					$class_q = do_query_array("SELECT * FROM classes WHERE level_id=$level->id", $next_year_db);
					//print_r($class_q);
					$classes = array();
					foreach($class_q as $cl){
						$classes[$cl->id] = $cl->{'name_'.$_SESSION['dirc']};
					}
				}
				$trs[] = write_html('tr', '',
					'<input type="hidden" name="std_id[]" value="'.$row->std_id.'" />'.
					write_html('td', 'class="unprintable"',
						write_html('button', 'type="button" class="ui-state-default circle_button hoverable" module="students" action="delStdRsult" std_id="'.$row->std_id.'"', write_icon('trash'))
					).
					write_html('td', '', $student->getName()).
					write_html('td', '', $class->getName()).
					($query != false ? 
						write_html('td', '',
								write_html_select('name="class_id[]" class="combobox"', $classes, '')
							)
					: '')
				);
			}
		}
		$select_level_arr = array('all' => $lang['all']);
		$grades = Levels::getList();
		foreach($grades as $grade){
			$select_level_arr[$grade->id] = $grade->getName();
		}
		
		if($level_id != ''){
			$level = new Levels($level_id);
		}
		$out = write_html('form', 'class="ui-corner-all ui-state-highlight unprintable optional" style="padding:5px"',
			(count($classes) == 0 ?
				write_error(write_html('h2', '',$lang['next_year_required']))
			: '').
			write_html('table', 'cellspacing="0" class="optional"', 
				write_html('tr', '', 
					write_html('td', 'width="120"', write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align" ', $lang['level'])).
					write_html('td', '', 				
						write_html_select('name="level_id" class="combobox" update="changeRedoubleLevel"', $select_level_arr, $level_id)
					)
				)
			)
		).
		write_html('h2', 'class="optional"', $lang['redoubling_report']).
		($level_id != ''?
			write_html('h2', '', $level->getName() )
		:'').
		write_html('h4', '', count($trs).' '. $lang['students']).
		write_html('div', 'id="suspension_table"',
			write_html('table', 'class="tablesorter"', 
				write_html('thead', '', 
					write_html('tr', '',
						write_html('th', 'class="unprintable {sorter:false}" width="20"', '&nbsp;').
						write_html('th', '', $lang['name']).
						write_html('th', '', $_SESSION['year'].'/'.$next_year).
						($query != false ? 
							write_html('th', '', $next_year.'/'.($next_year+1))
						: '')
					)
				).
				write_html('tbody', '', 
					implode('', $trs)
				)
			)
		);
		return $out;

	}
	
	static function loadWaitingReport($level_id=''){
		global $lang;
		$levels = $level_id!='' ? array(new Levels($level_id)) : Levels::getList();
		$fieldsets = '';
		$total = 0;
		$select_level_arr = array('all' => $lang['all']);
		
		foreach($levels as $level){
			$select_level_arr[$level->id] = $level->getName();
			$rows = do_query_array("SELECT * FROM waiting_list WHERE level_id=$level->id", MySql_Database);
			if(count($rows)>0){
				$total += count($rows);
				$classes = array(''=>' ');
				$cls = $level->getClassList();
				foreach($cls as $cl){
					$classes[$cl->id] = $cl->getName();
				}
				$fieldset = new Layout();
				$fieldset->level_name = $level->getName();
				$fieldset->tot_registred = count($level->getStudents());
				$fieldset->tot_waiting = count($rows);
				$fieldset->trs = '';
				$fieldset->template = "modules/reports/templates/waiting_list_fieldset.tpl";
				
				foreach($rows as $row){
					$student = new Students($row->std_id);				
					$fieldset->trs .= write_html('tr', '',
						'<input type="hidden" name="std_id[]" value="'.$student->id.'" />'.
						write_html('td', 'class="unprintable"',
							write_html('button', 'module="students" std_id="'.$student->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
						).
						write_html('td', '', $student->getName()).
						write_html('td', '', unixToDate($student->join_date)).
						write_html('td', '',
							write_html_select('name="class_id[]" class="combobox"', $classes, '')
						)
					);
				}
				$fieldsets.= $fieldset->_print();
			}
		}
		if($total == 0){
			$fieldsets = write_error($lang['no_students_found']);
		}
		
		$out = write_html('form', 'class="ui-corner-all ui-state-highlight unprintable optional" style="padding:5px"',
			write_html('table', 'cellspacing="0" class="optional"', 
				write_html('tr', '', 
					write_html('td', 'width="120"', write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align" ', $lang['level'])).
					write_html('td', '', 				
						write_html_select('name="level_id" class="combobox" update="changeWaitingLevel"', $select_level_arr, $level_id)
					)
				)
			)
		).
		write_html('h2', 'class="optional"', $lang['waiting_list']).
		($level_id != ''?
			write_html('h2', '', $level->getName() )
		:'').
		$fieldsets;
		return $out;
	}
}
