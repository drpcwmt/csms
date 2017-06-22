<?php
## file explorer
// switch my_documents, profs_docs, students_docs, admins_docs

//require_once('scripts/files_functions.php');
//require_once('scripts/documents_functions.php');
require_once('plugin/media/videos.class.php');
require_once('plugin/media/images.class.php');

$document = new Documents;

// ROOT PATHS
if(isset($_GET['type'])){
	switch ($_GET['type']){
		case 'mydoc' :
			$root_path = $document->user->doc_path.'/';	
		break;
		case 'lib' :
			if(isset($_GET['lib']) && safeGet($_GET['lib']) != ''){
				$lib = new sharedLibrary(safeGet($_GET['lib']));
				$root_path = $lib->path.'/';
			}
		break;
		case 'services' :
			$service = new services(safeGet($_GET['service_id']));
			$root_path = 'attachs/services/'.$service->getName().'/';
		break;
	}
}

// FILES OPERATIONS
if(isset($_GET['newdir'])){
	$dir = $_POST['dir'];
	$new = $_POST['new'];
	echo Folders::_new(sqlToSystemPath($root_path.$dir), sqlToSystemPath($new));

	// Rename 
} elseif(isset($_GET['rename'])){
	$link = $_POST['link'];
	$filename = urldecode($_POST['new']);
	if(strpos($link, '/') !== false && is_dir($link)){
		echo Folders::_rename($link, $filename);
	} else {
		echo Files::_rename($link, $filename);
	}
	//Delete
} elseif(isset($_GET['delete'])){
	$error = false;
	$files = array();
	if(isset($_REQUEST['file']) && count($_REQUEST['file']) > 0){
		foreach($_REQUEST['file'] as $link){
			$file = new Files(sqlToSystemPath($link));
			if($file->_delete() == false){
				$error= true;
			}
		}
	}
	if(isset($_REQUEST['folder']) && count($_REQUEST['folder']) > 0){
		foreach($_REQUEST['folder'] as $dir){
			$folder = new Folders(sqlToSystemPath($dir));
			if($folder->_delete() == false){
				$error= true;
			}
		}
	}
	if(!$error){
		$answer['path'] = '';
		$answer['error'] ='';
	} else {
		$answer['id'] = "";
		$answer['error'] ='Error delete :'.$path;
	}
	print json_encode($answer);
	
	// Download	

} elseif(isset($_GET['download'])){
	$files = array();
	$force = ($_GET['download'] != '0') ? true : false;
	if(isset($_REQUEST['file']) && count($_REQUEST['file']) == 1 && !isset($_REQUEST['folder'])){
		$file = new Files(sqlToSystemPath($_GET['file'][0]));
		$filepath = $file->path;
		if(file_exists($filepath) && !is_dir($filepath)){
			forceDownload($filepath, $force);
		echo $filepath;
		}
	} else {
		if(isset($_REQUEST['file']) ){
			foreach($_REQUEST['file'] as $link){
				$file = new Files($link);
				$filepath = sqlToSystemPath($file->path);
				if(file_exists($filepath) && !is_dir($filepath)){
					$files[] = $filepath;
				}				
			}
		}
		if(isset($_REQUEST['folder'])){
			foreach($_REQUEST['folder'] as $dir){
				$folder = new Folders(sqlToSystemPath($dir));
				$scan = $folder->scanDir(true);
				foreach($scan['files']
				 as $file){
					$files[] = $file->path;
				}
			}
		}
		if(count($files) > 0){
			downloadAsZip($files);
		} else {
			die('No files Found');
		}
	}
	
//Move

} elseif(isset($_GET['move'])){ 
	$links = strpos($_POST['links'], ',') !== false ? explode(',', $_POST['links']) : array($_POST['links']);
	$path = $root_path.$_POST['dir'];
	$error = 0;
	foreach($links as $link){
		$file = new Files($link);
		$old_path = $file->path;;
		$new_path = $path.'/'.$file->basename;
		if(file_exists(sqlToSystemPath($new_path)) === false){
			if(rename(sqlToSystemPath($old_path), sqlToSystemPath($new_path))){
				do_query_edit("UPDATE files SET path='$new_path' WHERE link='$link'", LMS_Database);
			} else {
				$error++;
			}
		} else {
				echo $new_path;
			$error++;
		}
	}
	if($error == 0){
		$answer['path'] = $path;
		$answer['error'] ='';
	} else {
		$answer['id'] = "";
		$answer['error'] ='Error moving '.$error.' file(s)';
	}
	print json_encode($answer);

	// Open	
} elseif(isset($_GET['open'])){
	$link = urldecode($_GET['file']);
	$path = getPathFromLink($link);
	if(stristr (PHP_OS, 'WIN')) { $path = iconv("UTF-8", "CP1256//TRANSLIT", $path);}
	// Parse Info / Get Extension
	$fsize = filesize($path);
	$path_parts = pathinfo($path);
	$ext = strtolower($path_parts["extension"]);
   
	// Determine Content Type
	switch ($ext) {
	  case "pdf": $ctype="application/pdf"; break;
	  case "exe": $ctype="application/octet-stream"; break;
	  case "zip": $ctype="application/zip"; break;
	  case "doc": $ctype="application/msword"; break;
	  case "xls": $ctype="application/vnd.ms-excel"; break;
	  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
	  case "txt": $ctype="text/plain"; break;
	  case "gif": $ctype="image/gif"; break;
	  case "png": $ctype="image/png"; break;
	  case "jpeg": $ctype="image/jpg"; break;
	  case "jpg": $ctype="image/jpg"; break;
	  default: $ctype="application/force-download";
	}

	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Disposition: filename=\"".basename($path)."\";" );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".$fsize);
	header("Content-Type: ". $ctype);
	readfile( $path );
	exit;
	
	
	// Share
} elseif(isset($_GET['share']) || isset($_GET['getconid'])){
	include('documents_share.php');
	
	// Library
//} elseif(isset($_GET['librarys'])){
//	include('documents_librarys.php');
//
	// Spacebar reload
} elseif(isset($_GET['reloadspacebar'])){
	echo $document->loadSpaceBar();
} else {
	// Default Body
	if(isset($_GET['view'])){
		$_SESSION['document_view'] = safeGet($_GET['view']);
	} 
	
	$view = isset($_SESSION['document_view']) ? $_SESSION['document_view'] : 'icon';
	$inc_dir = isset($_GET['dir']) && !empty($_GET['dir']) ? safeGet($_GET['dir']) : '';
	
	/***************************** BODY *************************************************/
	if(isset($_GET['type']) && $_GET['type'] == 'shared'){
		echo $document->getSharedFiles($view);
	} elseif(isset($_GET['type']) && $_GET['type'] == 'mydoc'){
		echo $document->loadMyDocs($inc_dir, $view);
		
	} elseif(isset($_GET['type']) && $_GET['type'] == 'lib'){
		if(isset($_GET['edit'])){
			$lib_id = isset($_GET['lib_id']) && $_GET['lib_id']!='' ? safeGet($_GET['lib_id']) : false;
			echo sharedLibrary::libForm($lib_id);
		}elseif(isset($_GET['save'])){
			echo sharedLibrary::_save($_POST);
		}elseif(isset($_GET['deletelib']) && isset($_POST['lib_id'])){
			$editor = new sharedLibrary($_POST['lib_id']);
			echo $editor->_delete();
		} else {
			echo $document->loadLibrary(safeGet($_GET['lib']), $inc_dir, $view);
		}

	} elseif(isset($_GET['type']) && $_GET['type'] == 'services'){
		echo $document->loadServices(safeGet($_GET['service_id']), $inc_dir, $view);
	} else {
		echo $document->loadMainLayout();
	}
}
?>