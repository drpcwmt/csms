<?php
/** Student List
*
*/
class StudentsList extends SearchEngine{
	public $con,
	$con_id,
	$cols = array(),
	$openable = false,
	$stats = array(1,3),
	$main_db,
	$year_db,
	$hidden_array= array( 'old_sch_grade', 'notes', 'guardians', 'country', 'country_ar', 'father_resp', 'mother_resp', 'father_emp', 'mother_emp', 'spension_till_date'),
	$hidden_filter_array= array('id', 'status', 'parent_id', 'guardians', 'notes', 'suspension_till_date', 'father_mobil', 'father_tel','father_mail','father_address','father_address_ar', 'father_religion', 'father_lang', 'mother_mobil', 'mother_tel','mother_mail','mother_address','mother_address_ar', 'mother_religion', 'mother_lang');
	
	public function __construct($con, $con_id, $sms='', $year=''){
		global $prvlg;
		if( $sms== '' ){
			global $sms;
		} 
		if($year == ''){
			$year = $_SESSION['year'];
		}
		$this->sms = $sms;
		$this->con = $con;
		$this->con_id = $con_id;
		$this->openable = $prvlg->_chk('std_read');
		$this->main_db = $sms->database;
		$this->year_db = Db_prefix.$year;
		if($sms->getSettings('ig_mode') != '1'){
			$this->hidden_filter_array[] = 'cand_no';
		}
			
	}
	
	public function getStudents(){
		if(!isset($this->students)){
			$object = $this->sms->getAnyObjById($this->con, $this->con_id);
			$this->students = $object->getStudents($this->stats);
		}
		return $this->students;
	}
	
	
	public function getCount(){
		return count($this->getStudents($this->stats));
	}
	
