<?php
 /* Bums
 *
 *
 */

 class Drivers extends Employers{

	public function __construct($id){
		if($id!= ''){
			
			$driver_info = do_query_obj("SELECT * FROM drivers WHERE id=$id", MySql_Database);
			parent::__construct($driver_info->emp_id, new HrMS($driver_info->hrms_id));
			foreach ($driver_info as $key => $value) {
				$this->$key = $value;
			}
		}
	}	

	public function loadLayout(){
		$layout = new Layout($this);
		$layout->personel_info = parent::loadLayout();
		$layout->routes= 'Routes';
		$layout->template = 'modules/resources/templates/drivers_layout.tpl';
		return $layout->_print();
	}
	
	public function getTel($all=false){
		$rows = do_query_array("SELECT * FROM phonebook WHERE con='emp' AND con_id=$this->emp_id", $this->hrms->database, $this->hrms->ip);
		$rows = sortArrayOfObjects($rows, $this->hrms->getItemOrder('phonebook-emp-'.$this->emp_id), 'id');
		if( $all==false ){
			$first = reset($rows);
			return isset($first->tel) ? $first->tel : '';
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $r){
					$out[] = $r->tel;
				} 
			}
			return $out;
		}
	}


	static function getList(){
		$sql = do_query_array("SELECT * FROM drivers", MySql_Database);
		$out = array();
		foreach($sql as $row){
			$hrms = new HrMS($row->hrms_id);
			$out[] = new Drivers($row->id);
		}
		return $out;
	}

	static function loadMainLayout(){
		$layout = new Layout();
		$layout->template = 'modules/resources/templates/driver_main.tpl';
		$drivers =  Drivers::getList();
		if(count($drivers) > 0) {
			$first_driver = $drivers[0];
			$layout->driver_layout = $first_driver->loadLayout();
		}
		$first = true;
		$layout->list_drivers = '';
		foreach($drivers as $driver){
			$d = new Layout($driver);
			$d->template = 'modules/resources/templates/driver_list_item.tpl';
			if($first) $d->active = 'ui-state-active';
			$d->name = $driver->getName();
			$d->hrms_id = $driver->hrms_id;
			$layout->list_drivers .= $d->_print();
			$first = false;
		}
		return $layout->_print();
	}

	static function addDriver($post){
		$driver_id = $post['driver_id'];
		if( do_insert_obj(array('id'=> $driver_id), 'drivers', MySql_Database)){
			$answer['id'] = $driver_id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);

	}

	static function saveDriver($post){
		$driver_id = $post['id'];
		if( do_update_obj($post, "id=$driver_id", 'drivers', MySql_Database)){
			$answer['id'] = $driver_id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);

	}


	static function delDriver($post){
		$driver_id = $post['id'];
		if( do_query_edit("DELETE FROM drivers WHERE id=".$_POST['id'], MySql_Database)){
			$answer['id'] = $driver_id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);

	}

	static function importForm(){
		$layout = new Layout();
		$layout->template = 'modules/resources/templates/import_form.tpl';
		$hrmss = HrMS::getList();
		$layout->con='drivers';
		$layout->hrms_opts = write_select_options(objectsToArray($hrmss));
		return $layout->_print();
	}

	static function importDrivers($hrms_id){
		global $this_system, $lang;
		$hrms = new Hrms($hrms_id);
		$job_code = 7;
		$error = '';
		$sql = "SELECT id, name_ltr, name_rtl FROM employer_data WHERE job_code=$job_code";
		$emps = do_query_array( $sql, $hrms->database, $hrms->ip);

		$num = 0;
		if(count($emps) > 0){
			foreach($emps as $emp){
				$emp_id = $emp->id;
				$emp_rtl = $emp->name_rtl;
				$emp_ltr = $emp->name_ltr;
				$chk = do_query("SELECT id FROM drivers WHERE emp_id=".$emp->id." AND hrms_id=$hrms_id", $this_system->database, $this_system->ip);
				if( $chk  == false){
					if(!do_insert_obj(array('emp_id'=>$emp_id, 'hrms_id'=>$hrms_id, 'name_ltr'=>$emp_ltr, 'name_rtl'=>$emp_rtl), "drivers",  $this_system->database, $this_system->ip)){
						$error = $lang['error_updating'];
					} else {
						$num++;
					}
				}  else {
					if(!do_update_obj(array('name_ltr'=>$emp_ltr, 'name_rtl'=>$emp_rtl), "emp_id=$emp_id AND hrms_id= $hrms_id", "drivers",  $this_system->database, $this_system->ip)){
						$error = $lang['error_updating'];
					} 
				}
			}
		} else {
			$error = $lang['error-employers_not_found'];
		}
		return "{\"error\" : \"$error\", \"num\" : \"$num\"}";
	}
	static function getAutocompleteDriver( $value){
		global $lang, $busms;
		$field = "name_".$_SESSION['dirc'];
		$Arabic = new I18N_Arabic('KeySwap');
		$params = array("name_rtl = '$value' ",
			"name_rtl LIKE '$value%' ",
			"name_rtl LIKE '".addslashes($Arabic->swapEa($value))."%' ",
			"LOWER(name_ltr) LIKE LOWER('$value') ",
			"LOWER(name_ltr) LIKE LOWER('$value%') ",
			"LOWER(name_ltr) LIKE LOWER('".addslashes($Arabic->swapAe($value))."%')"
		);
			
		$sql = "SELECT * FROM drivers WHERE (". implode(" OR ", $params) .")";
//echo $sql;
		$out = array();
		$matrons = do_query_array( $sql);
		if($matrons != false && count($matrons)>0){
			foreach($matrons as $matron ){
					$label = str_replace($value, write_html('b', 'style="color:red"', $value), $matron->$field);
					$row = array('id'=>$matron->id, 'name'=> $matron->$field, 'label'=> $matron->$field);
					$out[] = $row;
			}
		} else {
			$out[] = array('error' => $lang['cant_find_item']);	
		}
		return json_encode($out);
	}

}