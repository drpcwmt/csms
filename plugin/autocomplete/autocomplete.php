<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: application/json");

$db = $_GET['db'];
$table = $_GET['t'];
$feild = $_GET['f'];
$value = $_GET['term'];
$where = $_GET['w'];

$num = 0; 

$sql = "SELECT DISTINCT $feild FROM $table WHERE ($where LIKE '$value%' OR $where LIKE '".strtolower($value)."%' OR $where LIKE '".ucfirst($value)."%')";


$query = do_query_resource( $sql, $db);
$t = mysql_num_rows($query);
if($t>0){
	$arr = array();
	while($obj = mysql_fetch_assoc($query)){
		$arr[$num] = $obj;
		$num++;
	}
	print json_encode($arr);
} 
?>