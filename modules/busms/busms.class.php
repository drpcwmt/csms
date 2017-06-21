<?php
/** BusMS connection
*
*/

class BusMS extends CSMS{

	public $type = 'busms';
	
	private $thisTemplatePath = 'modules/accms/templates';

	public function __construct($id=''){
		if($id != ''){
			if(is_object($id)){
				$this->ip = $id->ip;
				$this->database = $id->database;
				$this->url = $id->url;	
				parent::__construct($this);
			} else {
				parent::__construct($id);
			}
		} else {
			global $this_system;
			if(MS_codeName == 'busms'){
				$this->ip = '127.0.0.1';
				$this->url= 'localhost';
				$this->database = BUSMS_Database;
				$this->id = 0;
				parent::__construct($this);
				
			} else {
				global $this_system;
				$this->url = $this_system->getSettings('busms_server_name');
				$this->ip =$this_system->getSettings('busms_server_ip');
				$this->database = BUSMS_Database;	
				$this->id = 0;
				parent::__construct($this);
			}
		}
		//$this->full_code = $this->getSettings('this_acc_code');
	}
	
	static function getList(){
		global $this_system;
		//$out = array();
		$busmss = do_query_array("SELECT * FROM connections WHERE type = 'busms'", $this_system->database, $this_system->ip);
		return $busmss;
	}
	
	public function getAnyObjById($con , $con_id){
		$result = false;
		try{
			switch($con){
				case  "route" :
					$result = new Route($con_id);
				break;
				case  "bus" :
					$result = new Bus($con_id);
				break;
				case  "matrons" :
					$result = new Employers($con_id);
				break;
				case  "drivers" :
					$result = new Employers($con_id);
				break;
				default :
					$result = new Employers($con_id);
				break;
			}
			return $result;
		} catch (Exception $e){
			//echo $e;
			return false;
		}
	}
	
	
	public function getGroups(){
		if(!isset($this->groups)){
			$this->groups =GroupRoutes::getList();
		} 
		return $this->groups;
	}
	
	
}