<?php
## book serial

$serial_toolbox = write_html('div', 'class="toolbox"',
	write_html('a', 'class="print_but" rel="#book_serials_div"', 
		$lang['print_tag'].
		write_icon('print')
	)
).
write_html('form', 'id="add_serial_form" class="unprintable ui-state-highlight ui-corner-all" style="padding:10px"', 
	write_html('label', 'class="label" style="width:100px; float:left"',$lang['add_serials']).
	'<input class="spinner" id="serials_count" name="serials_count" value="1" style="width:30px; height:14px; padding:3px; font-size:10px; margin-left:7px" />'.
	write_html('button', 'type="button" class="hoverable ui-corner-all" onclick="addNewSerials('.$book_id.')"', 
		$lang['add'].
		write_icon('plus')
	)
);

$serials_trs = '';
$serials = do_query_resource("SELECT * FROM book_serials WHERE book_id = $book_id ORDER BY serial ASC", LIBMS_Database);
while($serial = mysql_fetch_assoc($serials)){
	$stat = getStat($serial['stat']);
	$serials_trs .= write_html('tr', '',
		write_html('td', 'class="unprintable"', '<input type="checkbox" name="serials[]" value="'.$serial['serial'].'" />').
		write_html('td', '', $serial['book_id'].'-'.$serial['serial']).
		write_html('td', 'class="unprintable" style="text-align:center"',
			write_html('span', 'class="slider unprintable" value="'.$serial['stat'].'" rel="'.$serial['serial'].'"', '').
			write_html('span', 'class="stat_span" style="color:'.$stat[1].'"', $stat[0])
		).
		write_html('td', 'class="unprintable" style="text-align:center"',
			write_html('a', 'onclick="openHistoryDialog('.$book_id.', '.$serial['serial'].')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"', 
				write_icon('calendar')
			)
		) .
		write_html('td', 'class="unprintable" style="text-align:center"',
			(isAvaible($book_id, $serial['serial']) ? 
				write_html('a', 'onclick="openBorrowDialog('.$book_id.', '.$serial['serial'].')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"', 
					write_icon('suitcase')
				)
			: write_html('a', 'class="ui-corner-all ui-button-icon-only ui-button" style="height: 20px;"', 
					write_icon('cancel')
				)
			)
		).
		write_html('td', 'class="unprintable" style="text-align:center"',
			write_html('a', 'onclick="deleteSerial('.$book_id.', '.$serial['serial'].')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"', 
				write_icon('close')
			)
		)
	);
}

$book_serial = write_html('table', 'class="result" id="book_serial_table"',
	write_html('thead', '',
		write_html('tr', 'class="unprintable"',
			write_html('th', 'width="16" style="background-image:none"', '<input type="checkbox" class="select_all" />').
			write_html('th', '', $lang['serial']).
			write_html('th', 'width="120"', $lang['stat']).
			write_html('th', 'width="20"', $lang['history']).
			write_html('th', 'width="20"', $lang['borrow']).
			write_html('th', 'width="20"', $lang['delete'])
		)
	).
	write_html('tbody', '', $serials_trs)
);

?>