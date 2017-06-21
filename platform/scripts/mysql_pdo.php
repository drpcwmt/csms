<?php
## MySql PDO

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
 	$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	if(is_array($theValue)){
		$theValue= implode(',', $theValue);
	}
	$theValue = addslashes($theValue);
	//$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
	switch ($theType) {
		case "text":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		break;    
		case "long":
		case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
		break;
		case "double":
			$theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
		break;
		case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		break;
		case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
		break;
	}
  return $theValue;
}

function createQuery($selects, $tables, $where, $order=false, $limit=false){
	$tables = is_array($tables) ? $tables : array($tables);
	$selects = is_array($selects) ? $selects : array($selects);
	$where = is_array($where) ? $where : array($where);

	$sql = "SELECT ". implode(', ', $selects)." \r\n
	FROM ".implode(', ', $tables)." 
	WHERE ".implode(' AND ', $where).
	($order != false ? " ORDER BY $order " : '').
	($limit != false ? " LIMIT $limit " : '');
	return $sql;
}

function do_query_array($sql, $database='', $server_ip=''){
	global $this_system;
	if($server_ip == ''){
		$server_ip = $this_system->ip;
	}
	if($database == ''){
		$database = $this_system->database;
	}
//	$_SESSION['request']=$_SESSION['request']+1;
	try {
		$link = new PDO('mysql:host='.$server_ip.';dbname='.$database.';charset=utf8',
			MySql_UserName,
			MySql_Password,
			array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT => true
			)
		);
		$handle = $link->prepare($sql);
		$handle->execute();
		$result = $handle->fetchAll(PDO::FETCH_OBJ);
		$link = null;
		return $result;
	} catch(PDOException $e){
		if(isset($this_system) && $this_system->getSettings('debug_mode') == 1){
			echo "<b>$sql</b><br />";
			echo $e->getMessage();
		}
	}
}


function do_query_obj($sql, $database='', $server_ip=''){
//	$_SESSION['request']=$_SESSION['request']+1;
	global $this_system;
	if($server_ip == ''){
		$server_ip = $this_system->ip;
	}
	if($database == ''){
		$database = $this_system->database;
	}
	try {
		$link = new PDO('mysql:host='.$server_ip.';dbname='.$database.';charset=utf8',
			MySql_UserName,
			MySql_Password,
			array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT => true
			)
		);
		$handle = $link->prepare($sql);
		$handle->execute();
		$result = $handle->fetch(PDO::FETCH_OBJ);
		$link = null;
		return $result;
	} catch(PDOException $e){
		if($this_system->getSettings('debug_mode') == 1){
			echo "<b>$sql</b><br />";
			echo $e->getMessage();
		}
	}
}


function do_insert_obj($row, $table, $database='', $server_ip=''){
	global $this_system;
	if($server_ip == ''){
		$server_ip = $this_system->ip;
	}
	if($database == ''){
		$database = $this_system->database;
	}
	try {
		$link = new PDO('mysql:host='.$server_ip.';dbname='.$database.';charset=utf8',
			MySql_UserName,
			MySql_Password,
			array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT => true
			)
		);
		$commom_fields = getTableFields( $table, $database, $server_ip);
		$affect_common_value = array();
		$affect_common_fields = array();
		foreach($row as $key => $value){
			if(in_array($key, $commom_fields)){
				$affect_common_fields[] = "`$key`";
				if(strpos($key, '_date') !== false || $key == 'date'){
					if($value != ''){
						if(strpos($value, '/') !== false ){
							$affect_common_value[] = GetSQLValueString(dateToUnix($value), "int");
						} else {
							$affect_common_value[] = GetSQLValueString($value, "int");
						}
					} else {
						$affect_common_value[] = "''";
					}
				} elseif(strpos($key, '_time') !== false || $key == 'time'){
					if($value != ''){
						if(strpos( $value, ':') !== false ){
							$affect_common_value[] = GetSQLValueString(timeToUnix($value), "int");
						} else {
							$affect_common_value[] = GetSQLValueString($value, "int");
						}
					} else {
						$affect_common_value[] = "";
					}
				} else {
					$affect_common_value[] = GetSQLValueString($value, "text");
				}
			}
		}	
		if(count($affect_common_fields) > 0){
			$sql =  "INSERT INTO $table (".implode($affect_common_fields, ',').") VALUES ( ". implode($affect_common_value, ',').")";
			$result = $link->exec($sql);
			if($result){
				$lastInsertId = $link->lastInsertId();
				$link = null;
				if( $lastInsertId> 0){	
					return  $lastInsertId;
				} else {
					return true;
				}
			} else {
				$link = null;
				return false;
			}
		} else {
			$link = null;
			return true;
		}
	} catch(PDOException $e){
		if($this_system->getSettings('debug_mode') == 1){
			echo "<b>$sql</b><br />";
			echo $e->getMessage();
		}
		$link = null;
		return false;
	}
}

