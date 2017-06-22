<?php
## Borrow return form

if(isset($_GET['borrow_id']) && $_GET['borrow_id'] != ''){
	$borrow_id = $_GET['borrow_id'];
	$borrow_info = getBorrowInfoById($borrow_id);
	$book_id = $borrow_info['book_id'];
	$serial = $borrow_info['serial'];
} elseif(isset($_GET['book_code']) && $_GET['book_code'] != ''){
	$b = explode('-', $_GET['book_code']);
	$book_id = $b[0];
	$serial = $b[1];
	$borrow_info = getBorrowInfo($book_id, $serial);
	$borrow_id = $borrow_info['id'];
} else {
	$borrow_form = write_error($lang['cant_find_request']);
}

if($borrow_info != false){
	$stat = getStat($borrow_info['stat']);
	$borrow_form = write_html('form', 'id="borrow_return_form"',
		($dialog_mode == false ?
			write_html('div', 'class="toolbox"',
				write_html('a', 'onclick="submitReturnForm('.$borrow_info['id'].')"', 
					write_icon('disk').
					$lang['save']
				)
			)
		:'').
		write_html('fieldset', '',
			write_html('legend','', $lang['book']).
			write_html('table', '',
				write_html('tr', '',
					write_html('td', 'width="120" class="reverse_align"', 
						write_html('label', 'class="label"', $lang['book_name'])
					).
					write_html('td', 'width="250" class="ui-widget-content"', getNameFromId('books', $book_id))
				).
				write_html('tr', '',
					write_html('td', ' class="reverse_align"', 
						write_html('label', 'class="label"', $lang['patron_name'])
					).
					write_html('td', 'class="ui-widget-content"', $borrow_info['patron_name'])
				).
				write_html('tr', '',
					write_html('td', ' class="reverse_align"', 
						write_html('label', 'class="label"', $lang['date'])
					).
					write_html('td', 'class="ui-widget-content"', unixToDate($borrow_info['date']))
				).
				write_html('tr', '',
					write_html('td', ' class="reverse_align"', 
						write_html('label', 'class="label"', $lang['stat'])
					).
					write_html('td', 'class="ui-widget-content"', 
						write_html('span', 'class="stat_span" style="color:'.$stat[1].'"', $stat[0])
					)
				)
			)
		).
		write_html('fieldset', '',
			write_html('legend','', $lang['return']).
			'<input name="id" type="hidden" value="'.$borrow_id.'" />'.
			write_html('table', '',
				write_html('tr', '',
					write_html('td', 'width="120" class="reverse_align"', 
						write_html('label', 'class="label"', $lang['date'])
					).
					write_html('td', '', 
						'<input type="text" class="datepicker mask-date required" title="'.$lang['return_date'].'" id="return_date" name="return_date" value="'.unixToDate(time()).'" />'
					)
				).
				write_html('tr', '',
					write_html('td', ' class="reverse_align"', 
						write_html('label', 'class="label"', $lang['stat'])
					).
					write_html('td', '', 
						write_html('span', 'class="slider unprintable" id="return_stat_slider" value="'.$borrow_info['stat'].'" rel="'.$book_id.'-'.$serial.'"', '').
						'<input type="hidden" name="return_stat" id="return_stat" value="'.$borrow_info['stat'].'" />'
					)
				)
			)
		)
	);
} else {
	$borrow_form = write_error($lang['cant_find_borrow']);
}
?>
