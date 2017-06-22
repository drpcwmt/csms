<?php
## absents_daily_list
/***************** POSTS ********************/
// add daily absents
if(isset($_GET['add'])){
	setJsonHeader();
	if(getPrvlg('att_absent_edit')){
		$day = dateToUnix($_POST['date']);
		foreach($_POST['std_id'] as $id){
			do_query_edit( "INSERT INTO absents (con_id, day) VALUES ($id, $day)", DB_year);
			//sendAbsentMsg($id, $day);
		}
		echo json_encode(array('error' => ''));
	} else {
		echo json_encode(array('error' => $lang['no_privilege'])); 
	}
	exit;
}

if(isset($_GET['update'])){
	setJsonHeader();
	$absent_id = $_POST['id'];
	if(UpdateRowInTable('absents', $_POST, "id='$absent_id'", DB_year)){
		echo "{\"error\" : \"\", \"id\" : \"$absent_id\"}";
	} else {
		echo "{\"error\" : \"Error while updating\"}";
	}
	exit;	
}

function getStdAbsByDay($std_id, $day){
	$arr = array();
	$sql = "SELECT con_id FROM absents WHERE day = $day AND con_id=$std_id";
	$sql_abs = do_query_obj( $sql, DB_year);
	if($sql_abs != false && $sql_abs->con_id != ''){
		return true;
	} else {
		return false;	
	}
}

$absent_daily_toolbox = write_html('div', 'class="toolbox"',
	write_html('a', 'onclick="insertStdAbsent()"', write_icon('plus').$lang['by_student']).
	write_html('a', 'onclick="insertClassAbsent()"', write_icon('plusthick').$lang['by_class']).
	write_html('a', 'rel="#absents_daily_tab" class="print_but"', write_icon('print').$lang['print']).
	write_html('a', 'action="exportTable" rel="#absents_daily_tab"',write_icon('disk'). $lang['export'])
);

$abs_date = (isset($_GET['day']) && $_GET['day'] !='') ?  dateToUnix($_GET['day']) : mktime(0,0,0,date('m'),date('d'), date('Y'));
$sql_absent = "SELECT * FROM absents WHERE day=$abs_date ";

if(isset($_SESSION['cur_class'])){
	$cur_class = new Classes($_SESSION['cur_class']);
	$stds = $cur_class->getStudents();
	foreach($stds as $st){
		$stds_ids[] = $st->id;
	}
	$sql_absent .= "AND ( con_id=".implode(' OR con_id=', $stds_ids).")";
	$all_classes = array($cur_class);
} else {
	$all_classes = Classes::getList();
}

$query_absent = do_query_array( $sql_absent, DB_year);
$abs_total = count($query_absent);
$trs = array();
if($abs_total > 0 ){
	foreach($query_absent as $absent){
		$i = 0;
		$d =  0;
		$longAbs = 0;
		while($i < 3){
			if(isset($_GET['day']) && $_GET['day'] !=''){
				$day = datetoUnix($_GET['day']) - ($d * 86400);
			} else {
				$day = mktime(0,0,0, date('m'), (date('d') - $d), date('Y'));
			}
			if(!in_array(date('D', $day) , array('Fri','Sat')) && Calendars::chkHoliday($day, 'student', $absent->con_id)==false){
				$i++;
				if( getStdAbsByDay($absent->con_id, $day)){
					$longAbs++;
				}
			}
			$d++;
		}
		$longAbs_html=  $longAbs >= 3 ? '<span class="ui-icon ui-icon-check"  title="+3"></span>' : '&nbsp;';
		
		$student = new Students($absent->con_id);
		if(in_array($student->getClass(), $all_classes)){
			$trs[] = write_html('tr', '',
				write_html('td', 'class="unprintable" style="text-align:center"',
					write_html('button', 'module="students" std_id="'.$absent->con_id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
				).
				write_html('td', '', $student->getName()).
				write_html('td', '', $student->getClass()->getName()).
				write_html('td', ' style="text-align:center"', 
					'<input type="checkbox" name="justify" '.($absent->justify == 1 ? 'checked="checked"' : '').' onclick="addjustify(this,'.$absent->id.')" value="1"/>'
				).
				write_html('td', 'style="text-align:center"', 
					'<input type="checkbox" name="ill" '.($absent->ill == 1 ? 'checked="checked"' : '').' onclick="addill(this,'.$absent->id.')" value="1"/>'
				).
				write_html('td', 'style="text-align:center"',
					$longAbs_html
				).
				write_html('td', '',
					($absent->comments != '' ?
						write_html('button', 'type="button" class="ui-state-default hoverable circle_button"  onclick="addAbsComments('. $absent->id.', \''.$absent->comments.'\')"',  write_icon('pencil')).' '.
						write_html('span', ' title="'.$absent->comments.'"', $absent->comments)
					:
					write_html('button', 'class="ui-state-default hoverable circle_button"  onclick="addAbsComments('. $absent->id.')"',  write_icon('plus'))
					)
				).
				write_html('td', 'style="text-align:center"', $student->getBus()).
				write_html('td', 'style="text-align:center" class="unprintable"',
					write_html('button', 'class="ui-state-default hoverable circle_button" type="button" onclick="deleteAbs('. $absent->id.')"',  write_icon('closethick'))
				)
			);
		}
	}
}

$absents_daily_list = write_html('h2', 'class="title showforprint hidden" style="float:left"',$lang['absent_lists']).
write_html('form', 'class="ui-corner-all ui-state-highlight" style="margin:5px"',
	write_html('table', 'cellspacing="0" border="0"', 
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" ', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['date'])
			).
			write_html('td', 'valign="middel" colspan="2"',
				'<input type="text" class="datepicker mask-date" id="absent_cur_date" name="day" value="'.unixToDate($abs_date).'" />'.
				write_html('button', 'type="button" class="hoverable ui-corner-all ui-state-default" style="margin:0px 30px" action="reloadDailyAbsent"', write_icon('search').$lang['search'])

			)
		).
		write_html('tr', '',
			write_html('td', 'width="120" valign="middel" ', 
				write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['total'])
			).
			write_html('td', 'valign="middel" colspan="2"',
				'<input type="text" disabled="disabled" value="'.$abs_total.'" />'
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
			write_html('th', 'width="60"', $lang['justify']).
			write_html('th', 'width="60"', $lang['ill']).
			write_html('th', 'width="60"', ' +3 ').
			write_html('th', '', $lang['comments']).
			write_html('th', 'width="60"', $lang['bus']).
			write_html('th', 'width="20" style="background-image:none" class="unprintable"', '&nbsp')
		)
	).
	write_html('tbody', '', implode('', $trs))
);


?>