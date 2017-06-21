<?php
## Class Summary 
## work for prof and suoervisors

$cur_date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
if( in_array(date('w', $cur_date), explode(',', $MS_settings['weekend']))){
	$widget = '';
} elseif($cur_date> getYearSetting('end_date') || calendars::chkHoliday($cur_date, 'class', $_SESSION['cur_class'])){
	$widget = '';
} else {
	$home = new Layout();;
	
	$class = new Classes($_SESSION['cur_class']);
	$home->cur_class_name = $class->getName();
	$home->cur_class_id = $class->id;
	$students = $class->getStudents();
	$home->total_students = count($students);
	
	$absents = new absents('class', $_SESSION['cur_class']);
	$today = mktime(0,0,0, date('m'), date('d'), date('Y'));
	$total_absents = count($absents->getAbsents($today, $today ));
	$home->total_present = count($students) - $total_absents ;  
	
	
	$prof = new Profs($_SESSION['user_id']);
	$services = $prof->getServices('class', $_SESSION['cur_class']);
	
	$cur_term = terms::getCurentTerm('class', $_SESSION['cur_class']);
	if($cur_term != false){
		$home->cur_term_name = $cur_term->title;
		$home->cur_term_end_date = $cur_term->end_date;
	}
	
	foreach($services as $service){
		if($cur_term!=false){
			$exam = exams::getNextExam($service->id);
		} else {
			$exam = false;
		}
		$trs[] = write_html('tr', '', 
			write_html('td', 'width="24"', 
				write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" title="'.$lang['open'].'" module="services" serviceid="'.$service->id.'" action="openService"',
					write_icon('extlink')
				)
			).
			write_html('td', '', $service->getName()).
			write_html('td', 'width="24" ', 
				write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" title="'.$lang['new_exercice'].'" serviceid="'.$service->id.'" exerciseid="new" action="editExercise" module="lms"',
					write_icon('script')
				)
			).
			write_html('td', 'width="24" ', 
				write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" title="'.$lang['new_summary'].'" serviceid="'.$service->id.'"  action="openSummary" module="lms"',
					write_icon('note')
				)
			).
			write_html('td', 'width="24" ', 
				($exam != false ? 
					write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" title="'.$lang['new_exam'].'" serviceid="'.$service->id.'" exerciseid="new" action="loadExam" examno="'.$exam->exam_no.'" termid="'.$exam->term_id.'" module="marks"',
						write_icon('calendar')
					)
				: '')
			)
		);
	}
	$home->services_shortcuts = implode('', $trs);
	
	$widget = fillTemplate("modules/home/templates/class_summary.tpl", $home);
}
