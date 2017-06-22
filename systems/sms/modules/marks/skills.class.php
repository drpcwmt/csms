<?php
/** Skills
*
*/

class Skills{
	
	public function __construct($skill_id){
		global $lang,$sms;
		$this->DB_year = Db_prefix.$_SESSION['year'];
		if($skill_id != ''){	
			$skill = do_query_obj("SELECT * FROM materials_skills WHERE id=$skill_id");	
			if($skill->id != ''){
				foreach($skill as $key =>$value){
					$this->$key = $value;
				}
			}	
		}	
	}
	
	public function getName(){
		return $this->title;
	}
	
	public function getService($level_id){
		global $sms, $lang;
		$sub = do_query_obj("SELECT mat_id FROM materials_subs WHERE id=$this->sub_id");
		return Services::searchService($sub->mat_id, $level_id);
	}
	
	static function loadMainLayout($con, $con_id, $cur_term_id='', $cur_service_id=''){
		global $sms, $lang;
		$layout = new Layout();
		$layout->template = "modules/marks/templates/skill_main_layout.tpl";
		$layout->con = $con;
		$layout->con_id = $con_id;
		$object = $sms->getAnyObjById($con, $con_id);
		$marks = new Marks($con, $con_id);
		$services = $marks->getEditableServices();
		if($cur_service_id == ''){
			$cur_service = reset($services);
			$cur_service_id = $cur_service->id;
		} else {
			$cur_service = new Services($cur_service_id);
		}
		$terms = Terms::getTermsByCon($con, $con_id);
		if($cur_term_id == ''){
			$cur_term = $terms[0];
			$cur_term_id = $cur_term->id;
		} else {
			$cur_term = new Terms($cur_term_id);
		}
		$skills_toolbox = array();
		if($con == 'student'){
			$skills_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'module="marks" action="saveSkillsResults" std_id="'.$con_id.'"',
				"text"=> $lang['save'],
				"icon"=> "disk"
			);
			$skills_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'module="marks" action="openSkillReport" std_id="'.$con_id.'" ',
				"text"=> $lang['print'],
				"icon"=> "print"
			);
		} else {
			$skills_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="print_tab" rel="#skills_tabs_div-'.$con.'-'.$con_id.'"',
				"text"=> $lang['print'],
				"icon"=> "print"
			);
			$skills_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="saveAsPdf" rel="#skills_tabs_div-'.$con.'-'.$con_id.'"',
				"text"=> $lang['save_as_pdf'],
				"icon"=> "print"
			);
		}
		$terms_array = array();
		if($terms != false && count($terms) > 0){
			foreach($terms as $term){
				$terms_array[$term->id] = $term->name;
			}
			
			$skills_toolbox[] = array(
				"tag" => "span",
				"attr"=> 'style="margin:0px 10px" class="ui-corner-all ui-state-default"',
				"text"=> write_html('text', 'style="padding: 4px"',$lang['term'].': ').
					write_html_select('name="terms" update="reloadSkills" class="ui-state-default def-float combobox" ',  $terms_array, $cur_term_id),
				"icon"=> ""
			);
			
			
				$service_array = array();
				foreach($services as $service){
					$service_array[$service->id] = $service->getName();
				}
				$skills_toolbox[] = array(
					"tag" => "span",
					"attr"=> 'style="margin:0px 20px" class="ui-corner-all ui-state-default"',
					"text"=> write_html('text', 'style="padding: 4px"',$lang['material'].': ').
						write_html_select('name="services" update="reloadSkills" class="ui-state-default def-float combobox" ', $service_array, $cur_service_id),
					"icon"=> ""
				);	
		///	}
			if($con == 'student'){
				return write_html('form', '',
					'<input type="hidden" name="term_id" value="'.$cur_term_id.'" />'.
					'<input type="hidden" name="con" value="student" />
					<input type="hidden" name="con_id" value="'.$con_id.'" />'.
					createToolbox($skills_toolbox).
					write_html('div', 'class="skill_table_div"',
						Skills::createStudentSkillsTable($con_id, $cur_term, $cur_service)
					)
				);
			} else {
				$skills_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="printSkillsReport" con="'.$con.'" con_id="'.$con_id.'" title="'.$lang['print_all'].'"',
					"text"=> $lang['print_all'],
					"icon"=> "print"
				);
				$layout->toolbox = createToolbox($skills_toolbox);
				$layout->skill_table = write_html('div', 'class="skill_table_div"',
					Skills::createClassSkillsTable($con_id, $cur_term, $cur_service)
				);
				// per student table
				$layout->stds_trs = '';
				$obj = $sms->getAnyObjById($con, $con_id);
				$students = $obj->getStudents(array('1'));
				foreach($students as $student){
					$std_services =objectsToArray( $student->getServices());
					if(array_key_exists($cur_service_id, $std_services) === true){
						$layout->stds_trs .= write_html('tr', '',
							write_html('td', '', 
								write_html('button', 'type="button" class="circle_button ui-state-default hoverable" action="openSkillPerStd" std_id="'.$student->id.'"', write_icon('extlink'))
							).
							write_html('td', '', $student->getName()).
							write_html('td', '', 
								write_html('button', 'type="button" class="circle_button ui-state-default hoverable" action="openSkillReport" std_id="'.$student->id.'"', write_icon('print'))
							)
						);
					}
				}
			}
		} else {
			$layout->skill_table =  write_error($lang['undefined_terms']);
		}
		
		return $layout->_print();	
	}
	
	static function createStudentSkillsTable( $std_id, $cur_term, $cur_service){
		global $lang, $this_system;
	//	$cur_service = new Services(2);
		$student = new Students($std_id);
		$class_id = $student->getClass()->id;
		$form = new layout();
		$form->student_name = $student->getName();
		$form->template = "modules/marks/templates/skill_std_layout.tpl";
		$subs = $cur_service->getSubs();
		$gradding = $cur_service->getGradding();
		$grads_scale = $gradding->getGraddinArray();
		$results = do_query_array("SELECT * FROM services_skills_results WHERE std_id=$std_id AND term_id=$cur_term->id", $this_system->db_year);
		$res = array();
		foreach($results as $row){
			$res[$row->skill_id] = $row->result;
		}

		$items = array();
		$result_html = '';
		if($grads_scale != false){
			$i = 1;
			foreach($grads_scale as $scale){
				$result_html .= '<input type="radio" value="'.$scale->title.'" name="result-skill-[@skill_id]" [@result-'.$scale->title.'-selected] id="skill_result-'.$std_id.'-'.$cur_term->id.'-[@skill_id]-'.$scale->title.'"/>'.
				write_html('label', 'for="skill_result-'.$std_id.'-'.$cur_term->id.'-[@skill_id]-'.$scale->title.'" style="color:#'.$scale->color.'"', $scale->title);
				$i++;
			}
		}		

		// table body
		$skills_table_tbody ='';
		if($subs != false){
			foreach($subs as $sub){
				$trs = array();
				$skills = $cur_service->getSkills($sub->id, $cur_term->id);
			//	$skills = do_query_array("SELECT services_skills.* FROM services_skills, services_skills_terms WHERE services_skills.sub_id=$sub->id AND services_skills.id=services_skills_terms.skill_id AND services_skills_terms.term_id=$cur_term->id", $this_system->db_year);
				if($skills != false && count($skills) > 0){
					foreach($skills as $skill){
						$res_val = isset($res[$skill->id]) ? $res[$skill->id] : '';
						$trs[] = write_html('tr', '',
							write_html('td', '', 
								$skill->title.
								'<input type="hidden" name="skill_id[]" value="'.$skill->id.'" />'
							).
							write_html('td', '', 
								write_html('span', 'class="buttonSet"',
									str_replace('[@result-'.$res_val.'-selected]', 'checked="checked"', 
										str_replace("[@skill_id]", $skill->id, $result_html)
									)
								)
							)
						);
					}
					$skills_table_tbody .= write_html('tr', '',
						write_html('td', 'colspan="2" class="ui-state-default"', 
							write_html('h3', 'style="margin:5px 2px"', $sub->title)
						)
						
					).
					implode('', $trs);
				}
			}
		}

		// the table head
		$skill_table_thead = write_html('tr', '',
			write_html('th','',$lang['skills']).
			write_html('th','width="200" align="center"',$lang['results'])
		);
	
		
		
		return write_html('form', '',
			'<input type="hidden" name="con" value="student" />'.
			'<input type="hidden" name="con_id" value="'.$std_id.'" />'.
			'<input type="hidden" name="term_id" value="'.$cur_term->id.'" />'. 
			write_html('table', 'class="result"', 
				write_html('thead', '', $skill_table_thead) .
				write_html('tbody', '', 
					$skills_table_tbody
				) 
			)
		);
	}

	static function createClassSkillsTable( $class_id, $cur_term, $cur_service){
		global $lang, $this_system;
		$class = new Classes($class_id);
		$level_id = $cur_service != false ? $cur_service->level_id : $class->getLevel()->id;
		$gradding = $cur_service->getGradding();
		$grads_scale = $gradding->getGraddinArray();
		$services = $class->getServices();
		$skill_table_thead = write_html('tr', '',
			write_html('th', 'rowspan="2" class="{sorter:false}" width="20"', '&nbsp;').
			write_html('th', 'rowspan="2" style="text-align:center"', $lang['skills']).
			write_html('th', 'colspan="'.(count($grads_scale)).'" style="text-align:center" width="'.(count($grads_scale)*60).'"' , $lang['results'])
		);
		
		$term_title ='';
		if($grads_scale != false && count($grads_scale) > 0){
			foreach($grads_scale as $grad){
				$term_title .= write_html('th','style="text-align:center; color:#'.$grad->color.'" width="60"',
					$grad->title
				);
			}
		}
		$skill_table_thead .= write_html('tr', '',
			$term_title
		);
		
		$skills_table_tbody = '';
		$subs = $cur_service->getSubs();
		//$material = new Materials($cur_service->mat_id);
		foreach($subs as $sub){
			$skills = $cur_service->getSkills($sub->id, $cur_term->id);
			if($skills != false && count($skills) > 0){
				$skills_tr ='';
				foreach($skills as $skill){
					$tds =array();
					foreach($grads_scale as $grad){
						$count_result =  count(do_query_array("SELECT std_id FROM services_skills_results WHERE skill_id=$skill->id AND result LIKE '$grad->title' AND term_id=$cur_term->id", $this_system->db_year));
						$tds[] = write_html('td','style="text-align:center; color:#'.$grad->color.'" ',
							$count_result
						);
					}
					$skills_tr .= write_html('tr', '',
						write_html('td', '', 
							write_html('button', 'type="button" class="ui-state-default hoverable circle_button unprintable" action="openSkillsResults" skill_id="'.$skill->id.'" con="class" con_id="'.$class_id.'"', write_icon('extlink'))
						).
						write_html('td', '', 
							write_html('h4', 'style="margin:0px 12px"', $skill->title)
						).
						implode('', $tds)
					);
				}
				$skills_table_tbody .= write_html('tr', '',
					write_html('td', 'colspan="'.(count($grads_scale)+2).'" class="ui-state-default"', 
						write_html('h3', 'style="margin:5px 2px"', $sub->title)
					)
					
				).
				$skills_tr;
			}
		}
		// the table body
		return write_html('table', 'class="result"', 
			write_html('thead', '', $skill_table_thead) .
			write_html('tbody', '', 
				$skills_table_tbody
			) 
		);
	}
	
	public function LoadResultsForm( $con, $con_id, $term_id){
		global $lang, $this_system;
		$term_edit_provilege = getPrvlg('terms_edit');
		$results = do_query_array("SELECT * FROM services_skills_results WHERE skill_id=$this->id AND term_id=$term_id", $this_system->db_year);
		$res = array();
		foreach($results as $row){
			$res[$row->std_id] = $row->result;
		}
		$obj = $this_system->getAnyObjById($con, $con_id);
		$stds = $obj->getStudents(array('1'));
		$level = $obj->getLevel();
		$service = $this->getService($level->id);
		$gradding = $service->getGradding();
		$grads_scale = $gradding->getGraddinArray();
		$form = new Layout($this);
		$form->con_name = $obj->getName();
		$form->template = "modules/marks/templates/skill_results_form.tpl";
		$form->term_id = $term_id;
		$form->service_name = $service->getName();
		$sub = do_query_obj("SELECT title FROM materials_subs WHERE id=$this->sub_id");
		$form_sub_name = $sub->title;
		$form->std_table_tbody = '';
		$count = 1;
		// arrange student by results
		$trs = array();
		$result_html='';
		
		if($grads_scale != false){
			$i = 1;
			foreach($grads_scale as $scale){
				$result_html .= '<input type="radio" value="'.$scale->title.'" name="result-std-[@std_id]" [@result-'.$scale->title.'-selected] id="skill_result-'.$this->id.'-[@std_id]-'.$scale->title.'"/>'.
				write_html('label', 'for="skill_result-'.$this->id.'-[@std_id]-'.$scale->title.'" style="color:#'.$scale->color.'"', $scale->title);
				$i++;
			}
		}		

		foreach($stds as $student){
			// results td
			$std_service = objectsToArray($student->getServices());
			if(array_key_exists($service->id, $std_service)){
				$res_val = isset($res[$student->id]) ? $res[$student->id] : '';
				$trs[] = write_html('tr', '',
					write_html('td', 'width="14" align="center"', 
						$count.
						'<input type="hidden" name="std_id[]" value="'.$student->id.'" />'
					).
					write_html('td', '', $student->getName()).
					write_html('td', '', 
						write_html('span', 'class="buttonSet"',
							str_replace('[@result-'.$res_val.'-selected]', 'checked="checked"', 
								str_replace("[@std_id]", $student->id, $result_html)
							)
						)
					)
				);
				$count++;
			}
		}
		$form->std_table_tbody .= implode('', $trs);
		
		return $form->_print();
	}
	
	static function saveResults($post){
		global $sms, $lang;
		$result = true;
		$term_id = $post['term_id'];
		if(!isset($post['std_id'])){
			// student
			$std_id = $post['con_id'];
			foreach($post['skill_id'] as $skill_id){
				if(isset($post["result-skill-$skill_id"])){
					$insert = array(
						'std_id'=>$std_id,
						'skill_id' => $skill_id,
						'term_id'=> $term_id,
						'result'=>$post["result-skill-$skill_id"]
					);
					do_delete_obj("std_id=$std_id AND skill_id=$skill_id AND term_id=$term_id", 'services_skills_results', $sms->db_year);
					if(do_insert_obj($insert, 'services_skills_results', $sms->db_year) == false){
						$result = false;
					}
				}
			}
		} else {
			// Class or others
			$skill_id = $post['skill_id'];
			foreach($post['std_id'] as $std_id){
				if(isset($post["result-std-$std_id"])){
					$insert = array(
						'std_id'=>$std_id,
						'skill_id' => $skill_id,
						'term_id'=> $term_id,
						'result'=>$post["result-std-$std_id"]
					);
					do_delete_obj("std_id=$std_id AND skill_id=$skill_id AND term_id=$term_id", 'services_skills_results', $sms->db_year);
					if(do_insert_obj($insert, 'services_skills_results', $sms->db_year) == false){
						$result = false;
					}
				}
			}
			
		}
		
		return json_encode_result($result);
	}

	static function createReport( $student, $term, $service){
		global $lang, $this_system;
		$class_id = $student->getClass()->id;
		$subs = $service->getSubs();
		$items = array();
		$results = do_query_array("SELECT * FROM services_skills_results WHERE term_id=$term->id AND std_id=$student->id", $this_system->db_year);
		$res = array();
		foreach($results as $row){
			$res[$row->skill_id] = $row->result;
		}
		$gradding = $service->getGradding();
		$grads_scale = $gradding->getGraddinArray();
		$gr = array();
		foreach($grads_scale as $scal){
			$gr[$scal->title] = $scal->color;	
		}
		$skills_table_tbody = '';
		$count_skills = 0;
		if($subs != false){
			foreach($subs as $sub){
				$skills = $service->getSkills($sub->id, $term->id);	
				$count_skills += count($skills);		
				//$skills = do_query_array("SELECT services_skills.* FROM services_skills, services_skills_terms WHERE services_skills.sub_id=$sub->id AND services_skills.id=services_skills_terms.skill_id AND services_skills_terms.term_id=$term->id", $this_system->db_year);
				if($skills != false && count($skills) > 0){
					$trs = array();
					foreach($skills as $skill){
						if(isset($res[$skill->id])){
							$res_val = isset($res[$skill->id]) ? $res[$skill->id] : '';						
							$trs[] = write_html('tr', '',
								write_html('td', '', 
									$skill->title
								).
								write_html('td', 'align="center"', 
									write_html('span', 'style="font-weight:bolder; color:'.(isset($gr[$res_val]) ? $gr[$res_val] :'').'"',
										$res_val
									)
								)
							);
						}
					}
					$skills_table_tbody .= write_html('tr', '',
						write_html('td', 'colspan="2" class="ui-state-default"', 
							write_html('h3', 'style="margin:5px 2px"', $sub->title)
						)
						
					).
					implode('', $trs);
				}
			}
		}
		// the table body
		$skill_table_thead = write_html('tr', '',
			write_html('th','', '&nbsp;').
			write_html('th','width="120" align="center"',$lang['results'])
		);
		if($count_skills > 0){
			return write_html('h2', 'align="center"', $service->getName()).
			write_html('table', 'class="result"', 
				write_html('thead', '', $skill_table_thead) .
				write_html('tbody', '', 
					$skills_table_tbody
				) 
			);
		}
	}
	
	static function SkillReport($student, $service, $term){
		global $this_system;
		$report = new Layout();
		$report->template = "modules/marks/templates/skills_report.tpl";
		$report->student_name = $student->getName();
		$report->service_name = $service->getName();
		$report->term_name = $term->getName();
		$report->class_name = $student->getClass()->getName();
		// profsssssssssssssssssss
		$parents = getParentsArr('student', $student->id);
		if($parents != false && count($parents) > 0){
			foreach($parents as $array){
				$c = $array[0];
				$c_id = $array[1];
				$where[] = "(schedules_date.con='$c' AND schedules_date.con_id=$c_id)";
			}
		}
		$sql = "SELECT schedules_lessons.prof
		FROM schedules_date, schedules_lessons
		WHERE schedules_date.id = schedules_lessons.rec_id
		AND schedules_lessons.services=$service->id
		AND (
			(schedules_date.con='student' AND schedules_date.con_id=$student->id)".
			(count($where)>0 ? " OR ".implode(' OR ', $where) : '')
		.")";

		$lesson = do_query_obj($sql, $this_system->db_year);
		$prof = new Profs($lesson->prof);
		$report->prof_name = $prof->getName();
		
		$appr = do_query_obj( "SELECT comments FROM terms_apprc WHERE std_id=$student->id AND service=$service->id AND term_id=$term->id", $this_system->db_year);

		$report->appr_comments = $appr!= false ? $appr->comments : '';
		$report->appr_hidden = $appr== false || $appr->comments == '' ? 'hidden' : '';
		$subs = $service->getSubs();
		$gradding = $service->getGradding();
		$grads_scale = $gradding->getGraddinArray();
		$gr = array();
		foreach($grads_scale as $scal){
			$gr[$scal->title] = $scal->color;	
		}

		$results = do_query_array("SELECT * FROM services_skills_results WHERE std_id=$student->id AND term_id=$term->id ", $this_system->db_year);
		
		$res = array();
		foreach($results as $row){
			$res[$row->skill_id] = $row->result;
		}

		$items = array();
		$result_html = '';
		// table body
		$skills_table_tbody ='';
		if($subs != false){
			foreach($subs as $sub){
				$trs = array();
				$skills = $service->getSkills($sub->id, $term->id);
				if($skills != false && count($skills) > 0){
					foreach($skills as $skill){
						$res_val = isset($res[$skill->id]) ? $res[$skill->id] : '';
						$trs[] = write_html('tr', '',
							write_html('td', '', 
								$skill->title
							).
							write_html('td', 'align="center"',
								write_html('span', 'style="color:#'.(isset($gr[$res_val]) ? $gr[$res_val] : '').'; font-weight:bolder" ',
									$res_val
								)
							)
						);
					}
					$skills_table_tbody .= write_html('tr', '',
						write_html('td', 'colspan="2" class="ui-state-default"', 
							write_html('h3', 'style="margin:5px 2px"', $sub->title)
						)
						
					).
					implode('', $trs);
				}
			}
		}
		$report->std_table_tbody = $skills_table_tbody;
		return $report->_print();
	}
		
	static function deleteSkill($skill_id){
		global $lang;
		$answer = array();
		if(do_delete_obj("id=$skill_id", 'materials_skills', $this_system->database)){
			do_delete_obj("skill_id=$skill_id", 'services_skills_terms', $this_system->db_year);
			$answer['error'] ='';
		} else {
			$answer['error'] = $lang['error'];
		}
		return $answer;
	}
}