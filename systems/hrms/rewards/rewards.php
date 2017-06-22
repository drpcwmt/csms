<?php
if($prvlg->_chk('bonus_read') == false){die($lang['restrict_accses']);};


if(isset($_POST['ids']) && $_POST['ids'] !='' && in_array($_SESSION['group'], array('admin', 'superadmin'))){
	$day = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	$ids = explode(',', $_POST['ids']);
	foreach($ids as $id){
		if(isset($_POST['cash'])){
			$sql = "INSERT INTO bonus (con_id, day, cash, comments) VALUES ($id, $day, ".$_POST['cash'].", '".$_POST['comments']."')";
		} elseif (isset($_POST['value'])){
			$sql = "INSERT INTO bonus (con_id, day, value, comments) VALUES ($id, $day, ".$_POST['value'].", '".$_POST['comments']."')";
		}
			
		do_query_edit( $sql);
	}
	echo 1;
	exit;
}

if(isset($_GET['res'])){
	$today = mktime(0,0,0,date('m'),date('d'), date('Y'));
	
	$sql_bonus = "SELECT * FROM bonus WHERE  bonus.day=$today";
	$query_bonus = do_query_resource( $sql_bonus);
	$abs_total = mysql_num_rows($query_bonus);
	if($abs_total > 0 ){
		while($row_bonus = mysql_fetch_assoc($query_bonus)){
			echo '<tr>
				<td class="unpritable" align="center"><a href="#" class="ui-icon ui-icon-person" onclick="loadToDiv(\'#employer_details\',\'blocks/employer_data.php?dialog&id='.$row_bonus['con_id'].'\');$(\'#employer_details\').dialog(\'open\')"></a></td>
				<td>'.getEmployerNameById($row_bonus['con_id']).'</td>
				<td align="center">'.getJobNameByEmpId($row_bonus['con_id']).'</td>
				<td align="center">'.$row_bonus['value'].'</td>
				<td align="center">'.$row_bonus['cash'].'</td>
				<td align="center">'.$row_bonus['comments'].'</td>
				<td class="unpritable" align="center"><a href="#" class="ui-icon ui-icon-closethick" onclick="deleteBonus('.$row_bonus['id'].')"></a></td>
			</tr>';
		}
	}

	echo '<script type="text/javascript">
		$("#bonus_list").tablesorter();
		$("a.infos").click(function(){
			$("#dialog_info").html($(this).attr("title"));
			$("#dialog_info").dialog("open");
		})
	</script>';

exit;	
}

// delete absents
if(isset($_GET['delbouns'])){
	$delete = false;
	if(in_array($_SESSION['group'], array('admin', 'superadmin'))){
		if(do_query_edit( "DELETE FROM bonus WHERE id=".$_GET['delbouns'])){
			$delete = true;
		}
	}
	if($delete){ echo 1;} else {echo 'Error';}
	exit;
}


if(isset($_GET['bonuslist'])){
	$month = $_GET['m'];
	$sch = $_GET['s'];
	$job = $_GET['j'];


	$sql = "SELECT employer_data.school, employer_data.job_code, employer_data.id, bonus.* FROM employer_data, bonus WHERE employer_data.id = bonus.con_id AND bonus.day >= ".getGlobalSetting('begin_date');
	
	if($job != 0){ $sql .= " AND employer_data.job_code=$job";}
	if($month != 0){ 
		$month_begin = mktime(0, 0, 0, $month, 1, date('Y'));
		$month_end = mktime(0, 0 ,0, $month + 1, 1, date('Y'));
		$sql .= " AND (bonus.day >=$month_begin AND bonus.day<$month_end) " ;
	}
	if($sch != 'ALL'){
		$sql .= " AND employer_data.school='$sch'";
	}
	
	$query_bonus = do_query_resource( $sql);
	$abs_total = mysql_num_rows($query_bonus);
	if($abs_total > 0 ){
		
		while($row_bonus = mysql_fetch_assoc($query_bonus)){
			echo '<tr>
				<td align="center">'.unixToDate($row_bonus['day']).'</td>
				<td>'.getEmployerNameById($row_bonus['con_id']).'</td>
				<td align="center">'.getJobNameByEmpId($row_bonus['con_id']).'</td>
				<td align="center">'.$row_bonus['value'].'</td>
				<td align="center">'.$row_bonus['cash'].'</td>
				<td align="center">'.$row_bonus['comments'].'</td>
			</tr>';
		}
	}

	echo '<script type="text/javascript">
		$("#list_res").tablesorter();
		$("a.infos").click(function(){
			$("#dialog_info").html($(this).attr("title"));
			$("#dialog_info").dialog("open");
		})
	</script>';
	exit;
}

