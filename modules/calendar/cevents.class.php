<?php
/* Events .class
*
*/

class cEvents {
	
	public function __construct($id){
		global $sms;
		if($id != ''){	
			$event = do_query_obj("SELECT * FROM events WHERE id=$id", $sms->db_year);	
			if(isset( $event->id )){
				foreach($event as $key =>$value){
					$this->$key = $value;
				}
			}	
		}	
	
	}
	
	public function getName(){
		global $lang, $sms;
		if(!isset($this->name)){
			$type_q = do_query_obj("SELECT name FROM events_label WHERE id=$this->event_id", $sms->database);
			$this->name = $type_q->name;
		}
		return $this->name;
	}
	
	public function getCons(){
		global $sms;
		return do_query_array("SELECT con, con_id FROM events_con WHERE event_id=$this->id", $sms->db_year);
	}
	
	public function _delete(){
		global $sms;
		if(do_query_edit( "DELETE FROM events WHERE id=$this->id", $sms->db_year)){
			do_query_edit("DELETE FROM events_con WHERE event_id=$this->id", $sms->db_year);
			// insert meaage

			$answer['error'] = "";
		} else {
			$answer['error'] = $lang['error_updating'];
		}
		return $answer;

	}
	
	static function getEventsByDay($day, $con , $con_id){
		global $sms;
		$out = array();
		$con_arr = cEvents::GetEventUserCon($con, $con_id);
		$sql = "SELECT DISTINCT events.id FROM events, events_con 
		WHERE events.id = events_con.event_id
		AND (
			(events.begin_date<$day AND events.end_date>=$day)
			OR (events.begin_date=$day)
		)
		AND (
			( events.user_id=".$_SESSION['user_id'].")
			OR ".implode(' OR ', $con_arr)."
		)";
		//echo $sql;
		$evs =  do_query_array($sql, $sms->db_year);
		if($evs != false  && count($evs) > 0){
			foreach($evs as $ev){
				$out[] = new cEvents($ev->id);
			}
		}
		return $out;
	}
	
	public function loadLayout(){
		global $lang, $sms;
		$event = $this;
		$events_arr[0] = $lang['holiday'];
		$events = do_query_array( "SELECT DISTINCT(name), id FROM events_label", $sms->database);
		foreach($events as $ev){
			$events_arr[$ev->id] = $ev->name;
		} 
		include('con_menus.php');
		$event->con_menu = $con_menus;
		$event->events_options = write_select_options( $events_arr,  $event->event_id, false);
		
		// cons 
		$cons = $this->getCons();
		$cons_str = array();
		$cons_list = array();
		foreach($cons as $con){
			$cons_str[] = $con->con.'-'.$con->con_id;
			$cons_list[] = write_html('li', 'class="ui-state-default hoverable"', 
				$sms->getAnyNameById( $con->con, $con->con_id).
				write_html('text', 'class="mini"', ' ('.$lang[$con->con].')').
				write_html('a', 'class="close ui-icon ui-icon-close def_float" action="removeCon" con="'.$con->con.'-'.$con->con_id.'"', '')
			);
		}
		$event->tot_con = implode(',', $cons_str);
		$event->cons_list = implode('', $cons_list);
		return fillTemplate('modules/calendar/templates/events.tpl', $event);
	}

	static function _new($date){
		global $lang, $sms;
		$event = new stdClass();
		$event->begin_date = unixToDate($date);
		$event->begin_time = $sms->getSettings('day_time_begin');
		$event->end_time = $sms->getSettings('day_time_end');
		$events_arr[0] = $lang['holiday'];
		$events = do_query_array( "SELECT DISTINCT(name), id FROM events_label", $sms->database);
		foreach($events as $ev){
			$events_arr[$ev->id] = $ev->name;
		} 
		include('con_menus.php');
		$event->con_menu = $con_menus;
		$event->events_options = write_select_options( $events_arr, '', false);
		return fillTemplate('modules/calendar/templates/events.tpl', $event);
	}

