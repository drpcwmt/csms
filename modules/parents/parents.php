<?php
## parents infos ##
if(!getPrvlg('parents_read')){write_error($lang['restrict_accses']); exit;};

if(isset($_GET['parent_autocomplete'])){
	$value = trim($_GET['term']);
	print Parents::getAutocompleteParent( $value);
	exit;
}
/*********** Login Infos ************/
if(isset($_GET['logininfo'])){
	$id = $_GET['logininfo'];
	$user = do_query("SELECT name, password FROM users WHERE `group`='parent' AND user_id=$id", DB_student);
	if($user['name'] != ''){
		$login = $user['name'];
		$pass = $user['password'];
	} else {
		$login = strtolower(str_replace(' ','.',  getParentNameById($id)));
		$pass = rand(100000, 999999);
		$def_lang = $MS_settings['default_lang'];
		do_query_edit("INSERT into users (user_id, name, password, `group`, def_lang, css) VALUES ($id, '$login', '$pass', 'student', '$def_lang', 'default')", DB_student);
	}
	
	echo write_html('table', 'width="100%" cellspacing"0" border="0"',
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" ', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['login_name'])
			).
			write_html('td', 'valign="middel"',
				'<input type="text" value="'.$login.'" class="input_double ui-state-default ui-corner-right" />'
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" ', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['password'])
			).
			write_html('td', 'valign="middel"',
				'<input type="text" name="suspension_reason" value="'.$pass.'" class="ui-state-default ui-corner-right"/>'
			)
		)
	);
	exit;
}

/***************** Merge *******************/
if(isset($_GET['merge'])){
	if(getPrvlg('parents_edit')){
		$fields = getTableFields( 'parents', MySql_Database);
		
		$values = array();
		foreach($fields as $f){
			$values[$f]= '';
		}
		
		for($i=0; $i<count($_POST['id']); $i++){
			$id = $_POST['id'][$i];
			$sql = "SELECT `".implode('`,`', $fields) ."` FROM parents WHERE id=".$id;
			$query = do_query($sql, MySql_Database);
			foreach($fields as $f){
				if($values[$f] == ''){
					$values[$f] = $query[$f];
				}
			}
			
		}
		
		$final_id = $values['id'];
		
		// update english table
		$set = array();
		foreach($values as $key => $value){
			$set[] = "`$key`='$value'";
		}
		do_query_edit("UPDATE parents SET ".implode(', ', $set). " WHERE id=$final_id", MySql_Database);
		
		// delete other ids
		for($i=0; $i<count($_POST['id']); $i++){
			$id = $_POST['id'][$i];
			if($id != $final_id){
				do_query_edit("UPDATE student_data SET parent_id=$final_id WHERE parent_id=$id", MySql_Database);
				do_query_edit("DELETE FROM parents WHERE id=$id"	, MySql_Database);
				do_query_edit("DELETE FROM users WHERE user_id=$id AND `group`='parent'", MySql_Database);
			}
		}
	
		$answer = array();
		if(isset($final_id) && $final_id != ''){
			$answer['id'] = $final_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = "Error";
		}
		echo  json_encode($answer);	
	} else {
		echo json_encode(array('error' => $lang['no_privilege']));
	}	
	exit;
}

/*********Delete parent code ***************/
if(isset($_GET['delete'])){
	if(getPrvlg('parents_edit')){
		for($i=0; $i<count($_POST['id']); $i++){
			$id = $_POST['id'][$i];			
			if(do_query_edit("DELETE FROM parents WHERE id=$id", MySql_Database)){
				do_query_edit("DELETE FROM users WHERE user_id=$id AND `group`='parent'", MySql_Database);
			}
		} 
		echo json_encode(array('error' => ''));	
	} else {
		echo json_encode(array('error' => $lang['no_privilege']));
	}	
	exit;
}

/*********** POSTS parent ****************************/
if(isset($_POST['id'])){
	$answer = array();
	$value = $_POST;
	$value['father_emp'] = isset($_POST['father_emp']) && $_POST['father_emp'] == 1 ? 1 : 0;
	$value['mother_emp'] = isset($_POST['mother_emp']) && $_POST['mother_emp'] == 1 ? 1 : 0;
	$value['father_resp'] = isset($_POST['father_resp']) && $_POST['father_resp'] == 1 ? 1 : 0;
	$value['mother_resp'] = isset($_POST['mother_resp']) && $_POST['mother_resp'] == 1 ? 1 : 0;
	if($_POST['id'] != ''){
		$id = $_POST['id'];
		if(getPrvlg('parents_edit')){
			if(UpdateRowInTable("parents", $value, "id=$id", MySql_Database) != false){
				$answer['id'] = $id;
				$answer['error'] = '' ;
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		} else { 
			$answer['error'] = $lang['not_enough_privilege'] ;
		}
	} elseif($_POST['id'] == ''){ // new Student
		if(getPrvlg('parents_edit')){
			if(insertToTable("parents", $value, MySql_Database) != false){
				$answer['id'] =  mysql_insert_id();
				$answer['error'] = '';
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		} else { 
			$answer['error'] = $lang['not_enough_privilege'] ;
		}
	}
	
	setJsonHeader();
	print json_encode($answer);
	exit;
}

/********* parent list ***********************/
if(isset($_GET['list'])){
	include('parents_list.php');
	exit;
}
/********* search with parent code ***************/
$seek_parent = false;
if(isset($_GET['id']) && $_GET['id'] !=''){
	$parent_id = $_GET['id'];
} elseif(isset($_GET['std_id']) && $_GET['std_id'] !=''){
	$parent_id = getParentIdFromStdId($_GET['std_id']);
}


if(isset($parent_id) && $parent_id !=''){
	$sql = "SELECT * FROM parents WHERE id=$parent_id";
	$row = do_query($sql, MySql_Database);
	if($row['id'] != ''){
		$seek_parent = true;
	}
}

// Sons or brothers
if(isset($_GET['sons'])){
	require_once('parents_sons.php');
	exit;	
}

// Guardian
if(isset($_GET['guardian']) || isset($_GET['guard_id']) || isset($_GET['new_guard'])){
	require_once('parents_guardian.php');
	echo $guardian_html;
	exit;	
}

// default body

$parent = new Parents($parent_id);
echo $parent->loadMainLayout();
exit;
?>