<?php
	// the the MSGMS user id 
function getUserId($server_user_id, $server_group, $server_name){
	$user = do_query("SELECT user_id FROM users WHERE server_user_id='$server_user_id' AND `user_group`='$server_group' AND server_name='$server_name'", MSG_Database);
	if($user['user_id'] != ''){
		return $user['user_id'];
	} else {
		if(do_query_edit("INSERT INTO users (server_user_id, user_group, server_name) VALUES ( '$server_user_id', '$server_group','$server_name')", MSG_Database)){
			return mysql_insert_id();
		} else {
			return false;
		}
	}
}

	// Current user MSGms id
function  getMsgUserId(){
	global $MS_settings;
	$user_id = $_SESSION['user_id'];
	// merge superadmin and principal to admin msg group
	$group = in_array($_SESSION['group'], array( 'superadmin', 'principal')) ? 'admin' :$_SESSION['group'];
	$user = do_query("SELECT user_id FROM users WHERE server_user_id='$user_id' AND `user_group`='$group' AND server_name='".$MS_settings['server_name']."'", MSG_Database);
	if($user['user_id'] != ''){
		return $user['user_id'];
	} else {
		if(do_query_edit("INSERT INTO users (server_user_id, user_group, server_name) VALUES ( $user_id, '$group','".$MS_settings['server_name']."')", MSG_Database)){
			return mysql_insert_id();
		} else {
			return false;
		}
	}
}


function getMsgUserNameFromId($user_id){
	if($user_id == 0){
		return 'System';
	} else {
		$user = do_query("SELECT * FROM users WHERE user_id=$user_id", MSG_Database);
		return getAnyNameById($user['user_group'], $user['server_user_id']);
	}
}

function countUnreaded(){
	global $MS_settings;
	$msg_user_id = getMsgUserId();
	if($msgs = do_query_resource("SELECT id FROM messages WHERE type='messages' AND reciver=$msg_user_id AND seen=0 AND trash=0", MSG_Database, $MS_settings['msg_server_ip'])){
		return  mysql_num_rows($msgs);
	} else {
		return false;
	}
}

