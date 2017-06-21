<?php
## SMS 
## Files functions
// charset conversation
//	require_once("scripts/archive_zip.php");

function urlToSystemPath($path){
	return urldecode($path);
}

function systemToUrlPath($path){
	return urlencode($path);
}

function sqlToSystemPath($path){
	return stripslashes(getSystemPath($path));
}

function systemToSqlPath($path){
	return addslashes(getUtf8Path($path));
}


function getSystemPath($path){
	if (stristr (PHP_OS, 'WIN')){
		return iconv("UTF-8", "CP1256//TRANSLIT", $path);
	} else {
		return $path;
	}
}

function getUtf8Path($path){
	if (stristr (PHP_OS, 'WIN')){
		return iconv("CP1256", "UTF-8//TRANSLIT", $path);
	} else {
		return $path;
	}
}


//size function
function formatSize($size){
	if ($size < 1024){
	  return round($size,2).'Byte';
	}elseif ($size < (1024*1024)){
	  return round(($size/1024),2).'Kb';
	}elseif ($size < (1024*1024*1024)){
	  return round((($size/1024)/1024),2).'Mb';
	}elseif ($size < (1024*1024*1024*1024)){
	  return round(((($size/1024)/1024)/1024),2).'Gb';
	}
}

// Remove dir
function full_rmdir( $dir ){
	if(file_exists($dir.'/.') && is_dir($dir)){
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
		  (is_dir("$dir/$file")) ? full_rmdir("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	} 
}

// Download 
function downloadAsZip($ziparray){
	include_once('scripts/archive_zip.php');
	ini_set("memory_limit","1024M");
	$filename = "attachs/tmp/".time().".zip";
	// Create instance of Archive_Zip class, and pass the name of our zipfile
	$zipfile = New Archive_Zip(sqlToSystemPath($filename));
	
	// Create the zip file
	$zipfile->create($ziparray);
	
	forceDownload(sqlToSystemPath($filename), true);
	@unlink($filename);
}

function forceDownload($fullPath, $force=false) {
	ini_set("memory_limit","1024M");

  // Must be fresh start
  if( headers_sent() )
    die('Headers Sent');

  // Required for some browsers
  if(ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');

	// File Exists?
	//$fullPath = sqlToSystemPath($fullPath);
	if( file_exists($fullPath) ){
	   
		// Parse Info / Get Extension
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);
		$ext = strtolower($path_parts["extension"]);
	   
		// Determine Content Type
		if($force){
			$ctype="application/force-download";
		}else {
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
		}
		
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: ". $ctype);
		header("Content-Disposition: attachment; filename=\"".basename(systemToSqlPath($fullPath))."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$fsize);
		ob_clean();
		flush();
		readfile( $fullPath );
	} else{
		die('File Not Found'); 
	}
}



// Scan 
function scanRecursive($dir){
	$files = array();
	$fs = scandir($dir);
	foreach($fs as $f){
		if(!in_array($f, array('.', '..', '_notes'))){
			if(is_dir($dir.'/'.$f)){
				$files = array_merge($files, scanRecursive($dir.'/'.$f));
			} else {
				$files[] = $dir.'/'.$f;
			}
		}
	}
	return $files;
}

?>