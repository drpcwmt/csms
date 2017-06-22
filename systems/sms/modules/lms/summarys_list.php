<?php
## Summary list
require_once('scripts/services_functions.php');

if(isset($_GET['chapter_id'])){
	$chapter_id = safeGet($_GET['chapter_id']);	
}

$select = array("summarys.*", "books.title AS book_name", "chapters.title AS chapter_name");
$tables = array('summarys', "books", "chapters");
$where = array(
	"summarys.book_id=books.id",
	"summarys.chapter_id=chapters.id"
);

if(isset($_GET['chapter_id'])){
	$where[] = "summarys.chapter_id=$chapter_id";
}
	// Summarys
$summarys_layout = '';
$summarys_query = createQuery( $select, $tables, $where, 'summarys.id ASC');

$summarys= do_query_array($summarys_query, LMS_Database);
if(count($summarys) > 0){
	$lessons_summarys_list = '';
	$summary_trs = array();
	foreach ($summarys as $summary) {
		$lessons_summarys_list .= fillTemplate("$thisTemplatePath/summary_list.tpl", $summary);
	}
	$summarys_layout = write_html('table', 'width="100%" class="result fixed"',  $lessons_summarys_list);
} 
echo $summarys_layout;
?>
