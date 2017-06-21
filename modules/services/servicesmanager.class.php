<?php
/** Service Manager
*
*/

class ServicesManager extends Services{
	public $editable = false,
	$readable = false,
	$con = '',
	$con_id = '';
	
	public function __construct($con, $con_id){
		$this->con = $con;
		$this->con_id = $con_id;
		switch($con){
			case 'supervisor':
				$this->readable = getPrvlg('resource_read_supervisors');
				$this->editable = getPrvlg('resource_edit_supervisors');
				$this->conObj = new Supervisors($con_id);
			break;
			case 'prof': 
				$this->readable = getPrvlg('resource_read_profs');
				$this->editable = getPrvlg('resource_edit_profs');
				$this->conObj = new Profs($con_id);
			break;
			case 'level':
				$this->readable = getPrvlg('resource_read_levels');
				$this->editable = getPrvlg('resource_edit_levels');
				$this->conObj = new Levels($con_id);
			break;
			case 'class':
				$this->readable = getPrvlg('resource_read_classes');
				$this->editable = getPrvlg('resource_edit_classes');
				$this->conObj = new Classes($con_id);
			break;
			case 'group':
				$this->readable = getPrvlg('resource_read_groups');
				$this->editable = getPrvlg('resource_edit_groups');
				$this->conObj = new Groups($con_id);
			break;
			case 'student':
				$this->readable = getPrvlg('std_read');
				$this->editable = getPrvlg('std_edit');
				$this->conObj = new Students($con_id);
			break;
			case 'material':
				$this->readable = getPrvlg('resource_read_materials');
				$this->editable = getPrvlg('resource_edit_materials');
				$this->conObj = new Materials($con_id);
			break;
		}
		$this->services = $this->conObj->getServices();
	}

	
	public function loadLayout(){
		global $lang;
		$toolbox = array();
		if($this->con != 'material'){
			$toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="addService" con="'.$this->con.'" conid="'.$this->con_id.'"',
				"text"=> $lang['add'],
				"icon"=> "plus"
			);
		}
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="print_pre" rel="#service_list-'.$this->con.'-'.$this->con_id.'"',
			"text"=> $lang['print'],
			"icon"=> "print"
		);
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="saveAsPdf" rel="#service_list-'.$this->con.'-'.$this->con_id.'"',
			"text"=> $lang['save_as_pdf'],
			"icon"=> "print"
		);
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="exportTable" rel="#service_list-'.$this->con.'-'.$this->con_id.'"',
			"text"=> $lang['export'],
			"icon"=> "disk"
		);
		
		$out = write_html('div', 'id="service_list-'.$this->con.'-'.$this->con_id.'"',
			createToolbox($toolbox).
			 $this->intoTable()
		);
		return $out;
	}
	
	public function intoTable(){
		global $lang;
		$tbody = '';
		if(isset($this->services) && $this->services != ''){
			foreach($this->services as $service){
				if(isset($service->id )){
					$level = new Levels($service->level_id);
					$tbody .= write_html('tr', 'id="service-'.$service->id.'"',
						( $this->readable ? 
							write_html('td', 'class="unprintable"',
								write_html('button', 'type="button" class="ui-state-default hoverable circle_button" serviceid="'.$service->id.'" action="openService" module="services"',  write_icon('extlink'))
							) 
						: '').
						($this->editable ? 
							write_html('td', 'class="unprintable"',
								write_html('button', 'type="button" class="ui-state-default hoverable circle_button" module="services" action="deleteService" serviceid="'. $service->id.'" con="'.$this->con.'" conid="'.$this->con_id.'"',  write_icon('close'))
							)
						:'').
						write_html('td', 'align="center" valign="top"', $service->getName()).
						write_html('td', 'align="center" valign="top"', $level->getName())
					);
				}
			}
		}
		$table = write_html('table', 'cellspacing="1" class="tablesorter"',
			write_html('thead', '',
				write_html('tr', '',
					($this->readable ? write_html('th', 'width="24" class="unprintable {sorter:false}"', '&nbsp') : '').
					($this->editable ? write_html('th', 'width="24" class="unprintable {sorter:false}"', '&nbsp') : '').
					write_html('th', '',$lang['materials']).
					write_html('th', 'width="200"', $lang['level'])
				)
			).
			write_html('tbody', '', $tbody)
		);
		return $table;
	}
	
	public function assignServiceForm(){
		global $lang;
		$assigned_services = array();
		$assigned_materials = array();
		if($this->services != false && 	count($this->services) > 0){
			foreach($this->services as $service){
				$assigned_services[] = $service->id;
				$assigned_material[] = $service->mat_id;
			}
		}
		
		$table_trs = array();
		if($this->con == 'level'){
			$materials = Materials::getList();
			if($materials != false && count($materials) > 0){
				foreach($materials as $mat){
					$table_trs[] = write_html('tr', '',
						write_html('td', '', 
							'<input type="checkbox" name="mat[]" value="'.$mat->id.'" '.(in_array($mat->id, $assigned_materials) ? 'checked="checked"' : '').' />'
						).
						write_html('td', '', $mat->getName())
					);
				}
			}
			$table_title = $lang['materials'];
		} elseif(in_array($this->con , array('student', 'group', 'class'))){
				// the select 
			$select_title = $lang['level'];
			$levels = Levels::getList(true);
			foreach($levels as $level){
				$select_opts[$level->id] = $level->getName();
			}
				// the default table
			$con_level = getLevelFromCon($this->con, $this->con_id);
			$select_selected = $con_level->id;
			$level_services = $con_level->getServices();
			foreach($level_services as $service){
				$table_trs[] = write_html('tr', '',
					write_html('td', '', 
						'<input type="checkbox" name="services[]" value="'.$service->id.'" '.(in_array($service->id, $assigned_services) ? 'checked="checked"' : '').' update="colectServiceChkox" />'
					).
					write_html('td', '', $service->getName())
				);
			}
			$table_title = $lang['materials'];
		} else {
				// the select
			$select_title = $lang['materials'];
			$materials = Materials::getList();
			foreach($materials as $mat){
				$mat_id = $mat->id;
				$select_opts[$mat_id] = $mat->getName();
			}
			$select_selected = $mat_id;
			
				// the defaullt table
			$table_title = $lang['levels'];
			$services =sortArrayOfObjects( $mat->getServices(), getItemOrder('levels'), 'level_id');
			foreach($services as $service){
				$level = new Levels($service->level_id);
				$table_trs[] = write_html('tr', '',
					write_html('td', '', 
						'<input type="checkbox" name="services[]" value="'.$service->id.'" '.(in_array($service->id, $assigned_services) ? 'checked="checked"' : '').' update="colectServiceChkox" />'
					).
					write_html('td', '', $level->getName())
				);
			}
			
		}
			
		$cur_services_str = implode(',', $assigned_services);
		
		return write_html('form', 'id="services-form" class="ui-state-highlight ui-corner-all" style="padding:5px"',
			'<input type="hidden" name="con" value="'.$this->con.'" />'.
			'<input type="hidden" name="con_id" value="'.$this->con_id.'" />'.
			'<input type="hidden" id="colectChkox_value" name="services_str" value="'.$cur_services_str .'" />'.
			write_html('table', ' border="0" cellspacing="0"',
				(isset($select_opts) ? 
					write_html('tr', '',
						write_html('td', 'class="reverse_align" width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $select_title)
						).
						write_html('td', '',
							write_html_select('class="combobox" name="material" update="'.(in_array($this->con, array('prof', 'supervisor') ) ?  "setlvlByMat" : "setMatByLvl").'" con="'.$this->con.'" conid="'.$this->con_id.'"', $select_opts, $select_selected)
						)
					)
				: '')
			).
			write_html('div', 'id="lvlbymat_div"', 
				write_html('table', 'class="tablesorter"',
					write_html('thead', '', 
						write_html('tr', '',
							write_html('th', 'width="20" class={sorter:false}', 
								'<input type="checkbox" class="select_all">'
							).
							write_html('th', '', $table_title)
						)
					).
					write_html('tbody', '',
						implode('', $table_trs)
					)
				)
			)
		);	
	}
	
	public function getMatByLevel($level_id){
		global $lang;
		$assigned_services = array();
		if(is_array($this->services) && count($this->services) > 0){
			foreach($this->services as $service){
				$assigned_services[] = $service->id;
			}
		}
		$level = new Levels($level_id);
		$services = $level->getServices();
		if(count($services) > 0){
			foreach($services as $service){
				$material = new Materials($service->mat_id);
				$table_trs[] = write_html('tr', '',
					write_html('td', '', 
						'<input type="checkbox" name="services[]" value="'.$service->id.'" '.(in_array($service->id, $assigned_services) ? 'checked="checked"' : '').' update="colectServiceChkox" />'
					).
					write_html('td', '', $material->getName())
				);
			}
			return write_html('table', 'class="tablesorter"',
				write_html('thead', '', 
					write_html('tr', '',
						write_html('th', 'width="20" style="background-image:none"', '&nbsp;').
						write_html('th', '', $lang['level'])
					)
				).
				write_html('tbody', '',
					implode('', $table_trs)
				)
			);
		} else {
			return write_error($lang['mat_notassigned_for_level']);
		}
	}

	public function getLevelByMat($mat_id){
		global $lang;
		$assigned_services = array();
		if(is_array($this->services) && count($this->services) > 0){
			foreach($this->services as $service){
				$assigned_services[] = $service->id;
			}
		}
		$material = new Materials($mat_id);
		$services = $material->getServices();
		if(count($services) > 0){
			foreach($services as $service){
				$level = new Levels($service->level_id);
				$table_trs[] = write_html('tr', '',
					write_html('td', '', 
						'<input type="checkbox" name="services[]" value="'.$service->id.'" '.(in_array($service->id, $assigned_services) ? 'checked="checked"' : '').' update="colectServiceChkox" />'
					).
					write_html('td', '', $level->getName())
				);
			}
			return write_html('table', 'class="tablesorter"',
				write_html('thead', '', 
					write_html('tr', '',
						write_html('th', 'width="20" style="background-image:none"', '&nbsp;').
						write_html('th', '', $lang['material'])
					)
				).
				write_html('tbody', '',
					implode('', $table_trs)
				)
			);
		} else {
			return write_error($lang['mat_notassigned_for_level']);
		}
	}
	
	static function _save($post){
		$con_id = $post['con_id'];
		switch($post['con']){
			case 'supervisor':
				$table = "supervisors";
				$ref = "id";
				$db = MySql_Database;
			break;
			case 'prof': 
				$table = "profs_materials";
				$ref = "id";
				$db = MySql_Database;
			break;
			case 'level':
				$table = "services";
				$ref = "level_id";
				$db = DB_year;
			break;
			case 'class':
				$table = "materials_classes";
				$ref = "class_id";
				$db = DB_year;
			break;
			case 'group':
				$table = "materials_groups";
				$ref = "group_id";
				$db = DB_year;
			break;
			case 'student':
				$table = "materials_std";
				$ref = "std_id";
				$db = DB_year;
			break;
		}
		$error = false;
		if($post['con'] == 'level'){
			foreach($post['mat'] as $mat_id){
				if(!do_query_edit("INSERT INTO services (level_id, mat_id) VALUES ($con_id, $mat_id)", $db)){
					$error = true;
				}
			}
		} else {
			
			//do_query_edit("DELETE FROM $table WHERE $ref=$con_id", $db);
			for($i=0; $i<count($post['services']); $i++){
				$ser_id = $post['services'][$i];
				$chk = do_query_obj("SELECT * FROM $table WHERE $ref=$con_id AND services=$ser_id", $db);
				if(!isset($chk->services)){
					if(!do_query_edit("INSERT INTO $table ($ref, services) VALUES ($con_id, $ser_id)", $db)){
						$error = true;
					}
				}
			}
		}
		if($error == false){
			$answer['id'] = $con_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = "Error";
		}
		return  json_encode($answer);	
	}
}
