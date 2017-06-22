<?php
## services deltails homeworks
require_once('scripts/services_functions.php');

$notes= do_query_array("SELECT * FROM schedules_notes WHERE lesson_id=$lesson_id", DB_year);

if(count($notes) > 0){
	$lessons_notes_list = '';
	$notes_trs = array();
	foreach ($notes as $note) {
		$note->shared_attr  = ($note->shared == '1') ? 'shared="1"' : '';
		$lessons_notes_list .= fillTemplate("$thisTemplatePath/notes_list.tpl", $note);
	}
	$list = $lessons_notes_list;
}  else{
	$list = $lang['no_notes'];
}

$notes_layout = write_html('table', 'width="100%" class="result fixed notes_list"', $list);

?>