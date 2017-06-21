<?php
/** SChool
*
*/

class SMS extends CSMS{
	public $type = 'sms';
	private static $_instance = null;	
	
	public function __construct($id=''){
		if($id != ''){
			parent::__construct($id);
		} else {
			$this->id='';
			if(strpos(MS_codeName,'sms_') == 0){
				$this->ip = '127.0.0.1';
				$this->url= $_SERVER['HTTP_HOST'];
				$this->database = SMS_Database;
				$this->id = $this->getCC();
				parent::__construct($this);
			} else {
				die("SMS not defined");
			}
		}
		if(isset($_SESSION['year'])){
			$this->db_year = Db_prefix.$_SESSION['year'];
		}
		$this->full_code = '151'.$this->getCC().'000000';
		$this->ccid = $this->getCC();
	}
	
	
	public function getName($other_lang=false){
		if($_SESSION['lang'] == 'ar' ){
			if($this->getSettings('school_name_ar')!=false ){
				return $other_lang ? $this->getSettings('school_name') : $this->getSettings('school_name_ar');
			} else {
				return $this->getSettings('school_name');
			}
		} else {
			return $other_lang && $this->getSettings('school_name_ar')!=false ? $this->getSettings('school_name') : $this->getSettings('school_name_ar');
		}
	}
	
	
	public function setSession($user){
		parent::setSession($user);
			// extra session data
		if(isset($user->cur_id)){
			$_SESSION['cur_id'] = $user->cur_id;
		}
		if(isset($user->std)){
			$_SESSION['std'] = $user->std;
		}
	}

	public function loadJsonSettings(){
		$config = parent::loadJsonSettings();
		$config['DB_student'] = SMS_Database;
		$config['MSEXT_msg'] = MSEXT_msg == true ? 1 : 0;
		$config['MSEXT_lms'] = MSEXT_lms == true ? 1 : 0;
		$config['MSEXT_docs'] = MSEXT_docs == true ? 1 : 0;
		return $config;
	}

	public function getCode(){
		return $this->getSettings('sch_code');
	}
	public function getAccCode(){
		return '151'.$this->getCC();
	}

	public function getCC(){
		return $this->getSettings('this_ccid');
	}

	public function getStudents($stats=array(1,3)){
		return do_query_array("SELECT id, name_ar, name, middle_name, last_name FROM student_data WHERE (status=".implode(" OR status=",$stats).")", $this->database, $this->ip);
	}
	
	public function countStudent($stats=array('1','3')){
		$sql = "SELECT COUNT(std_id) AS total FROM classes_std";
		$out = do_query_obj( $sql, Db_prefix.$_SESSION['year'], $this->ip);
		return $out->total;
	}

	static function getList(){
		global $this_system;
		$out = array();
		$smss = do_query_array("SELECT * FROM connections WHERE type='sms'", $this_system->database, $this_system->ip);
		foreach($smss as $sms){
			$out[] = new SMS($sms->id);	
		}
		return $out;
				
	}
	
	public function getEtabList(){
		$etabs = do_query_array("SELECT id FROM etablissement", $this->database, $this->ip);
		$out = array();
		foreach($etabs as $etab){
			$out[] = new Etabs($etab->id, $this);
		}
		return sortArrayOfObjects($out, $this->getItemOrder('etabs'), 'id');
	}
	
	public function getLevelList(){
		$levels = do_query_array("SELECT id FROM levels", $this->database, $this->ip);
		$out = array();
		foreach($levels as $level){
			$out[] = new Levels($level->id, $this);
		}
		return sortArrayOfObjects($out, $this->getItemOrder('levels'), 'id');
	}
	
