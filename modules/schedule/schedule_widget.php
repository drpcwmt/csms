<?php
## Schedule Widget

$thisTemplatePath = "modules/schedule/templates";

if(in_array($_SESSION['group'], array('parent', 'student'))){
	$con = 'student';
	$con_id = $_SESSION['std_id'];
} elseif($_SESSION['group']== 'prof'){
	$con = 'prof';
	$con_id = $_SESSION['user_id'];
}
$schedule = new schedule($con, $con_id);
$cur_date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
if( in_array(date('w', $cur_date), explode(',', $MS_settings['weekend']))){
	$today_class = 'day_off';
	$today = $lang['weekend'];
} elseif($cur_date> getYearSetting('end_date') || calendars::chkHoliday($cur_date, $con, $con_id)){
	$today_class = 'day_off';
	$today = $lang['holiday'];
} else {
	$today_class = 'day_on';
	$today = $schedule->loadDay($cur_date);
}	

$widget = write_html('fieldset', '', 
	write_html('legend', '', $lang['schedule']).
	write_html('table', 'width="100%" cellpadding="0" cellspacing="0" class="schedule_table"',
		write_html('tr', '',
			write_html('td', 'width="35%" valign="top" class="regleTd"',
				$schedule->loadTimeline()
			).
			write_html('td', 'valign="top" class="'.$today_class.'"', 
				$today
			)
		)
	)
);
?>