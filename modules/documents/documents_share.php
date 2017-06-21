<?php
## Document Share
//Delete Share
if(isset($_GET['delshare'])){
	$link = $_POST['link'];
	$con = $_POST['con'];	
	$con_id = $_POST['con_id'];	
	if(do_query_edit("DELETE FROM files_share WHERE link='$link' AND con='$con' AND con_id=$con_id", LMS_Database)){
		$answer['id'] = '';
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = 'Error Cant remove share';
	}
	print json_encode($answer);	
	exit;
}

// INSERT share
if(isset($_POST['link'])){
	$error = false;
	$values =array();
	$classes =array();
	$groups = array();
	$stds = array();
	$links = strpos($_POST['link'], ',') !== false ? explode(',', $_POST['link']) : array($_POST['link']);	
	if(isset($_POST['folder'])){
		foreach($_POST['folder'] as $folder){
			$l = do_query("SELECT link FROM files WHERE path=".$_POST['folder']);
			if($l == false || $l['link'] == ''){
				$newlink = uniqid();
				do_query_edit("INSERT INTO files (path, link, owner_group, owner_id) VALUES ('".$folder."', '$newlink', '".$_SESSION['group']."', '".$_SESSION['user_id']."')", LMS_Database);
				$links[] = $newlink;
			} else {
				$links[] = $l['link'];
			}
		}
	}
	if(isset($_POST['class'])){
		foreach($_POST['class'] as $class_id){
			$classes[] = $class_id;
			$class = new Classes($class_id);
			$stds = $class->getStudents();
			$values['class'][] = $class_id;
		}
	}
	if(isset($_POST['group'])){
		foreach($_POST['group'] as $group_id){
			$g = do_query("SELECT parent, parent_id FROM groups WHERE id=$group_id", DB_year);
			if($g['parent'] != 'class' || ($g['parent'] == 'class' && !in_array($g['parent_id'], $classes))){
				$groups[] = $group_id;
				$group = new Groups($group_id);
				$stds = array_merge($stds, $group->getStudents());
				$values['group'][] = $group_id;
			}
		}
	}
	if(isset($_POST['std_id'])){
		foreach($_POST['std_id'] as $std_id){
			if(!in_array($std_id, $stds)){
				$values['student'][] = $std_id;
			}
		}
	}
	
	foreach($links as $link){
		foreach($values as $key => $vals){
			$con = $key;
			foreach($vals as $con_id){
				$sql = "INSERT INTO files_share (link, con, con_id, date) 
					SELECT * FROM (SELECT '$link', '$con', '$con_id',".time().") AS tmp 
					WHERE NOT EXISTS
						(SELECT link FROM files_share WHERE link='$link' AND con='$con' AND con_id=$con_id)";
				if(!do_query_edit($sql, LMS_Database)){
					$error = true;
				}
			}
		}
	}
	if($error == false){
		$answer['id'] = '';
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['error_updating'];
	}
	print json_encode($answer);
	exit;
}

	// Get con id html for sharing 
