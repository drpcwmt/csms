<?php
/** Levels AKA grades
*
*/

class Levels {
	public $db_table = 'level';

	public function __construct($id, $sms=''){
		if($sms == '' ){
			global $this_system;
			$sms = $this_system;
		}
		$this->sms = $sms;
		if($id != ''){	
			$level = do_query_obj("SELECT * FROM levels WHERE id=$id", $sms->database, $sms->ip);	
			if(isset($level->id)){
				foreach($level as $key =>$value){
					$this->$key = $value;
				}
			}	
		} else { echo 'undefined level';}
			
	}
	
	public function getName($other_lang = false){
		if($other_lang == false){
			return $_SESSION['lang'] == 'ar' ? $this->name_rtl : $this->name_ltr ;
		} else {
			return $_SESSION['lang'] == 'ar' ? $this->name_ltr : $this->name_rtl ;
		}
	}
	
	public function getLevel(){
		return $this;
	}
	
	public function getEtab(){
		return new Etabs($this->etab_id, $this->sms);	
	}
	
	
	public function getClassList(){
		if(!isset($this->classes)){
			$classes_arr = array();
			$classes = do_query_array("SELECT id FROM classes WHERE level_id=$this->id", $this->sms->db_year, $this->sms->ip); 
			if( $classes!= false){
				foreach($classes as $l){
					$classes_arr[] =  new Classes($l->id,'', $this->sms);
				}
			}
			$this->classes = $classes_arr;
		}
		return sortArrayOfObjects($this->classes, $this->sms->getItemOrder('classes'), 'id');
	}
	
	public function getPrincipals(){
		if(!isset($this->principals)){
			$out = array();
			$principals = do_query_array("SELECT id FROM principals WHERE levels=$this->id", $this->sms->database, $this->sms->ip);
			foreach($principals as $pr){
				$out[] = new Principals($pr->id, $this->sms);
			}
			$this->principals = $out;
		}
		return $this->principals;
	}
	
	public function loadLayout(){
		global $lang, $prvlg;
		$layout = new Layout($this);
		$layout->level_name = $this->getName();
		$etabs = Etabs::getList();
		foreach($etabs as $etab){
			$etabs_arr[$etab->id] = $etab->getName();
		}
		$layout->etabs_select = write_select_options( $etabs_arr, $this->etab_id, false);
		$student_list = new StudentsList('level', $this->id, $this->sms);
		$student_list->stats = array('1');
		$layout->total_students = $student_list->getCount();
			// principals
		$trs = array();
		$principals = $this->getPrincipals();
		$read_principal = getPrvlg('resource_read_principals');
		$serial = 1;
		foreach($principals as $pr){
			$trs[] = write_html('tr', '',
				($read_principal ?
					write_html('td', 'class="unprintable"', 
						write_html('button', 'module="resources" principalid="'.$pr->id.'" action="openPrincipal" class="ui-state-default hoverable circle_button"', write_icon('person'))
					)
				: 
					write_html('td', '', $serial)
				).
				write_html('td', '', $pr->getName())
			);
			$serial++;
		}

		$layout->principals_trs = implode('', $trs);

			// Classes
		$trs = array();
		$classes = $this->getClassList();
		$read_class = getPrvlg('resource_read_classes');
		$serial = 1;
		foreach($classes as $class){
			$trs[] = write_html('tr', '',
				($read_class ?
					write_html('td', 'class="unprintable"', 
						write_html('button', 'module="resources" classid="'.$class->id.'" action="openClass"  class="ui-state-default hoverable circle_button"', write_icon('extlink'))
					)
				: 
					write_html('td', '', $serial)
				).
				write_html('td', '', $class->getName())
			);
			$serial++;
		}
		$layout->classes_trs = implode('', $trs);
		$layout->resources_toolbox = Resources::getItemsToolbox('levels', $this->id);
		
		// Schools Fees Plugin
		if(MSSER_safems && (getPrvlg('fees_read') || getPrvlg('fees_write'))){
			$layout->school_fees_li = write_html('li','', 
				write_html('a', 'href="index.php?module=fees&con=level&con_id='.$this->id.'"', $lang['school_fees'])
			);
		}
		// editable 
		$layout->editable = $prvlg->_chk('resource_edit_levels') ? '1' : '0';
		$layout->template = 'modules/resources/templates/levels.tpl';
		return $layout->_print();
	}

	public function getStudents($all_state=false){
		if(!isset($this->students)){
			$out = array();
			$classes = $this->getClassList();
			foreach($classes as $class){
				$out = array_merge($out, $class->getStudents($all_state));
			}

			$this->students = $out;
		}
		return $this->students;
	}
	
