<?php
/** StoreMS connection
*
*/

class StoreMS extends CSMS{
	public $type = 'storems';
	
	private $thisTemplatePath = 'modules/storems/templates';

	public function __construct($id=''){
		if($id != ''){
			parent::__construct($id);
		} else {
			if(MS_codeName == 'storems'){
				$this->ip = '127.0.0.1';
				$this->url= '';
				parent::__construct($this);
				
			} else {
				global $this_system;
				$this->url = $this_system->getSettings('storems_server_name');
				$this->ip =$this_system->getSettings('storems_server_ip');
				parent::__construct($this);		
			}
		}
		//$this->full_code = $this->getSettings('this_acc_code');
	}
	
	static function getList(){
		global $this_system;
		//$out = array();
		$storemss = do_query_array("SELECT * FROM connections WHERE type = 'storems'", $this_system->getThisDB(), $this_system->ip);
		return $storemss;
	}
	
	public function getAnyObjById($con , $con_id){
		$result = false;
		try{
			switch($con){
				case  "route" :
					$result = new Product($con_id);
				break;
				case  "bus" :
					$result = new Branch($con_id);
				break;
				case  "matrons" :
					$result = new Categorys($con_id);
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
	}}

