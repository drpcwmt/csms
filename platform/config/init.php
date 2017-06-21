<?php
## initiate

// Debug mode
//if(!isset($_SESSION['group']) || ($_SESSION['group'] == 'superadmin' && $this_system->getSettings('debug_mode') == 1)){
if($this_system->getSettings('debug_mode') == '1'){
	error_reporting(E_ALL);
	ini_set("display_errors", 1); 
} else {
//	error_reporting(0);
	ini_set("display_errors", 0);
}

// check login
$loged = Login::isLogged();
if($loged){
	$prvlg = new Privileges();
}

// System user ( depricated )
$local = array("::1", "127.0.0.1");
$user_system = in_array($_SERVER['REMOTE_ADDR'], $local) && !isset($_SERVER['HTTP_USER_AGENT']);

//Settings array( depricated )
$MS_settings= array();
$rows = do_query_array("SELECT * FROM settings");

foreach($rows as $row){
	$MS_settings[$row->key_name] = $row->value;
}




// SEt Local direction and lang
switch($this_system->getSettings('default_lang')){
	case 'ar':
		setlocale(LC_ALL, 'ar.UTF-8');
	break;
	case 'fr':
		setlocale(LC_ALL, 'fr.UTF-8');
	break;
	case 'en':
		setlocale(LC_ALL, 'en.UTF-8');
	break;
	case 'de':
		setlocale(LC_ALL, 'de.UTF-8');
	break;
}
// window direction
define('MS_doc_direction',  isset($_SESSION["dirc"]) ? $_SESSION["dirc"] : ($this_system->getSettings('default_lang') == "ar" ? 'rtl' :'ltr'));

require_once('lang/'.(isset($_SESSION['lang']) ? $_SESSION['lang'] : $this_system->getSettings('default_lang')).'.php');

$days_name_arr = array(
	'1' => $lang['week_sun'],
	'2' => $lang['week_mon'], 
	'3' => $lang['week_tue'], 
	'4' => $lang['week_wed'], 
	'5' => $lang['week_thu'], 
	'6' => $lang['week_fri'], 
	'7' => $lang['week_sat']
);


// Themes
define('MS_theme', isset($_SESSION["css"]) ? $_SESSION["css"] : 'default');


// Uplaod settings
$config_max = $normalize($this_system->getSettings('upload_max_filesize'));
$max_upload = $normalize(ini_get('upload_max_filesize'));
if($max_upload < $config_max){
	@ini_set('upload_max_filesize', $this_system->getSettings('upload_max_filesize'));
	$max_upload = $normalize(ini_get('upload_max_filesize'));
}
$max_post = $normalize(ini_get('post_max_size'));
if($max_post < $config_max){
	@ini_set('post_max_size', $this_system->getSettings('upload_max_filesize'));
	$max_post = $normalize(ini_get('post_max_size'));
}
$memory_limit = $normalize(ini_get('memory_limit'));
define("max_size_upload", min($max_upload, $max_post, $memory_limit, $config_max));





	// Secure requests
//saferRequests();


?>
