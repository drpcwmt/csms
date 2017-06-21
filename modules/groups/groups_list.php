<?php
## Groups list
require_once('modules/employers/employers.class.php');
$editable = getPrvlg('create_groups');

$groups_show_for_print = write_html('div', 'class="showforprint hidden ui-corner-al ui-state-highlight" style="text-align:center"',
	write_html('h2', '', $lang['groups_list']).
	write_html('h3', '', $lang[$parent].': '. getAnyNameById($parent, $parent_id)).
	write_html('h3', '', $_SESSION['year'].'/'.($_SESSION['year'] +1))
);

$groups_toolbox = write_html('div', 'class="toolbox"',
	($editable ? 
		write_html('a', 'onclick="newGroup(\''.$parent.'\', '.$parent_id.');"', $lang['new']. write_icon('document'))
		: ''
	).
	write_html('a', 'action="print_pre" rel="$(this).parents(\'.ui-tabs-panel\')" plugin="print"', $lang['print_list'].write_icon('print')).
	write_html('a', 'onclick="saveAsPdf(\'#group_list_div\')"', $lang['save_as_pdf'].write_icon('disk')).
	write_html('a', 'action="exportTable" rel="$(this).parents(\'.ui-tabs-panel\')" plugin="xml"',write_icon('disk'). $lang['export'])
);

$groups_tbody = '';
$groups = do_query_resource("SELECT * FROM groups WHERE parent='$parent' AND parent_id=$parent_id", DB_year);
while($group = mysql_fetch_assoc($groups)){
	$resp = new Employers($group['resp']);
	$count_stds = mysql_num_rows(do_query_resource("SELECT ".DB_student.".student_data.id, ".DB_student.".student_data.name, ".DB_student.".student_data.name_ar FROM ".DB_student.".student_data, ".DB_year.".groups_std WHERE ".DB_year.".groups_std.group_id=".$group['id']." AND ".DB_year.".groups_std.std_id=".DB_student.".student_data.id AND ".DB_student.".student_data.status=1 ORDER BY sex, name", MySql_Database));
	$groups_tbody .= write_html('tr', '',
		write_html('td', 'class="unprintable"',
			write_html('button', 'type="button" class="ui-state-default hoverable circle_button" val="'.$group['id'].'" onclick="openGroupInfos('.$group['id'].')"',  write_icon('extlink'))
		).
		($editable ? 
			write_html('td', 'class="unprintable"',
				write_html('button', 'type="button" class="ui-state-default hoverable circle_button" onclick="deleteGroup('. $group['id'].')"',  write_icon('close'))
			)
		:'').
		write_html('td', 'align="center" valign="top"', $group['name']).
		write_html('td', 'align="center" valign="top"', $resp->getName()).
		write_html('td', 'align="center" valign="top"', $count_stds)
	);
}
$groups_table = write_html('table', 'class="tablesorter group_list"', 
	write_html('thead', '',
		write_html('tr','',
			write_html('th', 'width="20" class="unprintable" style="background-image:none"', '&nbsp').
			write_html('th', 'width="20" class="unprintable" style="background-image:none"', '&nbsp').
			write_html('th', '', $lang['name']).
			write_html('th', '', $lang['resp']).
			write_html('th', '', $lang['students'])
		)
	).
	write_html('tbody', '', $groups_tbody)
);


?>