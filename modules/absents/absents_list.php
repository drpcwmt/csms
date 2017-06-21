<?php 
## absent List'
if(isset($con)){
	$absent_list_table = '';
	if(isset($_GET['t'])){
		$term_id =  $_GET['t'];
		$selected = 't='.$term_id;
		if($term_id != 0){
			$term = do_query("SELECT title, begin_date, end_date FROM terms WHERE id=$term_id", DB_year);
			$begin_date = $term['begin_date'];
			$end_date = $term['end_date'];
		} else {
			$begin_date = getYearSetting('begin_date');
			$end_date = getYearSetting('end_date');
		}
	} elseif(isset($_GET['m'])){
		$month = $_GET['m'];
		$selected = "m=$month";
		$s_year = date( 'Y', getYearSetting('begin_date'));
		if($month < 7 ) {$s_year++;}
		$begin_date = mktime(0,0,0, $month, 1, $s_year);
		$end_date = mktime(0,0,0, $month+1 , 1, $s_year);
	} else {
		$begin_date = getYearSetting('begin_date');
		$end_date = getYearSetting('end_date');
	}
	
	$stds = array();
	if($con=='std'){
		$stds[] = $con_id;
	} else {
		$stds = getStdIds($con, $con_id);
	}
	
	if($stds != false && count($stds) > 0){
		$sql ="SELECT COUNT(day) AS count, con_id  FROM absents WHERE (con_id=".implode(' OR con_id=', $stds).")
		AND day>=$begin_date AND day <=$end_date
		GROUP BY con_id ASC";
		$trs = array();
		$absents = do_query_resource($sql, DB_year);
		if(mysql_num_rows($absents) > 0){
			while( $row_absent = mysql_fetch_assoc($absents)){
				$student = new Students($row_absent['con_id']);
				
				$trs[] = write_html('tr', '',
					($editable ?
						write_html('td', 'class="unprintable" style="text-align:center"',
							write_html('button', 'module="students" std_id="'.$row_absent['con_id'].'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
						)
					: '').
					($con != 'student' ? 
						write_html('td', '', $student->getName()).
						write_html('td', '', $student->getClass()->getName())
					: '').
					write_html('td', 'align="center"', $row_absent['count'])
				);
			}
		}
	
		$absent_list_table = write_html('table', 'class="tablesorter"',
			write_html('thead', '',
				write_html('tr', '',
					($editable ? 
						write_html('th', 'class="unprintable" style="background-image: none" width="20"', '&nbsp;')
					:"").
					($con != 'student' ? 
						write_html('th', 'width="250"',$lang['name']).
						write_html('th', 'width="100"',$lang['class'])
					: '').
					write_html('th', 'width="60"',$lang['count'])
				)
			).
			write_html('tbody', '', implode('', $trs))
		);
	}
} else {
	
	/***************** Toolbox & Form ***********************/
	//pick up defaukt class
	$all_classes = Classes::getList();
	$class_no=0;
	$terms_arr = array();
	$terms_arr['0'] = $lang['all'];
	$classes = array();
	foreach($all_classes as $class){
		$classes[$class->id] = $class->getName();
		if($class_no == 0 ){ 
			$cur_class_id = $class->id;
			$class_no++;
		}
	}

	$terms = Terms::getTermsByCon(isset($con) ? $con : 'class', isset($con_id) ? $con_id : $cur_class_id);
	if(count($terms) > 0 && $terms != false){
		foreach($terms as $term){
			$terms_arr[$term->id] = $term->getName();
		}
	}
		
	
	$absent_list_form =  write_html('form', 'id="absent_list_form" class="ui-corner-all ui-state-highlight"', 
		write_html('table', 'border="0" cellspacing="0"', 
			(!isset($con)?
				'<input type="hidden" name="con" value="class" />'.
				write_html('tr', '',
					write_html('td', 'width="120"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['class'])
					).
					write_html('td', '', 
						write_html_select('id="absent_list_classes" name="con_id" class="combobox" update="reloadTerms"', $classes, $cur_class_id)
					)
				)
			:
				'<input type="hidden" name="con" value="'.$con.'" />'.
				'<input type="hidden" name="con_id" value="'.$con_id.'" />'
			).
			write_html('tr', '',
				write_html('td', '',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['term'])
				).
				write_html('td', '', 
					write_html_select('id="absent_list_terms" update="reloadTerms" class="combobox"', array_merge($terms_arr, getPassedMonths()) , isset($selected)? $selected : '0').
					write_html('button', 'type="button" class="hoverable ui-corner-all ui-state-default" style="margin:0px 50px" action="submitAbsentListSearch"', write_icon('search').$lang['search'])
				)
			)
		)
	);
}