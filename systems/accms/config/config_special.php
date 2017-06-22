<?php

##config Scpecial
# special configuration for each system

define('MS_codeName', 'accms');

define('Ms_systemname', 'Accounting Management System');

define('MySql_Database', 'csms_accms');



	// CHeck Server Connection

// ACC
define("MSSER_accms", true);

$this_system = new AccMS();
$accms = $this_system;
$hrms = $this_system->getHrms();
?>