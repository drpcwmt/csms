<?php
## Suupplier main activity

require_once('suppliers.class.php');

if(isset($_GET['newform'])){
	echo Suppliers::newForm();
} elseif(isset($_GET['sup_id'])){
	$supplier = new Suppliers(safeGet($_GET['sup_id']));
	echo $supplier->_toDetails();
} elseif(isset($_GET['save'])){
	echo Suppliers::_save($_POST);
} elseif(isset($_GET['addprod'])){
	echo Suppliers::saveProducts($_POST);
} elseif(isset($_GET['delete_prod'])){
	echo Suppliers::deleteProducts($_POST);
} elseif(isset($_GET['autocomplete'])){
	$value = safeGet($_GET['term']);
	echo Suppliers::getAutocomplete($value); // json out
} else {
	echo Suppliers::loadMainLayout();
} 

?>