	static function _save($post){
		global $lang, $sms;
		$error = false;
		$cons = cEvents::conToArray($post['tot_con']);
		if($post['new_type'] != ''){
			$type_id = do_insert_obj(array('name'=>$post['new_type']), 'events_label', $sms->database);
		} else {
			$type_id = $post['event_type'];
		}
		$vals = $post;
		$vals['user_id'] = $_SESSION['user_id'];
		$vals['event_id'] = $type_id;
		
		if($post['id'] != '' ){ // Update Event
			$event_id = $post['id'];
			if( do_update_obj($post, "id=$event_id", 'events', $sms->db_year) != false){
				do_query_edit("DELETE FROM events_con WHERE event_id=$event_id", $sms->db_year);
				$sql_cons = array();
				foreach($cons as $array){
					$group = $array[0];
					$id = $array[1];
					$sql_cons[] = "($event_id, '$group', $id)";
				}
				if(!do_query_edit("INSERT INTO events_con (event_id, con, con_id) VALUES ".implode(',', $sql_cons), $sms->db_year)){
					$error = true;
				}
			} else {
				$error = true;
			}
			
		} else { // New Event
			if($type_id == '0'){ // Holiday
				$event_id = 0;
				$date = dateToUnix($post['begin_date']);
				$end_date = $post['end_date'] != '' ? dateToUnix($post['end_date']) : $date;
				$sql_cons = array();
				while($date <= $end_date){
					foreach($cons as $array){
						$group = $array[0];
						$id = $array[1];
						$sql_cons[] = "($date, '$group', $id)";
					}
					$date = mktime(0,0,0, date('m', $date), date('d', $date)+1, date('Y', $date));
				}
				if(!do_query_edit("INSERT INTO holidays (dates, con, con_id) VALUES ".implode(',', $sql_cons), $sms->db_year)){
					$error = true;
				}
				
			} else {
				if($type_id !== false && $type_id !== '' ){				
					if($event_id = do_insert_obj($vals, 'events', $sms->db_year)){
						$sql_cons = array();
						foreach($cons as $array){
							$group = $array[0];
							$id = $array[1];
							$sql_cons[] = "($event_id, '$group', $id)";
						}
						if(!do_query_edit("INSERT INTO events_con (event_id, con, con_id) VALUES ".implode(',', $sql_cons), $sms->db_year)){
							$error = true;
						}
					} else {
						$error = true;
					}
				} else {
					$error = true;
				}
			}
		}
		if(!$error){
			if(isset($post['alert']) && $post['alert'] ==2){
				$type_name = cEvents::getEventLabelById($type_id);
				$title =  addslashes($type_q->name).': '.
					$post['begin_date'].
					($post['end_date'] != '' ? 
						' - '.$post['end_date'] 
					: '').
					' - '.
					($post['begin_time'] != '' ?
						$post['begin_time'] 
					:  
						unixToTime($sms->getSettings('day_time_begin'))
					).
					' - '.
					($post['end_time'] != '' ? 
						$post['end_time'] 
					: 
						unixToTime($sms->getSettings('day_time_end'))
					);

				//sendEventMessage( $c[0], $c[1], $title, addslashes($post['comments']))	;
			}
			$answer['id'] = $event_id;
			$answer['error'] = "";
		} else {
			$answer['error'] = $lang['error_updating'];
		}
		return $answer;
		
	}

	static function getEventLabelById($label_id){
		global $lang, $sms;
		if($label_id == 0){
			return $lang['holiday'];
		} else {
			$type_q = do_query_obj("SELECT name FROM events_label WHERE id=$label_id", $sms->database);
			return $type_q->name;
		}
	}
	
	static function conToArray($str){
		$out = array();
		$cons = strpos($str, ',') !== false ? explode(',', $str) : array($str);
		foreach($cons as $con_str){
			$c = explode('-', $con_str);
			$out[] = array($c[0], $c[1]);
		}
		//print_r($str);
		return $out;
	}
	
