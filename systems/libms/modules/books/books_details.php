<?php
## LibMS
## books details

$seek = false;
if(isset($_GET['isbn']) || isset($_GET['book_id'])){
	if(isset($_GET['isbn']) && $_GET['isbn'] != ''){
		$sql = "SELECT * FROM books WHERE isbn ='".$_GET['isbn']."'";
	} elseif(isset($_GET['book_id']) && $_GET['book_id'] != ''){
		$sql = "SELECT * FROM books WHERE id ='".$_GET['book_id']."'";
	} 

	$row = do_query($sql, MySql_Database);
	if($row['id'] != ''){
		$book_id = $row['id'];
		$sub_code = getCodeFromSubId($row['cat_sub']);
		$seek= true;
	} else {
		die( write_error($lang['cant_find_book']));
	}
}

$book_toolbox = write_html('div', 'class="toolbox"',
	write_html('a', 'onclick="submitbookForm()"', 
		write_icon('disk').
		$lang['save']
	).write_html('a', 'class="print_but" rel="#book_detail_form"', 
		write_icon('print').
		$lang['print']
	)
);

$book_details =write_html('table', 'border="0" cellspacing="0"',
		write_html('tr', '',
			write_html('td', 'width="120" class="reverse_align"','&nbsp;').
			write_html('td', 'width="320"','&nbsp;').
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"',$lang['enable_borrow'])
			).
			write_html('td', '',
				write_html('span', 'class="buttonSet"',
					'<input type="radio"  name="borrow" id="enable_borrow-1" value="1" '. (!$seek || ($seek && $row['borrow']== 1) ? 'checked="checked"' : '') .'/><label for="enable_borrow-1">'.$lang['on'].'</label>
					<input type="radio"  name="borrow" id="enable_borrow-0" value="0" '. ($seek && $row['borrow']== 0 ? 'checked="checked"' : '') .'/><label for="enable_borrow-0">'.$lang['off'].'</label>'
				)
			)
		).
write_html('tr', '',
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"',$lang['name'])
			).
			write_html('td', '',
				'<input type="text" name="name" id="book_name" value="'.(($seek)? $row['name'] : '' ).'" title="name" class="input_double required" />'
			).
			($seek ?
				write_html('td', 'width="120" class="reverse_align"',
					write_html('label', 'class="label"',$lang['book_no'])
				).
				write_html('td', '',
					'<input type="text"  disabled="disabled" value="'. $row['id'].'" />'.
					'<input type="hidden"  name="id" id="book_id" value="'. $row['id'].'" />'
				)
			: 
				write_html('td', 'width="120" class="reverse_align"',
					write_html('label', 'class="label"',$lang['count_serials'])
				).
				write_html('td', ' ',
					'<input type="hidden" name="id" id="book_id"  />'.
					'<input type="text" name="count" id="count_serials"  " />'
				)
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"',$lang['cat'])
			).
			write_html('td', 'width="320"',
				'<input type="text" name="cat_name" id="cat_name"  value="'. (($seek)? getNameFromId('cats', $row['cat']) : '') .'" />'.
				'<input type="hidden" name="cat" id="cat" class="autocomplete_value" value="'. ($seek? $row['cat'] : '') .'" />'
			).
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"',$lang['sub_cat'])
			).
			write_html('td', '',
				'<input type="text" name="cat_sub_name" id="cat_sub_name"  value="'. (($seek)? getNameFromId('cats_sub', $row['cat_sub']) : '') .'" />'.
				'<input type="hidden" name="cat_sub" id="cat_sub" class="autocomplete_value" value="'. ($seek? $row['cat_sub'] : '') .'" />'
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"', $lang['cat_code'])
			).
			write_html('td', 'width="320"',
				'<input type="text" name="cat_code" id="cat_code" class="required" title="'.$lang['cat_code'].'" value="'.($seek? $sub_code : '') .'" '.($seek && $sub_code !='' ? 'disabled="disabled"' : '').' />'
			).
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"', 'ISBN')
			).
			write_html('td', 'width="320"',
				'<input type="text" name="isbn" class="required" title="ISBN" value="'. (($seek)? $row['isbn'] : '') .'" />'
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"', $lang['place'])
			).
			write_html('td', '',
				'<input type="text" name="place" title="'.$lang['place'].'" value="'. (($seek)? $row['place'] : '' ).'" />'
			).
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"', $lang['vols'])
			).
			write_html('td', '',
				'<input class="spinner" name="vols" title="'.$lang['vols'].'" value="'. (($seek)? $row['vols'] : '1' ).'" />'
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"', $lang['author'])
			).
			write_html('td', 'width="320"',
				'<input type="text" name="author_name" id="author_name"  value="'. (($seek)? getNameFromId('author', $row['author']) : '') .'" />'.
				'<input type="hidden" name="author" id="author" class="autocomplete_value" value="'. ($seek? $row['author'] : '') .'" />'
			).
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"', $lang['date'])
			).
			write_html('td', '',
				'<input type="text" name="date"  value="'. (($seek)? $row['date']: '' ).'" />'
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"', $lang['vendor'])
			).
			write_html('td', 'width="320"',
				'<input type="text" name="vendor_name" id="vendor_name"  value="'. (($seek)? getNameFromId('vendor', $row['vendor']) : '') .'" />'.
				'<input type="hidden" name="vendor" id="vendor" class="autocomplete_value" value="'. ($seek? $row['vendor'] : '') .'" />'
			).
			write_html('td', 'width="120" class="reverse_align"',
				write_html('label', 'class="label"', $lang['price'])
			).
			write_html('td', '',
				'<input type="text" name="price"  value="'. (($seek)? $row['price'] : '' ).'" />'
			)
		)
		
	).
	write_html('fieldset', '', 
		write_html('legend', '', $lang['notes']).
		write_html('textarea', 'name="comments"', (($seek)? $row['comments'] : '' )
	)
);
?>