<?php
## Service images list
session_start();
	chdir('../../');
	
	require_once('scripts/functions.php');
	require_once('config/mysql_conx.php');
	require_once('config/config.php');
	require_once('scripts/mysql_pdo.php');
	require_once('scripts/common_functions.php');
	require_once('scripts/files_functions.php');
	require_once('config/config_special.php');
	require_once('config/init.php');

if(isset($_GET['service_id'])){
	$service_id = $_GET['service_id'];
	$image_dir = "attachs/services/$service_id";
} else {
	$user = new Users($_SESSION['group'], $_SESSION['user_id']);
	$image_dir = $user->doc_path;		
}

$output = array();
if(is_dir($image_dir)){
	$files = scanRecursive($image_dir);
	foreach($files as $file){
		if(is_file($file) && filesize($file)>0 && getimagesize($file) != FALSE){
			$output[] = array(
				'title' => utf8_encode(str_replace($image_dir.'/', '',$file)),
				'value' => utf8_encode('../'.$file)
			);
		}
	}
}
setJsonHeader();
echo json_encode($output);

?>