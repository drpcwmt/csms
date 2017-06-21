<?php
/** Messages
*
*/

class systemMessages  {
	
	static function getStudentMsgResp($msg, $std){
			// Parents
		$msg->addReciver('parent', $std->parent_id);
		$level = $std->getLevel();
			// Principals
		$principals = $level->getPrincipals();
		foreach($principals as $p){
			$msg->addReciver('principal', $p->id);
		}
			// Profs
		$services = $std->getServices();
		foreach($services  as $service){
			$profs = $service->getProfs();
			foreach($profs as $prof){
				if(!in_array($prof, $profs_list)){
					$msg->addReciver('prof', $prof->id);
				}
			}
			$supervisors = $service->getSupervisors();
			foreach($supervisors as $supervisor){
				if(!in_array($supervisor, $supervisors_list)){
					$msg->addReciver('supervisor', $supervisor->id);
				}
			}
			
		}
		$msg->addReciver('admin', 0);
			
		return $msg;
	}
	
		// New student Inscription	
	static function sendInscriptionMsg($student, $data=''){
		global $lang;
		if($student->status == 4){
			$msg_title = $lang['new_student_application'].': '.$student->getName();
		} elseif($student->status == 2){
			$msg_title = $lang['new_student_waiting'].': '.$student->getName();
		} elseif($student->status == 1){
			$msg_title = $lang['new_student_inscripted'].': '.$student->getName();
		} 
		
		$message = new Messages();		
		$message = systemMessages::getStudentMsgResp($message, $student);
		$message->title = $msg_title;
		$message->content = $data;
		$message->sender = '0';
		$message->type= 'system';
		$message->send(); 
	}

		// Student Description
	static function sendDesinscriptionMsg($student){
		global $lang;
		$class_name = $student->getClass()->getName() ;
		$level_name = $student->getLevel()->getName();
		if($student->status == 0){
			$msg_title = $lang['desincrip_msg_final'].': '.$student->getName();
			$msg_content = write_html('label', '', $lang['desincrip_msg_final']).'<br>'.
				write_html('label', '', $lang['name'].': '.$student->getName()).'<br>'.
				write_html('label', '', $lang['class'].": ".$class_name).'<br>'.
				write_html('label', '', $lang['date'].': '.unixToDate($student->quit_date));
		} elseif($student->status == 3){
			$msg_title = $lang['desincrip_msg'].': '.$student->getName();
			$msg_content = write_html('label', '', $lang['desincrip_msg']).'<br>'.
				write_html('label', '', $lang['name'].': '.$student->getName()).'<br>'.
				write_html('label', '', $lang['class'].": ".$class_name).'<br>'.
				write_html('label', '', $lang['date'].': '.unixToDate($student->quit_date)).'<br>'.
				write_html('label', '', $lang['till'].': '.unixToDate($student->till_date));
		}
		
		$message = new Messages();		
		systemMessages::getStudentMsgResp($message, $student);
		$message->title = $msg_title;
		$message->content = $msg_content;
		$message->sender = '0';
		$message->type= 'system';
		$message->send(); 
	}
	
	// Absents
	static function sendAbsentMsg($std_id, $date){
		$student = new Students($std_id);
		$con = 'parent';
		$parent_id = $student->getParents()->id;
		$user = do_query("SELECT def_lang FROM users WHERE `group`='parent' AND user_id=$parent_id", MySql_Database);
		$def_lang = ($user['def_lang'] != '') ? $user['def_lang'] : 'en';
		include('../lang/'.$def_lang.'.php');
		$title = $lang['msg-absent_alert'].' - '.  $student->getName();
		$content = str_replace('%student_name%', $student->getName(), $lang['msg-absent_content']);
		$content = str_replace('%date%', date( 'D d/m/Y', $date),$content);

		$message = new Messages();		
		systemMessages::getStudentMsgResp($message, $student);
		$message->title = $title;
		$message->content = $content;
		$message->sender = '0';
		$message->type= 'system';
		$message->send(); 
	}
			// remove absent message
	static function sendAbsentDelMsg($std_id){
		$student = new Students($std_id);
		$parent_id = $student->parent_id;
		$user = do_query("SELECT def_lang FROM users WHERE `group`='parent' AND user_id=$con_id", MySql_Database);
		$def_lang = ($user['def_lang'] != '') ? $user['def_lang'] : 'en';
		include('../lang/'.$def_lang.'.php');
		$title = $lang['msg-rectification'].' - '.  getStudentNameById($std_id);
		$content = str_replace('%student_name%', getStudentNameById($std_id), $lang['msg-absent_delete']);

		$message = new Messages();		
		systemMessages::getStudentMsgResp($message, $student);
		$message->title = $title;
		$message->content = $content;
		$message->sender = '0';
		$message->type= 'system';
		$message->send(); 
	}
			// Homework
	static function sendHomeworkMessage($con, $con_id, $homework_id, $date, $answer_date, $material_name){
		global $lang;
		$msg_content = write_html('table', '',
			write_html('tr', '',
				write_html('td', '', $lang['material'].': '. $material_name).
				write_html('td', '', $lang['date'].': '. unixToDate($date))
			).
			write_html('tr', '',
				write_html('td', '', $lang['answer_date'].': '. unixToDate($answer_date)).
				write_html('td', '', '&nbsp;')
			).
			write_html('tr', '',
				write_html('td', '','&nbsp;').
				write_html('td', '', 
					write_html('button', 'type="button" class="ui-corner-all ui-state-default hoverable" onclick="answerHomework(\'write\', '.$homework_id.')"',
						'<span class="ui-icon ui-icon-extlink"></span>'.
						$lang['view']
					)
				)
			)
		);

		$message = new Messages();	
		$message->addReciver($con, $con_id);
		$message->title = $lang['new_homework'];
		$message->content = addslashes($lang['new_homework_content']).addslashes($msg_content);
		$message->sender = '0';
		$message->type= 'system';
		$message->send(); 
	}
	
	static function sendHomeworkDelMessage( $homework_id){
		global $lang;
		$hw = do_query("SELECT * FROM homeworks WHERE id=$homework_id", LMS_Database);
		if($hw['id'] != ''){
			$con = $hw['con'];
			$con_id = $hw['con_id'];
			$date = $hw['date'];
			$lesson_id = $hw['lesson_id'];
			$les = do_query("SELECT services FROM schedules_lessons WHERE id=$lesson_id", DB_year);
			$material_name = getServiceNameById($les['services']);
			$msg_content = write_html('table', '',
				write_html('tr', '',
					write_html('td', '', $lang['material'].': '. $material_name).
					write_html('td', '', $lang['date'].': '. unixToDate($date))
				)
			);
			$message = new Messages();	
			$message->addReciver($con, $con_id);
			$message->title = $lang['homework_canceled'];
			$message->content = addslashes($lang['homework_canceled']).addslashes($msg_content);
			$message->sender = '0';
			$message->type= 'system';
			$message->send(); 
		}
	}
	
		// holiday
	static function sendHolidayMessage($con, $con_id, $title, $content){
		$message = new Messages();	
		$message->addReciver($con, $con_id);
		$message->title = $title;
		$message->content =addslashes($content);
		$message->sender = '0';
		$message->type= 'system';
		$message->send(); 
	}
		//Events
	static function sendEventMessage($con, $con_id, $title, $content){
		$message = new Messages();	
		$message->addReciver($con, $con_id);
		$message->title = $title;
		$message->content =addslashes($content);
		$message->sender = '0';
		$message->type= 'system';
		$message->send(); 
	}
}
		