<?php
/** HrMS connection
*
*/

class HrMS extends CSMS{
	
	public $type = 'hrms';

	public function __construct($id=''){
		if($id != ''){
			parent::__construct($id);
		} else {
			if(MS_codeName == 'hrms'){
				$this->ip = '127.0.0.1';
				$this->url= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
				$this->database = HRMS_Database;
				parent::__construct($this);
				$this->full_code = '152'.$this->getCC().'000000';
			} else {
				global $this_system;
				$this->url = $this_system->getSettings('hrms_server_name');
				$this->ip =$this_system->getSettings('hrms_server_ip');
				$this->database = HRMS_Database;
				parent::__construct($this);			
			}
		}
	}
	
	
	public function getAccCode(){
		return '151'.$this->getCC();
	}

	public function getCC(){
		return $this->getSettings('this_ccid');
	}

	static function getList(){
		global $this_system;
		$out = array();
		$hrmss = do_query_array("SELECT * FROM connections WHERE type = 'hrms'", $this_system->database, $this_system->ip);
		foreach($hrmss as $hrms){
			$out[] = new HRMS($hrms->id);	
		}
		return $out;
				
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
	
	public function getSystemTab(){
		return '';
	}
		
}

?>