	public function createTable($students=array(), $cols_array = false){
		global $lang;
		if(count($students) == 0){
			$students = $this->getStudents();
		}
		if(isset($this->list_template) && $this->list_template!=false){
			$report = new Layout();
			$report->template  = 'attachs/templates/'.$this->list_template.'.tpl';
			$row_template = 'attachs/templates/'.$this->list_template.'_row.tpl';
			$report->rows = '';
			$ser = 1;
			foreach($students as $student){
				$row = new Layout($student);
				$row->template = $row_template;
				$row->ser = $ser;
				$row->name = $student->name_ltr;
				$row->religion = isset($student->religion) ? ($student->religion == 1 ? $lang['muslim'] : $lang['christian']) : '';
				$row->sex =isset($student->sex) ? ($student->sex == 1 ? $lang['male'] : $lang['female']) : '';
				
				if(get_class($student) == "Students"){
					$row->name.= $student->getStatusSpan();
					$row->name_ar .= $student->getStatusSpan();	
					$row->student_name = $student->getName().$student->getStatusSpan();
					$row->bus_code = $student->getBus();
				}
				$report->rows .= $row->_print();
				$ser++;
			}
			
			return $report->_print();
			
		} else {
			if($cols_array == false){
				$cols_array = getItemOrder('students_list');
			}
			$thead_cols = array();
			foreach($cols_array as $col){
				if($col == 'serial'){
					$thead_cols[] = write_html('th', 'class="{sorter:false}" width="20"', $lang['ser']);
				} elseif($col == 'signature'){
					$thead_cols[] = write_html('th', 'class="{sorter:false}" width="120"', $lang['signature']);
				} elseif($col == 'age'){
					$thead_cols[] = write_html('th', '', $lang['age_in_first_oct']);
				} else {
					$thead_cols[] = write_html('th', ($col == 'id' ? 'width="30"' : '').(strpos($col, '_ar') !== false || strpos($col, 'rtl') !== false ? ' dir="rtl"' : ''), $lang[$col]);
				}
			}
			$thead = write_html('thead', '', 
				write_html('tr', '',
					($this->openable ? write_html('th', 'class="unprintable {sorter:false}" width="20"', '&nbsp;') : '').
					implode('', $thead_cols)
				)
			);
			$i = 1;
			$trs= array();
			foreach($students as $std){
				$std_cols = array();
				$student = new Students($std->id, $this->sms);
				$parent = $student->getParent();
				foreach($cols_array as $col){
					if($col == 'serial'){
						$std_cols[] = write_html('td', 'align="center"', $i);
					} elseif($col == 'signature'){
						$std_cols[] = write_html('td', 'align="center"', '');
					} elseif($col == 'age'){
						$std_cols[] = write_html('td', 'align="center"', $student->getAge());
					} elseif($col == 'brothers'){
						$std_cols[] = write_html('td', '', $student->getBrothers());
					} elseif($col == 'tel'){
						$std_cols[] = write_html('td', '', $student->getTel());
					} elseif($col == 'mail'){
						$std_cols[] = write_html('td', '', $student->getMail());
					} elseif($col == 'address'){
						$std_cols[] = write_html('td', '', $student->getAddress(false,'en'));
					} elseif($col == 'address_ar'){
						$std_cols[] = write_html('td', '', $student->getAddress(false,'ar'));
						
					} elseif($col == 'father_address'){
						$std_cols[] = write_html('td', '', $parent->getAddress('father', 'en'));
					} elseif($col == 'father_address_ar'){
						$std_cols[] = write_html('td', '', $parent->getAddress('father', 'ar'));
					} elseif($col == 'father_tel'){
						$std_cols[] = write_html('td', '', $parent->getTel('father'));
					} elseif($col == 'father_mail'){
						$std_cols[] = write_html('td', '', $parent->getMail('father'));
						
					} elseif($col == 'mother_address'){
						$std_cols[] = write_html('td', '', $parent->getAddress('mother', 'en'));
					} elseif($col == 'mother_address_ar'){
						$std_cols[] = write_html('td', '', $parent->getAddress('mother','ar'));
					} elseif($col == 'mother_tel'){
						$std_cols[] = write_html('td', '', $parent->getTel('mother'));
					} elseif($col == 'mother_mail'){
						$std_cols[] = write_html('td', '', $parent->getMail('mother'));
						
					} elseif($col == 'name_ar'){
						$student->$col .= $student->getStatusSpan();
						$std_cols[] = write_html('td', 'dir="rtl"', 								
							write_html('text', 'class="holder-student_rtl-'.$std->id.'"', $student->name_ar)
						);
					} elseif($col == 'name'){
						$student->name_ltr .= $student->getStatusSpan();
						$std_cols[] = write_html('td', '', 
							write_html('text', 'class="holder-student_ltr-'.$std->id.'"', $student->name_ltr)
						);

					} elseif(strpos($col, 'lang_') !== false){
						$material = new Materials($std->$col);
						$std_cols[] = write_html('td', 'align="center"',$material->getName());

					} elseif($col == "absents"){
						$abs = do_query_obj("SELECT COUNT(*) as absents FROM absents WHERE con_id=$std->id", $this->year_db);
						$std_cols[] = write_html('td', 'align="center"',$abs!=false ? $abs->absents : '');

					}elseif( $col == 'login'){
						$login=do_query_obj("SELECT name, password FROM users WHERE `group`='student' AND user_id=$std->id",$this->main_db);	
						$login_table = 'N/A';
						if($login != false){
							$login_table = write_html('table', 'cellspacing="0" width="100%"',
								write_html('tr', '',
									write_html('td', 'width="20"', $login->name).
									write_html('td', '', $login->password)
								)
							);
						}
						$std_cols[] = write_html('td', 'align="center"', $login_table);
					} else {
						$selectableFields['sex'] = array();
						$selectableFields['sex']['1'] = $lang['male'];
						$selectableFields['sex']['2'] = $lang['female'];
						$selectableFields['religion'] = array();
						$selectableFields['religion']['1'] = $lang['muslim'];
						$selectableFields['religion']['2'] = $lang['christian'];
						$selectableFields['father_religion'] = array();
						$selectableFields['father_religion']['1'] = $lang['muslim'];
						$selectableFields['father_religion']['2'] = $lang['christian'];
						$selectableFields['mother_religion'] = array();
						$selectableFields['mother_religion']['1'] = $lang['muslim'];
						$selectableFields['mother_religion']['2'] = $lang['christian'];
						$selectableFields['father_emp'] = array();
						$selectableFields['father_emp']['1'] = $lang['yes'];
						$selectableFields['father_emp']['0'] = $lang['no'];
						$selectableFields['mother_emp'] = array();
						$selectableFields['mother_emp']['1'] = $lang['yes'];
						$selectableFields['mother_emp']['0'] = $lang['no'];
						/*$selectableFields['status'] = array();
						$selectableFields['status']['0'] = $lang['radiet'];
						$selectableFields['status']['1'] = $lang['inscript'];
						$selectableFields['status']['2'] = $lang['waiting_list'];
						$selectableFields['status']['3'] = $lang['suspended'];
						$selectableFields['status']['5'] = $lang['gruaduated'];*/
						$selectableFields['new_stat'] = array();
						$selectableFields['new_stat']['0'] = $lang['result_redouble'];
						$selectableFields['new_stat']['1'] = $lang['result_new'];
						$selectableFields['new_stat']['2'] = $lang['result_nc'];
						$selectableFields['status'] = array();
						$selectableFields['status']['0'] = '';
						$selectableFields['status']['1'] = $lang['married'];
						$selectableFields['status']['2'] = $lang['divorced'];
						$selectableFields['status']['3'] = $lang['father_deceased'];
						$selectableFields['status']['4'] = $lang['mother_deceased'];
						$selectableFields['status']['5'] = $lang['both_parents_deceased'];
						
						$short_field = getFieldShortName($col);
						if($std->$short_field != ''){
							if(isset($selectableFields[$short_field])){
								$val = $selectableFields[$short_field][$std->$short_field];
							} elseif(strpos($short_field, '_date') !== false){
								$val = unixToDate($std->$short_field);
							} else {
								$val = $std->$short_field;
							}
						} else { $val = '';}						
						
						$std_cols[] = write_html('td', '', $val);
					}
				}
				$trs[] = write_html('tr', '',
					($this->openable ?
						write_html('td', 'class="unprintable"', 
							write_html('button', 'module="students" std_id="'.$std->id.'" action="openStudent" class="ui-state-default hoverable circle_button"', write_icon('person'))
						)
					: '').
					implode('', $std_cols)
				);
				$i++;
			}
			$tbody = write_html('tbody', '', implode('', $trs));
		
			return write_html('table', 'class="tablesorter"', $thead. $tbody);
		}
	}
	
