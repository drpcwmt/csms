<?php
/********* insert new permission ************/
if(isset($_POST['stdId'])){
	setJsonHeader();
	if(getPrvlg('att_absent_edit')){
		$error = false;
		$day = dateToUnix($_POST['day']);
		$hour = timeToUnix($_POST['hour']);
		$till = ($_POST['till'] != '') ? timeToUnix($_POST['till']) : 'NULL';
		$out_by = $_POST['out_by'];
		$pers = ($_POST['pers'] != 'other') ? $_POST['pers'] : $_POST['other_name'];
		$ids = (strpos($_POST['stdId'], ',') !== false) ? explode(',', $_POST['stdId']) : array( $_POST['stdId']);
		$comments = $_POST['comments'];
		foreach($ids as $std_id){
			$chk_std = do_query_resource("SELECT id FROM out_permis WHERE std_id=$std_id AND day=$day", DB_year);
			if($chk_std == false || mysql_num_rows($chk_std) == 0){
				if(!do_query_edit( "INSERT INTO out_permis (std_id, day, hour, till, out_by, pers, comments) VALUES ($std_id, $day, $hour, $till, '$out_by', '$pers', '$comments')", DB_year)){
					$error = true;
				}
			}
		}
		if($error){
			echo json_encode(array('error' => $lang['error_while_updating'])); 
		} else {
			echo json_encode(array('error' => ''));
		}
	} else {
		echo json_encode(array('error' => $lang['no_privilege'])); 
	}
	exit;
}
// Add permissio frorm
if(isset($_GET['add_permission_form'])){
	echo write_html('form', 'id="permission_form"',
		write_html('table', 'width="100%" cellspacing="5"', 
			write_html('tr', '',
				write_html('td', 'valign="top" width="47%"', 
					'<input type="hidden" id="stdIds" name="stdId" class="stdnamesug" />'.
					write_html('fieldset', '',
						write_html('legend', '', $lang['time']).
						write_html('table', 'border="0" cellspacing="0"', 
							write_html('tr', '',
								write_html('td', 'width="85"',
									write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['date'])
								).
								write_html('td', '', 
									'<input type="text" class="datepicker mask-date" id="permission_form_date" name="day" value='.date('d/m/Y').'/>'
								)
							).
							write_html('tr', '',
								write_html('td', '',
									write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['from'])
								).
								write_html('td', '', 
									'<input type="text" size="8" name="hour" id="hour" class="mask-time" />'
								)
							).
							write_html('tr', '',
								write_html('td', '',
									write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['till'])
								).
								write_html('td', '', 
									'<input type="text" size="8" name="till" id="till" class="mask-time" />'
								)
							)
						)
					).
					write_html('fieldset', '',
						write_html('legend', '', $lang['out_by']).
						write_html('table', 'border="0" cellspacing="0"',
							write_html('tr', '',
								write_html('td', 'width="85"',
									write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['out_by'])
								).
								write_html('td', '', 
									write_html_select('name="out_by" id="out_by" class="combobox"', array('bus'=>$lang['bus'], 'gate'=>$lang['gate']), '')
								)
							).
							write_html('tr', '',
								write_html('td', '',
									write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['person_charge'])
								).
								write_html('td', '', 
									write_html('select', 'name="pers" class="combobox"', 
										write_html('option', 'value="father"', $lang['father']).
										write_html('option', 'value="mother"', $lang['mother']).
										write_html('option', 'value="resp"', $lang['resp']).
										write_html('option', 'value="others"', $lang['others'])
									)
								)
							)
						)
					).
					write_html('fieldset', '',
						write_html('legend', '', $lang['comments']).
						write_html('textarea', 'name="comments"', '')
					)
				).
				write_html('td', 'valign="top"',
					write_html('div', 'class="toolbox"',
						write_html('a', 'action="insertStdPermission"', write_icon('plus').$lang['by_student']).
						write_html('a', 'action="insertClassPermis"', write_icon('plus').$lang['by_class'])
					).
					write_html('fieldset', '',
						write_html('legend', '', $lang['students']).
						write_html('table', 'class="result" id="permission_std_table"', '')
					)
				)
			)
		)
	);
	exit;
}

