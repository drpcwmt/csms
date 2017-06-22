<?php
/** Appreciation
*
*/

class appreciations{
	public $con = '', 
	$con_id='',
	$cur_term=false,
	$cur_service=false;

	private $thisTemplatePath = 'modules/marks/templates';
	
	public function __construct($con, $con_id){
		global $sms;
		$this->con = $con;
		$this->con_id = $con_id;
		$marks = new marks($con, $con_id);
		$obj = $sms->getAnyObjById($con, $con_id);
		$this->level_id = $obj->getLevel()->id;
		$this->terms = $marks->getTerms();
		$this->cur_term = $marks->getCurrentTerm();
		if($this->cur_term == false){
			$this->cur_term =  Terms::getTermByno($this->level_id , 1);
		}		
		$this->services = $marks->getEditableServices();
		$this->cur_service = $this->services[0];		
	}
	
	public function loadLayout(){
		global $lang;
		
		$layout = new Layout();
		$layout->con = $this->con;
		$layout->con_id = $this->con_id;
		$cur_service = $this->cur_service;
		$cur_term = $this->cur_term;
		$appr_toolbox = array();
		$appr_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'module="marks" action="saveAppr"',
			"text"=> $lang['save'],
			"icon"=> "disk"
		);
		$appr_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'module="marks" action="print_pre" rel="#appr_tabs_div-'.$this->con.'-'.$this->con_id.'"',
			"text"=> $lang['print'],
			"icon"=> "print"
		);
		$appr_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="saveAsPdf" rel="#appr_tabs_div-'.$this->con.'-'.$this->con_id.'"',
			"text"=> $lang['save_as_pdf'],
			"icon"=> "print"
		);
		$terms_array = array();
		if($this->terms != false && count($this->terms) > 0){
			foreach($this->terms as $term){
				$terms_array[$term->id] = $term->name;
			}
			
			$appr_toolbox[] = array(
				"tag" => "span",
				"attr"=> 'style="margin:0px 10px" class="ui-corner-all ui-state-default"',
				"text"=> write_html('text', 'style="padding: 4px"',$lang['term'].': ').
					write_html_select('name="terms" update="reloadAppr" class="ui-state-default def-float combobox" ',  $terms_array, $cur_term->id),
				"icon"=> ""
			);
			
			if($this->con != 'student'){
				$service_array = array('0' => $lang['general']);
				foreach($this->services as $service){
					$service_array[$service->id] = $service->getName();
				}
				$appr_toolbox[] = array(
					"tag" => "span",
					"attr"=> 'style="margin:0px 20px" class="ui-corner-all ui-state-default"',
					"text"=> write_html('text', 'style="padding: 4px"',$lang['term'].': ').
						write_html_select('name="services" update="reloadAppr" class="ui-state-default def-float combobox" ', $service_array, $cur_service->id),
					"icon"=> ""
				);	
			}
			$layout->toolbox = createToolbox($appr_toolbox);
			$layout->appr_table = write_html('div', 'class="appr_table_div"',
				($this->con == 'student' ?  $this->createStudentApprTable() : $this->createClassApprTable())
			);
		} else {
			$layout->appr_table =  write_error($lang['undefined_terms']);
		}
		return fillTemplate("$this->thisTemplatePath/appreciation.tpl", $layout);
	}

	public function createStudentApprTable(){
		global $lang;
		$student = new Students($this->con_id);
		$class_id = $student->getClass()->id;
		$cur_term = $this->cur_term;
		// the table head
		$appr_table_thead = write_html('tr', '',
			write_html('th', 'rowspan="2"  width="120" style="text-align:center"', $lang['material']).
			write_html('th', 'colspan="'.(count($this->terms)).'" width="'.(count($this->terms)*50).'" style="text-align:center"' , $lang['student']).
			write_html('th', 'colspan="3" width="150" style="text-align:center"' , $lang['class']).
			write_html('th', 'rowspan="2" width="160" style="text-align:center"',$lang['evaluation']).
			write_html('th', 'rowspan="2" style="text-align:center"', $lang['appreciations']. ' ('.$cur_term->name.')' )
		);
		
		$term_title ='';
		foreach($this->terms as $term_id => $term){
			$term_title .= write_html('th','width="50"  style="text-align:center"', $term->name);
		}
		
		$appr_table_thead .= write_html('tr', '',
			$term_title.
			write_html('th','width="50" style="text-align:center"',$lang['min']).
			write_html('th','width="50" style="text-align:center"',$lang['max']).
			write_html('th','width="50" style="text-align:center"',$lang['moyen'])
		);
	
		// table body
		$appr_table_tbody ='';
		foreach($this->services as $service){
			$calc_type = marks::getLevelCalcType($service->level_id);
			$gradding = new gradding(false, $service->gradding);
			$mat_exam_no = $service->exam_no; 
			$appr_table_tbody .= '<tr>';
			$optional = ($service->optional == '1') ? write_html('span', 'class="big_red_dot" title="'.$lang['optional'].'"', '*' ) : '';
			$bonus = ($service->bonus == '1')? write_html('span', 'class="big_blue_dot" title="'.$lang['bonus'].'"', '*' ) : '';
		
			$appr_table_tbody .= write_html('td', '', $service->getName(). $optional.$bonus );
			foreach($this->terms as $term){
				if($calc_type == 'moyen'){
					$cell_content = $term->get_mat_term_avg($service, $this->con, $this->con_id);
					$grad = '';
				} elseif($calc_type == 'points'){
					$cell_content = $term->get_mat_term_points($service, $this->con, $this->con_id);
					$grad = $term->approved == 1 ? $gradding->getStdGrad($cell_content, $term->marks) : '';
				} elseif($calc_type == 'per'){
					$cell_content = $term->get_mat_term_per($service, $this->con, $this->con_id);
					$grad = $term->approved == 1 ? $gradding->getStdGrad($cell_content, $term->marks) : '';
				} elseif($calc_type == 'skills'){
					$cell_content = $term->get_mat_term_grad($service, $this->con, $this->con_id);
					$grad = '';
				} 
				$appr_table_tbody .=  write_html('td','width="40" style="text-align:center; position:relative"',
					write_html('span', 'class="appr_grad" style="color:#'.($grad != false ? $grad->color : '').'"', ($grad != false ? $grad->title :'')).
					$cell_content
				);
			}
			
			
				// class evaluation
			$term->con = $this->con;
			$term->con_id = $this->con_id;
			$class_eval = $term->get_class_term_statics( $service) ;
			$appr_table_tbody .= write_html('td', 'style="text-align:center"', ($class_eval != false ? $class_eval->min : ''));
			$appr_table_tbody .= write_html('td', 'style="text-align:center"', ($class_eval != false ? $class_eval->max : ''));
			$appr_table_tbody .= write_html('td', 'style="text-align:center"', ($class_eval != false ? $class_eval->avg : ''));
				// appreciation
			$appr = do_query( "SELECT comments FROM terms_apprc WHERE std_id=$this->con_id AND service=$service->id AND term_id=$cur_term->id", DB_year);
				// student evolution
			$evolution = '<img class="hand" onclick="enlargeChart(this)" src="'.MS_fullpath.'index.php?module=marks&exams_chart&con=student&con_id='.$this->con_id.'&service_id='.$service->id.'&'.time().'.png" width="160" height="53" alt="'.$student->getName().'" />';
			$appr_table_tbody .= write_html('td','align="center"', $evolution);
			$appr_table_tbody .= write_html('td', '', $appr['comments']);
			$appr_table_tbody .= '</tr>';
		}
		return write_html('table', 'class="result"', 
			write_html('thead', '', $appr_table_thead) .
			write_html('tbody', '', 
				$appr_table_tbody
			) 
		);
	}

	public function createClassApprTable(){
		global $lang, $this_system;
		$cur_term = $this->cur_term;
		if($this->cur_service != false ){
			$level_id = $this->cur_service->level_id;
		} else {
			$obj = $this_system->getAnyObjById($this->con, $this->con_id);
			$level_id = $obj->getLevel()->id;
		}
		$calc_type = marks::getLevelCalcType($level_id);
		$appr_table_thead = write_html('tr', '',
			write_html('th', 'rowspan="2" class="unprintable" width="20" style="background-image:none"', '&nbsp;').
			write_html('th', 'rowspan="2" width="180" style="text-align:center"', $lang['students']).
			($this->cur_service != false ? 
				write_html('th', 'colspan="'.(count($this->terms)).'" style="text-align:center" width="'.(count($this->terms)*60).'"' , $lang['students'])
			: '').
			write_html('th', 'rowspan="2" width="160"', $lang['evaluation']).
			write_html('th', 'rowspan="2" style="text-align:center" ', $lang['appreciations']. ' ('.($cur_term != false ? $cur_term->name : $lang['year']).')' )
		);
		
		$term_title ='';
		if($this->terms != false && count($this->terms) > 0 && $this->cur_service != false){
			foreach($this->terms as $term){
				$term->con=$this->con;
				$term->con_id = $this->con_id;
				$class_eval = $term->get_class_term_statics( $this->cur_service) ;
				$term_title .= write_html('th','style="text-align:center" width="60"',
					$term->name.
					'<br>'.
					($class_eval != false ? round($class_eval->avg, 2) : '')
				);
			}
		}
		$appr_table_thead .= write_html('tr', '',
			$term_title
		);
		
		if($this->cur_service==false){
			$cur_service = false;
			$students = getStdIds($this->con, $this->con_id);
		} else {
			$cur_service = $this->cur_service;
			$exam = exams::searchExam($cur_service->id, $this->cur_term->id, 1, $this->con, $this->con_id);
			if(isset($exam->id)){
				$students = $exam->getStudent();
			} else {
				$students = $cur_service->getStudents();
			}
		}
		// the table body
		$appr_table_tbody = '';
		$gradding = new gradding($level_id);
		foreach($students as $std){
			$student = new Students($std);
			$std_name = $student->getName();
			$terms_td ='';
			if($this->cur_service!=false){
				foreach($this->terms as $term){	
					if($calc_type == 'moyen'){
						$cell_content = $term->get_mat_term_avg($cur_service, 'student', $std);
						$grad = '';
					} elseif($calc_type == 'points'){
						$cell_content = $term->get_mat_term_points($cur_service, 'student', $std);
						$grad = $term->approved == 1 ? $gradding->getStdGrad($cell_content, $term->marks) : '';
					} elseif($calc_type == 'per'){
						$cell_content = $term->get_mat_term_per($cur_service, 'student', $std);
						$grad = $term->approved == 1 ? $gradding->getStdGrad($cell_content, $term->marks) : '';
					} elseif($calc_type == 'skills'){
						$cell_content = $term->get_mat_term_grad($cur_service, 'student', $std);
						$grad = '';
					} 
					$terms_td .= write_html('td','style="position:relative; text-align:center" '.($term == $cur_term ? 'class="ui-state-active"' : ''),
						write_html('span', 'class="appr_grad" style="color:#'.($grad != false ? $grad->color : '').'"',($grad != false ?  $grad->title : '')).
						$cell_content
					);
				}
				// student evolution
				$evolution = '<img class="hand" onclick="enlargeChart(this)" src="'.MS_fullpath.'index.php?module=marks&exams_chart&con=student&con_id='.$std.'&service_id='.$cur_service->id.'&'.time().'.png"  width="160" height="53" title="'.$std_name.'" />';
				// appreciations
				$appr = do_query( "SELECT id, comments FROM terms_apprc WHERE std_id=$std AND service=$cur_service->id AND term_id=$cur_term->id", DB_year);

			} else {
				$evolution = '';
				
				$appr = do_query( "SELECT id, comments FROM terms_apprc WHERE std_id=$std AND service=0 AND term_id=$cur_term->id", DB_year);
			}
			// collectiong the tr
			$appr_table_tbody .= write_html('tr', '',
				write_html('td', 'class="unprintable"', 	
					'<input type="hidden" name="std_id[]" value="'.$std.'" />'.						
					write_html('button', 'class="ui-state-default hoverable circle_button" onclick="openStudentMarks('. $std.', \''.$std_name.'\')"',  write_icon('person'))
				).
				write_html('td','', $std_name).
				$terms_td.
				write_html('td','align="center" style="padding:0"', $evolution).
				write_html('td','', 
					( $this->cur_term->approved ==0  ? 
						write_html('textarea', 'name="appr_'.$std.'"', $appr['comments']) 
					: 
						write_html('p', '', $appr['comments'])
					)
				)
			);
			
		}
		return write_html('table', 'class="tableinput"', 
			write_html('thead', '', $appr_table_thead) .
			write_html('tbody', '', 
				$appr_table_tbody
			) 
		);
	}
	
	// Save Appreciations
	public function saveAppr($post){
		global $this_system, $lang;
		$error = false;
		if(getPrvlg('mark_edit')){
			$term_id = $post['terms'];
			$service_id = $post['services'];
			$mark = new Marks($_GET['con'], $_GET['con_id']);
			$editable_materials = $mark->getEditableServices();
			$obj = $this_system->getAnyObjById($_GET['con'], $_GET['con_id']);
			if($service_id=='0'  && $_GET['con'] == 'class' && 
				(($_SESSION['group']=='prof' && $obj->resp != $_SESSION['user_id'])) 
			){
				$error = $lang['no_privilege'];
			} elseif(in_array($_SESSION['group'], array('prof', 'supervisor')) &&  $service_id!='0' && object_in_array(new Services($service_id), $editable_materials ) == false){
				$error = $lang['no_privilege'];
			} else {
				for($i=0; $i<count($post['std_id']); $i++){
					$std_id = $post['std_id'][$i];
					$comments = $post["appr_$std_id"];
					$chk = count(do_query_array("SELECT comments FROM terms_apprc WHERE term_id=$term_id AND service=$service_id AND std_id=$std_id", DB_year));
					if($chk != false && $chk>0){
						$update = array('comments'=>$comments);
						$result = do_update_obj($update, "term_id=$term_id AND std_id=$std_id  AND service=$service_id", 'terms_apprc', DB_year);
						//$sql = "UPDATE terms_apprc SET comments='$comments' WHERE term_id=$term_id AND std_id=$std_id  AND service=$service_id";
					} else {
						$insert = array(
							'term_id'=>$term_id,
							'service'=>$service_id,
							'std_id'=>$std_id,
							'comments'=>$comments
							);
						$result = do_insert_obj($insert, 'terms_apprc', DB_year);
						//$sql = "INSERT INTO terms_apprc (term_id, service, std_id, comments) VALUES ($term_id, $service_id, $std_id, '$comments')";
					}
					if(!$result){
						$error = $lang['error_updating'];
					}
				}
			}
		}  else {
			$error = $lang['no_privilege'];
		}
		
			
		$answer = array();
		if(!$error){
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $error;
		}
		return json_encode($answer);		
	}
	
	static function getStdAppr($std_id, $term_id, $service_id){
		$result = do_query_obj("SELECT comments FROM terms_apprc WHERE term_id=$term_id AND service=$service_id AND std_id=$std_id", DB_year);
		return $result != false ? $result->comments : '';
	}
}
	
	


?>