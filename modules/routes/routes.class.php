<?php
/**Routes main Class
*
*/

class Routes{
	public function __construct($id, $busms=''){
		if($busms == ''){
			global $busms;
		}
		$this->busms = $busms;
		$route = do_query_obj("SELECT * FROM bus_routes WHERE id='$id'", $busms->database, $busms->ip);
		if($route != false && $route->id != ''){
			foreach($route as $key =>$value){
				$this->$key = $value;
			}
		} 
	}
	
	public function getName(){
		return $this->no;
	}
	
	public function loadLayout(){
		$layout = new Layout($this);
		$layout->template = 'modules/routes/templates/route_layout.tpl';
		$data = new Layout($this);
		$data->template = 'modules/routes/templates/route_data.tpl';
		if($this->driver_id != '' && $this->driver_id!='0'){
			$driver = new Drivers($this->driver_id);
			$data->driver_name = $driver->getName();
			$data->driver_tel = implode(' - ', $driver->getTel());
		}
		if($this->matron_id != '' && $this->matron_id!='0'){
			$matron = new Matrons($this->matron_id);
			$data->matron_name = $matron->getName();
			$data->matron_tel = implode(' - ', $matron->getTel());
		}
		
		$buss = Bus::getList();
		$data->bus_opts = write_select_options(objectsToArray($buss), $this->bus_id);
		$groups = GroupRoutes::getList();
		$data->group_opts = write_select_options(objectsToArray($groups), $this->group_id);
		$layout->route_data = $data->_print();
		$members = $this->getMembers();
		$layout->members_trs = '';
		foreach($members as $meb){
			$layout->members_trs .=  $this->getMemberRow($meb, $this->id);
		}
		
		$layout->parcour_table_m = $this->getParcour('m');
		$layout->parcour_table_e = $this->getParcour('e');
		return $layout->_print();
	}

	public function getGroup(){
		return new GroupRoutes($this->group_id, $this->busms);
	}
	
	public function getFees($sms, $year){
		$busFees = new BusFees($sms, $year);
		return $busFees->getFees();
	}
	
	public function getParcour($r='m'){ // morning OR e  for evening
		$time_field = $r."_time";
		$address_field = $r."_address";
		$scheduled_member = array();
		$non_shedules_member = array();
		$yes_list = array();
		$non_list = array();
		$rows = do_query_array("SELECT * FROM route_members WHERE route_id=$this->id ORDER BY ".$r."_time ASC", $this->busms->database, $this->busms->ip);
		if($rows != false){
			foreach($rows as $row){
				$item = new layout($row);
				$item->cc_id = $row->cc_id;
				$item->time = $row->{$r.'_time'};
				if($row->con == 'std'){
					$sms = new SMS($row->cc_id);
					$memb = new Students($row->con_id, $sms);
					$level = $memb->getLevel();
					$item->memb_name = $memb->getName().' - '. ($level!=false ? $level->getName() : '');
					$item->school_code = $sms->code;
				} else {
					$hrms = new HrMS($row->cc_id);
					$memb = new Employers($row->con_id, $hrms);
					$item->memb_name = $memb->getName();
					$item->school_code = $hrms->code;
				}
				
				$address = $memb->getAddress(true, '', true);
				if(count($address) > 1){
					foreach($address as $adrs){
						$title = AddressBook::toStr($adrs);
						$address_opts[] = write_html('option', 'value="'.$adrs->id.'"', $title);
					}
					$item->address = write_html_select('name="address_id" width="300"',$address_opts, $row->{$r.'_address'});
					
				} elseif(count($address) > 0){
					$adrs = reset($address);
					$item->address =  AddressBook::toStr($adrs). '<input type="hidden" class="address_id" value="'.$adrs->id.'" />';
				}
				$item->address_id = $adrs->id;
				$item->template = 'modules/routes/templates/parcour_item.tpl';
				$item->r = $r;
				
				if($row->$time_field !=''){
					$scheduled_member[] = $item;
				} else {
					$non_shedules_member[] = $item;
				}
			}
			
			foreach($scheduled_member as $row){
				$yes_list[] = $row->_print();
			}
			foreach($non_shedules_member as $row){
				$row->time_td_hidden = 'none';
				$non_list[] = $row->_print();
			}
		}
		
		$layout = new Layout($this);
		$layout->r = $r;
		$layout->template = 'modules/routes/templates/parcour.tpl';
		$layout->yes_list = implode('', $yes_list);
		$layout->non_list = implode('', $non_list);
		return $layout->_print();
	}
	
