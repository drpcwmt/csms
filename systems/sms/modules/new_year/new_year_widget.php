<?php
## new year widget
$widget = '';
if(getPrvlg('new_year') && time() > getYearSetting('end_date')){
	$widget = write_html('fieldset', '', 
		write_html('legend', '', $lang['new_year']).
		write_html('div', 'class-"ui-state-highlight ui-corner-all"',
			write_html('h3', '', $lang['new_year_finalize_txt']).
			write_html('button', 'class="ui-state-default hoverable" module="new_year" action="finalizeYear"',
				$lang['finalize_year']
			)
		)
	);
}
?>