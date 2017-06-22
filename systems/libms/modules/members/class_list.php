<?php
##LIBMS 
## classes list
$class_list_toolbox = write_html('div', 'class="toolbox"',
	write_html('a', 'rel="#module_members" class="print_but" ', 
		write_icon('print').
		$lang['print']
	)
);
if(isset($_GET['class_id'])){
	$school = getSchools($school_code); 
	$students = do_query_resource("SELECT std_id FROM classes_std WHERE class_id=$class_id", DB_year,  $school['ip']);
	
	$tbody_trs='';
	while($std = mysql_fetch_assoc($students)){
		$have = getMemberNowBooks('std', $school_code.'-'.$std['std_id']);
		$total_borrow = getMemberTotalBorrows('std', $school_code.'-'.$std['std_id']);
		$late = getMemberBookLate('std', $school_code.'-'.$std['std_id']);
		$lost = getMemberBookLost('std', $school_code.'-'.$std['std_id']);
		$tbody_trs .= write_html('tr', '',
			write_html('td', ' class="unprintable"', 
				write_html('a', 'onclick="openMemberInfos('.$std['std_id'].', \'std\', \''.$school_code.'\')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"', 
					write_icon('extlink')
				)
			).
			write_html('td', ' class="unprintable"', 
				write_html('a', 'onclick="openBorrowByCon(\'std\', \''.$school_code.'-'.$std['std_id'].'\')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"', 
					write_icon('suitcase')
				)
			).
			write_html('td', '', getStudentNameById($std['std_id'], $school_code)).
			write_html('td', '', $have).
			write_html('td', '', $total_borrow).
			write_html('td', '', $late).
			write_html('td', '', $lost)
		);	
	}
	
	$thead = write_html('thead', '',
		write_html('tr', '', 
			write_html('th', 'width="20" class="unprintable"', '&nbsp;').
			write_html('th', 'width="20" class="unprintable"', '&nbsp;').
			write_html('th', '', $lang['name']).
			write_html('th', 'width="60"', $lang['have']).
			write_html('th', 'width="60"', $lang['borrows']).
			write_html('th', 'width="60"', $lang['late']).
			write_html('th', 'width="60"', $lang['lost'])
		)
	);
	
	$class_list = write_html('table', 'class="tablesorter"',
		$thead.
		write_html('tbody', '', $tbody_trs)
	);
} else {
	$class_list = write_error("cant find Class");
}
?>