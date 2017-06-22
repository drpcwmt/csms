<?php
/** Coordinators
*
*/
class Coordinators extends Employers{
	private $thisTemplatePath = 'modules/recources/templates';
	public $db_table = "coordinators";

	public function __construct($id, $sms=''){
		if($sms == ''){
			global $sms;
		}
		$this->sms = $sms;
		$this->db_table = 'coordinators';
		try{	
			parent::__construct($id, $sms->getHrms());
			$this->id = $id;
		} catch(Exception $e) {
			throw new Exception('id not found');	
		}
			
	}

	
	public function getLevelList(){
		if(!isset($this->levels)){
			$levels_arr = array();
			$levels = do_query_array("SELECT DISTINCT levels FROM $this->db_table WHERE id=$this->id", $this->sms->database, $this->sms->ip); 
			if( count($levels)>0){
				foreach($levels as $l){
					$levels_arr[] = new Levels($l->levels, $this->sms);
				}
			}
			$this->levels = $levels_arr;
		}
		return sortArrayOfObjects($levels_arr, getItemOrder('levels'), 'id');
	}
	
	public function getClassList(){
		$levels = $this->getLevelList();
		$classes_arr = array();
		if($levels != false && count($levels)>0){
			foreach($levels as $level){
				$classes = $level->getClassList();
				$classes_arr = array_merge($classes_arr, $classes);
			}
		}
		return sortArrayOfObjects($classes_arr, getItemOrder('classes'), 'id');
	}

	public function removeLevel($level_id){
		$answer = array();
		$sms = $this->sms;
		if(do_query_edit("DELETE FROM coordinators WHERE id=$this->id AND levels=$level_id", $sms->database, $sms->ip)){
			$answer['id'] = $this->id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = "Error";
		}
		return $answer;	

	}

	public function updateForm(){
		global $lang;
		$levels = Levels::getList(true);
		$layout = new Layout($this);
		$coord_level_arr = $this->getLevelList();
		$layout->levels_trs = '';
		foreach($levels as $level){
			$layout->levels_trs .= write_html('tr', '',
				write_html('td', '', '<input type="checkbox" name="level[]"  value="'.$level->id.'" '.(in_array($level, $coord_level_arr) ? 'checked="checked"' : '').'/>').
				write_html('td', '', $level->getName())
			);
		}
		$layout->fieldset_name = 'hidden';
		$layout->template = 'modules/resources/templates/coordinators_new.tpl';
		return $layout->_print();
	}
	
	public function loadLayout(){
		global $lang, $prvlg;
		$layout = new Layout($this);
		$layout->coordinator_name = $this->getName();
		$layout->editable_hidden = $prvlg->_chk('resource_edit_coordinators') ? '' : 'hidden';
		$layout->level_read_hidden = $prvlg->_chk('resource_read_levels') ? '' : 'hidden';
		foreach($this->getLevelList() as $level){
			$level_trs[] = write_html('tr', '',
				( $prvlg->_chk('resource_read_levels')? 
					write_html('td', 'class="unprintable"', 
						write_html('button', 'class="ui-state-default hoverable circle_button"  action="openLevel" levelid="'.$level->id.'"', write_icon('newwin'))
					)
				: '').
				( $prvlg->_chk('resource_edit_coordinators')?
					write_html('td', 'class="unprintable"',
						write_html('button', 'class="ui-state-default hoverable circle_button" action="deleteCoordinatorLevel" levelid="'.$level->id.'" coordinator_id="'.$this->id.'"', write_icon('close')) 
					)
				: '').
				write_html('td', '', $level->getName())
			);
		}
		$layout->level_trs = implode('', $level_trs);
		$layout->toolbox = Resources::getItemsToolbox($this->db_table, $this->id);
		$layout->template = 'modules/resources/templates/coordinators.tpl';
		return $layout->_print();
	}

	static function _new(){
		global $lang;
		$levels = Levels::getList(true);
		$layout = new Layout();
		$layout->levels_trs = '';
		foreach($levels as $level){
			$layout->levels_trs .= write_html('tr', '',
				write_html('td', '', '<input type="checkbox" name="level[]"  value="'.$level->id.'" />').
				write_html('td', '', $level->getName())
			);
		}
		$layout->template = 'modules/resources/templates/coordinators_new.tpl';
		return $layout->_print();
	}
	
	static function _save($post){
		global $sms;
		$result = true;
		if(isset($post['id']) && $post['id'] != ''){
			$id = $post['id'];
			$levels = $post['level'];
			if(do_query_edit("DELETE FROM coordinators WHERE id=$id", $sms->database, $sms->ip)){
				foreach($levels as $level_id){
					$values[] = "($id, $level_id)";
				}
				if(!do_query_edit("INSERT INTO coordinators (id, levels) VALUES ".implode(',', $values), $sms->database, $sms->ip)){
					$result = false;
				}
			}

		}
		if($result!=false){
			$coordinator = new Coordinators($id);
			$answer['id'] = $id;
			$answer['title'] = $coordinator->getName();
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
		if(do_delete_obj("id=$id", "coordinators",  $sms->database, $sms->ip)){
			// delete prof documents and share and user login 
			$user = new Users('coordinator', $id);
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
	
	static function getList(){
		global $sms;
		$out = array();
		$coordinators = do_query_array("SELECT DISTINCT id FROM coordinators", $sms->database, $sms->ip);
		if( count($coordinators)>0){
			foreach($coordinators as $coordinator){
				$out[] = new Coordinators($coordinator->id, $sms);	
			}
		}
		return $out;
	}
	
}