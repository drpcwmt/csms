<?php
##config Scpecial
# special configuration for each system

define('MS_codeName', 'sms_pro');

define('Ms_systemname', 'Students Management System');

//define( 'Db_prefix', "csms_sms_");
if(isset($_SESSION['year'])){
	define( 'DB_year', Db_prefix.$_SESSION['year']);
}

define( 'DB_student' , 'csms_sms');

define( 'MySql_Database' , 'csms_sms');

require_once('scripts/sms_functions.php');

//define( 'MySql_Database' , 'csms_sms');

$this_system = new SMS();
$sms = $this_system;
$hrms = $this_system->getHrms();
$accms = $this_system->getAccms();
// Messages extension
if(MS_codeName == 'sms_pro'){
	if(file_exists('modules/messages/messages.php')){
		define("MSEXT_msg", true);
	} else {
		define("MSEXT_msg", false);
	}

	// Leasrning system extension
	if(file_exists('modules/lms/lms.php')){
		define("MSEXT_lms", true);
	} else{
		define("MSEXT_lms", false);
	}
	
	// Documents
	if(file_exists('modules/documents/documents.php') && $this_system->getSettings('docs') == '1' ){
		define("MSEXT_docs", true);
	} else{
		define("MSEXT_docs", false);
	}

	// Tablettes
	if(file_exists('plugin/m/m.php')){
		define("MSPLUG_m", true);
	} else{
		define("MSPLUG_m", false);
	}
} else {
	define("MSEXT_msg", false);
	define("MSEXT_lms", false);
	define("MSEXT_docs", false);
	define("MSPLUG_m", false);
}

//define("MSEXT_lms", (MS_codeName != 'sms_basic' ? true : false));

// BUS
if($this_system->getSettings('busms_server') == 1){
	define("MSSER_busms", true);
} else{
	define("MSSER_busms", false);
}

// Librarys
if($this_system->getSettings('libms_server') == 1 ){
	define("MSSER_libms", true);
} else {
	define("MSSER_libms", false);
}

// Accounting
if($this_system->getSettings('accms_server') == 1 ){
	define('MSEXT_acc', true);
} else {
	define ("MSEXT_acc", false);
}

// Safe
if($this_system->getSettings('safems_server') == 1 && file_exists('modules/fees/fees.php')){
	define("MSSER_safems", true);
} else{
	define("MSSER_safems", false);
}

if($sms->getSettings('ig_mode') == '1'){
	include('ig_config.php');
}

//define("MSSER_accms", $MSSER_accms);

// THIS SYSTEM

?>