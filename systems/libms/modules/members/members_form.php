<?php
## Memebers form

$member_form = write_html('table', '',
	write_html('tr', '',
		write_html('td', 'width="120"',
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['have'])
		).
		write_html('td', '',
			'<input type="text" id="total_have" value="'.getMemberNowBooks($type, $server.'-'.$id).'" disabled/>'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120"',
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['borrows'])
		).
		write_html('td', '',
			'<input type="text" id="total_borrows" value="'.getMemberTotalBorrows($type, $server.'-'.$id).'" disabled/>'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120"',
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['late'])
		).
		write_html('td', '',
			'<input type="text" id="total_bad" value="'.getMemberBookLate($type, $server.'-'.$id).'" disabled/>'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120"',
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['lost'])
		).
		write_html('td', '',
			'<input type="text" id="total_lost" value="'.getMemberBookLost($type, $server.'-'.$id).'" disabled/>'
		)
	)
)

?>