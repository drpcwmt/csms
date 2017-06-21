<?php
/** Services IG
*
*/

class ServicesIG{

	public function __construct($service_id, $sms='', $year=''){
		if($sms == ''){
			global $sms;
		}
		if($year==''){ $year = $_SESSION['year'];}
		$this->year = $year;
		$this->sms = $sms;
		if($service_id != ''){		
			$service = do_query_obj("SELECT * FROM services_ig WHERE id=$service_id", Db_prefix.$year);	
			if(isset($service->mat_id)){ 
				foreach($service as $key =>$value){
					$this->$key = $value;
				}
				$this->color = $this->getMaterialColor($this->mat_id);
			} else {
				//throw new Exception('id Not Found');
			}	
		} else {
			//throw new Exception('id Not Found');
		}
	}
	
	public function getName(){
		global $lang;
		if(!isset($this->name)){
			$mat = $this->getMaterial();
			$this->name = $mat->getName();
		}
		return $this->name;
	}
	
	public function getMaterialColor(){
		global $lang;
		if(!isset($this->color)){
			$mat = $this->getMaterial();
			$this->color = $mat->color;
		}
		return $this->color;
	}
	
	public function getMaterial(){
		if(!isset($this->mat)){
			$this->mat = new Materials($this->mat_id, $this->sms);
		}
		return $this->mat;
	}
	
	public function getTypes($exam){
		global $sms;
		$rows = do_query_array("SELECT * FROM servicesig_fees WHERE service_id=$this->id AND $exam IS NOT NULL", $sms->db_year, $sms->ip);
		//echo "SELECT * FROM servicesig_fees WHERE service_id=$this->id AND $exam IS NOT NULL";
		if($rows != false){
			$types = $rows;
		} else{
			$types = array();
		}
		return $types;
	}
	
