<?php
/** Connection
*
*/

class CSMS{
	public function __construct($conx_id=''){
		global $lang;
		if(is_object($conx_id)){	
			$this->database = isset($conx_id->database) ? $this->database : $this->getThisDB();
			foreach($conx_id as $key =>$value){
				$this->$key = $value;
			}
		} elseif($conx_id != ''){
			$server = do_query_obj("SELECT * FROM connections WHERE id=$conx_id");
			if(isset( $server->id )){
				foreach($server as $key =>$value){
					$this->$key = $value;
				}
				$this->database = $this->getThisDB();
			}	
		} else {
			die('CSMS not defined');
		}
			/*if($this->ping() == false){
				print_r($this);
			}*/
	}
	
	public function getThisDB(){
		if(!isset($this->database)){
			switch($this->type){
				case 'sms'	:
					$this->database = SMS_Database;
					break;
				case 'hrms'	:
					$this->database = HRMS_Database;
					break;
				case 'accms'	:
					$this->database = ACCMS_Database;
					break;
				case 'busms'	:
					$this->database = BUSMS_Database;
					break;
				case 'libms'	:
					$this->database = LIBMS_Database;
					break;
				case 'safems'	:
					$this->database = SAFEMS_Database;
					break;
				case 'storems'	:
					$this->database = STOREMS_Database;
					break;
			}
		}
		return $this->database;
	}
	
	public function getName(){
		if($_SESSION['lang'] == 'ar' && $this->getSettings('school_name_ar')!=''){
			return $this->getSettings('school_name_ar');
		} else {
			return $this->getSettings('school_name');
		}
	}

	public function getCode(){
		return $this->getSettings('sch_code');
	}
	
	public function ping(){
		$opts = array('http' =>
			array(
				'method'  => 'GET',
				'timeout' => 5 
			)
		);
		$context  = stream_context_create($opts);
		$url_data = file_get_contents("http://$this->url/?ping", false, $context);
		//if($url_data != false){
			$result = json_decode($url_data);
			if(isset($result->last_sync) && $result->last_sync != ''){
				return true;
			} else {
				return false;
			}
		/*} else {
			return false;
		}*/
	}
	
	public function getSettings($key){
		if(!isset($this->settings)){
			$this->settings = new stdClass();
			$settings = do_query_array("SELECT * FROM settings", $this->database, $this->ip);
			foreach($settings as $s){
				$k = $s->key_name;
				$val = $s->value;
				$this->settings->$k = $val;
			}
		}
		if(isset($this->settings->$key)){
			return $this->settings->$key;
		} else { 
			//echo $key.'-'.$this->ip;
		}
	}
	
	public function getAnyNameById($con, $con_id){
		global $lang;
		if($con == '0' && $con_id == '0' ){
			return $lang['system'];
		} elseif($con =='etab' && $con_id == '0'){
			return $lang['school'];
		} elseif($con_id == '0'){
			if($con == 'class'){
				return $lang['classes'];
			}
			return $lang[$con.'s'];
		} else {
			try{
				$object = $this->getAnyObjById($con , $con_id);
				return $object->getName() ? $object->getName() : '';
				
			} catch (Exception $e){
				return $lang['undefined'];
			}
		}
	}


	public function setSession($user){
		$_SESSION["user_id"] = $user->user_id;
		$_SESSION["user"] = $user->name;
		$_SESSION["group"] = $user->group;

		//  styles
		$_SESSION["css"] = $user->css != '' ? $user->css : $this->getSettings('def_theme');

		//language 
		$_SESSION["lang"] = $user->def_lang != '' ? $user->def_lang : $this->getSettings('default_lang'); 
		if($_SESSION["lang"] == 'ar') {$_SESSION["dirc"] = "rtl";} else {$_SESSION["dirc"] = "ltr";}
		
		// current year database
		if(isset($_POST['year']) && $_POST['year'] != ''){
			$_SESSION['year'] = $_POST['year'];
			define( 'DB_year', Db_prefix.$_POST['year']);
		} else {
			$cur_year = getNowYear();
			$_SESSION['year'] = $cur_year->year;
			define('DB_year', Db_prefix.$cur_year->year);
		}
		// set last operation
		$_SESSION['last_op'] = time();
	}
	
	public function getLogo(){
		$url = 'http://'.$this->url."/attachs/img/logo.png";
		$file_header = @get_headers($url);
		if(stripos($file_header[0],"200 OK")){
			return "index.php?plugin=img_resize&path=attachs/img/logo.png&w=200";
		} else {
			return "index.php?plugin=img_resize&path=assets/img/logo.png&w=200";
		}
	}
	
	public function loadJsonSettings(){
		$config = array();	
		$config['MS_codeName'] = $this->type.'-'.$this->getSettings('sch_code');
		$config['debugMode'] = $this->getSettings('debug_mode');
		$config['maxUpload'] = max_size_upload;
		$config['maxExec'] = ini_get('max_execution_time');
		$config['database'] = $this->getThisDB();
		$config['uilang'] = (isset($_SESSION['lang']) ? $_SESSION['lang'] : $this->getSettings('default_lang'));
		if(defined("MapsApiKey")){
			$config['MapsApiKey'] = MapsApiKey;
		}
		return $config;
	}