	static function loadMainLayout(){		
		$layout = new Layout();
		$layout->list = '';
		$routes = Routes::getList();
		$first = true;
		foreach($routes as $r){
			$layout->list .= write_html('li', 'action="openRoute" route_id="'.$r->id.'" itemid="'.$r->id.'" class="clickable hoverable ui-state-default ui-corner-all '.($first ? 'ui-state-active' : '').'"', 
				write_html('text', 'class="holder-route-'.$r->id.'"', $r->id. ' - '. $r->region)
			);
			if($first){
				$first_route = new Routes($r->id);
				$layout->route_details = $first_route->loadLayout();
				$first = false;
			}
		}
		$layout->template = 'modules/routes/templates/main_layout.tpl';
		return $layout->_print();
	}

	static function getList(){
		global $this_system;
		$routes = do_query_array("SELECT * FROM bus_routes", MySql_Database);
		return sortArrayOfObjects($routes, $this_system->getItemOrder('routes'), 'id');
	}
	
	public function getMembers(){
		$out = array();
		$mebs = do_query_array("SELECT con, con_id, cc_id FROM route_members WHERE route_id=$this->id ORDER BY con, cc_id DESC", $this->busms->database, $this->busms->ip);
		$ccs = array();
		if($mebs != false && count($mebs)>0){
			foreach($mebs as $member){
				if(array_key_exists($member->cc_id, $ccs)){
					$cc = $ccs[$member->cc_id];
				} else {
					$cc = $member->con == 'std' ? new SMS($member->cc_id) : new HRMS($member->cc_id);
					$ccs[$cc->id] = $cc;
				}
				if($member->con == 'std'){
					$out[] = new Students($member->con_id, $cc);
				} else {
					$out[] = new Employers($member->con_id, $cc);
				}
			}
		}
		return $out;
	}
	
	static function getMemberRow($obj, $route_id){
		global $lang;
		$layout = new Layout($obj);
		$layout->template = 'modules/routes/templates/member_row.tpl';
		$layout->route_id = $route_id;
		$layout->con_id = $obj->id;
		$layout->member_name = $obj->getName();
		if(get_class($obj) == 'Students'){
			$layout->module = 'students';
			$layout->action = 'openStudent';
			$cc = $obj->sms;
			$layout->attr = 'sms_id="'.$cc->id.'" std_id="'.$obj->id.'"';
			$layout->con = 'std';
			$layout->con_name = $lang['student'];
		} else {
			$layout->module = 'employers';
			$layout->action = 'openEmployer';
			$cc = $obj->hrms;
			$layout->attr = 'hrms_id="'.$cc->id.'" empid="'.$obj->id.'"';
			$layout->con = 'emp';
			$layout->con_name = $lang['employer'];
		}
		$layout->cc_name = $cc->getName();
		return $layout->_print();
	}
	
	static function newMember($con, $route_id=''){
		$form = new Layout();
		$form->template = 'modules/routes/templates/new_member.tpl';
		$form->con = $con;
	//	$form->route_id = $route_id;
		if($con =='std'){
			$ccs = SMS::getList();
		} else {
			$ccs = HRMS::getList();
		}
		$routes= Routes::getList();
		foreach($routes as $route){
			$opts[] = write_html('option', 'value="'.$route->id.'" '.($route_id==$route->id?'checked="checked"':''), $route->no);
		}
		$form->routes_opts = implode('', $opts);
		$first_cc = reset($ccs);
		$form->schools_opts = '';
		foreach($ccs as $cc){
			$form->schools_opts .= write_html('option', 'value="'.$cc->id.'"', $cc->code);
		}
		if($con =='std'){
			$form->system_id = 'sms_id="'.$first_cc->id.'"';
		} else {
			$form->system_id = 'hrms_id="'.$first_cc->id.'"';
		}
		
		return $form->_print();	
	}
	
