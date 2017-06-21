<?php
/* Backup & Restore Database
*
*/

require_once('scripts/files_functions.php');
require_once('scripts/mysql_backup.class.php');

class Backup {
	
	static function getDatabases(){
		global $this_system;
		if($this_system->type== 'sms'){
			$out = array($this_system->database);
			$years = getYearsArray();
			foreach($years as $year){
				$out[] = Db_prefix.$year;
			}
			return $out;
		} else {
			return array(MySql_Database);
		}
	}

	static function createMySqlBackup($zip=false){
		$_SESSION['backup_mysql_progress'] = 1;
		$databases = backup::getDatabases();
		$count_databases = (count($databases)+1) * 2; // 2 for zip process

		try{
			// Main DB
			$mainDB = new Backup_Database(MySql_HostName, MySql_UserName, MySql_Password, MySql_Database);
			$out[MySql_Database] = $mainDB->backupTables('*', false);
			$done = 1;
			$_SESSION['backup_mysql_progress'] = round( $done / $count_databases * 100);
			
			foreach ($databases  as $db) {
				$yearDB = new Backup_Database(MySql_HostName, MySql_UserName, MySql_Password, $db);
				$out[$db] = $yearDB->backupTables('*', false);
				$done++;
				$_SESSION['backup_mysql_progress'] = round( $done / $count_databases * 100);
			}
	
			if($zip != false){
				$filename = 'DB_'.$zip.'.zip';
				$filepath = 'attachs/backup/'.$filename;
				if(file_exists($filepath ) && is_file($filepath )){ unlink($filepath ); }
				$zip = new ZipArchive;
				$res = $zip->open($filepath , ZipArchive::CREATE);
				foreach($out as $name => $sql){
					$zip->addFromString($name.'.sql', $sql);
					$done++;
					$_SESSION['backup_mysql_progress'] = round( $done / $count_databases * 100);
	
				}
				$zip->close();
				$_SESSION['backup_mysql_progress']=100;
				return file_exists($filepath);
			} else {
				$_SESSION['backup_mysql_progress']=100;
				return $out;
			}
		} catch (Exception $e) {
			unset($_SESSION['backup_mysql_progress']);
			return false;
		}
	}
	
	static function getMysqlBackupProgress(){
		if(isset($_SESSION['backup_mysql_progress'])){
			return json_encode(array('error' => '', 'progress' => $_SESSION['backup_mysql_progress']));
		} else {
			return '';
		}
	}

	static function createFilesBackup($zip=false){
		$_SESSION['backup_files_progress'] = 1;
		$rootFiles = scandir('attachs/');
		for($i=0; $i<count($rootFiles); $i++){// $rootFiles as $f){
			if(!in_array($rootFiles[$i], array('.', '..', 'tmp', 'backup'))){
				$outputRootFiles[] =$rootFiles[$i];
			}
		}

		$outputFiles = array();
		foreach($outputRootFiles as $r){
			if(is_dir('attachs/'.$r)){
				$outputFiles = array_merge($outputFiles, scanRecursive('attachs/'.$r));
			} else {
			
				$outputFiles[] ='attachs/'. $r;
			}
		}
		$count_files = count($outputFiles);

		// Add files
		if($zip != false){
			$done = 0;
			$filename = 'FILES_'.$zip.'.zip';
			$filepath = 'attachs/backup/'.$filename;
			if(file_exists($filepath ) && is_file($filepath )){ unlink($filepath ); }
			$zip = new ZipArchive;
			$res = $zip->open($filepath , ZipArchive::CREATE);
			foreach($outputFiles as $file){
				$zip->addFile($file, $file);
				$done++;
				$_SESSION['backup_files_progress'] = round( $done / $count_files * 100);
			}
			$zip->close();
			$_SESSION['backup_files_progress'] = 100;
			return file_exists($filepath);
		} else {
			$_SESSION['backup_files_progress'] = 100;
			return $outputFiles;
		}
	}
	
	static function getFilesBackupProgress(){
		if(isset($_SESSION['backup_files_progress'])){
			return json_encode(array('error' => '', 'progress' =>  $_SESSION['backup_files_progress']));
		} else {
			return 0;
		}
	}
	
	
	static function createFullBackup(){
		$filename = 'FULL_'.date( 'Y-M-d-h-i-s').'.zip';
		$filepath = 'attachs/backup/'.$filename;
		if(file_exists($filepath ) && is_file($filepath )){ unlink($filepath ); }
		$zip = new ZipArchive;
		$res = $zip->open($filepath , ZipArchive::CREATE);
		//FIles
		$files = Backup::createFilesBackup(false);
		foreach($files as $file){
			$zip->addFile($file);
		}
		// Datbase
		$databases = Backup::createMySqlBackup(false);
		foreach($databases as $name => $sql){
			$zip->addFromString($name.'.sql', $sql);
		}
		$zip->close();
		return file_exists($filepath);
	}

	
	static function getBackupList($intoTable=false){
		$out = array();
		$dir = 'attachs/backup/';
		$backupFiles = scandir($dir);
		foreach($backupFiles as $file){
			if(!in_array($file, array('.', '..'))&& strpos($file, '.zip') !== false){
				$out[] = $dir.$file;
			}
		}
		array_multisort(array_map('filemtime', $out), SORT_DESC, $out);
		if($intoTable != false){
			global $lang;
			$thead=write_html('thead', '',
				write_html('tr', '', 
					write_html('th', 'width="22"', '&nbsp;').
					write_html('th', 'width="22"', '&nbsp;').
					write_html('th', 'width="22"', '&nbsp;').
					write_html('th', '', $lang['file_name']).
					write_html('th', '', $lang['type']).
					write_html('th', '', $lang['date']).
					write_html('th', 'width="60"', $lang['size'])

				)
			);
			
			$trs = array();
			foreach($out as $file){
				$type = '';
				$date = filemtime($file);
				if(strpos($file, 'DB_') !== false || strpos($file, 'AUTO_') !== false){
					$type = 'Databases';
				} elseif(strpos($file, 'FILES_') !== false){
					$type = 'Files';
				} elseif(strpos($file, 'FULL_') !== false){
					$type = 'Full';
				} elseif(strpos($file, 'AUTO_') !== false){
					$type = 'Databases';

				}
				$trs [] = write_html('tr', '',
					write_html('td', '',
						write_html('button', 'action="downloadBackup" class="circle_button hoverable" title="'.$lang['download'].'" rel="'.$file.'"', write_icon('arrowthick-1-s'))
					).
					write_html('td', '',
						write_html('button', 'action="restoreBackup" class="circle_button hoverable" title="'.$lang['restore'].'" rel="'.$file.'"', write_icon('arrowreturnthick-1-w'))
					).
					write_html('td', '',
						write_html('button', 'action="deleteBackup" class="circle_button hoverable" title="'.$lang['delete'].'" rel="'.$file.'"', write_icon('close'))
					).
					write_html('td', '', str_replace($dir, '', $file)).
					write_html('td', '', $type).
					write_html('td', '', date('H:i:s d M Y', $date)).
					write_html('td', '', formatSize(filesize($file)))
				);
			}
			return  write_html('table', 'class="result"',
				$thead.
				write_html('tbody', '',
					implode('', $trs)
				)
			);
				
		} else {
			return $out;
		}
	}
	
