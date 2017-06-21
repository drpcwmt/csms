<?php
## init update file
ini_set("memory_limit","1024M");

require_once('config/mysql_conx.php');
require_once('scripts/files_functions.php');
require_once('scripts/html.php');
require_once('scripts/mysql.php');

require_once("modules/systools/dbStruct.php");
	
	// Read init.json
$json_file  = 'init.json';
$file_json = fopen($json_file, 'r');
$theData = fread($file_json, filesize($json_file));
$settings= json_decode($theData);
fclose($file_json);

$version_name = $settings->{'version'};
echo "<h2>New Version: $version_name</h2>";


	// Updating settings Table
echo "Sync Settings......<br>
<ul>";
foreach($settings->{'settings'} as $key => $value){
	$chk = do_query_resource("SELECT value FROM settings WHERE key_name='$key'", DB_student);
	if(mysql_num_rows($chk) == 0){ 
		do_query_edit("INSERT INTO settings (key_name, value)	VALUES('$key', '$value')", DB_student);
		echo "INSERT INTO settings (key_name, value)	VALUES('$key', '$value')";
	}
}
echo '</ul>';
	// Update version no


$main_db_file  = 'modules/systools/main.sql';
$file_main = fopen($main_db_file, 'r');
$structure_main = fread($file_main, filesize($main_db_file));
fclose($file_main);

$year_db_file  = 'modules/systools/year.sql';
$file_year = fopen($year_db_file, 'r');
$structure_year = fread($file_year, filesize($year_db_file));
fclose($file_year);


	// Compare main db
$updater = new dbStructUpdater();
$mainDB_current = analayseDB(DB_student);
$update_main = $updater->getUpdates($mainDB_current, $structure_main);
echo '<form id="init-form" method="post">
	<input type="hidden" value="update_sql" name="action" />
	Analyzing Main Databse......<br>
	<ul style="font-size:small">';
	if(count($update_main) > 0){
		foreach($update_main as $statment){
			echo write_html('li', '',write_html('label', '','<input type="checkbox" name="statments[]" checked value="'.DB_student.'/'.addslashes($statment).'" />'.$statment));
		}
	} else {
		echo "Nothing to change!<br>";
	}
	echo '</ul>
	
	Analyzing years Databse......<br>';
	$years = do_query_resource("SHOW DATABASES WHERE `Database` LIKE '".Db_prefix."%'");
	while ($row = mysql_fetch_assoc($years)) {
		$yearDB_current = analayseDB($row['Database']);
		$update_year = $updater->getUpdates($yearDB_current, $structure_year);
		echo '<ul style="font-size:small">
		Analysing '.$row['Database'].'...<br>';
		if(count($update_year) > 0){
			foreach($update_year as $statment){
				echo write_html('li', '',write_html('label', '','<input type="checkbox" name="statments[]" checked value="'.$row['Database'].'/'.addslashes($statment).'" />'.$statment));
			}
		} else {
			echo "Nothing to change!<br>";
		}
		echo '</ul>';
	}
	
echo '<input type="submit" /></form>';
?>