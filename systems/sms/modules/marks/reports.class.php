<?php
/** Reports Generator
*
*/

class Reports{
	
	
	public function __construct($con, $con_id, $term=''){
		global $sms;
		$this->DB_year = $sms->db_year;
		$this->con = $con;
		$this->con_id = $con_id;
		$this->obj = $sms->getAnyObjById($con, $con_id);
		$this->level_id = $this->obj->getLevel()->id;
		$this->cur_term = $term != '' ? $term : terms::getCurentTerm($con, $con_id);
		/*if($this->cur_term == false){
			$this->cur_term =  Terms::getTermByno($this->level_id , 1);
		}*/		
		if($this->cur_term != false){
			$this->cur_term_id = $this->cur_term->id;
		}
		$this->terms = terms::getTermsByCon($this->con, $this->con_id);
		
			// Auto approuv
		if($sms->getSettings('auto_approv') == 1){
			$now = time();
			$chk_approv = do_query_array("SELECT id, title FROM terms WHERE level_id=$this->level_id AND end_date<$now AND approved=0", $this->DB_year);
			if(count($chk_approv) > 0){
				do_query_edit("UPDATE terms SET approved=1 WHERE level_id=$this->level_id AND end_date<$now AND approved=0", $this->DB_year);	
			}
		}
			// finialize
		$approved_terms = do_query_array("SELECT id FROM terms WHERE level_id=$this->level_id AND approved=0", $this->DB_year);
		if(count($approved_terms)==0){
			$this->finalize = true;
		} else {
			$this->finalize = false;
		}
	  
	}
	
	public function getCertificatTemplate(){
		global $sms;
		if(!isset($this->cert_template)){
			$cert = do_query_obj("SELECT cert_templ FROM levels WHERE id=$this->level_id", $sms->database, $sms->ip);
			if($cert->cert_templ != ''){
				$this->cert_template = $cert->cert_templ;
				return $this->cert_template;
			} else {
				return false;
			}
		} else {
			return $this->cert_template;
		}
	}
	
	public function loadLayout(){
		global $lang;
		$out = '';

		if($this->getCertificatTemplate() != false){
			$toolbox = createToolbox(array(
				 array(
					"tag" => "a",
					"attr"=> 'module="marks" action="loadCertTools" con="'.$this->con.'" conid="'.$this->con_id.'"',
					"text"=> $lang['modify'],
					"icon"=> "pencil"
				)
			));
			
			if($this->con == 'student'){
				return $this->getStudentCertificate();
			} else {
				return $this->loadReportsTable();	
			}
			
		} else {
			$toolbox = createToolbox(array(
				 array(
					"tag" => "a",
					"attr"=> 'module="marks" action="saveCertTempl"',
					"text"=> $lang['modify'],
					"icon"=> "pencil"
				)
			));
			
			$out .= write_error($lang['no_template_defined']);
			if(getPrvlg('terms_edit')){
				$templates = array();
				$templates_array = scandir('modules/marks/certificates');
				foreach($templates_array as $templ){
					if(!in_array($templ, array('.', '..', '_notes')) and is_dir($templ)){
						$templates[$templ] = $lang['templ_'.$templ];
					}
				}
				$out .= write_html('form', 'id="template_form" style="margin-top:"10px"',
					'<input type="hidden" name="con" value="'.$this->con.'" />'.
					'<input type="hidden" name="con_id" value="'.$this->con_id.'" />'.
					'<input type="hidden" name="level_id" value="'.$this->level_id.'" />'.
					write_html('fieldset', '',
						write_html('legend', '', $lang['certificate_template']).
						write_html('table', 'width="100%"',
							write_html('tr', '',
								write_html('td', ' valign="top" class="reverse_align" width="200"', 
									write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['certificate_template'])
								).
								write_html('td', '', 
									write_html_select('name="cert_templ" id="cert_templ" class="combobox"', $templates, '')
								)
							)
						)
					)
				);
			}
		}
		
