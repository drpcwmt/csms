<?php
## ToDo Activity
require_once('todo.class.php');

$widget = write_html('fieldset', '', 
	write_html('legend', '', $lang['todo']).
	ToDo::getListTable(0, 10)
);

?>