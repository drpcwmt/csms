<?php 
## borrow list

$sql = "SELECT * FROM borrow";

if(isset($book_id)){
	$fields = array('patron_name', 'borrow_date', 'return_date');
	$sql .= " WHERE book_id=".$_GET['book_id'];
	if(isset($_GET['serial'])){
		$sql .= " AND serial=".$_GET['serial'];
	}
	$sql .= " ORDER BY borrow_date DESC";
} elseif(isset($std_id)){
	$fields = array('book_name', 'borrow_date', 'return_date', 'max_date');
	$sql .= " WHERE con='std' AND con_id='$std_id'";
	$sql .= " ORDER BY borrow_date DESC";		
}elseif(isset($emp_id)){
	$fields = array('book_name', 'borrow_date', 'return_date', 'max_date');
	$sql .= " WHERE con='emp' AND con_id='$emp_id'";
} elseif(isset($_GET['late'])){
	$title = $lang['late_list'];
	$fields = array('book_name', 'patron_name', 'book_id', 'serial', 'borrow_date', 'max_date');
	$sql .= " WHERE return_date IS NULL AND max_date<".time()." ORDER BY borrow_date ASC";
}

// Preparing thead
$thead_ths = write_html('th', 'width="16" style="background-image:none" class="unprintable"', '<input type="checkbox" class="select_all" />').
write_html('th', 'width="16" style="background-image:none" class="unprintable"', '&nbsp;');
if(isset($_GET['late']) || isset($_GET['today'])){
	$thead_ths .= write_html('th', 'width="16" style="background-image:none" class="unprintable"', '&nbsp;');
}
foreach($fields as $field){
	$thead_ths .= write_html('th', '', $lang[$field]);
}
$thead_ths .= write_html('th', '', $lang['stat']);

// preparing tbody
$tbody_trs = '';
$query = do_query_resource($sql, LIBMS_Database);
while($row = mysql_fetch_assoc($query)){
	$tr = write_html('td', ' class="unprintable"', '<input type="checkbox" name="id[]" />').
	write_html('td', ' class="unprintable"', 
		write_html('a', 'onclick="openBorrowInfo('.$row['id'].')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;" title="'.$lang['open_borrow'].'"', 
			write_icon('extlink')
		)
	);
	if(isset($_GET['late']) || isset($_GET['today'])){
		$tr .= write_html('td', ' class="unprintable"', 
			write_html('a', 'onclick="returnBookByCode('.$row['id'].')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;" title="'.$lang['return_book'].'"', 
				write_icon('arrowreturnthick-1-s')
			)
		);
	}
	foreach($fields as $field){
		if(strpos($field, '_date') !== false){
			$value = unixToDate($row[$field]);
		} elseif($field == 'patron_name'){
			$value = getPatronName($row['con'], $row['con_id']);
		} elseif($field == 'book_name'){
			$value = getNameFromId('books', $row['book_id']);
		} else {
			$value = $row[$field];
		}
		$tr .= write_html('td', '', $value);
	}
	if(isset($_GET['late']) || isset($_GET['today'])){
		$stat = getStat($row['stat']);
		$tr .= write_html('td', '', 
			write_html('span', 'class="stat_span" style="color:'.$stat[1].'"', $stat[0])
		);
	} else {
		$tr .= write_html('td', '', 
			($row['return_stat'] != '' && ($row['stat']>$row['return_stat'])? 
				write_icon('alert')
				: ''
			)
		);
	}
	$tbody_trs .= write_html('tr', '', $tr);
}

$list = write_html('table', 'class="tablesorter"',
	write_html('thead', '',
		write_html('tr', '',
			$thead_ths
		).
		write_html('tbody', '', $tbody_trs)
	)
);