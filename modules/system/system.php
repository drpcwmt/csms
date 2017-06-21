<?php
## System
if(isset($_GET['session'])){
	if(isset($_POST['field']) && isset($_POST['value'])){
		include('session.class.php');
		if(Session::setSession($_POST['field'], $_POST['value'])){
			echo json_encode(array('error' => '')); 
		}
	}
} elseif(isset($_GET['open_tpl'])){
	echo System::openTpl(safeGet('open_tpl'));
	
} elseif(isset($_REQUEST['action'])){
	if($_REQUEST['action'] == 'systools'){
		include('systools.php');
		
	} elseif($_REQUEST['action'] == 'backup'){
		$error = false;
		include('backup.class.php');
		$fileid = date( 'Y-M-d-h-i-s');
		$dir = 'attachs/backup/';
	
		if(isset($_GET['progress'])){
			if($_GET['progress'] = 'sql'){
				echo Backup::getMysqlBackupProgress();
			} else {
				echo Backup::getFilesBackupProgress();
			}
			exit;
		} elseif(isset($_GET['remove'])){
			if(Backup::removeBackup($_POST['file']) != false){
				echo json_encode(array('error' => ''));
			} else {
				echo json_encode(array('error' => 'Error'));
			}
			exit;
		} elseif(isset($_GET['restore'])){
			if(Backup::restoreFile($_POST['file']) != false){
				echo json_encode(array('error' => ''));
			} else {
				echo json_encode(array('error' => 'Error'));
			}
			exit;
		} elseif(isset($_POST['file']) && isset($_POST['sql'])){
			$filename = 'FULL_'.$fileid.'.zip';
			$type = 'Full';
			if(Backup::createFullBackup($fileid) == false){
				 $error = true;
			}
		} elseif(isset($_POST['sql'])){
			$filename = 'DB_'.$fileid.'.zip';
			$type = 'Database';
			if(Backup::createMySqlBackup($fileid) == false){
				 $error = true;
			}
		} elseif(isset($_POST['file'])){
			$type = 'Files';
			$filename = 'FILES_'.$fileid.'.zip';
			if(Backup::createFilesBackup($fileid) == false){
				 $error = true;
			}

		}
		
		if($error){
			echo json_encode(array('error' => 'Error'));
		} else {
			echo json_encode(array(
				'error' => '', 
				'filename' => $filename, 
				'filepath'=> $dir.$filename,
				'type'=> $type,
				'size' => get_size(filesize($dir.$filename)), 
				'time'=> date('H:i:s d M Y', filemtime($dir.$filename)	)		
			));
		}
	
	
	} else {
		include($_REQUEST['action'].'.php');
	}
} else {
	require_once('backup.class.php');
	backup::cleanOldBackup();
	
	$system = new System();
	echo $system->loadLayout();
}


?>