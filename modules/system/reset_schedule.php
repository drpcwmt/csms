<?php
## Reset Schedules to default
$answer = array();
if(isset($_POST['class_id'])){
	$error =false;
	$days= array();
	$class_id =$_POST['class_id'];
	$lessons = do_query_resource("SELECT schedules_lessons.*, schedules_date.date FROM schedules_date, schedules_lessons
	WHERE schedules_date.id=schedules_lessons.rec_id
	AND schedules_date.con='class'
	AND schedules_date.con_id=$class_id
	AND schedules_date.date<7
	ORDER BY schedules_date.date ASC", DB_year);
	if(mysql_num_rows($lessons) ==7){
		$error = 'Default week is already setted, nothing to do!';
	} elseif(mysql_num_rows($lessons) >0 ){
		while($lesso = mysql_fetch_assoc($lessons)){
			if(!in_array($lesso['date'], $days)){ 
				$days[]= $lesso['date'];
			}
		}
	} 
	
	if(count($days) < 7){
		$lessons = do_query_resource("SELECT schedules_lessons.*, schedules_date.date FROM schedules_date, schedules_lessons
		WHERE schedules_date.id=schedules_lessons.rec_id
		AND schedules_date.con='class'
		AND schedules_date.con_id=$class_id
		AND schedules_date.date>7
		ORDER BY schedules_date.date ASC", DB_year);
		while($les= mysql_fetch_assoc($lessons)){
			$def_date = date('w', $les['date']);	
			echo $def_date;
			if(!in_array($def_date, $days)){
				$chk = do_query("SELECT id FROM schedules_date WHERE con='class' AND con_id=$class_id AND date=$def_date", DB_year);
				if($chk['id'] != ''){
					$rec_id = $chk['id'];
				} else {
					if(do_query_edit("INSERT INTO schedules_date (con, con_id, date) VALUES ('class', $class_id, $def_date)", DB_year)){
						$rec_id = mysql_insert_id();
					}
				}
				$cur_date = $les['date'];
				$lss = do_query_resource("SELECT schedules_lessons.* FROM schedules_date, schedules_lessons
					WHERE schedules_date.id=schedules_lessons.rec_id
					AND schedules_date.con='class'
					AND schedules_date.con_id=$class_id
					AND schedules_date.date=$cur_date", DB_year);
				if(mysql_num_rows($lss) > 0){
					while($ls = mysql_fetch_assoc($lss)){
						do_query_edit("INSERT INTO schedules_lessons (rec_id, lesson_no, services, prof, hall, tools, exam, rule) VALUES ($rec_id, ". $ls['lesson_no'].", ".$ls['services'].", ".$ls['prof'].", '".$ls['hall']."', '".$ls['tools']."', '".$ls['exam']."', '".$ls['rule']."')", DB_year);
					}
					$days[] = $def_date;
				}
				
			}
		}
		if(isset($_POST['del_specials']) && $_POST['del_specials'] == 1){
			if(do_query_edit("DELETE schedules_lessons.* FROM schedules_date, schedules_lessons
			WHERE schedules_date.id=schedules_lessons.rec_id
			AND schedules_date.con='class'
			AND schedules_date.con_id=$class_id
			AND schedules_date.date>7 ", DB_year)){
				
			} else {
				$error= "Cant delete special dates! ";	
			} 
		}
		
		if(count($days) < 7){
			$error = 'Some date counld not be updated! please verify before continue';
		}
		if(count($days) == 0){
			$error = 'Default week could not be reset! may be the class have no special days setted';
		}
		if($error != false){
			$answer['error'] = $error;
		} else {
			$answer['error'] = '';
		}
	}
} else {
	$answer['error'] ='No Class defined';
}

setJsonHeader();
print json_encode($answer);
exit;
