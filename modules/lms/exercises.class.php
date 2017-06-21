<?php
/**
* Exercises
*
*/

class Exercises{
	public $service_id = '', 
	$book_id = '', 
	$chapter_id = '', 
	$summary_id = '',
	$editable =false,
	$answerable = false;
	
	private $thisTemplatePath = 'modules/lms/templates';
	
	public function __construct($obj){
		require_once('modules/services/services.class.php');
		$this->setEnv($obj);
		$this->editable = services::check_user_service_privilege($this->service_id);
		$this->answerable = $_SESSION['group'] == 'student' ? true : false;
	}
	
	public function setEnv($obj){
		foreach($obj as $key => $value){
			if(isset($this->$key)){
				$this->$key = $value;
			}
		}
	}

	public function loadSearchView(){
		require_once('scripts/lms_functions.php');
		global $lang;
		$exercise_toolbox = array(
			array(
				"tag" => "a",
				"attr"=> 'module="lms" action="searchExercise" type="question" serviceid="'.$this->service_id.'"',
				"text"=> $lang['search'],
				"icon"=> "search"
			),
			array(
				"tag" => "a",
				"attr"=> 'module="lms" action="editExercise" exerciseid="new" serviceid="'.$this->service_id.'"',
				"text"=> $lang['new'],
				"icon"=> "plus"
			)
		);
		
		$exercise = new stdClass();
		$exercise->service_id = $this->service_id;
		if($this->book_id != ''){
			$exercise->book_id = $this->book_id;
		}
		if($this->chapter_id != ''){
			$exercise->chapter_id = $this->chapter_id;
		}
		if($this->summary_id != ''){
			$exercise->summary_id = $this->summary_id;
		}
		$exercise->toolbox = createToolbox($exercise_toolbox);
		$exercise->book_id_options =write_select_options(
			getServiceBooks($this->service_id), 
			$this->book_id,
			true
		);
		$exercise->chapter_id_options =write_select_options(getBookChapers($this->book_id), $this->chapter_id, true);
		$exercise->books_search_fieldset = fillTemplate("$this->thisTemplatePath/books_search_fieldset.tpl", $exercise);
		return fillTemplate("$this->thisTemplatePath/exercises_search.tpl", $exercise);
		
	}
	
	public function loadSearchList(){
		
	}
	
	public function getExerciseData($exercise_id){
		$exercise = do_query_obj("SELECT * FROM exercise WHERE id=$exercise_id", LMS_Database);
		return $exercise;
	}
	
	public function loadEditExercise($exercise_id='new'){
		global $lang;
		if($exercise_id!='new'){
			$exercise = do_query_obj("SELECT * FROM exercise WHERE id=$exercise_id", LMS_Database);
			if($exercise->id != ''){
				$seek = true;
				setEnv($exercise);
				$count_pages = substr_count($exercise->content, '</ol>');
				if($count_pages > 1){
					$pages_lis = '';
					for($i=0; $i<$count_pages; $i++){
						$pages_lis .= write_html('li', 'class="ui-state-default ui-state-active hoverable clickable seletable" action="dispalyExerPage" targetpage="'.$i.'" title="'.$lang['page'].' '.$i.'"', ($i+1));
					}
					$exercise->page_nav = $pages_lis;
				} else {
					$exercise->page_nav = write_html('li', 'class="ui-state-default ui-state-active hoverable clickable seletable def_float" action="dispalyExerPage" targetpage="0" title="'.$lang['page'].' 1"', 1);
				}
			}
		} else {
			$exercise_id = 'new';	
			$exercise = new stdClass();		
			if($this->service_id == ''){
				die("error undefined subject");
			} else {
				$exercise->service_id = $this->service_id;
			}
			$exercise->page_nav = write_html('li', 'class="ui-state-default ui-state-active hoverable clickable seletable def_float" action="dispalyExerPage" targetpage="0" title="'.$lang['page'].' 1"', 1);
		}
		if($this->book_id != ''){
			$exercise->chapter_id_options =write_select_options(getBookChapers($this->book_id), $this->chapter_id, true);
		}
		$exercise->book_id_options =write_select_options( getServiceBooks($this->service_id), $this->book_id, true);
		$exercise->toolbox = createToolbox(array(
			array(
				"tag" => "a",
				"attr"=> 'action="addExercisePage"',
				"text"=> $lang['add_page'],
				"icon"=> "document"
			),
			array(
				"tag" => "a",
				"attr"=> 'action="addExerciseHeader"',
				"text"=> $lang['add_header'],
				"icon"=> "tag"
			),
			array(
				"tag" => "a",
				"attr"=> 'action="addExerciseText"',
				"text"=> $lang['add_text'],
				"icon"=> "script"
			),
			array(
				"tag" => "a",
				"attr"=> 'action="addExerciseHr"',
				"text"=> $lang['add_separator'],
				"icon"=> "grip-solid-horizontal"
			)
		));
		
		$ques_opts = new stdClass();
		$ques_opts->service_id = $this->service_id;
		$ques_opts->book_id = $this->book_id;
		$ques_opts->chapter_id = $this->chapter_id;
		$ques_opts->summary_id = $this->summary_id;
		$questionBank = new questionBank($ques_opts);
		
		$exercise->question_bank = $questionBank->loadSearchView();
		return fillTemplate("$this->thisTemplatePath/exercises_edit.tpl", $exercise);
	}
	
