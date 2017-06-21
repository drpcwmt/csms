<?php 
## tools
$array_classes =objectsToArray(Classes::getList());
$array_levels = objectsToArray(Levels::getList());
$mat_array = objectsToArray(Materials::getList());

// Reset Schedule Form

$reset_schedule_form = write_html('form', 'id="reset_schedule_form" class="ui-corner-all ui-state-highlight" style="padding:10px; margin:15px"',
	'<input type="hidden" name="action" value="reset_schedule" />'.
	write_html('h3', '', 'Reset Class Schedule to default value').
	write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['class'])
			).
			write_html('td', 'valign="middel"',
				write_html_select('name="class_id" class="combobox" id="class_id" ', $array_classes, '')
			)
		).
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('label', '', '<input type="checkbox" name="del_specials" value="1" /> Delete all special dates')
			)
		).
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('button', 'type="button" class="ui-state-default hoverable" onclick="submitThisForm(this)"', 'Run')
			)
		)
	)
);


// Groups Tools
/*$field_name = "name_".$_SESSION['dirc'];
$materials = do_query_resource("SELECT id, $field_name FROM materials", DB_student);
$mat_array = array();
while($mat = mysql_fetch_assoc($materials)){
	$mat_array[$mat['id']] = $mat[$field_name]; 
}*/
$religion_table = write_html('table', 'id="religion_table" border="0" cellspacing="0" style="margin:8px 20px"',
	write_html('tr', '',
		write_html('td', ' width="120" valign="middel"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"',$lang['islamic_subject'])
		).
		write_html('td', '',
			write_html_select('name="ser_muslim" class="combobox"', $mat_array, $MS_settings['islamic_material'])
		)
	).
	write_html('tr', '',
		write_html('td', ' width="120" valign="middel"', 
			write_html('label', 'class="label ui-widget-header ui-corner-left"',$lang['christian_subject'])
		).
		write_html('td', '',
			write_html_select('name="ser_christian" class="combobox"', $mat_array, $MS_settings['christian_material'])
		)
	)
);


$generate_group_form = write_html('form', 'id="generate_group_form" class="ui-corner-all ui-state-highlight" style="padding:10px; margin:15px"',
	'<input type="hidden" name="action" value="generate_groups" />'.
	write_html('h3', '', 'Generate optional groups').
	write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['level'])
			).
			write_html('td', 'valign="middel"',
				write_html_select('name="level_id" class="combobox" id="level_id" ', $array_levels, '')
			)
		)
	).
	write_html('ul', 'style="list-style:none"',
		write_html('li', '', 
			write_html('label', '','<input type="checkbox" value="1" name="generate_optional_groups" checked="checked" />'.$lang['generate_optional_groups'])
		).
		write_html('li', '', 
			write_html('label', '',
				'<input type="checkbox" value="1" name="generate_religion_groups" />'.
				$lang['generate_religion_groups']
			).
			$religion_table
		).
		write_html('li', '',
			write_html('button', 'type="button" class="ui-state-default hoverable" onclick="submitThisForm(this)"', 'Run')
		)
	)
);
		
// ReGenerate  Service
$regenerate_services_form = write_html('form', 'id="regenerate_services_form" class="ui-corner-all ui-state-highlight" style="padding:10px; margin:15px"',
	'<input type="hidden" name="action" value="generate_services" />'.
	write_html('h3', '', 'Regenerate classes and student services').
	write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['level'])
			).
			write_html('td', 'valign="middel"',
				write_html_select('name="level_id" class="combobox" id="level_id" ', $array_levels, '')
			)
		).
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('label', '',
					'<input type="checkbox" value="1" name="delete_old_values"  />'.
					'Delete old Values'
				)
			)
		).
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('button', 'type="button" class="ui-state-default hoverable" onclick="submitThisForm(this)"', 'Run')
			)
		)
	)
);

$backupForm = write_html('form', 'id="backup_form" class="ui-corner-all ui-state-highlight" style="padding:10px; margin:15px"',
	'<input type="hidden" name="action" value="backup" />'.
	write_html('h3', '', 'Backup system').
	write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('label', '',
					'<input type="checkbox" value="1" name="file"  />'.
					'Files'
				)
			)
		).
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('label', '',
					'<input type="checkbox" value="1" name="sql"  />'.
					'Database'
				)
			)
		).
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('label', '',
					'<input type="checkbox" value="1" name="attachs"  />'.
					'Users files and documents'
				)
			)
		).	
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('button', 'type="button" class="ui-state-default hoverable" onclick="submitThisForm(this, \'reloadBackupFiles(ans.files)\')"', ' Backup now')
			)
		)
	).
	write_html('ul', 'id="backup_ul"', '')
);

$updateForm = write_html('form', 'id="update_form" target="update-ifram" enctype="multipart/form-data" method="post" action="index.php?module=systools&action=update" class="MS_formed ui-corner-all ui-state-highlight" style="padding:10px; margin:15px"',
	'<input type="hidden" name="action" value="update" />'.
	write_html('h3', '', 'Update system').
	write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				write_html('label', '',
					'<input type="file" name="file" />'
				)
			)
		).
		write_html('tr', '',
			write_html('td', 'width="85" valign="middel" ', '&nbsp;').
			write_html('td', 'valign="right"',
				'<input type="submit" value="Update now" onclick="$(\'#update_form\').submit()" />'
			)
		)
	)
).write_html('iframe', 'name="update-ifram" src="index.php?module=systools&action=update" style="min-height:290px;" width="100%"', '');

// echo


echo write_html('div', 'class="tabs"', 
	write_html('ul', '', 
		write_html('li', '', write_html('a', 'href="#systools_generate_group"', $lang['groups'])).
		write_html('li', '', write_html('a', 'href="#systools_schedule"', $lang['schedule'])).
		write_html('li', '', write_html('a', 'href="#systools_service"', $lang['services']))
	). 
	write_html('div', 'id="systools_generate_group"', $generate_group_form).
	write_html('div', 'id="systools_schedule"', $reset_schedule_form).
	write_html('div', 'id="systools_service"', $regenerate_services_form)
);
		
?>