<?php
require_once("templates.class.php");

function write_html($tag, $attr, $content){
	$html = '<'.$tag.' '.$attr.'>'.$content.'</'.$tag.'>';
	return $html;
}

function write_page($head, $body, $scripts=false){
	return '<!DOCTYPE html>
		<html>
			<head>'.
				$head.
				'<script type="text/javascript">
					$(document).ready(function(){
						$(document).bind("drop dragover", function (e) {
							e.preventDefault();
						});'.
						($scripts != false ? 'loadMSScripts("'.implode(',', $scripts).'");' : '').
						'
					})
			</script>
			</head>
			<body>'.
				$body.
			'</body>
		</html>';
}

function isAssoc(array $arr){
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function write_html_select($attr, $value_arr, $selected=''){
	$selc = $selected != '' ? strpos($selected,',') !== false ? explode(',', $selected) : array($selected) : false;
	$options ='';
	$assoc = isAssoc($value_arr);
	if(count($value_arr) > 0 && $value_arr != false){
		foreach($value_arr as $value => $label){
			$options .= write_html('option', 'value="'.($assoc ? $value : $label).'" '.($selc != false && in_array($value, $selc) ? 'selected="selected"' : ''), $label);
		}
	}
	
	return write_html('select', $attr, $options); 
}

function write_select_options( $value_arr, $selected='', $all=false){
	global $lang;
	$selc = $selected != '' ? strpos($selected,',') !== false ? explode(',', $selected) : array($selected) : false;
	$options ='';
	if($all){
		$options .= write_html('option', 'value=" "', $lang['all'] );
	}
	
	if(count($value_arr) > 0 && $value_arr != false){
		foreach($value_arr as $value => $label){
			$options .= write_html('option', 'value="'.$value.'" '.($selc != false && in_array($value, $selc) ? 'selected="selected"' : ''), $label);
		}
	}
	
	return $options; 
}

function write_icon($icon, $title=false){
	return write_html('span', 'class="ui-icon ui-icon-'.$icon.'" '. ($title!=false? 'title="'.$title.'"' : ''), '');	
}

function write_error($str){
	return write_html('div', 'align="center" class="ui-corner-all ui-state-error"', $str);	
}

function write_script($str){
	return write_html('script', 'type="text/javascript"', $str);	
}

function fillTemplate($template_file, $variables){
	$template = new Template($template_file); 
	foreach ($variables as $key => $value) {
		if(!is_object($value) && !is_array($value)){
			$template->set($key, $value);
		}
	}
	return $template->output();
}

function filleMergedTemplate($template_file, $array){
	$rows = array();
	foreach ($array as $vars) {
		$template = new Template($template_file);
		foreach ($vars as $key => $value) {
			if(!is_object($value) && !is_array($value)){
				$template->set($key, $value);
			}
		}
		$rows[] = $template;
	}
	$out = Template::merge($rows);
	return $out;
}

function createToolbox($elements){
	$element_html = array();
	foreach($elements as $element){
		$icon = $element['icon'] != '' ? write_icon($element['icon']) : '';
		$element_html[] = write_html($element['tag'], $element['attr'], $element['text'].' '.$icon);	
	}
	if(count($element_html) > 0){
		return write_html('div', 'class="toolbox"', implode('', $element_html));
	} else {
		return '';
	}
}
?>
