<?php

class questionBank {
	public $service_id = '', 
	$book_id = '', 
	$chapter_id = '', 
	$summary_id = '',
	$type='questions',
	$editable =false,
	$answerable = false;
	

	public function __construct($obj){
		$this->setEnv($obj);
		$this->service = new Services($this->service_id);
		$this->editable = Services::check_user_service_privilege($this->service_id); 
		$this->answerable = $_SESSION['group'] == 'student' ? true : false;
	}
	
	public function setEnv($obj){
		foreach($obj as $key => $value){
			if(isset($this->$key)){
				$this->$key = $value;
			}
		}
	}
	
	public function findQuizTpl(){
		global $lang;
		$out = array();
		$tpls =  scandir(dirname(__file__).'/templates');
		foreach($tpls as $f){
			if(strpos($f , 'quiz_') !== false && strpos($f , '_answer') === false){
				$quiz_name = str_replace(array("quiz_", ".tpl")	, "", $f);
				$out[$quiz_name] = $lang[$quiz_name];
			}
		}
		return $out;
	}

	public function loadSearchView(){
		global $lang, $thisTemplatePath;
		$question_search = new stdClass();
		$question_search->service_id = $this->service_id;
		$question_search->material_id = $this->service->mat_id;
		$question_search->level_id = $this->service->level_id;
		$question_search->service_name = $this->service->getName();
		$question_search->level_name = getAnyNameById('level', $this->service->level_id);
		$question_search->search_results = $this->loadSearchResults();
		$question_search->book_id_options =write_select_options(getServiceBooks($this->service_id), $this->book_id);
		$question_search->chapter_id_options =write_select_options(getBookChapers($this->book_id), $this->chapter_id);
		$question_search->add_question_toolbox = createToolbox(array(
			array(
				"tag" => "span",
				"attr"=> 'style="margin:0px 10px" class="ui-corner-all ui-state-default"',
				"text"=> write_html('text', 'style="padding: 4px"',$lang['type'].': ').
					write_html_select('name="type" update="searchQuestion" class="ui-state-default def-float" ', $this->findQuizTpl(), $this->type).
					write_html('but', 'style="margin:2px" class="hoverable hand" action="searchQuestion"', write_icon('refresh')),
				"icon"=> ""
			),
			array(
				"tag" => "a",
				"attr"=> 'action="editQuestion" type="question" questionid="new" serviceid="'.$this->service_id.'"',
				"text"=> $lang['add'],
				"icon"=> "plus"
			),
			array(
				"tag" => "a",
				"attr"=> 'action="insertQuestion"',
				"text"=> $lang['insert'],
				"icon"=> "triangle-1-e"
			)
		));

		return fillTemplate("$thisTemplatePath/question_search.tpl", $question_search);
	}
	
	public function linkToimage($link){
		return "<img src=\"index.php?plugin=img_resize&path=".getPathFromLink($link[1])."\" />";
	}
	
	public function loadSearchResults(){
		global $lang, $thisTemplatePath;
		$type = $this->type;
		$sql = "SELECT questions.id AS question_id, questions.*, quiz_$type.* , books.id, books.title AS book_name, chapters.id, chapters.title AS chapter_name, summarys.title as summary_title
			FROM questions, quiz_$type , books, chapters, summarys
			WHERE questions.id=quiz_$type.id 
			AND questions.book_id=books.id
			AND questions.chapter_id=chapters.id
			AND questions.summary_id=summarys.id
			AND	questions.service_id=".$this->service_id.
			($this->book_id != '' ? " AND questions.book_id=".$this->book_id : '').
			($this->chapter_id != '' ? " AND questions.chapter_id=".$this->chapter_id : '').
			($this->summary_id != '' ? " AND questions.summary_id=".$this->summary_id : '');
		$questions = do_query_array($sql, LMS_Database);
		$out = '';
		foreach($questions as $question){
			$question->question = preg_replace_callback("/\[image\:(.*?)\]/i", 
				function($matchs){
					$file = new Files(str_replace(array('[image:',']'), '',$matchs[0]));
					return "</br><img src=\"index.php?plugin=img_resize&path=".$file->path."\" style=\"max-width:400px;vertical-align: top;margin:3px\" />";
				},
				$question->question
			);
			$out .= fillTemplate("$thisTemplatePath/question_search_items.tpl", $question);
		}
		return $out;
	}
	
