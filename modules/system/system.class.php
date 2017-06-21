<?php
/* System
*
*/

class System{


	public function loadLayout(){
		global $lang, $MS_settings;
		$layout = new Layout();
		$layout->backup_table = Backup::getBackupList(true);
		$layout->classes_opts = write_select_options(objectsToArray(Classes::getList()));
		$layout->levels_opts = write_select_options(objectsToArray(Levels::getList()));
		$layout->explorer = $this->getEditorExplorer();
		$materials = objectsToArray(Materials::getList());

		$layout->religion_table = write_html('table', 'id="religion_table" border="0" cellspacing="0" style="margin:8px 20px"',
			write_html('tr', '',
				write_html('td', ' width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"',$lang['islamic_subject'])
				).
				write_html('td', '',
					write_html_select('name="ser_muslim" class="combobox"', $materials , $MS_settings['islamic_material'])
				)
			).
			write_html('tr', '',
				write_html('td', ' width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"',$lang['christian_subject'])
				).
				write_html('td', '',
					write_html_select('name="ser_christian" class="combobox"', $materials , $MS_settings['christian_material'])
				)
			)
		);

		return fillTemplate('modules/system/templates/system_layout.tpl', $layout);
	}
	
	public function getFolderTpl($folder){
		$out = array();
		$modules = scandir($folder);		
		foreach($modules as $module){
			if(is_dir($folder.'/'.$module) && !in_array($module, array('.','..'))){
				$files = scanRecursive($folder.'/'.$module);
				foreach($files as $file){
					$path_parts = pathinfo($file);
					if(isset($path_parts["extension"])){
						$ext = strtolower($path_parts["extension"]);
						if($ext == 'tpl'){
							if(!array_key_exists($module, $out)){
								$out[$module] = array();
							}
							$out[$module][] = $file;
						}
					}
				}
			}
		}
		return $out;
	}

	public function getEditorExplorer(){
		$out = '';
		$folders = array('modules', 'plugin', 'attachs');
		foreach($folders as $folder){ // modules
			$fs = $this->getFolderTpl($folder);
			$modules = array();
			foreach($fs as $module=>$files){
				$f = array();
				foreach($files as $file){
					$path_parts = pathinfo($file);
					$file_name = strtolower($path_parts["filename"]);
					$f[] = write_html('li', '',
						write_html('a', ' class="hoverable hand" action="openTbl" rel="'.$file.'"', $file_name)
					);
				}
				$modules[] = write_html('li', '', 
					write_html('a', ' class="hoverable hand" action="toogleEditorUl"', $module).
					write_html('ul', 'class="hidden"', implode('', $f))
				);
			}
			$out .= write_html('li', '',
				write_html('a', ' class="hoverable hand" action="toogleEditorUl"', $folder).
				write_html('ul', 'class="hidden"', implode('', $modules))
			);					
		}
		
		return $out;	
	}
	
	static function openTpl($file=''){
		global $lang;
		if($file!= '' && !file_exists($file)){
			return write_error("$file dont exists!");
		}
		if($file != ''){
			$data = file_get_contents($file);
			$path_parts = pathinfo($file);
			$dir = $path_parts['dirname'];
			$file_name = $path_parts['basename'];
		} else {
			$layout->dir = 'attachs/templates';
			$data = '';
			$file_name= '';
		}
		

		$out = write_html('div', 'class="tabs"',
			write_html('ul', '',
    			write_html('li', '', write_html('a', 'href="#design_mode"', 'Design')).
				write_html('li', '', write_html('a', 'href="#code_mode"', 'Code'))
       		).
			write_html('div', 'id="design_mode"',
				 write_html('form', '',
					'<input type="hidden" name="directory" value="'.$dir.'" />'.
					write_html('fieldset', 'class="ui-state-highlight ui-corner-all"',
						write_html('table', 'width="100%" cellspacing="1" cellpadding="0" border="0"',
							write_html('tr', '',
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
								).
								write_html('td', 'valign="top"',
									'<input type="text" class="input_double required" value="'.$file_name.'"/>'.
									$dir.'/ '
								)
							)
						)
					).
					write_html('textarea', 'class="tinymce" name="data"', $data)
				)
			).
			write_html('div', 'id="code_mode" class="aceEditor" style="height:350px"',
				htmlentities($data)
			)
		);
		return $out;
	}
}
?>