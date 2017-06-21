<?php
/** Files 
*
*
*/
require_once(docRoot.'/scripts/files_functions.php');

class Files{
	public $filename = '',
	$extension = '',
	$path = '';
	
	private $database = LMS_Database;
	
	public function __construct($ref){
		// ref = path provided
		if(strpos($ref,'/') !== false || strpos($ref,'\\') !== false){
			
			$sql = "SELECT * FROM files WHERE path='".systemToSqlPath($ref)."'";
		} else {
			$this->link = $ref;
			$sql = "SELECT * FROM files WHERE link='$this->link'";
		}
		
		$fileDb = do_query_obj($sql, $this->database);
		if(isset($fileDb->path) && !empty($fileDb->path)){
			if(is_dir(sqlToSystemPath($fileDb->path)) || !file_exists(sqlToSystemPath($fileDb->path))){
				//$this->clearLinkFromDb();
				throw new Exception('File Not Found: '.$fileDb->path);
			}
			foreach($fileDb as $key=>$value){
				$this->$key = $value;
			}
			$this->path = sqlToSystemPath($fileDb->path);
		} else {
			// isert if not found Sql in case 
			$link = uniqid();
			$this->link = $link;
			$this->path = $ref;
			$this->owner_id = 0;
			$this->owner_group = 0;
			$insert_sql = "INSERT INTO files (path, link, owner_group, owner_id) VALUES('".systemToSqlPath($ref)."', '$link', 0, 0)";
			do_query_edit($insert_sql, $this->database);	
		}
		
		$fileInfos = $this->mb_pathinfo($this->path);
		foreach($fileInfos as $key=>$value){
			$this->$key = systemToSqlPath($value);
		}
		
		if(isset($this->extension)){		
			if(in_array(strtolower($this->extension), array('ppt', 'pptx'))){
				$this->type = 'ppt';
			} elseif(in_array(strtolower($this->extension), array('png', 'jpg', 'jpeg', 'gif'))){
				$this->type = 'img';
			} elseif(in_array(strtolower($this->extension), array('pdf'))){
				$this->type = 'pdf';
			} elseif(in_array(strtolower($this->extension), array( 'avi', 'mp4', 'flv', 'swf', 'ogg', 'webm', 'mpg', 'mpeg'))){
				$this->type = 'video/'.strtolower($this->extension);
			} else {
				$this->type= 'file';
			}
		} else {
			$this->type= 'file';
		}
						
	}
	
	public function mb_pathinfo($path, $opt = "")
		  {
			$separator = " qq ";
			$path = preg_replace("/[^ ]/u", $separator."\$0".$separator, $path);
			if ($opt == "") $pathinfo = pathinfo($path);
			else $pathinfo = pathinfo($path, $opt);
		
			if (is_array($pathinfo))
			{
			  $pathinfo2 = $pathinfo;
			  foreach($pathinfo2 as $key => $val)
			  {
				$pathinfo[$key] = str_replace($separator, "", $val);
			  }
			}
			else if (is_string($pathinfo)) $pathinfo = str_replace($separator, "", $pathinfo);
			return $pathinfo;
		  }

