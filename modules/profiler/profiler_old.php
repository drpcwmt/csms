<?php
## SMS 
## profiler

$userid = $_GET['userid'];
$group = $_GET['group'];
$lang = $_SESSION['lang'];
$stamp = mktime(date('H'), date('i'), 0, date('n'), date('j'), date('Y'));
$auth = md5(MySql_Password.$stamp);

if(in_array($group, array('prof', 'superadmin', 'admin', 'principal', 'supervisor'))){
	echo file_get_contents("http://".$MS_settings['hrms_server_name']."/blocks/profiler.php?emp_id=$userid&lang=$lang&auth=$auth");
} elseif($group == 'student'){
	
} elseif($group == 'parent'){
	
}
?>