	static function saveMember($post){
		if(do_insert_obj($post, 'route_members')){
			$answer = array();
			if($post['con'] == 'std'){
				$member = new Students($post['con_id'], new SMS($post['cc_id']));
			} else {
				$member = new Employers($post['con_id'], new HRMS($post['cc_id']));
			}
			$answer['error'] = '';
			$answer['html'] = Routes::getMemberRow($member, $post['route_id']);
			return $answer;
		} else {
			return false;
		}		
	}
	
	static function delMember($post){
		global $busms;
		return do_delete_obj("con='".$post['con']."' AND con_id=".$post['con_id']." AND route_id=".$post['route_id'], 'route_members', $busms->database, $busms->ip);
	}
	
	static function newRouteForm($group_id=''){
		$layout = new Layout();
		$layout->template = 'modules/routes/templates/route_data.tpl';
		$layout->new_hidden = 'hidden';
		$buss = Bus::getList();
		$layout->id_tr = 'hidden';
		$layout->bus_opts = write_select_options(objectsToArray($buss));
		$routes_group = GroupRoutes::getList();
		$layout->group_opts = write_select_options(objectsToArray($routes_group), $group_id);
		return $layout->_print();
		
	}
	
	static function _save($post){
		global $busms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'bus_routes', $busms->database, $busms->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'bus_routes', $busms->database, $busms->ip);
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
		return $answer;
	}
	
	static function _delete($route_id){
		if(do_query_edit("DELETE FROM bus_routes WHERE id=$route_id", $busms->database, $busms->ip)){
			do_query_edit("DELETE FROM route_members WHERE route_id=$route_id", $busms->database, $busms->ip);
			$answer['id'] = $route_id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return $answer;
	}
	
	static function moveMember($post){
		global $busms;
		return do_update_obj(array('route_id'=>$post['route_id']), "con='".$_POST['con']."' AND con_id=".$post['con_id'], 'route_members', $busms->database, $busms->ip); 
	}
	
	static function getAutocompleteRoute( $value){
		global $lang, $busms;
		$Arabic = new I18N_Arabic('KeySwap');
		$params = array("region = '$value' ",
			"region LIKE '$value%' ",
			"region LIKE '".addslashes($Arabic->swapEa($value))."%' ",
			"LOWER(region) LIKE LOWER('$value') ",
			"LOWER(region) LIKE LOWER('$value%') ",
			"LOWER(region) LIKE LOWER('".addslashes($Arabic->swapAe($value))."%')"
		);
			
		$sql = "SELECT * FROM bus_routes WHERE (". implode(" OR ", $params) .")";
//echo $sql;
		$out = array();
		$routes = do_query_array( $sql);
		if($routes != false && count($routes)>0){
			foreach($routes as $route ){
					$label = str_replace($value, write_html('b', 'style="color:red"', $value), $route->region);
					$row = array('id'=>$route->id, 'name'=> $route->region, 'label'=> $route->region, 'no'=> $route->no);
					$out[] = $row;
			}
		} else {
			$out[] = array('error' => $lang['cant_find_item']);	
		}
		return json_encode($out);
	}

	static function getMemebersBySchool($sms_id){
		$sms = new SMS($sms_id);
		$members = do_query_array("SELECT * FROM route_members WHERE con='std' AND cc_id=$sms_id ORDER BY route_id");
		$trs = array();
		if($members != false){
			foreach($members as $member){
				$route = new Routes($member->route_id);
				$student = new Students($member->con_id, $sms);
				$level =$student->getLevel();
				$address = $student->getAddress(true);
				$trs[] = write_html('tr', '',
					write_html('td', 'class="unprintable"',
						write_html('button', 'type="button" module="students" action="openStudent" std_id="'.$student->id.'" sms_id="'.$sms_id.'" class="circle_button ui-state-default hoverable"', write_icon('person'))
					).
					write_html('td', '',$student->getName()).
					write_html('td', '', (isset($level->id) ? $level->getName() :'')).
					write_html('td', '', implode('<br />', $address)).
					write_html('td', '', $route->no)
				);
			}
		}
		$layout = new layout();
		$layout->template = 'modules/routes/templates/members_by_school.tpl';
		$layout->school_name = $sms->getName();
		$layout->school_code = $sms->code;
		$layout->trs = implode( '', $trs);
		return $layout->_print();
	}
	
}