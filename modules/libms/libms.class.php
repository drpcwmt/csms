<?php
/** LibMS connection
*
*/

class LibMS extends CSMS{
	
	public $type = 'libms';

	public function __construct($id=''){
		if($id != ''){
			parent::__construct($id);
		} else {
			if(MS_codeName == 'libms'){
				$this->ip = '127.0.0.1';
				$this->url= '';
				$this->database = LIBMS_Database;
				parent::__construct($this);
				
			} else {
				global $this_system;
				$this->url = $this_system->getSettings('libms_server_name');
				$this->ip =$this_system->getSettings('libms_server_ip');
				$this->database = LIBMS_Database;	
				parent::__construct($this);		
			}
		}
		//$this->full_code = $this->getSettings('this_acc_code');
	}

	static function getList(){
		global $this_system;
		//$out = array();
		$libmss = do_query_array("SELECT * FROM connections WHERE type = 'libms'", $this_system->getThisDB(), $this_system->ip);
		return $libmss;
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
				case  "books" :
					$result = new Books($con_id);
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