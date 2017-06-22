<?php

//$cur_class = getProfCurClass($_SESSION['user_id']);
$patterns = do_query_resource( "SELECT DISTINCT pattern FROM behavior", $sms->db_year);
if(isset($_SESSION['cur_con'])){
	if($_SESSION['cur_con'] == 'class'){
		$students = getStudentIdsByClass($_SESSION['cur_class']);
	} elseif($_SESSION['cur_con'] == 'group'){
		$students = getStudentIdsByGroup($_SESSION['cur_class']);
	}
}

$cur_date = (isset($_GET['date'])) ? dateToUnix($_GET['date']) :  mktime(0,0,0,date('m'),date('d'), date('Y'));

// add behavior
if(isset($_POST['stdIds']) && $_POST['stdIds'] !=''){
	$fields = getTableFields($database_year, 'behavior');
	$ids = explode(',', $_POST['stdIds']);
	foreach($ids as $id){
		$affect_fields = array();
		$affect_data_value = array();
		foreach($_POST as $key => $value){
			if(in_array($key, $fields)){
				$affect_fields[] = $key;
				if($key == 'date'){
					$affect_data_value[] = GetSQLValueString(dateToUnix($value), "int");
					
				} else {
					$affect_data_value[] = GetSQLValueString($value, "text");
				}
			}
		}
		$sql = "INSERT INTO behavior (std_id, user_id, ".implode($affect_fields, ',').") VALUES (".$id.", ".$_SESSION['user_id'].", ". implode($affect_data_value, ',').")";
		//echo $sql;
		do_query_edit ( $sql, $sms->db_year);
	}
	echo 1;
	exit;
}
	
// delete absents
if(isset($_GET['delbehavior'])){
	if(do_query_edit( "DELETE FROM behavior WHERE id=".$_GET['delbehavior'], $sms->db_year)){
		 echo 1;
	} else {
		echo 'Error';
	}
	exit;
}

if(isset($_GET['addform'])){
	echo Behavior::loadAddForm();
} else {
	echo Behavior::loadMainLayout();
}

?>