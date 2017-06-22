<?php
/** Units AKA summarys
*
*/

class Units {
	
	public function __construct($id){
		if($id != ''){	
			$unit = do_query_obj("SELECT * FROM summarys WHERE id=$id", LMS_Database);	
			if(isset($unit->id)){
				foreach($unit as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}
	
	public function getName(){
		return $this->title;
	}
	
	
	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			$result = do_update_obj($post, 'id='.$post['id'], 'summarys', LMS_Database);
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'summarys', LMS_Database);
		}
		if($result!=false){
			$answer['id'] = $result;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}

	static function _delete($id){
		if(do_query_edit("DELETE FROM summarys WHERE id=$id", LMS_Database)){
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