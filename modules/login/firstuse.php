<?php
## client first use
////////////////////// BEGIN
$user_id = $_SESSION["user_id"];
$row = do_query( "SELECT * FROM users WHERE user_id =$user_id", MySql_Database); 

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

$loading_div = write_html('div', 'id="loading_main_div" class="hidden ui-widget-content ui-corner-all"',
	write_html('h3', '', $lang['loading']).
	write_html('div', 'id="loading_progress"', '')

);
$ballon_div = write_html('div', 'id="ballons_div"', '');
$assets_files = array(
	'lang/lang.js.php',
	'config/config.js.php',
	'assets/js/superfish.js',
	'assets/js/jquery.tablesorter.min.js',
	'assets/js/jquery.maskedinput-1.3.js',
	'assets/js/jquery.easing.1.3.js',
	'assets/js/globals.js',
	'assets/js/ui.combobox.js',
	'assets/js/jquery.colourPicker.js',
	'assets/js/jquery.hoverIntent.minified.js',
	'assets/js/jquery.dialogextend.js',
	'assets/js/tinymce/jquery.tinymce.min.js',
	'assets/js/tinymce/tinymce.min.js',
	'assets/css/jquery.colourPicker.css',
	'assets/css/superfish.css',
	'ui/main.js',
	'modules/profil/profil.js'
);
echo write_page($header, 
	$loading_div.
	$ballon_div.
	write_html('div', 'class="ui-widget-content ui-corner-all" align="center" style="padding:50px; margin:50px;"',
		write_html('div', 'div class="ui-state-error ui-corner-all" style="padding:10px"', 
			write_html('h2', '', $lang['first_use']).
			write_html('p', '', $lang['first_use_txt'])
		).
		write_html('div', 'id="profil_settings"',
			write_html('div', 'class="toolbox"',
				write_html('a', 'onclick="submitSettings()"', write_icon('disk').$lang['save'])
			).
			write_html('form', 'id="profil_setting_form" class="ui-corner-all ui-stat-highlight"',
			'<input type="hidden" name="first_use" value="first_use" />'.
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
						write_html_select('name="css" class="combobox" id="css"', $themes, ($row['css']!='' ? $row['css'] : $MS_settings['def_theme']))					
					)
				)
			)
		)
		)
	), $assets_files
);
?>