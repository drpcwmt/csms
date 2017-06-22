<?php

##config Scpecial
# special configuration for each system

define('MS_codeName', 'storems');

define('Ms_systemname', 'Stores Management System');

define( 'MySql_Database' , 'csms_storems');


// THIS SYSTEM
$this_system = new StoreMS();
$accms = $this_system->getAccms();
$hrms = $this_system->getHrms();

?>