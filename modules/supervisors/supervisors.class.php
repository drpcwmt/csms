<?php
/** Supervisors
*
*/
class Supervisors extends Employers{
	private $thisTemplatePath = 'modules/recources/templates';

	public function __construct($id, $sms=''){
		if($sms == '' ){
			global $this_system;
			$sms = $this_system;
		}
		$this->sms = $sms;
		if($id != ''){	
			parent::__construct($id, $sms->getHrms());
			$supervisor = do_query_obj("SELECT * FROM supervisors WHERE id=$id", $sms->database, $sms->ip);	
			if(isset($supervisor->id)){
				foreach($supervisor as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	public function getClassList(){
		$classes_arr = array();
		$masters = do_query_array("SELECT * FROM supervisors WHERE id=".$this->id, $this->sms->database, $this->sms->ip); 
		if( count($masters)>0){
			print_r($masters);
			foreach($masters as $master){
				$service_id = $master->services;
				$classes = do_query_array("SELECT class_id FROM materials_classes WHERE services=$service_id" , Db_prefix.$_SESSION['year']);
				foreach($classes as $class  ){
					$classes_arr[] = new Classes($class->class_id);
				}
			}
		}
		return sortArrayOfObjects($classes_arr, getItemOrder('classes'), 'id');
	}
	
	public function getServices(){
		$out= array();
		$services= do_query_array("SELECT services FROM supervisors WHERE services!=0 AND id =".$this->id, $this->sms->database, $this->sms->ip);
		if(count($services) > 0){
			foreach($services as $service){
				$ser = new services( $service->services);
				if(isset($ser->id)){
					$out[] = $ser;
				}
			}
			return sortArrayOfObjects($out, getItemOrder('material'), 'mat_id');
		} else {
			return false;
		}
	}

	public function loadLayout(){
		$layout = $this;
		$layout->super_name = $this->getName();
		$serviceManager = new ServicesManager('supervisor', $this->id);
		$layout->service_table = $serviceManager->loadLayout();
		$layout->toolbox = Resources::getItemsToolbox('supervisors', $this->id);
		unset($layout->services);
		return fillTemplate('modules/resources/templates/supervisors.tpl', $layout);
	}
	
	static function getList(){
		global $sms;
		$out = array();
		$supervisors = do_query_array("SELECT DISTINCT id FROM supervisors", $sms->database, $sms->ip);
		foreach($supervisors as $supervisor){
			$out[] = new Supervisors($supervisor->id);	
		}
		return $out;
		//return sortArrayOfObjects($out, getItemOrder('supervisors'), 'id');
	}
	
	static function _save($post){
		global $sms;
		$id = $post['id'];
		$service = isset($post['services']) ? $post['services'] : 'NULL';
		if(do_query_edit("INSERT INTO supervisors (id, services) VALUES( $id, '$service')", $sms->database, $sms->ip)){
			$supervisor = new Supervisors($id);
				// create user login
			$user = new Users('supervisor', $id);
			$answer['id'] = $id;
			$answer['title'] = $supervisor->getName();
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	
	}

	static function _new(){
		return fillTemplate('modules/resources/templates/supervisors_new.tpl', array());
	}

	static function _delete($id){
		global $sms;
		if(do_query_edit("DELETE FROM supervisors WHERE id=$id", $sms->database, $sms->ip)){
			// delete Supervisor documents and share and user login 
			$user = new Users('supervisor', $id);
			$user->_delete();
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}

		
}
?>