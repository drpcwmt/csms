<?php
## SMS functions

function getLevelFromCon($con, $con_id){
	global $this_system;
	$object = $this_system->getAnyObjById($con, $con_id);
	if($con == 'level'){
		return $object;
	} else {
		return $object->getLevel();
	}
}

## SMS Functions ##
/***************************** NAMES *****************************/
	// all name from id ( levels, classes, groups, students)

/***************************** Parents con *************************************************************/
	// student => class
function getClassIdFromStdId($id){
	if($id != false && $id != ''){	
		$r = do_query("SELECT class_id FROM classes_std WHERE std_id=$id", Db_prefix.$_SESSION['year']);	
		if($r["class_id"] != ''){
			return $r["class_id"];
		} else {
			return false;
		}	
	} else { return false;}
}
	// student = > group
function getGroupByStd($std_id){
	if($std_id != false && $std_id != ''){	
		$sql = "SELECT groups.id FROM groups, groups_std WHERE groups.id=groups_std.group_id AND groups_std.std_id = '$std_id'";
		$query = do_query_resource($sql, Db_prefix.$_SESSION['year']);		
		
		$groups_id =array();
		if(mysql_num_rows($query)> 0){
			while($row = mysql_fetch_assoc($query)){
				$groups_id[] = $row['id'];
			}
		}
		if(count($groups_id) > 0){
			return $groups_id;
		} else {
			return false;
		}
	} else { return false;}
}
	// student => bus
function getBusForStd($std_id){
	global $MS_settings;
	$enable_busms = $MS_settings['busms_server'] == 1 ? true : false;
	if($enable_busms){
		$row = do_query("SELECT route_id FROM route_std WHERE con='std' AND con_id=$std_id", BUSMS_Database, $MS_settings['busms_server_ip']);
		$rep = $row['route_id'];
	} else {
		$row = do_query( "SELECT bus_code FROM student_data WHERE id=$std_id", MySql_Database);
		$rep =  $row['bus_code'];
	}
	
	return ($rep != '') ? $rep : '&nbsp;';
}
	// student => parent
function getParentIdFromStdId($stdid){
	if($stdid != false && $stdid != ''){
		$row = do_query("SELECT parent_id FROM student_data WHERE id='$stdid'", MySql_Database);	
		if( $row["parent_id"] !="" ){
			return $row["parent_id"];
		} else {
			return false;
		}
	} else { return false; }
}

function getParentNameFromStdId($parent, $std_id){
	if($std_id != ''){
		//echo $parent."_name";
		$field = $_SESSION['lang'] == 'ar' ? $parent.'_name_ar' : $parent.'_name';
		$std = do_query("SELECT parent_id FROM student_data WHERE id=$std_id", MySql_Database);
		$row = do_query("SELECT $field FROM parents WHERE id=".$std['parent_id'], MySql_Database);	
		if( $row[$field] !=""){
			return $row[$field] ;
		} else {
			return false;
		}
	} else { return false; }
}
	// class => level
function getlevelByClass($class_id){
	if($class_id != ''){	
		$r = do_query( "SELECT level_id FROM classes WHERE id=$class_id",Db_prefix.$_SESSION['year']);		
		$level_id =  $r["level_id"];
		
		$l = do_query("SELECT id FROM levels WHERE id=$level_id", DB_student);
		if($l['id'] != ''){
			return $level_id;
		} else {
			return false;
		}
	} else { return false;}
}
	// level => etab
function getEtabFromLevel($id){
	if($id != ''){	
		$r = do_query("SELECT etab_id FROM levels WHERE id=$id", DB_student);		
		if($r['etab_id'] != ''){
			return $r['etab_id'];
		} else {
			return false;
		}
	} else { return false;}
}
	// group = > parents
function get_gr_parent($group_id){
	if($group_id != ''){	
		$r = do_query( "SELECT parent, parent_id FROM groups WHERE id=$group_id", Db_prefix.$_SESSION['year']);	
		if($r["parent"] != ''){
			return array($r["parent"], $r["parent_id"]);
		} else {
			return false;
		}	
	} else { return false;}
}

	// ANY => parent con
