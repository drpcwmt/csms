<?php

##config Scpecial
# special configuration for each system

define('MS_codeName', 'safems');

define('Ms_systemname', 'Safes Management System');

define('MySql_Database' , 'csms_safems');

// THIS SYSTEM
$this_system = new SafeMS();
$safems= $this_system;
$hrms = $this_system->getHrms();
$accms = $this_system->getAccms();
?>