		// load question for edit
	public function loadQuestion($question_id='new'){
		global $lang, $thisTemplatePath;
		if($question_id != 'new'){
			$question = do_query_obj("SELECT * FROM questions WHERE id=$question_id", LMS_Database);
			$this->setEnv($question);
			$question_data = do_query_obj("SELECT * FROM quiz_".$this->type." WHERE id=$question_id", LMS_Database);
			$question = (object)array_merge( (array)$question, (array)$question_data);
		} else {
			$question = new stdClass();
			$question->type = $this->type;
			$question->chapter_id = $this->chapter_id;
			$question->book_id = $this->book_id;
			$question->service_id = $this->service_id;
			$question->summary_id = $this->summary_id;
			$question->question = ''; 
			$question->time = '';
			$question->point = '';
			$question->answer = '';
		}
		if($question->summary_id != ''){
			$summary_title =  do_query_obj("SELECT title FROM summarys WHERE id=".$question->summary_id, LMS_Database);
			$question->summary_title = $summary_title->title;
		}
		$question->book_id_options =write_select_options(getServiceBooks($this->service_id), $this->book_id);
		$question->chapter_id_options =write_select_options(getBookChapers($this->book_id), $this->chapter_id);
		// begin layouting
			
		if($this->editable){
			$lines = isset($question->lines) && $question->lines>0 ? $question->lines : 3;
			$question_toolbox = array(array(
				"tag" => "a",
				"attr"=> 'action="insertQuestionImage"',
				"text"=> $lang['insert_image'],
				"icon"=> "image"
			));
			$question->question = write_html('textarea', 'name="question"', $question->question);
			$question->time_html = '<input type="text" name="time" class="input_half" value="'.$question->time.'" /> '.$lang['seconds'];
			$question->points_html = '<input type="text" name="point" class="input_half" value="'.$question->point.'"/> '.$lang['points'];
				// Question
			if($question->type == 'questions'){
				$question_toolbox[] = array(
					"tag" => "span",
					"attr"=> 'style="margin:0px 5px" class="ui-corner-right ui-state-default"',
					"text"=> write_html('text', 'style="padding:4px"',$lang['lines'].': ').'<input type="text" name="lines" value="'.$lines.'" class="spinner" style="padding:0; width:20px"" />',
					"icon"=> ""
				);
			// true OR false
			} elseif($question->type == 'truefalse'){
				if(isset($question->answer)){
					if($question->answer == 'true'){
						$question->value_true_class = 'ui-state-active';
					} elseif($question->answer == 'false'){
						$question->value_false_class = 'ui-state-active';
					}
				}
				// Complete
			} elseif($question->type == 'complete'){
				$question_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="insertPlaceHolder"',
					"text"=> $lang['add_words'],
					"icon"=> "plus"
				);
				$answers_lis = array();
				if($question->answer != ''){
					$answers = strpos($question->answer, ',') !== false ? explode(',', $question->answer) : array($question->answer);
					foreach($answers as $word){
						$answers_lis[] = write_html('li', '', '<input type="text" name="place_holder" value="'.$word.'" update="updatePlaceholder"/>'); 
					}
					$question->answer_html = implode('', $answers_lis);
				}
			}elseif($question->type == 'select'){
				if(isset($question->bool) && $question->bool != ''){
					$bools = strpos($question->bool, ',') !== false ? explode(',', $question->bool) : array($question->bool);
				} else{
					$bools = array('0'=>'','1'=>'','2'=>'','3'=>'');
				}
				$bool_html = '';
				$answers = strpos($question->answer, ',') !== false ? explode(',', $question->answer) : array($question->answer);
				for($i=0; $i<4; $i++){
					$bool_html .= write_html('li', '', 
						'<input type="text" name="opt'.($i+1).'" value="'.$bools[$i].'" update="updateSelectValues"/>'.
						write_html('span', 'class="ui-corner-all ui-state-default hoverable clickable '.(in_array($bools[$i], $answers) ? 'ui-state-active' : '').'" style="padding: 4px;"', 
							'<input type="checkbox" update="updateSelectAnswer" value="'.$bools[$i].'" title="'.$lang['select_correct_answer'].'" '.(in_array($bools[$i], $answers) ? 'checked="checked"' : '').' />'
						)
					);
				}
				$question->bool_html = $bool_html;
			}
			
			$question->toolbox = createToolbox($question_toolbox);
			return fillTemplate("$thisTemplatePath/quiz_".$this->type.".tpl", $question);
		} else {
			return write_error($lang['no_privleges']);
			
		}
	}
	
	public function submitQuestion($array, $type){
		global $lang;
		if($type == false || $type == ''){
			$answer['error'] = 'Must define question type';
		} else {
			$this->$type = $type;
			if($this->editable){
				if($array['id'] !='' && $array['id'] !='new'){ // edit question
					if(UpdateRowInTable("questions", $array, "id=".$array['id'], LMS_Database)){
						UpdateRowInTable("quiz_$type", $array, "id=".$array['id'], LMS_Database);
						$question_id = $array['id'];
						$result = true;
					}
				} else { // new homework
					if(isset($array['id'])){ unset($array['id']);}
					if( insertToTable("questions", $array, LMS_Database)){
						$question_id = mysql_insert_id();
						$array['id'] = $question_id;
						insertToTable("quiz_$type", $array, LMS_Database);
						$result = true;
						$array['chapter_name'] = getChapterNameById($array['chapter_id']);
						$array['book_name'] = getBookNameById($array['book_id']);
						$answer['html'] = $this->loadQuestion($question_id);
					}
				}
				if($result){
					$answer['id'] = $question_id;
					$answer['error'] = "";
				} else {
					$answer['id'] = "";
					$answer['error'] = $lang['error_updating'];
				}
			} else {
				$answer['error'] = $lang['no_privilege'];
			}
		}
		return json_encode($answer);
	}
	
	public function deleteQuestion($question_id, $type){
		global $lang;
		if($type == false || $type == ''){
			$answer['error'] = 'Must define question type';
		} else {
			$this->$type = $type;
			if($question_id == '' || $question_id == false){
				$answer['error'] = $lang['request_malformed'];
			} else {
				if($this->editable){
					if(do_query_edit("DELETE FROM questions WHERE id=$question_id", LMS_Database)){
						do_query_edit("DELETE FROM quiz_$type WHERE id=$question_id", LMS_Database);
						$answer['id'] = $question_id;
						$answer['error'] = "";
					} else {
						$answer['id'] = "";
						$answer['error'] = $lang['error_updating'];
					}
				} else {
					$answer['error'] = $lang['no_privilege'];
				}
			}
		}
		return json_encode($answer);
	}
		
		// load question for answer
	public function parseQuestion($data){
		global $lang, $thisTemplatePath;
		$data = str_replace(array('[', ']'), '', $data);
		if(strpos($data, '-') === false && !is_int( (int) $data)){
			return false;
		} else {
			if(strpos($data, '-') === false){
				$question_id = $data;
			} else {
				list($question_id, $point, $time) = explode('-', $data);
			}
			$question = do_query_obj("SELECT * FROM questions WHERE id=$question_id", LMS_Database);
			$this->setEnv($question);
			$question_data = do_query_obj("SELECT * FROM quiz_".$this->type." WHERE id=$question_id", LMS_Database);
			$question = (object)array_merge( (array)$question, (array)$question_data);
				// add summary title if exists
			if($question->summary_id != ''){
				$summary_title =  do_query_obj("SELECT title FROM summarys WHERE id=".$question->summary_id, LMS_Database);
				$question->summary_title = $summary_title->title;
			}
				// parse question images
			$question->question = preg_replace_callback("/\[image\:(.*?)\]/i", 
				function($matchs){
					$file = new Files(str_replace(array('[image:',']'), '',$matchs[0]));
					return "</br><div align=\"center\"><img src=\"index.php?plugin=img_resize&path=".$file->path."\" style=\"max-width:50%;vertical-align: top;margin:3px\" /></div>";
				},
				$question->question
			);
				// parse for answer if answerable
			if($this->answerable == true){
				$stdAns = do_query_obj("SELECT answer FROM exercices_answers WHERE exercise_id=$exercise_id AND question_id=$question_id AND std_id=$std_id", LMS_Database);
				// display student answer if exists
				if($stdAns->answer !=''){
					if($question->type == 'complete'){
						$answers = strpos($stdAns->answer, ',') !== false ? explode(',', $stdAns->answer) : $stdAns->answer;
						$question->question = $this->parseCompleteQuestion($data, $answers);
					} elseif($question->type == 'select'){
						$bools = strpos($question->bool, ',') !== false ? explode(',', $question->bool) : array($question->bool);
						$bool_html = '';
						$answers = strpos($question->answer, ',') !== false ? explode(',', $question->answer) : array($question->answer);
						for($i=0; $i<4; $i++){
							$bool_html .= write_html('li', '', 
								'<input type="text" name="opt'.($i+1).'" value="'.$bools[$i].'" update="updateSelectValues"/>'.
								write_html('span', 'class="ui-corner-all ui-state-default hoverable clickable '.(in_array($bools[$i], $answers) ? 'ui-state-active' : '').'" style="padding: 4px;"', 
									'<input type="checkbox" update="updateSelectAnswer" value="'.$bools[$i].'" title="'.$lang['select_correct_answer'].'" '.(in_array($bools[$i], $answers) ? 'checked="checked"' : '').' />'
								)
							);
						}
						$question->bool_html = $bool_html;

					} else {
						$question->answer = $stdAns->answer;
					}
				}
			} else {
				$question->answer = ''; // clear answer before fetch
				// Complete quiz parse
				if($question->type == 'complete'){
					$question->question = $this->parseCompleteQuestion($question->question, false);
				}elseif($question->type == 'select'){
					$bools = strpos($question->bool, ',') !== false ? explode(',', $question->bool) : array($question->bool);
					$bool_html = '';
					for($i=0; $i<4; $i++){
						$bool_html .= write_html('li', 'class="ui-corner-all ui-state-default hoverable selectable clickable"', 
						'<input type="checkbox" update="updateSelectAnswer" value="'.$bools[$i].'" />'.
							$bools[$i]
						);
					}
					$question->bool_html = $bool_html;
				} elseif($question->type == 'question'){
					if($question->lines != ''){
						$question->size = 'rows="'.$question->lines.'"';
					} else {
						$question->size = 'rows="3"';
					}
				}
			}
			
			
			return fillTemplate("$thisTemplatePath/quiz_".$this->type."_answer.tpl", $question);
		}
		
	}
	
	private function parseCompleteQuestion($data, $answer=false){
		$out = preg_replace_callback("/\[(.)x*\]/i", 
			function($matchs){
				global $answer;
				$index = $matchs[1];
				$value = ($answer != false && isset($answer[$index])) ? $answer[$index] : '';
				return '<input type="text" class="answer MS_formed complete_word" name="answer'.$index.'" value="'.$value.'" />';
			},
			$data
		);
		return $out;
	}
}