<?php
/** Lesson
*
*/

class Lessons{

	
	public function __construct($id){
		if($id != ''){	
			$lesson = do_query_obj("SELECT * FROM schedules_lessons WHERE id=$id", DB_year); 
			if(isset($lesson->id)){
				foreach($lesson as $key =>$value){
					$this->$key = $value;
				}
				$cons = do_query_obj("SELECT * FROM schedules_date WHERE id=$lesson->rec_id", DB_year);
				$this->con = $cons->con;
				$this->con_id = $cons->con_id;
				$this->date = $cons->date;
			} else {
				echo "SELECT schedules_date.con, schedules_date.con_id, schedules_date.date, schedules_lessons.* 
			FROM schedules_date, schedules_lessons 
			WHERE  schedules_date.id=schedules_lessons.rec_id
			AND schedules_lessons.id=$id";
				return false;
			}	
		} else { return false;}
			
	}
	
	static function searchSession($con, $con_id, $date, $lesson_no){
		$lesson = do_query_obj("SELECT schedules_date.con, schedules_date.con_id, schedules_date.date, schedules_lessons.* 
		FROM schedules_date, schedules_lessons 
		WHERE schedules_date.con='$con' 
		AND schedules_date.con_id=$con_id 
		AND schedules_date.date=$date
		AND schedules_date.id=schedules_lessons.rec_id
		AND schedules_lessons.lesson_no=$lesson_no", DB_year); 
		if(isset($lesson->id)){
			return new Lessons($lesson->id);
		} else {
			$schedule = new schedule($con, $con_id);
			if($schedule->createSession($date, $lesson_no)){
				return Lessons::searchSession($con, $con_id, $date, $lesson_no);
			} else {
				return false;
			}
		}	
	}
	
	public function getNotes(){
		$notes = do_query_array("SELECT id FROM schedules_notes WHERE lesson_id=$this->id", DB_year);
		$out = array();
		foreach($notes as $note){
			$new = new Notes($note->id); 
			$out[] = $new->toList();
		}
		return $out;
	}
	
	public function getHomework(){
		if(MSEXT_lms){
			$homeworks = do_query_array("SELECT * FROM homeworks WHERE lesson_id=$this->id", LMS_Database);
			$out = array();
			foreach($homeworks as $homework){
				$out[] = fillTemplate("modules/lms/templates/homeworks_list.tpl", $homework);
			}
		} else {
			return false;
		}
		return $out;
	}
	
	public function getSummarys(){
		global $lang;
		$sum_arr = array();
		$summarys_layout = '';
		
		$select = array("summarys.*", "books.title AS book_name", "chapters.title AS chapter_name");
		$tables = array('summarys',"lessons_summary", "books", "chapters");
		$where = array(
			"summarys.book_id=books.id",
			"summarys.chapter_id=chapters.id",
			"lessons_summary.summary_id=summarys.id",
			"lessons_summary.lesson_id=$this->id"
		);
		
		$summarys_query = createQuery( $select, $tables, $where, 'summarys.id ASC');
		
		$summarys= do_query_array($summarys_query, LMS_Database);
		
		if(count($summarys) > 0){
			$lessons_summarys_list = '';
			$summary_trs = array();
			foreach ($summarys as $summary) {
				if($this->is_editable()){
					$summary->dettach_but = write_html('td', 'width="28" valign="top"',
						write_html('button', 'title="'.$lang['remove'].'" action="dettachSummary" summaryid="'.$summary->id.'" lesson_id="'.$this->id.'" class="ui-state-default hoverable circle_button"',
							write_icon("circle-close")
						)
					);
				}
				$lessons_summarys_list .= fillTemplate("modules/lessons/templates/lesson_summarys.tpl", $summary);
			}
		//	$lessons_summarys_list = filleMergedTemplate("$thisTemplatePath/lesson_summarys.tpl", $summarys);
			return write_html('table', 'width="100%" class="result fixed"',  $lessons_summarys_list);
		}  else{
			return  write_html('div', 'class="ui-state-highlight ui-corner-all"', $lang['no_summary']);
		}
	}
	
	public function loadLayout(){
		global $lang, $sms;
		$notes = $this->getNotes();
		$accordion1_items[] = write_html('h3', '', 
			write_html('a','', $lang['notes'].' '. 
				write_html('span', 'class="notes_counter"', (count($notes) >0 ? "(".count($notes).")" : ''))
			)
		).
		write_html('div', 'id="notes_div" style="padding:3%"', 
			 write_html('table', 'width="100%" class="result fixed notes_list"', implode('', $notes))
		);
			
		$homeworks = $this->getHomework();
		if($homeworks != false){ 
			$countHomework = count($homeworks);
			$accordion1_items[] = write_html('h3', '',  
				write_html('a','', $lang['homework'].' '. 
					write_html('span', 'class="homework_counter"', ($countHomework>0 ? "($countHomework)" : '') )
				)
			).
			write_html('div', 'id="homework_div" style="padding:3%"', 
				($countHomework > 0 ? 
					write_html('table', 'width="100%" class="result fixed homework_list"', implode('', $homeworks))
				: 
					$lang['no_homework_found']
				)
			);
		};
		
		$layout = new Layout();
		$layout->date = unixToDate($this->date);
		$service = new services($this->services);
		$layout->material_name = $service->getName();
		$prof = new Employers($this->prof);
		$layout->prof_name = $prof->getName();
		$hall = new Halls($this->hall);
		$layout->hall_name = $hall->getName();
		$layout->con_name = $sms->getAnyNameById($this->con, $this->con_id);
		$layout->accordion1 =implode('', $accordion1_items);
	
		if(MSEXT_lms){ 
			$layout->summary_td = write_html('td', 'width="70%" valign="top"',
				write_html('fieldset', '', 
					write_html('legend', '', $lang['summary']).
					$this->getSummarys()
				)
			);
		};
					
		if($this->is_editable()){
			$session_toolbox = array();
			if(!in_array($_SESSION['group'], array('student', 'parent'))){
				$session_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="lessons" action="openNote" shared="1" lessonid="'.$this->id.'"',
					"text"=> $lang['add_note'],
					"icon"=> "comment"
				);
			}
			if(MSEXT_lms){
				$session_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="lms" action="openSummary" lessonid="'.$this->id.'" serviceid="'.$service->id.'"',
					"text"=> $lang['new_summary'],
					"icon"=> "note"
				);
				$session_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="lms" action="openHomework" lessonid="'.$this->id.'"',
					"text"=> $lang['homework'],
					"icon"=> "script"
				);
			}
			$layout->toolbox = createToolbox($session_toolbox);
			
