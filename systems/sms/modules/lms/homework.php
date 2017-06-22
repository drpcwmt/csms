<?php
## lessons Homework


if(isset($_GET['loadhomework'])){
	$lesson_id = safeGet($_GET['lesson_id']);
	$lesson = do_query_obj("SELECT services FROM schedules_lessons WHERE id=$lesson_id", DB_year);
	$editable = services::check_user_service_privilege($lesson->services);
	echo loadHomeworkList($lesson_id, $editable);
	exit;
}


/***************** Post *******************/
if(isset($_GET['submithomework'])){
	$result = false;
	setJsonHeader();
	$lesson_id = isset($_GET['lesson_id']) ? safeGet($_GET['lesson_id']) : '';
	echo json_encode(homeworks::_save($_POST, $lesson_id));
	exit;
}

if(isset($_POST['delhomework']) && $_POST['delhomework'] != ''){
	setJsonHeader();
	echo json_encode(homeworks::_delete($_POST));
	exit;
}

/****************** Body ******************/

$hw_seek = false;
if(isset($_GET['hw_id'])){
	$homework_id = safeGet($_GET['hw_id']);
	$homework = do_query_obj("SELECT * FROM homeworks WHERE id=$homework_id", LMS_Database);
	if(isset($homework->id)){
		$hw_seek = true;
		$homework->homeworkInpValue = $homework_id;
		$lesson_id = $homework->lesson_id;
	}

	$lesson = new Lessons($lesson_id);
	$editable = $lesson->is_editable();
	$service_id = $lesson->services;
	$homework->service_id = $service_id;

	$attach_arr = array();
	$hw_attach = do_query_resource("SELECT link FROM homeworks_attachs WHERE homework_id=$homework_id", LMS_Database);
	while($at = mysql_fetch_assoc($hw_attach)){
		$attach_arr[] = $at['link'];
	}
	$homework->attachements_list = Documents::loadAttachList('homework', $homework_id, $editable) ;
	$homework->attachements_value = implode(',', $attach_arr);
	$homework->maxtime = $homework->date + $homework->date + ( $homework->duration * 60);
	$homework->answer_date = unixToDate($homework->answer_date);
	$homework->points = $homework->points;
	$homework->selectMarkOn = $homework->marks == 1 ? 'checked="checked"' : '';
	$homework->selectMarkOff = $homework->marks != 1 ? 'checked="checked"' : '';
	$homework->date = unixToDate($homework->date);
	$homework->time = unixToTime($homework->time);
	$answerable = in_array($_SESSION['group'], array("student", "parent")) ? true : false;
	
	if($answerable){
		$answer = do_query("SELECT * FROM homeworks_answer WHERE std_id=".$_SESSION['std_id'] ." AND homework_id=$homework_id", LMS_Database);
		if($answer['id'] != ''){
			$seek_answer = true;
		} else {
			$seek_answer = false;
		}
	}
	if($homework->exercise_id != ''){
		$exrecises = strpos($homework->exercise_id, ',') !==false ? explode(',', $homework->exercise_id) : array($homework->exercise_id);
		if(count($exercises)== 1 && $exercise[0] != ''){
			$exercise_id = $exercise[0];
			include("exercises.php");
			$homework->exercice_html = $exercise_html;
		} else{
			$exercise_list = array();
			foreach($exercises as $ex_id){
				if($ex_id != ''){
					$exercise_list['id'] = $ex_id;
				}
			}
			$homework->exercice_html = filleMergedTemplate("$thisTemplatePath/exercises_list.tpl", $exercise_list);
		}
	} else {
		if($editable){
			$homework->exercice_html = write_html('textarea', 'class="tinymce" name="homework" style="width:100%; height:200px"', $homework->content);
		} else {
			$homework->exercice_html = write_html('fieldset', 'class="ui-state-highlight"', 
				write_html('legend', '', $lang['question']).
				$homework->content
			);
			if($answerable){
				$homework->exercice_html .= write_html('fieldset', '', 
					write_html('legend', '', $lang['answer']).
					write_html('textarea', 'class="tinymce" name="answer" style="width:100%; height:200px"', ($seek_answer ? $answer['answer'] : ''))
				);
			}
		}
	}

}elseif(isset($_GET['lesson_id'])){ // new homework
	$lesson_id = safeGet($_GET['lesson_id']);
	$lesson = do_query_obj("SELECT services FROM schedules_lessons WHERE id=$lesson_id", DB_year);
	$write_privilege = services::check_user_service_privilege($lesson->services); // TO be chasnged with: lesson::check_user_lesson_provilege
	if($write_privilege){
		$service_id = $lesson->services;
		$homework = new stdClass();
		$homework->service_id = $service_id;
		$homework->lesson_id = $lesson_id;	
		$homework->id = "new";
		$homework->homeworkInpValue = '';
		$attach_arr =array();
		$homework->attachements_value = '';
		$homework->date = unixToDate(mktime(0,0,0, date('m'), date('d'), date('Y')));
		$homework->time = '';
		$homework->duration = '';
		$homework->answer_date = mktime(0,0,0, date('m'), date('d'), date('Y'));
		$homework->points = 0;
		$homework->selectMarkOff = 'checked="checked"';
		$homework->attachements_list = '';
		$exercise_id = '';

		$exer_opt = new stdClass();
		$exer_opt->service_id = $homework->service_id;
		$exercise = new Exercises($exer_opt);
		
		
		//$homework->exercice_html = $exercise->loadSearchView();
		//include('exercises_edit.php');
		$homework->exercice_html = write_html('div', 'class="tabs"',
			write_html('ul', '', 
				write_html('li', '', write_html('a', 'href="#write_tab"', $lang['homework'])).
				write_html('li', '', write_html('a', 'href="#exercice_tab"', $lang['exercise']))
			).
			write_html('div', 'id="write_tab"',
				write_html('textarea', 'class="tinymce" name="homework" style="width:100%; height:200px"', '')
			).
			write_html('div', 'id="exercice_tab"',
				$exercise->loadSearchView()
			)
		);
	} else {
		echo write_error($lang['no_privilege']);
		exit;
	}
}


echo fillTemplate("$thisTemplatePath/homework.tpl", $homework);

?>