?>
<div class="tabs" id="tabDiv">
    <ul>
        <li><a href="#tabs-12-1"><?php echo $lang['bonus_insert'];?></a></li>
        <li><a href="#tabs-12-2"><?php echo $lang['bonus_list'];?></a></li>
        <li><a href="#tabs-12-3"><?php echo $lang['annual_bonus'];?></a></li>
    </ul>
  	<div id="tabs-12-1" class="ui-widget-content ui-corner-all" style="padding:10px">
    	<div class="ui-state-highlight ui-corner-all" style="padding:10px">
            <table width="100%" border="0" cellpadding="3" cellspacing="5">
              <tr>
                <td width="200" valign="top">
                    <input type="hidden" id="stdIds" name="stdIds" />
                    <h3><?php echo $lang['by_emp'];?></h3>
                    <input name="sugName" type="text" id="sugName"  /><span id="sugNameClear" class="hidden mini_link "> <?php echo $lang['clear'];?> </span>
                <h3><?php echo $lang['by_job'];?></h3>
                    <?php 
                        $job_sql = "SELECT * FROM jobs";
                        $job_query = do_query_resource( $job_sql);
                        echo '<select name="class_id">
                                <option></option>';
                        while($row_job = mysql_fetch_assoc($job_query)){
                                echo '<option onclick="getemployersByJob(this)" value="'.$row_job['job_code'].'">'.$row_job['name_'.$_SESSION['ui-lang']].'</option>';
                        }
                        echo '</select>';
                    ?>
                    <br>
                </td>
                <td valign="top">
                <fieldset>
                  <legend><?php echo $lang['bonus_value'];?></legend>
                  <ul style="list-style:none; padding:0; margin:5px">
                        <li><input type="radio" id="valByDay_rad" name="value_type" checked /> <?php echo $lang['value_by_day'];?></li>
                        <li><input type="radio" id="valByCach_rad" name="value_type" /> <?php echo $lang['value_by_cach'];?></li><br>
                  </ul>
    
                    <input type="text" name="cach_val" id="cach_val" style="width:50px" />
                 </fieldset>   
                </td>
                <td valign="top">
                 <fieldset>
                  <legend><?php echo $lang['comments'];?></legend>
                    <textarea name="comments" rows="7" id="comments"></textarea>
                 </fieldset>
                </td>
                <td align="center" valign="bottom"><button type="button" onClick="addbonus()"><?php echo $lang['add'];?></button></td>
              </tr>
            </table>
             
  </div>
        <div class="ui-state-highlight ui-corner-all unprintable" style="padding:10px;margin-bottom:10px"><button class="clickable" onclick="print_pre('#bonus_list_print')" type="button"><span class="ui-icon ui-icon-print"></span><?php echo $lang['print'];?></button></div>

      <div style="margin-top:10px" id="bonus_list_print">
        	 <div class="showforprint hidden" align="center">
                 <h1><?php echo $lang['bonus_report'];?></h1>
             </div> 
<table id="bonus_list" cellspacing="1"  class="tablesorter" width="100%">
                <thead>
                    <tr>
                        <th width="30" style="background-image:url(../images/spacer.gif)" class="unprintable">&nbsp;</th>
                        <th><?php echo $lang['name'];?></th>
                        <th><?php echo $lang['job'];?></th>
                        <th><?php echo $lang['value_by_day'];?></th>
                        <th><?php echo $lang['value_by_cach'];?></th>
                        <th><?php echo $lang['comments'];?></th>
                        <th width="30" style="background-image:url(../images/spacer.gif)">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="bonusRes"></tbody>
            </table>
        </div>
</div>
  	<div id="tabs-12-2" class="ui-widget-content ui-corner-all" style="padding:10px">
        <div style="padding:10px">
            <table width="100%" border="0" cellspacing="5">
              <tr>
                <td width="170" valign="top">
                    <div class="ui-state-highlight ui-corner-all" style="padding:10px">
					<?php
                    $sch_query = do_query_resource( "SELECT * FROM school");
                    $num_sch = mysql_num_rows($sch_query);
                    if($num_sch > 1){
                        echo '<h3>'.$lang['school'].': </h3>
                            <select name="school" id="school_list">
                                <option value="ALL" >'.$lang['all'].'</option>';
                                while($sch_row = mysql_fetch_assoc($sch_query)){
                                    echo '<option value="'.$sch_row['code'].'" >'.$sch_row['code'].'</option>';
                                }
                            echo'</select><br />';
                    }
                    $sch_list = getJobList();
                    echo '<h3>'.$lang['job'].': </h3>
                    <select name="job" id="job_list">
                        <option value="0" >'.$lang['all'].'</option>';
                        foreach($sch_list as $code => $job){
                            echo '<option value="'.$code.'" >'.$job.'</option>';
                        }
                    echo'</select><br />';
                ?>
                <h3><?php echo $lang['month'];?>: </h3>
                <select id="list_month">
                    <?php
                    echo '<option value="0">'.$lang['months_0'].'</option>';
                    $begin_date = getGlobalSetting('begin_date');
                    $begin_month = date('m', $begin_date);
                    $cur_month = date('m');
                    $cur_year = date('Y');
                    $c = $begin_month; 
                    while($c != $cur_month){
                        
                        $t = date('m', mktime(0,0,0,$c, 1, $cur_year));
                        echo '<option value="'.$t.'">'.$lang["months_$t"].'</option>';
                        if($c == 12){ $c = 1;} else { $c++;}
                    }
                    ?>
                </select><br />
                 <button onclick="submitBonusReq()"><?php echo $lang['search'];?></button>
             </div>
             
             </td>
                <td valign="top">
                <div class="ui-state-highlight ui-corner-all unprintable" style="padding:10px; margin-bottom:10px"><button class="clickable" onclick="print_pre('#bonus_list_print')" type="button"><span class="ui-icon ui-icon-print"></span><?php echo $lang['print'];?></button></div>
                <div id="list_res_print">
                
        <div style="margin-top:10px" id="bonus_list_print">
         <div class="showforprint hidden" align="center">
                             <h1><?php echo $lang['bonus_report'];?></h1>
                         </div> 

                    <table id="list_res" cellspacing="1"  class="tablesorter" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo $lang['date'];?></th>
                                <th><?php echo $lang['name'];?></th>
                                <th><?php echo $lang['job'];?></th>
                                <th><?php echo $lang['value_by_day'];?></th>
                                <th><?php echo $lang['value_by_cach'];?></th>
                                <th><?php echo $lang['comments'];?></th>
                            </tr>
                        </thead>
                        <tbody id="list_res_tbody">
                           
                        </tbody>
                    </table>        
                   </div>        
                </td>
              </tr>
            </table>
        </div>
   	</div>
  	<div id="tabs-12-3" class="ui-widget-content ui-corner-all" style="padding:10px">

	</div>
