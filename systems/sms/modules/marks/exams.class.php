<?php
/** Services
*
*/

class exams{
	private $thisTemplatePath = 'modules/marks/templates';

	public function __construct($exam_id){
		$this->DB_year = Db_prefix.$_SESSION['year'];
		if($exam_id != ''){	
			$exam = do_query_obj("SELECT * FROM exams WHERE id=$exam_id", $this->DB_year);	
			if(isset($exam->id)){
				foreach($exam as $key =>$value){
					$this->$key = $value;
				}
			//	$this->results = $this->getExamResults();
				return $this;
			} else {
				return false;
			}	
		} else { return false;}
			
	}


	static public function searchExam($service_id, $term_id, $exam_no, $con, $con_id){
		$con_arr = array("(con='$con' AND con_id='$con_id')");
		$parents = getParentsArr($con, $con_id);
		if($parents != false){
			foreach($parents as $array){
				$par_con =$array[0];
				$par_id= $array[1];
				$con_arr[] = "(con='$par_con' AND con_id='$par_id')";
			};
		}
		
		$exam_sql = "SELECT * FROM exams 
			WHERE term_id = $term_id
			AND service = $service_id
			AND exam_no = $exam_no
			AND ( ".implode(' OR ', $con_arr).")
			ORDER BY con='student' DESC, con='group' DESC, con='class' DESC, con='level' DESC LIMIT 1";
		$exam = do_query_obj($exam_sql,  Db_prefix.$_SESSION['year']);
		if(isset($exam->id )){
			if($con == 'class' && $exam->con !='class' ){
				$copy_exam = $exam;
				unset($copy_exam->id);
				$copy_exam->con =$con;
				$copy_exam->con_id =$con_id;
				$new_exam_id = do_insert_obj($copy_exam, 'exams',  Db_prefix.$_SESSION['year']);

				return new exams($new_exam_id);
			} else {
				return new exams($exam->id );
			}
		} else {
			$term = new Terms($term_id);
			$level = new Levels($term->level_id);
			if($term->exam_no == '-1'){
				$exam = array(
					"con"=>$con,
					"con_id"=>$con_id,
					"term_id"=>$term_id,
					"service"=>$service_id,
					"exam_no"=> $exam_no
				);
				$new_exam_id = do_insert_obj($exam, 'exams',  Db_prefix.$_SESSION['year']);
				return new exams($new_exam_id);
			} else {
				return false;
			}
		}
	}
	
	public function getExamResults(){
		if(isset($this->results)){
			return $this->results;
		} else {
			$this->results = array();
			$results = do_query_array("SELECT * FROM exams_results WHERE exam_id=$this->id GROUP BY std_id",$this->DB_year);
			if(count($results) > 0){
				foreach($results as $res){
					$this->results[$res->std_id] = $res->results;
				}
			} else {
				$parents = getParentsArr($this->con, $this->con_id);
				if($parents != false){
					$con_arr = array("(exams.con='$this->con' AND exams.con_id='this->con_id')");
					foreach($parents as $array){
						$par_con =$array[0];
						$par_id= $array[1];
						$con_arr[] = "(exams.con='$par_con' AND exams.con_id='$par_id')";
					};
				
					$exam_sql = "SELECT exams_results.* FROM exams, exams_results
						WHERE exams.term_id = $this->term_id
						AND exams.service = $this->service
						AND exams.exam_no = $this->exam_no
						AND ( ".implode(' OR ', $con_arr).")
						AND exams.id=exams_results.exam_id";
						
					$results = do_query_array($exam_sql, $this->DB_year);
					if(count($results) > 0){
						foreach($results as $res){
							$this->results[$res->std_id] = $res->results;
						}
					}
				}
			}
			return $this->results;
		}
	}
	
	public function getCountResults(){
		$results = $this->getExamResults();
		return count($results);
	}

	public function getCountAbsents(){
		$results = $this->getExamResults();
		$i=0;
		foreach($results as $std_id=>$res){
			if($res==''){ $i++;}
		}
		return $i;
	}
	
	public function getStudent(){
		global $this_system;
		if(isset($this->students)){
			return $this->students;
		} else {
			$object = $this_system->getAnyobjById($this->con, $this->con_id);
			$service = new services($this->service);
			$service_stds = $service->getStudents();
			$out = array();
			foreach($object->getStudents() as $class_std){
				if(in_array($class_std->id, $service_stds)){
					$out[] = $class_std->id;
				}
			}
			return $out;
		}
	}

