<?php
## SMS 
## SETTING module

if(isset($_GET['users'])){
	if(isset($_GET['user_data']) || isset($_GET['reset'])){
		$user = new Users(safeGet($_GET['group']), safeGet($_GET['user_id']));
		echo $user->loadLayout();
	} elseif(isset($_GET['save_user'])){
		if(getPrvlg('add_user')){
			$user = new Users(safeGet($_POST['group']), safeGet($_POST['user_id']));
			echo $user->saveUser($_POST);
		}
	} elseif(isset($_GET['new'])){
		if(getPrvlg('add_user')){
			echo Users::newUser();
		} else {
			echo write_error($lang['no_privilege']);
		}
	} elseif(isset($_GET['del'])){
		if(getPrvlg('add_user')){
			$user = new Users(safeGet($_POST['group']), safeGet($_POST['user_id']));
			if($user->_delete(true)!= false){
				echo '{"error": ""}';
			} else {
				echo '{"error": "'.$lang['error_updating'].'"}';
			}
		} else {
			echo '{"error": "'.$lang['no_privilege'].'"}';
		}
	} 
	
	exit;
}

if(isset($_GET['jobprvlg'])){
	$result = false;
	if(isset($_GET['add'])){
		 $result =json_encode_result(do_insert_obj(array('user_id'=>$_POST['user_id'], 'job'=>$_POST['job_id']), 'privilege_cc'));
	} elseif(isset($_GET['delete'])) {
		$result = json_encode_result(do_delete_obj('user_id='.$_POST['user_id'].' AND job='.$_POST['job_id'], 'privilege_cc'));
	}
	echo json_encode_result($result == false ? false : true);
	exit;
}

if(isset($_POST['chknewuser'])){
	$name = strtolower(trim( $_POST['chknewuser']));
	$name = str_replace('  ', '.', $name);
	$name = str_replace(' ', '.', $name);
	$chk = do_query_array("SELECT user_id FROM users WHERE name LIKE '$name%'" );
	if($chk != false && count($chk) > 0){
		echo '{"error" : "", "login" : "'. $name.mysql_num_rows($chk).'"}';	
	} else {
		echo '{"error" : "", "login" : "'. $name.'"}';
	}
	exit;	
}

//* Privileges */
if(isset($_GET['prvlg'])){
	if( strpos($_GET['prvlg'], '-') !== false ){
		$g = explode('-', $_GET['prvlg']) ;
		$group = $g[0];
		$user_id = $g[1];
	} else {
		$group = safeGet($_GET['prvlg']);	
		$user_id = '';
	}
	
	$prvlgForm = new Privileges($group, $user_id);
	if(isset($_GET['save'])){
		if($prvlg->_chk('edit_prvlg')){
			if(Privileges::_save($group, $user_id, $_POST['prvlg'])){
				echo '{"error" : ""}';	
			} else {
				echo '{"error": "'.$lang['error_updating'].'"}';
			}
		} else {
			echo '{"error": "'.$lang['no_privilege'].'"}';
		}
	} else {
		echo $prvlgForm->loadLayout();
	}
	exit;
}

/* post update settings */
if(isset($_POST['update_settings'])){
	setJsonHeader();
	if($prvlg->_chk('setting_write')){
		$ans = true;
		foreach($_POST as $key => $value){
			if(!is_array($value)){
				if(strpos($key, "_date") !== false ) { $value= datetounix($value);}
				if(strpos($key, "_time") !== false ) { $value= timetounix($value);}
				if(!do_query_resource(" UPDATE settings SET value='$value' WHERE key_name='$key'")){
					$ans = false;
				}	
			}
		}
		if(!do_query_resource("UPDATE years SET begin_date=".datetounix($_POST['year_begin']).", end_date=".datetounix($_POST['year_end'])." WHERE year=".$_SESSION['year'])){
			$ans = false;
		}
		if($ans){
			echo '{"error": ""}';
		} else {
			echo '{"error": "'.$lang['error_updating'].'"}';
		}
	} else {
		echo '{"error": "'.$lang['no_privilege'].'"}';
	}
	exit;
}


