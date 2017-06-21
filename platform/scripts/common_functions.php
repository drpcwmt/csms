<?php
## common functions
function __autoload($className) {
	if(isset($_SESSION['classes'][$className])){
		require_once($_SESSION['classes'][$className]);
	} else{
		if(!isset( $_SESSION['classes'])){
			$_SESSION['classes'] = array();
		}
		$modules = scandir('modules');
		foreach($modules as $module){
			if(!in_array($modules, array('.', '..')) && is_dir('modules/'.$module)){
				$className = strtolower($className);
				$class_path = "modules/$module/$className.class.php";
				if(file_exists($class_path)){
					require_once( "modules/$module/$className.class.php");
					if(!array_key_exists($className, $_SESSION['classes'])){
						$_SESSION['classes'][$className] = $class_path;
					}
				}
			}
		}
	}
	
}

function getPrvlg($module, $group=false){ // to add privilege by user
	$group = $_SESSION['group'];
	$user_id = $_SESSION['user_id'];
	$user_ena = do_query_array( "SELECT value FROM privileges WHERE user_id=$user_id");
	if($user_ena != false && count($user_ena) > 0){
		$u = do_query_obj( "SELECT value FROM privileges WHERE name LIKE '$module' AND `group`='$group' AND user_id=$user_id LIMIT 1");
		return ($u != false && $u->value == 1 ) ? true : false;
	} else {
		$p = do_query_obj( "SELECT value FROM privileges WHERE name LIKE '$module' AND `group`='$group' LIMIT 1");
		return ($p != false && $p->value == 1 ) ? true : false;
	}
}


	// This Year settings 
function getYearSetting($key_name){ // from student_year settings table
	$r = do_query("SELECT $key_name FROM years WHERE year=".$_SESSION['year']);	
	if ($r[$key_name] !='') {
		return $r[$key_name];
	} else {
		return false;	
	}
}

// Years 
function getYearsArray(){
	$years = do_query_array("SELECT year FROM years ORDER BY year DESC");
	$out = array();
	foreach ($years as $year) {
		$out[] =  $year->year;
	}
	
	return $out ;
}

function getYear($year=''){
	if(isset($_SESSION['year'])){
		if($year =='') { $year = $_SESSION['year'];}
		return do_query_obj("SELECT * FROM years WHERE year=$year");	
	} 
}

function getNowYear(){
	$now = time();
	return do_query_obj("SELECT * FROM years WHERE begin_date<=$now AND end_date>=$now");	
}

function getLangArray(){
	$dir = scandir('lang');
	$langs = array();
	foreach($dir as $file){
		if(!in_array($file, array('.','..', 'dictionary.php'))&& strpos($file, '.php')!== false && $file != 'lang.js.php'){
			$file = str_replace('.php', '', $file);
			$langs[$file] = ucfirst($file);
		}
	}
	return $langs;
}

function getThemeArray(){
	$themes_path = 'assets/css/themes';
	$dir = scandir($themes_path);
	$themes = array();
	foreach($dir as $file){
		if(!in_array($file, array('.','..'))&& is_dir($themes_path.'/'.$file) && file_exists($themes_path.'/'.$file.'/jquery-ui.css')){
			$themes[$file] = ucfirst($file);
		}
	}
	return $themes;
}

function getItemOrder($items){
	$query = do_query_obj("SELECT `order` FROM items_order WHERE items='$items'");
	if($query!=false && $query->order != ''){
		$order = strpos($query->order, ',') !== false ? explode(',', $query->order) : array($query->order);
		return $order;
	} else {
		return array();
	}
}

function updateDyndns(){
	global $this_system;
	if($this_system->getSettings('last_dns_update') == '' || (time()- $this_system->getSettings('last_dns_update')) > 3600){
		$path = "http://".WMTekDydns."/index.php?ser=".$this_system->getSettings('serial').'&name='.$this_system->getSettings('server_name');
		$file_main = fopen($path, 'r');
		$respons = fread($file_main, 1024);
	//	echo $respons; exit;
		fclose($file_main);
	}
}


function json_encode_result($result){
	if((is_string($result) &&
            (is_object(json_decode($result)) ||
            is_array(json_decode($result))))){
		return $result;
	} else {
		if(is_array($result)){
			if(!isset($result['error'])){
				$result['error'] = '';
			}
		} elseif($result === true){
			$result = array();
			$result['error'] = '';
		}elseif($result === false){
			global $lang;
			$result = array();
			$result['error'] = $lang['error'];
		} 
		return json_encode($result);
	}
}

	// passed month for absent
function getPassedMonths($y=''){
	global $this_system, $lang;
	$out = array();
	if($y == ''){ $y = $_SESSION['year'];}
	$year = getYear($y);
	$begin_day = $year->begin_date;
	$end_day = $year->end_date;
    $begin_month = date('m', $begin_day);
	$cur_month = date('m');
	$cur_year = date('Y');
	$c = $begin_month; 
	while($c != $cur_month){
		$t = date('m', mktime(0,0,0,$c, 1, $cur_year));
		$out['m='.$t] = $lang["months_$t"];
		if($c == 12){ $c = 1;} else { $c++;}
	}
	$out['m='.$cur_month] = $lang["months_$cur_month"];
	return $out;
}

function getAccount($code, $sub=''){
	if(strlen($code)==10){
		$full_code = $code;
	} elseif(strlen($code)<6){
		if($sub == '' || $sub == 0){
			$full_code = $code.'00000';
		} else {
			/*for($i=0; $i<=(5-strlen($code)); $i++){
				$code = $code.'0';
			}
			for($i=0; $i<=(6-strlen($sub)); $i++){
				$sub ='0'.$sub;
			}
			$full_code = $code.$sub;*/
			$full_code = Accounts::fillZero('main', $code). Accounts::fillZero('sub', $sub);
		}		
	} else {
		die('Error: Uncomplete account code!');
	}
	if(intval(substr($full_code,5,10))===0){
		return new MainAccounts($full_code);	
	} else {
		if(substr($full_code,0,1) == '1'){
			return new Assets($full_code);
		} elseif(substr($full_code,0,1) == '2'){
			return new Liability($full_code);
		} elseif(substr($full_code,0,1) == '3'){
			return new Expenses($full_code);
		} elseif(substr($full_code,0,1) == '4'){
			return new Incomes($full_code);
		}
	}
}

?>