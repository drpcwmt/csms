<?php
/* Marks addons
*
*/

if(isset($_GET['newaddonform'])){
	if(isset($_GET['addid']) && $_GET['addid'] != ''){
		$add_id = $_GET['addid'];
		$seek_add= true;
		$add = do_query("SELECT * FROM marks_addon WHERE id=$add_id", DB_year);
	} else {
		$seek_add = false;
		$terms_q = getTerms($con, $con_id);
		$terms_add_arr = array('false' => $lang['once_by_year']);
		$terms_add_arr['oet'] = $lang['once_each_term'];
		while($t = mysql_fetch_assoc($terms_q)){
			$terms_add_arr[$t['id']] = $t['title'];
		}
	}
	
	$new_addon = write_html('fieldset', 'class="ui-corner-all ui-state-highlight" style="padding:5px;"',
		write_html('form', 'id="new_addon_form"',
			'<input type="hidden" name="id" value="'.($seek_add ? $add_id : '').'" />'.
			write_html('table','width="100%" bordre="0" cellspacing="0"',
				write_html('tr', '',
					write_html('td', 'valign="middel" class="reverse_align" width="100"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
					).
					write_html('td', 'colspan="3"', '<input type="text" name="name" class="required input_double" value="'.($seek_add ? $add['name'] : '').'" />')
				).
				write_html('tr', '',
					write_html('td', 'valign="middel" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['max'])
					).
					write_html('td', '', '<input type="text" name="max" class="required" style="width:75px"  value="'.($seek_add ? $add['max'] : '').'"/>').
					write_html('td', 'valign="middel" class="reverse_align" width="100"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['min'])
					).
					write_html('td', '', '<input type="text" name="min" class="required" style="width:75px"  value="'.($seek_add ? $add['min'] : '').'"/>')
				).
				write_html('tr', '',
					($calc_type == 'per' ?
						write_html('td', 'valign="middel" class="reverse_align"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['value'])
						).
						write_html('td', '', '<input type="text" name="value" class="required" style="width:75px"  value="'.($seek_add ? $add['value'] : '').'"/>')
					 : 
						($calc_type == 'moyen' ?
							write_html('td', 'valign="middel" class="reverse_align"', 
								write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['coeffcient'])
							).
							write_html('td', '', '<input type="text" name="coef" class="required" style="width:75px"  value="'.($seek_add ? $add['coef'] : '').'" />')
						: 
							write_html('td', '', '').
							write_html('td', '', '')
						)
					).
					write_html('td', '', '').
					write_html('td', '', 
						'<input type="checkbox" name="bonus" '.($seek_add && $add['bonus'] ==1 ? 'checked="checked"': '').'/> '.$lang['bonus'].
						' <input type="checkbox" name="optional" '.($seek_add && $add['optional'] ==1 ? 'checked="checked"': '').'/> '.$lang['optional']
					)
				).
				(!$seek_add ?
					write_html('tr', '',
						write_html('td', 'valign="middel" class="reverse_align"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['occurrence'])
						).
						write_html('td', 'colspan="3"', 
							write_html_select('name="cur_term" class="combobox"', $terms_add_arr , '')
						)
					)
				: '')
			)
		)
	);
	
	// ECHO
	echo $new_addon;
	exit;	
}

if(isset($_GET['submit_add'])){
	$error = false;
	if(getPrvlg('mark_edit')){
		$values = array(
			'name' => $_POST['name'],
			'tot' => $_POST['tot'],
			'min' => $_POST['min']
		);
		if(isset($_POST['bonus'])) { $values['bonus'] =1;}
		if(isset($_POST['optional'])) { $values['optional'] =1;}
		if(isset($_POST['value'])) { $values['value'] = $_POST['value'];}
		if(isset($_POST['coef'])) { $values['coef'] = $_POST['coef'];}
		
		if(isset($_POST['id']) && $_POST['id'] != '' ){ 
			$id = $_POST['id'];
			if(!UpdateRowInTable("marks_addon", $values, "id=".$_POST['id'], DB_year)){
				$error = true;
			}
		} else { // new addon
			if($_POST['cur_term'] =='false'){ // apply one by level
				$values['level_id'] = $level_id;
				if(!insertToTable("marks_addon", $values, DB_year)){
					$error = true;
				}
			} elseif($_POST['cur_term'] =='oet') { // once each term
				$terms_q = getTerms($con, $con_id);
				while($t = mysql_fetch_assoc($terms_q)){
					$values['term_id'] = $t['id'];
					if(!insertToTable("marks_addon", $values, DB_year)){
						$error = true;
					}
				}
			} else {
				$values['term_id'] = $_POST['cur_term'];
				if(!insertToTable("marks_addon", $values, DB_year)){
					$error = true;
				}
			}
		}
	} else {
		$error = $lang['no_privilege'];
	}

	$answer = array();
	if(!$error){
		$answer['id'] = '';
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $lang['error_updating'];
	}
	print json_encode($answer);
	exit;
}

