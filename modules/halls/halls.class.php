<?php
/** Halls
*
*/

class Halls{
	private $thisTemplatePath = 'modules/recources/templates';

	public function __construct($id){
		global $sms;
		$this->database = $sms->database;
		$this->ip = $sms->getSettings('tools_server_ip');
		if($id != ''){	
			$hall = do_query_obj("SELECT * FROM halls WHERE id=$id", $this->database, $this->ip);	
			if(isset($hall->id)){
				foreach($hall as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	public function getName(){
		
		return isset($this->name) ? $this->name : '';
	}
	
	public function loadLayout(){
		$layout = $this;
		$layout->toolbox = Resources::getItemsToolbox('halls', $this->id);

		return fillTemplate('modules/resources/templates/halls.tpl', $layout);
	}
	
	static function getList(){
		global $sms;
		$out = array();
		$halls = do_query_array("SELECT id FROM halls", $sms->database, $sms->getSettings('tools_server_ip'));
		foreach($halls as $hall){
			$out[] = new Halls($hall->id);	
		}
		
		return sortArrayOfObjects($out, getItemOrder('halls'), 'id');
	}

	static function _save($post){
		global $lang, $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			$result = do_update_obj($post, 'id='.$post['id'], 'halls', $sms->database, $sms->getSettings('tools_server_ip'));
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'halls', $sms->database, $sms->getSettings('tools_server_ip'));
		}
		if($result!=false){
			$answer['id'] = $result;
			$answer['title'] = $post['name'];
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}

	static function _delete($id){
		global $lang, $sms;
		if(do_query_edit("DELETE FROM halls WHERE id=$id", $sms->database, $sms->getSettings('tools_server_ip'))){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}
	
	static function _new(){
		return fillTemplate('modules/resources/templates/halls_new.tpl', array());
	}
	
}
?>