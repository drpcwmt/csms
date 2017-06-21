<?php
## Update Scripts
//require_once('scripts/files_functions.php');
exit;
ini_set("memory_limit","1024M");
set_time_limit (0);
ini_set('max_execution_time', 0);

require_once('backup.class.php');
ob_start();
echo '<body style="background-color:#000; color:#FFF;; margin:0; padding:5px">';
if(isset($_FILES['file'])){
	echo 'File recived ...... OK<br>';
	ob_flush();
	// Backup Before continue
	echo 'Backup old data ......';
	$backUpFileName = 'Ubackup_'.date('Y-M-d-h-i-s').'.zip';
	if(Backup::createMySqlBackup()){
		echo 'OK<br>';
	} else {
		echo 'Failed<br>';
		exit;
	}
	//backup_tables("localhost", "csms", "webctrl/WMT", $database );
	$filename = $_FILES['file']['name'];
	$targetPath = MS_tmp.$filename;
	if ($_FILES["file"]["error"] > 0) {
		echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	} else {
		if(move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)){
			echo 'File extraction ...... ';
			$root = stristr (PHP_OS, 'WIN') ? '.\\' : './';
			$zip = new ZipArchive;
			$res = $zip->open($targetPath);
			if($res === true){
				echo 'OK<br>';
				if($zip ->extractTo($root)){
					include('modules/system/init.php');
				}
				$zip->close();
			} else {
				echo 'Failed<br>';
				exit;
			}
		} else {
			echo $targetPath;
		}
	}
} 
ob_end_flush(); 
?>