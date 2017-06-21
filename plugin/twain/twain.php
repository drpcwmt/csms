<?php

//echo $_POST['header'].'/'. $_POST['footer'];

$head = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>'.Ms_systemname.'</title>
	<link media="all" href="'.MS_fullpath.'assets/css/themes/default/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link media="all" href="'.MS_fullpath.'assets/css/common.css" rel="stylesheet" type="text/css" />
	<link media="all" href="'.MS_fullpath.'assets/css/ltr.css" rel="stylesheet" type="text/css" />
	<link media="all" href="'.MS_fullpath.'assets/css/print.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="'.MS_fullpath.'assets/js/jquery-1.8.3.js"></script>
	<script type="text/javascript" src="'.MS_fullpath.'assets/js/jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="'.MS_fullpath.'assets/js/init.js"></script>
	<script type="text/javascript" src="'.MS_fullpath.'assets/js/globals.js"></script>';
echo'<!DOCTYPE html>
<html>
	<head>'.
		$head.
		'</head>
	<body style="text-align:center">
		<div id="twain_div">
			<OBJECT id="TwainX" name="TwainX"
			 classid="clsid:61EC4ECB-81B6-4309-B7B5-8F7755830EEF"     
			 codebase="ScanProj.cab#version=1,0,16,1"
			 width="5" height="5"> </OBJECT>
	
			<input type="submit" name="Submit" value="Scan" onclick="ScanSomeRegions(); return false;" class="ui-corner-all ui-state-default hoverable hand"/>
		</div>
	</body>
</html>';
//echo write_page($header, $body);

?>
<script type="application/javascript">

function detectBrowserAgent(){
	var agent = navigator.userAgent;
	if(agent.indexOf('Firefox') !== -1){
		return 'firefox';
	} else if(agent.indexOf('Chrome') !== -1){
		return 'chrome';
	} else if(agent.indexOf('MSIE') !== -1 || agent.indexOf('Media Center') !== -1){
		return 'ie';
	}
}

var browser = detectBrowserAgent();

if(browser != 'ie'){
	var pluginFound =false
	var L = navigator.plugins.length;	
	for(var i = 0; i < L; i++) {
		if(navigator.plugins[i].filename=="npietab2.dll"){
			pluginFound = true;
		}
	}
	if(!pluginFound){
		$('#twain_div').hide();
		$('body').append('<div id="install_div" class="ui-state-error ui-corner-all" style="padding:10px 0px 30px; margin:20% 10%"><h1>Ietap extention is not installed. </h1><br /></div>');
		if(browser == 'firefox'){
			$('#install_div').append('<a class="ui-corner-all ui-state-default hand hoverable" style="padding:10px 20px" href="https://addons.mozilla.org/firefox/downloads/file/183644/ie_tab_v2_enhanced_ie_tab-4.12.22.2-fx-windows.xpi?src=devhub" >Install Extension!</a>');
		} else if(browser == 'chrome'){
			$('#install_div').append('<a class="ui-corner-all ui-state-default hand hoverable" style="padding:10px 20px" target="_blank" href="https://chrome.google.com/webstore/detail/ie-tab/hehijbfgiekmjfkfjpbkbammjbdenadd">Install Extension!</a>');
		} else {
			$('#install_div').html('<h1>Not supported! Please Use Firefox or chrome On windows station</h1>');
		}
	}
}


function ScanSomeRegions(){
  try {
     //TwainX.config (x_resolution, y_resolution, bit_depth_index,
     //     user_can_crop, user_can_resize, client_temp_directory,
     //  registy_key , interactive_mode, debug_mode);
     TwainX.config (150, 150, 2, 1, 1, "c:/", "my_product", 1, 1);
     //TwainX.scanRegions(temp_directory, region_defintion_string,
     //     interactive, registry_key, debug_mode);
     TwainX.scanRegions(
       "c:/",
       "150,200,200,250,region1.jpg,24,30,5300#"+
       "105,735,370,75,region2.jpg,24,100,2048",
       0, "param4", 0);
     }catch (e){
       alert("scanner error");
     }
}
</script>