function getParentsArr($con, $con_id){
	global $sms;
	$parents =array();
	$obj = $sms->getAnyObjById($con, $con_id);
	switch($con){
		case "class":
			$level = $obj->getLevel();
			$level_id = ($level) ?$level->id : false;
			$parents[]= array("level" ,$level_id);
		break;
		case "group":
			$parent = $obj->getParentObj();
			if(get_class($parent) == 'Etabs'){
				$levels = $parent->getLevelList();
				foreach($levels as $level){
					$parents[]= array("level" , $level->id);
				}
			} elseif(get_class($parent) == 'Levels'){
				$parents[]= array("level" , $parent->id);
			} elseif(get_class($parent) == 'Classes'){
				$parents[]= array("class" , $parent->id);
				$level = $obj->getLevel();
				$parents[] =  array("level", $level->id);
			}
		break;
		case "student":
			$i=0;
			$class_id = $obj->getClass()->id;
			$level_id = $obj->getLevel()->id;
			$sql_group= "SELECT groups.id FROM groups , groups_std
			WHERE groups.id=groups_std.group_id
			AND groups_std.std_id=$con_id";
			
			$g_query = do_query_array( $sql_group, Db_prefix.$_SESSION['year']);
			$parents= array();
			if(count($g_query) > 0){
				foreach($g_query as $rgroups ){
					$parents[] =array('group', $rgroup->id);
				}
			}
			$parents[] = array("class", $class_id);
			$parents[] =  array("level", $level_id);
		break;
	}
	return isset($parents) ? $parents : false;
}
	// ANY => parent
/*function getParent($con, $reqid){
	switch($con){
		case "std":
			$parent = 'class';
			$parent_id = getlevelByClass(getClassIdFromStdId($reqid));
			break;
		case "group":
			$par = get_gr_parent($reqid);
			$parent = $par[0];
			if($parent == 'level') {$parent_id = $par[1];}
			elseif($parent == 'class') {$parent_id = getlevelByClass($par[1]);}
			break;
		case "class":
			$parent ='level';
			$parent_id = getlevelByClass($reqid);
			break;
		case "level":
			$parent ='etab';
			$parent_id = getEtabFromLevel($reqid);
			break;
	}
	return array($parent, $parent_id);
}*/
	// ANY => Level
/*function getLevelIdFromCon($con, $con_id){
	switch($con){
		case "level":
			return ($con_id) ? $con_id : false;
		case "class":
			return ($con_id) ? getlevelByClass($con_id) : false;
		break;
		case "group":
			$par = get_gr_parent($con_id);
			if($par[0] == 'level'){
				return $par[1];
			} elseif($par[0] == 'class'){
				$class_id = $par[1];
				return getlevelByClass($class_id);
			}
		break;
		case "student":
			$class_id = getClassIdFromStdId($con_id);
			return ($class_id) ? getlevelByClass($class_id) : false;
		break;
	}
}*/

/***************************** Student Listes ******************************************************************/
	// parent => students
function getStdsFromParent($parent_id){	
	$out = array();
	$sql = "SELECT id, name, name_ar FROM student_data WHERE student_data.parent_id=$parent_id"; 
	$stds = do_query_resource($sql, MySql_Database);
	while($s = mysql_fetch_assoc($stds)){
		$out[$s['id']] = $_SESSION['lang'] == 'ar' ? $s['name_ar'] : $s["name"] ;
	}
	return $out;
}
	// class => students
function getStudentIdsByClass($class_id, $all_stat=false){
	if($class_id != ''){	
		global $MS_settings;
		$db_year =Db_prefix.$_SESSION['year'];
		$field_order_name = $_SESSION['lang'] == 'ar' ? "name_ar" : "name";
		$sql = "SELECT $db_year.classes_std.std_id FROM $db_year.classes_std, ".DB_student.".student_data WHERE $db_year.classes_std.class_id=$class_id AND $db_year.classes_std.std_id=".DB_student.".student_data.id";
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
		
		$sql .= " ORDER BY ".DB_student.".student_data.sex, ".DB_student.".student_data.$field_order_name";
		$query = do_query_resource($sql , DB_student);	
		$std_ids =array();
		if(mysql_num_rows($query) > 0){
			while($r = mysql_fetch_assoc($query)){
				$std_ids[] = $r['std_id'];
			}
		}
		if(count ($std_ids) > 0){
			return $std_ids;
		} else {
			return false;
		}
	} else { return false;}
}
	// class => groups
function getGroupsIdsByClass($class_id){
	if($class_id != ''){	
		$groups =array();
		$query = do_query_resource("SELECT id FROM groups WHERE parent='class' AND parent_id=$class_id", Db_prefix.$_SESSION['year']);	
		if(mysql_num_rows($query) > 0){
			while($r = mysql_fetch_assoc($query)){
				$groups[] = $r['id'];
			}
			return $groups;
		} else {
			return false;
		}
	} else { return false;}
}
	// group => students
