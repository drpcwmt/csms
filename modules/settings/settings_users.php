<?php
## setting Users ##


// generate user name
	// check user name

	// new user form 

if(isset($_POST['user_name'])){
	$group = do_query("SELECT name FROM groups WHERE id=".$_POST['group']);
	$group_name = $group['name'];
	$chk = do_query("SELECT * FROM users WHERE user_id=".$_POST['user_id']." AND `group`='$group_name'", MySql_Database);
	if($chk != false && $chk['user_id'] != ''){
		echo "UPDATE users SET name='".$_POST['name']."', password='".$_POST['password']."', def_lang='".$_POST['def_lang']."' WHERE user_id=".$_POST['user_id']." AND `group`='$group_name'";
		if(do_query_edit("UPDATE users SET name='".$_POST['name']."', password='".$_POST['password']."', def_lang='".$_POST['def_lang']."' WHERE user_id=".$_POST['user_id']." AND `group`='$group_name'", MySql_Database)){
			if($chk['group'] == 'student'){
				$old_path = 'attachs/'.$MS_settings['docs_root_stds'] . '/'. $chk['name'];
				$new_path = 'attachs/'.$MS_settings['docs_root_stds'] . '/'. $_POST['name'];
			} elseif($chk['group'] == 'prof'){
				$old_path = 'attachs/'.$MS_settings['docs_root_profs'] . '/'. $chk['name'];
				$new_path = 'attachs/'.$MS_settings['docs_root_profs'] . '/'. $_POST['name'];
			} else {
				$old_path = 'attachs/'.$MS_settings['docs_root_users'] . '/'. $chk['name'];
				$new_path = 'attachs/'.$MS_settings['docs_root_users'] . '/'. $_POST['name'];
			}
			if (stristr (PHP_OS, 'WIN')){
				$new_path = iconv("UTF-8", "CP1256//TRANSLIT", $new_path);
			}
			if(is_dir($old_path)){
				rename($old_path, $new_path);
			}
			echo json_encode(array('error' => ''));
		} else {
			echo json_encode(array('error' => $lang['error_updating']));
		}
	} else {
		if(!do_query_edit("INSERT INTO users (user_id, name, password, `group`, def_lang) VALUES (".$_POST['user_id'].", '".$_POST['name']."', '".$_POST['password']."', '$group_name', '".$_POST['def_lang']."' )", MySql_Database)){
			echo json_encode(array('error' => $lang['error_updating']));
		} else {
			echo json_encode(array('error' => ''));
		}
	}
	exit;
}



?>