	public function getOrders(){
		global $lang;
		$order_by = array(
			'0'=>'',
			$this->main_db.'.student_data.id'=> $lang['id'],
			$this->main_db.'.student_data.name' => $lang['name'].'(En)',
			$this->main_db.'.student_data.name_ar' => $lang['name_ar'],
			$this->main_db.'.levels.id' => $lang['levels'],
			$this->year_db.'.classes.id' => $lang['classes'],
			$this->year_db.'.groups.id' =>	$lang['group'],
			$this->main_db.'.levels.id' => $lang['levels'],
			$this->year_db.'.classes.id' => $lang['classes'],
			$this->year_db.'.groups.id' => $lang['group'],
			$this->main_db.'.student_data.sex' => $lang['sex'],
			$this->main_db.'.student_data.religion' => $lang['religion'],
			$this->main_db.'.student_data.nationality' => $lang['nationality'],
			$this->main_db.'.student_data.nationality_ar' => $lang['rtl_nationality'],
			$this->main_db.'.student_data.birth_date' => $lang['birth_date']
			
		);
		return $order_by;
	}
	
	public function getStatusFilter(){
		global $lang;
		$status_arr = array();
		$status_arr['1'] = $lang['inscript'];
		$status_arr['0'] = $lang['radiet'];
		$status_arr['2'] = $lang['waiting_list'];
		$status_arr['3'] = $lang['suspended'];
		$status_arr['5'] = $lang['gruaduated'];
		$status_arr['all'] = $lang['all'];
		$status_opts = write_html('div', 'class="ui-corner-all ui-widget-content filter_row" style="margin-bottom:5px"',
				write_html_select('class="selected_field"', array($this->main_db.'.student_data.status'=>$lang['status'])).
				write_html_select('class="pram_pre_select" update="serializeFilters"', $status_arr, '1')
		);
		return $status_opts;
	}
	
