<?php
## SMS
## setting layout

$sys_layout = new Layout($this_system->settings);

$sys_layout->debug_mode_active =  $this_system->settings->debug_mode == 1 ? 'checked="checked"' : '';
$sys_layout->debug_mode_off =  $this_system->settings->debug_mode == 0 ? 'checked="checked"' : '';

$sys_layout->auto_backup_active =  $this_system->settings->auto_backup == 1 ? 'checked="checked"' : '';
$sys_layout->auto_backup_off =  $this_system->settings->auto_backup == 0 ? 'checked="checked"' : '';

$sys_layout->theme_arr = write_select_options(getThemeArray(), $this_system->settings->def_theme);
$sys_layout->lang_arr = write_select_options(getLangArray(), $this_system->settings->default_lang);

$sys_layout->template = "modules/settings/templates/generals.tpl";
$settings_layout = $sys_layout->_print();

/*$settings_layout = write_html('table', 'cellspacing="0" border="0"',
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['def_theme'])
		).
		write_html('td', 'valign="top"',
			write_html_select('name="def_theme" class="combobox" id="def_theme"', getThemeArray(), $MS_settings['def_theme']) 
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['default_lang'])
		).
		write_html('td', 'valign="top"', 
			write_html_select('name="default_lang" class="combobox" id="default_lang"', getLangArray(), $MS_settings['default_lang'])
		)
	).
	write_html('tr', '',
		write_html('td', 'valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['debug_mode'])
		).
		write_html('td', 'valign="top"', 
			 write_html('span', 'class="buttonSet"',
				'<input type="radio"  name="debug_mode" id="debug_mode1" value="1" '. ($MS_settings['debug_mode']== 1 ? 'checked="checked"' : '') .'/><label for="debug_mode1">'.$lang['on'].'</label>
				<input type="radio"  name="debug_mode" id="debug_mode0" value="0" '. ($MS_settings['debug_mode']== 0 ? 'checked="checked"' : '') .'/><label for="debug_mode0">'.$lang['off'].'</label>'
			)
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['session_timeout'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="sessiontimeout" id="sessiontimeout" class="input_half" value="'. $MS_settings['sessiontimeout'] .'" /> '.$lang['minuts']
		)
	).

	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['logo'])
		).
		write_html('td', 'valign="top"', 
			write_html('button', 'type="button" class="ui-state-default hoverable ui-corner-all" module="upload" action="changeLogo"', 
				write_icon('circle-arrow-n').
				$lang['change']
			).
			write_html('span', '', '<img src="attachs/img/logo.png" id="settings-header" height="25" border="0" style="vertical-align: bottom; margin:0px 15px" />')
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['header'])
		).
		write_html('td', 'valign="top"', 
			write_html('button', 'type="button" class="ui-state-default hoverable ui-corner-all " module="upload" action="changeHeader"', 
				write_icon('circle-arrow-n').
				$lang['change']
			).
			write_html('span', '', '<img src="attachs/img/header.jpg" id="settings-header" height="25" border="0" style="vertical-align: bottom; margin:0px 15px" />')
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120"valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['footer'])
		).
		write_html('td', 'valign="top"', 
			write_html('button', 'type="button" class="ui-state-default hoverable ui-corner-all" module="upload" action="changeFooter"', 
				write_icon('circle-arrow-n').
				$lang['change']
			).
			write_html('span', '', '<img src="attachs/img/footer.jpg" id="settings-footer" height="25" border="0" style="vertical-align: bottom; margin:0px 15px" />')
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120"valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['ltr_lang_name'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="name_template" id="name_template" class="input_double" value="'.  $MS_settings['name_template'] .'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120"valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['date_template'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="date_template" id="date_template" value="'.  $MS_settings['date_template'] .'" />'
		)
	)
);*/

?>