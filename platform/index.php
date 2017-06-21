<?php
## SMS
## index file
//ob_start();
session_start();
//$begin = time(); ///



define('docRoot', dirname(__file__).'/');
	error_reporting(E_ALL);
	ini_set("display_errors", 1); 

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once('scripts/functions.php');
require_once('config/mysql_conx.php'); // to be removed
require_once('config/config.php');
require_once('scripts/mysql.php'); // to be removed
require_once('scripts/common_functions.php');
require_once('scripts/mysql_pdo.php');


if(isset($_GET['ping'])){
	setJsonHeader();
	echo json_encode_result(array('last_sync'=>time()));
	exit;
}

require_once('config/config_special.php');
require_once('config/init.php');

require_once('scripts/html.php');
require_once('scripts/I18N/Arabic.php');
require_once('scripts/layout.class.php');



	// Let plugin Chose by itself if it need login or not
if(isset($_GET['plugin'])){ // Execute plugins
	$plugin = $_GET['plugin'];
	if(file_exists("plugin/$plugin/$plugin.php")){
		include("plugin/$plugin/$plugin.php");
	} else {
		echo write_error($lang['cant_find_plugin']);
	}
	exit;
}

	// check requested page
$requestMain = isset($_GET['module']) || isset($_GET['common']) || isset($_GET['plugin']) ? false : true;
	// check system state
if($loged && $MS_settings['system_stat'] != 1 && $_SESSION['group'] != 'superadmin' ){
	if($requestMain){
		$error = $lang['error_login_3']; // timeout
		include('modules/login/logout.php');
	} else {
		echo 'session timeout';
	}
	exit;
}
	// Process requests if login
if($loged){
	$_SESSION['last_op'] = time();

	if(isset($_GET['module'])){ // Execute modules
		$module_name = $_GET['module'];
		if(file_exists("modules/$module_name/$module_name.php")){
			include("modules/$module_name/$module_name.php");
		} else {
			echo write_error($lang['cant_find_module']);
		}
	} elseif(isset($_GET['common'])){ // Execute common
		$module_name = $_GET['common'];
		if(file_exists("common/$module_name.php")){
			include("common/$module_name.php");
		} else {
			echo write_error($lang['cant_find_script']);
		}
	} else {	// Display Main Interface
		include('ui/main.php');
	}
} else {
	if(isset($_GET['module']) && $_GET['module'] == 'login'){
		include('modules/login/login.php');
	} else { 
		if( $requestMain == false){ //=> requesting module or doesnt request login validation
			echo 'session timeout';
		} else {
			include('modules/login/login.php');
		}
	}
}
?>