// Permission table
$day = isset($_GET['day']) ? dateToUnix($_GET['day']) : mktime(0,0,0, date('m'), date('d'), date('Y'));
$sql_per = "SELECT * FROM out_permis WHERE day=$day";
$query_per = do_query_resource( $sql_per, DB_year);
$trs = array();
while($row_per = mysql_fetch_assoc($query_per)){
	$student = new Students($row_per['std_id']);
	$pers = (in_array($row_per['pers'], array('father', 'mother'))) ? getParentNameFromStdId($row_per['pers'], $row_per['std_id']).' ('.$lang[$row_per['pers']].')' : $row_per['pers'].' ('.$lang['other'].')';
	$trs[] = write_html('tr', '',
		write_html('td', 'class="unprintable" style="text-align:center"',
			write_html('button', 'module="students" std_id="'.$student->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
		).
		write_html('td', '', $student->getName()).
		write_html('td', '', ($student->getClass() != false ? $student->getClass()->getName() :'')).		
		write_html('td', '', $lang[$row_per['out_by']]).
		write_html('td', '', ($row_per['hour'] != '' ? unixToTime($row_per['hour']) : '&nbsp;' )).
		write_html('td', '', ($row_per['till'] != '' ? unixToTime($row_per['till']) : $lang['end_of_day'] )).
		write_html('td', '', $pers)	.
		write_html('td', '', $student->getBus()).
		write_html('td', 'class="unprintable"',
			($row_per['comments'] != '' ?
				write_html('button', 'type="button" class="ui-state-default hoverable circle_button" onclick="addPermisComments('. $row_per['id'].', \''.addslashes($row_per['comments']).'\')"',  write_icon('pencil')).' '.
				write_html('span', 'title="'.$row_per['comments'].'"', $row_per['comments'])
			:
			write_html('button', 'class="ui-state-default hoverable circle_button" onclick="addPermisComments('. $row_per['id'].')"',  write_icon('plus'))
			)
		).
		write_html('td', 'class="unprintable" style="text-align:center"',
			write_html('button', 'class="ui-state-default hoverable circle_button" action="deletePer" absentid="'. $row_per['id'].'"',  write_icon('closethick'))
		)
	);
}


$permisson_table =  write_html('div', 'class="toolbox"',
	write_html('a', 'onclick="openPermissionForm()"', write_icon('plus').$lang['add']).
	write_html('a', 'rel="#permission_div" class="print_but"', write_icon('print').$lang['print'])
).
write_html('form', 'class="ui-corner-all ui-state-highlight" style="margin:5px"',
	write_html('table', 'cellspacing="0" border="0"', 
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" ', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['date'])
			).
			write_html('td', 'valign="middel" colspan="2"',
				'<input type="text" class="datepicker mask-date" id="permission_day"  value="'.unixToDate($day).'" />'
			)
		)
	)
).
write_html('table', 'class="tablesorter"', 
	write_html('thead', '',
		write_html('tr', '',
			write_html('th', 'class="unprintable" width="20"', '&nbsp;').
			write_html('th', '', $lang['name']).
			write_html('th', '', $lang['class']).
			write_html('th', '',  $lang['out_by']).
			write_html('th', '',  $lang['time']).
			write_html('th', '',  $lang['till']).
			write_html('th', '',  $lang['person_charge']).
			write_html('th', '',  $lang['bus']).
			write_html('th', 'class="unprintable"', $lang['comments']).
			write_html('th', 'class="unprintable" width="20"', '&nbsp;')
		)
	).
	write_html('tbody', '',
		implode('', $trs)
	)
);

if(isset($_GET['perres'])){
	echo implode('', $trs);
	exit;
}
echo  write_html('div', ' class="ui-widget-header ui-corner-top" ', 
	write_html('div', 'class="reverse_align title_wihte"', $lang['permition_out'])
).
write_html('div', 'id="permission_div" class="ui-widget-content ui-corner-bottom"', 
	$permisson_table
);

?>                       
