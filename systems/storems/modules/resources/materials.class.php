<?php
/** Materials
*
*/
class Materials{

	public function __construct($id, $sms=''){
		global $lang;
		if($sms == ''){
			global $sms;
		}
		$this->sms = $sms;
		if($id != ''){	
			$material = do_query_obj("SELECT * FROM materials WHERE id=$id", $sms->database, $sms->ip);	
			if(isset($material->id)){
				foreach($material as $key =>$value){
					$this->$key = $value;
				}
			}	
		} else { }
			
	}
	
	public function getName($other_lang = false){
		if($other_lang == false){
			return $_SESSION['lang'] == 'ar' ? $this->name_rtl : $this->name_ltr ;
		} else {
			return $_SESSION['lang'] == 'ar' ? $this->name_ltr : $this->name_rtl ;
		}
	}
	
	public function getServices(){
		if(!isset($this->services)){
			$out = array();
			$services = do_query_array("SELECT id FROM services WHERE mat_id=$this->id", $this->sms->db_year, $this->sms->ip);
			foreach($services as $service){
				$out[] = new Services($service->id);
			}
			$this->services = $out;
		}
		return $this->services;	 
	}
	
	public function getGroup(){
		return do_query_obj("SELECT * FROM materials_groups WHERE id=$this->group_id",  $this->sms->database, $this->sms->ip);
	}


	static function getList(){
		$out = array();
		$materials = do_query_array("SELECT id FROM materials", DB_student);
		foreach($materials as $material){
			$out[] = new Materials($material->id);	
		}
		
		return sortArrayOfObjects($out, getItemOrder('materials'), 'id');
	}
	
	static function _new(){
		global $lang;
		$form = new Layout();
		$form->template = 'modules/resources/templates/materials_new.tpl';
		$groups = Materials::getMaterialsGroupList();
		$form->groups_opts = write_select_options($groups);
		$form->colors_opts = write_select_options(getColorPicker());
		return $form->_print();
	}
	
	static function _delete($id){
		if(do_query_edit("DELETE FROM materials WHERE id=$id", DB_student)){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}
	
	public function loadLayout(){
		global $sms;
		$layout = new Layout($this);
		$materials_groups = Materials::getMaterialsGroupList();
		$layout->materials_groups = write_select_options( $materials_groups, $this->group_id, false);
		$layout->color_picker_pallette =  write_select_options( getColorPicker(), $this->color, false);
		if($sms->getSettings('ig_mode') == '0'){
			$serviceManager = new ServicesManager('material', $this->id);
			$layout->service_table = $serviceManager->loadLayout();
			$layout->toolbox = Resources::getItemsToolbox('materials', $this->id);
			$layout->items = implode('', $this->loadSkillsLayout());
			return fillTemplate('modules/resources/templates/materials.tpl', $layout);
		} else {
			$layout->service_table = ServicesIG::loadMainLayout($this->id);
			$layout->toolbox = Resources::getItemsToolbox('materials', $this->id);
			$layout->items = implode('', $this->loadSkillsLayout());
			return fillTemplate('modules/resources/templates/materials.tpl', $layout);
		}
	}
	
	static function getMaterialsGroupList(){
		global $sms;
		$out = array();
		$field = 'name_'.$_SESSION['dirc'];
		$groups = do_query_array("SELECT id, $field AS name FROM materials_groups", $sms->database, $sms->ip);
		foreach($groups as $gr){
			$out[$gr->id] = $gr->name;
		}
		return $out;
	}
	
	static function _save($post){
		global $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'materials', $sms->database, $sms->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'materials', $sms->database, $sms->ip);
			$id = $result;
		}

		if($result!=false){
			$answer['id'] = $id;
			$answer['title'] = $post['name_'.$_SESSION['dirc']];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	/************** Subs ***********************/
	public function getSubs(){
		global $this_system;
		return do_query_array("SELECT * FROM materials_subs WHERE mat_id=$this->id ORDER BY title ASC");			
	}

	static function saveSub($post){
		global $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'materials_subs') != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id']) && $post['id'] == ''){
			$result = do_insert_obj($post, 'materials_subs');
			$id = $result;
		}
		$answer = array();

