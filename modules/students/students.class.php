<?php
/** Student
*## Student stats
*## 0 = desincriped
*## 1 = inscriped
*## 2 = waiting list
*## 3 = temp suspension
*## 4 = Request
*## 5 = Guratuated

*/

class Students{
	
	private $thisTemplatePath = 'modules/students/templates';
	
	public function __construct($id, $sms=false){
		if($sms == false){
			global $sms;
			if(!isset($sms->ip)){
				die( 'sms not found');
			}
		}
		$this->sms = $sms;
		$sql = "SELECT * FROM student_data WHERE id=$id";
		$student = do_query_obj($sql, $sms->database, $sms->ip);
		if(isset($student->id)){ 
			foreach($student as $key =>$value){
				$this->$key = $value;
			}
		}
		$this->getName();
		$this->status = $this->getStatus();
	}
	
	public function getAccCode(){
		$etab = $this->getEtab();
		$main_code = Accounts::fillZero('main', $this->sms->getAccCode());
		$sub_code = Accounts::fillZero('sub', $this->id);
		return $main_code . $sub_code;
	}

	public function getAccount(){
		$accms = $this->sms->getAccms();
		$cc= $this->sms->getCC();
		$acc = do_query_obj("SELECT sub FROM sub_codes WHERE main='151$cc' AND sub=$this->id", $accms->database, $accms->ip);
		if($acc != false){
			return $acc;
		} else {
			$new_acc = array(
				'main'=>'151'.$cc,
				'sub'=>$this->id,
				'title'=>$this->name_ar,
				'notes'=>$this->sms->code
			);
			if(do_insert_obj($new_acc, 'sub_codes', $accms->database, $accms->ip)){
				return getAccount($this->getAccCode());
			} else {
				return false;
			}
		}
	}

	public function getName($other_lang = false){
		$name_template = $this->sms->getSettings('name_template');
		if($name_template == false || trim($name_template) == ''){
			$name_template = 'name middle_name last_name';
		}  
		$t = explode(' ', $name_template);
		$this->name_ltr = '';
		foreach($t as $cell){
			$this->name_ltr .= (isset($this->$cell) ? $this->$cell: '').' ';
		}

		if($_SESSION['lang'] == 'ar'){
			return trim($other_lang == false ? $this->name_ar :$this->name_ltr) ;
		} else {
			return trim($other_lang == false ? $this->name_ltr : $this->name_ar) ;
		}
	}
	
	public function getParent(){
		if(!isset($this->parent_obj)){
			$this->parent_obj = new Parents($this->parent_id, $this->sms);
		}
		return $this->parent_obj;
	}
	
	public function getRegStat(){
		$sms = $this->sms;
		$result = do_query_obj("SELECT new_stat FROM classes_std WHERE std_id=$this->id", $sms->db_year, $sms->ip);
		return $result->new_stat == 1 ? true : false;
	}
	
	public function getStatus(){
		$this->reel_stat = $this->status;
		if(in_array($this->status, array(1,3))){
			return $this->status;	
		} else {
			$year = getYear();
			// quited in the current Year
			if($this->quit_date >= $year->begin_date && $this->quit_date<= $year->end_date){
				if($this->quit_date > time()){
					return '1';
				} else {
					if($year->end_date < time()){
						return '1';
					} else {
						return '0';
					}
				}
			} else {
				return $this->status;
			}
		}
	}
	
	public function getStatusSpan(){
		global $lang;
		$span = '';
		if($this->reel_stat != 1 ){
			if($this->reel_stat == 0){
				$title =$lang['desinscriped'];
			$span .= write_html('span', 'class="red_item" title="'.$title.'" style="font-weight:bold"', ' * ');
			} elseif($this->reel_stat == 2){
				$title =$lang['waiting_list'];
			} elseif($this->reel_stat == 3){
				$title =$lang['suspended'];
			$span .= write_html('span', 'class="red_item" title="'.$title.'" style="font-weight:bold"', ' * ');
			} elseif($this->reel_stat == 5){
				$title = $lang['gruaduated'];
			}
		}
		return $span;
	}
	
