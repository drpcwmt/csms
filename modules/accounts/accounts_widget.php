<?php
## currency widget

	$widget = write_html('fieldset', '', 
		write_html('legend', '', $lang['accounts']).
		write_html('form', 'class="ui-state-highlight ui-corner-all" style="padding:3px"',
			write_html('h3', '', $lang['close_day']).
			'<input type="text" class="mask-date datepicker" id="closeday_date" value="'.unixTodate(time()).'" />'.
			write_html('button', 'class="ui-state-default hoverable" module="accounts" action="closeDay"',
				$lang['close_day']
			)
		)
	);