function getStudentIdsByGroup($group_id, $all_stat=false){
	if($group_id != ''){
		global $MS_settings;
		$field_order_name = $_SESSION['lang'] == 'ar' ? "name_ar" : "name";
		$db_year = Db_prefix.$_SESSION['year'];
		$sql = "SELECT $db_year.groups_std.std_id FROM $db_year.groups_std, ".DB_student.".student_data WHERE $db_year.groups_std.group_id=$group_id AND $db_year.groups_std.std_id=".DB_student.".student_data.id";
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
		$sql .= " ORDER BY ".DB_student.".student_data.sex, ".DB_student.".student_data.$field_order_name";
		$ids = array();
		$stds = do_query_resource($sql, DB_student);
		while($std = mysql_fetch_assoc($stds)){
			$ids[]= $std['std_id'];	
		}
		return count($ids) > 0 ? $ids : false;
	} else {
		return false;
	}
}
	// level => classes
function getClassesByLevel($level_id){
	$out = array();
	if($level_id != ''){	
		$sql = "SELECT id FROM classes WHERE level_id=$level_id";
		$query = do_query_resource( $sql, Db_prefix.$_SESSION['year']);		
		
		$class_ids =array();
		while($row = mysql_fetch_assoc($query)){
			$class_ids[] = $row['id'];
		}

		if(count ($class_ids) > 0){
			$order = getItemOrder('classes');
			foreach($order as $item){
				if(in_array($item, $class_ids)){
					$out[] = $item;
				}
			}
			return $out;
		} else {
			return false;
		}
	} else { return false;}
}
	// level => groups
function getGroupsIdsByLevel($level_id){
	if($level_id != ''){	
		$groups =array();
		$query = do_query_resource("SELECT id FROM groups WHERE parent='level' AND parent_id=$level_id", Db_prefix.$_SESSION['year']);	
		if(mysql_num_rows($query) > 0){
			while($r = mysql_fetch_assoc($query)){
				$groups[] = $r['id'];
			}
		}
		if(count ($groups) > 0){
			return $groups;
		} else {
			return false;
		}
	} else { return false;}
}
	// Level => students
function getStudentIdsByLevel($level_id){
	if($level_id != ''){
		$out = array();
		$classes = getClassesByLevel($level_id);
		foreach($classes as $class_id){
			$stds = getStudentIdsByClass($class_id);
			if($stds != false){
				$out = array_merge($out, $stds);	
			}
		}
		return $out;
	} else {
		return false;
	}
}
	// ANY => student
function getStdIds($table, $id){
	$t = array();
	if($table=='level'){
		return getStudentIdsByLevel($id);
	} elseif($table == "class"){
		return getStudentIdsByClass($id);
	}elseif($table == "group"){
		return getStudentIdsByGroup($id);
	}elseif($table == "student"){
		return array($id);
	}	
}
	// ANY => count student
function getStdNo($table, $id){
	$db_year = Db_prefix.$_SESSION['year'];
	if($table=='level'){
		$sql = "SELECT $db_year.classes_std.std_id, $db_year.classes.level_id FROM $db_year.classes_std, $db_year.classes, ".DB_student.".student_data WHERE ".DB_student.".student_data.status=1 AND ".DB_student.".student_data.id=$db_year.classes_std.std_id AND $db_year.classes.id=$db_year.classes_std.class_id AND $db_year.classes.level_id='$id'";
		
	} elseif($table == "class"){
		$sql = "SELECT $db_year.classes_std.std_id FROM $db_year.classes_std, ".DB_student.".student_data WHERE 
		".DB_student.".student_data.status=1
		AND ".DB_student.".student_data.id=$db_year.classes_std.std_id
		AND $db_year.classes_std.class_id='$id'";
	}elseif($table == "group"){
		$sql = "SELECT $db_year.groups_std.std_id FROM $db_year.groups_std WHERE $db_year.groups_std.group_id='$id'";
	}
	$r = do_query_resource($sql ,DB_student);
	return mysql_num_rows($r);
}

/**************** Other list *****************/
	// Etab => level
function getLevelsByEtab($etab_id){
	if($etab_id != ''){	
		$sql = "SELECT id, name_".$_SESSION['dirc']." FROM levels WHERE etab_id=$etab_id";
		$query = do_query_resource( $sql, DB_student);		
		
		$level_ids =array();
		while($row = mysql_fetch_assoc($query)){
			$level_ids[$row['id']] = $row["name_".$_SESSION['dirc']];
		}
		if(count ($level_ids) > 0){
			return sortArrayByArray($level_ids, getItemOrder('levels'));
		} else {
			return false;
		}
	} else { return false;}
}
	// level list