	public function getAnyObjById($con , $con_id){
		$result = false;
		try{
			switch($con){
				case  "prof" :
					$result = new Profs($con_id, $this);
				break;
				case  "supervisor" :
					$result = new Supervisors($con_id, $this);
				break;
				case  "principal" :
					$result = new Principals($con_id, $this);
				break;
				case  "coordinator" :
					$result = new Coordinators($con_id, $this);
				break;
				case  "etab" :
					$result = new Etabs($con_id, $this);
				break;	
				case  "level" :
					$result = new Levels($con_id, $this);
				break;	
				case  "class" :
					$result = new Classes($con_id, '', $this);
				break;	
				case  "classes" :
					$result = new Classes($con_id, '', $this);
				break;	
				case  "student" :
					$result = new Students($con_id, $this);
				break;
				case  "tool" :
					$result = new Tools($con_id, $this);
				break;
				case  "hall" :
					$result = new Halls($con_id, $this);
				break;
				case  "material" :
					$result = new Materials($con_id, $this);
				break;
				case  "group" :
					$result = new Groups($con_id, $this);
				break;	
				case  "parent" :
					$result = new Parents($con_id, $this);
				break;	
				case  "father" :
					$result = new Parents($con_id, $this);
				break;	
				case  "mother" :
					$result = new Parents($con_id, $this);
				break;	
				case  "service" :
					$result = new Services($con_id, $this);
				break;
				case  "book" :
					$result = new Books($con_id, $this);
				break;
				case  "chapter" :
					$result = new Chapters($con_id, $this);
				break;
				case  "summary" :
					$result = new Summarys($con_id, $this);
				break;
				case  "profil" :
					$result = new Profils($con_id, $this);
				break;
				case  "school" :
					if (self::$_instance === null) {
						self::$_instance = new self;
					}
					$result = self::$_instance;
				break;
				case  "0" :
					if (self::$_instance === null) {
						self::$_instance = new self;
					}
					$result = self::$_instance;
				break;
				case  "bus" :
					if($this->getSettings('busms_server') == '1'){
						$busms = $this->getBusms();
					} else {
						$b = new stdClass();
						$b->database = $this->database;
						$b->ip = $this->ip;
						$busms = new BusMS($b);
					}
					$result = new groupRoutes($con_id, $busms);
				break;
				default :
					//echo $con;
					$result = new Employers($con_id, $this->getHrms());
				break;
			}
			return $result;
		} catch (Exception $e){
			//echo $e;
			return false;
		}
	
	}
	
	public function getReservedStudents(){
		return $this->getStudents(array(3));	
	}

