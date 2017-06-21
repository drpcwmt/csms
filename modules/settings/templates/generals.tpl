<table cellspacing="0" border="0">
	<tr>
		<td width="150" valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#def_theme]</label>
		</td>
		<td valign="top">
			<select name="def_theme" class="combobox" id="def_theme">[@theme_arr]</select>
		</td>
	</tr>
	<tr>
		<td width="120" valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#default_lang]</label>
		</td>
		<td valign="top"> 
			<select name="default_lang" class="combobox" id="default_lang">[@lang_arr]</select>
		</td>
	</tr>
	<tr>
		<td valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#debug_mode]</label>
		</td>
		<td valign="top"> 
			 <span class="buttonSet"> 
             	<input type="radio"  name="debug_mode" id="debug_mode1" value="1" [@debug_mode_active]/><label for="debug_mode1">[#on]</label>
				<input type="radio"  name="debug_mode" id="debug_mode0" value="0" [@debug_mode_off]/><label for="debug_mode0">[#off]</label>
			</span>
		</td>
	</tr>
	<tr>
		<td width="120" valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#session_timeout]</label>
		</td>
		<td valign="top"> 
        	<input type="text" name="sessiontimeout" id="sessiontimeout" class="input_half" value="[@sessiontimeout]" />[#minuts]
		</td>
	</tr>

	<tr>
		<td width="120" valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#logo]</label>
		</td>
		<td valign="top"> 
			<button type="button" class="ui-state-default hoverable ui-corner-all" module="upload" action="changeLogo"> 
				<span class="ui-icon ui-icon-circle-arrow-n"></span>
				[#change]
			</button>
			<span>  <img src="attachs/img/logo.png" id="settings-header" height="25" border="0" style="vertical-align: bottom; margin:0px 15px" /></span>
		</td>
	</tr>
	<tr>
		<td width="120" valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#header]</label>
		</td>
		<td valign="top"> 
			<button type="button" class="ui-state-default hoverable ui-corner-all " module="upload" action="changeHeader"> 
				<span class="ui-icon ui-icon-circle-arrow-n"></span>
				[#change]
			</button>
			<span>  <img src="attachs/img/header.jpg" id="settings-header" height="25" border="0" style="vertical-align: bottom; margin:0px 15px" /></span>
		</td>
	</tr>
	<tr>
		<td width="120"valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#footer]</label>
		</td>
		<td valign="top"> 
			<button type="button" class="ui-state-default hoverable ui-corner-all" module="upload" action="changeFooter"> 
				<span class="ui-icon ui-icon-circle-arrow-n"></span>
				[#change]
			</button>
			<span>  <img src="attachs/img/footer.jpg" id="settings-footer" height="25" border="0" style="vertical-align: bottom; margin:0px 15px" /></span>
		</td>
	</tr>
	<tr>
		<td width="120"valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#ltr_lang_name]</label>
		</td>
		<td valign="top"> 
        	<input type="text" name="name_template" id="name_template" class="input_double" value="[@name_template]" />
		</td>
	</tr>
	<tr>
		<td width="120"valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#date_template]</label>
		</td>
		<td valign="top">
        	<input type="text" name="date_template" id="date_template" value="[@date_template]" />
		</td>
	</tr>
    <tr>
		<td width="120"valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#auto_backup]</label>
		</td>
		<td valign="top">
			 <span class="buttonSet"> 
             	<input type="radio"  name="auto_backup" id="auto_backup1" value="1" [@auto_backup_active]/><label for="auto_backup1">[#on]</label>
				<input type="radio"  name="auto_backup" id="auto_backup0" value="0" [@auto_backup_off]/><label for="auto_backup0">[#off]</label>
			</span>
		</td>
	</tr>
    <tr>
		<td width="120"valign="middel" class="reverse_align"> 
			<label class="label ui-widget-header ui-corner-left">[#backup_ttl]</label>
		</td>
		<td valign="top">
        	<input type="text" name="backup_ttl" id="backup_ttl" value="[@backup_ttl]" />
		</td>
	</tr>
</table>