	public function loadLayout(){
		global $lang;
		$approve_privilge = getPrvlg('mark_approv');
		$tabs = array();
		$tabs['ul'] = array(
			write_html('li', '',write_html('a', 'href="#exam_results_tab-'.$this->id.'"', $lang['results']))
		);
		$tabs['div'] = array(
			write_html('div', 'id="exam_results_tab-'.$this->id.'"', 
				write_html('form', 'class="exam_result_form"', 
					($approve_privilge? 
						write_html('div', 'class="toolbox"', 
							write_html('a', 'action="appprovExam" class="ui-corner-all lock '.( $this->approved == '1' ? 'hidden':'').'" examid="'.$this->id.'"', 
								write_icon('locked').
								$lang['lock']
							).
							write_html('a', 'action="unAppprovExam" class="ui-corner-all unlock '.( $this->approved == '0' ? 'hidden':'').'" examid="'.$this->id.'"', 
								write_icon('unlocked').
								$lang['unlock']
							)
						)
					: '').
					$this->loadExamData(). 
					$this->loadResultsTable()
				)
			)
		);
		
		if(MSEXT_lms){
			$exerciseData = new stdClass();
			$exerciseData->service_id = $this->service;
			$exercise = new Exercises($exerciseData);
			
			if(isset($this->exercise_id) && $this->exercise_id != '' && $this->getCountResults() > 0){
				// exam done 
				$exercise->editable == false;
				$tabs['ul'][] =write_html('li', '',write_html('a', 'href="#exam_sheet_tab-'.$this->id.'"', $lang['exam_sheet']));
				$tabs['div'][] = write_html('div', 'id="exam_sheet_tab-'.$this->id.'"', loadExercise($this->exercise_id));
					// answers
				$tabs['ul'][] =write_html('li', '',write_html('a', 'href="#exam_answer_tab-'.$this->id.'"', $lang['exam_answers']));
				$tabs['div'][] = write_html('div', 'id="exam_answer_tab-'.$this->id.'"', $exercise->loadExerciseAnswer($exercise_id));
			} elseif(isset($this->exercise_id) &&  $this->exercise_id != '' && $this->getCountResults() == false) {
				// exam seted but not answered so prepare to update 
				$tabs['ul'][] =write_html('li', '',write_html('a', 'href="#exam_sheet_tab-'.$this->id.'"', $lang['exam_sheet']));
				$tabs['div'][] = write_html('div', 'id="exam_sheet_tab-'.$this->id.'"', loadEditExercise($this->exercise_id));
			} elseif(isset($this->exercise_id) && $this->exercise_id == '' && $this->getCountResults() == false){
				// new exam 
				$tabs['ul'][] =write_html('li', '',write_html('a', 'href="#exam_sheet_tab-'.$this->id.'"', $lang['exam_sheet']));
				$tabs['div'][] = write_html('div', 'id="exam_sheet_tab-'.$this->id.'"', $exercise->loadSearchView());
			}
		}
		
		if(count($tabs['ul']) > 1){
			return write_html('div', 'class="tabs"',
				write_html('ul', '',
					implode('', $tabs['ul'])
				).
				implode('', $tabs['div'])
			);
		} else {
			return $tabs['div'][0];
		}
	}
	
