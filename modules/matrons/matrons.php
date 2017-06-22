<?php

// Matronn Drtails



if(isset($_GET['hrms_id'])){
	$hrms = new HrMS(safeGet('hrms_id'));
}



if(isset($_GET['del_matron'])){

	echo Matrons::delMatron($_POST);



} elseif(isset($_GET['matron_id'])){

	$matron = new Matrons(safeGet($_GET['matron_id']));

	echo $matron->loadLayout();


} elseif(isset($_GET['matrons_autocomplete'])){
	$value = trim($_GET['term']);
	print Matrons::getAutocompleteMatron( $value);


} elseif(isset($_GET['add_matron'])) {

	echo Matrons::addMatron($_POST);

} elseif(isset($_GET['import'])){
	if(isset($_GET['form'])){
		echo Matrons::importForm();
	} else {
		echo Matrons::importMatrons($_POST['hrms_id']);
	}
	
} else {

	echo Matrons::loadMainLayout();



}



?>