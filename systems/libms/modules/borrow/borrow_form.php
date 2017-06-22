<?php
## borrow form
$seek = false;
$fatal_error = false;
$con_array = array('std'=>$lang['student'],'emp'=>$lang['prof']);
$pre_borrow = false;

// if Patron have been send
if(isset($_GET['con_id'])){
	$patron_name = getPatronName($_GET['con'], $_GET['con_id']);
	$p = explode( '-', $_GET['con_id']);
	$patron_server= $p[0];
	$patron_id=$p[1];
	
}

// a saved borrow 
if(isset($_GET['borrow_id'])){
	$seek = true;
	$borrow = do_query(
		"SELECT borrow.*, books.isbn, books.name, book_serials.stat
		FROM borrow, books, book_serials
		WHERE borrow.book_id=books.id 
		AND books.id= book_serials.book_id
		AND borrow.serial = book_serials.serial
		AND borrow.id=".$_GET['borrow_id'], LIBMS_Database);
	if($borrow != false){
		$book_id = $borrow['book_id'];
		$serial =  $borrow['serial'];
		$name =  $borrow['name'];
		$isbn = $borrow['isbn'];
		$patron_name = getPatronName($borrow['con'], $borrow['con_id']);
		$p = explode( '-', $borrow['con_id']);
		$patron_server= $p[0];
		$patron_id=$p[1];
		$book_befor = getStat($borrow['stat']);
		if($borrow['max_date'] > time()){
			$borrow_stat_str = '';
		} elseif($borrow['return_date'] == ''){
			if($borrow['return_stat'] == -1){
				$borrow_icon = 'error';
				$borrow_stat_str =  $lang['lost'];
			} elseif($borrow['max_date'] < time()){
				$borrow_icon = 'warning';
				$borrow_stat_str = $lang['late'];
			}
		} elseif($borrow['return_date'] != ''){
			if($borrow['return_stat'] == -1){
				$borrow_icon = 'error';
				$borrow_stat_str = $lang['lost'];
			} elseif($borrow['return_stat'] < $borrow['stat']){
				$borrow_stat_str =  $lang['bad'];
				$borrow_icon = 'error';
			} else {
				$borrow_icon = 'success';
				$borrow_stat_str = '';
			}
			$book_after = getStat($borrow['return_stat']);
		}
	} else {
		$fatal_error = write_error($lang['cant_find_request']);
	}
} elseif(isset($_GET['book_id']) && isset($_GET['serial'])){
	$book_id = $_GET['book_id'];
	$serial = $_GET['serial'];
	if(isAvaible($book_id,  $serial)){
		$book = do_query("SELECT books.*,book_serials.stat FROM books, book_serials WHERE books.id=book_serials.book_id AND books.id=$book_id AND book_serials.serial=$serial", LIBMS_Database);
		$name = $book['name'];
		$isbn = $book['isbn'];
		$pre_borrow = true;
		$book_befor = getStat($book['stat']);
		$borrow_stat_str = '';
	} else {
		$fatal_error = write_error($lang['book_is_borrowed']);
	}
} 

if($fatal_error != false ){
	$borrow_toolbox = '';
	$borrow_form = $fatal_error;
} else {
	if(isset($book_id)) { $cat = getCatFromBook($book_id); }
	
	// Toolbox
	$borrow_toolbox = write_html('div', 'class="toolbox"',
		write_html('a', 'onclick="submitBorrowForm(\'loadModule(\\\'borrow\\\', \\\'borrow_id=\\\'+ans.id, getLang(\\\'borrow\\\'))\')" ', 
			write_icon('disk').
			$lang['save']
		).
		write_html('a', 'onclick="submitBorrowForm(true)"', 
			write_icon('disk').
			$lang['save_and_onother']
		).
		write_html('a', '', 
			write_icon('print').
			$lang['print']
		).
		($seek ?
			write_html('a', '', 
				write_icon('close').
				$lang['delete']
			)
		: '')
	);
	
	$borrow_form = write_html('form', 'id="borrow_form"',
		'<input type="hidden" name="id"  id="borrow_id" value="'. ($seek ? $borrow['id'] : '' ).'" />'.
		write_html('table', '', 
			write_html('tr', '',
				write_html('td', 'valign="top"',
					write_html('fieldset', '',
						write_html('legend', '', $lang['patron']).
						write_html('table', 'border="0" cellspacing="0"',
							write_html('tr', '',
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label"',$lang['to'])
								).
								write_html('td', 'width="320"',
									($seek ? 
										'<input type="text" disabled="disabled" value="'.$con_array[$borrow['con']].'"/>'. 
										'<input type="hidden" name="con" value="'.$borrow['con'].'"/>' 
									: 
										(isset($_GET['con']) ?
											'<input type="text" disabled="disabled" value="'.$con_array[$_GET['con']].'"/>'.
											'<input type="hidden" name="con" value="'.$_GET['con'].'"/>' 
										:	
											write_html_select('name="con" id="con" class="required combobox"', $con_array ,  'student' )
										)
									)
								)
							).
							write_html('tr', '', 
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label"',$lang['name'])
								).
								write_html('td', '',
									($seek ? 
										'<input type="text" disabled="disabled" class="input_double" value="'.$patron_name.' - '.$patron_server.'"/>'.
										'<input type="hidden" name="con_id" value="'.$borrow['con_id'].'" />' 
									: 
										(isset($_GET['con_id']) ?
											'<input type="text" disabled="disabled" class="input_double" value="'.$patron_name.' - '.$patron_server.'"/>' .
											'<input type="hidden" name="con_id" value="'.$_GET['con_id'].'" />'
										:
											'<input type="text" name="name" id="con_name" class="required input_double" title="'.$lang['name'].'" value="'.(isset($patron_name) ? $patron_name : '').'"/>'.'<input type="hidden" name="con_id"  class="autocomplete_value" />'
										)
									)
								)
							)
						)
					)
				).
				write_html('td', 'valign="top"',
					write_html('fieldset', '',
						write_html('legend', '', $lang['options']).
						write_html('table', 'border="0" cellspacing="0"',
							write_html('tr', '',
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label"', $lang['borrow_date'])
								).
								write_html('td', '',
									'<input type="text" name="borrow_date" id="borrow_date" class="datepicker mask-date required" title="'.$lang['borrow_date'].'" '.($seek ? 'disabled="disabled"' : '').' value="'. (($seek)? unixToDate($borrow['borrow_date']) : unixToDate(time()) ).'" />'
								)
							).
							write_html('tr', '',
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label"', $lang['max_date'])
								).
								write_html('td', '',
									'<input type="text" class="datepicker mask-date required" title="'.$lang['max_date'].'" id="max_date" name="max_date" '.($seek ? 'disabled="disabled"' : '').' value="'.($seek ? unixToDate($borrow['max_date']) : unixToDate(time()+ ($MS_settings['def_borrow_limit']*86400) )).'" />'
								)
							).
							($seek && $borrow['return_date'] !='' ? 
								write_html('tr', '',
									write_html('td', 'width="120" class="reverse_align"',
										write_html('label', 'class="label"', $lang['return_date'])
									).
									write_html('td', '',
										'<input type="text" class="datepicker mask-date" title="'.$lang['return_date'].'" id="return_date" name="return_date"  value="'.($seek && $borrow['return_date'] !='' ? unixToDate($borrow['return_date']) : '').'" />'
									)
								)
							: '' )
						)
					)
				)
			).
			write_html('tr', '',
				write_html('td', 'valign="top"',
					write_html('fieldset', '',
						write_html('legend', '', $lang['book']).
						write_html('table', 'border="0" cellspacing="0"',
							write_html('tr', '',
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label"',$lang['book_code'])
								).
								write_html('td', '',
									'<input type="text" id="book_code" title="'.$lang['book_code'].'" '.($seek ? 'disabled="disabled"' : '').'  value="'.($pre_borrow || $seek ? $book_id.'-'.$serial : '').'" />'
								).
								write_html('td', '',
									($seek == false && $pre_borrow == false ? 
										'<a class="button" icon="search" onClick="searchBookCode()">'.$lang['search'].'</a>'
									: '')
								)
							).
							write_html('tr', '',
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label"',$lang['name'])
								).
								write_html('td', '',
									'<input type="text" id="book_name" title="'.$lang['book_name'].'" '.($seek ? 'disabled="disabled"' : '').'  value="'.(isset($name) ? $name : '').'" class="input_double required" />'.
									'<input type="hidden" id="book_id" name="book_id" class="autocomplete_value" value="'.(isset($book_id) ? $book_id : '').'"  />'
								).
								write_html('td', '',
									($seek == false && $pre_borrow == false ? 
										'<a class="button" icon="search" onClick="searchBookInfos()">'.$lang['search'].'</a>'
									: '')
								)
							).
							write_html('tr', 'colspan="2"',
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label"', $lang['category'])
								).
								write_html('td', 'width="320"',
									'<input type="text" id="book_cat" class="input_double" disabled="disabled" title="'.$lang['category'].'" value="'.($seek || $pre_borrow ? $cat['cat'].' - '. $cat['cat_sub'] : '').'" />'
								)
							).
							write_html('tr', 'colspan="2"',
								write_html('td', 'width="120" class="reverse_align"',
									write_html('label', 'class="label"', 'ISBN')
								).
								write_html('td', 'width="320"',
									'<input type="text" name="isbn" id="isbn" class="required" title="ISBN" '.($seek? 'disabled="disabled"' : '').' value="'.(isset($isbn) ? $isbn : '').'" />'
								)
							)
						)
					)
				).
				write_html('td', 'valign="top"',
					($seek  || $pre_borrow ? 
						write_html('fieldset', '',
							write_html('legend', '', $lang['borrow_stat']).
							(isset($borrow_icon) ? '<img src="assets/img/'.$borrow_icon.'.png" />' : '').
							write_html('h2', 'class="title" style="padding:5px; float:left; margin:0px; 20px"', $borrow_stat_str).
						
							write_html('table', 'width=""',
								write_html('tr', '',
									write_html('td', 'width="120"',
										write_html('label', 'class="label" style="width:120px"', $lang['before'])
									).
									write_html('td', '',
										'<input type="text" disabled="disabled" value="'.$book_befor[0].'" />'
									)
								).
								(isset($book_after) ? 
									write_html('tr', '',
										write_html('td', '',
											write_html('label', 'class="label" style="width:120px"', $lang['after'])
										).
										write_html('td', '',
											'<input type="text" disabled="disabled" value="'.$book_after[0].'" />'
										)
									)
								: '')
							)
						)
					: 
						write_html('div', 'id="serial_div"', 
							'<input type="hidden" id="borrow_serial" name="serial"  value="'.(isset($serial) ? $serial : '').'"  />'
						)
					)
				)
			)
		)
	);
}
?>