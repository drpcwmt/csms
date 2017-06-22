<?php
 /* Bums
 *
 *
 */

 class Drivers extends Employers{

	public function __construct($id){
		global $busms;
		if($id!= ''){
			$driver_info = do_query_obj("SELECT * FROM drivers WHERE id=$id", $busms->database, $busms->ip);
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
		$layout->template = 'modules/drivers/templates/drivers_layout.tpl';
		return $layout->_print();
	}
	
	/*public function getTel($all=false){
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
	}*/


	static function getList(){
		global $busms;
		$sql = do_query_array("SELECT * FROM drivers", $busms->database, $busms->ip);
		$out = array();
		foreach($sql as $row){
			$hrms = new HrMS($row->hrms_id);
			$out[] = new Drivers($row->id, $hrms);
		}
		return $out;
	}

	static function loadMainLayout(){
		$layout = new Layout();
		$layout->template = 'modules/drivers/templates/driver_main.tpl';
		$drivers =  Drivers::getList();
		if(count($drivers) > 0) {
			$first_driver = reset($drivers);
			$layout->driver_layout = $first_driver->loadLayout();
		}
		$first = true;
		$layout->list_drivers = '';
		foreach($drivers as $driver){
			$d = new Layout($driver);
			$d->template = 'modules/drivers/templates/driver_list_item.tpl';
			if($first) $d->active = 'ui-state-active';
			$d->name = $driver->getName();
			$d->hrms_id = $driver->hrms_id;
			$layout->list_drivers .= $d->_print();
			$first = false;
		}
		return $layout->_print();
	}

	static function addDriver($post){
		global $busms;
		$driver_id = $post['driver_id'];
		if( do_insert_obj(array('id'=> $driver_id), 'drivers', $busms->database, $busms->ip) !== false){
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
		global $busms;
		$driver_id = $post['id'];
		if( do_update_obj($post, "id=$driver_id", 'drivers', $busms->database, $busms->ip) !== false){
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
		global $busms;
		$driver_id = $post['id'];
		if( do_delete_obj("id=".$_POST['id'], 'drivers' , $busms->database, $busms->ip) !== false){
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
		$layout->template = 'modules/drivers/templates/import_form.tpl';
		$hrmss = HrMS::getList();
		$layout->con='drivers';
		$layout->hrms_opts = write_select_options(objectsToArray($hrmss));
		return $layout->_print();
	}

	static function importDrivers($hrms_id){
		global $this_system, $lang;
		$hrms = new Hrms($hrms_id);
		$job_code = DriversJobCode;
		$error = '';
		$sql = "SELECT id, name_ltr, name_rtl FROM employer_data WHERE job_code=$job_code";
		$emps = do_query_array( $sql, $hrms->database, $hrms->ip);

		$num = 0;
		if(count($emps) > 0){
			foreach($emps as $emp){
				$emp_id = $emp->id;
				$emp_rtl = $emp->name_rtl;
				$emp_ltr = $emp->name_ltr;
				$chk = do_query_obj("SELECT id FROM drivers WHERE emp_id=".$emp->id." AND hrms_id=$hrms_id", $this_system->database, $this_system->ip);
				if( $chk  === false){
					if(do_insert_obj(array('emp_id'=>$emp_id, 'hrms_id'=>$hrms_id, 'name_ltr'=>$emp_ltr, 'name_rtl'=>$emp_rtl), "drivers",  $this_system->database, $this_system->ip) === false){
						$error = $lang['error_updating'];
					} else {
						$num++;
					}
				}  else {
					if(do_update_obj(array('name_ltr'=>$emp_ltr, 'name_rtl'=>$emp_rtl), "emp_id=$emp_id AND hrms_id= $hrms_id", "drivers",  $this_system->database, $this_system->ip) === false){
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