	public function getFilters(){
		global $lang;
		$student_fields = getTableFields( 'student_data', $this->main_db);
		$parent_fields = getTableFields( 'parents', $this->main_db);
		$filter_opts = '';
		$student_array = array();
		$father_array = array();
		$mother_array = array();
			// student
		//$student_array[] = write_html('option', 'value=""', '');
		//$student_array[] = write_html('option', 'value="'.$this->main_db.'.student_data.status" checked="checked"', $lang['status']);
		foreach($student_fields as $f){
			if(!in_array($f, $this->hidden_filter_array)){
				$student_array[] = write_html('option', 'value="'.$this->main_db.'.student_data.'.$f.'" t="student_data" db="'.$this->main_db.'"', $lang[$f]);
			}
		}
			// new stat
		$student_array[] = write_html('option', 'value="'.$this->year_db.'.classes_std.new_stat"', $lang['new_stat']);
		$filter_opts .= write_html('optgroup', 'label="'.$lang['student'].'" style="color:red; font-weight: bolder"', implode('', $student_array));
			// parents
		foreach($parent_fields as $f){
			if(!in_array($f, $this->hidden_filter_array)){
				if(strpos($f, 'father_') !== false){
					$father_array[] = write_html('option', 'value="'.$this->main_db.'.parents.'.$f.'" t="parents" db="'.$this->main_db.'"', $lang[str_replace('father_', '', $f)]);
				} elseif(strpos($f, 'mother_') !== false){
					$mother_array[] = write_html('option', 'value="'.$this->main_db.'.parents.'.$f.'" t="parents" db="'.$this->main_db.'"', $lang[str_replace('mother_', '', $f)]);
				}
			}
		}
		if(count($father_array) > 0){
			$filter_opts .= write_html('optgroup', 'label="'.$lang['father'].'" style="color:red; font-weight: bolder"', implode('', $father_array));
		}
		if(count($mother_array) > 0){
			$filter_opts .= write_html('optgroup', 'label="'.$lang['mother'].'" style="color:red; font-weight: bolder"', implode('', $mother_array));
		}
		return $filter_opts;
	}
	
	public function loadDataOutForm(){
		$layout = new Layout();
		$layout->year_db = $this->year_db;
		$layout->name_dirc = 'name_'.$_SESSION['dirc'];
		$layout->template = 'modules/reports/templates/engine_data_out.tpl';
		return $layout->_print();
	}
	
	public function getSearchTitle($main_param){
		global $lang;
		if($main_param != ''){
			$ps = explode('=', $main_param);
			$main_fld = $ps[0];
			$main_fld_id = $ps[1];
			if(strpos($main_param ,'.etablissement.') !== false){
				$main_title = $this->sms->getAnyNameById('etab',$main_fld_id);
			} elseif(strpos($main_param ,'.levels.') !== false){
				$main_title = $this->sms->getAnyNameById('levels',$main_fld_id);
			} elseif(strpos($main_param ,'.classes.') !== false){
				$main_title = $this->sms->getAnyNameById('classes',$main_fld_id);
			} elseif(strpos($main_param ,'.groups.') !== false){
				$main_title = $this->sms->getAnyNameById('group',$main_fld_id);
			}
		} else {
			$main_title = $lang['school_report'];
		}
		return $main_title;
	}
	
