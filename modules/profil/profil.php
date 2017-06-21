<?php
// update setting tab
if(isset($_POST['user_id']) && $_POST['user_id'] == $_SESSION['user_id']){
	$fields = getTableFields( 'users', MySql_Database);
	$updates = array();
	$error = false;
	foreach($_POST as $key => $value){
		if(in_array($key, $fields)){
			if($key != "password" || ($key == 'password' && $value !='')){
				$updates[] = $key."='".$value."'";
			}
		}
	}
	if(!do_query_edit( "UPDATE users SET ".implode( $updates,',')." WHERE user_id=".$_POST['user_id']." AND `group`='".$_SESSION['group']."'", MySql_Database)){
		$error = true;
	}
	$id=$_POST['user_id'];	
	
	if($_SESSION['lang'] != $_POST['def_lang']){
		$_SESSION['lang'] = $_POST['def_lang'];
		$_SESSION["dirc"] = $_POST['def_lang'] == 'ar' ? 'rtl': 'ltr';
		$id = 'restart';
	}
	if($_SESSION['css'] != $_POST['css']){
		$_SESSION['css'] = $_POST['css'];
		$id = 'restart';
	}
	
	if(isset($_POST['first_use']) &&  $_POST['first_use'] == 'first_use'){
		$_SESSION['css'] = $_POST['css'];
		$_SESSION['lang'] = $_POST['def_lang'];
		$_SESSION["dirc"] = $_POST['def_lang'] == 'ar' ? 'rtl': 'ltr';
		$id = 'restart';
	}
	
	$answer = array();
	if($error == false){
		$answer['id'] = $id;
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['error_updating'];
	}
	echo  json_encode($answer);	
	exit;
}



$user_id = $_SESSION["user_id"];

////////////////////// Default body
$row = do_query( "SELECT * FROM users WHERE user_id =$user_id AND `group`='".$_SESSION['group']."'", MySql_Database); 

$langs = array();
$dir = scandir('lang');
foreach($dir as $file){
	if(!in_array($file, array('.','..', '_notes') )&& strpos($file, '.php') !== false){
		$file = str_replace('.php', '', $file);
		$langs[$file] = ucfirst($file);
	}
}

$themes = array();
$dir = scandir('assets/css/themes');
foreach($dir as $file){
	if(!in_array($file, array('.','..', '_notes')) && strpos($file, '.css') === false){
		$file = str_replace('.php', '', $file);
		$themes[$file] = ucfirst($file);
	}
}

//personel_data_link
if($_SESSION['group'] == 'student'){
	$personel_data_link = 'index.php?module=students&my_infos='.$user_id.'"';
} elseif($_SESSION['group'] == 'student'){
	$personel_data_link = 'index.php?module=parents&id='.$user_id.'"';
} else {
	$personel_data_link = 'index.php?module=employers&id='.$user_id;
}

echo write_html('div', 'class="tabs"',
	write_html('ul', '', 
		write_html('li', '', write_html('a', 'href="#profil_settings"', $lang['settings'])).
		(getPrvlg('profil_read') ? 
			write_html('li', '', write_html('a', 'href="'.$personel_data_link .'"', $lang['personel_infos']))
		: '')
	).
	write_html('div', 'id="profil_settings"',
		write_html('div', 'class="toolbox"',
			write_html('a', 'onclick="submitSettings()"', write_icon('disk').$lang['save'])
		).
		write_html('form', 'id="profil_setting_form"',
			'<input type="hidden" name="user_id" value="'.$user_id.'" />'.
			write_html('table', 'width="100%" border="0" cellspacing="0"',
				write_html('tr', '',
					write_html('td', 'class="reverse_align" width="150" valign="middel"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['login_name'])
					).
					write_html('td', '',
						'<input type="text" name="name" value="'.$row['name'].'" />'
					)
				).
				write_html('tr', '',
					write_html('td', 'class="reverse_align" width="150" valign="middel"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"',$lang['chng_pass'])
					).
					write_html('td', '',
						'<input type="password" name="password" id="pass"/>'
					)
				).
				write_html('tr', '',
					write_html('td', 'class="reverse_align" width="150" valign="middel"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['cfm_password'].': ')
					).
					write_html('td', '',
						'<input type="password" name="pass2" onChange="validatePass(this)"/>'
					)
				).
				write_html('tr', '',
					write_html('td', 'class="reverse_align" width="150" valign="middel"',
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['chose_ui-lang'].': ')
					).
					write_html('td', '',
						write_html_select('name="def_lang" class="combobox" id="def_lang"', $langs, $row['def_lang'])					
					)
				).
				write_html('tr', '',
					write_html('td', 'class="reverse_align" width="150" valign="middel"',
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['theme'].': ')
					).
					write_html('td', '',
						write_html_select('name="css" class="combobox" id="css"', $themes, $row['css'])					
					)
				)
			)
		)
	)
);
?>