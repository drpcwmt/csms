<?php
session_start();

header("Content-type: application/json");


	chdir('../');
	
	require_once('scripts/functions.php');
	require_once('config/mysql_conx.php');
	require_once('config/config.php');
	require_once('scripts/mysql_pdo.php');
	require_once('scripts/common_functions.php');
	require_once('config/config_special.php');
	require_once('config/init.php');
	
	/*$config['MS_codeName'] = 'sms';
	$config['MSEXT_msg'] = MSEXT_msg == true ? 1 : 0;
	$config['MSEXT_lms'] = MSEXT_lms == true ? 1 : 0;
	$config['MSEXT_docs'] = MSEXT_docs == true ? 1 : 0;
	
	$config['debugMode'] = $MS_settings['debug_mode'];
	$config['maxUpload'] = max_size_upload;
	$config['maxExec'] = ini_get('max_execution_time');
	$config['uilang'] = (isset($_SESSION['lang']) ? $_SESSION['lang'] : $MS_settings['default_lang']);
	if(MSEXT_msg ){
		$config['MSEXT_msg']=1;
	}*/
	echo json_encode($this_system->loadJsonSettings());

?>