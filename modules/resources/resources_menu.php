<?php
## SMS
## Resources menu
$layout = new Layout();

$resource_menu = write_html('ul', ' id="resources_menus" class="nav"',
	(getPrvlg('resource_read_profs') ||  getPrvlg('resource_read_supervisors') || getPrvlg('resource_edit_principals') ? 
		write_html('li', '',
			write_html('a', 'class="ui-state-default hoverable"', $lang['personel']).
			write_icon('triangle-1-s').
			write_html('ul', '', 
				(getPrvlg('add_employers') && $MS_settings['hrms_server'] == '0' ? 
					write_html('li', '',
						write_html('a', 'module="employers" action="newEmployer" class="ui-state-default hoverable"', $lang['add'])
					)
				: '').
				(getPrvlg('resource_read_profs')? 
					write_html('li', '', 
						write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="profs"', $lang['profs'])
					)
				: '').
				(getPrvlg('resource_read_supervisors')? 
					write_html('li', '', 
						write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="supervisors"', $lang['supervisors'])
					)
				: '').
				(getPrvlg('resource_read_coordinators')? 
					write_html('li', '', 
						write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="coordinators"', $lang['coordinators'])
					)
				: '').
				(getPrvlg('resource_read_principals')? 
					write_html('li', '', 
						write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="principals"', $lang['principals'])
					)
				: '')
			)
		)
	: '').
	(getPrvlg('resource_read_materials')? 
		write_html('li', 'class="'.$layout->pro_option.'"', 
			write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="materials"', $lang['materials'])
		)
	: '').
	(getPrvlg('resource_read_levels')? 
		write_html('li', '', 
			write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="levels"', $lang['levels'])
		)
	: '').
	(getPrvlg('resource_read_classes')? 
		write_html('li', '', 
			write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="classes"', $lang['classes'])
		)
	: '').
	(getPrvlg('read_groups')? 
		write_html('li', '', 
			write_html('a', 'class="ui-state-default hoverable" action="openGroups" module="groups"', $lang['groups'])
		)
	: '').
	(getPrvlg('resource_read_halls')? 
		write_html('li', '', 
			write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="halls"', $lang['halls'])
		)
	: '').
	(getPrvlg('resource_read_tools')? 
		write_html('li', '', 
			write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="tools"', $lang['tools'])
		)
	: '')
);
?>