	static function removeBackup($file){
		return unlink($file);
	}
	
	static function cleanOldBackup(){
		global $MS_settings;
		$backup_ttl = $MS_settings['backup_ttl'];
		if($backup_ttl > 0){
			$files = Backup::getBackupList(false);
			foreach($files as $file){
				$infos = pathinfo($file);
				if( strpos($infos['filename'], 'AUTO_') !== false){
					$time = filemtime($file);
					if(time() > ($time + ($backup_ttl * 24 * 60 * 60))){
						removeBackup($file);
					}
				}
			}
		}
	}
	
	static function restoreFile($filepath){
		if(file_exists($filepath) && is_file($filepath)){
			$_SESSION['restore_progress'] = 0;
			$infos = pathinfo($filepath);
			$zip = new ZipArchive;
			$res = $zip->open($filepath);
			$filename = str_replace('.zip', '', $infos['filename']);
			$databases = Backup::getDatabases();
			$databases_files =array();
			foreach($databases as $db){
				$databases_files[] = $db.'.sql';
			}
			if(count($databases_files) > 0){
				$zip->extractTo("attachs/tmp/$filename/", $databases_files);
			}
			
			foreach(scandir("attachs/tmp/$filename/") as $sql_file){
				if(strpos($sql_file, '.sql') !== false){
					if(Backup::restoreDB("attachs/tmp/$filename/$sql_file") == false){
						return false;
					}
			//		$zip->deleteName($sql_file);
				}
			}
			if($zip->numFiles > 0){
				$zip->extractTo('./');
				$zip->close();
			}
			/*for($i = 0; $i < $zip->numFiles; $i++) {
				$entry = $zip->getNameIndex($i);
				if(preg_match('#\.(sql)$#i', $entry)){
                	copy("zip://".docRoot.$filepath."#".$entry, docRoot."attachs/tmp/$filename/".$entry.'.sql'); 
                } else {
					copy("zip://".docRoot.$filepath."#".$entry, docRoot."attachs/tmp/$filename/".$entry.'.sql'); 
				}
			}
			if(strpos($infos['filename'], 'DB_') !== false){
				if ($res === TRUE) {
					$zip->extractTo("attachs/tmp/$filename");
					$zip->close();
				} else {
					return false;
				}
			}

			foreach(scandir("attachs/tmp/$filename") as $sql_file){
				if(strpos($sql_file, '.sql') !== false){
					$sqls = explode(';', file_get_contents("attachs/tmp/$filename/$sql_file"));
					if(Backup::restoreDB("attachs/tmp/$filename/$sql_file") == false){
						return false;
					}
				}
			}*/
		//	rmdir("attachs/tmp/$filename/");
			return true;
		} else {
			return false;
		}
	}
	
	static function restoreDB($file){
		$str = file_get_contents($file);
		$sqls = explode(PHP_EOL, $str);
		$database = str_replace(array('USE ', ';', '`'), '', $sqls[1]);
		$temp_db = $database.'_tmp';
		do_query_edit("DROP DATABASE IF EXISTS `$temp_db`;", MySql_Database);
		do_query_edit("CREATE DATABASE `$temp_db`;", MySql_Database);
		
		$result = true;
		$handle = fopen($file, "r");
		$i = 0;
		if($handle) {
			while (($line = fgets($handle)) !== false) {
				if(trim($line) != ''){
					if(!do_query_edit($line, $temp_db)){
						$result = false;
					}
				}
			}
			fclose($handle);
			unlink($file);
		} else {
			$result = false;
		}
		
		if($result == false ){
			$tables = do_query_obj("SHOW TABLES", $temp_db);
			if($tables != false){
				foreach($tables as $t){
					$table = $t[0];
					do_query_edit("DROP TABLE IF EXISTS `$database`.`$table;", $database);
					do_query_edit("RENAME TABLE `$temp_db`.`$table` TO `$database`.`$table` ;", $temp_db);
				}
			}
		} 
		do_query_edit("DROP DATABASE `$temp_db`", MySql_Database);	
		return $result;
	}
}

?>