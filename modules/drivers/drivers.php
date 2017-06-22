<?php

if(isset($_GET['hrms_id'])){
	$hrms = new HrMS(safeGet('hrms_id'));
}


if(isset($_GET['add_driver'])){
	echo Drivers::addDriver($_POST);

} elseif(isset($_GET['save'])){
	echo Drivers::saveDriver($_POST);

} elseif(isset($_GET['del_driver'])){
	echo Drivers::delDriver($_POST);

} elseif(isset($_GET['import'])){
	if(isset($_GET['form'])){
		echo Drivers::importForm();
	} else {
		echo Drivers::importDrivers($_POST['hrms_id']);
	}

} elseif(isset($_GET['drivers_autocomplete'])){
	$value = trim($_GET['term']);
	print Drivers::getAutocompleteDriver( $value);


} elseif(isset($_GET['driver_id'])){
	$driver = new Drivers(safeGet($_GET['driver_id']), $hrms);
	echo $driver->loadLayout();

} else {
	echo Drivers::loadMainLayout();

}

?>