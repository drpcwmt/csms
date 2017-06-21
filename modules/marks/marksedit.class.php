<?php
/* Class Marks Edit
*
*/

class MarksEdit extends Marks{
	
	private $thisTemplatePath = 'modules/marks/templates';
	
	public function __construct($con, $con_id){
		parent::__construct($con, $con_id);
	}
	
	public function loadEditLayout(){
		global $lang;
		$terms = $this->getTerms();
		// no terms defined
		if($terms == false || count($terms) == 0){
			return $this->resetForm();
		} else {
			// look for current or first term
			$thisTerm = $this->getCurrentTerm();
			if($thisTerm == false){
				$thisTerm =  Terms::getTermByno($this->level_id , 1);
			}
			// check if dates are seted
			if($thisTerm->begin_date == ''){
				return write_html('form', 'style="margin-bottom:10px"', $this->getEditTermsForm());
			} else {
				// load terms exam table
				$layout = new stdClass();
				$layout->con = $this->con;
				$layout->con_id = $this->con_id;
				$layout->mark_table =  $this->createMarksTable(0, true);
				$layout->terms_table = $this->getEditTermsForm();
				$layout->addons_table = $this->getAddons();

				$templates = array();
				$templates_array = scandir('modules/marks/certificates');
				foreach($templates_array as $templ){
					if(!in_array($templ, array('.', '..'))){
						$template_name = str_replace('.php', '', $templ);
						$templates[$template_name] = $template_name;
					}
				}
				$level = new Levels($this->level_id);
				// gradin option html
				$gradin_opts = array("" => $lang['not_used']);
				$graddings = do_query_array( "SELECT * FROM gradding ORDER BY name ASC", DB_student);
				foreach($graddings  as $gradding){
					$gradin_opts[$gradding->id] = $gradding->name;
				}
				$layout->settings = write_html('fieldset', 'class="ui-state-highlight ui-corner-all"', 
					write_html('legend', '', $lang['settings']).
					write_html('table', '', 
						write_html('tr', '',
							write_html('td', ' valign="top" class="reverse_align"', 
								write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['gradding_shell'])
							).
							write_html('td', '', 
								write_html('button', 'onclick="newGrading()" class="ui-state-default hoverable def_float" style="padding:2px"', 
									write_icon('plus')
								).
								write_html('button', 'onclick="viewGrading()" class="ui-state-default hoverable def_float" style="padding:2px"', 
									write_icon('extlink')
								).
								write_html_select('name="grading" id="grading_list" class="combobox"', $gradin_opts, $this->gradding->id)
							)
						).
						write_html('tr', '',
							write_html('td', ' valign="top" class="reverse_align"', 
								write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['certificate_template'])
							).
							write_html('td', '', 
								write_html_select('name="cert_templ" id="cert_templ" class="combobox"', $templates, $level->cert_templ)
							)
						)
		
					)
				);
				

				$marks_toolbox = array();
				$marks_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="marks" action="resetTerms" con="'.$this->con.'" conid="'.$this->con_id.'"',
					"text"=> $lang['reset'],
					"icon"=> "arrowrefresh-1-s"
				);
				$marks_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="marks" action="selectAllExams"',
					"text"=> $lang['select_all'],
					"icon"=> "check"
				);
				$marks_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="marks" action="loadExamInfo" con="'.$this->con.'" conid="'.$this->con_id.'"',
					"text"=> $lang['preset_exam'],
					"icon"=> "calendar"
				);
				$marks_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="marks" action="addAddOn" term="'.$thisTerm->id.'" con="'.$this->con.'" conid="'.$this->con_id.'"',
					"text"=> $lang['addons'],
					"icon"=> "plus"
				);
				$marks_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="marks" action="print_pre" rel="#mark_edit_div-'.$this->con.'-'.$this->con_id.'"',
					"text"=> $lang['print'],
					"icon"=> "print"
				);
				$layout->toolbox = createToolbox($marks_toolbox);
				
				$addons_toolbox = array();
				$addons_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="marks" action="addAddOn" term="0" con="'.$this->con.'" conid="'.$this->con_id.'"',
					"text"=> $lang['reset'],
					"icon"=> "arrowrefresh-1-s"
				);
				$addons_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'module="marks" action="print_pre" rel="#mark_edit_div-'.$this->con.'-'.$this->con_id.'"',
					"text"=> $lang['print'],
					"icon"=> "print"
				);
				$layout->addons_toolbox = createToolbox($addons_toolbox);
				
				return fillTemplate("$this->thisTemplatePath/marks_edit.tpl", $layout);
			}
		}

	}
	
	public function getEditTermsForm(){
		global $lang;
		$year_begin_day = getYearSetting('begin_date');
		$year_end_day = getYearSetting('end_date');
		$terms = $this->getTerms();
		$terms_trs = array();
		$i = 1;

		
			// Terms Trs
		foreach($terms as $term){
			if($term->exam_no > 0) { $exam_th = true;} else {$exam_th = false;}
			$terms_trs[] = write_html('tr','',
				write_html('td', 'width="16" align="center"', 
					'<input type="hidden" name="term_no[]" value="'.$term->term_no.'"/>'.
					'<input type="hidden" name="term_id[]" value="'.$term->id.'"/>'.
					$term->term_no
				).
				write_html('td', '', '<input type="text" name="title[]"  value="'.($term->title != '' ? $term->title : $lang['order_'.$i]).'"/>').
				write_html('td', '', '<input type="text" class="datepicker mask-date" name="begin_date[]" style="width:70px" value="'.($term->begin_date!= '0' ? unixToDate($term->begin_date) :  ($i ==1 ? unixToDate($year_begin_day) : '')).'" />').
				write_html('td', '', '<input type="text" class="datepicker mask-date" name="end_date[]" style="width:70px" '.($i!=count($terms) ?'onchange="revalueNextBegin(this)"' : '').' value="'.($term->end_date!= '0' ? unixToDate($term->end_date) :  ($i ==count($terms) ? unixToDate($year_end_day) : '')).'"/>').
				((in_array($this->calcType, array("per", "marks"))) ? write_html('td', '', '<input type="text" name="marks[]" style="width:30px" value="'.$term->marks .'" /><span class="calc_span">'.$this->calcSpan.'</span>') : '').
				($term->exam_no > 0 ? 
					write_html('td', '', 
						'<input type="text" name="exam_no[]" style="width:30px" value="'.$term->exam_no.'" />'
					)
				: '')
			);
			$i++;
		}

		
		
		$out = '<input name="applyto" type="hidden" value="'.(isset($_POST['applyto']) ? $_POST['applyto'] : 'level').'" />'.
		write_html('table', 'class="tableinput"', 
			write_html('thead','',
				write_html('tr', '',
					write_html('th', 'width="16"', '').
					write_html('th', '', $lang['title']).
					write_html('th', 'width="80"', $lang['begin_date']).	
					write_html('th', 'width="80"', $lang['end_date']).
					((in_array($this->calcType, array("per", "marks"))) ? write_html('th', 'width="60"', $lang['marks_portion']) : '').
					($exam_th ? write_html('th', 'width="40"', $lang['exams']) : '')
				)
			).
			write_html('tbody','', implode('', $terms_trs))
		);

		return $out;
	}

	public function saveSettings($post){
		global $lang;
		$error = false;
		$vals =array();
		if(isset($post['cert_templ'])){
			$cert_templ = $post['cert_templ'];
			$gradding = $post['grading'];
			do_query_edit("UPDATE levels SET cert_templ='$cert_templ', gradding='$gradding' WHERE id=$this->level_id", DB_student);
		}
		for($i=0; $i< count($post['term_no']); $i++){
			$values =array();
			$values['term_no'] = $post['term_no'][$i];
			$values['title'] = $post['title'][$i];
			$values['begin_date']  = $post['begin_date'][$i];
			$values['end_date']  = $post['end_date'][$i];
			if(isset($post['marks'][$i])){
				$values['marks'] =  $post['marks'][$i];
			}
			if(isset($post['exam_no'][$i])){
				$values['exam_no'] =  $post['exam_no'][$i];
			}
			$vals[] = $values;
		}
			
		$applyto = $post['applyto'];
		if($applyto == 'level'){
			foreach($vals as $values){
				if(!UpdateRowInTable("terms", $values, "term_no=".$values['term_no']." AND level_id=$this->level_id", DB_year)){
					$error = true;
				}
			}
		} elseif($applyto == 'etab'){
			$level = new Levels($this->level_id);
			$etab = $level->getEtab();
			foreach($etab->getLevelList() as $lvl){
				foreach($vals as $values){
					if(!UpdateRowInTable("terms", $values, "term_no=".$values['term_no']." AND level_id=".$lvl->id, DB_year)){
						$error = true;
					}
				}
			}
		} elseif($applyto == 'school'){
			foreach($vals as $values){
				if(!UpdateRowInTable("terms", $values, "term_no=".$values['term_no'], DB_year)){
					$error = true;
				}
			}
		}
		$answer = array();
		if($error == false){
			$answer['id'] = "";
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = 'Error';
		}
		return json_encode($answer);
				
	}
	
	public function getAddons(){
		global $lang;
			// Addons
		$terms_trs = array();
		$addons = do_query_array("SELECT * FROM marks_addon WHERE level_id=$this->level_id", DB_year);
		if(count($addons) > 0){
			foreach($addonsas as $add){
				$terms_trs[] = write_html('tr', '',
					write_html('td', 'class="unprintable"',
						write_html('button', ' title="'.$lang['edit'].'" class="ui-state-default hoverable circle_button"  action="openAddInfos" addid="'. $add->id.'"',  write_icon('pencil'))
					).
					write_html('td', 'class="unprintable"',
						write_html('button', ' title="'.$lang['edit'].'" class="ui-state-default hoverable circle_button" action="deleteAddInfos" addid="'. $add->id.'"',  write_icon('close'))
					).
					write_html('td', '', $add['name']).
					write_html('td', '', 
						($this->calcType == 'moyen' ? 
							$add->coef
						: 
							($this->calcType == 'marks' || $this->calcType == 'per' ?
								$add->value 
							: '&nbsp;')
						).$this->calcSpan
					)
				);
			}
		}
		$out = write_html('form', '', 
			write_html('div', ' style="margin-bottom:10px"',
				write_html('table', 'class="result"', 
					write_html('thead','',
						write_html('tr', '',
							write_html('th', 'width="24" class="bg_none unprintable"', '').
							write_html('th', 'width="24" class="bg_none unprintable"', '').
							write_html('th', '', $lang['title']).
							((in_array($this->calcType, array("per", "marks"))) ? write_html('th', 'width="120"', $lang['marks_portion']) : '')
						)
					).
					write_html('tbody','', implode('', $terms_trs))
				)
			)	
		);
		
		return $out;
	}

	public function resetForm(){
		global $lang;
		$templates = array();
		$templates_array = scandir('modules/marks/certificates');
		foreach($templates_array as $templ){
			if(!in_array($templ, array('.', '..'))){
				$template_name = str_replace('.php', '', $templ);
				$templates[$template_name] = $template_name;
			}
		}
		// gradin option html
		$gradin_opts = write_html('option', 'value=""', $lang['not_used']);
		$graddings = do_query_array( "SELECT * FROM gradding ORDER BY name ASC", DB_student);
		foreach($graddings  as $gradding){
			$gradin_opts .= write_html('option', 'value="'.$gradding->id.'"'.(($this->gradding ==$gradding->id) ? 'selected="selected"': '' ), $gradding->name);
		}
	
		$level_opt_div = write_html('form', 'id="reset_form" class="ui-corner-all ui-state-highlight" style="padding:5px"',
			'<input type="hidden" name="con" value="'.$this->con.'" />'.
			'<input type="hidden" name="con_id" value="'.$this->con_id.'" />'.
			write_html('table', 'cellspacing="0" border="0"', 
				write_html('tr', '',
					write_html('td', 'valign="middel" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['no_of_terms'])
					).
					write_html('td', '', 
						write_html('select', 'name="count_terms" id="terms_select" class="combobox"',
							write_html('option', 'value="1" ' , $lang['one_term']).
							write_html('option', 'value="2" ' , $lang['two_term']).
							write_html('option', 'value="3" ' , $lang['three_term']).
							write_html('option', 'value="4" ' , $lang['four_term'])
						)
					)
				).
				write_html('tr', '',
					write_html('td', ' valign="top" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['mark_calc_type'])
					).
					write_html('td', '', 
						write_html('select', 'name="calc" id="calc_select" class="combobox" onchange="viewHideOption($(\'#calc_select\').val())"',
							write_html('option', 'value="per"', $lang['percent']).
							write_html('option', 'value="marks"', $lang['marks']).
							write_html('option', 'value="moyen"', $lang['moyen']).
							write_html('option', 'value="skills"', $lang['grading'])
						).
						write_html('div', 'id="calc_option_div"',
							write_html('div', 'id="perc_opt" class="hidden"',
								write_html('label', 'class="label ui-widget-header ui-corner-left def_float" style="width:40px"', $lang['max']).
								'<input type="text" style="width:60px" id="tot_marks" name="tot_marks" value="" />  '.$lang['points']
								.'<br style="clear:both" />'.
								write_html('label', 'class="label ui-widget-header ui-corner-left def_float" style="width:40px"', $lang['min']).
								'<input type="text" style="width:60px" id="min_marks" name="min_marks" value="" />  '.$lang['points']
							)
						)
					)
				).
				write_html('tr', '',
					write_html('td', ' valign="top" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['apply_to'])
					).
					write_html('td', '', 
						write_html('select', 'name="applyto" id="calc_applyto" class="combobox"',
							write_html('option', 'value="level"', $lang['this_level']).
							write_html('option', 'value="etab"', $lang['all_etablissement']).
							write_html('option', 'value="school"', $lang['all_shool'])
						)
					)
				).
				write_html('tr', '',
					write_html('td', ' valign="top" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['exams'])
					).
					write_html('td', '', 
						write_html('select', 'onchange="writeTermExam($(\'#exam_select_no\').val())" id="exam_select_no" class="combobox"',
							write_html('option', 'value="-1"', $lang['at_will']).
							write_html('option', 'value="0"', $lang['material_defined']).
							write_html('option', 'value="1"', $lang['defined'])
						).
						'<input type="text" name="exam_no" id="exam_no" value="-1" class="hidden" style="width:40px;margin:0px 28px"  />'
					)		
				).
				write_html('tr', '',
					write_html('td', ' valign="top" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['gradding_shell'])
					).
					write_html('td', '', 
						write_html('button', 'onclick="newGrading()" class="ui-state-default hoverable def_float" style="padding:2px"', 
							write_icon('plus')
						).
						write_html('button', 'onclick="viewGrading()" class="ui-state-default hoverable def_float" style="padding:2px"', 
							write_icon('extlink')
						).
						write_html('select', 'name="grading" id="grading_list" class="combobox"', $gradin_opts)
					)
				).
				write_html('tr', '',
					write_html('td', ' valign="top" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['certificate_template'])
					).
					write_html('td', '', 
						write_html_select('name="cert_templ" id="cert_templ" class="combobox"', $templates, '')
					)
				)
			)
		);
		return $level_opt_div;
	}
	

	public function resetSave($post){
		global $lang;
		$error = false;
		$applyto = $post['applyto'];
		$req_terms_no = $post['count_terms'];
		$exam_no = $post['exam_no'];
		$this->calcType = $post['calc'];
		$this->calcSpan = Marks::getCalcSpan($this->calcType);

		// level options
		$opts['calc'] = $post['calc'];
		$opts['gradding'] = isset($post['grading']) ? $post['grading'] : '';
		$opts['cert_templ'] =  isset($post['cert_templ']) ? $post['cert_templ'] : '';;
		if($post['calc'] == "marks"){
			$opts['tot_calc'] = $post['tot_marks'].(isset($post['min_marks']) ?'-'.$post['min_marks'] : '') ;
		} else {
			$opts['tot_calc'] = 'NULL';
		}

		if(isset($post['tot_marks']) && $post['calc'] == 'marks' && $post['tot_marks'] != ''){
			$portion = round($post['tot_marks'] / $req_terms_no, 2);
		} elseif(isset($post['calc']) && $post['calc'] == 'per'){
			$portion = round(100 /  $req_terms_no, 2);
		} else {
			$portion = '';
		}	
		$values =array();
		for($i=0; $i< $req_terms_no; $i++){
			$term_no = $i+1;
			$values[] = "$term_no, '$exam_no', '$portion'";
		}
	
		if($applyto == 'level'){
				// Set options
			$this->setLevelMarksOptions($this->level_id, $opts);
				// set terms
			if(!$this->createTerms($this->level_id, $values)){
				$error = true;
			}
		} elseif($applyto == 'etab'){
			echo 'etab';
			$level = new Levels($this->level_id);
			$etab = $level->getEtab();
			foreach($etab->getLevelList() as $lvl){
				$this->setLevelMarksOptions($lvl->id, $opts);
				if(!$this->createTerms($this->level_id, $values)){
					$error = true;
				}
			}
		} elseif($applyto == 'school'){
			do_query_edit("TRUNCATE TABLE terms", DB_year);
			do_query_edit("TRUNCATE TABLE exams", DB_year);
			do_query_edit("TRUNCATE TABLE exams_results", DB_year);
			do_query_edit("TRUNCATE TABLE marks_addon", DB_year);
			do_query_edit("TRUNCATE TABLE marks_addon_results", DB_year);

			$this->setLevelMarksOptions(false, $opts);
			$lvls = Levels::getList(true);
			foreach($lvls as $lvl){
				if(!$this->createTerms($lvl->id, $values)){
					$error = true;
				}
			}
		}
		return !$error ? true : false;
	}
	
	public function createTerms($level_id, $values){
		$this->clearLevelTerm($level_id);
		$sql_terms = "INSERT INTO terms ( level_id, term_no, exam_no, marks) VALUES ($level_id, ". implode("), ($level_id, ", $values).")";
		return do_query_edit( $sql_terms, DB_year);
	}
	
	public function setLevelMarksOptions($level_id, $opts){
		$calc_type = $opts['calc'];
		$calc_txt = $opts['tot_calc'];
		$gradding = $opts['gradding'];
		$cert_templ = $opts['cert_templ'];
		$sql = "UPDATE levels SET calc='$calc_type', tot_calc='$calc_txt', gradding='$gradding', cert_templ='$cert_templ'";
		if($level_id !== false){
			$sql.= " WHERE id=$level_id";
		}
		return do_query_edit($sql, DB_student);
	}
	
	public function clearLevelTerm($level_id){
		$terms = do_query_array("SELECT id FROM terms WHERE level_id=$level_id", DB_year);
		foreach($terms as $term){
			$exams = do_query_array("SELECT id FROM exams WHERE term_id=".$term->id, DB_year);
			foreach ($exams as $exam){
				do_query_edit("DELETE FROM exams_results WHERE exam_id=".$exam->id, DB_year);	
			}
			do_query_edit("DELETE FROM exams WHERE term_id=".$term->id, DB_year);
			$addons = do_query_array("SELECT id FROM marks_addon WHERE term_id=".$term->id, DB_year);
			foreach ($addons as $addon){
				do_query_edit("DELETE FROM marks_addons_results WHERE add_id=".$addon->id, DB_year);	
			}
			do_query_edit( "DELETE FROM marks_addon WHERE term_id=".$level_id, DB_year);
		}
		do_query_edit("DELETE FROM terms WHERE level_id=$level_id", DB_year);
	}
}
?>	