if(isset($_GET['delete_add'])){
	$error = false;
	if(getPrvlg('mark_edit')){
		$add_id = $_POST['id'];
		if(!do_query_edit("DELETE FROM marks_addon WHERE id=$add_id", DB_year)){
			do_query_edit("DELETE FROM marks_addon_results WHERE add_id=$add_id", DB_year);
			$error = $lang['error_updating'];
		}
	} else {
		$error = $lang['no_privilege'];
	}
	$answer = array();
	if(!$error){
		$answer['id'] = $add_id;
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $error;
	}
	print json_encode($answer);
	exit;		
}

if(isset($_GET['add_result_form'])){
	if(getPrvlg('addons_read')){
		if(isset($_GET['add_id'])){
			$add_id = $_GET['add_id'];
			$addon = do_query("SELECT * FROM marks_addon WHERE id=$add_id", DB_year);
			if($addon['id'] != ''){
				$seek_add = true;
			}
			if($addon['level_id'] != '' ){ // level case
				$level_id = $addon['level_id'];
				$for_title = getLevelNameById($addon['level_id']);
			} else {
				$term_id = $addon['term_id'];
				$term = do_query("SELECT * FROM terms WHERE id=$term_id", DB_year); 
				$for_title = $term['title'];
				$approved = getPrvlg('marks_approv') ? 0 : ($term['approved']=='1' ? 1 : 0);
			}
			$notes = do_query_resource( "SELECT * FROM marks_addon_results WHERE results > -1 AND add_id =".$add_id, DB_year) ;
			$tot_std_exist =  mysql_num_rows($notes);
		} else {
			$seek_add = false;
			$approved = false;
			$tot_std_exist = 0;
		}
		$count_students = getStdNo($con, $con_id);
			
		$addon_result_form = write_html('div', 'class="ui-corner-all ui-state-highlight" style="padding:10px; margin-bottom:5px"',
			write_html('h2', 'class="approve reverse_align '.($approved != '1' ? 'hidden': '').'"', 
				'<img src="'.MS_assetspath.'/img/success.png" style="vertical-align:middle"/>'.
				$lang['approved']
			).
			'<input type="hidden" name="id" id="add_id" value="'.($seek_add ? $add_id : '').'" />'.
			'<input type="hidden" name="con" value="'.$con.'" />'.
			'<input type="hidden" name="con_id" value="'.$con_id.'" />'.
			'<input type="hidden" id="exam_approved" value="'.$approved.'" />'.
			'<input type="hidden" name="term_id" value="'.($seek_add && isset($addon['term_id']) ? $term_id : '').'" />'.
			'<input type="hidden" name="level_id" id="exam_level_id" value="'.$level_id.'" />'.
			'<input type="hidden" name="max" value="'.($seek_add && isset($addon['max']) ? $addon['max'] : '').'" />'.
			write_html('table', 'width="100%" border="0" cellspacing="0"',
				write_html('tr', '', 
					write_html('td', 'valign="middel" class="reverse_align" width="100"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['name'])
					).
					write_html('td', '',
						($seek_add ? 
							write_html('span', 'class="ui-widget-content ui-corner-right fault_input"',$addon['name'])
						:
							'<input type="text" name="tot" value="'.($seek_add ? $addon['name'] : '').'" />'
						)
					).		
					write_html('td', 'valign="middel" class="reverse_align" width="100"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['term'])
					).
					write_html('td', '',
						write_html('span', 'class="ui-widget-content ui-corner-right fault_input"',$for_title)
					)		
				).
				( $calc_type != 'skills' ? 
					write_html('tr', '', 
						write_html('td', 'valign="middel" class="reverse_align"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['max'])
						).
						write_html('td', '', 
							write_html('span', 'class="ui-widget-content ui-corner-right fault_input"',$addon['max'])
						).
						write_html('td', 'valign="middel" class="reverse_align"', 
							write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['min'])
						).
						write_html('td', '', 
							write_html('span', 'class="ui-widget-content ui-corner-right fault_input"',$addon['min'])
						)
					)
				:'').
				write_html('tr', '',
					write_html('td', 'valign="middel" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['students'])
					).
					write_html('td', '', 
						write_html('span', 'class="ui-widget-content ui-corner-right fault_input"', 
							($seek_add ? $tot_std_exist.' / '.$count_students : $count_students)
						)
					).						
					write_html('td', 'valign="middel" class="reverse_align"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['avrage'])
					).
					write_html('td', '',
						write_html('span', 'class="ui-widget-content ui-corner-right fault_input"', get_add_avrage($add_id))
					)	
				)
			)
		);
		// student table
		$stds = getStdIds($con, $con_id);
		$std_table_tbody = '';
		$count =1;

		foreach($stds as $std_id){
			if($addon['id'] != ''){
				$result = do_query( "SELECT results FROM marks_addon_results WHERE std_id=$std_id AND add_id = ".$add_id, DB_year);
				$res_val = $result['results'];
			} else {
				$res_val = '';
			}
			if($calc_type == 'skills'){
				$grds = getGradinArray($level_id);
				$result_html = '<select name="result_'.$std_id.'" style="width:80px" class="result">
					<option value="-1"></option>';
				if($grds != false){
					$i = 1;
					foreach($grds as $key => $value){
						$result_html .= write_html('option', 'value="'.$i.'" '.($res_val==$i ? 'selected="selected"' : ''), $key);
						$i++;
					}
				}
				$result_html .= '</select>';
			} else {
				$result_html = '<input type="text" class="result" name="result_'.$std_id.'" value="'.(($res_val > -1 && $res_val != '') ? $res_val : '').'" style="width:80px" />';
			}
			$chk_html = '<input type="checkbox" name="chk_'.$std_id.'" '.(($res_val > -1 && $res_val != '') ? 'checked="checked"' : '').' style="margin:0px"  />';
			$grad = $res_val == -1 ? $lang['abs'] : ($calc_type != 'skills' ? getStdGrad($level_id, $addon['max'], $res_val) : '');
			$std_table_tbody .= write_html('tr', '', 
				write_html('td', 'width="14" align="center"', $chk_html).
				write_html('td', '', getStudentNameById($std_id)).
				($calc_type != 'skills' ?
					write_html('td', 'style="padding:0px;"', $result_html)
				: '').
				write_html('td', 'align="center" valign="middel" style="text-align:center"', ($calc_type != 'skills' ? $grad : $result_html))
			);
			$count++;
		}
		
		echo write_html('form', 'id="exam_result"', 
			$addon_result_form . 
			write_html('table', 'class="tablesorter"', 
				write_html('thead', '',
					write_html('tr','',
						write_html('th', 'style="background-color:none" width="16"', '&nbsp;').
						write_html('th', '', $lang['name']).
						($calc_type != 'skills' ?
							write_html('th', 'width="78"', $lang['points'])
						: '').
						write_html('th', 'width="78"', $lang['grading'])
					)
				).
				$std_table_tbody
			)
		);

	} else {
		echo write_error($lang['no_privilege']);
	}
	
	exit;
}

