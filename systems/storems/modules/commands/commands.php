<?php
## Commands main activity
require_once('commands.class.php');
if(isset($_GET['newform'])){
	echo Commands::newForm();
} elseif(isset($_GET['newpayement'])){
	$layout = new stdClass();
	$layout->date = unixToDate(time());
	echo fillTemplate("modules/commands/templates/payments.tpl", $layout);
}elseif(isset($_GET['com_id'])){
	$command = new Commands(safeGet($_GET['com_id']));
	echo $command->_toDetails();
} elseif(isset($_GET['save'])){
	echo Commands::_save($_POST);
} elseif(isset($_GET['search_form'])){
	echo Commands::_searchForm();
} elseif(isset($_GET['search'])){
	$commands = Commands::searchCommands($_POST);
	echo Commands::_toList($commands);
} elseif(isset($_GET['savepayment'])){
	echo Commands::savePayment($_POST);
}  else {
	echo Commands::newForm();
}

?>