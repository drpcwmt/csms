<?php
## Group import students
$error = false;
$all_stds = getStdIds($_GET['parent'], $_GET['parent_id']);
$std_name_field = $_SESSION['lang'] == 'ar'? 'name_ar' : 'name';
$stds = array();
if(count($all_stds) > 0){
	if(isset($_POST['service_id'])){
		$service_id = $_POST['service_id'];
		$service = new services($service_id);
		$material = $service->mat_id;
		foreach($all_stds as $std_id){
			$chk = do_query("SELECT $std_name_field, lang_1, lang_2, lang_3 FROM student_data WHERE id=$std_id", DB_student);
			if($material == $chk['lang_1'] || $material == $chk['lang_2'] || $material == $chk['lang_3'] ){
				$stds[$std_id] = $chk[$std_name_field];
			} else {
				if(do_query_obj("SELECT std_id FROM materials_std WHERE std_id=$std_id AND services = $service_id", $this_system->db_year)!= false){
					$student = new Students($std_id);
					$stds[$std_id] = $student->getName();
				}
			}
		}
	} elseif(isset($_POST['sex'])){
		foreach($all_stds as $std_id){
			$chk = do_query("SELECT $std_name_field, sex FROM student_data WHERE id=$std_id", DB_student);
			if($_POST['sex'] == $chk['sex'] ){
				$stds[$std_id] = $chk[$std_name_field];
			}
		}
	} elseif(isset($_POST['religion'])){
		foreach($all_stds as $std_id){
			$chk = do_query("SELECT $std_name_field, religion FROM student_data WHERE id=$std_id", DB_student);
			if($_POST['religion'] == $chk['religion'] ){
				$stds[$std_id] = $chk[$std_name_field];
			}
		}
	}
} else {
	$error = $lang['error-no_student_defined'];
}

if($error){
	$answer['error'] = $error;
} else {
	$answer['error'] = '';
	$answer['stds'] = $stds;
}

setJsonHeader();
print json_encode($answer);
exit;
?>