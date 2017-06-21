<?php
/** System widget
*

*/

if($_SESSION['group'] != 'superadmin'){
	$widget = write_error($lang['no_privlege']);
} else {
	// Users
	$connected_users = do_query_array("SELECT user_id, `group` FROM users WHERE last_login > ".(time()-180), MySql_Database);
	$users_tabs = write_html('table', 'width="100%"',
		write_html('tr', '', 
			write_html('td', '', 
				write_html('h4', '', $lang['cur_concted_user'].': '.count($connected_users) )
			).
			write_html('td', ' class="reverse_align"', 
				write_html('button', 'class="hoverable ui-state-default" action="openCurUsersList"', 
					$lang['open']. write_icon('extlink')
				)
			)
		)
	);
	if(count($connected_users) < 5 ) {
		$users_trs = array();
		foreach($connected_users as $usr){
			$user = new Users($usr->group, $usr->user_id);
			$users_trs[] = write_html('tr', '',
				write_html('td', 'width="24"',
					write_html('button', 'module="settings" action="openUser" userid="'.$user->user_id.'" group="'.$user->group.'" class="circle_button hoverable ui-state-default"',
						write_icon('person')
					)
				).
				write_html('td', '', $user->getRealName())
			);
		}
		
		$users_tabs .= write_html('table', 'class="result"',
			implode('', $users_trs)
		);
		
	}
	$users_out = write_html('fieldset', '',
		write_html('legend', '', $lang['users']).
		$users_tabs
	);
	
	// System State
	$state = $MS_settings['system_stat'] = 1 ? 'Online' : 'Offline';
	$system_out = write_html('fieldset', '',
		write_html('legend', '', $lang['system']).
		write_html('table', 'width="100%"',
			write_html('tr', '', 
				write_html('td', 'width="50%"', 
					write_html('h3', 'style="color:'.($state == 'Online' ? 'green' : 'red').'"', $state)
				).
				write_html('td', 'class="reverse_align"', 
					($state == 'Online' ? 
						write_html('button', 'module="settings" action="activateSys" state="0" class="button_circle hoverable ui-state-default" title="'.$lang['online'].'"', $lang['suspend']. write_icon('power'))
					: 
						write_html('button', 'module="settings" action="activateSys" state="1" class="button_circle hoverable ui-state-default" title="'.$lang['online'].'"', $lang['activate'].write_icon('power'))
					)
				)
			)
		)
	);
	
	// backup
	require_once('backup.class.php');
	
	$backups = Backup::getBackupList();
		// Auto backup
	if( $backups == false || count($backups) == 0 || (time() - filemtime($backups[0])) >= ($MS_settings['backup_ttl'] * 24 * 60 * 60)){
		$fileid = date( 'Y-M-d-h-i-s');
		$dir = 'attachs/backup/';
		$filename = $fileid.'.zip';
		if(Backup::createMySqlBackup($fileid) != false){
			@rename($dir.$filename, $dir.str_replace('DB_', 'AUTO_', $filename));
			$last_backup = date('H:i:s d M Y', time());
		} else {
			$last_backup = write_error('None');
		}
	} else {
		$last_backup = date('H:i:s d M Y', filemtime($backups[0]));
	} 
		
	
	$backup_out = write_html('fieldset', '',
		write_html('legend', '', $lang['backup']).
		write_html('h4', '', $lang['last_backup'].': '. $last_backup ).
		write_html('button', 'module="settings" action="openSysTools" class="ui-state-default hoverable"',
			$lang['system_tools'].
			write_icon('gear')
		)
	);
			
	$widget = $system_out.$users_out.$backup_out;
}
?>