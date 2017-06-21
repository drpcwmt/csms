<?php 
##absent by Student
if(isset($con_id)){
	$std_id = $con_id;
	$student = new Students($std_id);
	
	$class_id = $student->getClass()->getName();
	$level_id = $student->getLevel()->getName();
	$seek = true;
	$begin_day = getYearSetting('begin_date');
	$end_day = getYearSetting('end_date');
	$sql = "SELECT * FROM absents WHERE con_id= $std_id AND day >= $begin_day AND day < $end_day ORDER BY day DESC";
	$query = do_query_resource( $sql, DB_year);
	$total_absent =  mysql_num_rows($query);
	$terms_arr = getTermsList( 'student' , $std_id );
} else {
	die("can't find student, contact your system administrator!");
}

$std_abs_toolbox =  write_html('div', 'class="toolbox"',
	write_html('a', 'rel="#std_abs_div" class="print_but"', write_icon('print').$lang['print']).
	write_html('a', 'action="exportTable" rel="#std_abs_div"',write_icon('disk'). $lang['export'])
);


$std_abs_form = write_html('div', '',
	write_html('form', 'id="std_list_form" class="ui-corner-all ui-state-highlight" style="padding:5px; margin-bottom:10px"',
		'<input type="hidden" name="con" value="std" />'.
		'<input type="hidden" name="con_id" value="'.$std_id.'" />'.
		write_html('div', 'align="center" class="hidden showforprint"', write_html('h2', '', $student->getName())).
		write_html('table', 'width="100%" border="0" cellspacing="0" cellpadding="0"',
			write_html('tr', '',
				write_html('td', 'width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['date'])
				).
				write_html('td', '',
					write_html_select( 'id="std_absent_list_terms" onchange="submitStdAbsentList()" class="combobox"', array_merge($terms_arr, getPassedMonths()), '')
				)
			)
		)
	).
	write_html('table', 'width="100%" border="0" cellspacing="0" cellpadding="0"',
		write_html('tr', '',
			write_html('td', ' valign="top"', 
				write_html('table', 'width="100%" border="0" cellspacing="0" cellpadding="0"',
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['total_absent'])
						).
						write_html('td', '',
							'<input type="text" id="std_totlat_abs_inp" disabled="disabled" value="'.$total_absent.'"  />'
						)
					).
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['ill_abs_days'])
						).
						write_html('td', '',
							'<input type="text" id="ill_abs_days_inp" disabled="disabled" value="'.getStdTotalAbs('ill', $std_id).'"  />'
						)
					).
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['justify'])
						).
						write_html('td', '',
							'<input type="text" id="justify_abs_days_inp" disabled="disabled" value="'.getStdTotalAbs('justify', $std_id).'"  />'
						)
					)
				)
			).
			write_html('td', '', 
				write_html('div', 'id="chartDiv_stdabs"', '')
			)
		)
	)
);
?>