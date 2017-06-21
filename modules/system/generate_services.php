<?php
## Generate Services
$level_id = $_POST['level_id'];
$level = new Levels($level_id);
$count = 0;
$oblig_service = array();

$level_services = $level->getServices();
//do_query_resource("SELECT id FROM services WHERE optional = 0 AND level_id=".$level_id, DB_year);
foreach($level_services as $service){
	$oblig_service[] = $service->id;
}
$classes = $level->getClassList();
foreach($classes as $class){
	if(isset($_POST['delete_old_values']) && $_POST['delete_old_values'] == 1){
		do_query_edit("DELETE FROM materials_classes WHERE class_id=$class->id", DB_year);
	}
	$class_services = $class->getServices();
	if($class_services == false) $class_services = array();
	foreach($oblig_service as $service_id){
		if(!array_key_exists($service_id, objectsToArray($class_services))){
			do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES($class->id, $service_id)", DB_year);
			$count++;
		}
	}
	// class groups
	/*$class_groups = do_query_resource("SELECT id, service_id FROM groups WHERE parent='class' AND parent_id=$class_id AND service_id!=''", DB_year);
	if(mysql_num_rows($class_groups) > 0){
		while($group = mysql_fetch_assoc($class_groups)){
			if(!in_array( $group['service_id'], array_merge($class_services, $oblig_service))){
				do_query_edit("INSERT INTO materials_classes (class_id, services) VALUES($class_id, $service)", DB_year);
				$count++;
			}
		}
	}*/
	$stds = $class->getStudents();
	if($stds != false && count($stds) > 0){
		foreach($stds as $student ){
			if(isset($_POST['delete_old_values']) && $_POST['delete_old_values'] == 1){
				do_query_edit("DELETE FROM materials_std WHERE std_id=$student->id", DB_year);
			}
			$std_services =$student->getServices();
			if($std_services == false) $std_services = array();
			foreach($level_services as $service){
				if($service->optional == '0' && !array_key_exists($service->id, objectstoArray($std_services))){
					do_query_edit("INSERT INTO materials_std (std_id, services) VALUES($student->id, $service->id)", DB_year);
					$count++;
				}
			}
			// OPtionals 
			if($student->religion == '1'){
				$serv_id = Services::getServiceId($level_id, $this_system->getSettings('islamic_material'));
			} else {
				$serv_id = Services::getServiceId($level_id, $this_system->getSettings('christian_material'));				
			}
			if(isset($serv_id->id)){
				do_query_edit("INSERT INTO materials_std (std_id, services) VALUES($student->id, $serv_id->id)", DB_year);
			}
			/*$std_groups = do_query_resource("SELECT id, service_id FROM groups, groups_std WHERE groups.id=groups_std.group_id AND groups_std.std_id=$student->id", DB_year);
			if(mysql_num_rows($std_groups) > 0){
				while($group = mysql_fetch_assoc($std_groups)){
					if($group['service_id'] != ''  && !in_array($group['service_id'], array_merge($std_services, $oblig_service))){
						do_query_edit("INSERT INTO materials_std (std_id, services) VALUES($student->id, ".$group['service_id'].")", DB_year);
						$count++;
					}
				}
					
			}*/
		}
	}
}

$answer['error'] = '';
$answer['count']= $count.' Statment executed';

setJsonHeader();
print json_encode($answer);
exit;


?>