	static function GetEventUserCon($group, $id){
		global $sms;
		$where[] = "(con IS NULL AND con_id IS NULL)";
		$where[] = "(con='$group' AND con_id=$id)";
		$where[] = "(con='$group' AND con_id=0)";
		$where[] = "(con='etab' AND con_id=0)";
		
		$obj = $sms->getAnyObjById($group, $id);
		$parents = array();
		switch($group){
			case "class":
				$level = $obj->getLevel();
				$parents[]= array("level" ,$level->id);
				$parents[]= array("etab" ,$level->id);
			break;
			case "group":
				$par = $obj->getParentObj();
				if(get_class($par) == 'Levels'){
					$parents[]= array("level" => $par->id);
				} elseif(get_class($par) == 'class'){
					$parents[]= array("class", $par->id);
					$level = $par->getLevel();
					$parents[] =  array("level", $level->id);
				}
			break;
			case "student":
				$class = $obj->getClass();
				$level = $obj->getLevel();
				$etab = $obj->getEtab();
				$groups = $obj->getGroups();
				foreach($groups as $group){
					$parents[] =array('group', $group->id);
				}
				$parents[] = array("class", $class->id);
				$parents[] =  array("level", $level->id);
				$parents[] =  array("etab", $etab->id);
			break;
			case "parent":
				$students = $obj->getChildrens();
				foreach($students as $student){
					$parents = array_merge($parents, GetEventUserCon('student', $student->id));
					$parents[] = array("parent-class", $student->getClass()->id);
					$parents[] = array("parent-level", $student->getLevel()->id);
					$parents[] = array("parent-etab", $student->getEtab()->id);
				}
			break;
			case "prof":
				$classes = $obj->getClassList();
				foreach($classes as $class){
					$parents[] = array("class", $class->id);
					$parents[] = array("level", $class->getLevel()->id);
					$parents[] = array("etab", $class->getEtab()->id);
					$parents[] = array("prof-class", $class->id);
					$parents[] = array("prof-level", $class->getLevel()->id);
					$parents[] = array("prof-etab", $class->getEtab()->id);
				}
			break;
			case "supervisor":
				$classes = $obj->getClassList();
				foreach($classes as $class){
					$parents[] = array("class", $class->id);
					$parents[] = array("level", $class->getLevel()->id);
					$parents[] = array("etab", $class->getEtab()->id);
					$parents[] = array("supervisor-class", $class->id);
					$parents[] = array("supervisor-level", $class->getLevel()->id);
					$parents[] = array("supervisor-etab", $class->getEtab()->id);
				}
			break;
			case "coordinator":
				$levels = $obj->getLevelList();
				foreach($levels as $level){
					$parents[] = array("level", $level->id);
					$parents[] = array("etab", $level->getEtab()->id);
					$parents[] = array("coordinator-level", $level->id);
					$parents[] = array("coordinator-etab", $level->getEtab()->id);
					$classes = $level->getClassList();
					foreach($classes as $class){
						$parents[] = array("class", $class->id);
					}
				}
			break;
			case "principal":
				$levels = $obj->getLevelList();
				foreach($levels as $level){
					$parents[] = array("level", $level->id);
					$parents[] = array("etab", $level->getEtab()->id);
					$parents[] = array("principal-level", $level->id);
					$parents[] = array("principal-etab", $level->getEtab()->id);
					$classes = $level->getClassList();
					foreach($classes as $class){
						$parents[] = array("class", $class->id);
					}
				}
			break;
			default:
				$parents[] = array("level");
				$parents[] = array("class");
				$parents[] = array("etab");
			break;
		}
		$final = array();
		foreach($parents as $parent){
			if(!in_array($parent, $final)){
				if(isset($parent[1])){
					$where[] = "(con='".$parent[0]."' AND con_id='".$parent[1]."')";
				} else {
					$where[] = "(con='".$parent[0]."')";
				}
				$final[] = $parent;
			}
		}
		
		return $where;

	}
			
}