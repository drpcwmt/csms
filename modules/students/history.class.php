<?php
/** Classes
*
*/

class History extends Students{
	
	public function loadFinalResultsTable(){
		global $lang;
		$thead = write_html('thead', '', 
			write_html('tr', '',
				write_html('th', '', $lang['year']).
				write_html('th', '', $lang['class']).
				write_html('th', '', $lang['level']).
				write_html('th', '', $lang['status']).
				(MS_codeName != 'sms_basic' ?
					write_html('th', '', $lang['result']).
					write_html('th', '', $lang['certificate'])	
				:'')		
			)
		);
		
		$years = $this->getYearList();
		$trs = array();
		foreach($years as $year){
			$trs[] = write_html('tr', '',
				write_html('td', '', $year->year.'/'.($year->year+1)).
				write_html('td', '', $year->class_name).
				write_html('td', '', $year->level_name).
				write_html('td', '', $year->status).
				(MS_codeName != 'sms_basic' ?
					write_html('td', '', $year->result).
					write_html('td', '', $year->certificate)
				:'')		
			);
		}
		
		return write_html('div', 'class="toolbox"',
			write_html('a', 'action="print_pre" rel="#std_history-'.$this->id.'"', write_icon('print'). $lang['print'])
		).
		write_html('div', 'id="std_history-'.$this->id.'"',
			write_html('div', 'class="hidden showforprint"',
				write_html('h2', 'class="def_align"', unixToDate(time())).
				write_html('h2', 'align="center"', $lang['sequencing']).
				write_html('h3', 'align="center"', $this->getName())
			).
			write_html('table', 'class="tablesorter"',
				$thead.
				write_html('tbody', '',
					implode('', $trs)
				)
			)
		); 
	}
	
	public function getYearList(){
		global $lang;
		$out = array();
		$years = do_query_array("SELECT * FROM years ORDER BY year DESC", $this->sms->database, $this->sms->ip);
		foreach($years as $year){
			$thisYear = new stdClass();
			$thisYear->class_name = '';
			$thisYear->level_name = '';
			$thisYear->result = '';
			$thisYear->status = '';
			$thisYear->year = $year->year;

			$database = Db_prefix.$year->year;
			if(count(do_query_obj("SHOW DATABASES LIKE '$database'",  $this->sms->database, $this->sms->ip)) > 0){
				$chk_class = do_query_obj("SELECT class_id, new_stat FROM classes_std WHERE std_id=$this->id", $database, $this->sms->ip);
				if(isset($chk_class->class_id)){
					$thisYear->begin_date = $year->begin_date;
					$thisYear->end_date = $year->end_date;
					$thisYear->class_id = $chk_class->class_id;
					$thisYear->status = $chk_class->new_stat == 1 ? $lang['result_new'] : $lang['result_redouble'];
					
					$final_results = do_query_obj("SELECT * FROM final_result WHERE std_id=$this->id", $database, $this->sms->ip);
					if(isset($final_results->std_id)){
						$thisYear->class_name = $final_results->class_name;
						$thisYear->level_name = $final_results->level_name;
						$thisYear->result = $final_results->result;
					} else {
						$class = do_query_obj("SELECT * FROM classes WHERE id=$chk_class->class_id", $database, $this->sms->ip);
						$thisYear->class_name = $class->{'name_'.$_SESSION['dirc']};
						$level = new Levels($class->level_id, $this->sms);
						$thisYear->level_name = $level->getName();
						$thisYear->result = '0';
						$thisYear->std_id = $this->id;
						if($year->year != $_SESSION['year']){
							do_insert_obj($thisYear, 'final_result', $database, $this->sms->ip);
						} 
					}
					$thisYear->certificate = '';
					if($this->getLevel()!= false){
						$level_id = $this->getLevel()->id;
						$term = do_query_obj("SELECT term_no FROM terms WHERE level_id=$level_id ORDER BY term_no DESC LIMIT 1", $database, $this->sms->ip);
						if($term != false && isset($term->term_no)){
							$filename= 'certificate-'.$thisYear->year.'-term'.$term->term_no.'.pdf';
							if(file_exists("attachs/files/$this->id/$filename")){
								$thisYear->certificate = write_html('a', 'module="documents" action="openFile" type="pdf", rel="attachs/files/$this->id/$filename"', $lang['certificate']);
							}
						}
					} else {
						$thisYear->certificate = '';
					}
					$out[] = $thisYear;
				}
			}
		}
		return $out;
	}

}