<?php
$settings = $this_system->settings;
if($this_system->type == 'sms'){
	$module_settings = write_html('fiedset', '',
		write_html('table', '', 
			write_html('tr', '',
				write_html('td', 'width="150"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align"', $lang['this_account_code'])
				).
				write_html('td', '', 
					'<input type="text" value="'.$settings->this_main_code.'" name="this_main_code" />'
				)
			).
			write_html('tr', '',
				write_html('td', 'width="150"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align"', $lang['cost_center'])
				).
				write_html('td', '', 
					'<input type="text" value="'.$settings->this_ccid.'" name="this_ccid" />'
				)
			)
		)
	);
} elseif($this_system->type == 'safems'){
	$settings->ccs_opts = write_select_options(Costcenters::getListOpts(), $this_system->getSettings('cc_group_id'), false);
	$settings->banks_opts = Banks::getOptions($this_system->getSettings('def_bank'));
	$module_settings = fillTemplate('modules/accounts/templates/settings.tpl', $settings);
} else {
	$module_settings ='';
}
?>