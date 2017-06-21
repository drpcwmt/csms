<div class="tabs">
	<ul>
		<li><a href="#system_backupForm" >BackUp &amp; restore</a></li>
		<li><a href="#system_updateForm" >Update</a></li>
		<li><a href="index.php?module=system&amp;action=systools" >Database</a></li>
        <li><a href="#tpl_editor" >Editor</a></li>
		<li><a href="index.php?module=system&amp;action=dictionary" >Dictionary</a></li>
	</ul>
	<div id="system_backupForm">
		<form style="padding:10px; margin:15px" class="ui-corner-all ui-state-highlight" id="backup_form">
			<input type="hidden" value="backup" name="action" class="ui-corner-right">
			<h3>Backup system</h3>
			<table width="100%" cellspacing="1" cellpadding="0" border="0">
				<tr>
					<td valign="right" width="40%">
						<label><input type="checkbox" name="sql" value="1" checked="checked">Database</label>
					</td>
					<td>
						<div class="progress" id="backup_mysql_progressbar"></div>
					</td>
					<td width="40">
						<img class="success hidden" src="assets/img/success.png" width="24" alt="Success" />
						<img class="fail hidden" src="assets/img/error.png" width="24" alt="Failed" />
					</td>
				</tr>
				<tr>
					<td valign="right">
						<label><input type="checkbox" name="file" value="1">Files</label>
					</td>
					<td>
						<div class="progress" id="backup_files_progressbar"></div>
					</td>
					<td width="40">
						<img class="success hidden" src="assets/img/success.png" width="24" alt="Success" />
						<img class="fail hidden" src="assets/img/error.png" width="24" alt="Failed" />
					</td>
				</tr>
				<tr>
					<td valign="center" colspan="2">
						<button action="doBackup" class="ui-state-default hoverable" type="button"> Backup now </button>
						
					</td>
				</tr>
			</table>
		</form>
		<hr />
		[@backup_table]
	</div> 
	<div id="system_updateForm">
		<form style="padding:10px; margin:15px" class="ui-corner-all ui-state-highlight" action="index.php?module=system&amp;action=update" method="post" enctype="multipart/form-data" target="update-ifram" id="update_form">
			<input type="hidden" value="update" name="action">
			<h3>Update system</h3>
			<table width="100%" cellspacing="1" cellpadding="0" border="0">
				<tr>
					<td width="85" valign="middel">&nbsp;</td>
					<td valign="right">
						<label><input type="file" name="file"></label>
					</td>
				</tr>
				<tr>
					<td width="85" valign="middel">&nbsp;</td>
					<td valign="right">
						<input type="submit" onclick="$('#update_form').submit()" value="Update now">
					</td>
				</tr>
			</table>
		</form>
		<iframe width="100%" style="min-height:290px;" src="index.php?module=system&amp;action=update" name="update-ifram"></iframe>
	</div>	
	<div id="tpl_editor">
        <table width="100%" cellspacing="2">
            <tr>
                <td width="250" valign="top" class="ui-state-highlight">
                	<ul id="editor_explorer">
                    	[@explorer]
                    </ul>
                </td>
                <td valign="top" id="editor_data_td">
                    
                </td>
            </tr>
        </table>
    </div>
</div>

			
			