/* Group List */
if(isset($_GET['listgroup'])){
	echo Users::listGroup(safeGet('listgroup'));
	exit;
}
	


/* user Table */
$settings_users = Users::loadMainLayout();

/* Generate users */
if(isset($_GET['generate_users'])){
	include('settings_users_generator.php');
	exit;
}

/* layout div */
//include('settings_documents.php');

/* layout div */
include('settings_layout.php');

/* Infos div */
include('settings_infos.php');

/* alerts div */
//include('settings_alerts.php');

/* times div */
include('settings_times.php');

/* Servers div */
$settings_servers = $this_system->loadConnectionsSettings();


	// Modules settings'
$module_settings_li= '';
$module_settings_div= '';
$modules = scandir('modules');
$avoided_modules = array('settings');
foreach($modules as $module){
	if(!in_array($module, $avoided_modules) && is_dir('modules/'.$module) && file_exists('modules/'.$module.'/settings.php')){
		include('modules/'.$module.'/settings.php');
		$module_settings_li .= write_html('li', 'rel="setting_'.$module.'" class="hoverable clickable ui-corner-all ui-state-default"', $lang[$module]);
		$module_settings_div .= write_html('div', 'id="setting_'.$module.'" class="ui-corner-all ui-widget-content hidden setting_divs"', $module_settings);

	}
}

/* Default body */
$seek = true;
$setting_html = write_html('div', 'class="ui-corner-top ui-widget-header reverse_align"',
		write_html('h2', 'class="reverse_align big_title"', $lang['settings'])
	) .
write_html('div', 'class="ui-corner-bottom ui-widget-content transparent_div" ',
	write_html('form', 'id="settings_form" editable="'.($prvlg->_chk('setting_write') ? '1' : '0').'"',
		'<input type="hidden" name="update_settings" />'.
		($prvlg->_chk('setting_write') ?
			write_html('div', 'class="toolbox"',
				write_html('a', 'onclick="submitSettings()"', 
					write_icon('disk').
					$lang['save']
				).
				write_html('a', 'onclick="openSysTools()"', 
					write_icon('gear').
					$lang['system_tools']
				)
			)
		: '').
		write_html('table', 'width="100%" cellpadding="0" cellspacing="0" border="0"',
		write_html('tr', '',
			write_html('td', 'width="20%" valign="top"', 
				write_html('ul', 'class="listMenuUl"',
					write_html('li', 'rel="setting_infos" class="hoverable clickable ui-corner-all ui-state-default ui-state-active"', $lang['school_infos']).
					write_html('li', 'rel="setting_layout" class="hoverable clickable ui-corner-all ui-state-default"', $lang['layout']).
					write_html('li', 'rel="setting_users" class="hoverable clickable ui-corner-all ui-state-default"', $lang['users_options']).
				//	write_html('li', 'rel="setting_alerts" class="hoverable clickable ui-corner-all ui-state-default"', $lang['alerts_settings']).
					write_html('li', 'rel="setting_times" class="hoverable clickable ui-corner-all ui-state-default"', $lang['times']).
					write_html('li', 'rel="settings_servers" class="hoverable clickable ui-corner-all ui-state-default"', $lang['servers']).
					$module_settings_li
				)
			).
			write_html('td', 'id="setting_div_td" valign="top"',
				write_html('div', 'id="setting_infos" class="ui-corner-all ui-widget-content setting_divs"',
					$settings_infos
				).
				write_html('div', 'id="setting_layout" class="ui-corner-all ui-widget-content hidden setting_divs"', 
					$settings_layout
				).
				write_html('div', 'id="setting_users" class="ui-corner-all ui-widget-content hidden setting_divs"', 
					$settings_users
				).
				/*write_html('div', 'id="setting_alerts" class="ui-corner-all ui-widget-content hidden setting_divs"', 
					$settings_alerts
				).*/
				write_html('div', 'id="setting_times" class="ui-corner-all ui-widget-content hidden setting_divs"', 
					$settings_times
				).
				write_html('div', 'id="settings_servers" class="hidden setting_divs"', 
					$settings_servers
				).
				$module_settings_div
			)	
		)
	) 
	)
);

echo $setting_html;
echo write_script('iniSettings();');
?>