		return $toolbox.$out;

	}
	
	public function getStudentCertificate(){		
		include('modules/marks/certificates/skills/skills.php');
		return 	$this->write_report_page( builCertHtml($this->con_id), true, true);
	}

	public function loadReportsTable(){
		global $MS_settings, $lang; 
		$list = new StudentsList($this->con, $this->con_id);
		$stds = $list->getStudents();
		foreach($stds as $std){
			$std_id = $std->id;
			$std_term_cell = array();
			foreach($this->terms as $term){
				$term_id = $term->id;
				$title = $term->title;
				$approved = $term->approved;
				$term_no = $term->term_no;
				if($approved == 1){
					$filename= 'certificate-'.$_SESSION['year'].'-term-'.$term_no.'.pdf';
					$file_exists = file_exists("attachs/files/$std_id/$filename");
					$std_term_cell[] = write_html('td', '',
						write_html('table', '', 
							write_html('tr', '',
							(getPrvlg('mark_print')?
								(!$file_exists  ?
									write_html('td', 'style="padding:0px"', 
										write_html('button', 'class="ui-state-default hoverable circle_button" onclick="generateCertificateCfm(\'student\', '.$std_id.','. $term_id.')" title="'.$lang['missing'].'"', write_icon('notice'))
									)
								: 
									write_html('td', 'style="padding:0px"', 
										write_html('button', 'class="ui-state-default hoverable circle_button" onclick="generateCertificateCfm(\'student\','.$std_id.','. $term_id.', true)" title="'.$lang['regenerate'].'"', write_icon('refresh'))
									)
								)
							:'').
							($file_exists ?
								write_html('td', 'style="padding:0px"', 
									write_html('button', 'class="ui-state-default hoverable circle_button" onclick="downloadCertificate(\'student\','.$std_id.','. $term_id.')" title="'.$lang['download'].'"', write_icon('arrowthick-1-s'))
								).
								write_html('td', 'style="padding:0px"', 
									write_html('button', 'class="ui-state-default hoverable circle_button" onclick="printCertificate(\'student\','.$std_id.','. $term_id.')" title="'.$lang['print'].'"', write_icon('print'))
								)
							:'').
							//($term_id == $this->cur_term_id ?
								write_html('td', 'style="padding:0px"', 
									write_html('button', 'class="ui-state-default hoverable circle_button" action="previewCertHtml" std_id="'.$std_id.'" term_id="'.$term_id.'" title="'.$lang['preview'].'"', write_icon('search'))
								)
							//: '')
							)
						)
					);
				} else  { //if($term_no <= $this->cur_term->term_no){
					$std_term_cell[] = write_html('td', '',
						write_html('table', '', 
							write_html('tr', '',
								write_html('td', 'style="padding:0px"', 
									write_html('button', 'class="ui-state-default hoverable circle_button" action="previewCertHtml" std_id="'.$std_id.'" term_id="'.$term_id.'" title="'.$lang['preview'].'"', write_icon('search'))
								)
							)
						)
					);
//				} else {
//					$std_term_cell[] = write_html('td', '','&nbsp;');
				}
			}
			if($this->finalize == true && $MS_settings['auto_approv'] == 0){
				$std_term_cell[] = write_html('td', '', 
					write_html('button', 'type="button" class="ui-state-default hoverable ui-corner-all" onclick="approveStudent('.$std_id.')"', $lang['approv'])
				);
			}			
			$terms_tr[] = write_html('tr', '',
				write_html('td', '', $std->getName()).
				implode('', $std_term_cell)
			);
		}
		$terms_th = array(write_html('th', '', '&nbsp;'));
		foreach($this->terms as $term){
			$terms_th[] = write_html('th', '', $term->title);
		}
		if($this->finalize == true && $MS_settings['auto_approv'] == 0){
			$terms_th[] = write_html('th', '', $lang['next_year']);
		}
		$out = write_html('div', 'id="cert_div"',
			write_html('div', 'class="toolbox"', 
				write_html('a', 'onclick="loadCertTools(\''.$this->con.'\', '.$this->con_id.')" class="ui-state-default hoverable"', 
					$lang['tools']. 
					write_icon('gear')
				)
			).
			write_html('form', 'id="cert_form"',
				'<input type="hidden" name="con" value="'.$this->con.'" />'.
				'<input type="hidden" name="con_id" value="'.$this->con_id.'" />'
			).
			write_html('table', 'class="result"', 
				write_html('thead', '', write_html('tr', '',implode('', $terms_th))).
				write_html('tbody', '', implode('', $terms_tr))
			)
		);
		return $out;
		
	}
	
	public function getGenerateOptions(){
		global $MS_settings, $lang;
		return write_html( 'fieldset', '',
			write_html('legend', '', $lang['generate_options']).
			write_html('ul', 'style="list-style:none; padding:0; margin:0"', 
				write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
					'<input type="checkbox" value="1" name="option_generate_head" '. ($MS_settings['option_generate_head']== 1 ? 'checked="checked"' : '') .'/>'.$lang['head_page']
				).
				write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
					'<input type="checkbox" value="1" name="option_generate_cert" '. ($MS_settings['option_generate_cert']== 1 ? 'checked="checked"' : '') .'/>'.$lang['certficates']
				).
				write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
					'<input type="checkbox" value="1" name="option_generate_skills" '. ($MS_settings['option_generate_skills']== 1 ? 'checked="checked"' : '') .'/>'.$lang['skills']
				).
				write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
					'<input type="checkbox" value="1" name="option_generate_exams" '. ($MS_settings['option_generate_exams']== 1 ? 'checked="checked"' : '') .'/>'.$lang['exams_results']
				).
				write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
					'<input type="checkbox" value="1" name="option_generate_appr" '. ($MS_settings['option_generate_appr']== 1 ? 'checked="checked"' : '') .'/>'.$lang['appreciations']
				).
				write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
					'<input type="checkbox" value="1" name="option_generate_absents" '. ($MS_settings['option_generate_absents']== 1 ? 'checked="checked"' : '') .'/>'.$lang['absents']
				).
				write_html('li', 'class="ui-corner-all ui-state-default" style="padding:3px; width:150px"', 
					'<input type="checkbox" value="1" name="option_generate_behavior" '. ($MS_settings['option_generate_behavior']== 1 ? 'checked="checked"' : '') .'/>'.$lang['behavior']
				)
			)	
		);
	}
	
	public function getGenerateTools(){
		global $lang;
		$tools = array('download'=> $lang['download']);
		$tools['print'] = $lang['print'];
		if(getPrvlg('mark_print')){
			$tools['generate'] = $lang['generate_missing'];
			$tools['regenerate'] = $lang['regenerate'];
			$tools['delete'] = $lang['delete'];
		}
		$terms_select = array();
		foreach($this->terms as $term){
			$terms_select[$term->id] = $term->title;
		}
		return write_html('form', 'id="cert_tools_form" target="_blank" method="POST" action="index.php?module=marks&submit_tools"  class="ui-corner-all ui-state-highlight" style="padding:5px"',
			'<input type="hidden" name="con" value="'.$this->con.'" />'.
			'<input type="hidden" name="con_id" value="'.$this->con_id.'" />'.
			write_html('table', 'width="100%" cellspacing="0" border="0"',
				write_html('tr', '', 
					write_html('td', 'valign="middel" class="reverse_align" width="100"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['term'])
					).
					write_html('td', '',
						write_html_select('id="cur_term" name="cur_term" class="combobox"', $terms_select , $this->cur_term_id)
					)
				).
				write_html('tr', '', 
					write_html('td', 'valign="middel" class="reverse_align" width="100"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['action'])
					).
					write_html('td', '',
						write_html_select('id="tool" name="tool" class="combobox" onchange="evalToolOpt()"', $tools , "")
					)
				).
				write_html('tr', 'id="generate_options_tr" class="hidden"',
					write_html('td', 'colspan="2"',
						$this->getGenerateOptions()				
					)
				)
			)
		);
	}
	
	public function generate($term_id, $pages=array()){
		global $lang, $sms, $prvlg;

		if(count($pages) == 0){
			$pages['option_generate_head'] = $sms->getSettings('option_generate_head')== '1' ?true : false;
			$pages['option_generate_cert'] = $sms->getSettings('option_generate_cert')== '1' ?true : false;
			$pages['option_generate_exams'] = $sms->getSettings('option_generate_exams')== '1' ?true : false;
			$pages['option_generate_skills'] = $sms->getSettings('option_generate_skills')== '1' ?true : false;
			$pages['option_generate_appr'] = $sms->getSettings('option_generate_appr')== '1' ?true : false;
			$pages['option_generate_absents'] = $sms->getSettings('option_generate_absents')== '1' ? true : false;
			$pages['option_generate_behavior'] = $sms->getSettings('option_generate_behavior')== '1' ? true : false;
		}
		$dir = 'certificates/'.$this->getCertificatTemplate().'/';
		$out = '';
		if($pages['option_generate_head']){
			if(is_file($dir.'head_page.tpl')){
				$head = new Layout($this->obj);
				$head->template = $dir.'head_page.tpl';
				$out.= $this->write_report_page( $head->_print());
			}
			$out.= '<hr style="page-break-after:always">';
		}
		
		if($pages['option_generate_skills']){
			$skills ='';
			$services = $this->obj->getServices();
			foreach($services as $service){
				if($service->mark == '1'){
					$skills .= Skills::createReport( $this->obj, new Terms($term_id), $service);
				}
				if($pages['option_generate_appr'] == false){
					$appr = appreciations::getStdAppr($this->obj->id, $term_id, $service->id);
					if($appr != false && $appr != ''){
						$skills.= write_html('fieldset', ' style="padding:5px; font-weight:bolder"', $appr);
					}
				}
			}
			$out.= $this->write_report_page( $skills, true, false);
			$general_appr = appreciations::getStdAppr($this->obj->id, $term_id, '0');
			if($general_appr != false && $general_appr != ''){
				$out.= write_html('fieldset', ' class="ui-state-highlight" style="padding:5px; font-weight:bolder"', $general_appr);
			}
			$out.= '<hr style="page-break-after:always">';
		}

		if($pages['option_generate_cert']){
			include($dir.'cert.php');
			$out[] = $this->write_report_page( builCertHtml($this->con_id), true, false);
			$out.= '<hr style="page-break-after:always">';
		}

		if($pages['option_generate_exams']){
			$marks = new Marks('student', $this->con_id);
			$out.=  $this->write_report_page($marks->createMarksTable($term_id), true, false);
			$out.= '<hr style="page-break-after:always">';
		}
		
		if($pages['option_generate_appr']){
			$appreciations = new appreciations('student', $this->con_id);
			$out.=  $this->write_report_page($appreciations->createStudentApprTable(), true, false);
			$out.= '<hr style="page-break-after:always">';
		}
		
		if($pages['option_generate_absents']){
			$absents = new Absents('student', $this->con_id);
			$out.=  $this->write_report_page($absents->loadStdLayout('term', $term_id, false), true, false);
		}
		
		$out .= write_html('table', 'width="100%" cellspacing="10"',
			write_html('tr', '',
				write_html('td', 'align="center" valign="middle" height="30"', "Signature de l'Enseignant"). 
				write_html('td', 'align="center" valign="middle"', "Signature de la Directrice").
				write_html('td', 'align="center" valign="middle"', "Signature des Parents")
			).
			write_html('tr', '',
				write_html('td', 'height="50" style="border:1px solid #000"', "&nbsp;"). 
				write_html('td', 'style="border:1px solid #000"', "&nbsp;").
				write_html('td', 'style="border:1px solid #000"', "&nbsp;")
			)
		);
		return $out;		
	}

	public function saveReport($term_id, $opts, $pdf=true){
		global $lang, $sms, $prvlg;
		$term = new Terms($term_id);
		$error = false;
		$ccuntPage = 0;
		if($prvlg->_chk('mark_approv')){
			$student = new Students($this->con_id);
			$class = $student->getClass();
			$level = $class->getLevel();
			$filepath = "attachs/files/$this->con_id/";
			$filename= 'certificate-'.$_SESSION['year'].'-term-'.$term->term_no.'.pdf';
			if(!is_dir($filepath)){
				if(!mkdir($filepath, 0777, true)){
					$error ='Cant create path';
				}
			}
			if(file_exists($filepath. $filename)){
				unlink($filepath.$filename);
			}
				// Content
			$pages = array();
			$pages['option_generate_head'] = $opts['option_generate_head']== '1' ?true : false;
			$pages['option_generate_cert'] = isset($opts['option_generate_cert']) && $opts['option_generate_cert']== '1' ?true : false;
			$pages['option_generate_exams'] = isset($opts['option_generate_exams']) && $opts['option_generate_exams']== '1' ?true : false;
			$pages['option_generate_skills'] = isset($opts['option_generate_skills']) && $opts['option_generate_skills']== '1' ?true : false;
			$pages['option_generate_appr'] = isset($opts['option_generate_appr']) && $opts['option_generate_appr']== '1' ?true : false;
			$pages['option_generate_absents'] = isset($opts['option_generate_absents']) && $opts['option_generate_absents']= '1' ? true : false;
			$pages['option_generate_behavior'] = isset($opts['option_generate_behavior']) && $opts['option_generate_behavior']== '1' ? true : false;
			
			$pages = $this->generate($term_id, $pages);
			if($pdf == false){
				return implode('', $contents);
			} else {
				require_once("plugin/pdf/dompdf/pages/dompdf_pages.php");
				$dompdf = new dompdf_pages();
				$theme_css = readFileData('assets/css/themes/'.MS_theme.'/jquery-ui.css');
				$theme_css = str_replace('images/', 'assets/css/themes/'.MS_theme.'/images/', $theme_css);
				$css = write_html('style', '', 
				//	$theme_css.
					readFileData('assets/css/common.css').
					readFileData('plugin/pdf/pdf.css').
					readFileData('assets/css/'.($_SESSION['lang']== 'ar' ? 'rtl' : 'ltr').'.css')
					);
				
					// HTML
				foreach($pages as $page){
					$dompdf->set_paper('A4', 'landscape');
					$dompdf->load_html(trim($page));
					$dompdf->render();
				}
				//$dompdf->save($filepath.$filename);
				if(!file_put_contents($filepath.$filename, 
					$css.
					$dompdf->output()
				)){
					$error = "Can't create file";
				}
			}
		} else {
			$error = $lang['no_privilege'];
		}
		return array('error'=>($error!= false ? $error : ''));
	}
	
	public function downloadCertificates($term_id){
		$filename=  '';
		foreach($this->terms as $term){
			if($term->id == $term_id){
				$filename= 'certificate-'.$_SESSION['year'].'-term'.$term->term_no.'.pdf';
			}
		}
		require_once('scripts/files_functions.php');
		if($this->con == 'student'){
			$filepath = "attachs/files/$this->con_id/";
			if(file_exists($filepath. $filename)){
				forceDownload($filepath. $filename, true);
			} else{
				echo write_error('File dont exists!');
			}
		} else {
			$stds = getStdIds($con, $con_id);
			foreach($stds as $std_id){	
				$filepath = "attachs/files/$std_id/";
				if(file_exists($filepath. $filename)){
					$files[] = $filepath. $filename;
				}
			}
			downloadAsZip($files);
		}
	}
	
	public function deleteCertificate($term_id){
		$filename=  '';
		foreach($this->terms as $term){
			if($term->id == $term_id){
				$filename= 'certificate-'.$_SESSION['year'].'-term'.$term->term_no.'.pdf';
			}
		}
		$stds = getStdIds($con, $con_id);
		foreach($stds as $std_id){	
			$filepath = "attachs/files/$std_id/";
			if(file_exists($filepath. $filename)){
				if(!unlink($filepath. $filename)){
					$error = true;
				} 
			}
		}
		if(!$error){
			return json_encode(array('error'=>''));
		}
		
	}
	
	public function write_report_page($html, $header=true, $footer=true){
		$content = '';
		if($header){
			global $sms;
			global $con_id;
			global $cur_term;
			global $lang;
			global $MS_settings;
			$header = new stdClass();
			$header->cert_year = $lang['year'].': '.$_SESSION['year'].'/'.($_SESSION['year']+1);
			$student = new Students($this->con_id);
			$header->student_name = $student->getName();
			$header->class_name =  $student->getClass()->getName();
			$header->school_name = $MS_settings['school_name'];
			$header->cert_term = $lang['terms'] .': '. $this->cur_term->title;
			$header->logo_path = $sms->getLogo();
			$header_html = fillTemplate("modules/marks/templates/certificate_header.tpl", $header);
			$content .= $header_html;
		} 
		
		$content .= $html;
	
		if($footer){
			$footer_html = fillTemplate("modules/marks/templates/certificate_footer.tpl", new stdClass());
			$content .= $footer_html;
		}
		
		return write_html('page', 'class="page" style="page-break-after:always"',$content);
	}

}