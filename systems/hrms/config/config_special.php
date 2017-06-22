<?php

##config Scpecial
# special configuration for each system

define('MS_codeName', 'hrms');

define('Ms_systemname', 'Human Resources Management System');

define('MySql_Database', 'csms_hrms');

$this_system = new HrMS();
$hrms = $this_system;
$accms = $this_system->getAccms();
?>