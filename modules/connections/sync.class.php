<?php
/** Syncronization
*
*/
class Sync{
	
	static function addToqueues($sql, $database, $conx){
		global $MS_settings;
		$row = new stdClass	();
		$row->sql = $sql;
		$row->database = $database;
		$row->time = time();
		$row->ip = $conx->ip;
		return do_insert_obj($post, 'school_fees_profils', MySql_Database);
	}
	
	static function addInsert($value, $table, $database, $conx){
		try {
			$link = new PDO('mysql:host='.MySql_HostName.';dbname='.$database.';charset=utf8mb4',
				MySql_UserName,
				MySql_Password,
				array(
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_PERSISTENT => false
				)
			);
			$commom_fields = getTableFields( $table, $database);
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
							$affect_common_value[] = "NULL";
						}
					} elseif(strpos($key, '_time') !== false || $key == 'time'){
						if($value != ''){
							if(strpos( $value, ':') !== false ){
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
			$sql =  "INSERT INTO $table (".implode($affect_common_fields, ',').") VALUES ( ". implode($affect_common_value, ',').")";
			
			return Sync::addToqueues($sql, $database, $conx);
		} catch(PDOException $e){
			if($MS_settings['debug_mode'] == 1){
				echo "<b>$sql</b><br />";
				echo $e->getMessage();
			}
		}

	}
}
?>