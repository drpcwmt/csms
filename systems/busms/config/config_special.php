<?php

##config Scpecial
# special configuration for each system

define('MS_codeName', 'busms');

define('Ms_systemname', 'Bus Management System');

define('MySql_Database', 'csms_busms');

define('MapsApiKey', 'AIzaSyBj9ZkxtDs5emqu0CEpiniJELLZA1FXrXk');

define('DriversJobCode', 7);

define('MatronsJobCode', 2);

$this_system = new BusMs();
$busms = $this_system;
$accms = $this_system->getAccms();
$hrms = $this_system->getHrms();

?>