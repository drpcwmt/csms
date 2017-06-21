<?php
/** Marks
*
*/

class marks{
	public $con = '',
	$con_id = '',
	$level_id = '',
	$services = array(),
	$cur_term = '',
	$cur_service = '',
	$edit = false,
	$calcType = false,
	$calcSpan = '';
	
	private $thisTemplatePath = 'modules/marks/templates';

	
	public function __construct($con, $con_id){
		global $sms;
		$this->DB_year = Db_prefix.$_SESSION['year'];
		$this->con = $con;
		$this->con_id = $con_id;
		$obj = $sms->getAnyObjById($con, $con_id);
		$this->level_id = $obj->getLevel()->id;
		$this->calcType = $this->getLevelCalcType($this->level_id);
		$this->calcSpan = $this->getCalcSpan($this->calcType);
		$this->gradding = new gradding($this->level_id);
	}
	
	public function getServices(){
		global $MS_settings;
		if(count($this->services) == 0){
			if($this->con == 'class'){
				$class = new Classes($this->con_id);
				$con_services= $class->getServices();
			} elseif($this->con == 'group'){
				$group = new Groups($this->con_id);
				$con_services= $group->getServices();
			} elseif($this->con == 'student'){
				$student = new Students($this->con_id);
				$con_services= $student->getServices();
			} else {
				$level = new Levels($this->con_id);
				$con_services= $level->getServices();
			}
			
			if(count($con_services) > 0){
				foreach($con_services as $service){
					if($service->mark == 1){
						if(in_array($_SESSION['group'], array('prof', 'supervisor'))){
							$editable_services = $this->getEditableServices();
							if ($MS_settings['prof_can_see_other_marks'] == 1 || in_array($service, $editable_services)){
								$this->services[] = $service;
							}
						} else {
							$this->services[] = $service;
						}
					}
				}
			}
		}
		return $this->services;
	}
	
	public function getEditableServices(){
		if(!isset($this->editable_services)){
			$editable_materials = array();
			if($_SESSION['group'] == 'prof'){
				$prof = new Profs($_SESSION['user_id']);
				$editable_materials = $prof->getServices($this->con, $this->con_id);
			} elseif($_SESSION['group'] == "supervisor"){
				$supervisor = new Supervisors($_SESSION['user_id']);
				$editable_materials = $supervisor->getServices();
			} elseif(in_array($_SESSION['group'], array( "student", "parent"))){
				$editable_materials = array();
			} else{
				if(getPrvlg('mark_edit')){
					$editable_materials = $this->getServices();
				} else {
					$editable_materials = array();
				}
			}
			$this->editable_services = $editable_materials;
		}
		return $this->editable_services;	
	}

	public function getTerms(){
		if(!isset($this->terms)){
			$this->terms = terms::getTermsByCon($this->con, $this->con_id);
		}
		return $this->terms;
	}
	
	public function getCurrentTerm(){
		if($this->cur_term == ''){
			$this->cur_term = terms::getCurentTerm($this->con, $this->con_id);
		}
		return $this->cur_term;
	}
	
