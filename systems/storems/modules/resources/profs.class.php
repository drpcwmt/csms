<?php
/** Profs
*
*/

class Profs extends Employers {
	private $thisTemplatePath = 'modules/recources/templates';

	public function __construct($id){
		global $sms;
		$hrms = $sms->getHrms();
		try {	
			parent::__construct($id, $hrms);
			$prof = do_query_obj("SELECT * FROM profs WHERE id='$id'", $sms->database, $sms->ip);	
			if(isset($prof->id)){
				foreach($prof as $key =>$value){
					$this->$key = $value;
				}
			} else {
				throw new Exception('id not found');
			}	
		} catch(Exception $e){
			$this->id = "";
			$this->name = "N/A";
		}
			
	}
	
	public function getSupervisors(){
		$supers = array();
		if(!isset($this->supervisors)){
			$services = do_query_array("SELECT DISTINCT services FROM schedules_lessons WHERE prof=$this->id;", DB_Year);
			if($services != false && count($services) > 0){
				foreach($services as $service_id){
					$super = do_query_obj("SELECT id FROM supervisors WHERE services=$service_id", DB_student);
					if(isset($super->id)){
						$supers[] = $super->id;
					}
				}
			}
			$this->supervisors = $supers;
		}
		return $this->supervisors;
	}
	
	public function getClassList(){
		global $sms;
		$sql = "SELECT schedules_date.con, schedules_date.con_id 
		FROM schedules_date, schedules_lessons
		WHERE schedules_date.id = schedules_lessons.rec_id
		AND schedules_lessons.prof =".$_SESSION['user_id'];
		$classes_arr = array();
		$classes = do_query_array( $sql, Db_prefix.$_SESSION['year']);
		foreach($classes  as $class){
			if($class->con == 'group'){
				$parent = get_gr_parent($class->con_id);
				if($parent[0] == 'class'){
					$class_id = $parent[1];
				}
			} elseif($class->con == 'class'){
				$class_id = $class->con_id;
			}
			if(!in_array($class_id, $classes_arr)){
				$classes_arr[] = new Classes($class_id);
			}
		}
		return sortArrayOfObjects($classes_arr, getItemOrder('classes'), 'id');
	}
	
	public function getServices($con=false, $con_id=false){
		global $sms;
		if(!isset($this->services)){
			$out= array();
			if($con == false){
				$services= do_query_array("SELECT services FROM profs_materials WHERE id =".$this->id, DB_student);
				if(count($services) > 0){
					foreach($services as $row ){
						$service =  new services($row->services);
						if(isset($service->id)){
							$out[] = $service;
						} 
					}
				}
			} else {
				$out = schedule::getProfScheduleService($this->id, $con, $con_id);
				/*$where =array();
				$sql = "SELECT DISTINCT schedules_lessons.services FROM schedules_date, schedules_lessons 
				WHERE schedules_lessons.rec_id =schedules_date.id
				AND schedules_lessons.prof =$this->id ";
				if($con != false && $con != ''){
					$parents = getParentsArr($con, $con_id);
					foreach($parents as $array){
						$par_con =$array[0];
						$par_id= $array[1];
						$where[] = "(schedules_date.con='$par_con' AND schedules_date.con_id='$par_id')";
					};
					$childs = getChildsArr($con, $con_id);
					foreach($childs as $array){
						$child_con = $array[0];
						$child_con_id = $array[1];
						if(!in_array( "(schedules_date.con ='$child_con' AND schedules_date.con_id = $child_con_id)", $where)){
							$where[] = "(schedules_date.con ='$child_con' AND schedules_date.con_id = $child_con_id)";
						}
					}
					$sql .= "AND (
						(schedules_date.con ='$con' AND schedules_date.con_id = $con_id)".
						(count($where) > 0 ?
							"OR ".implode(' OR ', $where)
						: '').
					")";
				}
				$q = do_query_array( $sql, DB_year);
				foreach($q as $r){
					$new_service = new services($r->services);
					if($new_service != false){
						$out[] = $new_service;
					}
				}*/
				// get Materials not in schedule but flaged marks
				$prof_services = $this->getServices();
				$obj = $sms->getAnyObjById($con, $con_id);
				$obj_services = $obj->getServices();
				foreach($obj_services as $ser){
					if(in_array($ser, $prof_services) && !in_array($ser, $out) && $ser->mark == '1' && $ser->schedule == '0' ){
						$out[] = $ser;
					}
				}
			}
			$this->services = $out;
		}
		return Services::orderService($this->services);
	}

