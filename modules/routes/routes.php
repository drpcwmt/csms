<?php
## routes
if(isset($_GET['busms_id'])&& $_GET['busms_id'] !=0){
	$busms = new BusMS(safeGet($_GET['busms_id']));
}

if(isset($_GET['sms_id'])){
	$sms = new SMS(safeGet($_GET['sms_id']));
}

if(isset($_GET['groups'])){
	if(isset($_GET['group_id'])){
		$group = new GroupRoutes(safeGet('group_id'));
		echo $group->loadLayout();
	} else {
		echo GroupRoutes::loadMainLayout();
	}
	
} elseif(isset($_GET['saveroute'])){
	echo json_encode_result(Routes::_save($_POST));
	
} elseif(isset($_GET['newroute'])){
	echo Routes::newRouteForm(isset($_GET['group_id'])?safeGet('group_id') : '');
	
} elseif(isset($_GET['deleteroute'])){
	echo json_encode_result(Routes::_delete($_POST['route_id']));
	
} elseif(isset($_GET['addmember'])){
	if(isset($_GET['save'])){
		echo json_encode_result(Routes::saveMember($_POST));
	} else {
		echo Routes::newMember(safeGet('con'), isset($_GET['route_id']) ? safeGet('route_id') : '');	
	}
} elseif(isset($_GET['delmember'])){
	echo json_encode_result(Routes::delMember($_POST));	


} elseif(isset($_GET['movemember'])){
	echo json_encode_result(Routes::moveMember($_POST));	

} elseif(isset($_GET['route_id'])){
	$route = new Routes(safeGet($_GET['route_id']));
	echo $route->loadLayout();

} elseif(isset($_GET['route_no'])){
	$route = new Routes(safeGet($_GET['route_no']));
	echo $route->loadLayout();
	
} elseif(isset($_GET['route_autocomplete'])){
	$value = trim($_GET['term']);
	print Routes::getAutocompleteRoute( $value);
	
} elseif(isset($_GET['members_by_school'])){
	echo Routes::getMemebersBySchool(safeGet('sms_id'));
	
} elseif(isset($_GET['wizard'])){
	if(isset($_GET['step'])){
		$step = safeGet($_GET['step']);
		
	} else {
		$step = 0;
		
	}
	
} elseif(isset($_GET['updateMemberTime'])){
	$r = $_POST['r'];
	$route_id = $_POST['route_id'];
	$con = $_POST['con'];
	$con_id = $_POST['con_id'];
	$cc_id = $_POST['cc_id'];
	$address_id = $_POST['address_id'];
	$field_time = $r.'_time';
	$field_address = $r.'_address';
	$time = $_POST['time'];
	$result = true;
	if( do_update_obj(array($field_time=>$time, $field_address=>$address_id), "con='$con' AND con_id=$con_id AND route_id=$route_id", 'route_members', $busms->database, $busms->ip) == false){
		$result = false;
	}
	echo json_encode_result($result);
	
	
} elseif(isset($_GET['map_locations'])){
	$r = $_POST['r'];
	$field = $r."_time";
	$route_id = $_POST['route_id'];
	$out = array('error'=>'');
	$rows = do_query_array("SELECT * FROM route_members WHERE route_id=$route_id AND $field IS NOT NULL ORDER BY $field ASC", $busms->database, $busms->ip);
	if($rows != false){
		foreach($rows as $row){
			if($row->con == 'std'){
				$sys = new SMS($row->cc_id);
				$memb = new Students($row->con_id, $sys);
				$memb_name = $memb->getName().' - '. $memb->getLevel()->getName();
				$school_code = $sys->code;
			} else {
				$sys = new HrMS($row->cc_id);
				$memb = new Employers($row->con_id, $sys);
				$memb_name = $memb->getName();
				$school_code = $sys->code;
			}
			$address = do_query_obj("SELECT * FROM addressbook WHERE id=".$row->{$r."_address"}, $sys->database, $sys->ip);
			$out['locations'][] = array(
				'con'=>$row->con,
				'con_id'=>$row->con_id,
				'cc_id'=>$row->cc_id,
				'info'=> $memb_name,
				'time'=>unixToTime($row->{$r.'_time'}),
				'address_id'=>($address!= false ? $address->id: ''),
				'address'=> ($address!= false ? $address->address_ar." ".$address->region_ar." ".$address->city_ar." ".$address->country_ar: ''),
				'lng'=>($address!= false ? $address->lng: ''),
				'lat'=>($address!= false ? $address->lat: '')
			);
		}
	} else {
		$out['error'] = 'No time for students has been setted';
	}
	echo json_encode_result($out);
			
} else {
	echo GroupRoutes::loadMainLayout();
	//echo Routes::loadMainLayout();
}

?>