</div>    
<script type="text/javascript">
loadBonusRes();
setAutocompleteEmp('#sugName','common/autocomplete.php?db=employers&t=employer_data_<?php echo $_SESSION['ui-lang'];?>&f=id,first_name,last_name&w=first_name');

$('#sugNameClear').click(function(){
	$('#sugName').val('').focus();
	$('#sugName').attr('term','');
	$('#sugNameClear').fadeOut();
});

$('#sugName').focus(function(){
	$('#sugNameClear').fadeIn();
	$('#stdIds').val('');
	$('#std_class_list').empty();
});


function addbonus(){
	var ids, data;
	if($('#sugName').attr('term') != ''){
		data = 'ids=' + $('#sugName').attr('term');
	}
	if($('#stdIds').val() !=''){
		data = 'ids=' + $('#stdIds').val();
	}
	
	if($('#valByDay_rad').attr('checked')){
		data += '&value='+$('#cach_val').val();
	} else if($('#valByCach_rad').attr('checked')){
		data += '&cach='+$('#cach_val').val();
		
	}
	
	data += '&comments=' + $('#comments').val();
	$.ajax({
		url : 'blocks/bonus.php', 
		type : 'POST',
		data : data,
		success :  function(data){
			if(data == '1'){
				$('#sugName').val('');
				$('#sugName').attr('term','');
				$('#sugNameClear').fadeOut();
				loadBonusRes();

			}else {
				alert('Error');
			}
		}
	});
	$('#std_class_list').empty();
	
}

function loadBonusRes(){
	$('#bonusRes').load('blocks/bonus.php?res');
}

function submitBonusReq(){
	var sch = $('#school_list').val();
	var month = $('#list_month').val();
	var job = $('#job_list').val();
	$.ajax({
		url : 'blocks/bonus.php?bonuslist&m='+month+'&j='+job+'&s='+sch, 
		success :  function(data){
			if(data != ''){
				$('#list_res_tbody').html(data);
			}
		}
	});
}

function deleteBonus(id){
	$.ajax({
		url: 'blocks/bonus.php?delbouns='+id,
		success : function(data){
			if(data == '1'){
				loadBonusRes()
			} else {
				alert('Error');
			}
		}
	})
}

function getemployersByJob(jobId){
	$('#stdIds').val('');
	
	$('#dialog_info').load('blocks/absents.php?joblist=' + $(jobId).val(), function(){
		$("#std_class_list li").click(function(){
			$(this).toggleClass("ui-state-active");
		});
		
		$( "#dialog_info" ).dialog( "option", "height", 450); 
		$( "#dialog_info" ).dialog( "option", "buttons", { 
			'<?php echo $lang['select'];?>':function(){
				var list = new Array();
				$("#std_class_list li.ui-state-active").each(function(){
					list.push($(this).attr("val"));
				});
				$('#stdIds').val(list.join(','));
				$("#dialog_info").dialog('close');
			},
			'<?php echo $lang['cancel'];?>':function(){
				$("#dialog_info").dialog('close');
				$("#dialog_info").html('');
			}
		});
		$("#dialog_info").dialog('open');
	})
	
}


$('#employer_details').dialog({
	autoOpen: false,
	height:600,
	width: 800,
	modal: true,
	buttons: {
		'<?php echo $lang['save'];?>':function(){
			submitForm(); 
			var data = $('#dialog_from_std_list form').serialize();
			$.post(
				'blocks/employer_list.php?'+$('#std_list_pram').val(),
				data,
				function (answer){
					$('#dataDiv').html(answer)
				}
			)
		},
		'<?php echo $lang['cancel'];?>':function(){
			$(this).dialog('close');
		}
	}
});

</script>