<?php
/** Tools
*
*/

class Tools{
	private $thisTemplatePath = 'modules/recources/templates';

	public function __construct($id){
		global $sms;
		$this->database = $sms->database;
		$this->ip = $sms->getSettings('tools_server_ip');
		if($id != ''){	
			$tool = do_query_obj("SELECT * FROM tools WHERE id=$id", $this->database, $this->ip);	
			if(isset($tool->id)){
				foreach($tool as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function loadLayout(){
		$layout = $this;
		$layout->toolbox = Resources::getItemsToolbox('tools', $this->id);
		return fillTemplate('modules/resources/templates/tools.tpl', $layout);
	}
	
	static function getList(){
		global $sms;
		$out = array();
		$tools = do_query_array("SELECT id FROM tools",  $sms->database, $sms->getSettings('tools_server_ip'));
		foreach($tools as $tool){
			$out[] = new Tools($tool->id);	
		}
		
		return sortArrayOfObjects($out, getItemOrder('tools'), 'id');
	}

	static function _save($post){
		global $sms, $lang;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			$result = do_update_obj($post, 'id='.$post['id'], 'tools',  $sms->database, $sms->getSettings('tools_server_ip'));
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'tools',  $sms->database, $sms->getSettings('tools_server_ip'));
		}
		if($result!=false){
			$answer['id'] = $result;
			$answer['title'] = $post['name'];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}

	static function _delete($id){
		global $sms, $lang;
		if(do_query_edit("DELETE FROM tools WHERE id=$id",  $sms->database, $sms->getSettings('tools_server_ip'))){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}
	
	static function _new(){
		return fillTemplate('modules/resources/templates/tools_new.tpl', array());
	}
}
?>