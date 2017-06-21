<?php
## setting user generate


function insertNewUser($id, $group){
	global $sms;

	$login = trim($sms->getAnyNameById($group, $id));
	$pass = rand(100000, 999999);
	$login = str_replace("'", "", $login);
	$login = str_replace('..','.',  $login);
	$def_lang = $sms->getSettings('default_lang');
	if (do_query_edit("INSERT INTO users (user_id, name, password, `group`, def_lang) VALUES($id, '$login', '$pass', '$group', '$def_lang')", $sms->database, $sms->ip)){
		return array(
			'user_id' => $id,
			'name' => $login,
			'pass' => $pass
		);
	} else {
		return array(
			'name' => $login
		);
	}
}

$group = safeGet($_GET['group']);

$users = 'array';
switch($group){
	case 'prof':
		$users = do_query_array("SELECT * FROM profs", $sms->database, $sms->ip);
	break;	
	case 'supervisor':
		$users = do_query_array("SELECT * FROM supervisors", $sms->database, $sms->ip);
	break;	
	case 'principal':
		$users = do_query_array("SELECT * FROM principals", $sms->database, $sms->ip);
	break;	
	case 'student':
		$users = do_query_array("SELECT * FROM student_data WHERE status=1", $sms->database, $sms->ip);
	break;	
	case 'parent':
		$users = do_query_array("SELECT DISTINCT parent_id FROM student_data WHERE status=1", $sms->database, $sms->ip);
	break;	
}

$user_arr = array();
if($users != false){
	foreach($users as $user){
		if(isset($user->id) && $user->id!= ''){
			if(!in_array($user->id, $user_arr)){
				$user_arr[] = $user->id;
			} 
		}
	}
}

$c_new = 0;
$c_ok = 0;
$c_error = 0;
$c_found = 0;
$tbody= '';
foreach($user_arr as $user){
	$row = do_query_obj( "SELECT * FROM users WHERE user_id=$user AND `group`='$group'", $sms->database, $sms->ip);
	if($row != false){
		$c_found++;
		$tbody .= write_html('tr', '',
			write_html('td', '', $row->name).
			write_html('td', '', 'Not changed').
			write_html('td', '', $group).
			write_html('td', '', '&nbsp;')
		);
	}else {
		$c_new++;
		$new_user = insertNewUser($user, $group);
		if(isset($new_user['pass']) && $new_user['pass'] != ''){
			$c_ok++;
			$tbody .= write_html('tr', '',
				write_html('td', '', $new_user['name']).
				write_html('td', '', $new_user['pass']).
				write_html('td', '', $group).
				write_html('td', '', 'Ok')
			);
		} else {
			$c_error++;
			$tbody .= write_html('tr', '',
				write_html('td', '', $new_user['name']).
				write_html('td', '', '&nbsp;').
				write_html('td', '', $group).
				write_html('td', '', 'Error')
			);
		}
	}
}

echo write_html('table', 'class="result"',
	write_html('tr', '',
		write_html('td', '', $lang['new_found'].': '. $c_found).
		write_html('td', '', $lang['new_users'].': '. $c_new).
		(($c_ok > 0) ? write_html('td', '', $lang['new_ok'].': '. $c_ok. '<span class="ui-icon ui-icon-check" style="float:right"></span>') : '').
		(($c_error > 0) ? write_html('td', '', $lang['new_error'].': '. $c_error. '<span class="ui-icon ui-icon-notice" style="float:right"></span>') : '').
		write_html('td', '', $lang['total_user'].': ' .($c_ok + $c_found))
	)
);

echo write_html('button', 'class="ui-state-default ui-corner-all hoverable" style="margin:10px 20px" onclick="toggleLog(this)"', $lang['show_log']);

echo write_html('table', 'class="tablesorter hidden"',
	write_html('thead', '',
		write_html('th', '', $lang['login']).
		write_html('th', '', $lang['password']).
		write_html('th', '', $lang['group']).
		write_html('th', '', $lang['status'])
	).
	$tbody
);
?>