function get_grade_list($full = false){
	$grades = array();
	if($full){
		$levels = do_query_resource("SELECT id, name_".$_SESSION['dirc']." FROM levels", DB_student);
		while($level = mysql_fetch_assoc($levels)){
			$grades[$level['id']] = $level["name_".$_SESSION['dirc']];	
		}
	} else {
		if( $_SESSION['group'] == 'principal' ){
			$principal = new Principals($_SESSION['user_id']);
			return $principal->getLevelList();
		} elseif($_SESSION['group'] == 'supervisor'){
			$supervisor = new Supervisors($_SESSION['user_id']);
			return $supervisor->getClassList();

			foreach($classes as $class){
				$level= $class->level_id;
				if(!in_array($class->level_id, $selected)){
					$selected[] = $class->level_id;
					$grades[$class->level_id] =  $level->getName();
				}
			}
		} elseif(!in_array($_SESSION['group'], array('prof', 'student', 'parent'))){
			$levels = do_query_array("SELECT id, name_".$_SESSION['dirc']." FROM levels ORDER BY id ASC", DB_student);		
			foreach($levels as $r){
				$grades[$r->id] = $r->{'name_'.$_SESSION['dirc']};
			}
		}
	}
	
	return sortArrayByArray($grades, getItemOrder('levels'));
}
	// Class list
function get_class_list(){
	if($_SESSION['group'] ==  'prof'){
		$prof =  new Profs($_SESSION['user_id']);
		return $prof->getClassList();
	} elseif( $_SESSION['group'] == 'principal'){
		$principal = new Principals($_SESSION['user_id']);
		return $principal->getClassList();
	} elseif( $_SESSION['group'] == 'supervisor'){
		$supervisor = new Supervisors($_SESSION['user_id']);
		return $supervisor->getClassList();
	} elseif( $_SESSION['group'] == 'student'){
		$std = new Students($_SESSION['user_id']);
		return $std->getClass();
	}  else {
		return classes::getList();
	}
}
	// Group list
function get_group_list(){
	$groups =false;
	$parent = array();
	if(in_array($_SESSION['group'], array('prof'))){
		$classes = get_class_list();
		foreach($classes as $class_id => $n){
			$parent[] = "(parent='class' AND parent_id=$class_id)";	
		}
		$levels = get_grade_list();
		foreach($levels as $level_id => $n){
			$parent[] = "(parent='level' AND parent_id=$level_id)";	
		}
		
		if(count($parent) > 0){
			$groups = array();
			$sql = "SELECT * FROM groups WHERE ".implode(' OR ', $parent)." GROUP BY name";
			$query = do_query_resource( $sql, Db_prefix.$_SESSION['year']);	
			while($row = mysql_fetch_assoc($query)){
				$groups[$row['id']] = $row['name'];
			}
		}
	} else if(in_array($_SESSION['group'], array( 'principal', 'supervisor'))){
		$classes = get_class_list();
		foreach($classes as $class_id => $n){
			$parent[] = "(parent='class' AND parent_id=$class_id)";	
		}
		$levels = get_grade_list();
		foreach($levels as $level_id => $n){
			$parent[] = "(parent='level' AND parent_id=$level_id)";	
		}
		
		if(count($parent) > 0){
			$groups = array();
			$sql = "SELECT * FROM groups WHERE ".implode(' OR ', $parent);
			$query = do_query_resource( $sql, Db_prefix.$_SESSION['year']);	
			while($row = mysql_fetch_assoc($query)){
				$groups[$row['id']] = $row['name'];
			}
		}
	} else {
		$query = do_query_resource( "SELECT id, name FROM groups", Db_prefix.$_SESSION['year']);	
		if(mysql_num_rows($query) > 0){
			$groups = array();
			while($row = mysql_fetch_assoc($query)){
				$groups[$row['id']] = $row['name'];
			}
		}
	}
	
	return $groups;
}
	// Class = > prof
function getProfsByClass($class_id){
	$prof = array();
	$sql = "SELECT DISTINCT schedules_lessons.prof FROM schedules_date, schedules_lessons
	WHERE schedules_date.con='class'
	AND schedules_date.con_id=$class_id
	AND schedules_date.id=schedules_lessons.rec_id";
	$query = do_query_resource($sql, Db_prefix.$_SESSION['year']);
	while($row = mysql_fetch_assoc($query)){
		$prof[]=$row['prof'];	
	}
	return $prof;
}
	// pricipal => class