	public function getServices(){
		$sms =$this->sms;
		if(!isset($this->services)){
			$out= array();
			if($sms->getSettings('ig_mode') == '1'){
				$level_mat= do_query_array("SELECT id FROM services_ig", $this->sms->db_year, $this->sms->ip);
				if(count($level_mat) > 0 ){
					foreach($level_mat as $service){
						$out[]= new servicesIG($service->id, $this->sms);
					}
				}
			} else {
				$level_mat= do_query_array("SELECT id FROM services WHERE level_id =".$this->id, $this->sms->db_year, $this->sms->ip);
				if(count($level_mat) > 0 ){
					foreach($level_mat as $service){
						$out[]= new services($service->id, $this->sms);
					}
				}
			}
			$this->services = Services::orderService($out);
		} 
		return $this->services;
	}

	
	/*********************** Schoool Fees ********************************/
	public function loadFeesLayout(){
		$layout = new Layout();
		$layout->level_id = $this->id;
		$layout->level_name = $this->getName();
		$layout->sms_id = $this->sms->id;
		$year = $_SESSION['year'];		
		
		// payments
		$layout->payments_ths = '';
		$payments = $this->getDates($year);
		foreach($payments as $p){
			$layout->payments_ths .= write_html('th', '', $p->title);
		}
		
		$fees = $this->getFees($year);
		$layout->level_fees_rows = '';
		foreach($fees as $f){
			$row = new Layout($f);
			$row->value = $f->debit !=0 ? $f->debit : ($f->credit * -1);
			$row->disc_hidden = $f->discount=='1' ? '' : 'hidden';
			$row->annual_hidden = $f->increase=='1' ? '' : 'hidden';
			$row->main_code = Accounts::fillZero('main', $f->main_code);
			$row->sub_code = Accounts::fillZero('sub', $f->sub_code);
			$row->currency_opts = Currency::getOptions($f->currency);
			$row->sms_id = $this->sms->id;
			$row->template = "modules/fees/templates/level_fees_rows.tpl";
			$layout->level_fees_rows .= $row->_print();
		}
		$layout->template = "modules/fees/templates/level_layout.tpl";
		return $layout->_print();
	}

	public function getFees($year){
		if(!isset($this->fees)){
			$this->fees = array();
			$fees = do_query_array("SELECT id FROM school_fees WHERE year=$year AND con_id=$this->id AND con='level'", $this->sms->database, $this->sms->ip);
			foreach($fees as $f){
				$this->fees[] = new Fees($f->id, $this->sms);
			}
		}
		return $this->fees;
	}
	
/*	public function browse(){
		$classes = $this->getClassList();		
		$layout = new Layout($this);
		$sms = $this->sms;
		$layout->template = 'modules/fees/templates/level_browse.tpl';
		$layout->tabs_lis = '';
		$first = true;
		foreach($classes as $class){
			$href = $first ? '#first_class' : 'index.php?module=fees&browse&con=class&con_id='.$class->id.'&sms_id='.$sms->id;
			$layout->tabs_lis .= write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $class->getName())
			);
			if($first) { $layout->tabs_div = $class->loadFeesLayout();}
			$first = false;
		}
		
		return $layout->_print();
	}
*/
	public function getDates($year){
		$out = do_query_array("SELECT * FROM school_fees_dates WHERE con='level' AND con_id=$this->id AND year=$year ORDER By `from` ASC",  $this->sms->database, $this->sms->ip);
		if($out == false){
			$schoolFees = new SchoolFees($this->sms);
			return $schoolFees->getDates($year);
		} else {
			return $out;
		}
	}
	
		/**************** Statics ****************/
	static function getList($full=false){
		global $sms;
		$grades = array();
		if($full){
			$levels = do_query_array("SELECT id FROM levels", $sms->database, $sms->ip);
			foreach($levels as $level){
				$grades[] = new Levels($level->id, $sms);	
			}
		} else {
			if( $_SESSION['group'] == 'principal' ){
				$levels = do_query_array("SELECT levels FROM principals WHERE id=".$_SESSION['user_id'], $sms->database, $sms->ip);
				foreach($levels as $level){
					$grades[] = new Levels($level->levels, $sms);	
				}
			} elseif( $_SESSION['group'] == 'coordinator' ){
				$levels = do_query_array("SELECT levels FROM coordinators WHERE id=".$_SESSION['user_id'], $sms->database, $sms->ip);
				foreach($levels as $level){
					$grades[] = new Levels($level->levels, $sms);	
				}
			} elseif($_SESSION['group'] == 'supervisor'){
				$supervisor = new Supervisors($_SESSION['user_id'], $sms);
				$services = $supervisor->getServices();
				foreach($services as $service){
					$grades[] = new Levels($service->level_id, $sms);
				}
			} elseif(!in_array($_SESSION['group'], array('prof', 'student', 'parent'))){
				$levels = do_query_array("SELECT id FROM levels ORDER BY id ASC", $sms->database, $sms->ip);		
				foreach($levels as $level){
					$grades[] = new Levels($level->id, $sms);
				}
			} else {
				$levels = do_query_array("SELECT id FROM levels", $sms->database, $sms->ip);
				foreach($levels as $level){
					$grades[] = new Levels($level->id, $sms);	
				}
			}
		}
		
		return sortArrayOfObjects($grades, $sms->getItemOrder('levels'), 'id');
	}

	static function _delete($id){
		global $sms;
		if(do_query_edit("DELETE FROM levels WHERE id=$id", $sms->database, $sms->ip)){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return json_encode($answer);
	}
	
	static function _new(){
		global $lang;
		$new_class = new Layout();;
		$new_class->etabs_options = write_select_options( objectsToArray(Etabs::getList(true)), '', false);
		return fillTemplate('modules/resources/templates/levels_new.tpl', $new_class);
	}

	static function _save($post){
		global $sms;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'levels', $sms->database, $sms->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'levels', $sms->database, $sms->ip);
			$id = $result;
		}

		if($result!=false){
			$answer['id'] = $id;
			$answer['title'] = $post['name_'.$_SESSION['dirc']];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}


}
?>