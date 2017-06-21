<?php
## student list
## params 
## fields => Selected field to display
## orders: order_1, order_2, order_3
## extra : count(absent), brother table
## main params 
## params
## record by page

/***************** functions *****************/
	// Etab
function getEtabNameById($id){
	if($id != false && $id != ''){
		$r = do_query( "SELECT name_".$_SESSION['dirc']." FROM etablissement WHERE id=$id", MySql_Database);		
		return $r['name_'.$_SESSION['dirc']];
	} else { return false;}
}
	// level
function getLevelNameById($id){
	if($id != false && $id != ''){
		$r = do_query( "SELECT name_".$_SESSION['dirc']." FROM levels WHERE id=$id", DB_student);		
		return $r['name_'.$_SESSION['dirc']];
	} else { return false;}
}
	// class
function getClassNameById($id){
	if($id != false && $id != ''){
		$field = "name_".$_SESSION['dirc'];
		$r = do_query("SELECT $field FROM classes WHERE id=$id", Db_prefix.$_SESSION['year']);		
		return $r[$field];
	} else { return false;}
}
	// group
function getGroupNameById($id){
	if($id != false && $id != ''){
		$r = do_query("SELECT name FROM groups WHERE id=$id", Db_prefix.$_SESSION['year']);		
		return $r["name"];
	} else { return false;}
}

function getTableNameFromField($field){
	if(strpos($field, '.') !== false){
		$fs = explode('.', $field);
		return $fs[0].'.'.$fs[1];
	} else {
		echo 'ERROR : '.$field;
		return false;
	}
}

function getFieldShortName($field){
	if(strpos($field, ' AS ') !== false){
		$fs = explode(' AS ', $field);
		return $fs[1];
	} elseif(strpos($field, '.') !== false){
		$fs = explode('.', $field);
		return $fs[2];
	} else {
		return $field;
	}	
}

function addElmntToArr($elmnt, $array){
	if(!in_array($elmnt, $array)) { 
		$array[] = $elmnt;
	}
	return $array;
}

function buildExtraList($extra, $std_id){
	$lang = $GLOBALS['lang'];
	$out = '';
	if($extra == 'age'){
		$std = do_query("SELECT birth_date FROM student_data WHERE id=$std_id", DB_student);
		$birth_date = date_create();
		date_timestamp_set($birth_date, $std['birth_date']);
		$date = new DateTime($_SESSION['year'].'-10-1' ); 
		$interval = date_diff($date, $birth_date);
		$years = $interval->format('%y');
		$months = $interval->format('%m');
		$days = $interval->format('%d'); 
		$out = $years.' '.$lang['years'].' | '.$months.' '.$lang['months'].' | '.' '.$days.' '.$lang['days'];
	} elseif($extra == 'brothers'){
		$std = do_query("SELECT id, parent_id FROM student_data WHERE id=$std_id", DB_student);
		$name_fld = $_SESSION['lang']=='ar' ? "name_ar" : 'name';
		$bros = getStdsFromParent($std['parent_id']);  
		$out = '<table class="tablesorter" style="margin:0">';
		foreach($bros as $bro_id => $bro_name){
			$bro_class = getClassIdFromStdId($bro_id);
			if($bro_id != $std_id){
				$out .= write_html('tr', '',
					write_html('td', 'width="20"', $bro_id).
					write_html('td', '', $bro_name).
					write_html('td', 'width="25%"', getClassNameById($bro_class))
				);
			}
		}
		$out .='</table>';
	} elseif( $extra == 'absents'){
		$abs = do_query("SELECT COUNT(*) FROM absents WHERE con_id=$std_id", DB_year);
		$out = $abs['COUNT(*)'];
	}elseif( $extra == 'login'){
		$login = do_query_obj("SELECT name, password FROM users WHERE `group`='student' AND user_id=$std_id", DB_student);
		if($login != false){
			$out = write_html('table', 'cellspacing="0" width="100%"',
				write_html('tr', '',
					write_html('td', 'width="20"', $login->name).
					write_html('td', '', $login->password)
				)
			);
		}
	}
	return $out;
}
/***************** save last Requet *****************/
 
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
$selectableFields['status'] = array();
$selectableFields['status']['0'] = $lang['radiet'];
$selectableFields['status'][1] = $lang['inscript'];
$selectableFields['status']['2'] = $lang['waiting_list'];
$selectableFields['status']['3'] = $lang['suspended'];
$selectableFields['status']['5'] = $lang['gruaduated'];
$selectableFields['new_stat'] = array();
$selectableFields['new_stat']['0'] = $lang['result_redouble'];
$selectableFields['new_stat']['1'] = $lang['result_new'];
$selectableFields['new_stat']['2'] = $lang['result_transfer'];