function getPrincipalClasses($user_id){
	$out =array();
	$sup_l = do_query_resource( "SELECT * FROM principals WHERE id=$user_id", DB_student);
	$levels = explode(',', $sup_l['levels']);
	while($princ = mysql_fetch_assoc($sup_l)){
		$level_id = $princ['levels'];
		$classes = getClassesByLevel($level_id);
		if($classes !== false) {
			foreach($classes as $class_id){
				$out[$class_id] =getClassNameById( $class_id);
			}
		}
	}
	if(count($out) > 0){
		return $out;	
	} else {
		return false;
	}
}
	// super => class
function getSuperClass($user_id){
	include_once('scripts/services_functions.php');
	$out = array();
	$master_l_q = do_query_resource("SELECT * FROM supervisors WHERE id=".$_SESSION['user_id'], MySql_Database); 
	if( mysql_num_rows($master_l_q)>0){
		while($ser = mysql_fetch_assoc($master_l_q)){
			$service_id = $ser['services'];
			$sql = "SELECT class_id FROM materials_classes WHERE services=$service_id";
			/*$service = getService($service_id);
			$sql = "SELECT  schedules_date.con_id
			FROM  schedules_date, schedules_lessons
			WHERE schedules_date.id = schedules_lessons.rec_id
			AND schedules_lessons.services = $service_id
			AND con ='class'";*/
			$lessons = do_query_resource($sql , Db_prefix.$_SESSION['year']);
			while($rec  = mysql_fetch_assoc($lessons)){
					$out[$rec['class_id']] =getClassNameById($rec['class_id']);
			}
		}
	}
	
	if(count($out) > 0){
		return $out;	
	} else {
		return false;
	}
}
	// class => principals
function getClassPrincipals($class_id){
	$p = array();
	$level_id = getlevelByClass($class_id);
	$prs = do_query_resource("SELECT id FROM principals WHERE levels=$level_id", DB_student);
	while($pr = mysql_fetch_assoc($prs)){
		$p[] = $pr['id'];
	}
	return $p;
}

	// Prof => supervisor
function getSupervisorByprof($prof_id){
	$supers = array();
	if($prof_id != '' || $prof_id != false){
		$services = do_query_resource("SELECT DISTINCT services FROM schedules_lessons WHERE prof=$prof_id;", DB_Year);
		if($services != false && mysql_num_rows($services) > 0){
			while($service = mysql_fetch_assoc($services)){
				$super = do_query("SELECT id FROM supervisors WHERE services=".$service['service'], DB_student);
				if($super != false && $super['id'] != ''){
					$supers[] = $super['id'];
				}
			}
		}
	}
	return $supers;
}
	// get all superadmins
function getSuperadmins(){
	$superadmins = array();
	$query = do_query_resource("SELECT user_id FROM users WHERE `group` = 'superadmin' AND user_id!=0", DB_student);
	while($row= mysql_fetch_assoc($query)){
		$superadmins[] = $row['user_id'];
	} 
	return $superadmins;
}
	// get all admins
function getAlladmins(){
	$admins = array();
	$query = do_query_resource("SELECT user_id FROM users WHERE `group` = 'admin' AND user_id!=0", DB_student);
	while($row= mysql_fetch_assoc($query)){
		$admins[] = $row['user_id'];
	} 
	return $admins;
}
	// ANy to children
function getChildsArr($con, $con_id){
	$children = array();
	switch($con){
		case "level":
			$con = "level";
			$level_id = $con_id;
			$groups = getGroupsIdsByLevel($level_id);
			if($groups !== false){
				foreach($groups as $g){
					$children[] = array('group', $g);
				}
			}
			$classes = getClassesByLevel($level_id);
			if($classes !== false){
				foreach($classes as $c){
					$children[] = array('class', $c);
				}
			}
		break;
		case "class":
			$con = "class";
			$class_id = $con_id;
			$children = array();
			$groups = getGroupsIdsByClass($class_id);
			if($groups !== false){
				foreach($groups as $g){
					$children[] = array('group', $g);
				}
			}
		break;
	}
	return isset($children) ? $children : false;	
}
	