	public function loadLayout(){
		global $lang;
		$layout = new stdClass();
		$layout->con = $this->con;
		$layout->con_id = $this->con_id;
		$thisTerm = $this->getCurrentTerm();
		if($thisTerm == false){
			$thisTerm =  Terms::getTermByno($this->level_id , 1);
		}		
		$marks_toolbox = array();
		$marks_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'module="marks" action="resetTerms" con="'.$this->con.'" conid="'.$this->con_id.'"',
			"text"=> $lang['modify'],
			"icon"=> "pencil"
		);
		$marks_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'module="marks" action="print_pre" rel="#exams_tabs_div-'.$this->con.'-'.$this->con_id.'"',
			"text"=> $lang['print'],
			"icon"=> "print"
		);
		$marks_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="saveAsPdf" rel="#exams_tabs_div-'.$this->con.'-'.$this->con_id.'"',
			"text"=> $lang['save_as_pdf'],
			"icon"=> "print"
		);

		$marks_toolbox[] = array(
			"tag" => "span",
			"attr"=> 'style="margin:0px 10px" class="ui-corner-all ui-state-default"',
			"text"=> write_html('text', 'style="padding: 4px"',$lang['term'].': ').
				write_html_select('name="terms" update="reloadMarks" class="ui-state-default def-float combobox" ',  objectsToArray($this->getTerms()), ($thisTerm != false ? $thisTerm->id : '')),
			"icon"=> ""
		);			
		$layout->toolbox = createToolbox($marks_toolbox);
		
		if($thisTerm != false){
			$layout->mark_table = $this->createMarksTable($thisTerm->id);
		} else {
			$layout->mark_table =  write_error($lang['undefined_terms']);
		}
		
		if($this->con == 'student'){
			$layout->student_reports_list = write_html('li', '', write_html('a', 'href="index.php?module=marks&amp;reports&list&amp;con=student&con_id='.$this->con_id.'"',  $lang['reports_list']));
			$layout->student_gpa = write_html('li', '', write_html('a', 'href="index.php?module=marks&gpa&con=student&con_id='.$this->con_id.'"',  $lang['transcript']));
		}
		return fillTemplate("$this->thisTemplatePath/marks.tpl", $layout);

	}
	
	// Edit functions
	
	public function createMarksTable($term_id, $editMode=false){
		$this->cur_term = $term_id;
		global $lang, $sms;
		$calc_type = $this->calcType;
		$calc_span = $this->calcSpan;
		$terms = array();
		if($term_id != 0){
			$terms[] = new terms($term_id);
		} else {
			$terms = $this->getTerms();
		}

			// Table Head
		$exam_table_thead = '<tr><th width="25%">&nbsp;</th>';
		foreach($terms as $term){
			$exam_table_thead .= write_html('th', 'style="text-align:center"', 
				($term->approved == 1 ?
					write_html('span', 'class="approve reverse_align rev_float"', 
						'<img src="'.MS_assetspath.'img/success.png" style="vertical-align:middle"/>'.
						$lang['approved']
					)
				: '').
				$lang['term'].': '.(($term->title != '' ) ? $term->title : $term->term_no ).
				' ('.$term->marks .$calc_span.')'.
				($term->begin_date != '' && $term->end_date !='' ? '<br>'. unixToDate($term->begin_date).' - '.unixToDate($term->end_date) : '')
			);
		}
		$exam_table_thead .= '</tr>';
		
			// Table Body
		$exam_table_tbody ='';
		$editable_materials = $this->getEditableServices();
		$trs = array();
		$thisServices = $this->getServices();
		if( count($thisServices) > 0){
			foreach($thisServices as $service){
				$optional=($service->optional == '1')? write_html('span', 'class="big_red_dot" title="'.$lang['optional'].'"', '*' ) : '';
				$bonus = ($service->bonus == '1')? write_html('span', 'class="big_blue_dot" title="'.$lang['bonus'].'"', '*' ) : '';
				$thisRow = '<tr>';
				$thisRow .= write_html('td', '', 
					$service->getName(). ' '.
					($this->level_id != $service->level_id ? $sms->getAnyNameById('level', $service->level_id) : ''). 
					$optional.$bonus 
				);
				foreach($terms as $term){
					if($editable_materials != false && object_in_array($service, $editable_materials ) && $term->approved != '1'){
						$edit_exam = true;
					} else {
						$edit_exam = false;
					}
					$thisRow .= write_html('td', 'style="padding:0px;"', $this->createServiceTermExams($service, $term, $edit_exam,  count($terms), $editMode));
				}
				$thisRow .= '</tr>';
				$trs[] = $thisRow;
			}
			return write_html('table', 'class="result exam_tabel nohover" id="exam_tabel"', 
				write_html('thead', '', $exam_table_thead) .
				write_html('tbody', '', 
					implode('', $trs).
					'<tr height="3">&nbsp;</tr>'
				)
			);
		
		} else {
			return write_error($lang['no_service_defined']);
		}
	}
	
	public function createServiceTermExams($service, $term, $edit_exam, $tot_terms, $editMode){
		$out = '';
		$tds = array();
		if($tot_terms == 1){
			$css = 'large_cell';
		} else {
			$css = "small_cell";
		}
		
		$tds[] =  write_html('td', 'style="padding:0px;"',
		 	$this->getTermServiceExams($service, $term, $edit_exam, $tot_terms, $editMode)
		);

		if($editMode == false){
			$tds[] = $this->getTermServiceTotalCell($service, $term, $tot_terms);
		}
		
		return 	write_html('table', 'cellspacing="0" style="font-size:7px" width="100%"',
			write_html('tr', '', 
				implode('', $tds)
			)
		);
	}
	
	public function getTermServiceExams($service, $term, $edit_exam, $tot_terms, $editMode){
		global $lang;
		$mat_calc_type = $this->getLevelCalcType($service->level_id);
		$gradding = new gradding($service->level_id);
		if($term->exam_no > 0){
			$tot_exam = $term->exam_no;
		} elseif($term->exam_no == '0'){
			$tot_exam = $service->exam_no;
		} elseif($term->exam_no == -1){
			if($editMode){
				$con = 'level'; $con_id= $service->level_id;
			} else {
				$con = $this->con; $con_id= $this->con_id;
			}
			$exec_exams = do_query_array( "SELECT id FROM exams WHERE term_id=$term->id AND service=$service->id AND con='$con' AND con_id=$con_id GROUP BY exam_no", $this->DB_year);
			$tot_exam = count($exec_exams);
		}  else {
			$tot_exam = 0;
		}

		if($tot_terms == 1){
			$css = 'large_cell';
		} else {
			$css = "small_cell";
		}
	
		$exams_tds = array();
		for($e=1; $e<=$tot_exam; $e++){
			$exam = exams::searchExam($service->id, $term->id, $e, $this->con, $this->con_id);
			if($editMode==true){
				if($exam != false){
					if($mat_calc_type == 'skills'){
						$cell_content = '<span class="ui-icon ui-icon-radio-off"></span>';
					} elseif($mat_calc_type == 'marks') {
						$cell_content = $exam->max;
					} elseif($mat_calc_type == 'moyen') {
						$cell_content = $exam->coef;
					} elseif($mat_calc_type == 'per') {
						$cell_content = $exam->value;
					} 
				} else {
					$cell_content = '';
				}
				
				$class = "hand ui-state-default hoverable" ;
				$onclick_event = 'onclick="$(this).toggleClass(\'ui-state-active\')"';
			 
			} else {
				$tooltip_content = array();
				/**************************************************************/
				if(isset($exam->id)){
					$exam_id = $exam->id;
					$results = $exam->getExamResults();
					$absents = $exam->getCountAbsents();
					$count_results = count($exam->results);
					$statics = $exam->getStatics();
						// check if exam is approved 
					if($edit_exam && $exam->approved != '1'){
						$onclick_event = 'action="loadExam"';
						$class = "hand ui-state-default hoverable" ;
					} else {
						$onclick_event = '';
						$class = "hand ui-widget-content" ;
					}
										
					if($count_results > 0){
							// Bottom Left
						if($this->con == 'student'){
							$res = $results[$this->con_id];
							$bl = isset($results[$this->con_id]) && $results[$this->con_id]!= '' ? $results[$this->con_id] : $lang['abs'];
						} else {
							$res = $statics->avg;
							$bl = $statics->avg ;
						}
						
							// Top Right
						if( $mat_calc_type == 'moyen' || $mat_calc_type == 'skills' ){
							$tr = $exam->coef;
						} elseif($mat_calc_type == 'per'){
							$tr = $exam->value;
						} elseif($mat_calc_type == 'marks'){
							$tr = $exam->max;
						}
						
							// Top Left
						if($this->con != 'student'){
							if($exam->approved == '1'){
								$tl = write_icon('locked');
							} elseif($count_results == 0 && $exam->date > 1 && $exam->date < (time() - 86400)){
								$tl = write_icon('alert');
							} elseif($absents != 0){
								$tl = write_icon('notice');
							} else {
								$tl = '';
							}
						}else {
							$tl = '';
						}
						
							// Bottom Right
						if($mat_calc_type == 'skills'  || $exam->max == '0'){
							$br = '';
						} else {
							$grd_res =  $gradding->getStdGrad($res, $exam->max);
							$br =  $grd_res!=false ? $grd_res->title : '';
						}
							// Collect content
						$cell_content = write_html('div', 'class="'.$css.'"',
							($tot_terms == 1 ?
								write_html('span', 'class="br"', $br).
								write_html('span', 'class="tl"', $tl)
							: '').
							write_html('span', 'class="bl"', $bl).
							write_html('span', 'class="tr"', $tr)
						);
					} else {
						$avg = false;
						// exam have been defined but no results 
						$cell_content = write_html('div', 'class="'.$css.'"',
							($edit_exam && $exam->approved != 1 ?
								write_html('span', 'class="bl ui-icon ui-icon-pencil"', '')
							: '')
						);
					}
						// tooltip
					if($exam->title != ''){ $tooltip_content[] = array($lang['name'],$exam->title);}
					if($this->con == 'student'&& $exam->results != false ){
						if(isset($results[$this->con_id]) && $results[$this->con_id]!= NULL){
							$tooltip_content[] = array($lang['results'], $results[$this->con_id]);
						} else {
							$tooltip_content[] = array($lang['results'], $lang['abs']);
						}
					}
					if($exam->date >10){ $tooltip_content[] = array($lang['date'],unixToDate($exam->date));}
					if($absents != 0 && $count_results){ $tooltip_content[] = array($lang['absents'],$absents);}
					if($exam->approved == '1'){ $tooltip_content[] = array($lang['approved'], write_icon('check'));}
					if(($mat_calc_type == 'moyen' || $mat_calc_type == 'skills') && $exam->coef != 1) {
						$tooltip_content[] = array($lang['coeffcient'], $exam['coef']);
					} 
					if($mat_calc_type == 'per' && $exam->value != 0){ $tooltip_content[] = array($lang['value'], $exam->value); }
					if($statics->avg != 0){$tooltip_content[] = array($lang['avrage'], $statics->avg); }
					if($exam->max != 0){$tooltip_content[] = array($lang['max'], $exam->max);}
					if($count_results == 0 && $exam->date > 10 && $exam->date < (time() - 86400)){ 
						$tooltip_content[] = array(write_icon('alert'),$lang['late_exam']);
					}
					
			
			
				} else {
						// exam not have been defined 
					$cell_content = write_html('div', 'class=" uprintable '.$css.'"',
						write_html('span', 'class="bl ui-icon ui-icon-pencil"', '')
					);
					$tooltip_content[] = array( $lang['new_exam'], write_icon('document'));
					$onclick_event = '';
					$class = 'ui-widget-content';
				}	
				
				$tooltip = '<div class="tooltip"><table  border="0" width="100%">';
				foreach($tooltip_content as $arr){
					$tooltip .= write_html('tr', '',
						write_html('td', '', $arr[0]).
						write_html('td', '', $arr[1])
					);
				}
				$tooltip .= '</table></div>';
				$cell_content = $cell_content.$tooltip;
				/**************************************************************/				
			}
		
			
			$exams_tds[] = write_html('td', 'style="text-align:center" class="result_cell '.$css.' '.$class.'" serviceid="'.$service->id.'" termid="'.$term->id.'" examno="'.$e.'" '.$onclick_event, 
				$cell_content
			) ;
		}
		
		if($term->exam_no == -1){
			$exams_tds[] = write_html('td', 'style="text-align:center" class="result_cell hand ui-state-default hoverable" serviceid="'.$service->id.'" termid="'.$term->id.'" examno="'.$e.'" action="'.($editMode ? 'presetExam' : 'loadExam').'"', 
				write_icon('plus')
			) ;
		}
		
		return write_html('table', 'cellspacing="1" ',
			write_html('tr', '', 
				implode('', $exams_tds)
			)
		);
	}
	
	public function getTermServiceTotalCell($service, $term, $tot_terms){
		$service_id = $service->id;
		$term_id = $term->id;
		$gradding = new gradding($service->level_id);
		$mat_calc_type = $this->getLevelCalcType($service->level_id);
		if($tot_terms == 1){
			$css = 'large_cell';
		} else {
			$css = "small_cell";
		}
		
		$total_gradding = new stdClass();
		$total_gradding->title = '';
		$total_gradding->color = '';
		if($mat_calc_type == 'moyen'){
			$total_cell_content = $term->get_mat_term_avg($service, $this->con, $this->con_id);
		} elseif($mat_calc_type == 'points'){
			$total_cell_content = $term->get_mat_term_points($service, $this->con, $this->con_id);
			if($term->approved == 1 ){
				$total_gradding = $gradding->getStdGrad($total_cell_content, $term->marks);
			}
		} elseif($mat_calc_type == 'per'){
			$total_cell_content = $term->get_mat_term_per($service, $this->con, $this->con_id);
			if($term->approved == 1 ){
				$total_gradding = $gradding->getStdGrad($total_cell_content, $term->marks);
			}
		} elseif($mat_calc_type == 'skills'){
			$total_cell_content = $term->get_mat_term_grad($service, $this->con, $this->con_id);
		} else {
			echo $mat_calc_type;
		}
	
		$class = $total_cell_content > $term->marks ? "ui-state-error" : "ui-state-highlight";
		return write_html('td', 'class="'.$class.'" style="padding:0px;" width="'.($tot_terms == 1 ? 48:32).'"', 
			write_html('div', 'class="'.$css.'"',
				write_html('span', 'class="bl"', $total_cell_content).
				write_html('span', 'class="tr" style="color:#'.($total_gradding != false ? $total_gradding->color : '').'"', ($total_gradding != false ? $total_gradding->title :"")).
				write_html('span', 'class="br"', ($service->coef != 1 ? 'x'+$service->coef: ''))
			)
		);
	}
	
	static function getLevelCalcType($level_id){
		$level = do_query_obj( "SELECT calc FROM levels WHERE id=$level_id", DB_student);
		if($level->calc != ''){
			return $level->calc;
		} else {
			return false;
		}
	}
	
	static function getCalcSpan($calcType){
		global $lang;
		switch($calcType){
			case "per":
				$calc_span = '%';		
			break;	
			case "marks":
				$calc_span = ' '.$lang['points'];
			break;	
			default:
				$calc_span = '';
			break;	
			
		}
		return $calc_span;
	}

	public function getYearTotal($service){
		$total = 0;
		$gradding = false;
		$terms = $this->getTerms();;
		if($terms != false && count($terms)> 0){
			$gradding = new gradding($this->level_id);
			if($this->calcType == 'per'){
				$thisTermTotal = 0;
				foreach($terms as $term){
				//	echo $service->id.'-';
					$thisTermTotal = $thisTermTotal + $term->marks;
					if($service != false ){
						$thisTermTotalResults = $term->get_mat_term_per($service, $this->con, $this->con_id);
					} else {
						$thisTermTotalResults = $term->get_term_total_per($service, $this->con, $this->con_id);
					}
					$total = $total +$thisTermTotalResults;
				}
				$gradding = $gradding->getStdGrad($total, $thisTermTotal);
			} elseif($this->calcType == 'points'){
				$total = 0;
				$thisTermTotal = 0;
				foreach($terms as $term){
					$thisTermTotal = $thisTermTotal + $term->marks;
					if($service != false ){
						$thisTermTotalResults = $term->get_mat_term_points($service, $this->con, $this->con_id);
					} else {
						$thisTermTotalResults = $term->get_term_total_points($service, $this->con, $this->con_id);
					}
					$total = $total +$thisTermTotalResults;
				}
				$gradding = $gradding->getStdGrad($total, $thisTermTotal);
			} elseif($this->calcType == 'moyen'){
				$moyens = array();
				foreach($terms as $term){
					if($service != false ){
						$moyens[] = $term->get_mat_term_avg($service, $this->con, $this->con_id);
					} else {
						$moyens[] = $term->get_term_total_avg($service, $this->con, $this->con_id);
					}
				}
				$total = array_sum($moyens)/ count($moyens);
				$gradding = $gradding->getStdGrad($total, $term->marks);
			} elseif($this->calcType  == 'skills'){
				$results = array();
				$occurance = array();
				foreach($terms as $term){
					if($service != false ){
						$value = $term->get_mat_term_grad($service, $this->con, $this->con_id);
					} else {
						$value = $term->get_term_total_grad($service, $this->con, $this->con_id);
					}
					if(isset($occurance[$value])){
						$occurance[$value]++;
					} else {
						$occurance[$value] = 1;
					}
					if($value != false){
						for($i=0; $i< $service->coef; $i++){
							$results[] = $value;
						}
					}
				}
				arsort($occurence);
				$max = key($occurence);
				
				$gradding = new gradding($this->level_id);
				$grads = $gradding->getGraddinArray();
				if($grads != false){
					foreach($grads as $key => $value){
						if($key == $max){
							$total = $value;
							$gradding = $grad;
						}				
					}
				}
				
			}
			return array($total, $gradding);
		} else {
			return false;
		}
	} 
}
?>