	public function loadLayout(){	
		global $lang;	
			// tab to show
		$tabs = array();
		if(in_array($_SESSION['group'], array('parent', 'student'))){
			if(MS_codeName=='sms_elearn'){
				$tabs[] = 'timeline';
				$tabs[] = 'books';
				$tabs[] = 'homeworks';				
			}
			$tabs[] = 'notes';
			$tabs[] = 'documents';
		} elseif(in_array($_SESSION['group'], array('prof', 'supervisor'))){
			if(MS_codeName=='sms_elearn'){
				$tabs[] = 'timeline';
				$tabs[] = 'books';
				$tabs[] = 'planedTimeline';
				$tabs[] = 'homeworks';
			}
			$tabs[] = 'notes';
			$tabs[] = 'skills';
			$tabs[] = 'documents';
			$tabs[] = 'settings';
		} else {
			if(MS_codeName=='sms_elearn'){
				$tabs[] = 'books';
				$tabs[] = 'planedTimeline';
			}
			$tabs[] = 'notes';
			$tabs[] = 'documents';
			$tabs[] = 'settings';
			$tabs[] = 'skills';
		}
		$i = 0;
		$titles = array();
		$details_div = array();
		foreach($tabs as $tab){
			switch ($tab) {
				case 'notes':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=lessons&notes_list&service_id='.$this->id.'"', $lang['notes'])
					);
				break;
				case 'skills':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="index.php?module=services&skills&service_id='.$this->id.'"', $lang['skills'])
					);

				break;
				case 'settings':
					$titles[] = write_html('li', '', 
						write_html('a', 'href="#settings_details_tab"', $lang['settings'])
					);
					$layout = new Layout($this);
					/*if($this->schedule == 0 ){
						$layout->schedule_off = 'checked="checked"' ;
						$layout->schedule_on = '' ;
					} else {
						$layout->schedule_off = '' ;
						$layout->schedule_on = 'checked="checked"' ;
					}
					if($this->mark == 0 ){
						$layout->mark_off = 'checked="checked"' ;
						$layout->mark_on = '' ;
					} else {
						$layout->mark_off = '' ;
						$layout->mark_on = 'checked="checked"' ;
					}*/
					
					$gradin_opts = array("" => $lang['not_used']);
					$graddings = do_query_array( "SELECT * FROM gradding ORDER BY name ASC", DB_student);
					foreach($graddings  as $gradding){
						$gradin_opts[$gradding->id] = $gradding->name;
					}
					$layout->gradin_opts = write_select_options($gradin_opts, $this->gradding);
					$details_div[] = write_html('div', 'id="settings_details_tab"' , 
						fillTemplate('modules/services/templates/services_settings.tpl', $layout)
					);
				break;
			}
		}
		$titles[] = write_html('li', '', 
			write_html('a', 'href="index.php?module=services&ig&fees&id='.$this->id.'&lvl='.$this->lvl.'&mat_id='.$this->mat_id.'"', $lang['fees'])
		);
		return write_html('h3', 'class="title hidden showforprint"', $this->getName()).
		write_html('div', 'class="tabs"', 
			write_html('ul', '', implode('', $titles)).
			implode('', $details_div)
		);
	}

	
	static function getList($mat_id='', $lvl=''){
		global $sms, $ig_mode_lvl;
		$sql = "SELECT * FROM services_ig WHERE 1";
		if($mat_id != ''){
			$sql .= " AND mat_id=$mat_id";
		}
		if($lvl != ''){
			$sql .= " AND lvl='$lvl'";
		}
		$services = do_query_array($sql, $sms->db_year);	
		$out = array();
		if($services != false){
			foreach($services as $service){
				$out[] = new ServicesIG($service->id);
			}	
		}
		return $out;// = sortArrayOfObjects($out, $ig_mode_lvl, 'lvl');
	}
	
	static function loadMainLayout($mat_id){
		global $sms, $ig_mode_type, $ig_mode_lvl;
		foreach($ig_mode_lvl as $lvl){
			$service = ServicesIg::_search($mat_id, $lvl);	
			if($service!= false){	
				$stds_nov = do_query_obj("SELECT COUNT(std_id) AS count FROM materials_std WHERE services=$service->id AND exam='nov'", $sms->db_year, $sms->ip);
				$stds_jan = do_query_obj("SELECT COUNT(std_id) AS count FROM materials_std WHERE services=$service->id AND exam='jan'", $sms->db_year, $sms->ip);
				$stds_jun = do_query_obj("SELECT COUNT(std_id) AS count FROM materials_std WHERE services=$service->id AND exam='jun'", $sms->db_year, $sms->ip);
			}
			$tr[] = write_html('tr', 'mat_id="'.$mat_id.'" lvl="'.$lvl.'" service_id="'.($service!= false ? $service->id : '').'"',
				write_html('td', 'align="center"', 
					($service!= false ?
						write_html('button', 'type="button" action="openIgService" class="circle_button hoverable ui-state-default"', write_icon('extlink'))
					:
						write_html('button', 'type="button" action="addIgService" mat_id="'.$mat_id.'" lvl="'.$lvl.'" class="circle_button hoverable ui-state-default"', write_icon('plus'))
					)
				).
				write_html('td', 'align="center"', $lvl).
				write_html('td', '', ($service!= false ? $stds_nov->count:'')).
				write_html('td', '', ($service!= false ? $stds_jan->count:'')).
				write_html('td', '', ($service!= false ? $stds_jun->count:''))
			);
		}
		$material = new Materials($mat_id);
		$layout = new layout($material);
		$layout->template = 'modules/services/templates/servicesig_table.tpl';
		$layout->trs = implode('', $tr);
		return $layout->_print();
	}
	
	static function _search($mat_id, $lvl){
		global $sms;
		$service =  do_query_obj("SELECT id FROM services_ig WHERE mat_id=$mat_id AND lvl='$lvl'", $sms->db_year);
		if($service !=false){
			return new ServicesIG($service->id);
		} else {
			return false;
		}
	}
	
	public function loadFeesLayout(){
		global $sms, $ig_mode_type, $ig_mode_lvl;
		//$serviceIg = ServicesIg::_search($mat_id, $lvl);
		foreach($ig_mode_type as $type){
			$fees = do_query_obj("SELECT * FROM servicesig_fees WHERE service_id=$this->id AND type='$type'", $sms->db_year, $sms->ip);
			$tr[] = write_html('tr', '',
				write_html('td', 'align="center"', $type).
				write_html('td', '', '<input type="text" name="nov-'.$type.'" value="'.($fees!= false ? $fees->nov : '').'" />').
				write_html('td', '', '<input type="text" name="nov_reg-'.$type.'" value="'.($fees!= false ? $fees->nov_reg : '').'" />').
				write_html('td', '', '<input type="text" name="jan-'.$type.'" value="'.($fees!= false ? $fees->jan : '').'" />').
				write_html('td', '', '<input type="text" name="jan_reg-'.$type.'" value="'.($fees!= false ? $fees->jan_reg : '').'" />').
				write_html('td', '', '<input type="text" name="jun-'.$type.'" value="'.($fees!= false ? $fees->jun : '').'" />').
				write_html('td', '', '<input type="text" name="jun_reg-'.$type.'" value="'.($fees!= false ? $fees->jun_reg : '').'" />')
			);
		}
		
		$layout = new layout();
		$layout->id = $this->id;
		$layout->template = 'modules/services/templates/servicesig_fees_table.tpl';
		$layout->trs = implode('', $tr);
		
		// Remarking
		$layout->remarking_trs = $this->loadRemarkingTable();
		return $layout->_print();
		
	}
	
	public function getGroup(){
		$material =$this->getMaterial();
		$group = $material->getGroup();
		return $group;
	}

	public function loadRemarkingTable(){
		global $sms, $ig_mode_edex_remark_services, $ig_mode_camb_remark_services, $ig_mode_type;
		$group = $this->getGroup();
		if($group->{"name_".$_SESSION['dirc']} == 'Edex'){
			$services = $ig_mode_edex_remark_services;
		} else {
			$services = $ig_mode_camb_remark_services;
		}
		foreach($services as $ser){
			$rows = do_query_array("SELECT fees, paper FROM servicesig_remak_fees WHERE type='$ser' AND service_id=$this->id", $sms->db_year, $sms->ip);
			$arr = array();
			foreach($rows as $row){
				$arr[$row->paper] = $row->fees;
			}
			$trs[] = write_html('tr', '',
				write_html('td', '', '<input type="text" name="type[]" value="'.$ser.'">').
				write_html('td', '', '<input type="text" name="paper[]" value="1">').
				write_html('td', '', '<input type="text" name="fees[]" value="'.(isset($arr['1']) ? $arr['1'] :'').'">')				
			).
			write_html('tr', '',
				write_html('td', '', '<input type="text" name="type[]" value="'.$ser.'">').
				write_html('td', '', '<input type="text" name="paper[]" value="2">').
				write_html('td', '', '<input type="text" name="fees[]" value="'.(isset($arr['2']) ? $arr['2'] :'').'">')				
			).
			write_html('tr', '',
				write_html('td', '', '<input type="text" name="type[]" value="'.$ser.'">').
				write_html('td', '', '<input type="text" name="paper[]" value="1-2">').
				write_html('td', '', '<input type="text" name="fees[]" value="'.(isset($arr['1-2']) ? $arr['1-2'] :'').'">')				
			);
		}
		return implode('', $trs);
	}

	public function saveFees($post){
		global $sms, $ig_mode_exams, $ig_mode_type;
		$id = $this->id;
		$result = true;
		if(do_delete_obj("service_id=$this->id", 'servicesig_fees', $sms->db_year, $sms->ip)){
			foreach($ig_mode_type as $type){
				$ins = array(
					'service_id'=>$this->id,
				);
				$ins['type'] = $type;
				foreach($ig_mode_exams as $exam){
					$field = $exam.'-'.$type;
					if(isset($post[$field]) && $post[$field]> 0){
						$ins[$exam] = $post[$field];
					}
					$reg_field = $exam.'_reg'.'-'.$type;
					if(isset($post[$reg_field]) && $post[$reg_field]> 0){
						$ins[$exam.'_reg'] = $post[$reg_field];
					}
				}
				if(count($ins) > 2){
					if(do_insert_obj($ins, 'servicesig_fees', $sms->db_year, $sms->ip)== false){
						$result = false;
					}
				}
				
				// Remarking
				if(do_delete_obj("service_id=$this->id", 'servicesig_remak_fees', $sms->db_year, $sms->ip)){
					for($i =0; $i<count($post['fees']); $i++){
						if($post['type'][$i]!='' && $post['paper'][$i]!='' && $post['fees'][$i]!=''){
							$ins = array(
								'service_id'=>$this->id,
								'type'=>$post['type'][$i],
								'paper'=>$post['paper'][$i],
								'fees'=>$post['fees'][$i]
							);
							if(do_insert_obj($ins, 'servicesig_remak_fees', $sms->db_year, $sms->ip)== false){
								$result = false;
							}
						}
					}
				}
			}
		} else {
			$result = false;
		}

		if($result!=false){
			$answer['id'] = $this->id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return $answer;
	}
	
	
	public function getFees($exam){
		global $sms;
		$service_fees = do_query_obj("SELECT fees FROM servicesig_fees WHERE service_id=$this->id AND exam='$exam'", $sms->db_year, $sms->ip);
		return $service_fees!=false ? $service_fees->fees : false;
	}
	
	static function assignServiceForm($con, $con_id, $mat_id=''){
		global $sms, $ig_mode_lvl, $ig_mode_type, $ig_mode_exams;
		$materials = Materials::getList();
		$layout = new Layout();
		$layout->template = 'modules/services/templates/servicesig_assign.tpl';
		$layout->con = $con;
		$layout->con_id = $con_id;
		if($mat_id == ''){
			$first_mat = reset($materials);
			$mat_id = $first_mat->id;
		}
		$layout->mat_opts = write_select_options(objectsToArray($materials), $mat_id);
		$obj = $sms->getAnyObjById($con, $con_id);
		$con_services = $obj->getServices();
		$con_ser_arr = objectsToArray($con_services);
		$lvl_trs = array();
		$services = ServicesIG::getList($mat_id);
		if($services!=false && count($services)>0){
			foreach($services as $service){
			
				$exam_tds = array();
				foreach($ig_mode_exams as $exam){
					if(array_key_exists($service->id, $con_ser_arr)){
						$cur_ser = do_query_obj("SELECT * FROM materials_std WHERE std_id=$con_id AND services=$service->id AND exam='$exam' ", $sms->db_year, $sms->ip);
						$cur_type = ($cur_ser!=false ? $cur_ser->type :false);
					} else {
						$cur_type = false;
					}
					$types = $service->getTypes($exam);
					$opts = array();
					foreach($types as $type){
						$opts[]= write_html('option', 'value="'.$type->type.'" '.($cur_type!=false && $cur_type==$type->type ? 'selected="selected"':''), $type->type);
					}
					$exam_tds[] =  write_html('td', 'align="center"', 
						write_html('select', 'name="'.$service->id.'-'.$exam.'" style="width:60px"', 
							write_html('option', 'value="" '.(!$cur_type?'selected="selected"':''), "&nbsp;").
							implode('', $opts)
						)
					);
				}
				$lvl_trs[] = write_html('tr', '',
					write_html('td', '', $service->lvl).
					implode( '', $exam_tds)
				);
			}
		}
		$layout->trs = implode('', $lvl_trs);
		return $layout->_print();
	}

	static function getConSubjects($con, $con_id, $exam=''){
		global $lang, $sms, $ig_mode_lvl, $ig_mode_exams;
		if($exam == ''){
			$exam= reset($ig_mode_exams);
		}
		$layout = new layout();
		$layout->this_year = $_SESSION['year'];
		$layout->next_year = $_SESSION['year']+1;
		$toolbox = array();
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="addService" con="'.$con.'" conid="'.$con_id.'"',
			"text"=> $lang['add'],
			"icon"=> "plus"
		);		
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="print_pre" rel="#service_list-'.$con.'-'.$con_id.'"',
			"text"=> $lang['print'],
			"icon"=> "print"
		);
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="saveAsPdf" rel="#service_list-'.$con.'-'.$con_id.'"',
			"text"=> $lang['save_as_pdf'],
			"icon"=> "print"
		);
		$toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="exportTable" rel="#service_list-'.$con.'-'.$con_id.'"',
			"text"=> $lang['export'],
			"icon"=> "disk"
		);

		$obj = $sms->getAnyObjById($con, $con_id);
		$services = $obj->getServices();
		if($con == 'student'){
			$layout->template = 'modules/services/templates/serviceig_student.tpl';
			$layout->student_name = $obj->getName();
			$services = do_query_array("SELECT DISTINCT(services) FROM materials_std WHERE std_id=$con_id ORDER BY services", $sms->db_year, $sms->ip);
			//$services = $obj->getServices();
			if($services != false){
				$layout->AL_hidden = 'hidden';
				$layout->AS_hidden = 'hidden';
				$layout->A2_hidden = 'hidden';
				$layout->OL_hidden = 'hidden';
				$layout->AL_trs = '';
				$layout->AS_trs = '';
				$layout->A2_trs = '';
				$layout->OL_trs = '';
				$select_ser[] = array();
				foreach($services as $ser){
					$service = new ServicesIG($ser->services);
					$td = array();
					foreach($ig_mode_exams as $exam){
						$std_ser_opt = do_query_obj("SELECT * FROM materials_std WHERE std_id=$con_id AND services=$service->id AND exam='$exam'", $sms->db_year, $sms->ip);
						$td[] = write_html('td', 'align="center"', ($std_ser_opt!=false ? $std_ser_opt->type : ''));
					}
						
					//$service = new ServicesIG($ser->services);
					$layout->{$service->lvl.'_trs'} .= write_html('tr', '',
						write_html('td', '', $service->getName()).
						implode('', $td)
					);
					$layout->{$service->lvl.'_hidden'} = '';
				}
			}
		} elseif($con == 'level' || $con == 'class'){
			$layout->level_name = $obj->getName();
			$layout->exam = strtoupper($exam);
			$layout->exam_year = $exam == 'nov' ? $_SESSION['year'] : $_SESSION['year'] +1;
			$exam_labels = array_map(function($word) { return ucwords($word); }, $ig_mode_exams);
			$toolbox[] =array(
				"tag" => "span",
				"attr"=> 'style="margin:0px 20px" class="ui-corner-all ui-state-default"',
				"text"=> write_html('text', 'style="padding: 4px"',$lang['exam'].': ').
					write_html_select('name="exam" con="'.$con.'" con_id="'.$con_id.'" update="reloadIgServices" class="ui-state-default def-float combobox" ', $exam_labels, ucwords($exam)),
				"icon"=> ""
			);
			$layout->template = 'modules/services/templates/serviceig_level.tpl';
			$all_services = array();
			
			$layout->AL_total = '';
			$layout->AS_total = '';
			$layout->A2_total = '';
			$layout->OL_total = '';
			foreach($ig_mode_lvl as $lvl){
				$layout->{$lvl.'_ths'} = '';
				$all_services[$lvl]=array();
				$services = ServicesIg::getList('', $lvl);
				if(count($services) > 0){
					$layout->{$lvl.'_th_colspan'} = count($services);
					$layout->{$lvl.'_th_width'} = count($services) * 20;
					foreach($services as $service){
						$all_services[$lvl][] = $service;
						$layout->{$lvl.'_ths'} .= write_html('th', 'class="{sorter:false}"', write_html('span', 'class="rotated"', $service->getName()));
						$total = do_query_obj("SELECT COUNT(std_id) AS tot FROM materials_std WHERE services=$service->id AND exam='$exam'", $sms->db_year, $sms->ip);
						$layout->{$lvl.'_total'} .= write_html('th', 'class="{sorter:false}"',
							$total->tot
						);
					}
				} else {
					$layout->{$lvl.'_th_hidden'} = 'hidden';
				}
			}
			$level = new Levels($con_id, $sms);
			$students = $level->getStudents(array('1'));
			$layout->students_trs = '';
			$count = 0;
			foreach($students as $student){
				$OL_tds = array();
				$AL_tds = array();
				$A2_tds = array();
				$AS_tds = array();
				$selected_services = do_query_obj("SELECT GROUP_CONCAT(services SEPARATOR ', ') AS sers FROM materials_std WHERE std_id=$student->id", $sms->db_year, $sms->ip);
				$cur_services =$selected_services!= false ? explode(',', $selected_services->sers) : array();
				foreach($ig_mode_lvl as $lvl){
					$sers = $all_services[$lvl];
					foreach($sers as $ser){
						if(in_array($ser->id, $cur_services)){
							$ex = do_query_obj("SELECT type FROM materials_std WHERE std_id=$student->id AND services=$ser->id AND exam='$exam'", $sms->db_year, $sms->ip);
							${$lvl.'_tds'}[] = write_html('td', '', ($ex!=false?$ex->type:''));
						} else {
							${$lvl.'_tds'}[] = write_html('td', '', '');
						}
					}
				}
				$layout->students_trs .= write_html('tr', '',
					write_html('td', 'class="unprintable"',
						write_html('button', 'module="students" std_id="'.$student->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
					).
					write_html('td', '', $student->cand_no).
					write_html('td', '', $student->getName()).
					(count($OL_tds) > 0 ? implode('',$OL_tds) : '').
					(count($AS_tds) > 0 ? implode('',$AS_tds) : '').
					(count($A2_tds) > 0 ? implode('',$A2_tds) : '').
					(count($AL_tds) > 0 ? implode('',$AL_tds) : '')
				);	
				$count++;
				//if($count > 50) {break;}
			}
			
					
		}
		
		$out = write_html('div', 'id="service_list-'.$con.'-'.$con_id.'"',
			createToolbox($toolbox).
			 $layout->_print()
		);
		return $out;
	}
	
	static function addStudentSubject($post){
		global $sms, $ig_mode_exams;;
		$mat_id = $post['material'];
		$result = true;
		$con_id =$post['con_id'];
		$cur_services = ServicesIg::getList($mat_id);
		$year =  $_SESSION['year'];
		$studentFees = new StudentFees(new Students($con_id, $sms));
		
		$cur_s_arr =array();
		foreach($cur_services as $service){
			if(do_delete_obj( "std_id=$con_id AND services=$service->id ", "materials_std", $sms->db_year, $sms->ip)!=false){
				foreach($ig_mode_exams as $e){
					if( $post[$service->id.'-'.$e] !=''){
						$f = explode('-', $post[$service->id.'-'.$e]);
						$fees = $studentFees->getStdServiceFeesByExam($service->id, $e, $year, $post[$service->id.'-'.$e]);
						$insert = array(
							'std_id'=>$con_id, 
							'services'=>$service->id,
							'exam'=>$e,
							'type'=>$post[$service->id.'-'.$e],
							'fees'=>$fees['fees'],
							'reg'=>$fees['reg']
						);
						if(do_insert_obj($insert, 'materials_std', $sms->db_year, $sms->ip)== false){
							$result = false;
						}
					}
				}
			} else {
				$result = false;
			}
		}
		if($result){
			$studentFees->generateServiceFees($year);
		}
		return $result;
	}
	

	static function removeStdService($std_id, $exam, $service_id=''){
		global $sms;
		if(do_delete_obj("std_id=$std_id AND exam='$exam' ".($service_id!='' ? "AND services=$service_id" :''), 'materials_std', $sms->db_year, $sms->ip)){
			//SchoolFees::getnerateRegistration($std_id, $_SESSION['year']);
			return true;
		} else {
			return false;
		}
	}

}
		
		
	
