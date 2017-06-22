<?php
/** GPA
*
*/


class GPA {

	public function __construct($std_id){
		$this->std_id = $std_id;
		$this->student = new Students($std_id);
	}
	
	
	public function loadLayout($max_years=4){
		global $lang;
		
		
		$gpa_toolbox = array();
		$gpa_gradding = Gradding::getGpaGrad();
		
		$gpa_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="openGradding" gradingid="'.$gpa_gradding->id.'"',
			"text"=> $lang['edit_gpa'],
			"icon"=> "pencil"
		);
		$gpa_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'module="marks" action="print_pre" rel="#gpa_div-'.$this->std_id.'"',
			"text"=> $lang['print'],
			"icon"=> "print"
		);
		$gpa_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="saveAsPdf" rel="#gpa_div-'.$this->std_id.'"',
			"text"=> $lang['save_as_pdf'],
			"icon"=> "print"
		);
		$gpa_toolbox[] = array(
			"tag" => "span",
			"attr"=> 'style="margin:0px 10px" class="ui-corner-all ui-state-default"',
			"text"=> write_html('text', 'style="padding: 4px"',$lang['years'].': ').
				write_html_select('name="count_years" update="reloadGpa"  stdid="'.$this->std_id.'" class="ui-state-default def-float combobox" ',  array(2=>2, 3=>3, 4=>4, 5=>5), 4),
			"icon"=> ""
		);	
				
		
		// Get Chart
		$student = $this->student;			
		$services = $student->getServices();
		$services_array = array('0' => $lang['general']);
		$first_service = false;
		foreach($services as $service){
			if($first_service == false){
				$first_service = $service;
			}
			if($service->mark == 1){
				$services_array[$service->mat_id] = $service->getName();
			}
		}
		
		$gpa_toolbox[] = array(
			"tag" => "span",
			"attr"=> 'style="margin:0px 20px" class="ui-corner-all ui-state-default"',
			"text"=> write_html('text', 'style="padding: 4px"',$lang['term'].': ').
				write_html_select('name="services" update="reloadGpa" stdid="'.$this->std_id.'" class="ui-state-default def-float combobox" ', $services_array, ''),
			"icon"=> ""
		);	

		
		// Get Years
		$years = $this->getYears($max_years);
		
		// Out
		return write_html('div', 'id="gpa_div-'.$this->std_id.'"', 
			createToolbox($gpa_toolbox).
			write_html('table', '',
				write_html('tr', '',
					write_html('td', ' valign="top"',
						write_html('div', 'class="gpa_year_div"',
							$years->html
						)
					).
					write_html('td', 'width="300" valign="top"',
						write_html('h1', 'style="margin:40px 0px; text-align:center"', 'GPA: '. $years->gpa ).
						// Chart
						write_html('fieldset', '',
							 '<img class="chart" src="index.php?module=marks&gpa&chart&con=student&con_id='.$this->std_id.'&mat_id='.$first_service->mat_id.'" style="width:300px" onclick="enlargeChart(this)" class="hand" />'
						).
						// Gpa to table
						$this->createGpaTable()
					)
				)
			)
		);
	}
	
	public function getYears($max_years=4, $services=''){
		$out = array();
		$years = getYearsArray();
		$html = '';
		$gpa = 0;
		$count = 0;
		for($i=0; $i<$max_years; $i++){
			if(isset($years[$i])){
				$count++;
				$cur_year = $years[$i];
				$year = $this->loadYearTable($cur_year, $services);
				$html .= $year->html;
				$gpa = $gpa + $year->gpa;
			}
		}
		$out = new stdClass();
		$out->html = $html;
		$out->gpa = $gpa/$count;
		return $out;
	}
	
	public function loadYearTable($year, $services=''){
		global $lang;
		$out = new stdClass();
		$now_year = $_SESSION['year'];
		$_SESSION['year'] = $year;
		$credits = 0;
		$crdt = 0;
		$trs = array();
		$student = new Students($this->std_id);
		if($student->getClass() != false){
			if($services == ''){
				$services = $student->getServices();
			} else {
				require_once('modules/services/services.class.php');
				$services = array(new Services($services));
			}
			$level_name = $student->getLevel()->getName();
			$gpa = gradding::getGpaGrad();
			$marks = new Marks('student', $this->std_id);
			
			foreach($services as $service){
				if($service->mark == 1){
					$gradding = new gradding($service->level_id);
					$result = $marks->getYearTotal($service);
					$grade = $result[1];
					$gpa_res = $gpa->getStdGrad($result[0], 100);
					$crdt = $crdt + ($gpa_res->title * $service->coef) ;
					$credits = $credits + $service->coef ;
					$trs[] = write_html('tr', '', 
						write_html('td', '', $service->getName()).
						write_html('td', '', ($grade != false ? write_html('span', 'style="color:#'.$grade->color.'"', $grade->title) :'')).
						write_html('td', '', $service->coef).
						write_html('td', '', $gpa_res->title * $service->coef)
					);
				}
			}
				// Out
			$gpa_out = $credits>0 ? round($crdt/$credits, 1) : '';
			$html = write_html('table', 'class="result"',
				write_html('thead', '', 
					write_html('tr', '', 
						write_html('th', 'width="100"', $lang['material']).
						write_html('th', '', $lang['grade']).
						write_html('th', '', $lang['value']).
						write_html('th', '', $lang['credit'])
					)
				).
				write_html('tbody', '',
					implode('', $trs)
				).
				write_html('tfoot', '',
					write_html('tr', '', 
						write_html('th', 'colspan="3"', $lang['total_credit']).
						write_html('th', '', $credits)
					).
					write_html('tr', '', 
						write_html('th', 'colspan="3"', $lang['grade_point_avg']).
						write_html('th', '', $gpa_out)
					)
				)		
			);
			$out->gpa = $gpa_out;

		} else {
			$html = $lang['no_data'];
			$out->gpa = 0;
		}
		
		$out->html =  write_html('fieldset', 'style="display:inline-block; width:250px; vertical-align:top"',
			write_html('legend', '', $year .'/'. ($year+1) ).
			write_html('em', 'style="font-weight:bold; width:100%; text-align:center"', $level_name).
			$html
		);
		
		$_SESSION['year'] = $now_year;
		return $out;
	}
	
	public function createGpaTable(){
		global $lang;
		$level = $this->student->getLevel();
		$gradding = new gradding($level->id);
		
		$gpa = gradding::getGpaGrad();
		$scale = $gpa->getGraddinArray();
		$trs = array();
		foreach($scale as $s){
			$trs[] = write_html('tr', '',
				write_html('td', '', $gradding->getStdGrad($s->max, 100)->title).
				write_html('td', '', $s->title).
				write_html('td', '', $s->max)
			);
		}
		
		$out = write_html('table', 'class="result"',
			write_html('thead', '',
				write_html('tr', '',
					write_html('th', '', $lang['grad']).
					write_html('th', '', 'GPA').
					write_html('th', '', $lang['percent'])
				)
			). 
			write_html('tbody', '', implode('', $trs))
		);
		
		return $out;
	}
	
}
