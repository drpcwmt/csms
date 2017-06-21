<?php 
## Gradding ##

/************************** Gradding  Structure ***************************************/
if(isset($_GET['delete_gradding'])){
	$id= $_POST['id'];
	if(do_query_edit("DELETE FROM gradding WHERE id=$id" , DB_student)){
		do_query_edit("DELETE FROM gradding_points WHERE gradding_id=$id" , DB_student);
		$answer['id'] = $id;
		$answer['error'] = "";
	 } else {
		$answer['id'] = "";
		$answer['error'] = $error;
	 }
	 print json_encode($answer);
	 exit;
}

if(isset($_GET['newgrad']) || isset($_GET['viewgrad'])){
	if(isset($_GET['newgrad'])){
		$seek = false;
		$tr = write_html('tr', '',
			write_html('td', '', '<input type="text" name="title[]"  style="height:24px" />').
			write_html('td', '', '<input type="text" name="min[]" style="width:40px; height:24px" /> %').
			write_html('td', '', '<input type="text" name="max[]" style="width:40px; height:24px" /> %').
			write_html('td', 'style="position:relative"', write_html('select', 'name="color[]"  class="color_picker" style="width:50px"',  write_select_options( getColorPicker(), '', false)))
		);
		for($i=1; $i<=3;$i++){
			$tr.=$tr;
		}
	} else {
		$tr = '';
		$seek = true;
		$grad_id = $_GET['viewgrad'];
		$graddins = do_query_resource("SELECT * FROM gradding_points WHERE gradding_id =$grad_id ORDER BY `min` DESC", DB_student);
		$grad_name = do_query( "SELECT name FROM gradding WHERE id=".$grad_id, DB_student);
		while($grad = mysql_fetch_assoc($graddins)){
			$tr .= write_html('tr', '', 
				write_html('td', '', '<input type="text" name="title[]" style="height:24px" value="'.$grad['title'].'" />').
				write_html('td', '', '<input type="text" name="min[]" style="width:40px; height:24px"" value="'.($grad['min'] != '0' ? $grad['min']: '0').'" /> %').
				write_html('td', '', '<input type="text" name="max[]" style="width:40px; height:24px"" value="'.($grad['max'] != '0' ? $grad['max']: '0').'" /> %').
				write_html('td', 'style="position:relative"', write_html('select', 'name="color[]" class="color_picker" style="width:75px"',  write_select_options( getColorPicker(), '#'.$grad['color'], false)))
			);
		}
	}
	
	$out = write_html('div', 'class="toolbox"',
		write_html('a', 'onclick="newTitle();" class="ui-state-default hoverable"', 
			$lang['add']. 
			write_icon('plus')
		)
	).
	write_html('form', 'id="gradding_form"',
		'<input type="hidden" name="id" value="'.($seek ? $grad_id : '').'" />' .
		write_html('div', 'class="ui-corner-all ui-state-highlight" style="padding:5px"', 
			write_html('table', 'cellspacing="0" border="0"', 
				write_html('tr', '',
					write_html('td', 'valign="middel" class="reverse_align" width="120"', 
						write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['title'])
					).
					write_html('td', '', 
						'<input type="text" name="name" value="'.($seek ? $grad_name['name'] : $lang['default']).'" />'
					)
				)
			)
		).
		write_html('table', 'class="tableinput" id="gradding_table"',
			write_html('thead', '',
				write_html('th', 'width=""', $lang['name']).
				write_html('th', 'width="80"', $lang['from']).
				write_html('th', 'width="80"', $lang['to']).
				write_html('th', 'width="80"', $lang['color'])
			).
			write_html('tbody', '', $tr)
		)
	); 

	echo $out;
	exit;
}

/************************* Submit Gradding ***************************************/
if(isset($_GET['submit_gradding']) ){
	if(getPrvlg('terms_edit')){
		$answer = array();
		$error = false;
		if($_POST['name'] == ''){
			$error = "Must deffine name.";
		} else {
			$name = $_POST['name'];
			if($_POST['id'] != ''){
				$id = $_POST['id'];
				do_query_edit("UPDATE gradding SET name='$name' WHERE id=$id", DB_student); 
			} else {
				$chk = do_query("SELECT name FROM gradding WHERE name='".$name."'" , DB_student);
				if($chk['name'] != ''){
					$error = $lang['title_allready_exists'];
				} else {
					do_query_edit( "INSERT INTO gradding (name) VALUES ('$name')", DB_student);
					$id = mysql_insert_id();
				}
			}
			
			if(!$error){
				do_query_edit("DELETE FROM gradding_points WHERE gradding_id=$id" , DB_student);
				$points_arr = array();
				for($i=0; $i<count($_POST['title']); $i++){
					if($_POST['title'][$i] != ''){
						$points_arr[] = "$id, '".$_POST['title'][$i]."', '".$_POST['max'][$i]."', '".$_POST['min'][$i]."', '".$_POST['color'][$i]."'";	
					}
				}
						
				if(!do_query_edit( "INSERT INTO gradding_points (gradding_id, title, max, min, color) VALUES(". implode("), (", $points_arr).")", DB_student)){
					$error = "can't insert points";
				}
			}
		}
	} else {
		$error = $lang['no_privilege'];
	}
	 if( !$error){
		$answer['id'] = $id;
		$answer['name'] = $name;
		$answer['error'] = "";
	 } else {
		$answer['id'] = "";
		$answer['name'] = "";
		$answer['error'] = $error;
	 }
	 print json_encode($answer);
	 exit;
}

/************************** get grad ***************************************/

if(isset($_GET['getgrad']) && $_GET['getgrad'] != ''){
	$level_id = $_GET['getgrad'];
	$max = $_GET['max'];
	$res = $_GET['res'];
	if($res==''){
		echo $lang['abs'];
	} else {
		$g_arr = getStdGrad($level_id, $max, $res);
		if($g_arr != false){		
			echo ucwords($g_arr);
		}
	}
	exit;
}