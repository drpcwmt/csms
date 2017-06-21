<?php
## libMs 
## settings Infos ##

$settings_infos = write_html('table', 'cellspacing="0" border="0"',
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['school_name'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="school_name" id="school_name" class="input_double" value="'.  $MS_settings['school_name'] .'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['school_name_ar'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="school_name_ar" id="school_name_ar" class="input_double" value="'.  $MS_settings['school_name_ar'] .'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['code'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="sch_code" value="'.  $MS_settings['sch_code'] .'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['school_addres'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="school_adress" id="school_adress" class="input_double" value="'.  $MS_settings['school_adress'] .'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['school_site'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="school_site" id="school_site" class="input_double" value="'.  $MS_settings['school_site'] .'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['school_mail'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="school_mail" id="school_mail" class="input_double" value="'.  $MS_settings['school_mail'] .'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120"valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['school_tel'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="school_tel" id="school_tel"  value="'.  $MS_settings['school_tel'] .'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120"valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['school_cp'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="school_cp" id="school_cp"  value="'.  $MS_settings['school_cp'] .'" />'
		)
	)
);
?>
