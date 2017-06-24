<?php
/** Schedules
*
*/

class schedule{
	public $con = '',
	$con_id = '',
	$type = '',
	$default=true,
	$inherited=true,
	$hide_lessons = false,
	$update_auth = false,
	$editable = false,
	$MSEXT_lms = MSEXT_lms,
	$date_begin=false,
	$date_end = false,
	$spc_date_begin=false,
	$spc_date_end = false,
	$static_array = array('prof', 'hall', 'tools');

	
	public function __construct($con, $con_id){
		global $this_system;
		$this->con = $con;
		$this->con_id = $con_id;
		$this->first_day_week =  $this_system->getSettings('first_day');
		$this->day_time_begin =  $this_system->getSettings('day_time_begin');
		$this->day_time_end =  $this_system->getSettings('day_time_end');
		$weekend_str = $this_system->getSettings('weekend');
		$this->week_ends = strpos($weekend_str, ',') !== false ? explode(',', $weekend_str) : array($weekend_str);
		$year = getNowYear();
		$this->date_begin =  $year->begin_date;
		$this->date_end = $year->end_date;
	}
	
	// main function load schedule holder and current week
	public function loadSchedule(){
		global $thisTemplatePath, $lang;

		$schedule = new stdClass();
		$schedule->con = $this->con;
		$schedule->con_id = $this->con_id;
		$schedule->schedule_week_holder = $this->loadWeek(isset($this->week_no) ? $this->week_no : false);
		$schedule->weekTimeLine = $this->createWeekTimeLine();
		if($this->editable == false){
			$toolbox = array(
				array(
					"tag" => "a",
					"attr"=> 'action="toggleDisplayEvents"',
					"text"=> $lang['hide_events'],
					"icon"=> "calendar"
				),
				array(
					"tag" => "a",
					"attr"=> 'action="print_pre" rel=".schedule-week-holder:visible" plugin="print"',
					"text"=> $lang['print'],
					"icon"=> "print"
				)
			);
			if($this->update_auth){
				$toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="'.(in_array($this->con, $this->static_array) ? 'addBlackTime' :'openEditDialog').'"',
					"text"=> $lang['modify'],
					"icon"=> "pencil"
				);
			}
			$schedule->toolbox = createToolbox($toolbox);
		}
		return fillTemplate("modules/schedule/templates/schedules.tpl", $schedule);
	}
	
	public function loadWeek($week_no=false){
		global $thisTemplatePath, $MS_settings, $lang, $days_name_arr;
		$first_time_in_year = first_week_route($this->date_begin, $this->first_day_week);

		if($week_no === false){ // now
			$this->week_day_beg = first_week_route(time(), $this->first_day_week);
			$this->week_day_end = round(($this->week_day_beg - $first_time_in_year) / 604800)+1;
			$this->week_no = round(($this->week_day_beg - $first_time_in_year) / 604800)+1;
		} else {
			$this->week_no = $week_no;
			if($week_no > 0){
				$this->week_day_beg = mktime(0,0,0, date('m', $first_time_in_year), (date('d', $first_time_in_year) +(($week_no-1)*7)) , date('Y', $first_time_in_year));
				$this->week_day_end = mktime(0,0,0, date('m', $this->week_day_beg), (date('d', $this->week_day_beg) +7) , date('Y', $this->week_day_beg));
			} else {
				$this->week_day_beg = 0;
				$this->week_day_end = 6;
			}
		}

		// store tiiles		
		$schedule = new stdClass();
		$schedule->week_no = $this->week_no;
		for($i=0; $i<=6; $i++){ 
			// get cur date
			if($this->week_no != 0){
				$cur_date = mktime(0, 0, 0, 
					date('m', $this->week_day_beg), 
					(date('d',$this->week_day_beg) +$i ),
					date('Y', $this->week_day_beg)
				);
			} else {
				$cur_date = $i;
			}

			// set titles
			$schedule->{"day_".($i+1)."_title"} = $days_name_arr[$i+1]. ($this->week_no != 0 ? " ".date('d-m', $cur_date) :'');
			
			// set styles
				// outside dates
			if( $this->week_no !=0  && 
				$cur_date < $this->date_begin 
				|| $cur_date > $this->date_end
				|| ($this->spc_date_begin != false && $cur_date < $this->spc_date_begin)
				|| ($this->spc_date_end != false && $cur_date > $this->spc_date_end)
				){ 
				$schedule->{"day_".($i+1)."_class"} = 'maintenance';
				//Holidays & week ends
			} elseif( in_array($i, $this->week_ends)){
				$schedule->{"day_".($i+1)."_class"} = 'day_off';
				$schedule->{"day_".($i+1)."_list"} = $lang['weekend'];
			} elseif(($this->week_no !=0  && Calendars::chkHoliday($cur_date, $this->con, $this->con_id) == true)){
				$schedule->{"day_".($i+1)."_class"} = 'day_off';
				$schedule->{"day_".($i+1)."_list"} = $lang['holiday']. unixToDate($cur_date);
			} else {
				$schedule->{"day_".($i+1)."_class"} = 'day_on';
				$schedule->{"day_".($i+1)."_list"} = $this->loadDay($cur_date);
			}
			
			// set timeline
			$schedule->time_line = $this->loadTimeline();
			
			// set day content
		}
		return fillTemplate("modules/schedule/templates/schedules_week.tpl", $schedule);
	}
	
	public function loadDay($day){
		return $this->loadSessions($day);
	}
	
	public function getId($con, $con_id, $date, $lesson_no=false){
		$out = array();
		$sql = "SELECT schedules_date.*, schedules_times.* 
		FROM schedules_date, schedules_times 
		WHERE schedules_date.con='$con'
		AND schedules_date.con_id = $con_id
		AND schedules_date.date =$date
		AND schedules_date.id = schedules_times.rec_id".
		($lesson_no != false ? " AND schedules_times.lesson_no=$lesson_no " : '').
		" ORDER BY schedules_times.begin ASC";
		
		$qu = do_query_array( $sql, DB_year);
		
		if( count($qu) > 0) {
			foreach($qu as $session){
				$session->con = $this->con;
				$session->con_id = $this->con_id;
				$out[] = $session;
			}
			return $out;
		} else {
			return false;
		}
	}
	
	public function getParentRecId($date, $lesson_no=false){
		if(!isset($this->parents)){
			$this->parents = getParentsArr($this->con, $this->con_id);
		} 
		
		if($this->parents != false && count($this->parents) > 0){
			foreach($this->parents as $array){
				$d_con =$array[0];
				$d_con_id = $array[1];
				$id = $this->getId($d_con, $d_con_id, $date, $lesson_no);
				if($id != false) {
					return $id;
				}
			}
			if($this->default != false){
				$d = date('w', $date);
				foreach($this->parents as $array){
					$d_con =$array[0];
					$d_con_id = $array[1];
					$id = $this->getId($d_con, $d_con_id, $d, $lesson_no);
					if($id != false) {
						return $id;
					}
				}
			}
		} else {
			return false;
		}
	}
	
	public function getStaticId($date){
		$sql_rule = '';
		$year_week_first_day = first_week_route($this->date_begin, $this->first_day_week);	
		if( $date > 7){
			$sql_rule .= " AND (
				schedules_lessons.rule = '1/1' OR ";
			$this_week_begin = first_week_route($date, $this->first_day_week);
			$this_week_no = round(($this_week_begin - $year_week_first_day) / 604800)+1;
			if($this_week_no % 2 == 0){ // unpaire week for 2 week alternate
				$sql_rule .= "schedules_lessons.rule='2/2'";
			} else {
				$sql_rule .= "schedules_lessons.rule='1/2'";
			}
	
			if($this_week_no % 4 == 0){ // unpaire week for 4 week alternate
				$sql_rule .= " OR schedules_lessons.rule='4/4'";
			} elseif($this_week_no % 4 == 1){ 
				$sql_rule .= " OR schedules_lessons.rule='1/4'";
			} elseif($this_week_no % 4 == 2){ 
				$sql_rule .= " OR schedules_lessons.rule='2/4'";
			} elseif($this_week_no % 4 == 3){ 
				$sql_rule .= " OR schedules_lessons.rule='3/4'";
			} 
			$sql_rule .= ') ';
		}
	
		$def_date = date('w', $date);
		$sessionsQ = "SELECT schedules_lessons.*, schedules_date.con, schedules_date.con_id, schedules_date.date
			FROM schedules_lessons, schedules_date
			WHERE schedules_lessons.".$this->con." = '".$this->con_id."' 
			AND schedules_date.id = schedules_lessons.rec_id
			AND (schedules_date.date = $date OR schedules_date.date = $def_date) ".
			$sql_rule.
			"ORDER BY schedules_date.date ASC";
		$sessions = do_query_array( $sessionsQ, DB_year);
		$out = array();
		if(count($sessions) > 0){
			foreach($sessions as $session){
				$session->type='c';
				$time = do_query_obj( "SELECT * FROM schedules_times 
					WHERE rec_id=".$session->rec_id." 
					AND lesson_no =".$session->lesson_no, DB_year);
				if(isset($time->begin) && $time->begin != ''){
					$session->begin = $time->begin;
					$session->end = $time->end;
				} else {
					$def_date = date('w', $session->date);
					$parents =  getParentsArr($session->con, $session->con_id);
					$where = array("(schedules_date.con='".$session->con."' AND .schedules_date.con_id=".$session->con_id.")");
					foreach($parents as $array){
						$pcon =$array[0];
						$pcon_id= $array[1];
						$where[] = "(schedules_date.con='$pcon' AND .schedules_date.con_id=$pcon_id)";
					}

					$timeQ ="SELECT schedules_times.* 
						FROM schedules_times, schedules_date
						WHERE schedules_date.date=$def_date 
						AND (".implode(' OR ', $where).") 
						AND schedules_date.id = schedules_times.rec_id
						AND schedules_times.lesson_no =".$session->lesson_no;
						
					$time = do_query_obj($timeQ , DB_year);
					$session->begin = $time->begin;
					$session->end = $time->end;
				}
				$out[] = $session;
			}
			return $out;
		} else {
			return false;
		}
	}

	public function loadStaticNASessions($date){
		$day_begin = $this->day_time_begin;
		$def_date = date('w', $date);
		$out =  '';
		// get Notavaible time
		$NAtimes = do_query_array( "SELECT * FROM schedules_static_time WHERE con='".$this->con."' AND con_id=".$this->con_id." AND date=$date OR date=$def_date ORDER BY begin ASC", DB_year);
		if(count($NAtimes) > 0){
			foreach($NAtimes as $time ){
				$top = ($time->begin - $day_begin) / 60 ; 
				$height = (($time->end  - $time->begin ) / 60)-1;
				$out .= write_html('li', 'id="black_time-'.$seq['id'].'" class="maintenance blackTimeOut '.($this->update_auth? 'hand':'').'" '.($this->update_auth? 'onclick="deleteBlackTime(this, '.$seq['id'].')"':'').' style="position: absolute; z-index:2; top:'.$top.'px; height:'.$height.'px" valign="top"  val="'.$seq['id'].'"',
					unixToTime($time->begin) .' - '. unixToTime($time->end)
				);
			}
		}
		return $out;
	}
	
	public function getSessionsStructure($date, $lesson_no=false){
		$d = date('w', $date);
		if(in_array($this->con, $this->static_array)){
			return $this->getStaticId($date, $lesson_no);
		} else {
			if($result = $this->getId($this->con, $this->con_id, $date, $lesson_no)) {
				return $result;
			} else{
				if($this->default == true && $date > 7){
					if($result = $this->getId($this->con, $this->con_id, $d, $lesson_no)) {
						return $result;
					}  
				} 
				if($this->inherited){ 
					if($result = $this->getParentRecId($date, $lesson_no)) {
						return $result;
					} 
				}
				return false;
			}
		}
	}
		
	public function loadSessions($cur_date){
		global $lang;
		$sessions = $this->getSessionsStructure($cur_date);
		$out = '<ul class="day_timeline" day="'.$cur_date.'">';
		if(in_array($this->con, $this->static_array)){
			$out .= $this->loadStaticNASessions($cur_date);
		}
		if($sessions != false){
			foreach($sessions as $session){
				$lesson_no = $session->lesson_no;
				$top = ($session->begin - $this->day_time_begin) / 60 ; 
				$height = (($session->end - $session->begin) / 60)-1;
				if ($session->type == 'b'){
					$out .= write_html('li', 'style="top:'.round($top).'px; height:'.round($height-(($height -9) /2)).'px; padding-top:'.(($height -9) /2).'px" valign="top"  val="'.$cur_date.'-'.$lesson_no.'" class="rec"  begin="'.$session->begin.'" end="'.$session->end.'"',
						write_html('span', '', unixToTime($session->begin).' - '.unixToTime($session->end))
					);
				} else {
					$out .=  write_html('li', 'style="top:'.round($top).'px; height:'.round($height).'px; padding:0px" valign="top"  val="'.$cur_date.'-'.$lesson_no.'" class="activeLesson" begin="'.$session->begin.'" end="'.$session->end.'"',
						$this->getLessonCell($session, $cur_date)
					);
				}
			}
		} else{
			$out .= $lang['no_sessions'];
		}
		
		$out .= ($this->editable == false)? $this->get_shedule_events($cur_date, $this->con, $this->con_id): '';
		$out .=  '</ul>';
		return $out;
	}

	
	// lessons structures
	public function getLessonCell($session, $cur_date){
		global $lang, $MS_settings, $thisTemplatePath;
		$out='';
		if($this->hide_lessons == false ){
			$lessons = $this->getSessionLessons($session, $cur_date);
			if($lessons != false){
				// Clean up static unused lessons
				for($i=0; $i<count($lessons); $i++){
					$thisCon = $this->con;
					$thisLesson = $lessons[$i];
					if(in_array($this->con, $this->static_array) && $thisLesson->$thisCon != $this->con_id){
						unset($lessons[$i]);
					}
				}

				// Set the Read provile to openable materials and openable mat for teachres and supervisors
				if(in_array($_SESSION['group'], array('prof', 'supervisor'))) {
					$read_prvlg = true;
					if(!isset($this->openable_mats)){
						$this->openable_mats = array();
						if($_SESSION['group'] == 'supervisor'){
							$openable_mats = services::getSupervisorServices($_SESSION['user_id']);
						} elseif($_SESSION['group'] == 'prof'){
							$openable_mats = schedule::getProfScheduleService($_SESSION['user_id']);
						} 
						foreach($openable_mats as $ser){
							$this->openable_mats[] = $ser->id;
						}
					} 
				} else {
					$read_prvlg = getPrvlg('lesson_read');
				}
				//	print_r($this->openable_mats);
		
				if(count($lessons) > 0 ){
					$countLessonOnSession = count($lessons);
					$height = (($session->end - $session->begin)/60);
					$les_no = 1;
					if($countLessonOnSession == 1){
						$cell_hight = ($height - $countLessonOnSession) / $countLessonOnSession;
						$cell_width=100;
					} elseif($countLessonOnSession == 2){
						if($lessons[0]->rule != '1/1'){
							$cell_width=50;
							$cell_hight = ($height - 2);
						} else {
							$cell_width=100;
							$cell_hight = ($height - 2) / 2;
						}
					} else {
						$cell_hight = ($height - 2) / 2;
						$cell_width=50;
					}
					$font_size = ($cell_hight * 0.6 > 10) ? 10 : $cell_hight * 0.6;
					$pad = ($cell_hight -$font_size)/ 2;
					foreach($lessons as $lesson){
						if($lesson->services != ''){
							// display only lesson if static array
							if($read_prvlg && $cur_date > 7 && $this->editable ==false){
								if(in_array($_SESSION['group'], array('student', 'parent')) 
									&& $MS_settings['std_can_see_preset_lessons'] !=1 
									&& $cur_date > time() ){
									$openable = false;
								} elseif($_SESSION['group'] == 'prof' && !in_array($lesson->services, $this->openable_mats)){
									$openable = false;
								} elseif($_SESSION['group'] == 'supervisor' && !in_array($lesson->services, $this->openable_mats)){
									$openable = getPrvlg('lesson_read');
								} else {
									$openable = true;
								}
							} else {
								$openable =false;
							}
						
							$lesson_id = $lesson->id;
							$rec_id = $lesson->rec_id;
								// Service
							$service_id = $lesson->services;
							if(!isset($this->subjects[$service_id])){
								$service = new services($lesson->services);
								$service->opColor = getopositeColor($service->color);
								$this->subjects[$service_id] = $service;
							} 
							$service = $this->subjects[$service_id];
								// Icons = 
							if($session->date > 7){
								$icons = $this->getLessonIcons($lesson_id);
							} else {
								$icons = '';
							}
							$icons = $this->getLessonIcons($lesson_id);
							$out .= write_html('div', 
								'module="lessons" 
								lessonid="'.$lesson_id.'" 
								date="'.$cur_date.'"'.
								($openable ? 'action="openLessonDetails"' : ''). ' 
								class="tooltip-div '.($openable ? 'hand' : 'cursor_disabled' ) .'" 
								style="height:'.round($cell_hight - $pad).'px; padding-top:'.round($pad).'px; width:'.round($cell_width).'%; background-color:#'.$service->color.'; color:'.$service->opColor.'; "',
								write_html('b', '', 
									write_html('text', 'class="holder-material-'.$service->mat_id.'"', $service->getName())
								).
								write_html('div', 'style="position:absolute; top:0px"', $icons)
							);
							
							$out .= $this->createLessonToolTip($lesson, $service, $session, $icons);
						}
					}
				}
			} else {
				$out.= ($session->type != 'b' ? 
					write_html('span', 'style="padding-top:8px"', $lang['session'].': '. $session->lesson_no).'<br>'
				: '').
				write_html('span', '', unixToTime($session->begin).' - '.unixToTime($session->end));
			}
		} else {
			$out.= ($session->type != 'b' ? 
				write_html('span', 'style="padding-top:8px"', $lang['session'].': '. $session->lesson_no).'<br>'
			: '').
			write_html('span', '', unixToTime($session->begin).' - '.unixToTime($session->end));
		}
		return $out;
	}
	
	public function createLessonToolTip($lesson, $service, $session, $icons){
		global $lang, $thisTemplatePath, $sms;
			// prof
		$prof_id = $lesson->prof;
		if(!isset($this->profs[$prof_id])){
			$prof = new Profs($prof_id);
			$this->profs[$prof_id] = $prof;	
		}
		$prof = $this->profs[$prof_id];
			// halls
		if( $lesson->hall!= ''){
			$hall_id = $lesson->hall;
		 	if(!isset($this->halls[$hall_id])){
			
				$hall = new Halls($hall_id);
				if($hall != false){
					$this->halls[$hall_id] = $hall;
				}
				$hall = $this->halls[$hall_id];
			}
		}
		
		$tooltip = new stdClass();
		$tooltip->time_begin = unixToTime($session->begin);
		$tooltip->time_end = unixToTime($session->end);
		$tooltip->lang_con = $lang[$lesson->con];
		$tooltip->con_name = $sms->getAnyNameById($lesson->con, $lesson->con_id);
		$tooltip->mat_id = $service->mat_id;
		$tooltip->mat_name = $service->getName();
		$tooltip->prof_id = $prof!=false ? $prof->id : '';
		$tooltip->prof_name = $prof!=false ? $prof->getName() : '';
		$tooltip->hall_id =  $lesson->hall ;
		$tooltip->hall_name = isset($hall) != '' ? $hall->getName() : '';
		$tooltip->icons = $icons;

		// tooltip template
		if(!isset($this->tooltip_template)){
			$this->tooltip_template = @file_get_contents("$thisTemplatePath/schedules_lesson_tooltip.tpl");
		}
		
		$output = $this->tooltip_template;
		foreach ($tooltip as $key => $value){
			$tagToReplace = "[@$key]";
			$output = str_replace($tagToReplace, $value, $output);
		}
		$paterns =array();
		$paterns[0] = "/(\[@.*\])/";
		$paterns[1] = "/\[#(.*?)\]/";
		$replace = array();
		$replace[0] = "";
		$replace[1] = '$lang["$1"]';//${"lang[$1]"};

		$output = preg_replace($paterns[0], $replace[0], $output);		
		$output= preg_replace_callback($paterns[1], 
      	 "translatePatern", $output);
		
		return  $output;
	}
	
	public function getLessonIcons($lesson_id){
		global $lang;
		// check span icons
		$spans = array();
		// Notes
		if(count(do_query_array("SELECT id FROM schedules_notes WHERE lesson_id=".$lesson_id, DB_year)) > 0  ){
			$spans[] = '<span style="float:left" title="'.$lang['note'].'" class="ui-icon ui-icon-comment"></span>';
		}
		if( $this->MSEXT_lms == true){
			// summary
			if(count(do_query_array("SELECT * FROM lessons_summary WHERE lesson_id=".$lesson_id, LMS_Database)) > 0){
				$spans[] = '<span style="float:left" title="'.$lang['summary'].'" class="ui-icon ui-icon-note"></span>';
			}
			// Homework
			if(count(do_query_array("SELECT id FROM homeworks WHERE lesson_id=".$lesson_id, LMS_Database)) > 0){
				$spans[] = '<span style="float:left" title="'.$lang['homework'].'" class="ui-icon ui-icon-script"></span>';
			}
		}
		return implode('', $spans);
	}
	
	public function getSessionLessons($session, $cur_date){
		$def_date = date('w', $cur_date);
		$result = $this->getLesson( $session->con, $session->con_id, $session->lesson_no, $cur_date); // try to get the curent date for cur con
		if($result != false){
			return $result;
		} else { // try to get the default date value 
			if($this->inherited != false){
				$parents =  !in_array($this->con , $this->static_array) ? 
					(isset($this->parents) ?  $this->parents : getParentsArr($this->con, $this->con_id)) 
				:
					getParentsArr($session->con, $session->con_id);
				
					// student or group (younger children)	
				if(in_array($this->con, array('student', 'group'))){ 
					if($parents != false){
						foreach($parents as $array){
							$parent_con = $array[0];
							$parent_con_id = $array[1];
							if($result = $this->getLesson($parent_con, $parent_con_id, $session->lesson_no, $cur_date)){
								return $result;
							} else {
								if(in_array($parent_con, array('level', 'class'))){
									$childrens =  getChildsArr($session->con, $session->con_id); // return children values if found
									if($childrens != false){
										if($result = $this->getChidrenLesson($childrens, $session->lesson_no, $cur_date)){
											return $result;
										}
									}
								}
							}
						}
					}
					// Class or level (parents content children)
				} elseif(in_array($this->con, array('level', 'class'))){
					$childrens = !in_array($this->con , $this->static_array) ? (isset($this->childrens) ? $this->childrens : getChildsArr($this->con, $this->con_id)) : getChildsArr($this->con, $this->con_id); // return children values if found
					if($childrens != false){
						if($result = $this->getChidrenLesson($childrens, $session->lesson_no, $cur_date)){
							return $result;
						}
					}
				}
					// retur false if no result have been found
				return false;
			}
		}
	}

	public function getLesson($con, $con_id, $lesson_no, $date){
		$first_day_week = $this->first_day_week;
		$year_begin_date = $this->date_begin;
		$year_week_first_day = first_week_route($year_begin_date, $first_day_week);	
		$sql = "SELECT schedules_date.*, schedules_lessons.* 
		FROM schedules_date, schedules_lessons 
		WHERE schedules_date.id = schedules_lessons.rec_id
		AND schedules_lessons.lesson_no =$lesson_no
		AND schedules_date.date = %d
		AND schedules_date.con='$con'
		AND schedules_date.con_id = $con_id";
		// rules
		if($date > 7){
			$sql .= " AND (
				schedules_lessons.rule = '1/1' OR ";
			$this_week_begin = first_week_route($date, $first_day_week);
			$this_week_no = round(($this_week_begin - $year_week_first_day) / 604800)+1;
			if($this_week_no % 2 == 0){ // unpaire week for 2 week alternate
				$sql .= "schedules_lessons.rule='2/2'";
			} else {
				$sql .= "schedules_lessons.rule='1/2'";
			}
	
			$this_week_index = $this_week_no % 4;
			if($this_week_index == 0 ){ // unpaire week for 4 week alternate
				$sql .= " OR schedules_lessons.rule='4/4'";
			} elseif($this_week_no % 4 == 1){ 
				$sql .= " OR schedules_lessons.rule='1/4'";
			} elseif($this_week_no % 4 == 2){ 
				$sql .= " OR schedules_lessons.rule='2/4'";
			} elseif($this_week_no % 4 == 3){ 
				$sql .= " OR schedules_lessons.rule='3/4'";
			} 
			$sql .= ') ORDER BY schedules_lessons.rule ASC';
		}

		$query = do_query_array(sprintf($sql, $date), DB_year);
		if(count($query) > 0) {
		//	echo sprintf($sql, $date);
			return $query;
		} elseif($date > 7) {
			$query = do_query_array(sprintf($sql, date('w', $date)), DB_year);
			return (count($query) > 0 ?  $query :  false);
		} else {
			return false;
		}
	}
	
	public function getChidrenLesson( $array, $lesson_no, $date){
		$year_week_first_day = first_week_route($this->date_begin, $this->first_day_week);	
		$cons = array();
		foreach( $array as $arr){
			$con_id = $arr[1];
			$con = $arr[0];
			$cons[] = "(schedules_date.con='$con' AND schedules_date.con_id = $con_id)";
		}
		$sql = "SELECT schedules_date.*, schedules_lessons.* 
		FROM schedules_date, schedules_lessons 
		WHERE schedules_date.id = schedules_lessons.rec_id
		AND schedules_lessons.lesson_no =$lesson_no
		AND schedules_date.date = %d
		AND (". implode(' OR ', $cons).")";
	//	echo $sql;
		
		// rules
		if( $date > 7){
			$sql .= " AND (
				schedules_lessons.rule = '1/1' OR ";
			$this_week_begin = first_week_route($date, $this->first_day_week);
			$this_week_no = round(($this_week_begin - $year_week_first_day) / 604800)+1;
			if($this_week_no % 2 == 0){ // unpaire week for 2 week alternate
				$sql .= "schedules_lessons.rule='2/2'";
			} else {
				$sql .= "schedules_lessons.rule='1/2'";
			}
	
			if($this_week_no % 4 == 0){ // unpaire week for 4 week alternate
				$sql .= " OR schedules_lessons.rule='4/4'";
			} elseif($this_week_no % 4 == 1){ 
				$sql .= " OR schedules_lessons.rule='1/4'";
			} elseif($this_week_no % 4 == 2){ 
				$sql .= " OR schedules_lessons.rule='2/4'";
			} elseif($this_week_no % 4 == 3){ 
				$sql .= " OR schedules_lessons.rule='3/4'";
			} 
			$sql .= ') ORDER BY schedules_lessons.rule ASC';
		}
		//	echo $sql;
		$query = do_query_array(sprintf($sql, $date), DB_year);
		if(count($query) > 0) {
		//	echo sprintf($sql, $date);
			return $query;
		} elseif($date > 7) {
			$query = do_query_array(sprintf($sql, date('w', $date)), DB_year);
			return (count($query) > 0 ?  $query :  false);
		} else {
			return false;
		}
	}
	
	static function reloadCell($con, $con_id, $date, $lesson_no, $editMode=false){
		$schedule  = new schedule($con, $con_id);
		if($editMode){
			$schedule->update_auth == true;
		}
		$sessions = $schedule->getSessionsStructure($date, $lesson_no);
		$out = '';
		foreach($sessions as $session){
			$out .= $schedule->getLessonCell($session, $date);
		}
		return $out;
	}
	
	public function loadTimeline(){
		$css_dir = ($_SESSION['lang'] == 'ar') ? 'left' : 'right';
		$now = 0;

		$out = '<ul class="regle" >';
			for($tl = $this->day_time_begin; $tl <= ($this->day_time_end + 300); $tl= $tl+300){
				if( $tl % 3600 == 0){
					$out .= write_html('li', 'style="width:50px;"', '').
						write_html('span', 'style="'.$css_dir.':55px; top:'.$now.'px"', unixToTime($tl));	
				}elseif( $tl % 1800 == 0){
					$out .= write_html('li', 'style="width:20px;"', '').
					write_html('span', 'style="'.$css_dir.':25px; top:'.$now.'px"', 
						(($tl == $this->day_time_begin || $tl == $this->day_time_end) ?  unixToTime($tl) : '30')
					);
				}elseif( $tl % 300 == 0){
					$out .= write_html('li', 'style="width:10px;"', '');
				}
				$now = $now+5;
			}
			$out .= write_html('li', 'class="indecitor unprintable"', '');
		$out .= '<ul>';
		return $out;
	}
	
	public function createWeekTimeLine($begin=false, $end=false){
		global $lang, $sms;
		$obj = $sms->getAnyObjById($this->con, $this->con_id);
		$level_id = $obj->getLevel()->id;
		if($begin == false){
			$begin = $this->date_begin;//getYearSetting('begin_date');
		}
		if($end == false){
			$end = $this->date_end;//getYearSetting('end_date');
		}
		$from = $this->spc_date_begin!=false ? $this->spc_date_begin : $begin;
		$till = $this->spc_date_end!=false ? $this->spc_date_end : $end;
		
		$first_week_day = first_week_route($begin, $this->first_day_week);
		
		$week_no = 1;
		$first_week = first_route($begin, $this->first_day_week);
	
		$out = '<ul class="week_line_ul">';
		$out .= write_html('li', 'class="ui-state-default clickable hoverable selectable '.
				( $this->week_no == 0  ? 'ui-state-active':'').
				'" title="'.$lang['default'].'" weekno="0"',
			$lang['default']
		);
		$pallete = array("", "#fd6864", "#666666", "#f8a102", "#329a9d", "#6200c9", "#9698ed");
		if($first_week_day < $begin){
			if($first_week_day < $from){
				$thisTerm = !in_array($this->con , $this->static_array) ? terms::getTermByDate($level_id, $begin):false;
				$background = $thisTerm != false ? $pallete[$thisTerm->term_no] : $pallete[1];
				$out .=write_html('li', 'class="ui-state-default clickable hoverable selectable '.
					( $this->week_no == 1  ? 'ui-state-active':'').
					'" title="'.($thisTerm != false ? $thisTerm->title.': ' : '').unixToDate($begin).' - '.unixToDate($first_week - 86400).'" weekno="1" style="border-top-color:'.$background.'; border-top-width:2px"',
					$week_no
				);
			}
			$week_no++;
		}
			
		$f_week_day = date('d', $first_week);
		$f_week_month = date('m', $first_week);
		$f_week_year = date('Y', $first_week);
		$i = $f_week_day;
		$cur_stamp = $first_week;
		while($cur_stamp <= $till){
			$cur_week_first_day =  unixToDate($cur_stamp);
			$cur_stamp_last = mktime(0,0,0, $f_week_month, $i+6, $f_week_year);
			$cur_week_last_day =  unixToDate($cur_stamp_last);
			if($cur_stamp_last > $end){
				$cur_week_last_day = unixToDate($end);
			}
			if($first_week_day <= ($from+86400)){
				$thisTerm = !in_array($this->con , $this->static_array) ? terms::getTermByDate($level_id, $cur_stamp): false;
				$background = $thisTerm != false ? $pallete[$thisTerm->term_no] : $pallete[1];
				$out .=write_html('li', 'class="ui-state-default clickable hoverable selectable '.
					( $this->week_no == $week_no  ? 'ui-state-active':'').
					'" title="'.($thisTerm != false ? $thisTerm->title.': ' : '').$cur_week_first_day.' - '.$cur_week_last_day.'" weekno="'.$week_no.'" style="border-top-color:'.$background.'; border-top-width:2px"',
					$week_no
				);
			}
			$week_no++;				
			$i = $i+7;
			$cur_stamp = mktime(0,0,0, $f_week_month, $i, $f_week_year);
		}
		$out .= '</ul>';
		return $out;
	}

	public function get_shedule_events( $date){
		global $MS_settings;
		$con = $this->con;
		$con_id = $this->con_id;
	$where = array();
		$parents = isset($this->parents) ?  $this->parents : getParentsArr($this->con, $this->con_id);
		if($parents != false && count($parents) > 0){
			foreach($parents as $array){
				$c = $array[0];
				$c_id = $array[1];
				$where[] = "(".DB_year.".events_con.con='$c' AND ".DB_year.".events_con.con_id=$c_id)";
			}
		}
		
		$t = "SELECT ".DB_year.".events.*, ".DB_student.".events_label.name
		FROM ".DB_year.".events, ".DB_student.".events_label, ".DB_year.".events_con  
		WHERE ".DB_year.".events.id=".DB_year.".events_con.event_id
		AND ".DB_year.".events.event_id=".DB_student.".events_label.id 
		AND (
			".DB_year.".events.begin_date=$date 
			OR (".DB_year.".events.begin_date<$date AND ".DB_year.".events.end_date>=$date )
		)
		AND (
			(".DB_year.".events_con.con='$con' AND ".DB_year.".events_con.con_id=$con_id)
			OR (".DB_year.".events_con.con IS NULL AND ".DB_year.".events_con.con_id IS NULL)".
			(count($where)>0 ? " OR ".implode(' OR ', $where) : '').
		")
		ORDER BY ".DB_year.".events.begin_time ASC";
		$rec = do_query_array($t, DB_year);
		
		$out = '';
		if(count($rec) > 0){
			$day_begin = $this->day_time_begin;
			foreach($rec as $seq){
				$top = $seq->begin_time!= '' ? ($seq->begin_time - $day_begin) / 60 : 0; 
				$height = (($seq->end_time - $seq->begin_time) / 60);
				$out .= write_html('li', 'class="calender_events ui-state-highlight" style="top:'.$top.'px; height:'.$height.'px" val="'.$seq->id.'"',
					write_html('b', 'style="color:#000066"', unixToTime($seq->begin_time) .' - '. unixToTime($seq->end_time).'<br />'.$seq->name)
				);
			}
		}
		return $out;
	}
	
	static public function getProfScheduleService($prof_id, $con='', $con_id=''){
		$out = array();
		$sql = "SELECT DISTINCT schedules_lessons.services FROM schedules_date, schedules_lessons 
		WHERE schedules_lessons.rec_id =schedules_date.id
		AND schedules_lessons.prof =$prof_id ";
		if($con != false && $con != ''){
			$materilas = array();
			$childs = getChildsArr($con, $con_id);
			$where =array();
			foreach($childs as $array){
				$child_con = $array[0];
				$child_con_id = $array[1];
				$where[] = "(schedules_date.con ='$child_con' AND schedules_date.con_id = $child_con_id)";
			}
			$sql .= "AND (
				(schedules_date.con ='$con' AND schedules_date.con_id = $con_id)".
				(count($where) > 0 ?
					"OR ".implode(' OR ', $where)
				: '').
			")";
		}
		
		$q = do_query_array( $sql, DB_year);
		foreach($q as $r){
			$out[] = new services($r->services);
		}
		
		if(count($out) > 0){
			return Services::orderService($out);
		} else {
			return false;
		}
	}
	
	public function createDay($date) {
		$scheduleEdit = new scheduleEdit($this->con, $this->con_id);
		$new_rec_id = $scheduleEdit->getRecId($this->con, $this->con_id, $date, true);
		$this->default = true;
		$this->inherited = true;
		$sessions = $this->getSessionsStructure($date);
		$result = true;
		if($sessions != false){
			foreach($sessions as $session){
				$lessons = $this->getSessionLessons($session, $date);
				foreach($lessons as $lesson){
					$new_lesson = $lesson;
					$new_lesson->exam = 0;
					$new_lesson->rec_id = $new_rec_id;
					unset($new_lesson->id);
					if(!do_insert_obj($new_lesson, 'schedules_lessons', DB_year)){
						$result = false;
					}					
				}
			}
		} else {
			$result = false;
		}
		return $result;
	}

	public function createSession($date, $lesson_no) {
		$scheduleEdit = new scheduleEdit($this->con, $this->con_id);
		$new_rec_id = $scheduleEdit->getRecId($this->con, $this->con_id, $date, true);
		$this->default = true;
		$this->inherited = true;
		$sessions = $this->getSessionsStructure($date, $lesson_no);
		$result = true;
		if($sessions != false){
			foreach($sessions as $session){
				$lessons = $this->getSessionLessons($session, $date);
				foreach($lessons as $lesson){
					$new_lesson = $lesson;
					$new_lesson->exam = 0;
					$new_lesson->rec_id = $new_rec_id;
					unset($new_lesson->id);
					if(!do_insert_obj($new_lesson, 'schedules_lessons', DB_year)){
						$result = false;
					}					
				}
			}
		} else {
			$result = false;
		}
		return $result;
	}

}