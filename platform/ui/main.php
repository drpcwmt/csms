<?php 
## LibMS main ui 
// load Module

/******************************************/
$assets_files = array(
	'lang/lang.js.php',
	'assets/js/superfish.js',
	'assets/js/jquery.tablesorter.min.js',
	'assets/js/jquery.metadata.js',
	'assets/js/jquery.maskedinput-1.3.js',
	'assets/js/jquery.easing.1.3.js',
	'assets/js/ui.combobox.js',
	'assets/js/jquery.colourPicker.js',
	'assets/js/jquery.hoverIntent.minified.js',
	'assets/js/jquery.dialogextend.js',
	'assets/js/ace/ace.js',
//	'assets/js/tinymce/jquery.tinymce.min.js',
//	'assets/js/tinymce/tinymce.min.js',
	'assets/css/jquery.colourPicker.css',
	'assets/css/superfish.css',
	'modules/home/home.js',
	'ui/main.js',
	'plugin/print/print.js',
	'plugin/pdf/pdf.js',
	'plugin/xml/xml.js',
	'modules/employers/employers.js'
);
if(defined("MapsApiKey")){
	$assets_files[] = 'assets/js/jquery.geocomplete.min.js';
}
/******************************************/
// default body

require_once('header.php');
require_once('layout.php');
echo write_page($header, $layout_table, $assets_files);
?>