<?php
## Commands main activity
require_once('buys.class.php');

if(isset($_GET['newbuy'])){
	$sup_id = isset($_GET['sup_id']) ? safeGet($_GET['sup_id']) : false;
	$to = isset($_GET['to']) ? safeGet($_GET['to']) : (isset($_SESSION['cur_con']) ? $_SESSION['cur_con'] :false);
	$to_id =isset($_GET['to_id']) ? safeGet($_GET['to_id']) : (isset($_SESSION['cur_con_id']) ? $_SESSION['cur_con_id'] :false);
	echo Buys::newBuy($sup_id, $to, $to_id);

}elseif(isset($_GET['buy_id'])){
	$buy = new Buys(safeGet($_GET['buy_id']));
	echo $buy->_toDetails();
	
} elseif(isset($_GET['save'])){
	echo Buys::_save($_POST);
} elseif(isset($_GET['search_form'])){
	echo Buys::_searchForm();
} elseif(isset($_GET['search'])){
	$buys = Buys::searchCommands($_POST);
	echo Buys::_toList($buys);
}


?>