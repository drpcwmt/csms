<?php
/** Cost Center main Class
*
*/
class CostcentersGroup{
	public $id, $title, $notes;
	
	public function __construct($id){
		global $accms;
		$cc_q = do_query_obj("SELECT * FROM cc_groups WHERE id='$id'", $accms->database, $accms->ip);
		if($cc_q != false && $cc_q->id != ''){
			$this->id =  $cc_q->id;
			$this->title = $cc_q->title;
			$this->notes =  $cc_q->notes;
		} 
		$this->accms = $accms;
	}
	
	public function getName(){
		return $this->title;
	}
	
	public function getMembers(){
		global $accms;
		$cc_q = do_query_array("SELECT * FROM cc_groups_divisions WHERE group_id='$this->id'", $accms->database, $accms->ip);
		$out = array();
		if($cc_q != false){
			foreach($cc_q as $cc){
				$out[] = new Costcenters($cc->cc_id);
			}
		} 
		return $out;
	}
	
	static function getList(){
		global $accms;
		$cc_q = do_query_array("SELECT * FROM cc_groups", $accms->database, $accms->ip);
		$out = array();
		if($cc_q != false){
			foreach($cc_q as $cc){
				$out[] = new CostcentersGroup($cc->id);
			}
		} 
		return $out;
	}
	
	static function getListTr(){
		$groups = CostcentersGroup::getList();
		$ccs = Costcenters::getList();
		$trs = '';
		foreach($groups as $group){
			$tds = array();
			$mm = array();
			$members_arr = objectsToArray($group->getMembers());
			foreach($ccs as $cc){
				if(array_key_exists($cc->id, $members_arr)){
					//$mm[] = $m->title;
					$div = do_query_obj("SELECT value FROM cc_groups_divisions WHERE group_id=$group->id AND cc_id=$cc->id");
				} else {
					$div = false;
				}
				$tds[] = write_html('td', 'align="center"',
					($div != false ? $div->value : '&nbsp;')
				);
			}
			$row = new Layout($group);
			$row->tds = implode('', $tds);
			$row->template = 'modules/costcenters/templates/costcentergroup_list_rows.tpl';
			$row->members = implode(', ', $mm);
			$trs .= $row->_print();
		}
		return $trs;
	}
	
	static function getListOpts(){
		global $accms;
		$out = array();
		$ccs = do_query_array("SELECT id, title FROM cc_groups", $accms->database, $accms->ip);
		foreach($ccs as $cc){
			$out[$cc->id] = $cc->title;	
		}		
		return $out;
	}
	
	static function _save($post){
		global $accms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'cc_groups', $accms->database, $accms->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(!isset($post['id']) || $post['id'] == ''){
			$result = do_insert_obj($post, 'cc_groups', $accms->database, $accms->ip);
			$id = $result;
		}

		if($result!=false){
			do_delete_obj("group_id=$id", 'cc_groups_divisions', $accms->database, $accms->ip);
			if(isset($post['cc'])){
				for($i=0; $i<count($post['cc']); $i++){
					$cc_id = $post['cc'][$i];
					$cc = array(
						'group_id'=> $id,
						'cc_id'=>$cc_id,
						'value'=> $post['division'] == '0' ? (1/count($post['cc'])) : $post["division_value-$cc_id"]
					);
					do_insert_obj($cc, 'cc_groups_divisions', $accms->database, $accms->ip);
				}
			}
				
			$answer['id'] = $id;
			$answer['title'] = $post['title'];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function loadNewForm(){
		$layout = new stdClass();
		$ccs = Costcenters::getList();
		$layout->cc_opts ='';
		$division = Costcenters::getDivision();
		foreach($ccs as $cc){
			$layout->cc_opts .= write_html('li', 'class="hoverable ui-state-default ui-corner-all"',
				'<input type="checkbox" name="cc[]" value="'.$cc->id.'" module="accounts" update="checkAccCC"  />'.
				$cc->title.
				'<input type="text" name="division_value-'.$cc->id.'" class="hidden input_half rev_float" value="'.$division[$cc->id].'"/>'
			);
		}

		return fillTemplate('modules/costcenters/templates/costcentersgroup_details.tpl', $layout);	
	}
	
	
}
