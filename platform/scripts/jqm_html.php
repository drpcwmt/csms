<?php
## Jquery mobile Html

function format_jqm_content($content, $header=false, $footer=false, $id=false){
	global $lang;
	if($header!=false){
		$header_html = $header;
	} else {
		include('blocks/toolbar.php');
		$header_html = write_html('div', 'data-role="header" data-position="fixed" data-fullscreen="true"', $toolbar);
	}

	if($footer!=false){
		$footer_html = $footer;
	} else {
		include('blocks/footer.php');
		$footer_html = write_html('div', 'data-role="footer"', $footer);
	}
	
	return write_html('div', 'data-role="page" '.($id!=false? 'id="'.$id.'"':''),
		$header_html.
		write_html('div', 'role="content"', $content).
		$footer_html
	);
}

function write_jqm_page($content, $title, $head=false, $assets=false){
	if($head!=false){
		$head_html = $head;
	} else {
		$head_html = '<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="mobile-web-app-capable" content="yes">
		<link rel="shortcut icon" sizes="196x196" href="img/logo.png">
		<link rel="stylesheet" href="css/jquery.mobile-1.4.4.min.css">
		<link rel="stylesheet" href="css/themes/jquery.mobile.icons.min.css">
		<link rel="stylesheet" href="css/styles.css">
		<script src="js/jquery-1.8.3.js"></script>
		<script src="js/jquery.mobile-1.4.4.min.js"></script>
		<script src="js/globals.js"></script>
		<script src="js/init.js"></script>
		<script src="lang/lang.js.php" type="application/javascript"></script>';
		if($assets != false){
			foreach($assets as $file){
				$head_html .= '<script src="'.$file.'"></script>';
			}
		}
	} 
	if($title != ''){
		$head_html .= "<title>$title</title>";
	} else {
		$head_html .= "<title>".Ms_systemname."</title>";
	}
	
	$content_html = $content;

	return $content_html;
}

function write_jqm_grid($array){
	$out = '';
	$alph = 'abcdefghijklmnopqrstuvwxyz';
	for($i=0; $i<count($array); $i++){
		$cols = count($array[$i]);
		if($cols == 0){ 
			$out .= '';
		} elseif($cols == 1){
			$out .= write_html('div', 'class="grid_item"', 
				'<div class="ui-block-a" >'.
					'<a class="ui-shadow ui-btn ui-corner-all" href="'.$array[$i][0].'">'.$array[$i][1].'</a>'.
				'</div>'
			);
		} else {
			$out .= '<div >';
				for($x=0; $x<count($array[$i]); $x++){
					$out .= '<div class="grid_item">'.
						'<a class="ui-shadow ui-btn ui-corner-all" href="'.$array[$i][$x][0].'">'.$array[$i][$x][1].'</a>'.
					'</div>';
				}
			$out .= '</div>';
		}
	}
	return $out;
}

function write_jqm_input($label , $id, $name, $extra, $type=false){
	if($type == '' || $type ==false){
		$type = 'text';
	}
	return write_html('label', 'for="'.$id.'"', $label).
	'<input id="'.$id.'" name="'.$name.'" type="'.$type.'" '.$extra.'>';	
}