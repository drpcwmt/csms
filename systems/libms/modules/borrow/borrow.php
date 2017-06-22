<?php
##libMS
## borrow main file
require_once('scripts/hrms_functions.php');
require_once('borrow_functions.php');
//dialog mmode
$dialog_mode = isset($_GET['dialog']) ? true : false;


if(isset($_POST['id'])){
	if($_POST['id'] != ''){
		$borrow_id= $_POST['id'];
		if(UpdateRowInTable('borrow', $_POST, "id='$borrow_id'")){
			echo "{\"error\" : \"\", \"id\" : \"$borrow_id\"}";
		} else {
			echo "{\"error\" : \"Error while updating\"}";
		}
	} else {
		if($borrow_id = insertToTable('borrow', $_POST)){
			echo "{\"error\" : \"\", \"id\" : \"$borrow_id\"}";
		} else {
			echo "{\"error\" : \"Error while inserting\"}";
		}
	}
	exit;	
}

if(isset($_GET['borrowlist'])){
	if(isset($_GET['book_id'])) { $book_id = $_GET['book_id'];}
	if(isset($_GET['std_id'])) { $std_id = $_GET['std_id'];}
	if(isset($_GET['emp_id'])) { $emp_id = $_GET['emp_id'];}
	require_once('borrow_lists.php');
	if ( $dialog_mode){
		echo $list;
	} else {
		echo write_html('div', 'class="ui-corner-top ui-widget-header reverse_align"',
			write_html('h3', 'class="title_wihte"',  isset($title)? $title : $lang['borrow_list'])
		).
		write_html('div', 'class="ui-corner-bottom ui-widget-content module_content" style="padding:5px"',
			$list
		);
	}
	exit;	
}

if(isset($_GET['borrowreturn'])){
	require_once('borrow_return.php');
	echo $borrow_form;
	exit;	
}

if(isset($_GET['borrow_id']) ||isset($_GET['new_borrow']) ){
	require_once('borrow_form.php');
	if ( $dialog_mode){
		echo $borrow_form;
	} else {
		echo write_html('div', 'class="ui-corner-top ui-widget-header reverse_align"',
			write_html('h3', 'class="title_wihte"',isset($title)? $title : $lang['borrow'])
		).
		write_html('div', 'class="ui-corner-bottom ui-widget-content module_content" style="padding:5px"',
			$borrow_toolbox.$borrow_form
		);
	}
	exit;
}

if(isset($_GET['book_serial'])){
	$book_id = $_GET['book_id'];
	$book_serials = do_query_resource("SELECT * FROM book_serials, books WHERE books.id=book_serials.book_id AND books.id=$book_id AND books.borrow=1 AND book_serials.stat<6 AND book_serials.stat>-1", LIBMS_Database);
	$out= '';
	$selected = false;
	while($book_serial = mysql_fetch_assoc($book_serials)){
		$serial = $book_serial['serial'];
		$chk = isAvaible($book_id,  $serial);
		if($chk == false) { $borrow_info = getBorrowInfo($book_id,  $serial);}
		if($selected == false && $chk != false){
			$checked= true;
			$selected = true;
		} else { $checked = false;}
		$out .= write_html('li', 'class="ui-corner-all '.($chk ? 'ui-state-default hoverable clickable' : 'ui-state-error').'" onclick="selectSerial(this)"',
			write_html('table', 'width="100%"',
				write_html('tr', '',
					write_html('td', '',
						write_html('h4', '', 
							'<input type="radio" name="serial" value="'.$serial.'" '.($chk ? '': 'disabled="disabled').' '.($checked ? 'checked="checked"': '').'"/>'.$lang['serial_no'].': '.$serial.
							'<input type="radio" name="stat" class="hidden" value="'.$book_serial['stat'].'" />'
						)
					).
					write_html('td', 'align="center"', ($book_serial['stat']*20)." %").
					write_html('td', 'align="center" width="180"', 
						write_html('div', 'class="serial_stat" style="height:5px" value= "'.($book_serial['stat']*20).'"', '')
					)
				).
				($chk == false ?
					write_html('tr', '',
						write_html('td', 'colspan="2"', 
							write_html('span', '', $borrow_info['patron_name'])
						).
						write_html('td', '', 
							write_html('span', '', $lang['return_date'].': '. unixToDate($borrow_info['date_max']))
						)
					)
				: ''
				)
			)
		);
	}
	
	echo write_html('fieldset', '',
		write_html('legend', '', $lang['serials']).
		write_html('ul', 'class="serial_list"',
			$out
		)
	);
	exit;
}
