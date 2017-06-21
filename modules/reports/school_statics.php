<?php
## School statics
if(isset($_GET['etab_id']) && $_GET['etab_id'] != 0){
	$req_etab_id = safeGet($_GET['etab_id']);
	$etabs = array(new Etabs($req_etab_id));	
} else {
	$req_etab_id =0;
	$etabs = Etabs::getList();	
}

$count_all_std = 0;
$count_all_class = 0;
$count_all_female = 0;
$count_all_male = 0;
$count_all_muslim = 0;
$count_all_chistians = 0;
$count_all_egyptians = 0;
$count_all_forgeins = 0;
$count_all_redouble = 0;
$count_all_new = 0;
$count_all_waiting = 0;
$count_all_suspended = 0;
$trs = array();
$serial = 1;
$pre_sql = "SELECT id FROM student_data WHERE ";
foreach($etabs as $etab){
	$std_by_etab = 0;
	$class_by_etab = 0;
	$levels = $etab->getLevelList(); 
	foreach($levels as $level){
		$level_id = $level->id;
		$level_name = $level->getName();
		
		$count_class = count($level->getClassList());
		
		$count_waiting = count(do_query_array("SELECT std_id FROM waiting_list WHERE level_id=$level_id", DB_student));
		$count_all_waiting = $count_all_waiting + $count_waiting;

		$student_list = new StudentsList('level', $level->id);
		$student_list->stats = array('1', '3');
		$students= $student_list->getStudents();
		$count_std= $student_list->getCount();
		$stds = array();
		$count_new= 0;
		$count_redouble = 0;
		$count_christ= 0;
		$count_muslim = 0;
		$count_mal= 0;
		$count_female = 0;
		$count_suspended= 0;
		$count_egypt = 0;
		$count_forgn = 0;
		foreach($students as $student){
			if($student->getRegStatus() == 1){
				$count_new++;
			} else {
				$count_redouble++;
			}
			if($student->religion == 1){
				$count_muslim++;
			} else {
				$count_christ++;
			}
			if($student->sex == 1){
				$count_mal++;
			} else {
				$count_female++;
			}
			
			if($student->status == 3){
				$count_suspended++;
			}
			$stds[] = $student->id;
		}
		$where_stat = array();
		foreach($student_list->stats as $stat){
			$where_stat[] =" status=$stat";
		}
		$stat_sql = " (".implode(' OR ', $where_stat).")";		
		if(count($stds) > 0){
			$std_sql = '(id='.implode($stds, ' OR id=').')';
			
			$count_egypt = count(do_query_array($pre_sql. $std_sql." AND $stat_sql AND (nationality LIKE '%egyptian%' OR nationality_ar LIKE '%مصري%'  OR nationality_ar LIKE '%مصرى%')", DB_student));
			$count_forgn = count(do_query_array($pre_sql. $std_sql."AND $stat_sql AND (nationality NOT LIKE '%egyptian%' AND nationality_ar NOT LIKE '%مصري%')", DB_student));
			
		}
		$count_all_std = $count_all_std + $count_std;
		$count_all_class = $count_all_class + $count_class;
		$count_all_female = $count_all_female + $count_female;
		$count_all_male = $count_all_male + $count_mal;
		$count_all_muslim = $count_all_muslim + $count_muslim;
		$count_all_chistians = $count_all_chistians + $count_christ;
		$count_all_egyptians = $count_all_egyptians + $count_egypt;
		$count_all_forgeins = $count_all_forgeins + $count_forgn;
		$count_all_redouble = $count_all_redouble + $count_redouble ;
		$count_all_new = $count_all_new + $count_new;
		$count_all_suspended = $count_all_suspended + $count_suspended;
		
		$trs[] = write_html('tr', '',
			write_html('td', '', $serial).
			write_html('td', '', $level->getName()).
			write_html('td', '', $count_class).
			write_html('td', 'class="sex" ', $count_mal).
			write_html('td', 'class="sex" ', $count_female).
			write_html('td', 'class="religion" ', $count_muslim).
			write_html('td', 'class="religion" ', $count_christ).
			write_html('td', 'class="nationality" ', $count_egypt).
			write_html('td', 'class="nationality" ', $count_forgn).
			write_html('td', 'class="register_stat" ', $count_new).
			write_html('td', 'class="register_stat" ', $count_redouble).
			write_html('td', '', count($stds)).
			write_html('td', 'class="suspended" ', $count_suspended).
			write_html('td', 'class="waiting" ', $count_waiting)
		);
		$serial = $serial+1;
	}
}