	public function getSize(){
		// First, try the filesize() function
		$size = filesize($this->path);
		if ($size < 0){
			// If the platform is Windows...
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
				// Try using the NT substition modifier %~z
				$size = trim(exec("for %F in (\"".$this->path."\") do @echo %~zF"));
				if (!$size || !ctype_digit($size)){
					// Use the Windows COM interface
					$fsobj = new COM('Scripting.FileSystemObject');
					if (dirname($this->path) == '.')
						$file = ((substr(getcwd(), -1) == DIRECTORY_SEPARATOR) ? getcwd().basename($this->path) : getcwd().DIRECTORY_SEPARATOR.basename($file));
					$f = $fsobj->GetFile($this->path);
					return $f->Size;
				}
				return $size;
			}
	
			// If the platform is not Windows, use the stat command (should work for *nix and MacOS)
			return trim(`stat -c%s $this->path`);
		}
		return $size;
	}
	
	public function getThumb($wdth=64){
		global $MS_settings;
		$extention = $this->extension;
		// Reel Thumbnails
		if($MS_settings['real_thumb'] == 1){
			// Images
			if(in_array($extention, array('jpg', 'png', 'gif', 'jpeg'))){
				$image = new Images($this->path);
				$image->resize('', $wdth);
				$thumb =  $image->getThumb($wdth);
				if( $thumb != false){
					return $thumb ;
				} 
			// Video
			} elseif(in_array($extention, array('mp4', 'avi', 'wmv', 'flv', 'webm'. 'mpg', 'mpeg', 'asf'))){
				try{
					$video = new Videos($this->path);
					$thumb =  $video->getThumb(15, $wdth);
					if( $thumb != false){
						return $thumb ;
					} 
				} catch(Exception $e){
				//	echo $e;
				}
			} 
		}
		// Default Icons
		if(file_exists('assets/img/filemanger_icons/'.$extention.'.png')){
			return 'assets/img/filemanger_icons/'.$extention.'.png';
		} else {
			return 'assets/img/filemanger_icons/default.png';
		}
		
	}
	
	public function getOwner(){
		global $this_system;
		return $this_system->getAnyNameById($this->owner_group, $this->owner_id);
	}
	
	public function dateCreated(){
		return filemtime($this->path);
	}
	
	public function download($force=true){
		if( headers_sent() ){
			die('Headers Sent');
		}
	
		ini_set("memory_limit","1024M");
		// Required for some browsers
		if(ini_get('zlib.output_compression')){
			ini_set('zlib.output_compression', 'Off');
		}
		
		if( file_exists($this->path) ){
		   
			// Parse Info / Get Extension
			$fsize = $this->getSize();
			$ext = strtolower($this->extension);
		   
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
			header("Content-Disposition: attachment; filename=\"".basename($this->Path)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$fsize);
			//ob_clean();
			flush();
			readfile( sqlToSystemPath($this->Path) );
		} else{
			die('File Not Found'); 
		}
	}
	
	public function is_hidden(){
		if (stristr (PHP_OS, 'WIN')){
			$attr = trim(exec('FOR %A IN ("'.$this->path.'") DO @ECHO %~aA'));
			if($attr[3] === 'h'){
				return true;
			}
			return false;
		} else {
			 if(strpos($this->path, '.') !== (int) 0) {
				 return false;
			 } 
			return true;
		}
	}
	
	public function is_shared(){
		if(isset($this->link ) && $this->link != ''){
			return testFoundRecords("SELECT link FROM files_share WHERE link='$this->link'", $this->database);
		} else return false;
	}
		
	public function loadListView($options){
		global $lang;
		return write_html('tr', 'class="item"',
			// selectable
			 ($options->selectable ? 
				write_html('td', '', '<input type="checkbox"  name="file[]" value="'.$this->link.'" />')
			: 
				'<input type="hidden"  name="file[]" value="'.$this->link.'" />'
			).
			// sharable
			($options->sharable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button '.($this->is_shared() ? 'ui-state-active' : '').'" action="shareFile" link="'.$this->link.'" title="'.($this->is_shared() ? $lang['shared'] : $lang['share']).'"',  write_icon('transferthick-e-w'))
				)
			: '').
			// Downloadable
			 ($options->downloadable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="downloadFiles"  title="'.$lang['download'].'"',  write_icon('circle-arrow-s'))
				)
			: '').
			 ($options->openable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="openFile"  type="'.$this->type.'" rel="'.systemToSqlPath($this->path).'" title="'.$lang['open'].'"',  write_icon('extlink'))
				)
			: '').
			// Delete Or deattache
			($options->editable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="'.($options->deattach ? 'deleteFile' : 'deattachFile').'" title="'.$lang['delete'].'"',  write_icon('close'))
				)
			: '').
			// Openable			
			write_html('td', '', 
				($options->openable ? 
					write_html('a', 'href="#" class="file_name" action="openFile" rel="'.systemToSqlPath($this->path).'" type="'.$this->type.'" ', 
						'<img src="'.$this->getThumb().'" border="0" height="24" width="24" style="vertical-align:middle;" /> '.
						write_html('span', 'class="filename"', getUtf8Path($this->filename))
					)
				: 
					'<img src="'.$this->getThumb().'" border="0" height="24" width="24" style="vertical-align:middle;" /> '.
					write_html('span', 'class="filename"', getUtf8Path($this->filename))
				)	
			).
			($options->mini == false ?
				write_html('td', '',
					strftime ("%d.%b %I:%M %p", $this->dateCreated())
				).			
				write_html('td', 'style="font-size:8px"',
					formatSize($this->getSize())
				)
			: '')
		);				
	}

	public function loadIconView($options){
		global $lang;
		return write_html('li', 'class="item file_name ui-corner-all ui-state-default hoverable hand" title="'.getUtf8Path($this->filename).'.'.getUtf8Path($this->extension).' <br />date: '.strftime ("%d.%b %I:%M %p", $this->dateCreated()).'<br />size: '.formatSize($this->getSize()).'<br />'.$lang['owner'].': '.$this->getOwner().'"',
			/*
			// sharable
			($options->sharable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button '.($this->is_shared() ? 'ui-state-active' : '').'" action="shareFile" title="'.($this->is_shared() ? $lang['shared'] : $lang['share']).'"',  write_icon('transferthick-e-w'))
				)
			: '').
			// Downloadable
			 ($options->downloadable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="downloadFiles"  title="'.$lang['download'].'"',  write_icon('circle-arrow-s'))
				)
			: '').
			// Openable
			 ($options->openable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="browseDir" title="'.$lang['open'].'"',  write_icon('extlink'))
				)
			: '').
			// Delete Or deattache
			($options->editable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="'.($options->deattach ? 'deleteFile' : 'deattachFile').'" title="'.$lang['delete'].'"',  write_icon('close'))
				)
			: '').*/
			write_html('ul', 'class="file_tools hidden "',
				 ($options->selectable ? 
					write_html('li', '', '<input type="checkbox" name="file[]" value="'.$this->link.'" />')
				: 
					'<input type="hidden" name="file[]" value="'.$this->link.'" />'
				).
				($options->sharable ? 
					write_html('li', '', 
						write_html('button', 'class="ui-state-default hoverable mini_circle_button '.($this->is_shared() ? 'ui-state-active' : '').'" action="shareFile" title="'.($this->is_shared() ? $lang['shared'] : $lang['share']).'"',  write_icon('transferthick-e-w'))
					)
				: '')
			).
			write_html('a', 'action="openFile"'.($options->openable ? ' rel="'.urldecode(systemToSqlPath($this->path)).'" type="'.$this->type.'"': ''), 
				'<img src="'.$this->getThumb().'" border="0" height="60" width="60"  /> <br />'.
				write_html('span', 'class="filename"', systemToSqlPath($this->filename))
			)
		);				
	}
	
	static function _rename($link, $filename){
		$file = new Files(sqlToSystemPath($link));
		$old_filename = $file->filename;
		$path = $file->path;
		
		$s = explode('/', $path);
		
		if(strpos($filename, '.') !== true){
			$extention = $file->extension;
			$filename.=  $file->extension != '' ? '.'.$file->extension : '';
		}
	
		unset($s[count($s)-1]);
		$new_path = addslashes(implode('/', $s).'/'.$filename);

		if (rename(sqlToSystemPath($path), sqlToSystemPath($new_path))){
			do_query_edit("UPDATE files SET path='$new_path' WHERE link='$link'", $file->database);
			$answer['path'] = $new_path;
			$answer['error'] ='';
		} else {
			$answer['id'] = "";
			$answer['error'] ='Error cant rename :'.$path;
		}
		return json_encode($answer);
	}

	public function _delete(){
		if(unlink($this->path)){
			return $this->clearLinkFromDb();
		} else {
			return false;
		}
	}
	
	public function clearLinkFromDb(){
		if(do_query_edit("DELETE FROM files WHERE link='$this->link'", $this->database)){
			do_query_edit("DELETE FROM  files_share WHERE link='$this->link'", $this->database);
			do_query_edit("DELETE FROM summarys_attachs WHERE link='$this->link'", $this->database);
			do_query_edit("DELETE FROM homeworks_attachs WHERE link='$this->link'", $this->database);
			return true;
		} else {
			return false;
		}
	}


}

?>