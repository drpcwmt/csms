<?php
## SMs 
## settings Times ##
$begin_day = getYearSetting('begin_date');
$end_day = getYearSetting('end_date');

if(in_array($this_system->type, array('sms', 'hrms'))){
	$new_week_day = '<ul id="new_week_day">';
		for($i=0; $i<=6; $i++){ 
			$new_week_day .= '<li style="cursor:pointer" class="'; 
			$new_week_day .=  (in_array($i, explode(',', $this_system->getSettings('weekend'))) !== false) ? 'ui-state-default' : 'ui-state-active' ; 
			$new_week_day .= '" val="'.$i.'" onclick="recordWeekDay(this)"> '.$days_name_arr[$i+1].' </li>';
		}
	$new_week_day .= '</ul>';
}
$settings_times = write_html('table', 'cellspacing="0" border="0"',
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['year_begin'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="year_begin" id="year_begin" class="mask-date datepicker" value="'.  unixtoDate($begin_day).'" />'
		)
	).
	write_html('tr', '',
		write_html('td', 'width="120" valign="middel" class="reverse_align"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['year_end'])
		).
		write_html('td', 'valign="top"', 
			'<input type="text" name="year_end" id="year_end" class="mask-date datepicker" value="'. unixtoDate($end_day) .'" />'
		)
	).
	(in_array($this_system->type, array('sms', 'hrms')) ?
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" class="reverse_align"', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['day_time_begin'])
			).
			write_html('td', 'valign="top"', 
				'<input type="text" name="day_time_begin" id="day_time_begin" class="mask-time" value="'.  unixtotime($this_system->getSettings('day_time_begin')) .'" />'
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" class="reverse_align"', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['day_time_end'])
			).
			write_html('td', 'valign="top"', 
				'<input type="text" name="day_time_end" id="day_time_end" class="mask-time" value="'. unixtotime($this_system->getSettings('day_time_end')) .'" />'
			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" class="reverse_align"', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['weekend'])
			).
			write_html('td', 'valign="top"', 
				'<input type="hidden" name="weekend" id="weekend" value="'.$this_system->getSettings('weekend').'" />'.
				$new_week_day
			)
		)
	:'')
);
?>
