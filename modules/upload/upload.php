<?php
## Upload Script

require_once('scripts/files_functions.php');

$allowed_array = explode(',', $this_system->getSettings('docs_filter'));


// proogress
if(isset($_GET['progress_key'])) {
    $status = apc_fetch('upload_'.$_GET['progress_key']);
    echo $status['current']/$status['total']*100;
    die;
} 

//upload form
if(isset($_GET['uploadform'])){
	$upload = new Upload();
	$upload->destination = isset($_GET['dest']) ? $_GET['dest'] : Ms_myDocs;
	$upload->filename = isset($_GET['filename']) ? $_GET['filename'] : false;
	$upload->overwrite = isset($_GET['overwrite']) ? true : false;
	$upload->multiple = !isset($_GET['multi']) || $_GET['multi'] == 'true' ? true : false;
	echo $upload->loadUploadForm();
	exit;	
}

$user = new Users($_SESSION['group'], $_SESSION['user_id']);
$doc_root = $user->doc_path;

if(isset($_FILES['file'])){
	ini_set('default_charset', 'UTF-8');
	$fileType = str_replace('image/', '', $_FILES['file']['type']);
	$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	if($this_system->getSettings('docs_allowed') == 0 || in_array(strtolower($ext), $allowed_array)){
		$overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] == 'true' ? true : false;
		$destination = isset($_POST['dest']) && $_POST['dest'] != '' ? $_POST['dest'] : $doc_root;
		if(!is_dir($destination)){
			mkdir($destination);
		}
		$filename = isset($_POST['filename']) && $_POST['filename'] != '' ? $_POST['filename'] : str_replace("'", "",$_FILES['file']['name']);
		
		// Save file
		$filepath = sqlToSystemPath($destination.'/'.$filename); 
		//	echo 'file:'.$filepath.'<br />'.sqlToSystemPath($filepath);
	
		if(file_exists(($filepath)) && is_file(($filepath))){
			unlink(($filepath));
		}
		
		//$destination = sqlToSystemPath($destination);
		/*if(!is_dir(sqlToSystemPath($destination))){
			mkdir(sqlToSystemPath($destination), 0777, true);
		} else {
			chmod(sqlToSystemPath($destination), 0777);	
		}*/
		
		if($MS_settings['autoconvert_image'] == 1 && isset($_POST['autoconvert']) && $_POST['autoconvert']==1){
			require_once('plugin/media/images.class.php');
			// IMAGES
			if(in_array(strtolower($fileType), array("jpg", "jpeg", "gif", "png", "gd", "wbmp", "xbm", "xpm"))){
				try{
					$image = new Images($_FILES['file']['tmp_name']);
					$image->resize($MS_settings['conv_img_hight_size']);
					$image->setType($MS_settings['conv_img_type']);
					$image->save(true, $MS_settings['conv_img_quality']);
				} catch(Exception $e){
					if($MS_settings['debug_mode'] == 1){
						echo 'Error:'.$e;
					}
				}
			}
		}
		
		
		//Save file
		//if(!file_exists(getSystemPath($filepath)) && !is_file(getSystemPath($filepath)) ){
			if(move_uploaded_file($_FILES['file']['tmp_name'], $filepath)){
				if(file_exists($filepath)){
					// insert in SQL
					$link = uniqid();
					$owner_id = $_SESSION['user_id'];
					$owner_group = $_SESSION['group'];
					$safeSqlFilepath = systemToSqlPath($filepath);
					$insert_sql = "INSERT INTO files (path, link, owner_group, owner_id) VALUES('$safeSqlFilepath', '$link', '$owner_group', $owner_id)";
					do_query_edit($insert_sql, LMS_Database);
					echo  "File: $filepath";
				} 
				if($MS_settings['autoconvert_video'] == 1 && isset($_POST['autoconvert']) && $_POST['autoconvert']==1){
					$fileType = str_replace('video/', '', $_FILES['file']['type']);
					//echo $fileType;
					if(in_array(strtolower($fileType), array("x-ms-wmv", "mp4", "avi", "flv", "ogg", "x-ms-asf", "mpeg"))){
						//echo 'begin conversation';
						try{
							require_once('plugin/media/videos.class.php');
							$video = new Videos($filepath);
							$video->compress();
							/// $video->getInfos());
						} catch(Exception $e){
							//echo $e;
						}
					}
					///
					///
				}
			} else {
				echo 'Error: Saving the file';
			}
		//}
	} else {
		echo "Error: $ext ($fileType) Are not allowed";
	}
}
?>