<?php
/** Eatblissement
*
*/

class Etabs{
	private $thisTemplatePath = 'modules/recources/templates';

	public function __construct($id, $sms=''){
		if($sms == '' ){
			global $this_system;
			$sms = $this_system;
		}
		$this->sms = $sms;
		if($id != ''){	
			$etab = do_query_obj("SELECT * FROM etablissement WHERE id=$id", $sms->database, $sms->ip);	
			if(isset($etab->id)){
				foreach($etab as $key =>$value){
					$this->$key = $value;
				}
			}	
		}
			
	}
	
	public function getName($other_lang = false){
		if($other_lang == false){
			return $_SESSION['lang'] == 'ar' ? $this->name_rtl : $this->name_ltr ;
		} else {
			return $_SESSION['lang'] == 'ar' ? $this->name_ltr : $this->name_rtl ;
		}
	}
	
	static function getList(){
		global $sms;
		$out = array();
		$etabs = do_query_array("SELECT id FROM etablissement", $sms->database, $sms->ip);
		foreach($etabs as $etab){
			$out[] = new Etabs($etab->id, $sms);	
		}
		
		return sortArrayOfObjects($out, $sms->getItemOrder('etabs'), 'id');
	}
	
	public function getLevelList(){
		if(!isset($this->levels)){
			$out = array();
			$levels = Levels::getList();
			foreach($levels as $level){
				if($level->etab_id == $this->id){
					$out[] = new Levels($level->id, $this->sms);
				}
			}
			$this->levels= $out;
		}
		return  sortArrayOfObjects($this->levels, $this->sms->getItemOrder('levels'), 'id');;
	}
	
	public function getClassList(){
		$out = array();
		$levels = $this->getLevelList();
		foreach($levels as $level){
			$out = array_merge($out, $level->getClassList());
		}
		return sortArrayOfObjects($out, $this->sms->getItemOrder('classes-'.$_SESSION['year']), 'id');
	}

	public function getGroupList(){
		$out = array();
		$groups = do_query_array("SELECT * FROM groups WHERE `parent`='etab' AND parent_id=$this->id", $this->sms->db_year, $this->sms->ip);
		foreach($groups as $group){
			$out[] = new Groups($group->id, $this->sms);
		}
		return sortArrayOfObjects($out, $this->sms->getItemOrder('etab-groups-'.$_SESSION['year']), 'id');
	}
	
	public function getServices(){
		$out = array();
		$levels = $this->getLevelList();
		foreach($levels as $level){
			$out = array_merge($out, $level->getServices());
		}
		return Services::orderService($out);
	}

	public function getStudents(){
		if(!isset($this->students)){
			$out = array();
			$levels = $this->getLevelList();
			foreach($levels as $level){
				$out = array_merge($out, $level->getStudents());
			}
			$this->students = $out;
		}
		return $this->students;
	}
		
	static function _save($post){
		global $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			$result = do_update_obj($post, 'id='.$post['id'], 'etablissement', $sms->database, $sms->ip);
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'etablissement', $sms->database, $sms->ip);
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
		global $sms;
		if(do_query_edit("DELETE FROM etablissement WHERE id=$id", $sms->database, $sms->ip)){
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