$thead = write_html('thead', '', 
	write_html('tr', '',
		write_html('th', 'style="background-image:none"', $lang['ser']).
		write_html('th', 'style="background-image:none"', '&nbsp;').
		write_html('th', 'style="background-image:none"', $lang['count_classes']).
		write_html('th', 'class="sex" style="background-image:none" colspan="2"', $lang['sex']).
		write_html('th', 'class="religion" style="background-image:none" colspan="2"', $lang['religion']).
		write_html('th', 'class="nationality" style="background-image:none" colspan="2"', $lang['nationality']).
		write_html('th', 'class="register_stat" style="background-image:none" colspan="2"', $lang['register_stat']).
		write_html('th', 'style="background-image:none"', '&nbsp;').
		write_html('th', 'class="suspended" style="background-image:none"', '&nbsp;').
		write_html('th', 'class="waiting" style="background-image:none"', '&nbsp;')
	).
	write_html('tr', '',
		write_html('th', 'width="20"', '&nbsp;').
		write_html('th', 'rawspan="2"', $lang['level']).
		write_html('th', 'width="50"', '&nbsp;').
		write_html('th', 'class="sex" width="50"', $lang['male']).
		write_html('th', 'class="sex" width="50"', $lang['female']).
		write_html('th', 'class="religion" width="50"', $lang['muslim']).
		write_html('th', 'class="religion" width="50"', $lang['christian']).
		write_html('th', 'class="nationality" width="50"', $lang['egyptian']).
		write_html('th', 'class="nationality" width="50"', $lang['forgein']).
		write_html('th', 'class="register_stat" width="50"', $lang['result_new']).
		write_html('th', 'class="register_stat" width="50"', $lang['result_redouble']).
		write_html('th', 'width="50"', $lang['count_student']).
		write_html('th', 'class="suspended" style="background-image:none"', $lang['suspended']).
		write_html('th', 'class="waiting" style="background-image:none"', $lang['waiting_list'])
	)
);
$etabs_arr = array('0' => $lang['all']);
$all_etabs = Etabs::getList();	
foreach($all_etabs as $etab){
	$etabs_arr[$etab->id] = $etab->getName();
}

$school_static = write_html('form', 'style="padding:3px" class="unprintable ui-corner-all ui-state-highlight optional"',
	((!isset($_GET['toolbox']) || $_GET['toolbox'] != 'false') ? 
		write_html('table', 'width="100%" border="0" cellspacing="0" class="optional"',
			write_html('tr', '',
				write_html('td', ' width="120" valign="middel"', 	
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['etab'])
				).
				write_html('td', '',
					write_html_select( 'id="static_con" update="openSchoolStatic" class="combobox"', $etabs_arr, $req_etab_id)
				)
			).
			write_html('tr', '',
				write_html('td', ' width="120" valign="middel"', 	
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['view'])
				).
				write_html('td', '',
					write_html('span', 'class="buttonSet"',
						'<input type="checkbox" id="static_chk_1" checked="checked" />'.write_html('label', 'for="static_chk_1"  onclick="toogleStaticElement(\'sex\')"', $lang['sex']).
						'<input type="checkbox" id="static_chk_2" checked="checked" />'.write_html('label', 'for="static_chk_2" onclick="toogleStaticElement(\'religion\')"', $lang['religion']).
						'<input type="checkbox" id="static_chk_3" checked="checked" />'.write_html('label', 'for="static_chk_3" onclick="toogleStaticElement(\'nationality\')"', $lang['nationality']).
						'<input type="checkbox" id="static_chk_4" checked="checked" />'.write_html('label', 'for="static_chk_4" onclick="toogleStaticElement(\'register_stat\')"', $lang['register_stat']).
						'<input type="checkbox" id="static_chk_5" checked="checked" />'.write_html('label', 'for="static_chk_5" onclick="toogleStaticElement(\'suspended\')"', $lang['suspended']).
						'<input type="checkbox" id="static_chk_6" checked="checked" />'.write_html('label', 'for="static_chk_6" onclick="toogleStaticElement(\'waiting\')"', $lang['waiting_list'])
					)
				)
			)
		)
	: '')
).
write_html('h2', '', $lang['school_static_title'].' '. $_SESSION['year'].'/'. ($_SESSION['year']+1)).
write_html('table', 'class="tablesorter" id="statics_table"', 
	$thead.
	write_html('tbody', '', implode($trs, '')).
	write_html('tfoot' ,'', 
		write_html('tr', '',
			write_html('th', 'width="20"', '&nbsp;').
			write_html('th', 'rawspan="2"', $lang['total']).
			write_html('th', 'width="50"', $count_all_class).
			write_html('th', 'class="sex" width="50"', $count_all_male).
			write_html('th', 'class="sex" width="50"', $count_all_female).
			write_html('th', 'class="religion" width="50"', $count_all_muslim).
			write_html('th', 'class="religion" width="50"', $count_all_chistians).
			write_html('th', 'class="nationality" width="50"', $count_all_egyptians).
			write_html('th', 'class="nationality" width="50"', $count_all_forgeins).
			write_html('th', 'class="register_stat" width="50"', $count_all_new).
			write_html('th', 'class="register_stat" width="50"', $count_all_redouble).
			write_html('th', 'width="50"', $count_all_std).
			write_html('th', 'class="suspended" width="50"', $count_all_suspended).
			write_html('th', 'class="waiting" width="50"', $count_all_waiting)
		)
	)
);

?>
