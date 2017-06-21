<?php 

// Init Errors
$my_error = array();
function push_my_error($error){
	//$my_error = $GLOBALS['my_error'];
	$my_error[] = $error;
	echo $error;
}



// mysql connection
function mysql_con($server=''){
	if($server == '') { $server = MySql_HostName;}
	if($conx = @mysql_pconnect( $server, MySql_UserName, MySql_Password)){
		mysql_query("SET NAMES 'utf8'", $conx);
		return $conx;
	} else {
		global $MS_settings;
		if($MS_settings['debug_mode'] == 1){echo "Error: Can't connect to $server MySql server";}
		return false;
	}
}

// mysql query return mysql resource 
function do_query_resource($sql, $db='', $server=''){
//	$_SESSION['request']=$_SESSION['request']+1;
	$conx = mysql_con($server);
	if($db == '') { $db = MySql_Database;}
	mysql_select_db($db, $conx);
	$result = mysql_query($sql, $conx) or push_my_error(mysql_error());
	if($result){
		$out= $result; 
	} else {
		global $MS_settings;
		if($MS_settings['debug_mode'] == 1){echo 'Error: '.$sql;}
		$out= false;
	}
	return $out;
}

function do_query($sql, $db='', $server=''){
//	$_SESSION['request']=$_SESSION['request']+1;
	$query = do_query_resource($sql, $db, $server);
	if( $query != false){
		if( mysql_num_rows($query)> 0){
			$out = mysql_fetch_assoc($query);
			return  $out; 	
		} else {
			return false;
		}
	} else {
		//push_my_error("Can't find record for your request: ($sql)");
		return  false; 	
	}
	
}

function do_query_list($sql, $db='', $server=''){
	$query = do_query_resource($sql, $db, $server);
	$table = array();
    if (mysql_num_rows($query) > 0){
        $i = 0;
        while($table[$i] = mysql_fetch_assoc($query)) 
            $i++;
        unset($table[$i]);                                                        
		mysql_free_result($query);
		return $table;
	} else {
		
		push_my_error("Can't find record for your request: ($sql)");
	}                     
}

function do_query_insert( $table, $field, $values, $db='', $server=''){
	$sql = "INSERT INTO $table ($field) VALUES ($values)";
	$insert = do_query_edit($sql, $db, $server);
	if($insert!= false){
		return mysql_insert_id();
	} else {
		return false;
	}
}

function do_query_edit($sql, $db='', $server=''){
	$conx = mysql_con($server);
	mysql_select_db($db, $conx );
	return do_query_resource($sql, $db, $server);
}

function testFoundRecords($sql, $db='', $server=''){
	$result = do_query_resource($sql, $db, $server);
	if($result!=false && mysql_num_rows($result) > 0){
		return true	;
	} else {
		return false;
	}
}

function deleteFromDB($table, $feild, $id, $db='', $server=''){
	$sql = "DELETE FROM $table WHERE $feild=$id";
	$result = do_query_resource( $sql, $db, $server);
	if($result) {
		return true ;
	} else {
		return false;
	}
}


/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*', $create=true)
{
	$return = '';
	$link = mysql_connect($host,$user,$pass);
	mysql_query("SET NAMES 'utf8'", $link);
	mysql_select_db($name,$link);

	$return .= 'CREATE DATABASE IF NOT EXISTS '.$name.";\n\n";
    $return .= 'USE '.$name.";\n\n";	//get all of the tables
    
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE  IF EXISTS '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = @preg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	if($create){
		$handle = fopen('attachs/backup/db-backup-'.$name.'-'.date('Y-M-d-h-i-s').'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
	} else {
		return $return;
	}
}

function deleteDatabase($db){
	$sql = "DROP DATABASE $db";
	return do_query_edit( $sql);
}

function insertToTable($table, $values, $database=''){
	$commom_fields = getTableFields( $table, $database);
	$affect_common_value = array();
	$affect_common_fields = array();
	foreach($values as $key => $value){
		if(in_array($key, $commom_fields)){
			$affect_common_fields[] = "`$key`";
			if(strpos($key, '_date') !== false){
				if($value != ''){
					if(strpos($value, '/') !== false ){
						$affect_common_value[] = GetSQLValueString(dateToUnix($value), "int");
					} else {
						$affect_common_value[] = GetSQLValueString($value, "int");
					}
				} else {
					$affect_common_value[] = "NULL";
				}
			} elseif(strpos($key, '_time') !== false){
				if($value != ''){
					if(strpos($value, ':') !== false ){
						$affect_common_value[] = GetSQLValueString(timeToUnix($value), "int");
					} else {
						$affect_common_value[] = GetSQLValueString($value, "int");
					}
				} else {
					$affect_common_value[] = "NULL";
				}
			} else {
				$affect_common_value[] = GetSQLValueString($value, "text");
			}
		}
	}	
	if(do_query_edit ( "INSERT INTO $table (".implode($affect_common_fields, ',').") VALUES ( ". implode($affect_common_value, ',').")", $database)){
		if(in_array('id', $affect_common_fields)){
			return mysql_insert_id();
		} else {
			return true;
		}
	} else { 
		return false; 
	}
}

function UpdateRowInTable($table, $values, $where, $database=''){
	$commom_fields = getTableFields( $table, $database);
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

	if(do_query_edit ( "UPDATE $table SET ".implode($affect_common_fields, ',')." WHERE ".$where, $database)){
		return true;
	} else { 
		return false; 
	}
}

function ReplaceInTable($table, $values, $where, $database=''){
	$commom_fields = getTableFields( $table, $database);
	$affect_common_fields = array();
	foreach($values as $key => $value){
		if(in_array($key, $commom_fields)){
			$affect_common_fields[] = $key;
			if(strpos($key, '_date') !== false){
				if($value != ''){
					if(strpos('/', $value) !== false ){
						$affect_common_value[] = GetSQLValueString(dateToUnix($value), "int");
					} else {
						$affect_common_value[] = GetSQLValueString($value, "int");
					}
				} else {
					$affect_common_value[] = "NULL";
				}
			} elseif(strpos($key, '_time') !== false){
				if($value != ''){
					if(strpos(':', $value) !== false ){
						$affect_common_value[] = GetSQLValueString(timeToUnix($value), "int");
					} else {
						$affect_common_value[] = GetSQLValueString($value, "int");
					}
				} else {
					$affect_common_value[] = "NULL";
				}
			} else {
				$affect_common_value[] = GetSQLValueString($value, "text");
			}
		}
	}
	if(do_query_edit ( "REPLACE INTO $table (".implode($affect_common_fields, ',').") VALUES ( ". implode($affect_common_value, ',').") WHERE ".$where, $database)){
		return true;
	} else { 
		return false; 
	}
}

function analyseTable($table, $db){
	$row2 = mysql_fetch_row(do_query_resource('SHOW CREATE TABLE '.$table, $db));
	return $row2[1];
}

function analayseDB( $db){
	$tables = array();
	$result = do_query_resource('SHOW TABLES', $db);
	while($row = mysql_fetch_row($result)){
		$tables[] = analyseTable($row[0], $db);
	}
	return implode(';', $tables);
}
?>