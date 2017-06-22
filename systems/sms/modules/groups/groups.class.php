<?php
/** Groups
*
*/
class Groups{
	public $editable = false;

	public function __construct($id, $sms=''){
		global $lang;
		if($sms == ''){
			 global $sms;
		}
		$this->sms = $sms;
		if($id != ''){	
			$group = do_query_obj("SELECT * FROM groups WHERE id=$id", Db_prefix.$_SESSION['year'], $sms->ip);	
			if(isset($group->id)){
				foreach($group as $key =>$value){
					$this->$key = $value;
				}
				if($_SESSION['group'] =='superadmin'){
					$this->editable = true;
				} elseif($group->resp!= '' && $group->resp == $_SESSION['user_id']){
					$this->editable = true;
				} elseif($group->{'parent'} == 'etab'&& $_SESSION['group'] == 'principal'){
					$this->editable = true;
				} elseif($group->{'parent'} == 'level' && $_SESSION['group'] == 'principal'){
					$principal = new Principals($_SESSION['user_id']);
					$principal_levels = $principal->getLevelList();
					if(in_array($group->parent_id , $principal_levels)){
						$this->editable = true;
					} 
				}
			}	
		}
			
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getParentObj(){
		if(!isset($this->parentObj)){
			global $this_system;
			$this->parentObj = $this_system->getAnyObjById($this->{'parent'}, $this->parent_id);
		}
		return $this->parentObj;
	}
	
	public function getLevel(){
		if(!isset($this->level)){
			$parent = $this->getParentObj();
			if(get_class($parent) != 'Etabs'){
				$this->level = $parent->getLevel();
			} else {
				$levels = $parent->getlevelList();
				$this->level = reset($levels);
			}
		}
		return $this->level;
	}
	
	public function getServices($inherited=false){
		if(!isset($this->privateServices)){
			$out= array();
			$mats= do_query_obj("SELECT service_id FROM groups WHERE id =".$this->id, $this->sms->db_year);
			if($mats->service_id != ''){
				$privateService = $this->sms->getSettings('ig_mode') == '1' ? new servicesIG($mats->service_id) :  new services($mats->service_id);
				$out[] = $privateService;
				$this->privateServices = $privateService;
			}
		}
		if($inherited){
			$parent = $this->getParentObj();
			$parent_services = $parent->getServices();
			foreach($parent_services as $service){
				if(!in_array($service, $out)){
					$out[] = $service;
				}
			}
			$this->allServices = Services::orderService($out);
			return $this->allServices;
		} elseif(isset($this->privateServices)) {
			return $this->privateServices;
		} else {
			return array();
		}
	}
	
	public function getStudents($all_stat=false){
		if(!isset($this->students)){
			$db_year =Db_prefix.$_SESSION['year'];
			$field_order_name = $_SESSION['lang'] == 'ar' ? "name_ar" : "name";
			$sql = "SELECT $db_year.groups_std.std_id FROM $db_year.groups_std, ".$this->sms->database.".student_data WHERE $db_year.groups_std.group_id=$this->id AND $db_year.groups_std.std_id=".$this->sms->database.".student_data.id";
			if(!$all_stat){
				$sql .= " AND student_data.status=1";
			} else{
				if(is_array($all_stat)){
					foreach($all_stat as $stat){
						$where_stat[] ="(student_data.status=$stat)";
					}
					$sql .= " AND (".implode(' OR ', $where_stat).")";
				}
			}
			
			$sql .= " ORDER BY ".$this->sms->database.".student_data.sex, ".$this->sms->database.".student_data.$field_order_name";
			$students = do_query_array($sql , $this->sms->database);	
			$std_ids =array();
			if(count($students) > 0){
				foreach($students as $student){
					$std_ids[] = new Students($student->std_id, $this->sms);
				}
			}
			$this->students = $std_ids;
		}
		return $this->students;
	}
	
	public function loadLayout(){
		$layout = new Layout($this);
		$layout->group_name = $this->getName();
		$groupService = $this->getServices(false);
		unset($layout->privateServices);
		if(isset($layout->allServices)) unset($layout->allServices);
		$parent = $this->getParentObj();
		$layout->parent_name = $this->getParentObj()->getName();
		$parent_services = $parent->getServices();
		foreach($parent_services as $service){
			$services_arr[$service->id] = $service->getName().'-'.$service->lvl;
		}
		if(isset($_GET['dialog'])){
			$layout	->edit_button = 'hidden';
		}
		$resp = new Employers($this->resp);
		$layout->resp_name = $resp->getName();
		unset($layout->parentObj);
		$layout->services_select = write_select_options( $services_arr, (count($groupService) > 0 ?$groupService->id : ''), false);
		$student_list = new StudentsList('group', $this->id);
		$student_list->stats = array('1');
		$layout->student_list_table = $student_list->createTable();
		$layout->total_students = $student_list->getCount();
		return fillTemplate('modules/groups/templates/groups.tpl', $layout);
	}

	static function newForm($par, $par_id){
		global $sms;
		$layout = new Layout();
		$parent = $sms->getAnyObjById($par, $par_id);
		$layout->parent_name = $parent->getName();
		$parent_services = $parent->getServices();
		$services_arr = array(''=>'');
		foreach($parent_services as $service){
			$services_arr[$service->id] = $service->getName().'-'.$service->lvl;
		}
		$resp = new Employers($_SESSION['user_id']);
		$layout->resp = $resp->id;
		$layout->resp_name = $resp->getName();
		$layout->{'parent'} = $par;
		$layout->parent_id = $par_id;
		$layout->editable = 1;
		$layout->services_select = write_select_options( $services_arr, '');
		$layout->template = 'modules/groups/templates/new_form.tpl';
		return $layout->_print();
	}
	
	static function getList($parent=false, $parent_id=false){
		$sql = "SELECT id FROM groups";
		if($parent != false){
			$sql .= " WHERE parent='$parent'";
			if($parent_id != false){
				$sql .= " AND parent_id='$parent_id'";
			}
		}
		$out = array();
		$groups = do_query_array($sql, Db_prefix.$_SESSION['year']);
		foreach($groups as $group ){
			$out[] = new Groups($group->id);
		}
		return $out;
		//return sortArrayOfObjects($out, getItemOrder('groups'), 'id');
	}
	
	static function getListTable($parent=false, $parent_id=false){
		global $lang, $sms;
		$groups = Groups::getList($parent, $parent_id);
		$trs = array();
		$i = 1;
		foreach($groups as $group){
			if($group->resp != ''){
				$resp = new Employers($group->resp);
			}
			$service_name = '';
			if($group->service_id != '' ){
				$service = $sms->getSettings('ig_mode') == '1' ? new servicesIG($group->service_id) :  new services($group->service_id);
				$service_name = $service->getName();
			}
			$trs[] = write_html('tr', '',
				write_html('td', 'class="unprintable"', 
					write_html('button', 'module="groups" groupid="'.$group->id.'" action="openGroup" style="width:24px; height:24px" class="ui-state-default hoverable circle_button" title="'.$lang['open'].'"', write_icon('extlink'))
				).
				write_html('td', 'class="unprintable"',
					($group->editable ? 
						write_html('button', 'module="groups" groupid="'.$group->id.'" action="deleteGroup" style="width:24px; height:24px" class="ui-state-default hoverable circle_button" title="'.$lang['delete'].'"', write_icon('close'))
					: '')
				).
				write_html('td', '', $group->getName()).
				write_html('td', '', $service_name).
				write_html('td', '', $group->resp != '' ? $resp->getName() : '').
				write_html('td', 'align="center"', count($group->getStudents()))
			);
		}
		
		return write_html('table', 'class="tablesorter"', 
			write_html('thead', '', 
				write_html('tr', '',
					write_html('th', 'width="24" style="background-image:none"', '&nbsp;').
					write_html('th', 'width="24" style="background-image:none"', '&nbsp;').
					write_html('th', '', $lang['name']).
					write_html('th', '', $lang['materials']).
					write_html('th', '', $lang['resp']).
					write_html('th', '', $lang['total_std'])
				)
			).
			write_html('tbody', '', 
				implode('', $trs)
			)
		);
	}
	
	static function _save($post){
		global $lang, $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			$group_id = safeGet($post['id']);
			$result = do_update_obj($post, 'id='.$group_id, 'groups', $sms->db_year, $sms->ip);
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'groups', $sms->db_year, $sms->ip);
			$group_id= $result;
		}
		if($result==false){
			$answer['error'] = $lang['error_updating'];
		} else {
			if(isset($post['tot_con'])){
				$stds = explode(',', $post['tot_con']);
				$values = array();
				foreach($stds as $std_id){
					$values[] = "($group_id, $std_id)";
				}
				do_query_edit("DELETE FROM groups_std WHERE group_id =$group_id", Db_prefix.$_SESSION['year']);
				do_query_edit("INSERT INTO groups_std (group_id, std_id) VALUES ".implode(',', $values), Db_prefix.$_SESSION['year']);
			}
			$answer['id'] = $group_id;
			$answer['title'] = $post['name'];
			$answer['error'] = "";
		}
		return $answer;
	}

	static function _delete($id){
		if(do_query_edit("DELETE FROM groups WHERE id=$id", Db_prefix.$_SESSION['year'])){
			do_query_edit("DELETE FROM groups_std WHERE group_id=$id", Db_prefix.$_SESSION['year']);
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}
	
	static function loadMainLayout($etab=''){
		global $lang;
		$etabs = Etabs::getList();
		if($etab == ''){
			$etab = reset($etabs);
		}
		$etabs_arr = objectsToArray($etabs);
		$toolbox = array();
		$toolbox[] = array(
			"tag" => "span",
			"attr"=> 'style="margin:0px 7px 0px 2px"',
			"text"=>write_html_select('name="etab_id", class="combobox"', $etabs_arr, $etab->id),
			"icon"=> ""
		);
		if(getPrvlg('create_groups')){
			$toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="newGroup" parent="etab" parent_id="'.$etab->id.'" title="'. $lang['new'].'" ',
				"text"=> '',
				"icon"=> "document"
			);
			
		}
		
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'class="print_but" rel="#group_list" title="'. $lang['print_list'].'"',
			"text"=> '',
			"icon"=> "print"
		);
		
		$layout = new Layout();
		$layout->resource_type_name = $lang['groups'];
		$layout->item_type = 'groups';
		$layout->toolbox = createToolbox($toolbox);
		$groups = $etab->getGroupList();
		$layout->items_list = '';
		foreach($groups as $group){
			if(isset($group->id) && $group->id != ''){
				 $layout->items_list .=write_html( 'li', 'group_id="'.$group->id.'"class="hoverable clickable ui-stat-default ui-corner-all" action="openEtabsGroup"', 
					write_html('text', 'class="holder-groups-'.$group->id.'"',
						$group->getName()
					)
				);	
			}
		}
		return fillTemplate('modules/groups/templates/main_layout.tpl', $layout);
	}

	
}

?>