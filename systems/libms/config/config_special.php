<?php

##config Scpecial
# special configuration for each system

define('MS_codeName', 'libms');

define('Ms_systemname', 'Librarys Management System');

define('MySql_Database', 'csms_libms');


// include the default system Class
//require_once(MS_codeName.'.class.php');



	// CHeck Server Connection
// HR
// ACC
define("MSSER_accms", true);


$this_system = new LibMS();
$libms = $this_system;
$hrms = $this_system->getHrms();
$safems = $this_system->getSafems();
?>