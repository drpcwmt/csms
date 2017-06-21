<?php
## Globals functions

// get full URL
function get_full_url() {
	$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
	return
		($https ? 'https://' : 'http://').
		(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
		(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
		($https && $_SERVER['SERVER_PORT'] === 443 ||
		$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
		substr($_SERVER['SCRIPT_NAME'],0, strpos($_SERVER['SCRIPT_NAME'], '/'));
}

function getImagePath($path, $width='', $height=''){
	$out = 'index.php?plugin=img_resize&path='.urlencode($path);
	if($width != ''){
		$out .= '&amp;w='.$width;
	}
	if($height != ''){
		$out .= '&amp;h='.$height;
	}
	return $out;
}

function getLogo($width, $height){
	$uploaded = 'attachs/img/logo.png';
	$default = 'assets/img/logo.png';
	return getImagePath((file_exists($uploaded) ? $uploaded : $default), $width, $height);
}

function dateToUnix($date){ // 14/05/2005
	if(strpos($date, '/') === false ){
		return $date;
	} else {
		if($date != '' && $date!=false && preg_match("/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/", $date) !=false){
//		if($date != '' && $date!=false && preg_match("/[0-9]\/[0-9]\/[0-9]{4}/", $date) !=false){
			$d  = explode('/', $date);
			return  mktime(0,0, 0, $d[1], $d[0], $d[2]);
		} else {
			return NULL;
		}
		
	}
}

function unixToDate($unix){
	global $this_system;
	$template = $this_system->getSettings('date_template') != false ? $this_system->getSettings('date_template') : 'd/m/Y';
	if(preg_match("/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/", $unix)){
		return $unix;
	} else {
		//if(!empty($unix)&& is_long($unix)){
		if($unix !=0 && $unix!=7200&& $unix!=''){
			$dt = new DateTime();
			$dt->setTimestamp($unix); 
			return $dt->format($template);
		} else {
			return '';
		}
	}
}

function timeToUnix($time){  // 07:30
	if($time != ''){
		$d  = explode(':', $time);
		return mktime($d[0],$d[1], 00, 1, 1,1970);
	} else {
		return 0;
	}
}

function unixToTime($unix){
	if($unix != ''&& $unix >0){
		return date('H:i',$unix);
	} else {
		return NULL;
	}
}

function secondToHours($unix){
	if($unix != ''){
		$min_rest = $unix % 3600;
		$hours = ($unix - $min_rest)/3600;
		$sec_rest = $min_rest % 60;
		$minuts = ($min_rest - $sec_rest)/60;
		return $hours.':'.$minuts;
	} else {
		return NULL;
	}
}

function dateDifference($date_1 , $date_2 , $differenceFormat = '%d/%m/%y' )
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
    
    $interval = date_diff($datetime1, $datetime2);
    
    return $interval->format($differenceFormat);
    
}

function NumberToMoney($number){
	setlocale(LC_MONETARY, 'en_US.UTF-8');
	if(intval($number) != 0){
		return number_format($number, 2, ',', '&nbsp;');
		/*if (stristr (PHP_OS, 'WIN')){
			return number_format($number, 2, ',', '&nbsp;');
		} else {
			return money_format('i', $number);
		}*/
	} else return '';
}

function first_route($date, $target_week_day) {
	if($date == ''){
	 	$date = getdate();
	} else {
		$date = getdate($date);
	}
	$day = $date["mday"];
	$week_day = $date["wday"];
	$month = $date["mon"];         
	$year = $date["year"];

	if($week_day <= $target_week_day){
		$days_left = $target_week_day - $week_day;
	} else {
		$days_left = 7 - ($week_day - $target_week_day);
	}
	return mktime(0, 0, 0, $month, $day + $days_left, $year); 
}

function first_week_route($date, $first_day) {
	if($date == ''){
		$date = time();
	}
	$day = date('d', $date); //$date["mday"];
	$week_day = date('w', $date); //$date["wday"];
	$month = date('m', $date); //$date["mon"];         
	$year = date('Y', $date); //$date["year"];
	
	if($week_day >= $first_day){
		$days_before = $week_day - $first_day;
	}else{
		$dif = ($first_day - $week_day);
		$days_before = 7 - $dif;
	}
	
	return mktime(0, 0, 0, $month, $day - $days_before, $year);
}

function secondsToHours($seconds){
    $ret = "";
    $hours = intval(intval($seconds) / 3600);
    if($hours > 0){
        $ret .= "$hours:";
    } else {
		$ret .= "00:";
	}
    $minutes = bcmod((intval($seconds) / 60),60);
    if($hours > 0 || $minutes > 0){
        $ret .= "$minutes";
    } else{
		$ret .= "00";
	}
    return $ret;
}

function addToStrArray($separator, $strarr, $addvalue){
	if($strarr != ''){
		$d = explode($separator, $strarr);
		if(!in_array($addvalue, $d )){
			$d[] = $addvalue;
		}
		return implode($separator, $d);	
	} else {
		return $addvalue;
	}
}

function remoFromStrArray($separator, $strarr, $remvalue){
	if($strarr != ''){
		$d = explode($separator, $strarr);
		$out = array();
		foreach($d as $v){
			if($v != $remvalue){
				$out[] = $v;
			}
		}
		return implode($separator, $out);
	} else {
		return '';
	}
}

function getColorPicker(){
	$out = array();
	$out["ffffff"]="#ffffff";
	$out["ffccc9"]="#ffccc9";
	$out["ffce93"]="#ffce93";
	$out["fffc9e"]="#fffc9e";
	$out["ffffc7"]="#ffffc7";
	$out["9aff99"]="#9aff99";
	$out["96fffb"]="#96fffb";
	$out["cdffff"]="#cdffff";
	$out["cbcefb"]="#cbcefb";
	$out["cfcfcf"]="#cfcfcf";
	$out["fd6864"]="#fd6864";
	$out["fe996b"]="#fe996b";
	$out["fffe65"]="#fffe65";
	$out["fcff2f"]="#fcff2f";
	$out["67fd9a"]="#67fd9a";
	$out["38fff8"]="#38fff8";
	$out["68fdff"]="#68fdff";
	$out["9698ed"]="#9698ed";
	$out["c0c0c0"]="#c0c0c0";
	$out["fe0000"]="#fe0000";
	$out["f8a102"]="#f8a102";
	$out["ffcc67"]="#ffcc67";
	$out["f8ff00"]="#f8ff00";
	$out["34ff34"]="#34ff34";
	$out["68cbd0"]="#68cbd0";
	$out["34cdf9"]="#34cdf9";
	$out["6665cd"]="#6665cd";
	$out["9b9b9b"]="#9b9b9b";
	$out["cb0000"]="#cb0000";
	$out["f56b00"]="#f56b00";
	$out["ffcb2f"]="#ffcb2f";
	$out["ffc702"]="#ffc702";
	$out["32cb00"]="#32cb00";
	$out["00d2cb"]="#00d2cb";
	$out["3166ff"]="#3166ff";
	$out["6434fc"]="#6434fc";
	$out["656565"]="#656565";
	$out["9a0000"]="#9a0000";
	$out["ce6301"]="#ce6301";
	$out["cd9934"]="#cd9934";
	$out["999903"]="#999903";
	$out["009901"]="#009901";
	$out["329a9d"]="#329a9d";
	$out["3531ff"]="#3531ff";
	$out["6200c9"]="#6200c9";
	$out["343434"]="#343434";
	$out["680100"]="#680100";
	$out["963400"]="#963400";
	$out["986536"]="#986536";
	$out["646809"]="#646809";
	$out["036400"]="#036400";
	$out["34696d"]="#34696d";
	$out["00009b"]="#00009b";
	$out["303498"]="#303498";
	$out["000000"]="#000000";
	$out["330001"]="#330001";
	$out["643403"]="#643403";
	$out["663234"]="#663234";
	$out["343300"]="#343300";
	$out["013300"]="#013300";
	$out["003532"]="#003532";
	$out["010066"]="#010066";
	$out["340096"]="#340096";
	//$cs = array('00', '33', '66', '99', 'CC', 'FF');
	/*for($i=0; $i<6; $i+2) {
        for($j=0; $j<6; $j+2) {
            for($k=0; $k<6; $k+2) {
                $c = $cs[$i] .$cs[$j] .$cs[$k];
				$out["$c"]= "#$c";
            }
        }
    }*/
	return $out;
}
function random_color(){
    mt_srand((double)microtime()*1000000);
    $c = '';
    while(strlen($c)<6){
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}

function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}
function getopositeColor($hex){
	if($hex != false && $hex != ''){
		$dic = hex2RGB($hex);
		$e = array();
		foreach($dic as $color){
			$e[] = 255 - $color;
		}
		$out = '#';
		 for($i = 0; $i<3; $i++)
			$e[$i] = dechex(($e[$i] <= 0)?0:(($e[$i] >= 255)?255:$e[$i]));
			 
		 for($i = 0; $i<3; $i++)
			$out .= ((strlen($e[$i]) < 2)?'0':'').$e[$i];
				 
		 $out = strtoupper($out); 
		 return $out;
	} else {
		return false;
	}
}


function setJsonHeader(){
	 // preparing header for JSON requests
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header("Cache-Control: no-cache, must-revalidate" );
	header("Pragma: no-cache" );
	header("Content-type: application/json");
}

function readFileData($path){
	$opts = array( 'http'=>array( 'method'=>"GET",
	  'header'=>"Accept-language: en\r\n" .
	   "Cookie: ".session_name()."=".session_id()."\r\n" ) 
	);
	$context = stream_context_create($opts);
	session_write_close();  
	return file_get_contents($path, false, $context);
}


function getBrowser(){
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
	$browser = get_browser(null, true);
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";
/*
    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
   
    // Next get the name of the useragent yes seperately and for good reason
	if (false !== strpos($_SERVER["HTTP_USER_AGENT"], 'Trident/7.0; rv:11.0')) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
	} elseif(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
			
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            @$version= $matches['version'][0];
        }
        else {
            @$version= $matches['version'][1];
        }
    }
    else {
       @$version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
   	if(false !== strpos($_SERVER["HTTP_USER_AGENT"], 'Trident/7.0; rv:11.0')){
		$version = 11;
	}*/

    return array(
        'userAgent' => $u_agent,
        'name'      => $browser['browser'],
        'version'   => $browser['version'],
        'platform'  => $browser['platform']
    );
}

/****************** Version 3 Functions ***********/
function safeGet($get){
	if(array_key_exists($get, $_GET)){
		return trim(htmlentities($_GET[$get]));
	} else {
		return trim(htmlentities($get));
	}
}

function objectsToArray($objects, $field='id'){
	$out = array();
	if(count($objects)>0 && $objects!=false){
		foreach($objects as $obj){
			$out[$obj->$field] = $obj->getName();
		}
	}
	return $out;	
}

function object_in_array($obj, $array, $field='id') {
	$all = array();
	foreach($array as $h){
		$all[] = $h->$field;
	}
	return in_array($obj->$field, $all);

}
	
function sortArrayByArray($array,$orderArray) {
	if($array != false && is_array($array)){
		$ordered = array();
		foreach($orderArray as $key) {
			if(array_key_exists($key,$array)) {
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		foreach($array as $key => $value){
			if(!array_key_exists($key,$ordered)) {
				$ordered[$key] = $array[$key];
			}
		}
		return $ordered ;
	} else {
		return $array;
	}
}


function sortArrayOfObjects($objects, $pattern, $field){
	$out = array();
	$array = array();
	$pattern = is_array($pattern) ? $pattern : explode(',', $pattern);
	foreach($objects as $object){
		$array[$object->$field] = $object;
	}

	foreach($pattern as $pat){
		if(array_key_exists($pat, $array)){
			$out[] = $array[$pat];
			unset($array[$pat]);
		}
	}
	foreach($array as $key => $obj){
		$out[] = $obj;
	}
	return $out;
}

function translatePatern($matches){
	global $lang;
	$match = substr($matches[0], 2);
	$match = substr($match, 0, strlen($match)-1 );
	return $lang[$match];
}

$normalize = function($size) {
	if (preg_match('/^([\d\.]+)([KMG])$/i', $size, $match)) {
		$pos = array_search($match[2], array("K", "M", "G"));
		if ($pos !== false) {
			$size = $match[1] * pow(1024, $pos + 1);
		}
	}
	return $size;
};


function regenerateSession(){
	if(!isset($_SESSION)) session_start();
	if(isset($_SESSION['regenerated_count'])){
		if ( ++$_SESSION['regenerated_count'] > 100 ){
			$_SESSION['regenerated_count'] = 0;
			session_regenerate_id(true);
		}
	} else {
		$_SESSION['regenerated_count'] = 0;
	}
}

function saferRequests(){
	if(isset($_GET)){
		foreach($_GET as $key => $value){
			if(!is_array($value)){
				$_GET[$key] = htmlspecialchars (strip_tags($value));
			} else {
				foreach($value as $v){
					$_GET[$key][] = htmlspecialchars (strip_tags($v));
				}
			}
		}
	} 
	if(isset($_POST)){
		foreach($_POST as $key => $value){
			$_POST[$key] =str_replace('<script', '', str_replace("<?", '', $value));
		}
	}
}

function xmlpp($xml, $html_output=false) {
    $xml_obj = new SimpleXMLElement($xml);
    $level = 4;
    $indent = 0; // current indentation level
    $pretty = array();
    
    // get an array containing each XML element
    $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

    // shift off opening XML tag if present
    if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
      $pretty[] = array_shift($xml);
    }

    foreach ($xml as $el) {
      if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
          // opening tag, increase indent
          $pretty[] = str_repeat(' ', $indent) . $el;
          $indent += $level;
      } else {
        if (preg_match('/^<\/.+>$/', $el)) {            
          $indent -= $level;  // closing tag, decrease indent
        }
        if ($indent < 0) {
          $indent += $level;
        }
        $pretty[] = str_repeat(' ', $indent) . $el;
      }
    }   
    $xml = implode("\n", $pretty);   
    return ($html_output) ? htmlentities($xml) : $xml;
}
?>