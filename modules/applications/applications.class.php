<?php
/* Applications
*# Online Application plugin

*# status :
*# 0 = form filled
# 1 = form accepted and reply with rendez-vous
# 2 = exams
# 3 = reexam
# 4 = std accepted 
# 5 = contacted parent
#
* 
* appli list by stat
* new appli = 1
* first interview = 2
* accepted  = 3
* rejected = 4
*/

class Applications{
	
	public function __construct($app_id){
		global $lang;
		if($sms == '' ){
			global $this_system;
			$sms = $this_system;
		}
		$this->sms = $sms;
		if($id != ''){	
			$year = $year != '' ? $year : $_SESSION['year'];
			$appli = do_query_obj("SELECT * FROM applications WHERE id=$id");	
			if(isset( $appli->id )){
				foreach($appli as $key =>$value){
					$this->$key = $value;
				}
			}	
			$student = do_query_obj("SELECT * FROM applications_std WHERE id=$id");	
			if(isset( $student->id )){
				foreach($student as $key =>$value){
					$this->$key = $value;
				}
			}	
		}
		
		$this->std_name = $this->getName(); 
		$this->level = new Levels($appli->level_id);
		$this->level_name = $level->getName();
	}
	
	public function getName($other_lang = false){
		$name_template = $this->sms->getSettings('name_template');
		if($name_template == false || trim($name_template) == ''){
			$name_template = 'name middle_name last_name';
		}  
		$t = explode(' ', $name_template);
		$this->name_ltr = '';
		foreach($t as $cell){
			$this->name_ltr .= $this->$cell.' ';
		}

		if($_SESSION['lang'] == 'ar'){
			return trim($other_lang == false ? $this->name_ar :$this->name_ltr) ;
		} else {
			return trim($other_lang == false ? $this->name_ltr : $this->name_name) ;
		}
	}

	static function loadMainLayout(){
		$layout = new Layout();
		$layout->template= "modules/applications/templates/main_layout.tpl";
		
		return $layout->_print();
	}
	
	static function getList($num){
		$layout = new Layout();
		$trs = array();
		$sql = "SELECT * FROM applications WHERE ";
		if($num == 1){
			$sql .= "status=0 OR status=1";
			$layout->template= "modules/applications/templates/new_appli.tpl";
			$row_template = "modules/applications/templates/new_appli_row.tpl";
		} elseif($num == 2){
			$sql .= "status=2";
			$layout->template= "modules/applications/templates/accepted_appli.tpl";
			$row_template = "modules/applications/templates/accepted_appli_row.tpl";
		} elseif($num == 3){
			$sql .= "status=3";
			$layout->template= "modules/applications/templates/accepted_std.tpl";
			$row_template = "modules/applications/templates/accepted_std_row.tpl";
		} elseif($num == 4){
			$sql .= "status=4";
			$layout->template= "modules/applications/templates/rejected.tpl";
			$row_template = "modules/applications/templates/rejected_row.tpl";
		}
		$sql .= " ORDER BY date ASC";
		$rows = do_query_array($sql);
		foreach($rows as $row){
			$appli = new Applications($row->id);
			$line = new Layout($appli);
			$line->template = $row_template;
			$trs[] = $line->_print();
		}
		$layout->trs = implode('', $trs);
		return $layout->_print();
	}
}
