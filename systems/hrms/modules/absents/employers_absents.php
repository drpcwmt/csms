<?php
## employer absents
if(!getPrvlg('absent_read')){ die($lang['restrict_accses']);};


$emp_id = $_GET['emp_id'];
$employer = new Employers($emp_id);
$begin_date = $this_system->getYearSetting('begin_date');
$end_date = $this_system->getYearSetting('end_date');

$absents = $employer->getAbsents($begin_date, $end_date);
$total_absent =  count($absents);
$total_conv = count($employer->getAbsents($begin_date, $end_date, 'ill!=1'));
$total_ill = count($employer->getAbsents($begin_date, $end_date, 'ill=1'));

$employer_absent_trs ='';
foreach($absents as $row){
	$employer_absent_trs .= write_html('tr', '',
		write_html('td', 'align="center"', unixToDate($row->day)).
		write_html('td', 'align="center"',
			($row->ill == 1  ? write_icon('check') : '' )
		).
		write_html('td', 'align="center"',
			($row->approved == 1  ? write_icon('check') : '' )
		).
		write_html('td', 'align="center"', $row->comments)
	);
}

$opts = array();

for($i =0; $i<12; $i++){
	$m = date('m', $begin_date)+$i;
	$b = mktime(0,0,0,$m, 1, $_SESSION['year']);
	$month = date('m', $b);
	$opts[] = write_html('option', 'value="'.$m.'"', $lang["months_$month"]);
}

echo write_html('div', 'id="employer_abs_div"',
	write_html('div', 'class="toolbox"', 
		write_html('span', '',
			write_html('label', 'class="label ui-widget-header ui-corner-left" style="width:100px"', $lang['period']).
			write_html('select', 'name="period" class="combobox"',
				write_html('option', 'value="0"', $lang['all']).
				implode('', $opts)
			)
		).
		write_html('a', 'class="print_but" rel="tabs_absents"', write_icon('print').$lang['print'])
	).
    write_html('div', 'class="showforprint hidden" align="center"',
        write_html('h1', '',$lang['absents_report']).
    	write_html('h2', '', $employer->getName())
	).
	write_html('table', 'width="100%" border="0" cellspacing="0"',
		write_html('tr', '',
			write_html('td width="200" valign="top"', '',
				write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel" ', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['total_absent'])
						).
						write_html('td', 'valign="middel" colspan="2"',
							write_html('div', 'class="ui-widget-content ui-corner-right" style="width:150px; font-size:12px; padding:4px"',
								$total_absent
							)
						)
					).
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel" ', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['ill_abs_days'])
						).
						write_html('td', 'valign="middel" colspan="2"',
							write_html('div', 'class="ui-widget-content ui-corner-right" style="width:150px; font-size:12px; padding:4px"',
								$total_ill
							)
						)
					).
					write_html('tr', '',
						write_html('td', 'width="120" valign="middel" ', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['conv_abs'])
						).
						write_html('td', 'valign="middel" colspan="2"',
							write_html('div', 'class="ui-widget-content ui-corner-right" style="width:150px; font-size:12px; padding:4px"',
								$total_conv
							)
						)
					)
				)
			)
		).
		write_html('tr', '',
			write_html('td', '',
				write_html('table', 'class="tablesorter"',
                	write_html('thead', '',
                    	write_html('tr', '',
                        	write_html('th', '', $lang['date']).
							write_html('th', '', $lang['ill_abs_days']).
							write_html('th', '', $lang['conv_abs']).
							write_html('th', '', $lang['comments'])
						)
					).
					write_html('tbody', '', $employer_absent_trs)
				)
			)
		)
	)
);
	
?>