	public function getQueryFromPost($post){
		global $sms;
		/**************** Extras ************************/
		$extras = isset($_POST['extras']) ? $_POST['extras'] : array();
	
		/**************** Fields ************************/
		$selfields = isset($_POST['fields']) ? $_POST['fields'] : array();
	
		/**************** Serial ***********************/
		$show_ser = isset($_POST['serial']) && $_POST['serial'] == 1 ? true : false;
		/**************** SIgnature ***********************/
		$signature = isset($_POST['signature']) && $_POST['signature'] == 1 ? true : false;
		
		/**************** Order by ************************/
		$ordered_by = array();
		if($_POST['order_1'] != '0'){ $ordered_by[] = $_POST['order_1'];	}
		if($_POST['order_2'] != '0'){ $ordered_by[] = $_POST['order_2'];	}
		if($_POST['order_3'] != '0'){ $ordered_by[] = $_POST['order_3'];	}
		if($_POST['order_4'] != '0'){ $ordered_by[] = $_POST['order_4'];	}
		
		if(isset($ordered_by[0])){
			$main_order = $ordered_by[0];
		} else {
			$main_order ='';
		}
		
		/**************** Filters ************************/
		$params = array();
		if($_POST['main_param'] != ''){ 
			$params[] = $_POST['main_param'];
		}
		if($_POST['params'] != ''){
			$par = explode(';', $_POST['params']);
			foreach($par as $p){
				if($p != '' && strpos($p, "status='all'") === false){
					$params[] = $p;
				}
			}
		}
		if(isset($post['status']) && $post['status'] == 'all'){
			unset($post['status']);
		}

		/**************** Grouped ************************/
		$grouped = isset($_POST['grouped']) && $_POST['grouped'] == 1 && isset($main_order) ? true : false;
		
		/**************** collecting tables ************************/
		$tables = array($sms->database.'.student_data');
		$displayed_fields = $_POST['fields'];
		$all_fields = array_merge($displayed_fields, $params);
			// add order tables
		foreach($ordered_by as $order){
			if($order == $sms->database.'.levels.id' && !in_array($sms->database.'.levels.name AS level_name', $all_fields)){
				$all_fields[] = $sms->database.'.levels.id';
				$displayed_fields[] = $sms->database.'.levels.name_'.$_SESSION['dirc'].' AS level_name';
			}
			if($order == $sms->db_year.'.classes.id' && !in_array($sms->db_year.'.classes.name AS class_name', $all_fields)){
				$all_fields[] = $sms->db_year.'.classes.id';
				$displayed_fields[] = $sms->db_year.'.classes.name_'.$_SESSION['dirc'].' AS class_name';
			}
			if($order == $sms->db_year.'.groups.id' && !in_array($sms->db_year.'.groups.name AS group_name', $all_fields)){
				$all_fields[] = $sms->db_year.'.groups.id';
				$displayed_fields[] = $sms->db_year.'.groups.name AS group_name';
			}
			$tables =addElmntToArr( getTableNameFromField($order), $tables);
		}
			// add fields table
		foreach($all_fields as $field){
			$tables = addElmntToArr( getTableNameFromField($field), $tables);
		}
			// add main param table
		if($_POST['main_param'] != '' && getTableNameFromField($_POST['main_param'])!= false ) { 
			$tables =addElmntToArr(  getTableNameFromField($_POST['main_param']), $tables);
		}
			// add relation tables classes_std and group std
		if( in_array($sms->db_year.'.classes', $tables)) { $tables = addElmntToArr( $sms->db_year.'.classes_std', $tables);}
		if( in_array($sms->db_year.'.groups', $tables)) { $tables =addElmntToArr( $sms->db_year.'.groups_std', $tables);}
		if( in_array($sms->database.'.levels', $tables)) { 
			$tables =addElmntToArr( $sms->db_year.'.classes_std', $tables);
			$tables =addElmntToArr( $sms->db_year.'.classes', $tables);
		}
		if( in_array($sms->database.'.etablissement', $tables)) { 
			$tables =addElmntToArr( $sms->db_year.'.classes_std', $tables);
			$tables =addElmntToArr( $sms->db_year.'.classes', $tables);
			$tables =addElmntToArr( $sms->database.'.levels', $tables);
		}
		/**************** collecting links jor join ************************/
		$link = array();
		foreach($tables as $table){
			if($table == $sms->database.'.parents'){ // parents
				if(!in_array($sms->database.'.parents.id='.$sms->database.'.student_data.parent_id', $link)) { $link[] = $sms->database.'.parents.id='.$sms->database.'.student_data.parent_id';}
			} elseif($table == $sms->db_year.'.groups'){// groups
				if(!in_array($sms->db_year.'.groups_std.std_id='.$sms->database.'.student_data.id', $link)) { $link[] = $sms->db_year.'.groups_std.std_id='.$sms->database.'.student_data.id';}
				if(!in_array($sms->db_year.'.groups.id='.$sms->db_year.'.groups_std.class_id', $link)) { $link[] = $sms->db_year.'.groups.id='.$sms->db_year.'.groups_std.group_id';}
			} elseif($table == $sms->db_year.'.classes_std'){ // Classes
				if(!in_array($sms->db_year.'.classes_std.std_id='.$sms->database.'.student_data.id', $link)) { $link[] = $sms->db_year.'.classes_std.std_id='.$sms->database.'.student_data.id';}
			} elseif($table == $sms->db_year.'.classes'){ // Classes
				if(!in_array($sms->db_year.'.classes_std.std_id='.$sms->database.'.student_data.id', $link)) { $link[] = $sms->db_year.'.classes_std.std_id='.$sms->database.'.student_data.id';}
				if(!in_array($sms->db_year.'.classes.id='.$sms->db_year.'.classes_std.class_id', $link)) { $link[] = $sms->db_year.'.classes.id='.$sms->db_year.'.classes_std.class_id';}
			} elseif($table == $sms->database.'.levels'){ // levels
				if(!in_array($sms->db_year.'.classes_std.std_id='.$sms->database.'.student_data.id', $link)) { $link[] = $sms->db_year.'.classes_std.std_id='.$sms->database.'.student_data.id';}
				if(!in_array($sms->db_year.'.classes.id='.$sms->db_year.'.classes_std.class_id', $link)) { $link[] = $sms->db_year.'.classes.id='.$sms->db_year.'.classes_std.class_id';}
				if(!in_array($sms->database.'.levels.id='.$sms->db_year.'.classes.level_id', $link)) { $link[] = $sms->database.'.levels.id='.$sms->db_year.'.classes.level_id';}		
			} elseif($table == $sms->database.'.etablissement'){ // Etablissement
				if(!in_array($sms->db_year.'.classes_std.std_id='.$sms->database.'.student_data.id', $link)) { $link[] = $sms->db_year.'.classes_std.std_id='.$sms->database.'.student_data.id';}
				if(!in_array($sms->db_year.'.classes.id='.$sms->db_year.'.classes_std.class_id', $link)) { $link[] = $sms->db_year.'.classes.id='.$sms->db_year.'.classes_std.class_id';}
				if(!in_array($sms->database.'.levels.id='.$sms->db_year.'.classes.level_id', $link)) { $link[] = $sms->database.'.levels.id='.$sms->db_year.'.classes.level_id';}
				if(!in_array($sms->database.'.etablissement.id='.$sms->database.'.levels.etab_id', $link)) { $link[] = $sms->database.'.etablissement.id='.$sms->database.'.levels.etab_id';}		
			}
		}
			
		/**************** THE QUERY ************************/
		$sql = "SELECT ".$sms->database.'.student_data.id,'. implode(',', $displayed_fields).
		" FROM ". implode(',', $tables). 
		(count($link) > 0 || count($params) > 0 ? " WHERE " : '').
		(count($link) > 0 ? implode(' AND ', $link) : ''). 
		(count($link) > 0 && count($params) > 0 ? " AND " : '').
		(count($params) > 0 ? implode(' AND ', $params) : '').
		" ORDER BY ".$sms->database.".student_data.cand_no ".(count($ordered_by) > 0 ?  ','.implode(',', $ordered_by) : '');
		//echo $sql;
		return write_html('div', 'class="ui-corner-all ui-state-highlight" style="padding:5px"',
			write_html('h2', '', $this->getSearchTitle($_POST['main_param']))
		).
		$this->loadQueryResult($sql, $selfields, $main_order, $grouped, $show_ser, $extras, isset($post['layout']) ? $post['layout'] : '', $signature);
	}

		
}