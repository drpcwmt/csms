<?php
## SMS 
## profiler
$stamp = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
$auth = md5('m.dnlhjslnsdn kns/'.$stamp);
$this_server_name = $MS_settings['sch_code'];

if(isset($_GET['q'])){ // Search
	$id = $_GET['id'];
	$con = $_GET['q'];
	$lang = $_SESSION['lang'];
	if($con == 'hrms'){
		$pre_url = $MS_settings['hrms_server_name'];
		$link = "http://$pre_url/index.php?module=profiler&r&id=$id";
	} elseif($con == 'libms'){
		$pre_url = $MS_settings['libms_server_name'];
		$link = "http://$pre_url/index.php?module=profiler&r&server=$this_server_name&type=std&id=$id";
	} elseif($con == 'busms'){
		$pre_url = $MS_settings['busms_server_name'];
		$link = "http://$pre_url/index.php?module=profiler?r&server=$this_server_name&type=std&id=$id";
	}

	$profil_out =  file_get_contents($link."&lang=$lang&auth=$auth");
	echo $profil_out;
	exit;
}

if(isset($_GET['r'])){
	if($_GET['auth'] == $auth){
		include('profiler_reponse.php');
	} else {
		echo write_error('No enough privileges');
	}
	exit;
}

?>