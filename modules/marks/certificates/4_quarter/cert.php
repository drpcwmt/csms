<?php
## certificate template
## 2 Simester 4 quarter with final exam

function getServiceResults($student, $service, $terms){
	$std_id = $student->id;
	$service_id = $service->id;
	$level_id = $student->getlevel()->id;
	
	$gradding = new gradding($level_id);
	$out = array();

	foreach($terms as $term){
		$term_results = new stdClass();
		$term_id = $term->id;
		$term_value = $term->marks;
		if($term->approved != 1){
			$term_results->exam = 0;
			$term_results->exam_perc = 0;
			$term_results->yearwork = 0;
			$term_results->yearwork_perc = 0;
			$term_results->total = 0;
			$term_results->total_perc  = 0 ;
			$term_results->total_grad = 0;
		} else {
			$final = do_query_obj("SELECT * FROM exams , exams_results
			WHERE exams.term_id=$term_id 
			AND exams.service=$service_id 
			AND exams_results.exam_id=exams.id
			AND exams_results.std_id=$std_id
			ORDER BY exams.exam_no DESC LIMIT 1", DB_year);
		//	echo $final['id'];
			if(isset($final->id )){
				$final_exam_id = $final->id;
				$final_result = do_query_obj("SELECT results FROM exams_results WHERE exam_id=$final_exam_id AND std_id=$std_id", DB_year);
				$result = $final_result->results != -1 ? $final_result->results : 0;
				$exam_partc = $final->value / $term_value;
				$term_results->exam = ($result / $final->max) * $final->value;
				$term_results->exam_perc = ($result / $final->max) * $exam_partc * 100;
				$final_exam_no = $final->exam_no;
			} else {
				$term_results->exam = 0;
				$term_results->exam_perc = 0;
				$final_exam_id = NULL;
				$final_exam_no = NULL;
			}
			
			$year_works_exams = do_query_array("SELECT exams_results.results, exams.max, exams.value FROM exams, exams_results WHERE 
				exams.term_id = $term_id
				AND exams.service=$service_id
				AND exams_results.exam_id=exams.id
				AND exams_results.std_id=$std_id
				AND exams.exam_no!='$final_exam_no'
				GROUP BY exams.exam_no", DB_year);
			$year_work_result = 0;
			$year_work_max = 0;
			$year_work_value = 0;
			foreach($year_works_exams as $exam){
				$year_work_max = $year_work_max + $exam->max;
				$year_work_value = $year_work_value + $exam->value;
				$year_work_result = $year_work_result + ($exam->results != -1 || $exam->results != '' ? $exam->results : 0 );
			}

			$yearwork_partc = $year_work_value / $term_value;
			$term_results->yearwork = ($year_work_result / ($year_work_max > 0 ?$year_work_max : 1)) * $year_work_value;
			$term_results->yearwork_perc = ($year_work_result / ($year_work_max > 0 ?$year_work_max : 1)) * $yearwork_partc * 100;
			
			$term_results->total = $term_results->exam + $term_results->yearwork;
			$term_results->total_perc  = $term_results->exam_perc + $term_results->yearwork_perc ;
			
			$grad_res = $gradding->getStdGrad($term_results->total ,$term_value);
			$term_results->total_grad = write_html('span', 'style="color:#'.$grad_res->color.'"', $grad_res->title);

			
		}
		$out[] = $term_results;
	}
	//print_r($out);
	return $out;
}

function writeServiceRow($student, $service, $terms){
	$level_id = $student->getlevel()->id;
	$cur_term = terms::getCurentTerm('student', $student->id);
	if($cur_term == false){
		$cur_term = Terms::getTermByno($this->level_id , 1);
	}		
	$cur_term_no = $cur_term->term_no;
	$result = getServiceResults($student, $service, $terms);
	$gradding = new gradding($level_id);
	
	$Q1 =  $result[0];
	$Q2 =  $result[1];
	$Q3 =  $result[2];
	$Q4 =  $result[3];
	$row = new stdClass();
	$row->service_name = $service->getName();
	$row->Q1_work = $Q1->yearwork_perc != 0 ? round($Q1->yearwork_perc, 1) .'%' : '&nbsp;';
	$row->Q1_exam = $Q1->exam_perc != 0 ? round($Q1->exam_perc , 1).'%' : '&nbsp;';
	$row->Q1_total = $Q1->total_perc != 0 ? $Q1->total_perc .'%': '&nbsp;';
	$row->Q1_grad = $Q1->total_grad;
	$row->Q1_part = $Q1->total != 0 ? $Q1->total .'%': '&nbsp;';
	
	$row->Q2_work = $Q2->yearwork != 0 ? round($Q2->yearwork, 1) .'%' : '&nbsp;';
	$row->Q2_exam = $cur_term_no > 1 && $Q2->exam != 0 ? round($Q2->exam, 1) .'%' : '&nbsp;';
	
	$row->S1_total =  $Q2->exam >0 && ($Q1->total + $Q2->total !=0)? 
			round(($Q1->total + $Q2->total),1 ).'%'
		: '&nbsp;' ;
	if( $Q2->exam >0 && ($Q1->total + $Q2->total !=0 )){
		$grad_res = $gradding->getStdGrad($Q1->total + $Q2->total , 50);
		$row->S1_grad = write_html('span', 'style="color:#'.$grad_res->color.'"', $grad_res->title);
	} else {
		$row->S1_grad =  '&nbsp;';
	}

	$row->Q3_work = $Q3->yearwork_perc != 0 ? round($Q3->yearwork_perc, 1) .'%' : '&nbsp;';
	$row->Q3_exam = $Q3->exam_perc != 0 ? round($Q3->exam_perc , 1).'%' : '&nbsp;';
	$row->Q3_total = $Q3->total_perc != 0 ? $Q3->total_perc .'%': '&nbsp;';
	$row->Q3_grad = $Q3->total_grad;
	$row->Q3_part = $Q3->total != 0 ? $Q3->total .'%': '&nbsp;';

	$row->Q4_work = $cur_term_no > 3 && $Q4->yearwork != 0 ? round($Q4->yearwork, 1) .'%' : '&nbsp;';
	$row->Q4_exam = $cur_term_no > 3 && $Q4->exam != 0 ? round($Q4->exam, 1).'%' : '&nbsp;';
	
	$row->S2_total =  $Q4->exam >0 && ($Q3->total + $Q4->total !=0)? 
			round(($Q3->total + $Q4->total),1 ).'%'
		: '&nbsp;' ;
	if( $Q4->exam >0 && ($Q3->total + $Q4->total !=0 )){
		$grad_res = $gradding->getStdGrad($Q3->total + $Q4->total , 50);
		$row->S2_grad = write_html('span', 'style="color:#'.$grad_res->color.'"', $grad_res->title);
	} else {
		$row->S2_grad =  '&nbsp;';
	}

	$year_total = $Q1->total + $Q2->total + $Q3->total + $Q4->total;
	$row->Y_total = $Q4->exam >0 && $year_total !=0? $year_total .'%': '&nbsp;';
	$row->Y_grad = $Q4->exam >0 && $year_total !=0? 
			$gradding->getStdGrad( $year_total, 100 )->title 
		: '&nbsp;';

	return fillTemplate('modules/marks/certificates/4_quarter/cert_tr.tpl', $row);
}

/************* Main Function ******************/
function builCertHtml($std_id){
	global $lang;
	global $MS_settings;

	$student = new Students($std_id);
	$class = $student->getClass();
	$level = $student->getLevel();
	
	$terms = terms::getTermsByCon('student', $std_id);

	$main_table_head = fillTemplate('modules/marks/certificates/4_quarter/cert_thead.tpl', new StdClass());
	
	$services = $student->getServices();
		
	$head_div = write_html('div', 'class="ui-corner-top ui-state-highlight" style="padding:5px;"',
		write_html('table', 'width="100%"',
			write_html('tr', '',
				write_html('td', '',
					write_html('h2', 'class="title"', 'ACADEMIC REPORT  '.$_SESSION['year'].'-'.($_SESSION['year']+1) )
				).
				write_html('td', 'class="reverse_align"',
					write_html('h3', '', 
						write_html('em', '', $lang['level'].': '.$class->getName().' / '.$level->getName())
					)
				)
			).
			write_html('tr', '',
				write_html('td', 'colspan="2"',
					write_html('h3', '', $lang['student_name'].': '.$student->getName())
				)
			)
		)
	);

	
	$main_table_body = '';
	foreach($services as $service){
		$main_table_body .= writeServiceRow($student, $service, $terms);
	}
	
	// write out
	$extra_col = $MS_settings['cert_remarks'] !='' || $MS_settings['add_grad_to_cert'] ==1 ? true : false;
	
	$out = write_html('table', 'width="100%" class="certificate_table_layout"',
		write_html('tr', '', 
			write_html('td', 'valign="top"', 
//				$head_div.
				write_html('div', 'align="center" style="text-align:center; margin:0px;padding:0px; border-top:1px solid #555555; border-right:1px solid #555555"',
					write_html('table', 'cellspacing="0" cellpadding="0" border="0" class="certificate_table"', 
						write_html('thead', '', $main_table_head).
						write_html('tbody', '', $main_table_body)
					)
				)
			).
			($extra_col ?
				write_html('td', 'width="15%" valign="top" style="font-size:9px"',
					($MS_settings['add_grad_to_cert'] ==1 ?
						write_html('div', 'class="ui-corner-top ui-widget-header"',
							write_html('h3', 'style="margin:3px"', $lang['gardding_shell'])
						).
						write_html('div', 'class="ui-corner-bottom ui-widget-content"',
							''//getGradTable($level['gradding'])
						)
					: '').
					($MS_settings['cert_remarks'] !='' ?
						write_html('div', 'class="ui-corner-top ui-widget-header"',
							write_html('h3', 'style="margin:3px"', $lang['cert_remarks'])
						).
						write_html('div', 'class="ui-corner-bottom ui-widget-content"',
							write_html('p', '', $MS_settings['cert_remarks'])
						)
					: '')					
				)
			: '')
		)
	);
	return $out;
}

?>