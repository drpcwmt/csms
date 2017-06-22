<?php

$settings = new Layout();
$settings->template = 'modules/resources/templates/settings.tpl';
$class_list_templ_dir = 'attachs/templates';
$files = scandir($class_list_templ_dir);
$tpls = array();
foreach($files as $f){
	if(strpos($f, 'class_list') !== false){
		$file = str_replace('_row.tpl', '.tpl', $f);
		if(!in_array($file, $tpls)){
			$name = str_replace('.tpl', '' , $file);
			$tpls[$name] = $name;
		}
	}
}
		
$settings->class_list_opt = write_html_select('name="class_list_template" class="combobox"', $tpls, $this_system->getSettings('class_list_template'));

$module_settings = $settings->_print();

?>