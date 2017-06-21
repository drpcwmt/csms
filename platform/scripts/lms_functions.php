<?php
## LMS functions


function buildSummaryCombobox($cat , $parent_id, $selected, $backend=false){
	global $lang;
	switch($cat){
		case 'services':
			$sql = "SELECT id, title FROM services ";
			$def_event = 'reloadBooks(this.value)';
			$field = "service_id";
			$label = $lang['material'];
			$new_label = '';
		break;
		case 'books':
			$sql = "SELECT id, title FROM books WHERE service_id=$parent_id";
			$def_event = 'reloadChapters(this.value)';
			$field = "book_id";
			$label = $lang['book'];
			$new_label = $lang['new_book'];
		break;
		case 'chapters':
			$sql = "SELECT id, title FROM chapters WHERE book_id=$parent_id";
			$def_event = 'reloadSummarys(this.value)';
			$field = "chapter_id";
			$label = $lang['chapter'];
			$new_label = $lang['new_chapter'];
		break;
		case 'summarys':
			$sql = "SELECT id, title FROM summarys WHERE chapter_id=$parent_id";
			$def_event = '';
			$field = "summary_id";
			$label = $lang['summary'];
			$new_label = $lang['new_summary'];
		break;
	}
	
	if($parent_id != false){
		$options = do_query_resource($sql, LMS_Database);
		if(mysql_num_rows($options) > 0) {
			$out = '<span id="'.$field.'_span"> 
			<select name="'.$field.'" id="select_'.$field.'" class="ui-state-default">
				<option></option>';
			while($option = mysql_fetch_assoc($options)){
				$out .= '<option value="'.$option['id'].'" onclick="'.(!$backend ? $def_event : $backend).'"  '.($selected!=false ? ($selected==$option['id'] ? 'selected="selected"':'') : '').'>'.$option['title'].'</option>';
			}
			$out .= '<option onclick="new'.$field.'()">'.$lang['new'].'... </option>
			</select></span>';
		}
	} 
	
	if(!isset($out)) {
		$out = '<span id="new_'.$field.'_span">'.
			'<input id="new_'.$field.'_inp" type="text" onfocus="cleartNewSelect(this)" /> '.
			write_html('button', 'class="ui-corner-all hoverable ui-state-default" style="padding:1px" onclick="submitNew'.$field.'()"', write_icon('check'));
	}
	
	return $out;
}

function getBookNameById($book_id){
	if($book_id != ''){
		$book = do_query("SELECT title FROM books WHERE id=$book_id", LMS_Database);
		if($book != false && $book['title']!=''){
			return $book['title'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function getChapterNameById($chapter_id){
	if($chapter_id != ''){
		$chapter = do_query("SELECT title FROM chapters WHERE id=$chapter_id", LMS_Database);
		if($chapter != false && $chapter['title']!=''){
			return $chapter['title'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function getConFromHomework($lesson_id){
	$lesson = do_query("SELECT schedules_date.* FROM schedules_date,schedules_lessons WHERE 
		schedules_lessons.id=$lesson_id
		AND schedules_lessons.rec_id=schedules_date.id", DB_year);
	
	return array('con'=>$lesson['con'], 'con_id'=>$lesson['con_id']);
}

function getServiceBooks($service_id){
	$out = array();
	if($service_id != false && $service_id != ''){
		$books= do_query_array("SELECT id, title FROM books WHERE service_id=$service_id", LMS_Database);
		foreach($books as $book){
			$out[$book->id] = $book->title;
		}
		return $out;
	} else return false;
}

/*function getBooksByService($service_id){
	if($service_id != false && $service_id != ''){
		$out = array();
		$books = do_query_resource("SELECT id, title FROM books WHERE service_id=$service_id", LMS_Database);
		while($book = mysql_fetch_assoc($books)){
			$out[$book['id']] = $book['title'];
		}
		return $out;
	} else {
		return false;
	}
}*/

function getBookChapers($book_id){
	if($book_id != false && $book_id != ''){
		$out = array();
		$chapters = do_query_resource("SELECT id, title FROM chapters WHERE book_id=$book_id", LMS_Database);
		while($chapter = mysql_fetch_assoc($chapters)){
			$out[$chapter['id']] = $chapter['title'];
		}
		return $out;
	} else {
		return false;
	}
}

function getChapterSummarys($chapter_id){
	if($chapter_id != false && $chapter_id != ''){
		$out = array();
		$summarys = do_query_resource("SELECT id, title FROM summarys WHERE chapter_id=$chapter_id", LMS_Database);
		while($summary = mysql_fetch_assoc($summarys)){
			$out[$summary['id']] = $summary['title'];
		}
		return $out;
	} else {
		return false;
	}
}

	
	// lesson summary list
function loadSummarysList($lesson_id, $editable=false){
	global $lang;
	$sum_arr = array();
	$summary_html = '';
	$lesson_sums = "SELECT summarys.* FROM summarys, lessons_summary 
		WHERE lessons_summary.summary_id=summarys.id 
		AND lessons_summary.lesson_id=$lesson_id";
	$summarys= do_query_array($lesson_sums, LMS_Database);
	if(count($summarys) > 0){
		$lessons_summarys_list = '';
		$summary_trs = array();
		foreach ($summarys as $summary) {
			$lessons_summarys_list .= fillTemplate("modules/lms/templates/summary_list.tpl", $summary);
		}
		$summarys_layout = $lessons_summarys_list;
	}  else{
		$summarys_layout = $lang['no_summary'];
	}
	return write_html('table', 'width="100%" class="result fixed"', $summarys_layout);

}

function loadHomeworkList($lesson_id, $editable=false){
	global $lang;
	if($lesson_id != ''){
		$homeworks = do_query_array("SELECT * FROM homeworks WHERE lesson_id=$lesson_id", LMS_Database);
		if(count($homeworks) > 0){
			$lessons_homeworks_list = '';
			foreach ($homeworks as $homework) {
				$lessons_homeworks_list .= fillTemplate("modules/lms/templates/homeworks_list.tpl", $homework);
			}
			$homeworks_layout = $lessons_homeworks_list;
		} else {
			$homeworks_layout = $lang['no_homework_found'];
		}
	}
	return write_html('table', 'width="100%" class="result fixed homework_list"', $homeworks_layout);
}

?>