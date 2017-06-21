<?php
## Dictionary editor

function createData($lang_arr){
	$str = '<?php'.PHP_EOL. '$lang=array();'.PHP_EOL;
	foreach($lang_arr as $key=>$value){
		$str .= '$lang["'.$key.'"] = "'.$value.'";'.PHP_EOL;
	}
	return $str;
}

function createFile($lang, $lang_arr){
	$file = "lang/$lang.php";
	$result = false;
	if($enh = fopen($file, 'wb')){
		if(fputs($enh, "\xEF\xBB\xBF". createData($lang_arr).'?>')){
			$result = true;
		}
		fclose($enh);
	}
	return $result;
}

//*************** IMPORT *******************/
if(isset($_GET['import_dict'])){
	$lng = $_POST['lang'];
	$ip = isset($_POST['server']) && $_POST['server']!= '' ? $_POST['server'] : '127.0.0.1';
	$database = isset($_POST['database']) && $_POST['database']!= '' ? $_POST['database'] : 'csms';
	$dictionarys = do_query_array("SELECT name, $lng FROM dictionary", $database, $ip);
	foreach($dictionarys as $row){
		$langs[$row->name] = $row->$lng;
	}
	$answer = array();
	if(count($langs) > 0){
		if( createFile($lng, $langs)){
			$answer['error'] = '';
		}
	} else {
		$answer['error'] = 'Error: cant find language on database';
	}
	echo json_encode($answer);
	exit;
}


//*************** POST *******************/

if(isset($_POST['index'])){
	ini_set('max_input_vars', 3000);
	$lng = $_GET['lang'];
	$index_array = array();
	for($i=0; $i<count($_POST['index']); $i++){
		$index = $_POST['index'][$i];
		if(!in_array($index, $index_array)){
			$langs[$index] = $_POST['value'][$i];
		}
	}
	if(createFile($lng, $langs)){	
		$answer['error'] = '';
		$answer['done'] = 'OK';
	} else {
		$answer['error'] = 'Error';
	}
	echo json_encode($answer);
	exit;
}

//*************** FORN *******************/
$trs = array();

$lng = isset($_GET['lang']) ? safeGet('lang') : $this_system->getSettings('default_lang');
include("lang/$lng.php");
/*
$indexs = array();
foreach($lang as $key =>  $values){
	$indexs[] = $key;
}

asort($indexs);
foreach($indexs as $index){
	$trs[] = write_html('tr','', 
		write_html('td', '', write_html('button', 'type="button" action="deleteThis" class="ui-state-default hoverable circle_button"', write_icon('close'))).
		write_html('td', '', '<input type="hidden" name="index[]" value="'.$index.'" / >'.$index).
		write_html('td', '',
			'<input name="value[]"  value="'. (isset($lang[$index]) ? $lang[$index] : '').'" />'
		)
	);
}

if(isset($_GET['lang'])){
	echo implode('', $trs);
	exit;
}*/

$langs_arr= array("en"=>"En", "ar"=>"Ar", "fr"=>"Fr", "de"=>"De");

echo write_html('form', 'id="dictionary-form" action="" method="POST"',
	write_html('h3', ' style="margin:7px 20px"', count($trs).' '. $lang['words']).
	 write_html('div', 'class="toolbox"',
		write_html('a', 'action="submitDictionary" lang="'.$lng.'"', write_icon('disk').$lang['save']).
		write_html('a', 'action="importDictionary" lang="'.$lng.'"', write_icon('refresh').' Import').
		//write_html('a', 'action="addWord"', write_icon('plus').$lang['add_words']).
		write_html('span', '',
			write_html('select', 'class="combobox" update="changeDictLang" name="lang"',
				write_select_options($langs_arr, $lng)
			)
		)
	).
	write_html('table', 'class="tableinput" id="lang_table"', 
		write_html('thead', '', 
			write_html('tr', '',
				write_html('th', 'width="20"', '&nbsp;').
				write_html('th', 'width="150"', 'Index').
				write_html('th', '', $lng)
			)
		).
		write_html('tbody', '',
			implode('', $trs)
		)
	).
	 write_html('div', 'class="toolbox"',
		write_html('a', 'action="submitDictionary" lang="'.$lng.'"', write_icon('disk').$lang['save'])
	)
);
?>