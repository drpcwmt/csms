<?php
## certificate template
## 2 Simester 4 quarter with final exam

if(!isset($terms[1])  || !isset($terms[2]) || !isset($terms[3]) || !isset($terms[4])){
	echo write_error($lang['incompatible_template']);
	exit;
}
	// THEAD
$th_Q1_work = round((getConValue('term', $terms[1]) - getConValue('exam', $finals[1])) *100/ getConValue('term', $terms[1]), 1) ;
$th_Q1_exam = getConValue('exam', $finals[1]) *100/ getConValue('term', $terms[1]);
$th_Q1_total = 100;
$th_Q2_work = getConValue('term', $terms[2]) - getConValue('exam', $finals[2]);
$th_Q2_exam = getConValue('exam', $finals[2]);
$th_SEM1_total = getConValue('term', $terms[1]) + getConValue('term', $terms[2]);

$th_Q3_work = round((getConValue('term', $terms[3]) - getConValue('exam', $finals[3])) *100/ getConValue('term', $terms[3]), 1) ;
$th_Q3_exam = getConValue('exam', $finals[3]) *100/ getConValue('term', $terms[3]);
$th_Q3_total = 100;
$th_Q4_work = getConValue('term', $terms[4]) - getConValue('exam', $finals[4]);
$th_Q4_exam = getConValue('exam', $finals[4]);
$th_SEM2_total = getConValue('term', $terms[3]) + getConValue('term', $terms[4]);


$main_table_head = write_html('tr', '',
	write_html('th', 'width="16%" rowspan="3" ', '&nbsp;').
	write_html('th', 'width="36%" colspan="9" class="totals_td" style="padding:5px; border-bottom: solid 1px #555555;"' , 'Semester 1').
	write_html('th', 'width="36%" colspan="9" class="totals_td" style="padding:5px; border-bottom: solid 1px #555555;"' , 'Semester 2').
	write_html('th', 'width="6%" rowspan="3" class="totals_td"', 'Final<br />100%').
	write_html('th', 'width="6%" rowspan="3" class="grad" style="width:50px" ', 'Final Grading')
).
write_html('tr', '',
	write_html('th', 'width="4%" colspan="5" class="border-bottom"', 'Q1').
	write_html('th', 'width="4%" class="border-bottom"', 'Q2').
	write_html('th', 'width="4%" rowspan="2" class="subtotal"', 'Exam '. $th_Q2_exam.'%').
	write_html('th', 'width="4%" rowspan="2" class="grad"', 'Grad').
	write_html('th', 'width="4%" rowspan="2" class="totals_td"', 'Total '. $th_SEM1_total.'%').
	
	write_html('th', 'colspan="5" class="border-bottom"', 'Q3').
	write_html('th', 'width="4%" class="border-bottom"', 'Q4').
	write_html('th', 'width="4%" rowspan="2" class="subtotal"', 'Exam '. $th_Q4_exam.'%').
	write_html('th', 'width="4%" rowspan="2" class="grad"', 'Grad').
	write_html('th', 'width="4%" rowspan="2" class="totals_td"', 'Total '. $th_SEM2_total.'%')
).
write_html('tr', '',
	write_html('th', 'width="4%" class="th_unit"', 'Work '.$th_Q1_work.'%').
	write_html('th', 'width="4%"', 'Exam '.$th_Q1_exam.'%').
	write_html('th', 'width="4%"', 'Total '.$th_Q1_total.'%').
	write_html('th', 'width="4%" class="grad"', 'Grad').
	write_html('th', 'width="4%" class="subtotal"', 'Part. '. getConValue('term', $terms[1]).'%').
	write_html('th', 'width="4%" class="subtotal"', 'Work '.$th_Q2_work.'%').
	write_html('th', 'width="4%"', 'Work '.$th_Q3_work.'%').
	write_html('th', 'width="4%"', 'Exam '.$th_Q3_exam.'%').
	write_html('th', 'width="4%"', 'Total '.$th_Q3_total.'%').
	write_html('th', 'width="4%" class="grad"', 'Grad').
	write_html('th', 'width="4%" class="subtotal"', 'Part. '.getConValue('term', $terms[3]).'%').
	write_html('th', 'width="4%" class="subtotal"', 'Work '.$th_Q4_work.'%')
);