			$lesson_layout =fillTemplate("modules/lessons/templates/lesson.tpl", $layout);
	
			return write_html('div', 'class="tabs"',
				write_html('ul', '',
					write_html('li', '', write_html('a', 'href="#lesson_tab"', $lang['lesson'])).
					write_html('li', '', write_html('a', 'href="index.php?module=lms&plan&lesson_id"', $lang['plan']))
				).
				write_html('div', 'id="lesson_tab"',
					$lesson_layout
				)
			);
		} else {
			return fillTemplate("modules/lessons/templates/lesson.tpl", $this);
		}
	}
	
		// function to check if user can edit lesson and lms function
	public function is_editable(){
		if(!isset($this->editable)){
			if(!in_array($_SESSION['group'], array('prof', 'supervisor'))){
				$this->editable = getPrvlg('lesson_edit');
			} else {
				if($_SESSION['group'] == 'prof'){
					$lesson= do_query_obj("SELECT * FROM schedules_lessons WHERE id=$this->id AND prof=".$_SESSION['user_id'], DB_year);
					if($lesson->services != ''){
						$this->editable = true;
					} else { 
						$this->editable = false;
					}
				} elseif($_SESSION['group'] == 'supervisor'){
					$supervisor = new Supervisors($_SESSION['user_id']);
					$supervisor_service = $supervisor->getServices();
					$this->editable = in_array(new services($this->services), getSupervisorServices($_SESSION['user_id']));
				}
			}
		}
		return $this->editable;
	}

}
