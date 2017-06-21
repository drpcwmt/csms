<?php
## CLients

if(isset($_GET['cc'])){
	echo Clients::loadCostCenter(safeGet($_GET['cc']));
}elseif(isset($_GET['others'])){
	echo Clients::loadOthers();
} else {
	echo clients::loadMainLayout();
}
?>