/************************ Tbody ****************************/
function getServiceResults($std_id, $service_id){
	$level_id = $GLOBALS['level_id'];
	$t_final = array();
	$t_tot = array();
	$t_grad = array();
	$t_max = array();
	$t_final_perc = array();
	$terms = getTerms('level', $level_id);
	$term_no = 0;
	while($term = mysql_fetch_assoc($terms)){
		$term_no++;
		$term_id = $term['id'];
		$term_value = getConValue('term', $term_id);
		
		$final = do_query("SELECT * FROM exams , exams_results
		WHERE exams.term_id=$term_id 
		AND exams.service=$service_id 
		AND exams_results.exam_id=exams.id
		AND exams_results.std_id=$std_id
		ORDER BY exams.exam_no DESC LIMIT 1", DB_year);
	//	echo $final['id'];
		if($final['id'] != ''){
			$final_exam_id = $final['id'];
			$final_result = do_query("SELECT results FROM exams_results WHERE exam_id=$final_exam_id AND std_id=$std_id", DB_year);
			$this_final = $final_result['results'] != -1 ? $final_result['results'] : 0;
			$t_final_exam_value = $final['value'] ;
			$t_final[$term_no] = ($this_final / $final['max']) * $t_final_exam_value;
			$t_final_perc[$term_no] = $this_final * ($t_final_exam_value/$term_value) *100/ $final['max'];
			
		} else {
			$t_final[$term_no] = 0;
			$final_exam_id = NULL;
		}
		
		// all term results
		$all_r = do_query("SELECT SUM(exams_results.results) FROM exams, exams_results WHERE 
			exams.term_id = $term_id
			AND exams.service=$service_id
			AND exams_results.exam_id=exams.id
			AND exams_results.std_id=$std_id
			AND exams_results.results !='-1'
			AND exams.id!='$final_exam_id'", DB_year);
		$this_result = $all_r !=false && $all_r['SUM(exams_results.results)']!='' ? $all_r['SUM(exams_results.results)'] : 0;
		//Max
		$max = do_query("SELECT SUM(exams.max), SUM(exams.value) FROM exams, exams_results WHERE 
			exams.term_id = $term_id
			AND exams_results.exam_id=exams.id
			AND exams.service=$service_id
			AND exams_results.exam_id=exams.id
			AND exams_results.std_id=$std_id
			AND exams.id!='$final_exam_id'", DB_year);
		$this_max = $max!=false && $max['SUM(exams.max)']!= '' ? $max['SUM(exams.max)'] : 0;
		
		$t_work_value = $term_value - (isset($t_final_exam_value) ? $t_final_exam_value : 0);
		$this_total = $this_result!=0 && $this_max != 0 ? 
			($this_result / $this_max) * $t_work_value
		: 0;
			
		$t_tot[$term_no] = $this_total;
		$t_work_perc[$term_no] = ($this_total * ($t_work_value/ $term_value) *100)/$t_work_value ;
	}
	return array(
		't_final' => $t_final,
		't_tot' => $t_tot,
		't_grad' => $t_grad,
		't_max' => $t_max,
		't_final_perc' => $t_final_perc,
		't_work_perc' => $t_work_perc
	);
}

function writeServiceRow($std_id, $service_id){
	$level_id = $GLOBALS['level_id'];
	$cur_term_no = $GLOBALS['cur_term_no'];
	$result = getServiceResults($std_id, $service_id);
	$Q1_work = $result['t_tot'][1] != 0 ? round($result['t_work_perc'][1], 1) .'%' : '&nbsp;';
	$Q1_exam = $result['t_final'][1] != 0 ? round($result['t_work_perc'][1] , 1).'%' : '&nbsp;';
	$Q1_total = $result['t_tot'][1] != 0 ? $Q1_work+ $Q1_exam .'%': '&nbsp;';
	$Q1_grad = $result['t_tot'][1]!=0 ? getStdGrad($level_id, 100, $Q1_total):'&nbsp;';
	$Q1_part = $result['t_tot'][1]+ $result['t_final'][1]!= 0 ? round($result['t_tot'][1]+ $result['t_final'][1],1 ) .'%' : '&nbsp;';
	$Q2_work =round($result['t_tot'][2], 1) .'%';
	//$Q2_work = $cur_term_no > 1 && $result['t_tot'][2] != 0 ? round($result['t_tot'][2], 1) .'%' : '&nbsp;';
	$Q2_exam = $cur_term_no > 1 && $result['t_final'][2] != 0 ? round($result['t_final'][2], 1) .'%' : '&nbsp;';
	
	$SEM1_total =  $Q2_exam>0 && ($result['t_tot'][1]+ $result['t_final'][1]+$result['t_tot'][2]+ $result['t_final'][2]!=0)? 
		round(($result['t_tot'][1]+ $result['t_final'][1]+$result['t_tot'][2]+ $result['t_final'][2]),1 ).'%'
		: '&nbsp;' ;
	$SEM1_grad =  $Q2_exam>0 && $SEM1_total!=0 ? 
		getStdGrad($level_id, 50, $SEM1_total ) 
		: '&nbsp;';

	$Q3_work = $result['t_tot'][3] != 0 ? round($result['t_work_perc'][3] , 1) .'%' : '&nbsp;';
	$Q3_exam = $result['t_final'][3] != 0 ? round($result['t_work_perc'][3] , 1).'%' : '&nbsp;';
	$Q3_total = $result['t_tot'][3] != 0 ? $Q3_work+ $Q3_exam .'%': '&nbsp;';
	$Q3_grad = $result['t_tot'][3]!=0 ? getStdGrad($level_id, 100, $Q3_total):'&nbsp;';
	$Q3_part = $result['t_tot'][3]+ $result['t_final'][3]!=0 ? round($result['t_tot'][3]+ $result['t_final'][3],1 ) .'%' : '&nbsp;';
	$Q4_work = $cur_term_no > 3 && $result['t_tot'][4] != 0 ?round($result['t_tot'][4], 1) .'%' : '&nbsp;';
	$Q4_exam = $cur_term_no > 3 && $result['t_final'][4] != 0 ? round($result['t_final'][4], 1).'%' : '&nbsp;';
	
	$SEM2_total =  $Q4_exam>0 && $result['t_tot'][3]+ $result['t_final'][3]+$result['t_tot'][4]+ $result['t_final'][4]!=0 ? 
		round($result['t_tot'][3]+ $result['t_final'][3]+$result['t_tot'][4]+ $result['t_final'][4],1).'%' 
		:'&nbsp;' ;
	$YEAR_total = $Q4_exam>0 && $SEM1_total+$SEM2_total!=0? $SEM1_total+$SEM2_total .'%': '&nbsp;';
	$SEM2_grad =  $Q4_exam>0 && $SEM2_total!=0 ? 
		getStdGrad($level_id, 50, $SEM2_total ) 
		: '&nbsp;';
	$YEAR_grad = $Q4_exam>0 && $YEAR_total !=0? getStdGrad($level_id, 100, $YEAR_total ) : '&nbsp;';

	return write_html('tr', '',
		write_html('td', 'class="mat_label"', getServiceNameById($service_id)).
		write_html('td', '', $Q1_work).
		write_html('td', '', $Q1_exam).
		write_html('td', '', $Q1_total).
		write_html('td', 'class="grad"', $Q1_grad).
		write_html('td', 'class="subtotal"', $Q1_part).
		write_html('td', 'class="subtotal"', $Q2_work).
		write_html('td', 'class="subtotal"', $Q2_exam).
		write_html('td', 'class="grad"', $SEM1_grad).
		write_html('td', 'class="totals_td" ', $SEM1_total).
		write_html('td', '', $Q3_work).
		write_html('td', '', $Q3_exam).
		write_html('td', '', $Q3_total).
		write_html('td', 'class="grad"', $Q3_grad).
		write_html('td', 'class="subtotal"', $Q3_part).
		write_html('td', 'class="subtotal"', $Q4_work).
		write_html('td', 'class="subtotal"', $Q4_exam).
		write_html('td', 'class="grad"', $SEM2_grad).
		write_html('td', 'class="totals_td"', $SEM2_total).
		write_html('td', 'class="totals_td" align="center"', $YEAR_total).
		write_html('td', 'class="grad"', $YEAR_grad)
	);
}

function builCertHtml($std_id){
	global $lang;
	$services = $GLOBALS['con_material'];
	$level_id = $GLOBALS['level_id'];
	$level_name = $GLOBALS['level_name'];
	$terms = $GLOBALS['terms'];
	$cur_term_no = $GLOBALS['cur_term_no'];
	$cur_term = $GLOBALS['cur_term'];
	$finals = $GLOBALS['finals'];
	$main_table_head = $GLOBALS['main_table_head'];
	
	$student_name = getStudentNameById($std_id);
	$class_id = getClassIdFromStdId($std_id);
	$class_name = getClassNameById($class_id);
	
	$head_div = write_html('div', 'class="ui-corner-all ui-state-highlight" style="padding:5px;"',
		write_html('table', 'width="100%"',
			write_html('tr', '',
				write_html('td', '',
					write_html('h2', 'class="title"', 'ACADEMIC REPORT  '.$_SESSION['year'].'-'.($_SESSION['year']+1) )
				).
				write_html('td', 'class="reverse_align"',
					write_html('h3', '', 
						write_html('em', '', $lang['level'].': '.$class_name.' / '.$level_name)
					)
				)
			).
			write_html('tr', '',
				write_html('td', 'colspan="2"',
					write_html('h3', '', $lang['student_name'].': '.$student_name)
				)
			)
		)
	);

	
	$main_table_body = '';
	foreach($services as $service_id){
		$main_table_body .= writeServiceRow($std_id, $service_id);
	}
	
	// write out
	$out ='';
//	$out = ' <page_header style="text-align:center;"><img src="../attachs/img/header.jpg" /> </page_header>';
	$out .= $head_div;
	$out .= write_html('div', 'align="center" style="text-align:center; margin:0px;padding:0px; border-top:1px solid #555555; border-right:1px solid #555555"',
		write_html('table', 'cellspacing="0" cellpadding="0" border="0" class="certificate_table"', 
			write_html('thead', '', $main_table_head).
			write_html('tbody', '', $main_table_body)
		)
	);
//	$out .= '<page_footer style="text-align:center; ">
//		<img src="../assets/img/footer.png" />
//	</page_footer>';
	
	return $out; //'<page>'.$out.'</page>';
}

?>