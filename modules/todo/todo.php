<?php
## ToDo Activity
require_once('todo.class.php');

if(isset($_GET['id'])){
	$todo = new ToDo(safeGet($_GET['id']));
	echo $todo->read();
} elseif(isset($_GET['list'])){
	echo ToDo::getListTable();
}

?>