<?php
/** Principals
*
*/

class Principals extends Employers{
	private $thisTemplatePath = 'modules/recources/templates';
	public $db_table;
	public function __construct($id, $sms=''){
		if($sms == '' ){
			global $sms;
		}
		$this->sms = $sms;
		$this->db_table = 'principals';
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
		if(do_query_edit("DELETE FROM $this->db_table WHERE id=$this->id AND levels=$level_id", $sms->database, $sms->ip)){
			$answer['id'] = $this->id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = "Error";
		}
		return $answer;	

	}
	
	public function loadLayout(){
		global $lang, $prvlg;
		$layout = new Layout($this);
		$layout->principal_name = $this->getName();
		$layout->editable_hidden = $prvlg->_chk('resource_edit_principals') ? '' : 'hidden';
		$layout->level_read_hidden = $prvlg->_chk('resource_read_levels') ? '' : 'hidden';
		foreach($this->getLevelList() as $level){
			$level_trs[] = write_html('tr', '',
				( $prvlg->_chk('resource_read_levels')? 
					write_html('td', 'class="unprintable"', 
						write_html('button', 'class="ui-state-default hoverable circle_button"  action="openLevel" levelid="'.$level->id.'"', write_icon('newwin'))
					)
				: '').
				( $prvlg->_chk('resource_edit_principals')?
					write_html('td', 'class="unprintable"',
						write_html('button', 'class="ui-state-default hoverable circle_button" action="deletePrincipalLevel" levelid="'.$level->id.'" principalid="'.$this->id.'"', write_icon('close')) 
					)
				: '').
				write_html('td', '', $level->getName())
			);
		}
		$layout->level_trs = implode('', $level_trs);
		$layout->toolbox = Resources::getItemsToolbox($this->db_table, $this->id);
		$layout->template = 'modules/resources/templates/principals.tpl';
		return $layout->_print();
	}
	
	public function updateForm(){
		global $lang;
		$levels = Levels::getList(true);
		$layout = new Layout($this);
		$princ_level_arr = $this->getLevelList();
		$layout->levels_trs = '';
		foreach($levels as $level){
			$layout->levels_trs .= write_html('tr', '',
				write_html('td', '', '<input type="checkbox" name="level[]"  value="'.$level->id.'" '.(in_array($level, $princ_level_arr) ? 'checked="checked"' : '').'/>').
				write_html('td', '', $level->getName())
			);
		}
		$layout->fieldset_name = 'hidden';
		$layout->template = 'modules/resources/templates/principals_new.tpl';
		return $layout->_print();
	}

	static function getList(){
		$out = array();
		$principals = do_query_array("SELECT DISTINCT id FROM principals", DB_student);
		if( count($principals)>0){
			foreach($principals as $principal){
				$out[] = new Principals($principal->id);	
			}
		}
		return $out;
		//return sortArrayOfObjects($out, getItemOrder('principals'), 'id');
	}

	static function _new(){
		global $lang;
		$levels = Levels::getList(true);
		$levels_trs = '';
		foreach($levels as $level){
			$levels_trs .= write_html('tr', '',
				write_html('td', '', '<input type="checkbox"  value="'.$level->id.'" />').
				write_html('td', '', $level->getName())
			);
		}
		echo write_html('form', 'id="new_resource_form" class="ui-state-highlight ui-corner-all" style="padding:5px"',
			'<input type="hidden" class="colectChkox_value" name="levels" value="" />'.
			write_html('table', 'width="100%" border="0" cellspacing="0"',
				write_html('tr', '',
					write_html('td', 'width="120" valign="middel"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
					).
					write_html('td', '',
						'<input type="text" id="new_employer_name" class="input_double required" />'.
						'<input type="hidden" class="autocomplete_value" name="id" id="new_principal_id" />'
					)
				).
				write_html('table', 'class="tablesorter chkTable"',
					write_html('thead', '',
						write_html('tr', '',
							write_html('th', 'width="16" class="bgnone"', '&nbsp;').
							write_html('th', '', $lang['levels'])
						)
					).
					write_html('tbody', '', $levels_trs)
				)
			)
		);
	}
	
	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			$id = $post['id'];
			$levels = explode(',', $post['levels']);
			if(do_query_edit("DELETE FROM principals WHERE id=$id", DB_student)){
				foreach($levels as $level_id){
					$values[] = "($id, $level_id)";
				}
				$result = do_query_edit("INSERT INTO principals (id, levels) VALUES ".implode(',', $values), DB_student);
			}

		}
		if($result!=false){
			$principal = new Principals($id);
				// create user login
			$user = new Users('principal', $id);
			$answer['id'] = $id;
			$answer['title'] = $principal->getName();
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}
	
	static function _delete($id){
		if(do_query_edit("DELETE FROM principals WHERE id=$id", DB_student)){
			// delete prof documents and share and user login 
			$user = new Users('principal', $id);
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