	/****************** Reports ************************/
	public function loadSchoolBalance($year='', $inc_classes=false){		
		global $lang;		
		if($year == ''){
			$year = $_SESSION['year'];
		}
		$etabs = $this->getEtabList();
		$count_all_std_reg = 0;
		$count_all_std_reserv = 0;
		$count_all_class = 0;
		$trs = array();
		$serial = 1;
		$etab_arr  = array();
		foreach($etabs as $etab){
			$etab_id = $etab->id;
			$etab_name = $etab->getName();
			$std_by_etab_reg = 0;
			$std_by_etab_reserv = 0;
			$class_by_etab = 0;
			$levels = $etab->getLevelList(); 
			if(count($levels) > 0){
				foreach($levels as $level){
					$level_id = $level->id;
					$level_name = $level->getName();
					$std_by_level_reg = $this->getCountStudents('level', $level->id, $year, array('1'));
					$std_by_level_reserv = $this->getCountStudents('level', $level->id, $year, array('3'));
					if($std_by_level_reg>0){
						$classes = $level->getClassList($year);

						$class_by_level = count($classes);
						$class_by_etab = $class_by_etab + $class_by_level;
						
						$count_all_std_reg = $count_all_std_reg + $std_by_level_reg;
						$count_all_std_reserv = $count_all_std_reserv + $std_by_level_reserv;
						
						$std_by_etab_reg = $std_by_etab_reg + $std_by_level_reg;
						$std_by_etab_reserv = $std_by_etab_reserv + $std_by_level_reserv;
						
						$count_all_class =$count_all_class +$class_by_level;
						$trs[] = write_html('tr', 'style="font-weight:bold;"',
							write_html('td', '', $serial).
							write_html('td', '', $level->getName()).
							write_html('td', '', $class_by_level).
							write_html('td', '', $std_by_level_reg ).
							write_html('td', '', $std_by_level_reserv ).
							write_html('td', '', $std_by_level_reserv+$std_by_level_reg )
						);
						$serial++;
						
						foreach($classes as $class){
							$count_class_reg = $this->getCountStudents('class', $class->id, $year , array('1'));
							$count_class_reserv = $this->getCountStudents('class', $class->id, $year , array('3'));
							if($count_class_reg > 0){
								$trs[] = write_html('tr', 'class="class_tr '.($inc_classes==false? 'hidden' :'').'"',
									write_html('td', '', '').
									write_html('td', '', $class->getName()).
									write_html('td', '', '').
									write_html('td', '', $count_class_reg).
									write_html('td', '', $count_class_reserv>0 ?$count_class_reserv : '').
									write_html('td', '', $count_class_reg +$count_class_reserv)
								);
							}
						}
					}
				}
			}
			if($std_by_etab_reg>0){
				$trs[] = write_html('tr', 'style="font-weight:bold"',
					write_html('td', 'class="ui-state-default" colspan="2"', $lang['total'].': '. $etab_name).
					write_html('td', 'class="ui-state-default"', $class_by_etab).
					write_html('td', 'class="ui-state-default"', $std_by_etab_reg).
					write_html('td', 'class="ui-state-default"', $std_by_etab_reserv).
					write_html('td', 'class="ui-state-default"', $std_by_etab_reg + $std_by_etab_reserv)
				);
			}
		}
		$trs[] = write_html('tr', 'style="font-weight:bold; font-size:14px"',
			write_html('td', 'class="ui-state-default" colspan="2"', $lang['total_school']).
			write_html('td', 'class="ui-state-default"', $count_all_class).
			write_html('td', 'class="ui-state-default"', $count_all_std_reg).
			write_html('td', 'class="ui-state-default"', $count_all_std_reserv).
			write_html('td', 'class="ui-state-default"', $count_all_std_reg + $count_all_std_reserv)
		);
			
		$thead = write_html('thead', '', 
			write_html('tr', '',
				write_html('th', 'width="20"', $lang['ser']).
				write_html('th', '', $lang['level']).
				write_html('th', '', $lang['count_classes']).
				write_html('th', '', $lang['registred']).
				write_html('th', '', $lang['reservations']).
				write_html('th', '', $lang['count_student'])
			)
		);
		
		$school_balance = write_html('h2', '', $lang['school_balance_title'].' '. $_SESSION['year'].'/'. ($_SESSION['year']+1)).
		write_html('table', 'class="tablesorter"', 
			$thead.
			write_html('tbody', '', implode($trs, ''))
		);
		return $school_balance;
	
	}


