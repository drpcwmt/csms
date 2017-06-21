<?php
/** Folders 
*
*
*/
require_once(docRoot.'/scripts/files_functions.php');

class Folders{
	public function __construct($path=''){
		if($path!=''){
			$this->path = $path;
		}
	}
	
	public function scanDir($recursive=false){
		$dir = $this->path;
		$list = scandir($dir); 
		natsort($list);
		$num = count($list);
		$folders = array();
		$files = array();
		$filter = array(".", "..", ".htaccess", ".htpasswd","Thumbs.db","folder.jpg","folder.png","folder.gif","folder.bmp","Detsktop.ini","detsktop.ini","thumb.db","thumb", "_notes", "", " ");
		
		for($i = 0; $i < $num; $i++ ){
			if(!in_array($list[$i], $filter) ) {
				$file_name = $list[$i];
				$path = $dir .'/'. $list[$i];
				if(is_dir($path)){
					$thisFolder = new Folders($path);
					$folders[] =$thisFolder;
				} else {
					$thisFile = new Files($path);
					if($thisFile->is_hidden() == false){
						$files[] = $thisFile;
					}
				}
			}
		}
		
		return array('files'=>$files, 'folders'=>$folders);
		
	}
	
	public function getSize(){
		$dir = $this->path;
		$size = 0;
		$scan = $this->scanDir($dir, true);
		foreach($scan['files'] as $file){
			$size += $file->getSize();
		}
		return $size;
	}
	
	public function download(){
		$dir = $this->path;
		include_once("scripts/archive_zip.php");
		ini_set("memory_limit","1024M");
		
		$filename = "attachs/tmp/".time().".zip";
		$zipfile = new Archive_Zip($filename);
		
		$ziparray = $this->scanDir($dir, true);
		$zipfile->create($ziparray['files']);
		
		if(file_exists($filename)){
			$file = new Files($filename);
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers
			header("Content-Type: application/zip");
			header("Content-Disposition: attachment; filename=\"".basename($file->path)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$file->getSize());
			//ob_clean();
			flush();
			readfile( $file->Path );
			$file->delete();
		} else {
			die('Error: File Not Found'); 
		}
	}
	
	public function getThumb(){
		return 'assets/img/filemanger_icons/folder.png';
	}
	
	public function getName(){
		$path = $this->path;
		if(strpos($path, '/') !== false){
			$f = explode('/', $path);
			$name = $f[count($f)-1];
		} else {
			$name = $path;
		}
		return $name;	
	}
	
	public function dateCreated(){
		return filemtime($this->path);
	}
	
	public function loadListView($options){
		global $lang;
		return write_html('tr', 'class="item"',
			// selectable
			 ($options->selectable ? 
				write_html('td', '', '<input type="checkbox" name="folder[]" value="'.systemToSqlPath($this->path).'" />')
			: 
				'<input type="hidden" name="folder[]" value="'.systemToSqlPath($this->path).'" />'
			).
			// sharable none for folder
			($options->sharable ? 	write_html('td', '', '&nbsp;') : ''	).
			// Downloadable
			 ($options->downloadable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="downloadFiles"  title="'.$lang['download'].'"',  write_icon('circle-arrow-s'))
				)
			: '').
			// Openable
			 ($options->openable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="browseDir"',  write_icon('extlink'))
				)
			: '').
			// Delete only option for folder
			($options->editable ? 
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="deleteDir" title="'.$lang['delete'].'"',  write_icon('close'))
				)
			: '').
			
			write_html('td', '', 
				($options->openable ? 
					write_html('a', 'href="#" class="file_name" action="browseDir"', 
						'<img src="'.$this->getThumb().'" border="0" height="24" width="24" style="vertical-align:middle;" /> '.
						write_html('span', 'class="filename"', getUtf8Path($this->getName()))
					)
				: 
					'<img src="'.$this->getThumb().'" border="0" height="24" width="24" style="vertical-align:middle;" /> '.
					write_html('span', 'class="filename"', getUtf8Path($this->getName()))
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
		return write_html('li', 'class="item file_name ui-corner-all ui-state-default hoverable hand" title="'.getUtf8Path($this->getName()).' <br />date: '.strftime ("%d.%b %I:%M %p", $this->dateCreated()).'<br />size: '.formatSize($this->getSize()).'"',
			write_html('ul', 'class="file_tools hidden "',
				 ($options->selectable ? 
					write_html('li', '', '<input type="checkbox" name="folder[]" value="'.systemToSqlPath($this->path).'" />')
				: 
					'<input type="hidden" name="folder[]" value="'.systemToSqlPath($this->path).'" />'
				)
			).
			write_html('a', ($options->openable ? 'action="browseDir"': ''), 
				'<img src="'.$this->getThumb().'" border="0" height="60" width="60"  /> <br />'.
				write_html('span', 'class="filename"', getUtf8Path($this->getName()))
			)
		);
	}
	
	static function _new($root, $value){
		if( is_dir($root)){
			$system_path = $root.$value;
			if(is_dir($system_path) ){
				$answer['id'] = $system_path;
				$answer['error'] = "allready_exists";
			} else {
				if (mkdir($system_path, true, 0777)){
					@chmod ($system_path, 0777);
					$answer['path'] = $system_path;
					$answer['error'] ='';
				} else {
					$answer['id'] = "";
					$answer['error'] ='Error cant create :'.$system_path;
				}
			}
		} else {
			$answer['id'] = "";
			$answer['error'] ='Error cant find root :'.$root;
		}
		return json_encode($answer);
	}

	public function _delete(){
		$scan = $this->scanDir(true);
		if($scan != false && count($scan['files']) > 0){
			foreach($scan['files'] as $file){
				$file->_delete();
			}
		}
		return full_rmdir(sqlToSystemPath($this->path));
	}

	static function _rename($path, $name){
		$s = explode('/', $path);	
		unset($s[count($s)-1]);
		$new_path = implode('/', $s).'/'.$name;

		if (rename(sqlToSystemPath($path), sqlToSystemPath($new_path))){
			$answer['path'] = $new_path;
			$answer['error'] ='';
		} else {
			$answer['id'] = "";
			$answer['error'] ='Error cant rename :'.$path;
		}
		return json_encode($answer);
	}

}

?>