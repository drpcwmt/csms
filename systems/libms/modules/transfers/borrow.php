<?php
session_start();
include("../connections/conx.php");
include('../common/functions.php');
include('../common/sql.php');
include('../lang/'.$_SESSION['ui-lang'].'.php');
$group ='*';
include('../restrict.php');
$seek = false;

// server list for borrow infos
if(isset($_GET['iserver'])){
	if($_GET['iserver'] == 'std'){
		$servers = get_SMS_servers();
		if(count($servers) > 1){
			echo '<label style="width:80px; display:inline-block">'.$lang['school'].'</label>
				<select name="server" id="server" onchange="$(\'#borrow_name\').show(); setAutocomplete(\'#borrow_name\', \'#server\',\'common/autocomplete.php?db=students&t=student_data_'.$_SESSION['lang'].'&f=id,first_name,last_name&w=first_name\' )">';
					echo '<option value=""> </option>';
					foreach( $servers as $server_name => $server_ip){
						echo '<option value="'.$server_ip.'">'.$server_name.'</option>';
					}
				echo '</select>';
		} else {
			foreach( $servers as $server_name => $server_ip){
				echo '<input type="hidden" name="server" value="'.$server_ip.'" />
				<script type="text/javascript">setAutocomplete(\'#borrow_name\', \'#server\',\'common/autocomplete.php?db=students&t=student_data_'.$_SESSION['lang'].'&f=id,first_name,last_name&w=first_name\' )</script>';
			}
		}
	} elseif($_GET['iserver'] == 'emp'){
		$servers = get_HrMS_servers();
		if(count($servers) > 1){
			echo '<label style="width:80px; display:inline-block">'.$lang['school'].'</label>
				<select name="server" id="server" onchange="$(\'#borrow_name\').show(); setAutocomplete(\'#borrow_name\', \'#server\',\'common/autocomplete.php?db=employers&t=employer_data_'.$_SESSION['lang'].'&f=id,first_name,last_name&w=first_name\' )">';
					echo '<option value=""> </option>';
					foreach( $servers as $server_name => $server_ip){
						echo '<option value="'.$server_ip.'">'.$server_name.'</option>';
					}
				echo '</select>';
		} else {
			foreach( $servers as $server_name => $server_ip){
				echo '<input type="hidden" name="server" value="'.$server_ip.'" />
				<script type="text/javascript">setAutocomplete(\'#borrow_name\', \'#server\',\'common/autocomplete.php?db=employers&t=employer_data_'.$_SESSION['lang'].'&f=id,first_name,last_name&w=first_name\' )</script>';
			}
		}

	}
	echo '<label style="width:80px; display:inline-block">'.$lang['borrow_name'].'</label><input type="text" name="borrow_name" id="borrow_name"  />';
	exit;
}
if(isset($_GET['borrow_id'])){
	$borrow_id= $_GET['borrow_id'];
	$row= do_query($hostname_lms, $database_lms, "SELECT * FROM borrow WHERE id=$borrow_id");
	$ssek = true;
}
echo '<div class="ui-corner-all ui-widget-content">';
	echo '<form id="borrow_form">
		<table width="100%" cellspacing="5">
			<tr>
				<td width="200">&nbsp;</td>
				<td><input type="hidden" name="id" value="" />
			</tr>
			<tr>
				<td>'.$lang['book_serial'].'</td>
				<td><input type="text" name="serial" value="'. (($seek) ? $row['serial'] : '' ) .'" /></td>
			</tr>
			<tr>
				<td>'.$lang['book'].' '.$lang['name'].'</td>
				<td>
					<input type="text" name="book_name" id="book_name" value="'. (($seek) ? getNameFromId('books', $row['serial']) : '' ) .'" />
					
				</td>
			</tr>
			<tr>
				<td>'.$lang['borrow_name'].'</td>
				<td id="borrow_identifier">
					<label style="width:80px; display:inline-block">'.$lang['group'].'</label>
					<select id="group" name="group" onchange="identifySch(this.value)">
						<option value=""></option>
						<option value="emp" '.(($seek && $row['group'] =="prof") ? 'selected="selected"' : '').'>'.$lang['prof'].'</option>
						<option value="std" '.(($seek && $row['group'] =="std") ? 'selected="selected"' : '').'>'.$lang['student'].'</option>
					</select>';

				echo '<td>&nbsp;</td>
			</tr>
		</table>
	</form>
</div>';
?>
<script type="text/javascript">
function identifySch(group){
	$.get(
		'blocks/borrow.php?iserver='+group,
		function(data){
			$('#borrow_identifier').append(data);
		}
	);
}
