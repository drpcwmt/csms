<?php
##marks Settings
if(MS_codeName !='sms_basic'){
	$module_settings = write_html('fieldset', '',
		write_html('table', '', 
			write_html('tr', '',
				write_html('td', 'valign="middel" width="220" class="reverse_align"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['auto_approv_exp'])
				).
				write_html('td', 'valign="top"', 
					 write_html('span', 'class="buttonSet"',
						'<input type="radio"  name="auto_approv" id="auto_approv1" value="1" '. ($MS_settings['auto_approv']== 1 ? 'checked="checked"' : '') .'/><label for="auto_approv1">'.$lang['on'].'</label>
						<input type="radio"  name="auto_approv" id="auto_approv0" value="0" '. ($MS_settings['auto_approv']== 0 ? 'checked="checked"' : '') .'/><label for="auto_approv0">'.$lang['off'].'</label>'
					)
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="middel" class="reverse_align"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['std_can_see_unlocked_term'])
				).
				write_html('td', 'valign="top"', 
					 write_html('span', 'class="buttonSet"',
						'<input type="radio"  name="std_can_see_unlocked_term" id="std_can_see_unlocked_term1" value="1" '. ($MS_settings['std_can_see_unlocked_term']== 1 ? 'checked="checked"' : '') .'/><label for="std_can_see_unlocked_term1">'.$lang['on'].'</label>
						<input type="radio"  name="std_can_see_unlocked_term" id="std_can_see_unlocked_term0" value="0" '. ($MS_settings['std_can_see_unlocked_term']== 0 ? 'checked="checked"' : '') .'/><label for="std_can_see_unlocked_term0">'.$lang['off'].'</label>'
					)
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="middel" class="reverse_align"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['std_can_see_preset_exams'])
				).
				write_html('td', 'valign="top"', 
					 write_html('span', 'class="buttonSet"',
						'<input type="radio"  name="std_can_see_preset_exams" id="std_can_see_preset_exams1" value="1" '. ($MS_settings['std_can_see_preset_exams']== 1 ? 'checked="checked"' : '') .'/><label for="std_can_see_preset_exams1">'.$lang['on'].'</label>
						<input type="radio"  name="std_can_see_preset_exams" id="std_can_see_preset_exams0" value="0" '. ($MS_settings['std_can_see_preset_exams']== 0 ? 'checked="checked"' : '') .'/><label for="std_can_see_preset_exams0">'.$lang['off'].'</label>'
					)
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="middel" class="reverse_align"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['prof_can_see_other_marks'])
				).
				write_html('td', 'valign="top"', 
					 write_html('span', 'class="buttonSet"',
						'<input type="radio"  name="prof_can_see_other_marks" id="prof_can_see_other_marks1" value="1" '. ($MS_settings['prof_can_see_other_marks']== 1 ? 'checked="checked"' : '') .'/><label for="prof_can_see_other_marks1">'.$lang['on'].'</label>
						<input type="radio"  name="prof_can_see_other_marks" id="prof_can_see_other_marks0" value="0" '. ($MS_settings['prof_can_see_other_marks']== 0 ? 'checked="checked"' : '') .'/><label for="prof_can_see_other_marks0">'.$lang['off'].'</label>'
					)
				)
			)
		)
	).
	write_html('fieldset', '',
		write_html('legend', '', $lang['certficates']).
		write_html('table', 'width="100%", cellspasing="0"', 
			write_html('tr', '',
				write_html('td', 'valign="middel" class="reverse_align"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['add_grad_to_cert'])
				).
				write_html('td', 'valign="top"', 
					 write_html('span', 'class="buttonSet"',
						'<input type="radio"  name="add_grad_to_cert" id="add_grad_to_cert1" value="1" '. ($MS_settings['add_grad_to_cert']== 1 ? 'checked="checked"' : '') .'/><label for="add_grad_to_cert1">'.$lang['on'].'</label>
						<input type="radio"  name="add_grad_to_cert" id="add_grad_to_cert0" value="0" '. ($MS_settings['add_grad_to_cert']== 0 ? 'checked="checked"' : '') .'/><label for="add_grad_to_cert0">'.$lang['off'].'</label>'
					)
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top" class="reverse_align" width="160"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['cert_remarks'])
				).
				write_html('td', 'valign="top"', 
					 write_html('textarea', 'name="cert_remarks"', $MS_settings['cert_remarks'])
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top" class="reverse_align"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['generate_options'])
				).
				write_html('td', 'valign="top"', 
					write_html('ul', 'style="list-style:none; padding:0; margin:0"', 
						write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
							'<input type="checkbox" value="1" name="option_generate_head" '. ($MS_settings['option_generate_head']== 1 ? 'checked="checked"' : '') .'/>'.$lang['head_page']
						).
						write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
							'<input type="checkbox" value="1" name="option_generate_cert" '. ($MS_settings['option_generate_cert']== 1 ? 'checked="checked"' : '') .'/>'.$lang['certficates']
						).
						write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
							'<input type="checkbox" value="1" name="option_generate_skills" '. ($MS_settings['option_generate_skills']== 1 ? 'checked="checked"' : '') .'/>'.$lang['skills']
						).
						write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
							'<input type="checkbox" value="1" name="option_generate_exams" '. ($MS_settings['option_generate_exams']== 1 ? 'checked="checked"' : '') .'/>'.$lang['exams_results']
						).
						write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
							'<input type="checkbox" value="1" name="option_generate_appr" '. ($MS_settings['option_generate_appr']== 1 ? 'checked="checked"' : '') .'/>'.$lang['appreciations']
						).
						write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
							'<input type="checkbox" value="1" name="option_generate_absents" '. ($MS_settings['option_generate_absents']== 1 ? 'checked="checked"' : '') .'/>'.$lang['absents']
						).
						write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
							'<input type="checkbox" value="1" name="option_generate_behavior" '. ($MS_settings['option_generate_behavior']== 1 ? 'checked="checked"' : '') .'/>'.$lang['behavior']
						)
					)					
				)
			)
		)
	);
} else {
	$module_settings = write_error('You must Upgrade to SMS School life to enable this module.');	
}
?>