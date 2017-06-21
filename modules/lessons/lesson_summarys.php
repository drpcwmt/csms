<?php
## lesson Summarys

$sum_arr = array();
$summarys_layout = '';

$select = array("summarys.*", "books.title AS book_name", "chapters.title AS chapter_name");
$tables = array('summarys',"lessons_summary", "books", "chapters");
$where = array(
	"summarys.book_id=books.id",
	"summarys.chapter_id=chapters.id",
	"lessons_summary.summary_id=summarys.id",
	"lessons_summary.lesson_id=$lesson_id"
);

$summarys_query = createQuery( $select, $tables, $where, 'summarys.id ASC');

$summarys= do_query_array($summarys_query, LMS_Database);

if(count($summarys) > 0){
	$lessons_summarys_list = '';
	$summary_trs = array();
	foreach ($summarys as $summary) {
		if($editable){
			$summary->dettach_but = write_html('td', 'width="28" valign="top"',
				write_html('button', 'title="'.$lang['remove'].'" action="dettachSummary" summaryid="'.$summary->id.'" lesson_id="'.$lesson_id.'" class="ui-state-default hoverable"',
					write_icon("circle-close")
				)
			);
		}
		$lessons_summarys_list .= fillTemplate("$thisTemplatePath/lesson_summarys.tpl", $summary);
	}
//	$lessons_summarys_list = filleMergedTemplate("$thisTemplatePath/lesson_summarys.tpl", $summarys);
	$summarys_layout = write_html('table', 'width="100%" class="result fixed"',  $lessons_summarys_list);
}  else{
	$summarys_layout =  write_html('div', 'class="ui-state-highlight ui-corner-all"', $lang['no_summary']);
}

?>