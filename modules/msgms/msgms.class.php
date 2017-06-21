<?php
/** BusMS connection
*
*/

class MsgMS {

	public $type = 'msgms';
	
	private $thisTemplatePath = 'modules/msgms/templates';

	public function __construct(){
		global $this_system;
		$this->url = $this_system->getSettings('msgms_server_name');
		$this->ip =$this_system->getSettings('msgms_server_ip');
		$this->database = MSGMS_Database;	
	}
	
	
	public function getAnyObjById($con , $con_id){
		$result = false;
		try{
			switch($con){
				case  "user" :
					$result = new Useer($con_id);
				break;
				case  "cc" :
					$result = new CostCenters($con_id);
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