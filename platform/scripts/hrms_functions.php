<?php
## HrMS functions ##
## *********************** SINGLE HRMS

function getEmployerNameById($emp_id){
	if($emp_id != ''){
		if($emp_id == 0 ){
			return 'Localadmin';
		} else {
			global $MS_settings;
		
			if(isset($MS_settings['hrms_ver']) && $MS_settings['hrms_ver'] == 2){
				$field = $_SESSION['lang'] == 'ar' ? 'name_rtl' : 'name_ltr';
				$sql = "SELECT $field FROM employer_data WHERE id=$emp_id";
				$row = do_query($sql, HRMS_Database, $MS_settings['hrms_server_ip']) ;
				
				return $row[$field] != '' ? $row[$field] : false;
			} else {
				$ldb = "employer_data_".($_SESSION['lang'] == 'ar' ? 'ar' : 'en');
				$sql = "SELECT first_name, last_name FROM $ldb WHERE id=$emp_id";
				$row = do_query($sql, HRMS_Database, $MS_settings['hrms_server_ip']) ;
		
				return $row['first_name'] != '' ? $row['first_name'].' '.$row['last_name'] : false;
			}
		}
	} else {
		return false;
	}
}


function getEmployerTelById($emp_id){
	if($emp_id != ''){
		global $MS_settings;
		$sql = "SELECT mobil,tel FROM employer_data WHERE id=$emp_id";
		$row = do_query($sql, HRMS_Database, $MS_settings['hrms_server_ip']) ;
		$out = array();
		if($row['mobil'] != ''){
			$out[] = $row['mobil'];
		} 
		if($row['tel'] != ''){
			$out[] = $row['tel'];
		} 
		
		return count($out) > 0 ? $out : false;
	} else {
		return false;
	}
}

?>