	public function loadExercise($exercise_id=false){
		global $lang;
		if($exercise_id != false){
			$exercise = getExerciseData($exercise_id);
			$exercise->data = $this->parseExerciseData($exercise->content);
		} else {
			$exercise = new stdClass();
			foreach($_GET as $key =>$value){
				$exercise->$key = safeGet($value);
			}
			$data = urldecode($_GET['data']);
			$exercise->data = $this->parseExerciseData($data);
		}
		if($exercise->layout == "slide"){
			$exercise->slider = "page_slider";
			$exercise->pages_nav = write_html('table', 'cellpadding="0" width="100%"',
				write_html('tr', '', 
					write_html('td', 'aligh="center" width="50%"', write_html('a', 'action="exercisePrevPage" class="button ui-corner-all ui-state-default hoverable hand"', $lang['prev'])).
					write_html('td', 'aligh="center" width="50%"', write_html('a', 'action="exerciseNextPage" class="button ui-corner-all ui-state-default hoverable hand"', $lang['next']))
				)
			);
		} 
		return fillTemplate("$this->thisTemplatePath/exercises.tpl", $exercise);
	}
	
	public function parseExerciseData($data){
		global $lang;
		preg_match_all("/{(.*)}/U", $data, $pages);
		$out = '';
		$i=1;
		foreach($pages[1] as $page){
			$html = write_html('h3', '', $lang['page'].': '.$i);
			$i++;
			preg_match_all("/<%(.*?)%>/i", $page, $tags); //[^(<%)]
			foreach($tags[1] as $tag){
				if(preg_match("/\[(.*)\]/i",$tag)){
					$ques_opts = new stdClass();
					$ques_opts->service_id = $this->service_id;
					$ques_opts->book_id = $this->book_id;
					$ques_opts->chapter_id = $this->chapter_id;
					$ques_opts->summary_id = $this->summary_id;
					$questionBank = new questionBank($ques_opts);
					$html .= write_html('li', 'class="ui-corner-all ui-widget-content"', $questionBank->parseQuestion($tag));
				} else {
					if(preg_match("~<h3>(.*)</h3>~i",$tag)){
						$class = "ui-widget-header";
					} elseif(preg_match("~<p(.*)</p>~i",$tag)){
						$class = "ui-state-highlight";
					} else {
						$class ="";
					}
					$html .= write_html('li', 'class="ui-corner-all '.$class.'"', $tag);
				}
			}
			$out .= write_html('page', 'class="page"', 
				write_html('ol', '', $html)
			);
		}
		return write_html('div', 'class="slider_content"', $out);
	}
	
	public function submitExercise(){
		
	}
	
	public function deleteExercise($exercise_id){
		
	}
	
	public function loadExerciseAnswer($exercise_id){
		
	}
	
	public function loadExerciseResults($exercise_id, $std_id){
		
	}
	
}
	
?>