	public function getCurClass(){
		$unix_date = mktime(0,0,0,date('m'), date('d'), date('Y'));
		$unix_time = time() - $unix_date;
		
		// search for cur class for cur date
		$sql =  "SELECT schedules_date.*, schedules_lessons.* , schedules_times.*
		FROM schedules_date, schedules_lessons, schedules_times
		WHERE schedules_date.id = schedules_times.rec_id
		AND schedules_date.id = schedules_lessons.rec_id
		AND schedules_date.date=$unix_date
		AND ( schedules_times.`begin` <= $unix_time AND schedules_times.`end` >= $unix_time)
		AND schedules_lessons.prof = $this->id ";
		//echo $sql;
		$chk_exp_val = do_query_array($sql,Db_prefix.$_SESSION['year'] );
		if(count($chk_exp_val) > 0){
			$row = $chk_exp_val[0];
			$con = $row->con;
			$con_id = $row->con_id;
		} else{
			// search for cur class in the default dates
			$sql = "SELECT schedules_date.*, schedules_lessons.* , schedules_times.*
			FROM schedules_date, schedules_lessons, schedules_times
			WHERE schedules_date.id = schedules_lessons.rec_id
			AND schedules_date.date < 7
			AND (schedules_times.`begin` <= $unix_time AND schedules_times.`end` >= $unix_time)
			AND schedules_lessons.prof = $this->id ";
			$chk_exp_val = do_query_array( $sql, Db_prefix.$_SESSION['year']);
			if(count($chk_exp_val) > 0){
				$row = $chk_exp_val[0];
				$con = $row->con;
				$con_id = $row->con_id;
			}
	
		}
		if(isset($con)){
			return $con_id;
		} else {
			return false;
		}
	}

	public function loadLayout(){
		global $prvlg;
		$layout = new Layout($this);
		$layout->prof_name = $this->getName();
		$serviceManager = new ServicesManager('prof', $this->id);
		$layout->service_table = $serviceManager->loadLayout();
		$layout->toolbox = Resources::getItemsToolbox('profs', $this->id);
		if($prvlg->_chk('profil_read-profs')!= true){
			$layout->profil_data = 'hidden';
		}
		unset($layout->services);
		return fillTemplate('modules/resources/templates/profs.tpl', $layout);
	}
	
	static function _save($post){
		$id = $post['id'];
		$max = $post['max'];
		if(do_query_edit("INSERT INTO profs (id, max) VALUES( $id, '$max')", DB_student)){			
			$prof = new Profs($id);
				// create user login
			$user = new Users('prof', $id);
			$answer['id'] = $id;
			$answer['title'] = $prof->getName();
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	
	}
	
	static function _delete($id){
		if(do_query_edit("DELETE FROM profs WHERE id=$id", DB_student)){
			// delete prof materials
			do_query_edit("DELETE FROM profs_materials WHERE id=$id", DB_student);
			
			// delete prof documents and share and user login 
			$user = new Users('prof', $id);
			$user->_delete();
			// handel schedule
			
			// remove from class reponsability
			do_query_edit("UPDATE classes SET resp='' WHERE resp=$id", Db_prefix.$_SESSION['year']);
			
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function _new(){
		return fillTemplate('modules/resources/templates/profs_new.tpl', array());
	}
	
	static function getList(){
		global $sms;
		$hrms = $sms->getHrms();
		$out = array();
		$profs = do_query_array("SELECT id FROM profs", $sms->database, $sms->ip);
		foreach($profs as $prof){
			try{
				$out[] = new Profs($prof->id);
			} catch(Exception $e){
				//
			}
		}
		return $out;
	}

	static function _import(){
		$error = '';
		global $sms;
		$hrms= $sms->getHrms();
		$sql = "SELECT id FROM employer_data WHERE job_code=9";
		$hr_profs = do_query_array( $sql, $hrms->database, $hrms->ip);
		$num = 0;
		if(count($hr_profs) > 0){
			foreach($hr_profs as $prof){
				$chk = do_query_obj("SELECT * FROM profs WHERE id=".$prof->id, $sms->database, $sms->ip);
				if(!isset($chk->id)){
					if(!do_query_edit("INSERT INTO profs (id) VALUES (".$prof->id.")",  $sms->database, $sms->ip)){
						$error = $lang['error_updating'];
					} else {
						$num++;
					}
				} 
			}
		} else {
			$error = $lang['error-employers_not_found'];
		}
		
		return array('error'=>$error, 'num'=>$num);
	}
}
?>