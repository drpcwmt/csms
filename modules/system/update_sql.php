<?php
## Update SQL

foreach($_POST['statments'] as $statment){
	$e = explode('/', $statment);
	$database = $e[0];
	$sql = str_replace($e[0].'/', '', $statment);
	$error = 0;
	echo "Executing :".$sql."......";
	if(do_query_edit($sql, $database)){
		echo $sql.' -> OK<br>';
	} else {
		$error++;
		echo 'Failed<br>';
	}
}

if($error > 0){
	echo '<h2>Update Failed</h2>';
} else {
	echo '<h2>Update Done</h2>
	<script type="text/javascript">window.parent.location.reload()</script>';	
}

?>