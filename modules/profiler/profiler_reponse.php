<?php
## Profiler ##
require_once('scripts/sms_functions.php');
require_once('scripts/common_functions.php');
$_SESSION['lang'] = $_GET['lang'];
include('lang/'.$_SESSION['lang'].'.php');

$years = getYearsArray();
define('DB_year', Db_prefix.$years[0]);
$_SESSION['ui-lang'] = $_GET['lang'];
$_SESSION['group'] = 'profiler';
$_SESSION['dirc'] = $lang == 'ar' ? 'rtl' : 'ltr';

$std_id = $_GET['id'];

if($MS_settings['read_from_out_side'] == 1){
	$editable = $MS_settings['edit_from_out_side'] ? 1 :  0 ;
	include('modules/students/student_infos.php');
	echo $student_infos;
} else {
	echo write_error('No enough privileges');
}
?>