if(isset($_GET['getconid'])){
	$con = $_GET['getconid'];
	switch($con){
		case 'class' :
			if($_SESSION['group']== 'student'){
				$out = '<input type="hidden" name="id" id="share_con_id" value="'.getClassIdFromStdId($_SESSION['user_id']).'" />';
			} else {
				$out = '<select name="id" id="share_con_id">';
					$classes = get_class_list();
					foreach ($classes as $id => $name){
						$out .= write_html('option', 'value="'.$id.'"', $name);
					}
				$out .= '</select>';
			}
		break;
		case 'student' :
			if(in_array($_SESSION['group'], array('student', 'prof', 'master'))){
				$out = '<script type="text/javascript">openSelectDialog(\'student\', \'#share_to\')</script>';
			} else {
				$out = '<input type="text" name="con_id" id="sug_share_con_id" value="" style="width:300px" />
				<input type="hidden" name="id" id="share_con_id" value="" />
				<script type="text/javascript">setStudentAutocomplete(\'#sug_share_con_id\', \'#share_con_id\')</script>';
			}
		break;
		case 'group':
			if($_SESSION['group']== 'student'){
				$out = '<script type="text/javascript">openSelectDialog(\'group\', \'#share_to\')</script>';
			} else {
				$out = '<select name="id" id="share_con_id">';
					$classes = get_group_list();
					foreach ($classes as $id => $name){
						$out .= write_html('option', 'value="'.$id.'"', $name);
					}
				$out .= '</select>';
			}
		break;		
		case 'level':
			$out = '<select name="id" id="share_con_id">';
				$classes = get_grade_list();
				foreach ($classes as $id => $name){
					$out .= write_html('option', 'value="'.$id.'"', $name);
				}
			$out .= '</select>';
		break;		
		case 'emp':
			$out = '<input type="text" name="con_id" id="sug_share_con_id" value="" style="width:300px" />
			<input type="hidden" name="id" id="share_con_id" value="" />
			<script type="text/javascript">setEmployerAutocomplete(\'#sug_share_con_id\', \'#share_con_id\')</script>';
		break;		
		case 'prof':
			if($_SESSION['group']== 'student'){
				$out = '<script type="text/javascript">openSelectDialog(\'prof\', \'#share_to\')</script>';
			} else {
				$out = '<input type="text" name="con_id" id="sug_share_con_id" value="" style="width:300px"/>
				<input type="hidden" name="id" id="share_con_id" value="" />
				<script type="text/javascript">setEmployerAutocomplete(\'#sug_share_con_id\', \'#share_con_id\')</script>';
			}
		break;		
	}
	echo $out;	
	exit;
}

$shares_tbody_html ='';
$link_table = '';
$files = array();
if(isset($_REQUEST['file']) && count($_REQUEST['file']) > 0){
	$files = array_merge($files, $_REQUEST['file']);
}
if(isset($_REQUEST['folder']) && count($_REQUEST['folder']) > 0){
	foreach($_REQUEST['folder'] as $fold){
		$l = do_query("SELECT link FROM files WHERE path='".urldecode(getUtf8Path($fold))."'", LMS_Database);
		if($l == false || $l['link'] == ''){
			$newlink = uniqid();
			do_query_edit("INSERT INTO files (path, link, owner_group, owner_id) VALUES ('".urldecode(getUtf8Path($fold))."', '$newlink', '".$_SESSION['group']."', '".$_SESSION['user_id']."')", LMS_Database);
			$folder_link = $newlink;
		} else {
			$folder_link = $l['link'];
		}
		$files[] = $folder_link;
	}
}

foreach($files as $file){
	$f = new Files($file);
	$filepath = $f->path;
	$file_name = $f->filename;
	$file_icon  = $f->getThumb();
	$link_table .= write_html('tr', '', 
		write_html('td', 'width="24"', '<img src="'.$file_icon.'" border="0" height="24" width="24" />').
		write_html('td', '', $file_name)
	);
	
	$shares = do_query_array("SELECT * FROM files_share WHERE link='$file' ORDER BY con, con_id", LMS_Database);
	if(count($shares) >0){
		foreach($shares as $share){
			$shares_tbody_html .= write_html('tr', '',
				write_html('td', 'width="16"', 
					write_html('a', 'class="hand" onclick="removeShare(\''.$file.'\', \''.$share->con.'\', \''.$share->con_id.'\')" title="'.$lang['remove'].'"', 
						write_icon('close')
					)
				).
				write_html('td', '', $lang[$share->con]).
				write_html('td', '', getAnyNameById($share->con, $share->con_id))
			);
		}
	}
}

// reload
if(isset($_GET['reload'])){
	die($shares_tbody_html);
}

// body		
echo write_html('form', 'id="share_form"',
	write_html('fieldset', 'class="ui-corner-all ui-widget-content"',
		write_html('legend', '', $lang['files_to_share']).
		'<input type="hidden" name="link" id="shared_links" value="'.implode(',', $files).'" />'.
		write_html('table', 'class="result"',$link_table)
	)
);

echo write_html('fieldset', 'class="ui-corner-all ui-widget-default"',
	write_html('legend', 'class="ui-corner-all ui-widget-header" style="padding:3px 5px"', $lang['share']).
	write_html('table', 'class="result" id="shares_table"',
		$shares_tbody_html
	)
);

exit;
?>