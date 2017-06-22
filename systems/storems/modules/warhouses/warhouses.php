<?php
## Warhouses main activity
require_once('warhouses.class.php');
//$products =  new Products();

if(isset($_GET['newform'])){
	echo Warhouses::newForm();
}elseif(isset($_GET['war_id'])){
	$war = new Warhouses(safeGet($_GET['war_id']));
	echo $war->laodLayout();
} elseif(isset($_GET['save'])){
	echo Warhouses::_save($_POST);
} else {
	echo Warhouses::loadMainLayout();
} 

?>