if(isset($_GET['submit_addon_result'])){
	$error = false;
	if(getPrvlg('addons_edit')){	
		// get term id
		$term_id = $_POST['term_id'];
		$level_id = $_POST['term_id'];
		$add_id = $_POST['id'];
		$addon = do_query( "SELECT * FROM marks_addon WHERE id=$add_id", DB_year);
		if($term_id != ''){
			$term_id = $addon['term_id'];
			$term = do_query("SELECT * FROM terms WHERE id=$term_id", DB_year); 
			$approved = getPrvlg('marks_approv') ? 0 : ($term['approved']=='1' ? true : false);
		} else { // level case
			$approved = false;
		}

		if(!$approved){
			$vals =array();
			$stds = getStdIds($con, $con_id);
			foreach($stds as $std_id){
				$std_id = str_replace(' ', '',$std_id);
				if(isset($_POST['result_'.$std_id])){
					if($_POST['result_'.$std_id] != '' && isset($_POST['chk_'.$std_id] ) && $_POST['chk_'.$std_id] != false){ // value found
						$result = $_POST['result_'.$std_id];
					} else {
						$result = '-1';
					}
					
					$chk_result = do_query( "SELECT results FROM marks_addon_results WHERE add_id = $add_id AND std_id=$std_id", DB_year);
					if($chk_result['results'] != ''){
						$result_sql = "UPDATE marks_addon_results SET results='$result' WHERE add_id = $add_id AND std_id=$std_id";
					} else {
						$result_sql = "INSERT INTO marks_addon_results (add_id, std_id, results) VALUES ( $add_id, $std_id, '$result')";
					}
					if(!do_query_edit($result_sql, DB_year)){
						$error = 'Error : cant update addon!.';
					}
				}
			}
		} else {
			$error = $lang['addon_allready_approved'];
		}
	} else {
		$error = $lang['no_privilege'];
	}
	$answer = array();
	if(!$error){
		$answer['id'] = '';
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $error;
	}
	print json_encode($answer);
	exit;
	exit;	
}

?>