function getMessageContent($msg_id){
	global $lang;
	if($msg_id != ''){
		$sql = "SELECT * FROM messages WHERE id=$msg_id";
		$messages = do_query($sql, MSG_Database);
		if($messages['id'] != ''){
			do_query_edit("UPDATE messages SET seen=1 WHERE id=".$messages['id'], MSG_Database);		
			return write_html('div', 'class="ui-corner-top ui-state-highlight"; style="padding:5px; font-weight:bold;"',
				'<input type="hidden" id="cur_msg_id" value="'.$messages['id'].'" />'.
				write_html('table', 'width="100%"',
					write_html('tr', '',
						write_html('td', '', write_html('h3', '', $lang['from'].': '.getMsgUserNameFromId($messages['sender']))).
						write_html('td', 'width="250"', write_html('h4', 'style="margin:2px"', $lang['date'].': '. date('D d/m/Y h:i a', $messages['date'])))
					).
					write_html('tr', '',
						write_html('td', 'colspan="2"', write_html('h4', 'style="margin:2px"', $lang['to'].': '.getMsgUserNameFromId($messages['reciver'])))
					).
					write_html('tr', '',
						write_html('td', 'colspan="2"', write_html('h4', 'style="margin:2px"', $lang['title'].': '. $messages['title']))
					)
				)
			).
			write_html('div', 'class="ui-corner-bottom ui-widget-content" style="padding:5px; min-height:200px;"', $messages['content']);
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function markUnseen($msg_id){
	if(do_query_edit("UPDATE messages SET seen=0 WHERE id=".$msg_id, MSG_Database)){
		return true;
	} else {
		return false;
	}
}

function deleteMsg($msg_id){
	if(do_query_edit("UPDATE messages SET trash=1 WHERE id=".$msg_id, MSG_Database)){
		return true;
	} else {
		return false;
	}
}

function restoreMsg($msg_id){
	if(do_query_edit("UPDATE messages SET trash=0 WHERE id=".$msg_id, MSG_Database)){
		return true;
	} else {
		return false;
	}
}

function getIdsArray($str){
	if(strpos($str, ',') !== false){
		$arr = explode(',', $str);
	} else {
		$arr = array($str);
	}
	return $arr;
}

function sendSystemMsg( $con, $con_id, $title, $content){
	global $MS_settings;
	$group = $con;
	if(!is_array($con_id)){
		$ids = array($con_id);
	} else {
		$ids = $con_id;
	}
	
	foreach($ids as $id){
		$user = do_query("SELECT user_id FROM users WHERE server_user_id=$id AND user_group='$group'", MSG_Database);
		if($user['user_id'] != ''){
			$user_id = $user['user_id'];
		} else {
			do_query_edit("INSERT INTO users (server_user_id, user_group, server_name) VALUES ($id, '$group', '".$MS_settings['server_name']."')", MSG_Database);
			$user_id = mysql_insert_id();
		}
		$date = time();
		do_query_edit("INSERT INTO messages (type,sender, reciver, title, content, date) VALUES ('system', 0, $user_id, '$title', '$content', $date)", MSG_Database);
	}
}

/****************** Customised Messages ******************/
function getStudentMsgResp($std_id){
	$send_to = array();
	$send_to['student'] = $std_id;
	$parent = getParentIdFromStdId($std_id);
	$send_to['parent'] = $parent;
	$class_id = getClassIdFromStdId($std_id);
	$send_to['admin'] = array();
	if($class_id != false){
		$principals = getClassPrincipals($class_id);
		if($principals != false && count($principals) > 0){
			foreach($principals as $p){
				$send_to['admin'][] = $p;
			}
		}
		$profs = getProfsByClass($class_id);
		if($profs != false && count($profs) > 0){
			$send_to['prof'] = array();
			foreach($profs as $p){
				$send_to['prof'][] = $p;
			}
		}
	}
	$admins = getAlladmins();
	if($admins != false && count($admins) > 0){
		foreach($admins as $p){
			if(!in_array($p, $send_to['admin'])){
				$send_to['admin'][] = $p;
			}
		}
	}
	$superadmins = getSuperadmins();
	if($superadmins != false){
		foreach($superadmins as $p){
			if(!in_array($p, $send_to['admin'])){
				$send_to['admin'][] = $p;
			}
		}
	}
	return $send_to;
}
	// Absents
function sendAbsentMsg($std_id, $date){
	$con = 'parent';
	$con_id = getParentIdFromStdId($std_id);
	$user = do_query("SELECT def_lang FROM users WHERE `group`='parent' AND user_id=$con_id", MySql_Database);
	$def_lang = ($user['def_lang'] != '') ? $user['def_lang'] : 'en';
	include('../lang/'.$def_lang.'.php');
	$title = $lang['msg-absent_alert'].' - '.  getStudentNameById($std_id);
	$content = str_replace('%student_name%', getStudentNameById($std_id), $lang['msg-absent_content']);
	$content = str_replace('%date%', date( 'D d/m/Y', $date),$content);
	sendSystemMsg( $con, $con_id, $title, $content);
}
	// remove absent message
function sendAbsentDelMsg($std_id){
	$con = 'parent';
	$con_id = getParentIdFromStdId($std_id);
	$user = do_query("SELECT def_lang FROM users WHERE `group`='parent' AND user_id=$con_id", MySql_Database);
	$def_lang = ($user['def_lang'] != '') ? $user['def_lang'] : 'en';
	include('../lang/'.$def_lang.'.php');
	$title = $lang['msg-rectification'].' - '.  getStudentNameById($std_id);
	$content = str_replace('%student_name%', getStudentNameById($std_id), $lang['msg-absent_delete']);
	sendSystemMsg( $con, $con_id, $title, $content);
}
	// Homework
function sendHomeworkMessage($con, $con_id, $homework_id, $date, $answer_date, $material_name){
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
	sendSystemMsg( $con, $con_id, $lang['new_homework'], addslashes($lang['new_homework_content']).addslashes($msg_content));
}

function sendHomeworkDelMessage( $homework_id){
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
		sendSystemMsg( $con, $con_id, $lang['homework_canceled'], addslashes($lang['homework_canceled']).addslashes($msg_content));
	}
}

	// Student inscription
function sendNewStudentMessage($std_id){ 
	$student = new Students($std_id);
	$std_name = $student->getName();
	require_once('modules/resources/classes.class.php');
	$class = $student->getClass();
	global $lang;	
	$send_to = array();
	$principals = $class->getPrincipals();
	$parent = $student->parent_id;;
	$profs = $class->getProfs();
	$superadmins = getSuperadmins();
	$send_to =  getStudentMsgResp($std_id);
	$std = do_query("SELECT status, join_date FROM student_data WHERE id=$std_id", DB_student);
	$out = write_html('label', '', $lang['class'].": ".$class->getName()).'<br />'.
	write_html('label', '', $lang['status'].": ".($std['status'] == 1 ? $lang['inscript'] : $lang['waiting_list'])).'<br />'.
	write_html('label', '', $lang['join_date'].": ".($std['status'] == 1 ? unixToDate($std['join_date']) :''));
	foreach($send_to as $reciver_group => $reciver_id){
		if(is_array($reciver_id)){
			foreach($reciver_id as $id){
				sendSystemMsg( $reciver_group,  $id, $lang['new_student'].': '.$std_name, $out);
			}
		} else {
			sendSystemMsg( $reciver_group,  $reciver_id, $lang['new_student'].': '.$std_name, $out);	
		}
	}
}
	// student desinscription
function sendDisinscripMessage($std_id, $stat, $date){
	global $lang;
	$student = new Students($std_id);
	$std_name = $student->getName();
	require_once('modules/resources/classes.class.php');
	$class = $student->getClass();
	$out = write_html('label', '', $lang['class'].": ".$class->getName()).'<br />'.
	write_html('label', '',  $lang['from'].': '.unixToDate($date)).
	write_html('p', '', 
		($stat ==0 ? $lang['desincrip_msg_final'] : $lang['desincrip_msg'])
	);

	// begin sending
	$send_to =  getStudentMsgResp($std_id);
	foreach($send_to as $reciver_group => $reciver_id){
		if(is_array($reciver_id)){
			foreach($reciver_id as $id){
				sendSystemMsg( $reciver_group,  $id, ($stat ==0 ? $lang['radiet']: $lang['suspended']).': '.$std_name, $out);
			}
		} else {
			sendSystemMsg( $reciver_group,  $reciver_id, ($stat ==0 ? $lang['radiet']: $lang['suspended']) .': '.$std_name, $out);	
		}
	}
}
	// holiday
function sendHolidayMessage($con, $con_id, $title, $content){
	sendSystemMsg( $con, $con_id, $title, $content);
}
	//Events
function sendEventMessage($con, $con_id, $title, $content){
	sendSystemMsg( $con, $con_id, $title, $content);
}
?>