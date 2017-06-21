<?php
## Cost Centers 

if(isset($_GET['opencc'])){
	$cc = new Costcenters(safeGet($_GET['opencc']));
	echo $cc->loadLayout();
	
} elseif(isset($_GET['newcc'])){
	echo Costcenters::LoadNewForm();
		
} elseif(isset($_GET['savecc'])){
	echo Costcenters::_save($_POST);
	
} elseif(isset($_GET['opengroup'])){
	$cc = new CostcentersGroup(safeGet($_GET['opengroup']));
	echo $cc->loadLayout();
	
} elseif(isset($_GET['newgroup'])){
	echo CostcentersGroup::LoadNewForm();
		
} elseif(isset($_GET['savegroup'])){
	echo CostcentersGroup::_save($_POST);
} elseif(isset($_GET['incomereport'])){
	$cc = new CostCenters($_GET['incomereport']);
	echo $cc->getIncomeReport();
	
} elseif(isset($_GET['sync'])){
	echo Costcenters::syncServer($_POST['id']);
} else {
	echo Costcenters::loadMainLayout();
}
?>