<?php
/** AccMS connection
*
*/

class AccMs extends CSMS{
	
	public $type = 'accms';

	public function __construct($id=''){
		if(MS_codeName == 'accms'){
			$this->ip = '127.0.0.1';
			$this->url=  $_SERVER['HTTP_HOST'];
			$this->database = ACCMS_Database;
			parent::__construct($this);
			
		} else {
			global $this_system;
			$this->url = $this_system->getSettings('accms_server_name');
			$this->ip =$this_system->getSettings('accms_server_ip');
			parent::__construct($this);		
		}
	}
	
	public function getBanks(){
		$accs = do_query_array("SELECT * FROM sub_codes WHERE main='161' OR main LIKE '161%'", $this->database, $this->ip);
		return $accs; 
	}
	
	public function loadLayout(){
		$layout = $this;
		$layout->hrms_id = $this->id;
		//$layout->search_form = fillTemplate('modules/salary/templates/student_search.tpl', $layout);

	}
	public function getAnyObjById($con , $con_id){
		$result = false;
		try{
			switch($con){
				case  "accounts" :
					$result = new Account($con_id);
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

}

?>