	public function loadConnectionsSettings(){
		$sch_code = $this->getSettings('sch_code'); // just to be sure to retruve variable settings
		$servers_layout = new Layout($this->settings);
			// Hr Server Connection 
		$servers_layout->hrms_server_on = ($this->getSettings('hrms_server') == 1 ? 'checked="checked"' : '');
		$servers_layout->hrms_server_off = ($this->getSettings('hrms_server') != 1 ? 'checked="checked"' : '');
		$servers_layout->hrms_server_ip_disabled = ($this->getSettings('hrms_server') != 1 ? 'disabled="disabled"' : '');
		$servers_layout->hrms_server_name_disabled = ($this->getSettings('hrms_server') != 1 ? 'disabled="disabled"' : '');
		
			// Bus Server Connection 
		$servers_layout->busms_server_on = ($this->getSettings('busms_server') == 1 ? 'checked="checked"' : '');
		$servers_layout->busms_server_off = ($this->getSettings('busms_server') != 1 ? 'checked="checked"' : '');
		$servers_layout->busms_server_ip_disabled = ($this->getSettings('busms_server') != 1 ? 'disabled="disabled"' : '');
		$servers_layout->busms_server_name_disabled = ($this->getSettings('busms_server') != 1 ? 'disabled="disabled"' : '');
		
			// Lib Server Connection 
		$servers_layout->libms_server_on = ($this->getSettings('libms_server') == 1 ? 'checked="checked"' : '');
		$servers_layout->libms_server_off = ($this->getSettings('libms_server') != 1 ? 'checked="checked"' : '');
		$servers_layout->libms_server_ip_disabled = ($this->getSettings('libms_server') != 1 ? 'disabled="disabled"' : '');
		$servers_layout->libms_server_name_disabled = ($this->getSettings('libms_server') != 1 ? 'disabled="disabled"' : '');
		
			// ACC Server Connection 
		$servers_layout->accms_server_on = ($this->getSettings('accms_server') == 1 ? 'checked="checked"' : '');
		$servers_layout->accms_server_off = ($this->getSettings('accms_server') != 1 ? 'checked="checked"' : '');
		$servers_layout->accms_server_ip_disabled = ($this->getSettings('accms_server') != 1 ? 'disabled="disabled"' : '');
		$servers_layout->accms_server_name_disabled = ($this->getSettings('accms_server') != 1 ? 'disabled="disabled"' : '');
		
			// Safe Server Connection 
		$servers_layout->safems_server_on = ($this->getSettings('safems_server') == 1 ? 'checked="checked"' : '');
		$servers_layout->safems_server_off = ($this->getSettings('safems_server') != 1 ? 'checked="checked"' : '');
		$servers_layout->safems_server_ip_disabled = ($this->getSettings('safems_server') != 1 ? 'disabled="disabled"' : '');
		$servers_layout->safems_server_name_disabled = ($this->getSettings('safems_server') != 1 ? 'disabled="disabled"' : '');
		

		$servers_layout->servers_table = '';
		if(!in_array($this->type, array('sms', 'hrms'))){
			$systems = do_query_array("SELECT * FROM connections ORDER BY type ASC", $this->database, $this->ip);
			$servers_layout->servers_table = Connections::loadConxTable($systems);
		}
		$servers_layout->template = "modules/connections/templates/connection-$this->type.tpl";
		return $servers_layout->_print();
	}
	
	// This Year settings 
	public function getYearSetting($key_name){ // from student_year settings table
		if(!isset($this->cur_year)){
			$this->cur_year = do_query_obj("SELECT * FROM years WHERE year=".$_SESSION['year'], $this->database, $this->ip);	
		}
		if ($this->cur_year != false && $this->cur_year->$key_name !='') {
			return $this->cur_year->$key_name;
		} else {
			return false;	
		}
	}
	
	// Years 
	public function getYearsArray(){
		$years = do_query_array("SELECT year FROM years ORDER BY year DESC", $this->database, $this->ip);
		$out = array();
		if($years != false){
			foreach ($years as $year) {
				$out[] =  $year->year;
			}
		}
		return $out ;
	}

	public function getItemOrder($items){
		$query = do_query_obj("SELECT `order` FROM items_order WHERE items='$items'",  $this->database, $this->ip);
		if($query!= false && $query->order != ''){
			$order = strpos($query->order, ',') !== false ? explode(',', $query->order) : array($query->order);
			return $order;
		} else {
			return array();
		}
	}
	
	public function getHrms(){
		if(!isset($this->hrms)){
			$this->hrms = new HrMS();
		}
		return $this->hrms;
	}

	public function getSafems(){
		if(!isset($this->safems)){
			$this->safems = new SafeMS();
		}
		return $this->safems;
	}
	
	public function getAccms(){
		if(!isset($this->accms)){
			$this->accms = new AccMS();
		}
		return $this->accms;
	}

	public function getBusms(){
		if(!isset($this->busms)){
			if($this->type == 'accms' ){
				$buss = BusMs::gelList();
				$this->busms = reset($buss);
			} elseif($this->type == 'sms' ){
				if($this->getSettings('busms_server') == '1'){
					$b = new stdClass();
					$b->database = BUSMS_Database;
					$b->ip = $this->getSettings('busms_server_ip');
					$b->url = $this->getSettings('busms_server_name');
					$this->busms = new BusMS($b);
					
				} else {
					$b = new stdClass();
					$b->database = $this->database;
					$b->ip = $this->ip;
					$b->url = $_SERVER['HTTP_HOST'];
					$this->busms = new BusMS($b);
				}
			} else {
				$this->busms = false;
			}
		}
		return $this->busms;
	}
	
	public function getLibms(){
		if(!isset($this->libms)){
			$this->libms = new LibMS();
		}
		return $this->libms;
	}
	
}
?>