	public function loadSchoolStatics(){
		global $lang;
		if(isset($_GET['etab_id']) && $_GET['etab_id'] != 0){
			$req_etab_id = safeGet($_GET['etab_id']);
			$etabs = array(new Etabs($req_etab_id, $this));	
		} else {
			$req_etab_id =0;
			$etabs = $this->getEtabList();	
		}
		
		$count_all_std = 0;
		$count_all_class = 0;
		$count_all_female = 0;
		$count_all_male = 0;
		$count_all_muslim = 0;
		$count_all_chistians = 0;
		$count_all_egyptians = 0;
		$count_all_forgeins = 0;
		$count_all_redouble = 0;
		$count_all_new = 0;
		$count_all_waiting = 0;
		$count_all_suspended = 0;
		$trs = array();
		$serial = 1;
		$pre_sql = "SELECT id FROM student_data WHERE ";
		foreach($etabs as $etab){
			$std_by_etab = 0;
			$class_by_etab = 0;
			$levels = $etab->getLevelList(); 
			foreach($levels as $level){
				$level_id = $level->id;
				$level_name = $level->getName();
				
				$count_class = count($level->getClassList());
				
				$count_waiting = count(do_query_array("SELECT std_id FROM waiting_list WHERE level_id=$level_id", $this->database, $this->ip));
				$count_all_waiting = $count_all_waiting + $count_waiting;
		
				$student_list = new StudentsList('level', $level->id, $this);
				$student_list->stats = array('1', '3');
				$students= $student_list->getStudents();
				$count_std= $student_list->getCount();
				if($count_std > 0){
					$stds = array();
					$count_new= 0;
					$count_redouble = 0;
					$count_christ= 0;
					$count_muslim = 0;
					$count_mal= 0;
					$count_female = 0;
					$count_suspended= 0;
					$count_egypt = 0;
					$count_forgn = 0;
					foreach($students as $student){
						if($student->getRegStatus() == 1){
							$count_new++;
						} else {
							$count_redouble++;
						}
						if($student->religion == 1){
							$count_muslim++;
						} else {
							$count_christ++;
						}
						if($student->sex == 1){
							$count_mal++;
						} else {
							$count_female++;
						}
						
						if($student->status == 3){
							$count_suspended++;
						}
						$stds[] = $student->id;
					}
					$where_stat = array();
					foreach($student_list->stats as $stat){
						$where_stat[] =" status=$stat";
					}
					$stat_sql = " (".implode(' OR ', $where_stat).")";		
					if(count($stds) > 0){
						$std_sql = '(id='.implode($stds, ' OR id=').')';
						
						$count_egypt = count(do_query_array($pre_sql. $std_sql." AND $stat_sql AND (nationality LIKE '%egyptian%' OR nationality_ar LIKE '%مصري%'  OR nationality_ar LIKE '%مصرى%')", $this->database, $this->ip));
						$count_forgn = count(do_query_array($pre_sql. $std_sql."AND $stat_sql AND (nationality NOT LIKE '%egyptian%' AND nationality_ar NOT LIKE '%مصري%')", $this->database, $this->ip));
						
					}
					$count_all_std = $count_all_std + $count_std;
					$count_all_class = $count_all_class + $count_class;
					$count_all_female = $count_all_female + $count_female;
					$count_all_male = $count_all_male + $count_mal;
					$count_all_muslim = $count_all_muslim + $count_muslim;
					$count_all_chistians = $count_all_chistians + $count_christ;
					$count_all_egyptians = $count_all_egyptians + $count_egypt;
					$count_all_forgeins = $count_all_forgeins + $count_forgn;
					$count_all_redouble = $count_all_redouble + $count_redouble ;
					$count_all_new = $count_all_new + $count_new;
					$count_all_suspended = $count_all_suspended + $count_suspended;

					$trs[] = write_html('tr', '',
						write_html('td', '', $serial).
						write_html('td', '', $level->getName()).
						write_html('td', '', $count_class).
						write_html('td', 'class="sex" ', $count_mal).
						write_html('td', 'class="sex" ', $count_female).
						write_html('td', 'class="religion" ', $count_muslim).
						write_html('td', 'class="religion" ', $count_christ).
						write_html('td', 'class="nationality" ', $count_egypt).
						write_html('td', 'class="nationality" ', $count_forgn).
						write_html('td', 'class="register_stat" ', $count_new).
						write_html('td', 'class="register_stat" ', $count_redouble).
						write_html('td', '', count($stds)).
						write_html('td', 'class="suspended" ', $count_suspended).
						write_html('td', 'class="waiting" ', $count_waiting)
					);
					$serial = $serial+1;
				}
			}
		}
		
		$thead = write_html('thead', '', 
			write_html('tr', '',
				write_html('th', 'style="background-image:none"', $lang['ser']).
				write_html('th', 'style="background-image:none"', '&nbsp;').
				write_html('th', 'style="background-image:none"', $lang['count_classes']).
				write_html('th', 'class="sex" style="background-image:none" colspan="2"', $lang['sex']).
				write_html('th', 'class="religion" style="background-image:none" colspan="2"', $lang['religion']).
				write_html('th', 'class="nationality" style="background-image:none" colspan="2"', $lang['nationality']).
				write_html('th', 'class="register_stat" style="background-image:none" colspan="2"', $lang['register_stat']).
				write_html('th', 'style="background-image:none"', '&nbsp;').
				write_html('th', 'class="suspended" style="background-image:none"', '&nbsp;').
				write_html('th', 'class="waiting" style="background-image:none"', '&nbsp;')
			).
			write_html('tr', '',
				write_html('th', 'width="20"', '&nbsp;').
				write_html('th', 'rawspan="2"', $lang['level']).
				write_html('th', 'width="50"', '&nbsp;').
				write_html('th', 'class="sex" width="50"', $lang['male']).
				write_html('th', 'class="sex" width="50"', $lang['female']).
				write_html('th', 'class="religion" width="50"', $lang['muslim']).
				write_html('th', 'class="religion" width="50"', $lang['christian']).
				write_html('th', 'class="nationality" width="50"', $lang['egyptian']).
				write_html('th', 'class="nationality" width="50"', $lang['forgein']).
				write_html('th', 'class="register_stat" width="50"', $lang['result_new']).
				write_html('th', 'class="register_stat" width="50"', $lang['result_redouble']).
				write_html('th', 'width="50"', $lang['count_student']).
				write_html('th', 'class="suspended" style="background-image:none"', $lang['suspended']).
				write_html('th', 'class="waiting" style="background-image:none"', $lang['waiting_list'])
			)
		);
		$etabs_arr = array('0' => $lang['all']);
		$all_etabs = Etabs::getList();	
		foreach($all_etabs as $etab){
			$etabs_arr[$etab->id] = $etab->getName();
		}
		
		$school_static = write_html('form', 'style="padding:3px" class="unprintable ui-corner-all ui-state-highlight optional"',
			((!isset($_GET['toolbox']) || $_GET['toolbox'] != 'false') ? 
				write_html('table', 'width="100%" border="0" cellspacing="0" class="optional"',
					write_html('tr', '',
						write_html('td', ' width="120" valign="middel"', 	
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['etab'])
						).
						write_html('td', '',
							write_html_select( 'id="static_con" update="openSchoolStatic" class="combobox"', $etabs_arr, $req_etab_id)
						)
					).
					write_html('tr', '',
						write_html('td', ' width="120" valign="middel"', 	
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['view'])
						).
						write_html('td', '',
							write_html('span', 'class="buttonSet"',
								'<input type="checkbox" id="static_chk_1" checked="checked" />'.write_html('label', 'for="static_chk_1"  onclick="toogleStaticElement(\'sex\')"', $lang['sex']).
								'<input type="checkbox" id="static_chk_2" checked="checked" />'.write_html('label', 'for="static_chk_2" onclick="toogleStaticElement(\'religion\')"', $lang['religion']).
								'<input type="checkbox" id="static_chk_3" checked="checked" />'.write_html('label', 'for="static_chk_3" onclick="toogleStaticElement(\'nationality\')"', $lang['nationality']).
								'<input type="checkbox" id="static_chk_4" checked="checked" />'.write_html('label', 'for="static_chk_4" onclick="toogleStaticElement(\'register_stat\')"', $lang['register_stat']).
								'<input type="checkbox" id="static_chk_5" checked="checked" />'.write_html('label', 'for="static_chk_5" onclick="toogleStaticElement(\'suspended\')"', $lang['suspended']).
								'<input type="checkbox" id="static_chk_6" checked="checked" />'.write_html('label', 'for="static_chk_6" onclick="toogleStaticElement(\'waiting\')"', $lang['waiting_list'])
							)
						)
					)
				)
			: '')
		).
		write_html('h2', '', $lang['school_static_title'].' '. $_SESSION['year'].'/'. ($_SESSION['year']+1)).
		write_html('table', 'class="tablesorter" id="statics_table"', 
			$thead.
			write_html('tbody', '', implode($trs, '')).
			write_html('tfoot' ,'', 
				write_html('tr', '',
					write_html('th', 'width="20"', '&nbsp;').
					write_html('th', 'rawspan="2"', $lang['total']).
					write_html('th', 'width="50"', $count_all_class).
					write_html('th', 'class="sex" width="50"', $count_all_male).
					write_html('th', 'class="sex" width="50"', $count_all_female).
					write_html('th', 'class="religion" width="50"', $count_all_muslim).
					write_html('th', 'class="religion" width="50"', $count_all_chistians).
					write_html('th', 'class="nationality" width="50"', $count_all_egyptians).
					write_html('th', 'class="nationality" width="50"', $count_all_forgeins).
					write_html('th', 'class="register_stat" width="50"', $count_all_new).
					write_html('th', 'class="register_stat" width="50"', $count_all_redouble).
					write_html('th', 'width="50"', $count_all_std).
					write_html('th', 'class="suspended" width="50"', $count_all_suspended).
					write_html('th', 'class="waiting" width="50"', $count_all_waiting)
				)
			)
		);
		return $school_static;
	}
	
	public function loadRegistrationReport($level_id='', $sex='', $order='sex'){
		global $lang;
		$field_name = 'name_ar';
		$boys = array();
		$girls = array();
		$all = array();
		if($level_id==''){
			$levels = $this->getLevelList();
			$level = reset($levels);
		} else {
			$level = new Levels($level_id, $this);
		}
		$pages = array();
		$students = $level->getStudents();
		
		foreach($students as $stud){
			if($order=='age'){
				$stds_arr[$stud->birth_date] = $stud;
			} else {
				$stds_arr[$stud->getName()] = $stud;
			}
		}
		ksort($stds_arr);

		if($sex != ''){
			foreach($stds_arr as $index=>$std){
				if($std->sex != $sex){
					unset($stds_arr[$index]);
				}
			}
		}
		
		foreach($stds_arr as $index=>$student){
			$parent = $student->getParent();
			$row = new Layout($student);
			$row->template = 'modules/sms/templates/students_reg_row.tpl';
			$row->student_name = $student->getName();
			$row->father_name_ar = $parent->father_name_ar;
			$row->father_job_ar = $parent->father_job_ar;
			$row->reg_stat = $student->getRegStat() ? $lang['result_new'] : $lang['result_redouble'];
			$row->religion = $student->religion == 1 ? $lang['muslim'] : $lang['christian'];
			$row->birth_day = date('d', $student->birth_date);
			$row->birth_month = date('m', $student->birth_date);
			$row->birth_year = date('Y', $student->birth_date);
			$birth_date = date_create();
			date_timestamp_set($birth_date, $student->birth_date);
			$date = new DateTime($_SESSION['year'].'-10-1' ); 
			$interval = date_diff($date, $birth_date);
			$row->oct_day = $interval->format('%d');
			$row->oct_month = $interval->format('%m');
			$row->oct_year = $interval->format('%y'); 
			
			if($student->sex == 1){
				$row->ser = count($boys)+1;
				$boys[] = $row->_print();
			} else {
				$row->ser = count($girls)+1;
				$girls[] = $row->_print();
			}
			$all[] = $row->_print();
		}
		$page = new Layout();
		$page->template = 'modules/sms/templates/students_reg.tpl';
		$page->year = $_SESSION['year'].'/'. ($_SESSION['year'] +1);
		$page->school_name = $this->getName();
		
		if($sex != ''){
			if($sex == '1'){
					// Boys
				$page->title = $level->getName().' - '. $lang['boys'];
				$page->trs = implode('', $boys);
				$pages[] = $page->_print();
			} elseif($sex =='2'){
					//Girls
				$page->title = $level->getName().' - '. $lang['girls'];
				$page->trs = implode('', $girls);
				$pages[] = $page->_print();
			}
		} else {
			if($order =='sex'){
				$all = array_merge($boys, $girls);
			} 
			$page->title = $level->getName();
			$page->trs = implode('', $all);
			$pages[] = $page->_print();
		}
		return implode('<p style="page-break-before: always"><!-- pagebreak --></p>', $pages);
	}
	
	public function loadReservationList($level_id=''){
		global $lang, $sms;
		if($level_id != '' ){
			$level = new Levels($level_id, $this);
		}
		$status = 3;
		$prev_year = getYear($_SESSION['year']-1);
		$year_begin = $prev_year->end_date;
		$year_end = getYearSetting('end_date');		
		$sql = "SELECT *
			FROM student_data
			WHERE `status`=$status
			AND quit_date>$year_begin 
			AND quit_date<=$year_end";

		$stds = do_query_array($sql, $sms->database, $sms->ip);
		$students = array();
		foreach($stds as $std){
			if($level_id !=''){
				$s = new Students($std->id);
				$std_level = $s->getLevel();
				if($std_level->id == $level_id){
					$students[] = $s;
				}
			} else {				
				$students[] = new Students($std->id);				
			}
		} 
		$trs = array();
		foreach($students as $student){
			$class = $student->getClass();
			$class_name = ($class != false ? $class->getName():'');
			$trs[] = write_html('tr', '',
				write_html('td', 'width="20" class="unprintable"', 
					write_html('button', 'module="students" std_id="'.$student->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
				).
				write_html('td', '', $student->getName()).
				write_html('td', '',  $class_name)
			);	
		}		
		$select_level_arr = array();
		if(in_array($_SESSION['group'], array('superadmin', 'admin'))){ 
			$select_level_arr['all'] = $lang['all'];
		}
		$grades = $this->getLevelList();
		$select_level_arr = objectsToArray($grades);
		
		$out = write_html('form', 'class="ui-corner-all ui-state-highlight unprintable optional" style="padding:5px"',
			write_html('table', 'cellspacing="0" class="optional"', 
				write_html('tr', '', 
					write_html('td', 'width="120"', write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align" ', $lang['level'])).
					write_html('td', '', 				
						write_html('select', 'name="level_id" class="combobox" update="changeQuitedLevel"', 
							write_select_options($select_level_arr, $level_id, true)
						)
					)
				)
			)
		).
		($level_id != ''?
			write_html('h2', '', $level->getName() )
		:'').
		write_html('div', 'id="suspension_table"',
			write_html('table', 'class="tablesorter"', implode('', $trs))
		);
		return $out;
	}

	/****************** Accounting ************************/
	public function getCountStudents($con, $con_id, $year='', $stats=array('1', '3')){
		$item = new StudentsList($con, $con_id, $this, $year);
		$item->stats = $stats;
		return $item->getCount();
	}
	
	public function SyncStudentsAcc(){
		global $lang;
		$students = $this->getStudents();
		$total = 0;
		$main_code = $this->getAccCode();
		$cc = $this->getCC();
		foreach($students as $std){
			if(!do_query_obj("SELECT * FROM sub_codes WHERE main=$main_code AND sub=$std->id")){
				$ins = array(
					'main'=>$main_code,
					'sub'=> $std->id,
					'title'=>$lang['student'].'/ '.$std->name_ar,
					'ccid'=>$cc
				);
				if(do_insert_obj($ins, 'sub_codes')!=false){
					$total++;
				}
			}
		}
		return array('error'=>'', 'results'=>$total);
	}
	/****************** System check & repaire ************************/
	public function getSystemTab(){
		$layout = new Layout($this);
		$layout->classes_opts = write_select_options(objectsToArray(Classes::getList()));
		$layout->levels_opts = write_select_options(objectsToArray(Levels::getList()));
		$materials = objectsToArray(Materials::getList());

		$layout->religion_table = write_html('table', 'id="religion_table" border="0" cellspacing="0" style="margin:8px 20px"',
			write_html('tr', '',
				write_html('td', ' width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"',$lang['islamic_subject'])
				).
				write_html('td', '',
					write_html_select('name="ser_muslim" class="combobox"', $materials , $MS_settings['islamic_material'])
				)
			).
			write_html('tr', '',
				write_html('td', ' width="120" valign="middel"', 
					write_html('label', 'class="label ui-widget-header ui-corner-left"',$lang['christian_subject'])
				).
				write_html('td', '',
					write_html_select('name="ser_christian" class="combobox"', $materials , $MS_settings['christian_material'])
				)
			)
		);
		return '';
	}
}