	public function getTel($all=false){
		$rows = do_query_array("SELECT * FROM phonebook WHERE con='student' AND con_id=$this->id", $this->sms->database, $this->sms->ip);
		$rows = sortArrayOfObjects($rows, $this->sms->getItemOrder('phonebook-student-'.$this->id), 'id');
		if( $all==false ){
			$first = reset($rows);
			return isset($first->tel) ? $first->tel : '';
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $r){
					$out[] = $r->tel;
				} 
			}
			return $out;
		}
	}

	public function getMail($all=false){
		$phonebook = new MailBook('student', $this->id, $this->sms);
		$rows = do_query_array("SELECT * FROM mailbook WHERE con='student' AND con_id=$this->id", $this->sms->database, $this->sms->ip);
		$rows = sortArrayOfObjects($rows, $this->sms->getItemOrder('mailbook-student-'.$this->id), 'id');
		if( $all==false ){
			$first = reset($rows);
			return isset($first->mail) ? $first->mail : '';
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $r){
					$out[] = $r->mail;
				} 
			}
			return $out;
		}
	}
	
	public function getAddress($all=false, $lang='', $data=false){
		$phonebook = new AddressBook('student', $this->id, $this->sms);
		$rows = do_query_array("SELECT * FROM addressbook WHERE con='student' AND con_id=$this->id", $this->sms->database, $this->sms->ip);
		$rows = sortArrayOfObjects($rows, $this->sms->getItemOrder('addressbook-student-'.$this->id), 'id');
		if($lang == ''){ $lang = $_SESSION['lang'];}
		if( $all==false ){
			$first = reset($rows);
			if(isset($first->address_ar)){
				if($data){
					return $first;
				} else {
					return AddressBook::toStr($first);
				}
			} else {
				return '';
			}
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $row){
					if($data){
						$out[] = $row;
					} else {
						$out[] = AddressBook::toStr($row);
					}
					
				} 
			}
			return $out;
		}
	}
	
	public function getClass(){
		if(!isset($this->class)){
			$r = do_query_obj("SELECT class_id FROM classes_std WHERE std_id=$this->id", $this->sms->db_year, $this->sms->ip);	
			if(isset($r->class_id) && $r->class_id != ''){
				$this->class = new Classes($r->class_id, '', $this->sms);
			} else {
				$this->class = false;
			}
		}
		return $this->class;
	}
	
	public function getLevel(){
		if(!isset($this->level)){
			$class = $this->getClass();
			$this->level = $class!= false ? $class->getLevel() : false;
		}
		return $this->level;
	}
	
	public function getEtab(){
		if(!isset($this->etab)){
			$level = $this->getLevel();
			if(isset($level->id)){
				$this->etab = $level->getEtab();
			}else {
				return false;
			}
		}
		return $this->etab;
	}
	
	public function getGroups(){
		if(!isset($this->groups)){
			$this->groups = array();
			$sql_group= "SELECT groups.id FROM groups , groups_std
			WHERE groups.id=groups_std.group_id
			AND groups_std.std_id=$this->id";		
			$g_query = do_query_array( $sql_group, $this->sms->db_year, $this->sms->ip);
			if($g_query != false && count($g_query) > 0){
				foreach($g_query as $gr){
					$this->groups[] = new Groups($gr->id);
				}
			}
		}
		return $this->groups;
	}

	public function getServices($write=false, $year=''){
		global $ig_mode_lvl;
		if($year == ''){ $year = $_SESSION['year'];}
		if(!isset($this->services)){
			$out= array();
			if($this->sms->getSettings('ig_mode') != '1'){
				$services= do_query_array("SELECT services FROM materials_std WHERE std_id =".$this->id, Db_prefix.$year, $this->sms->ip);
				if(count($services) > 0){
					foreach($services as $service){
						$out[] = new services($service->services, $this->sms);
					}
				} else {
					if($write){// insert
						$new_services = array();
							//group services
						$group_services = do_query_array("SELECT groups.services FROM `groups`, groups_std WHERE `groups`.id =groups_std.group_id AND `groups`.id=$group_id AND std_id=$this->id",Db_prefix.$year, $this->sms->ip);
						if(count($chk_std_gr) > 0){
							$new_services[] = $group_services->service;
							$out[] = new services($group_services->service, $this->sms);
						}
							// class service
						$class = $this->getClass();
						$class_services = $class->getServices();
						foreach($class_services as $service){
							if($service->optional != 0){
								$new_services[]  = $service->id;
								$out[] = $service;
							}
						}
						if(count($new_services) > 0){
							do_query_edit("INSERT INTO materials_std (std_id, services) VALUES ($this->id, ".implode("), ($this->id,", $new_services).")", $this->sms->db_year, $this->sms->ip);
						}
					}
				}
			} else {
				$services= do_query_array("SELECT DISTINCT(services) FROM materials_std WHERE std_id =".$this->id, Db_prefix.$year, $this->sms->ip);
				if(count($services) > 0){
					foreach($services as $service){
						$out[] = new ServicesIG($service->services, $this->sms, $year);
					}
				}
				//$out = $out;sortArrayOfObjects($out, $ig_mode_lvl, 'lvl');
			}
			$this->services = Services::orderService($out);
		}
		return $this->services;
	}
	
	public function loadMainLayout(){
		global $lang, $prvlg, $this_system;
		if(getPrvlg('parents_read')){
			if($this->parent_id != ''){
				$parents =new parents($this->parent_id, $this->sms);
				$parents_form = $parents->loadMainLayout();
			} else {
				$parents_form = parents::newParents($this->id);
			}
		}
		$student_toolbox = array();
		if ($this->status==0){ // Desinscriped
			$student_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="certRadiation" std_id="'.$this->id.'"',
				"text"=> $lang['cert_rad'],
				"icon"=> "print"
			);
		} elseif($this->status==1){ // Registred student
			$student_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="certScholarity" std_id="'.$this->id.'"',
				"text"=> $lang['cert_school'],
				"icon"=> "print"
			);
			if( getPrvlg('std_edit')){
				$student_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="disinscripStd" std_id="'.$this->id.'"',
					"text"=> $lang['suspend'],
					"icon"=> "cancel"
				);
			}
		} elseif($this->status==2 && getPrvlg('std_add')){ // Waiting list
			$student_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="insripStd" std_id="'.$this->id.'"',
				"text"=> $lang['inscrip'],
				"icon"=> "check"
			);
		} elseif($this->status==3){
			$student_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="certScholarity" std_id="'.$this->id.'"',
				"text"=> $lang['print'],
				"icon"=> "print"
			);
			if( getPrvlg('std_add')){
				$student_toolbox[] = array(
					"tag" => "a",
					"attr"=> 'action="insripStd" std_id="'.$this->id.'"',
					"text"=> $lang['inscrip'],
					"icon"=> "check"
				);
			}
		}elseif($this->status==5){
			$student_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'action="certScholarity" std_id="'.$this->id.'"',
				"text"=> $lang['cert_grad'],
				"icon"=> "print"
			);
		}
		if(getPrvlg('std_login_infos')){ 
			$student_toolbox[] = array(
				"tag" => "a",
				"attr"=> 'module="settings" action="openUser" group="student" userid="'.$this->id.'"',
				"text"=> $lang['login_infos'],
				"icon"=> "comment"
			);
		}
		$student_toolbox[] = array(
			"tag" => "a",
			"attr"=> 'action="print_pre" rel="#MS_dialog_students-'.$this->id .' #student-infos"',
			"text"=> $lang['print'],
			"icon"=> "print"
		);
		
		$layout = new Layout($this);
		
		$out = write_html('div', 'class="tabs" stdid="'.$this->id .'"',
			write_html('ul', '',
				write_html('li', '', write_html('a', 'href="#student-infos"',$lang['personel_infos'])).
				(getPrvlg('parents_read') ? 
					write_html('li', '', write_html('a', 'href="#parents-infos" ',$lang['parents']))
				: '').
				write_html('li', '', write_html('a', 'href="index.php?module=students&history&std_id='.$this->id.'&sms_id='.$this->sms->id.'"',$lang['history'])).
				write_html('li', '', write_html('a', 'href="index.php?module=medical&std_id='.$this->id.'&sms_id='.$this->sms->id.'"',$lang['medical'])).
				(in_array($this->status, array('1', '3')) ? 
					($this_system->type == 'sms' && $layout->pro_option != 'hidden' ?
						write_html('li', '', write_html('a', 'href="index.php?module=services&list&con=student&con_id='.$this->id.'" ',$lang['materials'])).
						write_html('li', '', write_html('a', 'after="initExamTable" href="index.php?module=marks&con=student&con_id='.$this->id.'"',$lang['marks'])).
						write_html('li', '', write_html('a', 'after="initTimeTable" href="index.php?module=schedule&con=student&con_id='.$this->id.'"',$lang['schedule']))
					: '').
					($prvlg->_chk('att_absent_read') ?
						write_html('li', '', write_html('a', 'href="index.php?module=absents&std_id='.$this->id.'"',$lang['absents']))
					:'').
					($prvlg->_chk('read_behavior') ?
						write_html('li', '', write_html('a', 'href="index.php?module=behavior&con=student&con_id='.$this->id.'"',$lang['behavior']))
					:'')
				: '').
				($this->sms->getSettings('libms_server')== 1 ?
					write_html('li', '', write_html('a', 'href="index.php?module=profiler&q=libms&id='.$this->id.'"',$lang['librarys']))
				: '').
				($this->sms->getSettings('safems_server')== 1 &&  ($prvlg->_chk('read_std_fees') || $prvlg->_chk('read_std_fees_stat'))? 
					write_html('li', '', write_html('a', 'href="index.php?module=fees&con=student&sms_id='.$this->sms->id.'&con_id='.$this->id.'"',$lang['accounting']))
				: '')			
			).
			write_html('div', 'id="student-infos"', 
				createToolbox($student_toolbox).
				$this->loadDataLayout()
			).
			(getPrvlg('parents_read') ? 
				write_html('div', 'id="parents-infos"', 
					$parents_form
				)
			: '')
		);

		return $out;
		
	}
	
	public function loadDataLayout($new=false){
		global $lang, $prvlg;
		$layout = new Layout($this);
		$layout->sms_id = $this->sms->ccid;

		// set std id for update purposes
		$layout->std_id = $this->id;
		// Languages Materials
		$langs = do_query_array("SELECT * FROM materials WHERE `group_id`=1", $this->sms->database, $this->sms->ip);
		$langs_arr = array();
		$langs_arr[0] = '';
		foreach($langs as $lng){
			$langs_arr[$lng->id] = $lng->{'name_'.$_SESSION['dirc']};
		}
		$layout->optional_service_opts_1 = write_select_options( $langs_arr, $this->lang_1, false);
		$layout->optional_service_opts_2 = write_select_options( $langs_arr, $this->lang_2, false);
		$layout->optional_service_opts_3 = write_select_options( $langs_arr, $this->lang_3, false);

		// Sex 
		$layout->sex_1_checked = $this->sex == 1 ? 'checked="checked"' : '';
		$layout->sex_2_checked = $this->sex == 2 ? 'checked="checked"' : '';
		
		// Religion 
		$layout->religion_1_checked = $this->religion == 1 ? 'checked="checked"' : '';
		$layout->religion_2_checked = $this->religion == 2 ? 'checked="checked"' : '';

		// Ig mode
		$layout->ig_mode = $this->sms->getSettings('ig_mode') == '0' ? 'hidden' : 'label';
		$layout->not_ig_mode = $this->sms->getSettings('ig_mode') == '0' ? '' : 'hidden';

		$layout->quit_hidden = in_array($this->status, array('1', '2', '4')) || $new==true ? 'hidden' : '';
		
		// Old School
		//$layout->old_school_hidden = $this->old_sch == '' ? 'hidden' : ''; 
		
		// class div
		$class_div ='';
		if($this->getStatus() == 0 && $new==false){ 
			$class_div = write_html('h3', 'class="title"', $lang['desinscriped']).
			( $this->suspension_reason != '' ? 
				write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
					write_html('tr', '',
						write_html('td', 'width="85" valign="middel" ', 
							write_html('label', 'class="label"', $lang['suspension_reason'])
						).
						write_html('td', 'class="def_align"',
							'<input name="suspension_reason" type="text" id="transfered_to" value="'.$this->suspension_reason .'" />'
						)
					)
				)
			: '' );
		} elseif(in_array($this->getStatus(), array('1', '3'))){
			$stdclass = $this->getClass();
			if(!in_array($_SESSION['group'], array('student', 'parent', 'prof', 'supervisor'))){
				$sms = $this->sms;
				$classes = Classes::getList(true);
				$array_classes = array(''=>'');
				foreach($classes as $cl){
					$array_classes[$cl->id] = $cl->getName();
				}
				$class_html = write_html_select('name="class_id" class="combobox" id="class_id"  ', $array_classes, $stdclass != false ? $stdclass->id : '');
			} else {
				$class_html = write_html('div', 'class="ui-corner-right fault_input"',  $stdclass->getName());
			}
			$class_div = write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
				write_html('tr', '',
					write_html('td', 'width="85" valign="middel" ', 
						write_html('label', 'class="label"', $lang['class'])
					).
					write_html('td', 'valign="middel" class="def_align"',
						$class_html
					)
				)
			);
			if($this->getStatus() == "3"){
				$class_div .= write_html('h2', '', $lang['reservations']);
			}
		} elseif( $this->getStatus()  == "2" ){
			$w_level = do_query_obj("SELECT level_id FROM waiting_list WHERE std_id=$this->id", $this->sms->database, $this->sms->ip);
			$std_level = new Levels($w_level->level_id, $this->sms);
			
			$classes =  $std_level->getClassList();
			$level_name = $std_level->getName();
			$class_arr = array(0=> '');
			foreach($classes as $class){
				$class_arr[$class->id] = $class->getName()." - ".$level_name;
			}
			
			$class_div = write_html('h3', 'class="title"', $lang['waiting_list']).
			write_html('table', 'width="100%" border="0" cellspacing="1" cellpadding="0"',
				write_html('tr', '',
					write_html('td', 'width="85" valign="middel" ', 
						write_html('label', 'class="label"', $lang['class'])
					).
					write_html('td', 'valign="middel"',
						write_html_select('name="class_id" class="combobox" id="class_id" ', $class_arr, '')
					)
				)
			);
		} elseif($this->getStatus() == 5 ) { 
			$class_div = write_html('h3', 'class="title"', $lang['gruaduated']);
		}
		$layout->class_div = $class_div;
	
		// Thunmbnais
		$img_div = $this->getThumb($new?false:true);
		$layout->img_div = $img_div;
		
		// Phonebook
		$phonebook = new PhoneBook('student', $this->id, $this->sms);
		$layout->phone_book = $phonebook->getList();
		// Addressbook
		$addressbook = new AddressBook('student', $this->id, $this->sms);
		$layout->address_div = $addressbook->getList();
		// mailbook
		$mailbook = new MailBook('student', $this->id, $this->sms);
		$layout->mail_book = $mailbook->getList();
		// Notebook
		$notebook = new NoteBook('student', $this->id, $this->sms);
		$layout->notes_div = $notebook->getList();

		$layout->editable_btn = $prvlg->_chk('std_edit') ? 'inline-block' : 'none';

		// Bus Fragment
		if($this->sms->getSettings('busms_server') != '1' ){
			$layout->bus_div = '<input name="bus_code" type="text" id="bus_code" class="input_half" value="'. ($this->bus_code > -1 ?$this->bus_code :'') .'" />';
		} else {
			$bus_code = $this->getBus();
			if($bus_code == false){
				$field_id = 'bus_code-'. $this->id ;
				$layout->bus_div = write_html('div', 'class="fault_input"',$lang['no']);
			} else {
				$layout->bus_div = '<input name="bus_code" type="hidden" id="bus_code" value="'. $bus_code .'" /><div class="fault_input" style="min-width:70px">'. $bus_code .' </div>'.
					write_html('button' , 'type="button" module="students" action="getBusCard" std_id="'.$this->id.'" sms_id="'.$sms->id.'" class="circle_button ui-state-default hoverable"',
						write_icon('extlink')
					);
			}
			
		}
		
		// Editable
		$layout->editable = getPrvlg('std_edit') ? 1 : 0;
		return fillTemplate("$this->thisTemplatePath/student_infos.tpl", $layout);
		
	}
	
	public function getThumb($stat=true){
		global $lang;
		$img_div = '';
		if($stat){
			if($this->status == 5 ){
				$img_div .= '<img src="assets/img/graduation_hat.png" style="margin-top:-25px; margin-left:-2px; position:absolute" />';
			} elseif($this->status == 3 || $this->status == 2){
				$img_div .= '<img src="assets/img/warning.png" style="position:absolute" width="48" />';
			}elseif($this->status == 0 ){
				$img_div .= '<img src="assets/img/desinscripted.png" style="position:absolute"  width="48" />';
			}
		}
		$img_div .= write_html('a', 'class="hand" title="'.$lang['change'].'" onclick="changStdThumb('.$this->id.')"',
			'<img src="'.$this->getPhotoPath().'" alt="'.$this->name.'" name="id_thumb" width="128" height="128" id="thumb-std-'.$this->id.'" border="0" />'
		);
		return $img_div;
	}
	
	public function getPhotoPath(){
		if(file_exists("attachs/files/$this->id/folder.jpg")){
			return 'attachs/files/'.$this->id.'/folder.jpg"';
		} else {
			if($this->sex == 2){
				return 'assets/img/students_f.png';
			} else {
				return 'assets/img/students.png';
			}
		}
	}
	
	public function getBus($data=false){
		if($this->sms->getSettings('busms_server') == 1 ){
			$busms = $this->sms->getBusms();
			$this_cc_code = $this->sms->ccid;
			$bus = do_query_array("SELECT route_id FROM route_members WHERE con='std' AND con_id=$this->id AND cc_id='$this_cc_code'", $busms->database, $busms->ip);
			if($bus != false && count($bus)>0){
				$this->bus_code = '';
				if(!$data){
					foreach($bus as $b){	
						$route =  new Routes($b->route_id, $busms);
						$out[] = $route->no;
					}
					return implode(',' , $out);
				} else {
					foreach($bus as $b){	
						$out[] = new Routes($b->route_id, $busms);
					}
					return $out;
				}
			} else {
				$this->bus_code = false;
			}
		} else {
			return $this->bus_code;
		}
	}
	
	public function getBusCard(){
		global $lang, $sms;
		if($this->sms->getSettings('busms_server') == 1 ){
			$busms = $sms->getBusms();
			$this_cc_code = $sms->id;
			$layout = new Layout();
			$layout->template = 'modules/students/templates/bus_card.tpl';
			$layout->tel_problem = $busms->getSettings('tel_problem');
			$layout->tel_emrgency = $busms->getSettings('tel_emrgency');
			$layout->school_name = $sms->getName();
			$layout->student_name = $this->getName();
			$class = $this->getClass();
			$layout->class_name= $class!=false? $class->getName() :'';
			
			$rows = do_query_array("SELECT * FROM route_members WHERE con='std' AND con_id=$this->id AND cc_id='$this_cc_code'", $busms->database, $busms->ip);
			
			if(count($rows) == 1 && $rows[0]->address_id!= ''){
				$layout->spc_address_hidden = 'hidden';
				$main_address = do_query_obj("SELECT * FROM addressbook WHERE id=".$rows[0]->address_id, $sms->database, $sms->ip);
				$layout->main_address = AddressBook::toStr($main_address);
			} elseif(count($rows) == 2){
				$layout->main_address_hidden = 'hidden';
			}
			
			foreach($rows as $row){
				$route = new Routes($row->route_id, $busms);
				$matron = new Matrons($route->matron_id, $busms);
				if($row->m_time != ''){
					$layout->m_route_no = $route->no;
					$layout->m_time = $row->m_time;
					$layout->m_matron = $matron->getName();
					$layout->m_tel = $matron->getTel();
					if(count($rows) >1 ){
						$m_address = do_query_obj("SELECT * FROM addressbook WHERE id=".$row->address_id,$sms->database, $sms->ip);
						$layout->m_address = AddressBook::toStr($m_address);
					}
				}	
				if($row->e_time != ''){
					$layout->e_route_no = $route->no;
					$layout->e_time = $row->e_time;
					$layout->e_matron = $matron->getName();
					$layout->e_tel = $matron->getTel();
					if(count($rows) >1 ){
						$e_address = do_query_obj("SELECT * FROM addressbook WHERE id=".$row->address_id,$sms->database, $sms->ip);
						$layout->e_address = AddressBook::toStr($e_address);
					}
				}
			}
			
			return $layout->_print();
		} else {
			return 'No busms';
		}
	}
		
	public function getRegStatus(){
		$stat = do_query_obj("SELECT new_stat FROM classes_std WHERE std_id=$this->id", $this->sms->db_year, $this->sms->ip);
		return $stat!=false ? $stat->new_stat : 1;
	}
	
	public function getAge($date=''){
		global $lang;
		if($this->birth_date != ''){
			$birth_date = date_create();
			date_timestamp_set($birth_date, $this->birth_date);
			$date = new DateTime($_SESSION['year'].'-10-1' ); 
			$interval = date_diff($date, $birth_date);
			$years = $interval->format('%y');
			$months = $interval->format('%m');
			$days = $interval->format('%d'); 
			$out = $years.' '.$lang['years'].' | '.$months.' '.$lang['months'].' | '.' '.$days.' '.$lang['days'];
			return $out;
		} else {
			return '';
		}
	}
	
	public function getBrothers(){
		$bros = do_query_array("SELECT id FROM student_data WHERE parent_id='$this->parent_id' AND id!=$this->id", $this->sms->database, $this->sms->ip);
		$lis = array();
		if($bros != false &&count($bros) > 0){
			foreach($bros as $bro){
				$brother = new Students($bro->id, $this->sms);
				$class_name = '';
				if($brother!=false){
					if(in_array($brother->status, array('1', '3'))){
						$bro_class = $brother->getClass();
						$class_name = $bro_class !=false ? $bro_class->getName() : '';
					}
					$lis[] = write_html('li', '', 
						write_html('a', 'class="hand ui-state-default hoverable" action="openStudent" std_id="'.$bro->id.'"', 
						$brother->getName()).
						$class_name
					);
				}
			}
		}
		if(count($lis) > 0){
			return write_html('ul', 'class="brothers_ul"',
				implode('', $lis)
			);
		} else {
			return '';
		}
	}
	
	static function newStudent(){
		global $sms, $lang;
		$layout = new Layout();
		$layout->quit_hidden = "hidden";	
		$layout->sms_id = $sms->ccid;
		// Languages Materials
		$langs = do_query_array("SELECT * FROM materials WHERE `group_id`=1", $sms->database, $sms->ip);
		$langs_arr = array();
		$langs_arr[0] = '';
		foreach($langs as $lng){
			$langs_arr[$lng->id] = $lng->{'name_'.$_SESSION['dirc']};
		}
		$layout->optional_service_opts_1 = write_select_options( $langs_arr, '', false);
		$layout->optional_service_opts_2 = write_select_options( $langs_arr, '', false);
		$layout->optional_service_opts_3 = write_select_options( $langs_arr, '', false);

		// Thunmbnais
		$layout->img_div = '<img src="assets/img/students.png" name="id_thumb" width="128" height="128" id="thumb-std-new" border="0" />';
				
		// Bus Fragment
		if($sms->getSettings('busms_server') == '0'){
			$layout->bus_div = '<input name="bus_code" type="text"  class="input_half" value="" />';
		}
		
	
		// Editable
		$layout->editable = getPrvlg('std_add') ? 1 : 0;
		$layout->id = 'new';
		$layout->insert_hidden = 'hidden';
		return fillTemplate("modules/students/templates/student_infos.tpl", $layout);
		
	}
		
	static function saveStudent($post){
		global $sms;
		$answer = array();
		if($post['std_id'] != ''){
			$std_id = $post['std_id'];
			if(do_update_obj($post, "id=$std_id", "student_data", $sms->database, $sms->ip) != false){
				if(isset($post['class_id']) &&  $post['class_id']!= ''){
					$class_id = $post['class_id'];
					students::changeClass($std_id, $class_id);
				}
				$answer['id'] = $std_id;
				$answer['error'] = '' ;
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		} elseif($post['std_id'] == ''){ // new Student
			if($std_id = do_insert_obj($post, "student_data", $sms->database, $sms->ip)){
				$answer['id'] =  $std_id;
				$answer['error'] = '';
			} else {
				$answer['error'] = $lang['error_updating'];
			}
		}
		
		return json_encode($answer);
	}
	
	static function changeClass($std_id, $new_class_id){
		global $sms, $lang;
		$student = new Students($std_id);
		$old_class = $student->getClass();
		do_delete_obj("std_id=$std_id", 'classes_std', $sms->db_year, $sms->ip);
		$new = array('class_id'=>$new_class_id, 'std_id'=>$std_id);
		if(do_insert_obj( $new, 'classes_std', $sms->db_year, $sms->ip)){
			$new_class = new Classes($new_class_id);
			if($old_class != false && isset($old_class->id)){
				if($old_class->getlevel()->id != $new_class->getLevel()->id){
					$user = new Users();
					$fees_note = array(
						'std_id'=>$std_id,
						'sms_id'=>$sms->ccid,
						'user_id'=> '0',
						'notes'=> $lang['student_have_changed_class']. unixToDate(time()). ' - '.$user->getRealName()
					);
					$safems = $sms->getSafems();
					do_insert_obj($fees_note, 'students_notes', $safems->database, $safems->ip);
				}
			}
		}
	}
	
	static function suspension($std_id){
		global $sms;
		$layout = new Layout();
		$layout->std_id = $std_id;
		$layout->date = unixTodate($sms->getYearSetting('end_date'));
		return fillTemplate("modules/students/templates/suspension.tpl", $layout);
	}


	static function saveSuspension($post){
		global $lang, $sms;
		$id = $post['id'];
		$student = new Students($id, $sms);
		$class = $student->getClass();
		$class_name = $class->getName();
		$level = $student->getLevel();
		$level_name = $level->getName();
		$suspension_reason = $post['suspension_reason'];
		$status = $post['status'];	
		$quit_date = dateToUnix( $post['quit_date']);
		$update = array('status'=>$status, 'quit_date'=>$quit_date, 'suspension_reason'=>$suspension_reason);		
		/*if(isset($post['suspension_till_date'])){
			$update["suspension_till_date"] = $post['suspension_till_date'];
		}*/
		
		if(do_update_obj($update, "id=$id", 'student_data', $sms->database, $sms->ip)){
			//$student->till_date = isset($post['suspension_till_date']) ? $post['suspension_till_date'] : '';
			$student->status = $status;
			$years = do_query_array("SELECT * FROM years WHERE begin_date>=$quit_date", $sms->database, $sms->ip);
			foreach($years as $year){
				do_delete_obj("std_id=$id", 'classes_std', Db_prefix.$year->year, $sms->ip);
			}
			//$m = systemMessages::sendDesinscriptionMsg($student);			
			return json_encode(array('error' => ''));
		} else {
			return json_encode(array('error' => $lang['error_updating']));
		}
	}
	
	static function inscription($std_id){
		global $sms;
		$classes = Classes::getList();
		foreach($classes as $class){
			$classes_arr[$class->id] = $class->getName();
		}
		$student = new Students($std_id, $sms);
		$layout = new Layout();
		$layout->std_id = $std_id;
		$layout->date = $student->join_date;
		$layout->classes_opts = write_select_options($classes_arr, $student->getClass()->id);
		return fillTemplate("modules/students/templates/inscription.tpl", $layout);
	}
	
	static function saveInscription($post){
		global $lang, $sms;
		$id = $post['id'];	
		$join_date =dateToUnix( $post['join_date']);	
		$class_id = $post['class_id'];
		$class = new Classes($class_id, '', $sms);
		$student = new Students($id, $sms);

		if(do_query_edit("UPDATE student_data SET status=1, suspension_reason='NULL', join_date=$join_date WHERE id=$id", $sms->database, $sms->ip)){
			do_query_edit( "DELETE FROM waiting_list WHERE std_id=$id", $sms->database, $sms->ip);
			$student->status = 1;
			students::changeClass($id, $class_id);
			$m = systemMessages::sendInscriptionMsg($student);
			return json_encode(array('error' => ''));
		} else {
			return json_encode(array('error' => $lang['error_updating']));
		}
	}	

	static function getAutocompleteStudent( $value, $stats=array('1')){
		global $lang, $sms;
		if($stats == '') {$stats = array('1');}
		$stats = is_array($stats) ? $stats : (strpos( $stats, ',') !== false ? explode(',', $stats) : array($stats));
		$Arabic = new I18N_Arabic('KeySwap');
		$params = array("name_ar = '$value' ",
			"name_ar LIKE '$value%' ",
			"name_ar LIKE '".addslashes($Arabic->swapEa($value))."%' ",
			"LOWER(name) LIKE LOWER('$value') ",
			"LOWER(name) LIKE LOWER('$value%') ",
			"LOWER(name) LIKE LOWER('".addslashes($Arabic->swapAe($value))."%')"
		);
			
		if(strpos($value, " ") !== false){
			$s = explode(" ", $value);
			$first = trim($s[0]);
			$last = trim($s[count($s)-1]);
			$not_first = substr_replace($value, '', 0, strlen($first));
			$not_first = trim($not_first);
			$middle = substr_replace($not_first, '', -1* strlen($last), -1);
			$middle = trim($middle);
			$params[] = "(LOWER(name) LIKE LOWER('$first') AND LOWER(middle_name) LIKE LOWER('$middle%'))";
			$params[] = "(LOWER(name) LIKE LOWER('$first') AND LOWER(middle_name) LIKE LOWER('$not_first%'))";
			$params[] = "(LOWER(name) LIKE LOWER('$first') AND LOWER(middle_name) LIKE LOWER('$middle') AND LOWER(last_name) LIKE LOWER('$last%'))";			
		} 
		$sql = "SELECT id, status FROM student_data WHERE (". implode(" OR ", $params) .")";
//echo $sql;
		$out = array();
		$stds_ids = do_query_array( $sql, $sms->database, $sms->ip);
		if($stds_ids != false && count($stds_ids)>0){
			$all_classes = Classes::getList();
			foreach($stds_ids as $std_id ){
				$student = new Students($std_id->id, $sms);
				if(in_array($student->status, $stats)){
					$student_name = $student->getName();
					$label = str_replace($value, write_html('b', 'style="color:red"', $value), $student_name);
					$std = array('id'=>$std_id->id, 'name'=> $student_name, 'label'=> $label, 'status'=> $student->status);
					$class = $student->getClass();
					//	if(!in_array($_SESSION['group'], array('student', 'parent')) /*&& in_array($class, $all_classes)*/){			
							$std['clas'] = isset($class->id) ? $class->getName():'';
					//	}
					$out[] = $std;
				}
			}
		} else {
			$out[] = array('error' => $lang['cant_find_std']);	
		}
		return json_encode($out);
	}
	
	/**************** School Fees ************************/
	public function getProfil(){
		$prof = do_query_obj("SELECT profil_id FROM school_fees_profil_std WHERE std_id=$this->id", $this->sms->database, $this->sms->ip);
		if($prof != false && $prof->profil_id != ''){
			return new Profils($prof->profil_id, $this->sms);
		} else {
			return false;
		}
	}

	public function getFees($year){
		global $sms;
		$busms = $sms->getBusms();
		$out = array();
		$level = $this->getLevel();
			// Profil Or Level
		$profil = $this->getProfil();
		if($profil != false){
			$profil_fees = $profil->getFees($year);
			if($profil_fees != false && count($profil_fees) > 0){
				$out = array_merge($out, $profil_fees);
			} else {
				$out = array_merge($out, $level->getFees($year));
			}
		} else {
			if(isset($level->id)){
				$out = array_merge($out, $level->getFees($year));
			}
		}
		if($sms->getSettings('ig_mode') == '1'){
			$servicesFees = do_query_array("SELECT * FROM school_fees WHERE year=$year AND con_id=$this->id AND con='services'", $this->sms->database, $this->sms->ip);
			if($servicesFees!= false){
				foreach($servicesFees as $ser){
					$out[] = new Fees($ser->id, $this->sms);
				}
			}
		}
			// Extra
		$extraFees = do_query_array("SELECT * FROM school_fees WHERE year=$year AND con_id=$this->id AND con='student'", $this->sms->database, $this->sms->ip);
		foreach($extraFees as $f){
			$out[] = new Fees($f->id, $this->sms);
		}

			// Bus
		$route_id = $this->getBus();
		if($route_id != false && $route_id!=' ' && $route_id!=''){
			$bus = new BusFees($this->sms);
			$route = new Routes($route_id, $busms);
			$group = $route->getGroup();
			$busFees = $bus->getFees($group->id, $year);
			if($busFees != false){
				foreach($busFees as $f){
					$f->value = $f->debit;
					$out[] = $f;
				}
			}
		}
			// Books
		$books = new BookFees($sms);
		$books_fees = $books->getLevelFees($level->id, $year);
		if(is_array($books_fees)){
			$out = array_merge($out, $books_fees);
		}

		if($out != false && count($out) > 0){
			return $out;
		} else {
			return array();
		}
	}
	
	public function getDates($year){
		$dates = do_query_array("SELECT * FROM school_fees_dates WHERE con='student' AND con_id=$this->id AND year=$year ORDER BY `from` ASC", $this->sms->database, $this->sms->ip);
		if($dates != false && count($dates) > 0){
			return $dates;
		} else  {
			$level = $this->getLevel();
			if(isset($level->id)){
				return $level->getDates($year); 
			} else {
				return false;
			}
		}
	}
	
	public function getFeesBydate($fees, $date){
		$payment= do_query_obj("SELECT SUM(value) AS value FROM school_fees_payments WHERE fees_id=$fees->id AND term=$date->id AND con='student' AND con_id='$this->id' AND year=".$_SESSION['year'], $this->sms->database, $this->sms->ip);
		if($payment->value == ''){
			$payment= do_query_obj("SELECT SUM(value) AS value FROM school_fees_payments WHERE fees_id=$fees->id AND term=$date->id AND con='$fees->con' AND con_id='$fees->con_id' AND year=".$_SESSION['year'], $this->sms->database, $this->sms->ip);
		}
		return $payment->value;
	}
	
	public function generatePayments($year=''){
		$year = $year != '' ? $year : $_SESSION['year'];
		$dates = $this->getDates($year);
		$profil = $this->getProfil();
		$sms = $this->sms;
		$safems = $sms->getSafems(); 
		$result = true;
		$schoolFees= new SchoolFees($sms);
		$sms_dates = $schoolFees->getDates($year);
		$old = do_query_array("SELECT SUM(paid) AS paid, currency, fees_id FROM school_fees WHERE std_id=$this->id AND cc=$sms->ccid AND year=$year GROUP BY fees_id,currency", $safems->database, $safems->ip);
		$paid =array();
		foreach($old as $row){
			$f = new Fees($row->fees_id, $sms);
			$acc = $f->getMainAccCode();
			if(!isset($paid[$acc][$row->currency])){
				$paid[$acc][$row->currency] = 0;
			} 
			$paid[$acc][$row->currency] += $row->paid;
		}
			// delete old val
		do_query_edit("DELETE FROM school_fees WHERE std_id=$this->id AND cc=$sms->ccid AND year=$year", $safems->database, $safems->ip);
		
		$first_date = reset($dates);
		if($first_date->con == 'student'){
			$fees = do_query_array("SELECT * FROM school_fees_payments WHERE year=$year AND con='student' AND con_id=$this->id AND year=$year", $sms->database, $sms->ip);
			foreach($fees as $f){
				$fe = new Fees($f->fees_id);
				$value = $f->value;
				$insert= array(
					'fees_id' => $fe->id,
					'std_id' => $this->id,
					'year' => $year,
					'currency' => $fe->currency,
					'cc' => $sms->ccid,
					'date_id'=>$f->term
				);
				foreach(array_merge($dates, $sms_dates) as $date){
					if($date->id == $f->term){
						$insert['due_date'] = $date->limit;
					}
				}
				if($profil != false && $fe->discount == '1'){
					if($fe->con == 'bus'){
						$insert['value'] = $value - Profils::calcDiscount($value , $profil->bus_discount);
					} elseif($fe->con == 'books'){
						$insert['value'] =$value -  Profils::calcDiscount($value , $profil->lib_discount);
					} else {
						$insert['value'] = $value - Profils::calcDiscount($value , $profil->discount);
					}
				} else {
					$insert['value'] = $value;
				}
				if ($insert['value'] > 0 && !do_insert_obj($insert, 'school_fees',  $safems->database, $safems->ip)){
					$result = false;
				}
			}
		
		} else {
			$fees = $this->getFees($year);
			foreach($fees as $fee){
				$count_dates = 0;
				$totalvalue = $fee->debit;
				if($profil != false && $fee->discount == '1'){
					if($fee->con == 'bus'){
						$discount = $profil->calcDiscount($totalvalue , $profil->bus_discount);
					} elseif($fee->con == 'books'){
						$discount = $profil->calcDiscount($totalvalue , $profil->lib_discount);
					} else {
						$discount = $profil->calcDiscount($totalvalue , $profil->discount, $profil->exclude);
					}
				} else {
					$discount = 0;
				}
				$insert= array(
					'fees_id' => $fee->id,
					'std_id' => $this->id,
					'year' => $year,
					'currency' => $fee->currency,
					'cc' => $sms->ccid
				);
				
				krsort($dates);
				foreach($dates as $date){
					if($fee->con == 'bus' || $fee->con == 'books'){
						$date = $sms_dates[$count_dates];
					} 
					$insert['date_id'] = $date->id;
					$insert['due_date'] = $date->limit;					
//					
					
					$value = $this->getFeesBydate($fee, $date);
					if($discount>0){						
						if($value>=$discount){
							$insert['value'] = $value-$discount;
							$discount = 0;
						} else {
							$insert['value'] = 0;
							$discount = $discount-$value;							
						}
					} else {
						$insert['value'] = $value;
					}
					
					if ($insert['value'] > 0 ){
						if(!do_insert_obj($insert, 'school_fees',  $safems->database, $safems->ip)){
							$result = false;
						}
					}
					$count_dates++;
				}
			}
		}
		if(count($paid)>0){
			foreach($paid as $acc=>$values){
				foreach($values as $cur=>$val){
					if($val>0){
						$payment = array(
							'std_id'=>$this->id,
							'year'=>$year,
							'value'=>$val,
							'rel'=>$acc,
							'currency'=>$cur,
							'cc'=> $sms->ccid,
							'date'=>time()
						);
						Fees::addPayment($payment, array(), false);
						//$insert['paid'] = $paid > $insert['value'] ? $insert['value'] : $paid;
						$paid[$acc][$cur] -= $value;
					}
				}
			}
		}
		return $result;
	}
		
	public function saveProfil($profil_id){
		global $lang;
		if($profil_id != '0'){
			$sql = "REPLACE INTO school_fees_profil_std (profil_id, std_id) VALUES ($profil_id, $this->id)";
		} else {
			$sql = "DELETE FROM school_fees_profil_std WHERE std_id=$this->id";
		}
		if(do_query_edit($sql, $this->sms->database, $this->sms->ip)){
			$this->generatePayments();
			return array('error'=>'');
		} else {
			return array('error'=> $lang['error_updating']);
		}
	}
	
	public function getStaticFees(){
		return do_query_array("SELECT * FROM school_fees_static WHERE std_id=$this->id", $this->sms->database, $this->sms->ip);
	}
	
	public function getStaticFeesAmount($main_code, $sub_code, $cur=''){
		$sql = "SELECT value FROM school_fees_static WHERE std_id=$this->id AND main_code=$main_code AND sub_code=$sub_code";
		if($cur != false){
			$sql .= " AND currency='$cur'";
		}
		$rows = do_query_array($sql, $this->sms->database, $this->sms->ip);
		$out = array();
		if($rows != false){
			foreach($rows as $row){
				if($row->value!= '' && $row->value!=0){
					$out[$row->currency] = $row->value;
				}
			}
		}
		return $out;
	}

}