function do_update_obj($values, $where, $table,  $database='', $server_ip=''){
	global $this_system;
	$result = false;
	if($server_ip == ''){
		$server_ip = $this_system->ip;
	}
	if($database == ''){
		$database = $this_system->database;
	}
	try {
		$link = new PDO('mysql:host='.$server_ip.';dbname='.$database.';charset=utf8',
			MySql_UserName,
			MySql_Password,
			array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT => true,
				PDO::MYSQL_ATTR_FOUND_ROWS => true
			)
		);
		$commom_fields = getTableFields( $table, $database, $server_ip);
		$affect_common_fields = array();
		foreach($values as $key => $value){
			if(in_array($key, $commom_fields)){
				if(strpos($key, '_date') !== false){
					if($value != ''){
						$affect_common_fields[] =  "`$key`=".GetSQLValueString(dateToUnix($value), "int");
					}
				} elseif(strpos($key, '_time') !== false){
					if($value != ''){
						$affect_common_fields[] = "`$key`=".GetSQLValueString(timeToUnix($value), "int");
					}
				} else {
					$affect_common_fields[] = "`$key`=".GetSQLValueString($value, "text");
				}
			} 
		}	
		if(count($affect_common_fields) > 0){
			$sql = "UPDATE $table SET ".implode($affect_common_fields, ',')." WHERE ".$where;
			//echo $sql;
			$result = $link->exec($sql);
		} else {
			$result = true;
		}
		if($result != false){
			//$_SESSION['last_sql_modifield_rows'] = $result->rowCount();
			$link=null;
			return true;
		} else {
			$link=null;
			$_SESSION['last_sql_modifield_rows'] = 0;
			return false;
		}
	} catch(PDOException $e){
		if($this_system->getSettings('debug_mode') == 1){
			echo "<b>$sql</b><br />";
			echo $e->getMessage();
		}
		$link = null;
		return false;
	}
}

function do_delete_obj($where, $table, $database='', $server_ip=''){
	global $this_system;
	if($server_ip == ''){
		$server_ip = $this_system->ip;
	}
	if($database == ''){
		$database = $this_system->database;
	}
	try {
		$link = new PDO('mysql:host='.$server_ip.';dbname='.$database.';charset=utf8',
			MySql_UserName,
			MySql_Password,
			array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT => true
			)
		);
		
		$sql = "DELETE FROM $table WHERE $where";
		$result = $link->exec($sql);
		$link=null;
		if($result === false){
			return false;
		} else {
			return true;
		}
	} catch(PDOException $e){
		if($this_system->getSettings('debug_mode') == 1){
			echo "<b>$sql</b><br />";
			echo $e->getMessage();
		}
		$link=null;
		return false;
	}
}

function getTableFields( $table, $db='', $server=''){
	$sql = "SHOW COLUMNS FROM $table";
	$query = do_query_array($sql, $db, $server);

	$sql_feilds = array();
	foreach($query as $row){
		$sql_feilds[] = $row->Field;
	}
	return $sql_feilds;
}
?>