	public function loadExamData(){
		global $lang;
		$service = new services($this->service);
		$calcType = marks::getLevelCalcType($service->level_id);
		$term_edit_prvlg = getPrvlg('terms_edit');
		
		$data = new stdClass();
		$data->exam_id = $this->id;
		$data->con = $this->con;
		$data->con_id = $this->con_id;
		$data->approved = $this->approved;
		$data->approved_img = write_html('h2', 'class="approved_tag '.($this->approved != 1 ? 'hidden' : '').'"', '<img src="assets/img/success.png" style="vertical-align:middle"/>'. $lang['approved']);
		$data->date = $this->date > 0 ? unixToDate($this->date) : '';
		$data->date_disabled = $this->approved || ($this->date > 0  && $term_edit_prvlg == false) ? 'disabled="disabled"' : '';
		$data->title = $this->title != '' ? $this->title : $lang['exam'].'-'.$this->exam_no ;
		$data->title_disabled = $this->title != '' && $term_edit_prvlg == false ? 'disabled="disabled"' : '';
		$data->service_name = $service->getName();
		$data->exam_no = $this->exam_no;
		$thisTerm = new terms($this->term_id);
		$data->term_title = $thisTerm->name;
		$data->min = $this->min;
		$data->min_disabled = $this->approved || ($this->min != '' && $term_edit_prvlg == false) ? 'disabled="disabled"' : '';
		$data->max = $this->max ;	
		$data->max_disabled = $this->approved || ($this->max > 0  && $term_edit_prvlg == false) ? 'disabled="disabled"' : '';	
		$data->students_count = count($this->getExamResults()) > 0 ? count($this->getStudent()) - $this->getCountAbsents() .'/'. count($this->getStudent()) : count($this->getStudent());
		$gradding = new gradding($service->level_id);
		$exam_statics = $this->getStatics();
		$grd_res = $gradding->getStdGrad($exam_statics->avg, $this->max);
		$data->avrage =count($this->getExamResults()) > 0 ? $exam_statics->avg.' / '. write_html('span', 'style="color:#'.$grd_res->color.'"', $grd_res->title) : '';
		if($calcType == 'moyen' || $calcType == 'skills'){
			$data->value_td_label = write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['coeffcient']);
			$data->value_td_input = '<input type="text" name="coef" value="'.($this->coef > 1 ? $this->coef : 1 ).'" '.($this->approved || $term_edit_prvlg ==false ? 'disabled="disabled"' : '').' />';
		} elseif($calcType == 'per') {
			$data->value_td_label = write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['value']);
			$data->value_td_input = '<input type="text" name="value" value="'.$this->value.'"'.($this->approved || $term_edit_prvlg ==false ? 'disabled="disabled"' : '').' />';
		}
		return fillTemplate("$this->thisTemplatePath/exam_data.tpl", $data);
		
	}
		
	public function loadResultsTable(){
		global $lang, $this_system;
		$term_edit_provilege = getPrvlg('terms_edit');
		$results = $this->getExamResults();
		arsort($results);
		$stds = $this->getStudent();
		$service = new services($this->service);
		$calc_type = marks::getLevelCalcType($service->level_id);
		$gradding = new gradding(false, $service->gradding);
		$grads_scale = $gradding->getGraddinArray();
		//print_r($grads_scale);
		$std_table_tbody = '';
		$count = 1;
		// arrange student by results
		$arranged_std = array();
		foreach($results as $std_id =>$res){
			if(in_array($std_id, $stds)){
				$arranged_std[] = $std_id;
				if(($key = array_search($std_id, $stds)) !== false) {
					unset($stds[$key]);
				}
			}
		}
		$arranged_std = array_merge($arranged_std, $stds);

		foreach($arranged_std as $std_id){
			// results td
			$res_val = isset($results[$std_id]) ? $results[$std_id] : '';
			if($calc_type == 'skills'){
				$result_html = '<select name="result_'.$std_id.'" style="width:75px" class="result" '.($this->approved== 1 ? 'disabled="disabled"' : '').'>
					<option value="-1"></option>';
				if($grads_scale != false){
					$i = 1;
					foreach($grads_scale as $scale){
						$result_html .= write_html('option', 'value="'.$i.'" style="background-color:'.$scale->color.'" '.($res_val==$i ? 'selected="selected"' : ''), $scale->title);
						$i++;
					}
				}
				$result_html .= '</select>';
			} else {
				$result_html = '<input type="text" class="result input_half no-corner" name="result_'.$std_id.'" value="'.$res_val.'" '.($this->approved== 1 ? 'disabled="disabled"' : '').' />';
			}


			// Trs
			$tr = array(
				write_html('td', 'width="14" align="center"', 
					$count.
					'<input type="hidden" name="stds[]" value="'.$std_id.'" />'
				),
				write_html('td', '', $this_system->getAnyNameById('student', $std_id)),
			);
			
			if($calc_type != 'skills' ){
				$grd_res = $gradding->getStdGrad($res_val, $this->max);
				$tr[] =	write_html('td', 'align="center" valign="middel" style="text-align:center;padding:0px;"', $result_html);
				$tr[] =	write_html('td', 'align="center" valign="middel" style="text-align:center; font-weight:bold"', 
					(isset($results[$std_id]) ? 
						$results[$std_id] != "" ? write_html('span', 'style="color:#'.$grd_res->color.'"', $grd_res->title) : $lang['abs']
					: '')
				);
			} else {
				$tr[] =	write_html('td', 'align="center" valign="middel" style="text-align:center; font-weight:bold"', $result_html);
			}
			
			//collect
			$std_table_tbody .= write_html('tr', '',implode('', $tr));
			$count++;
		}
		
		return write_html('table', 'class="tableinput"', 
			write_html('thead', '',
				write_html('tr','',
					write_html('th', 'style="background-color:none" width="16"', '&nbsp;').
					write_html('th', '', $lang['name']).
					($calc_type != 'skills' ?
						write_html('th', 'width="78"', $lang['points'])
					: '').
					write_html('th', 'width="78"', $lang['grading'])
				)
			).
			write_html('tbody', '',
				$std_table_tbody
			)
		);
	}
	
	public function get_exam_avrage(){
		if(isset($this->average)){
			return $this->average;
		} else {
			$avg = do_query("SELECT AVG(results) FROM exams_results WHERE exam_id =$this->id AND `results` IS NOT NULL", $this->DB_year) ;
			$this->average = round($avg['AVG(results)'],1);
			return $this->average;
		}
	}

	public function getCalcType(){
		if(isset($this->calcType)){
			return $this->calcType;
		} else {
			$service = new services($this->service);
			return marks::getLevelCalcType($service->level_id);
		}
	}
	
	public function getStatics(){
		$out = new StdClass();
		$out->min =  0;
		$out->max =  0;
		$out->avg =  0;
		$results = $this->getExamResults();
		$service = new services($this->service);
		
		if($results){
			foreach($results as $std_id => $res ){
				if($res == NULL){
					unset($results[$std_id]);
				}
			}
			if(count($results) > 0){
				if($this->getCalcType() == 'skills'){
					$gradding = new gradding($service->level_id);
					$grads_scale = $gradding->getGraddinArray();
					if($grads_scale != false){
						$occurence = array();
						$i = 1;
						foreach($grads_scale as $key => $value){
							$res = do_query_array("SELECT * FROM exams_results WHERE exam_id=$this->id AND results=$i", $this->DB_year);
							$occurence[$key] = count($res);
							$i++;
						}
						asort($occurence);
						$min = key($occurence);
						arsort($occurence);
						$max = key($occurence);
						$avg =  array_search ($occurence[round(count($occurence)/2)], $occurence);
						$out->min = $grads_scale[$min]->title;
						$out->max = $grads_scale[$max]->title;
						$out->avg = $grads_scale[$avg]->title;
					}
					//return $out;
				} else {
					$avg = array_sum($results)/count($results);
					$max = max($results);
					$min = min($results);
					$out->min =  round($min, 1);
					$out->max =  round($max, 1);
					$out->avg =  round($avg, 1);
				}
			}
		} 
		return $out;
//		$sql = "SELECT MIN(exams_results.results) AS min, MAX(exams_results.results) AS max, AVG(exams_results.results) AS avg
//		FROM exams, exams_results 
//		WHERE exams.id=$this->id
//		AND exams.id=exams_results.exam_id
//		AND exams_results.result IS NOT NULL";
//		$results = do_query_obj($sql, $this->DB_year);
//		if(!isset($results->min)){
//			 $results->min =  0;
//			 $results->max =  0;
//			 $results->avg =  0;
//		};
//		return $results;
	}
	
	// exam avg (grad)
	public function getExamGradingAvrg(){
		$results = $this->getExamResults();
		if(count($results)>0){
			$service = new services($this->service);
			$calc_type = marks::getLevelCalcType($service->level_id);
			$gradding = new gradding($service->level_id);
			$grads_scale = $gradding->getGraddinArray();
			if($grads_scale != false){
				$occurence = array();
				$i = 1;
				foreach($grads_scale as $key => $value){
					$results = do_query_resource("SELECT * FROM exams_results WHERE exam_id=$this->id AND results=$i", $this->DB_year);
					$occurence[$key] = mysql_num_rows($results);
					$i++;
				}
				arsort($occurence);
		
				$key_of_max = key($occurence);
				return $grads_scale[$key_of_max]->title;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function approveExam(){
		$error = false;
		if(getPrvlg('mark_approv')){
			if(!do_query_edit("UPDATE exams SET approved=1 WHERE id=$exam_id", $this->DB_year)){
				$error = $lang['error_updating'];
			} 
		} else {
			$error = $lang['no_privilege'];
		}
		$answer = array();
		if(!$error){
			$answer['id'] = $exam_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $error;
		}
		return json_encode($answer);
	}

	public function unApproveExam($exam_id){
		$error = false;
		if(getPrvlg('mark_approv')){
			if(!do_query_edit("UPDATE exams SET approved=0 WHERE id=$exam_id", $this->DB_year)){
				$error = $lang['error_updating'];
			}
		} else {
			$error = $lang['no_privilege'];
		}
		$answer = array();
		if(!$error){
			$answer['id'] = $exam_id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $error;
		}
		return json_encode($answer);
	}

	static function submitExam($post){
		$error = false;
		if(getPrvlg('mark_edit')){	
			// get term id
			$exam_id = $post['id'];			
			$exam_sql = "SELECT exams.* FROM exams WHERE id=$exam_id ";
			$exam = do_query_obj( $exam_sql, Db_prefix.$_SESSION['year']);
			if($exam->approved != 1){
				$min =(isset($post['min']) && $post['min'] != '') ? $post['min'] : $exam->min; 
				$max = (isset($post['max']) && $post['max'] != '') ? $post['max'] : $exam->max; 
				$date = (isset($post['date']) && $post['date'] != '') ? dateToUnix($post['date']) : $exam->date; 
				$coef = (isset($post['coef']) && $post['coef'] != '') ? $post['coef'] : $exam->coef; 
				$title = (isset($post['title']) && $post['title'] != '') ? $post['title'] : $exam->title; 
				$value = isset($post['value']) ? $post['value']: $exam->value;
			
				if($exam_id != ''){
					$sql_term = "UPDATE exams SET 
					min='$min', 
					max='$max', 
					`date`='$date',
					coef='$coef',
					title='$title',
					value='$value' 
					WHERE id=$exam_id";
				} 
				//echo $sql_term.'<br />'.dateToUnix($post['date']);
				if(!do_query_edit( $sql_term, Db_prefix.$_SESSION['year'])){
					$error = 'Error : cant update exam!.';
				} else {
					if(isset($post['stds']) && count($post['stds']) > 0){
						do_query_edit("DELETE FROM exams_results WHERE exam_id=$exam_id", Db_prefix.$_SESSION['year']);
						for($i=0; $i<count($post['stds']); $i++){
							$std_id = $post['stds'][$i];
							$result = $post["result_$std_id"]!= '' ? $post["result_$std_id"] : 'NULL';
							if(!do_query_edit("INSERT INTO exams_results (exam_id, std_id, results) VALUES ($exam_id, $std_id, $result)", Db_prefix.$_SESSION['year'])){
								$error = $lang['error_updating'];
							}
						}
					}
				}
			} else {
				$error = $lang['exam_allready_approved'];
			}
		} else {
			$error = $lang['no_privilege'];
		}
		
		$answer = array();
		if(!$error){
			$answer['id'] = '';
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $error;
		}
		return json_encode($answer);
	}

	static function getNextExam($service_id){
		$cur_term = terms::getCurentTerm('class', $_SESSION['cur_class']);
		$exam = do_query_obj("SELECT id FROM exams WHERE service=$service_id AND term_id=$cur_term->id AND approved=0 AND date>NOW() LIMIT 1", Db_prefix.$_SESSION['year']);
		if(isset($exam->id)){
			return new exams($exam->id);
		} else {
			$exam = do_query_obj("SELECT id FROM exams WHERE service=$service_id AND term_id=$cur_term->id AND approved=0 ORDER BY exam_no ASC LIMIT 1", Db_prefix.$_SESSION['year']);
			if(isset($exam->id)){
				return new exams($exam->id);
			} else {
				return false;
			}
		}
	}
	
	static function presetExamsForm($con, $con_id, $cells){
		global $lang;
		$mono = false;
		$seek_exams = false;
		$obj = getAnyObjById($con, $con_id);
		$level = $obj->getLevel();
		$calc_type = Marks::getLevelCalcType($level->id);
		if(count($cells) == 1){
			$e = explode('-', $cells[0]);
			$service_id = $e[0];
			$term_id = $e[1];
			$exam_no = $e[2];
			$level_exams= do_query("SELECT * FROM exams WHERE term_id=$term_id AND exam_no=$exam_no AND con='level' AND service=$service_id", DB_year);
			if($level_exams['id'] != ''){
				$seek_exams= true;
			}
			$term = do_query( "SELECT title FROM terms WHERE id=$term_id", DB_year);
			$service = new Services($service_id);
			$mono = true;
		}
		$exam_info_table = write_html('form', 'id="exam_form" class="ui-corner-all ui-state-highlight" style="padding:5px; margin-bottom:5px"',
			'<input type="hidden" name="con" value="'.$con.'" />'.
			'<input type="hidden" name="con_id" value="'.$con_id.'" />'.		
			'<input type="hidden" name="cells" value="'.implode(',', $cells).'" />'.
			write_html('table', 'width="100%" border="0" cellspacing="0"',
				($mono ? 
					write_html('tr', '', 
						write_html('td', 'valign="middel" class="reverse_align"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['term'])
						).
						write_html('td', '', 
							write_html('span', 'class="ui-widget-content ui-corner-right fault_input"',$term['title'])
						)		
					).
					write_html('tr', '',
						write_html('td', 'valign="middel" class="reverse_align"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['material'])
						).
						write_html('td', '', 
							write_html('span', 'class="ui-widget-content ui-corner-right fault_input"',$service->getName())
						)
					)
				: '').
				write_html('tr', '',
					write_html('td', 'valign="middel" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['title'])
					).
					write_html('td', '','<input type="text" name="title" value="'.($seek_exams ? $level_exams['title'] : ($mono ? $lang['exam'].'-'.$exam_no : '') ).'" />')		
				).
				write_html('tr', '', 
					write_html('td', 'valign="middel" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['date'])
					).
					write_html('td', '', '<input type="text" name="date" value="'.(($seek_exams && $level_exams['date'] !=0) ?  unixToDate($level_exams['date']) :'' ).'" class="datepicker mask-date" />')
				).
				write_html('tr', '', 
					write_html('td', 'valign="middel" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['max'])
					).
					write_html('td', '', '<input type="text" name="max" class="input_half" value="'.($seek_exams ?  $level_exams['max'] :'' ).'" />')
				).
				write_html('tr', '', 
					write_html('td', 'valign="middel" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['min'])
					).
					write_html('td', '', '<input type="text" name="min" class="input_half" value="'.($seek_exams  ?  $level_exams['min'] :'' ).'"/>')
				).
				write_html('tr', '', 
					($calc_type == 'moyen' ?
						write_html('td', 'valign="middel" class="reverse_align"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['coeffcient'])
						).
						write_html('td', '', '<input type="text" name="coef" class="input_half" value="'.(($seek_exams && $mono) ?  $level_exams['coef'] :'1' ).'" />')
					:  
						($calc_type == 'per' ?
							write_html('td', 'valign="middel" class="reverse_align"', 
								write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['value'])
							).
							write_html('td', '', '<input type="text" name="value" class="input_half" value="'.(($seek_exams && $mono) ?  $level_exams['value'] :'1' ).'" />'. Marks::getCalcSpan($calc_type))
						: 
							write_html('td', '', '').
							write_html('td', '', '')
						)
					)		
				)
			)
		);
	
		return $exam_info_table;
	}
	
	static function savePreset($post){
		$error = false;
		$cells = explode(',', $post['cells']);
		foreach($cells as $exam){
			$vals = $_POST;
			$e = explode('-', $exam);
			$vals['service'] = $e[0];
			$vals['term_id'] = $e[1];
			$vals['exam_no'] = $e[2];
			$vals['date'] = dateToUnix($vals['date']);;
			$chk = do_query_obj("SELECT id FROM exams WHERE service='".$vals['service']."' AND term_id='".$vals['term_id']."' AND exam_no='".$vals['exam_no']."' AND con='level'", DB_year);
			if(isset($chk->id)){ 
				if(!UpdateRowInTable("exams", $vals, "id=".$chk->id, DB_year)){
					$error = true;
				}
			} else { // new exams
				if(insertToTable("exams", $vals, DB_year) == false){
					$error = true;
				}
			}
		}
		$answer = array();
		if(!$error){
			$answer['id'] = '';
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
}
