<?php
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

/************ Lists ***********************/
$parents_arr = array();

$lang_ext = $_SESSION['lang'] == 'ar' ? '_ar' : '';

foreach($_GET as $key => $value){
	$pram = "$key=$value";
}

if(isset($_GET['all'])){
	$title = $lang['all_parent'];
	$params = 'all';
	$parents = do_query_array("SELECT id FROM parents", MySql_Database);
	foreach($parents as $parent){
		$parents_arr[] = new Parents($parent->id, $sms);	
	}
} else {
	$std_ids = array();
	if(isset($_GET['etab'])){
		$etab = new Etabs(safeGet('etab'));
		$title = $lang['level'] .': '. $level->getName();
		$std_ids = $etab->getStudents();
	} elseif(isset($_GET['grade'])){
		$level = new Levels(safeGet('grade'));
		$title = $lang['level'] .': '. $level->getName();
		$std_ids = $level->getStudents();
	} elseif(isset($_GET['class'])){
		$class = new Classes(safeGet('class'));
		$title = $lang['class'] .': '. $class->getName();
		$std_ids = $class->getStudents();
	} elseif(isset($_GET['group'])){
		$group = new Classes(safeGet('group'));
		$title = $lang['group'] .': '. $group->getName();
		$std_ids = $group->getStudents();
	}
	
	if($std_ids != false && count($std_ids) > 0){
		foreach($std_ids as $student){
			$parent = $student->getParent();
			$parent_id = do_query("SELECT parent_id FROM student_data WHERE id=$student->id");
			if(!in_array($parent, $parents_arr)){
				$parents_arr[] = $parent;
			}
		}
	}
}

if(count($parents_arr) > 0){
	$tbody = '';
	foreach($parents_arr as $parent){
		$father_phonebook = $parent->getTel('father', true);
		$father_mailbook = $parent->getMail('father', true);
		$father_addressbook = $parent->getAddress('father', true);
		$count_std_str = "SELECT id FROM student_data WHERE parent_id=$parent->id AND (status=1 OR status=3)";
		$sons = do_query_array($count_std_str, $sms->database);
		if(count($sons) > 0){
			$tbody .= write_html('tr','',
				write_html('td', 'class="unprintable"', '<input type="checkbox" name="id[]" value="'.$parent->id.'" />').
				write_html('td', 'class="unprintable"', 
					write_html('button', 'type="button" class="ui-state-default hoverable circle_button" module="parents" action="openParent" parentid="'. $parent->id.'"',  write_icon('person'))
				).
				write_html('td', '', $parent->father_name.'<br />'.$parent->father_name_ar).
				write_html('td', '', implode('<br />', $father_phonebook)).
				write_html('td', '', $parent->{'mother_name'.$lang_ext}).
				write_html('td', '', $parent->mother_mobil).
				write_html('td', '', $parent->{'father_address'.$lang_ext}).
				write_html('td', '', count($sons))
			);
		}
	}
	

	echo write_html('form', 'id="parent_list_form" class="printable"',
		write_html('h2', 'class="showforprint hidden ui-state-highlight" align="center" style="padding:10px; margin:20px"', $lang['parent_list']).
		write_html('div', 'class="showforprint ui-corner-top ui-widget-header" style="padding:10px; margin:20px 0px"', $title).
		write_html('div', 'class="ui-widget-content ui-corner-bottom"',
			write_html('div', 'class="toolbox"',
				(getPrvlg('parents_edit') ? 
					write_html('a', 'action="mergeParents"', write_icon('link').$lang['merge']).
					write_html('a', 'action="exportTable" rel="#parent_list_form" plugin="xml"', write_icon('disk'). $lang['export']).
					write_html('a', 'action="deleteParents"', write_icon('close').$lang['delete']) 
				: '' ).
				write_html('a', 'action="print_pre" rel=".printable" plugin="print"', write_icon('print').$lang['print'])
			).
			write_html('table', 'class="tablesorter"',
				write_html('thead', '',
					write_html('tr', '',
						write_html('th', 'class="unprintable" width="20"', '&nbsp;').
						write_html('th', 'class="unprintable" width="20"', '&nbsp;').
						write_html('th', '', $lang['father_name']).
						write_html('th', '', $lang['father_tel']).
						write_html('th', '', $lang['mother_name']).
						write_html('th', '', $lang['mother_tel']).
						write_html('th', '', $lang['address']).
						write_html('th', 'width="50"', $lang['count_sons'])
					)
				).
				write_html('tbody', '', $tbody)
			)
		)
	);
	
} else {
	echo write_error($lang['request_malformed']);
}
?>