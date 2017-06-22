<?php
## Absents by lesson

$abs_by_lesson_toolbox=  write_html('div', 'class="toolbox"',
	write_html('a', 'onclick="insertStdAbsentByLesson()"', write_icon('plus').$lang['by_student']).
	write_html('a', 'onclick="insertClassAbsentByLesson()"', write_icon('plus').$lang['by_class']).
	write_html('a', 'rel="#absent_by_lesson_tab" class="print_but"', write_icon('print').$lang['print'])
);


$abs_date = (isset($_POST['day']) && $_POST['day'] !='') ?  dateToUnix($_POST['day']) : mktime(0,0,0,date('m'),date('d'), date('Y'));
$sql_absent = "SELECT * FROM absents_bylesson WHERE date=$abs_date ";
if(isset($_SESSION['cur_class'])){
	$stds = getStudentIdsByClass($_SESSION['cur_class']);
	$sql_absent .= "AND ( std_id=".implode(' OR std_id=', $stds).")";
}
$query_absent = do_query_resource( $sql_absent, DB_year);
$abs_by_lesson_total = mysql_num_rows($query_absent);
$trs = array();
if($abs_by_lesson_total > 0 ){
	while($row_absent = mysql_fetch_assoc($query_absent)){
		$trs[] = write_html('tr', '',
			write_html('td', 'class="unprintable" style="text-align:center"',
				write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" onclick="openStudentInfos('. $row_absent['con_id'].')"',  write_icon('person'))
			).
			write_html('td', '', getStudentNameById($row_absent['con_id'])).
			write_html('td', '', getClassNameById(getClassIdFromStdId($row_absent['con_id']))).
			write_html('td', '', getStudentNameById($row_absent['con_id'])).
			write_html('td', '', $row_absent['lesson_no']).
			write_html('td', '',
				($row_absent['comments'] != '' ?
					write_html('button', 'type="button" class="ui-state-default hoverable" style="width:24px; height:24px" onclick="addAbsComments('. $row_absent['id'].', \''.$row_absent['comments'].'\')"',  write_icon('pencil')).' '.
					write_html('span', ' title="'.$row_absent['comments'].'"', $row_absent['comments'])
				:
				write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" onclick="addAbsComments('. $row_absent['id'].')"',  write_icon('plus'))
				)
			).
			write_html('td', 'style="text-align:center" class="unprintable"',
				write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" type="button" onclick="deleteAbs('. $row_absent['id'].')"',  write_icon('closethick'))
			)
		);
	}
}


$abs_by_lesson_list = write_html('form', 'class="ui-corner-all ui-state-highlight"', 
	write_html('table', 'border="0" cellspacing="0"', 
		write_html('tr', '',
			write_html('td', 'width="120"', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['date'])
			).
			write_html('td', '', 
				'<input type="text" class="datepicker mask-date" id="absent_by_lesson_cur_date" name="day" value="'.unixToDate($abs_date).'" />')
		).
		write_html('tr', '',
			write_html('td', '',
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['total'])
			).
			write_html('td', '', 
				'<input type="text" disabled="disabled" value="'.$abs_by_lesson_total.'" />'
			)
		)
	)
).
write_html('table', 'class="tablesorter"',
	write_html('thead', '',
		write_html('tr', '',
			write_html('th', 'width="20" style="background-image:none" class="unprintable"', '&nbsp').
			write_html('th', '', $lang['name']).
			write_html('th', '', $lang['class']).
			write_html('th', '', $lang['material']).
			write_html('th', '', $lang['lesson_no']).
			write_html('th', '', $lang['comments']).
			write_html('th', 'width="20" style="background-image:none" class="unprintable"', '&nbsp')
		)
	).
	write_html('tbody', '', implode('', $trs))
);

?> 	