		if($result!=false){
			$answer['id'] = $id;
			$answer['item'] = write_html('h3', '', 
				write_html('a', 'href="#"',
					$_POST['title'].
					write_html('button', 'type="button" module="resources" action="addSkill" sub_id="'.$id.'" class="ui-state-default hoverable circle_button"', write_html('b', '', '+'))
				)
			).
			write_html('div', '','');
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode_result($answer);
	}
	
	static function newSkill($sub_id){
		$layout = new Layout();
		$layout->template = "modules/services/templates/skills.tpl";
		$layout->sub_id = $sub_id;
		return $layout->_print();
	}
	
	static function saveSkill($post){
		global $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'materials_skills') != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id']) && $post['id'] == ''){
			$result = do_insert_obj($post, 'materials_skills');
			$id = $result;
		}

		if($result!=false){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);

	}
	
	static function editSkill($skill_id){
		$skill = new Skills($skill_id);
		$layout = new Layout($skill);
		$layout->template = "modules/services/templates/skills.tpl";
		return $layout->_print();
	}


	public function loadSkillsLayout(){
		global $this_system, $lang;
		$subs = $this->getSubs();
		$items = array();
		if($subs != false){
			foreach($subs as $sub){
			//	$material = new Materials($sub->mat_id);
				
				$trs = array();
				$skills = do_query_array("SELECT * FROM materials_skills WHERE sub_id=$sub->id  ORDER BY title ASC");
				if($skills != false && count($skills) > 0){
					foreach($skills as $skill){
						$row = new Layout($skill);
						$used_skills = do_query_array("SELECT term_id FROM services_skills_terms WHERE skill_id=$skill->id", $this_system->db_year);
						if($used_skills != false && count($used_skills) > 0){
							$tooltip =array();
							foreach($used_skills as $t){
								$term = new Terms($t->term_id);
								$level = isset($term->level_id) ? new Levels($term->level_id) : '';
								$tooltip[] = ($level!='' ? $level->getName().' - ': ''). (isset($term->level_id) ? $term->title :'');
							}
							$row->tooltip = implode('<br />', $tooltip);
							$row->count = count($used_skills);
						}
						$row->template = "modules/resources/templates/materials_skills_rows.tpl";
						$trs[] = $row->_print();	
					}
				}
				$items[] = write_html('h3', '', 
					write_html('a', '',
						write_html('text', 'class="sub_holder-'.$sub->id.'"',
							$sub->title
						).
						write_html('button', 'type="button" module="resources" action="addSkill" sub_id="'.$sub->id.'" class="ui-state-default hoverable circle_button rev_float" ', write_html('b', '', '+')).
						write_html('button', 'type="button" action="renameMatSub" sub_id="'.$sub->id.'" rel="'.$sub->title.'" class="ui-state-default hoverable circle_button rev_float" ', write_html('b', 'title="'.$lang['rename'].'"', 'R')).
						write_html('button', 'type="button" action="deleteMatSub" sub_id="'.$sub->id.'" rel="'.$sub->title.'" class="ui-state-default hoverable circle_button rev_float" ', write_html('b', 'title="'.$lang['delete'].'"', 'X'))
					)
					
				).
				write_html('div', '',
					write_html('table', 'class="tablesorter"',
						write_html('thead', '',
							write_html('tr', '',
								write_html('th', '', $lang['title']).
								write_html('th', 'width="150"', $lang['group']).
								write_html('th', 'width="20"', '&nbsp;').
								write_html('th', 'width="20" class="unprintable {sorter:false}"', '&nbsp;').
								write_html('th', 'width="20" class="unprintable {sorter:false}"', '&nbsp;')
							)
						).
						write_html('tbody', '',	
							implode('', $trs)
						)
					)
				);
			}
		}
		
		return $items;
	}
}
?>