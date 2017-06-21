<?php
/** Mediacal
*
*/

class Medical {
	private $thisTemplatePath = 'modules/medical/templates';

	public function __construct($std_id){
		$this->std_id = $std_id;
		
	}
	
	public function loadLayout(){
		$layout = new Layout();
		$layout->medical_data = $this->loadData();
		$layout->medical_history = $this->loadHistory();
		
		return fillTemplate('modules/medical/templates/medical_layout.tpl', $layout);
		
	}
	
	public function loadData(){
		$data = do_query_obj("SELECT * FROM medical WHERE id=$this->std_id", MySql_Database);	
		if(!isset($data->id)){
			$data = new Layout();;
			$data->id = $this->std_id;
		} else {
			$array = array('acute', 'allergie', 'emotional', 'gastro', 'heart', 'injuries', 'kidney', 'muscular', 'skin', 'surgical');	
			foreach($array as $f){
				if($data->{$f."_chk"} == '1'){
					$data->{$f."_chk_1_checked"} = 'checked="checked"';
					$data->{$f."_chk_0_checked"} = '';
				} else {
					$data->{$f."_chk_0_checked"} = 'checked="checked"';
					$data->{$f."_chk_1_checked"} = '';
				}
			}
		}
		return fillTemplate('modules/medical/templates/medical_data.tpl', $data);
		
	}
	
	public function loadHistory(){
		global $lang;
		$out = '';
		$visits = do_query_array("SELECT * FROM medical_history WHERE std_id=$this->std_id ORDER BY visit_date DESC", MySql_Database);
		if($visits != false  && count($visits) > 0){
			foreach($visits as $visit){
				$doctor = new Employers($visit->user_id);
				$visit->doctor_name = $doctor->getName();
				$visit->visit_t = unixToDate($visit->visit_date).' '.unixToTime($visit->visit_time);
				$out .= fillTemplate('modules/medical/templates/medical_history.tpl', $visit);
			}
			
			$toolbox = array();
			$toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="addMedicalVisit" stdid="'.$this->std_id.'"',
				"text"=> $lang['add'],
				"icon"=> "plusthick"
			);
			$toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="print_pre" rel="#medical_list_div-'.$this->std_id.'"',
				"text"=> $lang['print'],
				"icon"=> "print"
			);
			
			$student = new Students($this->std_id);
			return createToolbox($toolbox).
			write_html('div', 'id="medical_list_div-'.$this->std_id.'"',
				write_html('div', 'class="showforprint hidden"',
					write_html('h2', '', $student->getName()).
					write_html('h3', '', $student->getClass()->getName()).
					write_html('h2', 'align="center"', $lang['medical_history'])
				).
				write_html('ul', 'class="medical_hisory_ul" stdid="'.$this->std_id.'"', $out)
			);
		} else {
			return '';
		} 
	}
	
	static function saveData($post){
		global $lang;
		$answer = array();
		$answer['error'] = $lang['error_updating'];
		if($post['id'] != ''){
			$std_id = $post['id'];
			$chk = do_query_obj("SELECT id FROM medical WHERE id=$std_id", MySql_Database);
			if(isset($chk->id)){
				if(UpdateRowInTable("medical", $post, "id=$std_id", MySql_Database) != false){
					$answer['id'] = $std_id;
					$answer['error'] = '' ;
				}
			} else { // new Student
				if(insertToTable("medical", $post, MySql_Database) != false){
					$answer['id'] =  $std_id;
					$answer['error'] = '';
				}
			}
		}
		return json_encode($answer);
	}
	
	static function visitForm($std_id){
		$data = new Layout();;
		if($std_id != false){
			$student = new Students($std_id);
			$data->student_name = $student->getName();
			$data->std_id = $std_id;
		}
		return fillTemplate('modules/medical/templates/medical_form.tpl', $data);
	}
	
	static function saveVisit($post){
		global $lang;
		$answer = array();
		$answer['error'] = $lang['error_updating'];
		$post['user_id'] = $_SESSION['user_id'];
		if(insertToTable("medical_history", $post, MySql_Database) != false){
			$answer['id'] =  $post['std_id'];
			$answer['error'] = '';
			$doctor = new Employers($_SESSION['user_id']);
			$out = new Layout();;
			$out->symptoms = $post['symptoms'];
			$out->response = $post['response'];
			$out->doctor_name = $doctor->getName();
			$out->visit_t = $post['visit_date'].' '.$post['visit_time'];
			$answer['html'] = fillTemplate('modules/medical/templates/medical_history.tpl', $out);
		}
		return json_encode($answer);
	}
	
	static function deleteVisit($visit_id){
		global $lang;
		$answer = array();
		$answer['error'] = $lang['error'];
		if(do_query_edit("DELETE FROM medical_history WHERE id=$visit_id", MySql_Database) != false){
			$answer['id'] =  $visit_id;
			$answer['error'] = '';
		}
		return json_encode($answer);
	}
	
	
}
	