<?php
## Absents rate

if(isset($_SESSION['cur_class'])){
	$con = 'class';
	$con_id = $_SESSION['cur_class'];
}

if(isset($_GET['con_id'])&& trim($_GET['con_id'])!= ''){
	$con = $_GET['con'];
	$con_id = $_GET['con_id'];
}

if(isset($con_id)){
	$cur_class = new Classes($con_id);
	$stds = $cur_class->getStudents();

	$trs = array();
	if(isset($_GET['t'])){
		$term_id =  $_GET['t'];
		$selected = 't='.$term_id;
		if($term_id != 0){
			$term = do_query("SELECT title, begin_date, end_date FROM terms WHERE id=$term_id", DB_year);
			$begin_date = $term['begin_date'];
			$end_date = $term['end_date'];
			$title = $lang['term']. ': '.$term['title'];
		} else {
			$title =  $lang['months_0'];
			$begin_date = getYearSetting('begin_date');
			$end_date = getYearSetting('end_date');
		}
	} elseif(isset($_GET['m'])){
		$month = $_GET['m'];
		$selected = "m=$month";
		$cur_year = date('Y');
		if(time() < mktime(0,0,0, $month, 1, $cur_year)){
			$s_year = date('Y') - 1;
		} else{
			$s_year = date('Y');
		}
		$title =  $lang['month'].': '.$lang["months_$month"];
		$begin_date = mktime(0,0,0, $month, 1, $s_year);
		$end_date = mktime(0,0,0, $month+1 , 1, $s_year);
	} else {		
		$begin_date = getYearSetting('begin_date');
		$end_date = getYearSetting('end_date');
	}
	
	
	if(count($stds)> 0){
		$calendar = new Calendars($con, $con_id);
		$TotalWorkingDay = round($calendar->getTotalWorkingDay($begin_date, $end_date));
		$class_total = 0; 
		foreach($stds as $student){
			$std_id = $student->id;
			$total_std = getStdTotalAbs('', $std_id);
			$trs[]= write_html('tr', '',
				write_html('td', 'class="unprintable"  align="center"',
					write_html('button', 'module="students" std_id="'.$student->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
				).
				write_html('td', '', $student->getName()).
				write_html('td', '', $total_std).
				write_html('td', '', round(($total_std * 100 / ($TotalWorkingDay > 0 ? $TotalWorkingDay:1)),2).' % ').
				write_html('td', '', round(100 - ($total_std * 100 / ($TotalWorkingDay >0?$TotalWorkingDay:1)),2).' % ')
			);
			$class_total = $class_total + $total_std;
		}
		$class_total_workin = ($TotalWorkingDay * getStdNo($con, $con_id) );
		$class_per = round( ($class_total * 100 /  ($class_total_workin>0?$class_total_workin:1)) ,2);
		/*$trs[]= write_html('tr', '',
			write_html('td', 'class="unprintable"  align="center"', '&nbsp;').
			write_html('td', 'class="reverse_align"', write_html('strong', '', $lang['total'])).
			write_html('td', '', write_html('strong', '', $class_total)).
			write_html('td', '', write_html('strong', '', $class_per.'%')). 
			write_html('td', '', write_html('strong', '', (100 - $class_per).' %'))
		);*/
	
	}

	$abs_rate_table = write_html('table', 'class="tablesorter"',
		write_html('thead', '',
			write_html('tr', '',
				write_html('th', 'class="unprintable" style="background-image:none;" width="20"', '&nbsp;').
				write_html('th', '', $lang['name']).
				write_html('th', 'width="60"',$lang['total']).
				write_html('th', 'width="60"',$lang['off_rate']).
				write_html('th', 'width="60"',$lang['on_rate'])
			)
		).
		write_html('tbody', '', implode('', $trs)).
		write_html('tfoot','',
			write_html('tr', '',
				write_html('th', 'class="unprintable"  align="center"', '&nbsp;').
				write_html('th', 'class="reverse_align"', write_html('strong', '', $lang['total'])).
				write_html('th', '', write_html('strong', '', $class_total)).
				write_html('th', '', write_html('strong', '', $class_per.'%')). 
				write_html('th', '', write_html('strong', '', (100 - $class_per).' %'))
			)
		)
	);
	
	$abs_rate_layout = write_html('table', '',
		write_html('tr', '',
			write_html('td', 'valign="top"',
				$abs_rate_table
			).
			write_html('td', 'valign="top" width="300"',
				write_html('h3', 'class="title"', getAnyNameById($con, $con_id)).
				write_html('h3', 'class="title"', $title).
				write_html('h4', '', $lang['total_workin_day'].': '.$TotalWorkingDay).
				write_html('div', 'id="chartDiv_absrate"', 
					'<img class="hand" onclick="enlargeChart(this)" src="index.php?module=absents&chart&con='.$con.'&con_id='.$con_id.'&'.time().'.png"  width="320" height="120" />'
				)
			)
		)
	);
}

$all_classes = classes::getList();
$class_no=0;
foreach($all_classes as $class){
	$classes[$class->id] = $class->getName();
	if($class_no == 0 ){ 
		$cur_class_id = $class->id;
		$class_no++;
	}
}
$terms_arr = array();
$terms = Terms::getTermsByCon(isset($con) ? $con : 'class', isset($con_id) ? $con_id : $cur_class_id);
if(count($terms) > 0 && $terms != false){
	foreach($terms as $term){
		$terms_arr[$term->id] = $term->getName();
	}
}
$absent_rate_form =  write_html('form', 'id="absent_rate_form" class="ui-corner-all ui-state-highlight unprintable"', 
	write_html('table', 'border="0" cellspacing="0"', 
		(!isset($_GET['con'])?
			'<input type="hidden" name="con" value="class" />'.
			write_html('tr', '',
				write_html('td', 'width="120"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['class'])
				).
				write_html('td', '', 
					write_html_select('id="absent_rate_classes" name="con_id" class="combobox" onchange="reloadTerms(this.value, \'#absent_rate_terms\')"', $classes, $cur_class_id)
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
				write_html_select('id="absent_rate_terms" class="combobox"', array_merge($terms_arr, getPassedMonths()) , isset($selected)? $selected : '0').
				write_html('button', 'type="button" class="hoverable ui-corner-all" style="margin:0px 50px" onclick="submitAbsentRateSearch()"', write_icon('search').$lang['search'])
			)
		)
	)
);
?>