if(isset($_POST['req'])){
	$name = $_POST['req'];
	$proc = do_query("SELECT * FROM  list_procudures WHERE name='$name'", DB_student);
	$sql = $proc['sql'];
	$main_order = $proc['order'];
	if($proc['extras'] != ''){
		$extras = explode(',', $proc['extras']);
	}
	$grouped = $proc['grouped'] == 1 && $main_order != '' ? true : false;
	$selfields = explode(',', $proc['select']);
	$show_ser = false;
} else {
	/**************** Extras ************************/
	$extras = isset($_POST['extras']) ? $_POST['extras'] : array();

	/**************** Fields ************************/
	$selfields = isset($_POST['fields']) ? $_POST['fields'] : array();

	/**************** Serial ***********************/
	$show_ser = isset($_POST['serial']) && $_POST['serial'] == 1 ? true : false;
	
	/**************** Order by ************************/
	$ordered_by = array();
	if($_POST['order_1'] != '0'){ $ordered_by[] = $_POST['order_1'];	}
	if($_POST['order_2'] != '0'){ $ordered_by[] = $_POST['order_2'];	}
	if($_POST['order_3'] != '0'){ $ordered_by[] = $_POST['order_3'];	}
	if($_POST['order_4'] != '0'){ $ordered_by[] = $_POST['order_4'];	}
	
	if(isset($ordered_by[0])){
		$main_order = $ordered_by[0];
	}
	
	/**************** Filters ************************/
	$params = array();
	if($_POST['main_param'] != ''){ 
		$params[] = $_POST['main_param'];
	}
	if($_POST['params'] != ''){
		$par = explode(';', $_POST['params']);
		foreach($par as $p){
			if($p != '') { $params[] = $p;}
		}
	}
	/**************** Grouped ************************/
	$grouped = isset($_POST['grouped']) && $_POST['grouped'] == 1 && isset($main_order) ? true : false;
	
	/**************** collecting tables ************************/
	$tables = array(DB_student.'.student_data');
	$displayed_fields = $_POST['fields'];
	$all_fields = array_merge($displayed_fields, $params);
		// add order tables
	foreach($ordered_by as $order){
		if($order == DB_student.'.levels.id' && !in_array(DB_student.'.levels.name AS level_name', $all_fields)){
			$all_fields[] = DB_student.'.levels.id';
			$displayed_fields[] = DB_student.'.levels.name_'.$_SESSION['dirc'].' AS level_name';
		}
		if($order == DB_year.'.classes.id' && !in_array(DB_year.'.classes.name AS class_name', $all_fields)){
			$all_fields[] = DB_year.'.classes.id';
			$displayed_fields[] = DB_year.'.classes.name_'.$_SESSION['dirc'].' AS class_name';
		}
		if($order == DB_year.'.groups.id' && !in_array(DB_year.'.groups.name AS group_name', $all_fields)){
			$all_fields[] = DB_year.'.groups.id';
			$displayed_fields[] = DB_year.'.groups.name AS group_name';
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
	if( in_array(DB_year.'.classes', $tables)) { $tables = addElmntToArr( DB_year.'.classes_std', $tables);}
	if( in_array(DB_year.'.groups', $tables)) { $tables =addElmntToArr( DB_year.'.groups_std', $tables);}
	if( in_array(DB_student.'.levels', $tables)) { 
		$tables =addElmntToArr( DB_year.'.classes_std', $tables);
		$tables =addElmntToArr( DB_year.'.classes', $tables);
	}
	if( in_array(DB_student.'.etablissement', $tables)) { 
		$tables =addElmntToArr( DB_year.'.classes_std', $tables);
		$tables =addElmntToArr( DB_year.'.classes', $tables);
		$tables =addElmntToArr( DB_student.'.levels', $tables);
	}
	/**************** collecting links jor join ************************/
	$link = array();
	foreach($tables as $table){
		if($table == DB_student.'.parents'){ // parents
			if(!in_array(DB_student.'.parents.id='.DB_student.'.student_data.parent_id', $link)) { $link[] = DB_student.'.parents.id='.DB_student.'.student_data.parent_id';}
		} elseif($table == DB_year.'.groups'){// groups
			if(!in_array(DB_year.'.groups_std.std_id='.DB_student.'.student_data.id', $link)) { $link[] = DB_year.'.groups_std.std_id='.DB_student.'.student_data.id';}
			if(!in_array(DB_year.'.groups.id='.DB_year.'.groups_std.class_id', $link)) { $link[] = DB_year.'.groups.id='.DB_year.'.groups_std.group_id';}
		} elseif($table == DB_year.'.classes_std'){ // Classes
			if(!in_array(DB_year.'.classes_std.std_id='.DB_student.'.student_data.id', $link)) { $link[] = DB_year.'.classes_std.std_id='.DB_student.'.student_data.id';}
		} elseif($table == DB_year.'.classes'){ // Classes
			if(!in_array(DB_year.'.classes_std.std_id='.DB_student.'.student_data.id', $link)) { $link[] = DB_year.'.classes_std.std_id='.DB_student.'.student_data.id';}
			if(!in_array(DB_year.'.classes.id='.DB_year.'.classes_std.class_id', $link)) { $link[] = DB_year.'.classes.id='.DB_year.'.classes_std.class_id';}
		} elseif($table == DB_student.'.levels'){ // levels
			if(!in_array(DB_year.'.classes_std.std_id='.DB_student.'.student_data.id', $link)) { $link[] = DB_year.'.classes_std.std_id='.DB_student.'.student_data.id';}
			if(!in_array(DB_year.'.classes.id='.DB_year.'.classes_std.class_id', $link)) { $link[] = DB_year.'.classes.id='.DB_year.'.classes_std.class_id';}
			if(!in_array(DB_student.'.levels.id='.DB_year.'.classes.level_id', $link)) { $link[] = DB_student.'.levels.id='.DB_year.'.classes.level_id';}		
		} elseif($table == DB_student.'.etablissement'){ // Etablissement
			if(!in_array(DB_year.'.classes_std.std_id='.DB_student.'.student_data.id', $link)) { $link[] = DB_year.'.classes_std.std_id='.DB_student.'.student_data.id';}
			if(!in_array(DB_year.'.classes.id='.DB_year.'.classes_std.class_id', $link)) { $link[] = DB_year.'.classes.id='.DB_year.'.classes_std.class_id';}
			if(!in_array(DB_student.'.levels.id='.DB_year.'.classes.level_id', $link)) { $link[] = DB_student.'.levels.id='.DB_year.'.classes.level_id';}
			if(!in_array(DB_student.'.etablissement.id='.DB_student.'.levels.etab_id', $link)) { $link[] = DB_student.'.etablissement.id='.DB_student.'.levels.etab_id';}		
		}
	}
	

	/**************** THE QUERY ************************/
	$sql = "SELECT ".DB_student.'.student_data.id,'. implode(',', $displayed_fields).
	" FROM ". implode(',', $tables). 
	(count($link) > 0 || count($params) > 0 ? " WHERE " : '').
	(count($link) > 0 ? implode(' AND ', $link) : ''). 
	(count($link) > 0 && count($params) > 0 ? " AND " : '').
	(count($params) > 0 ? implode(' AND ', $params) : '').
	(count($ordered_by) > 0 ? " ORDER BY ". implode(',', $ordered_by) : '');
	
}

//echo $sql;
$stds = do_query_resource($sql, DB_student);
$total_stds = mysql_num_rows($stds);
$serial = 0;
if( $stds !=false && mysql_num_rows ($stds) > 0){
	// header
	$where_title = '';
	$main_title = '';
	if(isset($params) && count($params) > 0) {
		if($_POST['main_param'] != ''){
			$ps = explode('=', $_POST['main_param']);
			$main_fld = $ps[0];
			$main_fld_id = $ps[1];
			if(strpos($_POST['main_param'] ,'.etablissement.') !== false){
				$main_title = $lang['etab'].': '.getEtabNameById($main_fld_id);
			} elseif(strpos($_POST['main_param'] ,'.levels.') !== false){
				$main_title = $main_title = $lang['level'].': '.getLevelNameById($main_fld_id);
			} elseif(strpos($_POST['main_param'] ,'.classes.') !== false){
				$lang['class'].': '.getClassNameById($main_fld_id);
			} elseif(strpos($_POST['main_param'] ,'.groups.') !== false){
				$main_title = $lang['group'].': '.getGroupNameById($main_fld_id);
			}
		} else {
			$main_title = $lang['school_report'];
		}
		if(count($params) > 1) {
			$where_title = $lang['matching'].': ';
		}
		for($i =1; $i< count($params); $i++){
			$short_field = getFieldShortName($params[$i]);
			if(strpos($short_field, 'IS NULL') !== false){
				$fld = str_replace(' IS NULL', '', $short_field);
				$fld_prm = '';
				$fld_val = $lang['empty'];
			} elseif(strpos($short_field, 'IS NOT NULL') !== false) {
				$fld = str_replace(' IS NOT NULL', '', $short_field);
				$fld_val = $lang['not_empty'];
			} elseif(strpos($short_field, '!=') !== false){
				$fss = explode('!=', $short_field);
				$fld = $fss[0];
				$fld_val = $fss[1];
				$fld_prm = $lang['not_equal'];
			} elseif(strpos($short_field, '<=') !== false){
				$fss = explode('<=', $short_field);
				$fld = $fss[0];
				$fld_val = $fss[1];
				$fld_prm = $lang['less_equal'];
			} elseif(strpos($short_field, '>=') !== false){
				$fss = explode('>=', $short_field);
				$fld = $fss[0];
				$fld_val = $fss[1];
				$fld_prm = $lang['grater_equal'];
			} elseif(strpos($short_field, '>') !== false){
				$fss = explode('>', $short_field);
				$fld = $fss[0];
				$fld_val = $fss[1];
				$fld_prm = $lang['greater'];
			} elseif(strpos($short_field, '<') !== false){
				$fss = explode('<', $short_field);
				$fld = $fss[0];
				$fld_val = $fss[1];
				$fld_prm = $lang['less'];
			} elseif(strpos($short_field, '=') !== false){
				$fss = explode('=', $short_field);
				$fld = $fss[0];
				$fld_val = $fss[1];
				$fld_prm = $lang['equal'];
			}
			
			if(isset($selectableFields[$fld])){
				$where_title_val = $selectableFields[$fld][str_replace("'", '',$fld_val)];
			} elseif(strpos($short_field, '_date') !== false){
				$where_title_val = unixToDate($fld_val);
			} else {
				$where_title_val = $short_field;
			}
			
			$where_title .= $lang[$fld]." ".$fld_prm." ".$where_title_val;
			if($i<count($params)-1) {$where_title .= ' '.$lang['and'].' ';}
		}
	}	
	
	// storing the requet to the session
	$_SESSION['last_list_requet'] = $sql;
	$_SESSION['last_list_main_order'] = isset($main_order) ? $main_order : '';
	$_SESSION['last_list_extra'] = isset($_POST['extras']) ? implode(',', $_POST['extras']) : '';
	$_SESSION['last_list_grouped'] = isset($_POST['grouped']) ? 1 : 0;
	$_SESSION['last_list_selected'] = implode(',',$selfields);
	
	// TRS
	$trs = array();
	while($std = mysql_fetch_assoc($stds)){
		$serial++;
		$tds = array();
		foreach($selfields as $col){
			$short_field = getFieldShortName($col);
			if($std[$short_field] != ''){
				if(isset($selectableFields[$short_field])){
					$val = $selectableFields[$short_field][$std[$short_field]];
				} elseif(strpos($short_field, '_date') !== false){
					$val = unixToDate($std[$short_field]);
				} else {
					$val = $std[$short_field];
				}
			} else { $val = '';}
			$tds[] = write_html('td', '', $val);
		}
		
		if(isset($extras)){
			foreach($extras as $extra){
				$tds[] = write_html('td', '', buildExtraList($extra, $std['id']));
			}
		}
		$this_tr = write_html('tr', '',
			($show_ser ? write_html('td', 'width="16"', $serial) : '').
			write_html('td', 'width="16" class="unprintable"', '<input type="checkbox" name="std[]" value="'.$std['id'].'"').
			write_html('td', 'width="20" class="unprintable"', 
				write_html('button', 'class="ui-state-default hoverable circle_button" action="openStudent" std_id="'. $std['id'].'"',  write_icon('person'))
			).
			implode('', $tds)
		);	
		if($grouped == true){
			$tab = explode('.', getTableNameFromField($main_order));
			if($tab[1] == 'levels') { $key = $std['level_name'];}
			elseif($tab[1] == 'classes') { $key = $std['class_name'];}
			elseif($tab[1] == 'groups') { $key = $std['group_name'];}
			elseif($tab[1] == 'classes_std') { $key = $std['new_stat'];}
			else {
				if(isset($selectableFields[getFieldShortName($main_order)])){
					$key = $selectableFields[getFieldShortName($main_order)][$std[getFieldShortName($main_order)]];
				} elseif(strpos(getFieldShortName($main_order), '_date') !== false){
					$key = unixToDate($std[getFieldShortName($main_order)]);
				} else {
					$key = $std[$short_field];
				}
			} 
			$trs[$key][] = $this_tr;  
		} else {
			$trs[] = $this_tr;
		}

	}
	
	$content = '';
	//TH
	$ths = array();
	foreach($selfields as $col){
		$title = getFieldShortName($col);
		$ths[] = write_html('th', '', $lang[$title]);
	}
	if(isset($extras)){
		foreach($extras as $col){
			$ths[] = write_html('th', '', $lang[$col]);
		}
	}
	
	if($grouped == true){
		$tab = explode('.', getTableNameFromField($main_order));
		if(in_array($tab[1] , array('levels', 'classes', 'groups'))){
			$trs = sortArrayByArray($trs, getItemOrder($tab[1]));
		} 
		
		foreach($trs as $key => $value){
			$content_fieldset[] = write_html('fieldset', '',
				write_html('legend', '', $key).
				write_html('table', 'class="tablesorter"',
					write_html('thead', '',  
						write_html('tr', '',
							($show_ser ? write_html('th', 'width="16"', $lang['ser']) : '').
							write_html('th', 'width="16" class="unprintable"', '&nbsp;').
							write_html('th', 'width="20" class="unprintable"', '&nbsp;').
							implode('', $ths)
						)
					).
					write_html('tbody', '', implode('', $value))
				)	
			);
		}
		$content = implode(write_html('div', 'class="print_footer"', '<img src="../'.getImagePath('../attachs/img/footer.jpg', '', '').'" border="0" />').write_html('div', 'class="print_header"', '<img src="../'.getImagePath('../attachs/img/header.jpg', '', '').'" border="0" />'), $content_fieldset);
	} else {
		$content .= write_html('table', 'class="tablesorter"',
			write_html('thead', '',  
				write_html('tr', '',
					($show_ser ? write_html('th', 'width="16"', $lang['ser']) : '').
					write_html('th', 'width="16" class="unprintable"', '&nbsp;').
					write_html('th', 'width="20" class="unprintable"', '&nbsp;').
					implode('', $ths)
				)
			).
			write_html('tbody', '', implode('', $trs))
		);
	}
	// Print out
	echo write_html('div', 'id="home_list_content" class="ui-widget-content"',
		write_html('div', 'class="toolbox"',
			write_html('a', 'action="print_pre" rel="#home_list_content" plugin="print"', write_icon('print').$lang['print']).
			write_html('a', 'action="exportTable" rel="#home_list_content" plugin="xml"', write_icon('disk'). $lang['export']).
			write_html('a', 'action="saveLastRequet"', write_icon('disk').$lang['save_requet'])
		).
		write_html('div', 'class="ui-corner-all ui-state-highlight" style="padding:7px"',
			(isset($proc['name']) ? 
				write_html('h3', '', $proc['name'])
			:
				write_html('h3', '', $main_title)
			).
			write_html('h4', '', $lang['count_students'].' '.$total_stds).
			$where_title
		).
		$content 
	);
}else {
	echo '<div class="ui-corner-all ui-state-error" style="padding:50px">'.$lang['result_not_found'].'</div>';
}
?>