/***************************** tools ******************************************************************/
function getClassDefProf($class_id){
	if($class_id != ''){	
		$r = do_query("SELECT resp FROM classes WHERE id=$class_id", Db_prefix.$_SESSION['year']);	
		$prof =  $r['resp'];
		
		if($prof != false){
			$l = do_query( "SELECT id FROM halls WHERE id=$prof", DB_student);
			if(getEmployerNameById($prof) != false){
				return $prof;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else { return false;}
}

function getClassDefRoom($class_id){
	if($class_id != ''){	
		$r = do_query("SELECT room_no FROM classes WHERE id=$class_id", Db_prefix.$_SESSION['year']);	
		$room =  $r['room_no'];
		
		if($room != false){
			$l = do_query( "SELECT id FROM halls WHERE id=$room", DB_student);
			if($l['id'] != ''){
				return $room;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else { return false;}
}

function getClassRespProf($class_id){
	if($class_id != ''){	
		$r = do_query("SELECT resp FROM classes WHERE id=$class_id", Db_prefix.$_SESSION['year']);	
		
		if($r != false){
			return $r['resp'];
		} else {
			return false;
		}
	} else { return false;}
}

function getHallSize($id){
	if($id != ''){
		$r = do_query("SELECT max_size FROM halls WHERE id=$id", MySql_Database);		
		return $r['max_size'];
	} else { return false;}
}

	// Student toltal Absents 
function getStdTotalAbs($type , $std_id){
	$today = mktime(0,0,0, date('m'), date('d'), date('Y'));
	$begin_year = getYearSetting('begin_date');
	if($type =='ill') {
		$sql = "SELECT id FROM absents WHERE con_id=$std_id AND day<=$today AND day>=$begin_year AND ill=1";		
	} elseif($type =='justify') {
		$sql = "SELECT id FROM absents WHERE con_id=$std_id AND day<=$today AND day>=$begin_year AND justify=1";		
	} else  {
		$sql = "SELECT id FROM absents WHERE con_id=$std_id AND day<=$today AND day>=$begin_year AND ill=0";
	}

	$query = do_query_resource($sql, Db_prefix.$_SESSION['year'] );
	return mysql_num_rows($query);
}
	// check if student is absent
function checkStudentAbsent($stdid, $date, $lesson_no){
	$absent = false;
	if($date !== false ||$date != '' ){
		$l_date = $date;
	} else{
		$l_date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	}
	
	// lesson
	if($lesson_no !== false ||$lesson_no != '' ){
		$l_no = $lesson_no;
	} elseif(isset($_SESSION['this_lesson_no']) && $_SESSION['this_lesson_no'] !== false){
		$l_no = $_SESSION['this_lesson_no'];
	}
	
	if(isset($l_no)){
		$absent_bylesson = do_query_resource("SELECT id FROM absents_bylesson WHERE std_id=$stdid AND date=$l_date AND lesson_no=$l_no", Db_prefix.$_SESSION['year']);
		if(mysql_num_rows($absent_bylesson) > 0){
			$absent = true;
		}
	}
	
	//day
	$absent_byday = do_query_resource("SELECT id FROM absents WHERE con_id=$stdid AND day=$l_date ", Db_prefix.$_SESSION['year']);
	if(mysql_num_rows($absent_byday) > 0){
		$absent = true;
	}
	
	return $absent;
}

	// get previous database
function oldDatabaseYear(){
	$old_year = $_SESSION['year'] -1;
	$db_name = Db_prefix.$old_year;
	if(mysql_select_db($db_name, mysql_con())){
		return $db_name;
	} else {
		return false;
	}
}


	// Change Classes_std
function changeStdClass($std_id, $class_id){
	if($std_id != '') {
		$class_sql = "DELETE FROM classes_std WHERE std_id=$std_id";
		do_query_edit( $class_sql, Db_prefix.$_SESSION['year']);
		$class_sql = "INSERT INTO classes_std (class_id, std_id) VALUES ($class_id, $std_id)";
		return do_query_edit( $class_sql, Db_prefix.$_SESSION['year']);
	} else {
		return false;
	}	
}

	// Terms list
function getTermsList($con, $con_id){
	if(MS_codeName != 'sms_basic'){
		global $lang;
		$terms = Terms::getTermsByCon($con, $con_id);
		$terms_arr = array();
		$terms_arr = array('t=0'=>$lang['months_0']);
		if($terms != false && count($terms) > 0){
			foreach($terms as $t){
				$terms_arr['t='.$t->id] = $t->name;
			}
			return $terms_arr;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
	// materials group

?>
