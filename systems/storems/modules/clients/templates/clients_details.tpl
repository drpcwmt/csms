<form name="client">
	<input type="hidden" name="id" value="[@id]" />
	<div class="dashed">
        <table width="100%">
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#name] <span class="astrix">*</span></label>
                </td>
                <td>
                    <input type="text" class="input_double required" name="name" value="[@name]" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#company]</label>
                </td>
                <td>
                    <input type="text" class="input_double" name="company" value="[@company]" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#group] <span class="astrix">*</span></label>
                </td>
                <td>
                    <select name="group_id" id="group_id" class="required combobox">
                     <option value=""></option>
                     [@groups_options]
                   </select>
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#code]</label>
                </td>
                <td>
                    <div class="fault_input ui-corner-right ">[@id]</div>
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#email]</label>
                </td>
                <td>
                    <input type="text" class="input_double" name="email" value="[@email]" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#address]</label>
                </td>
                <td>
                    <input type="text" class="input_double" name="address" value="[@address]" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#tel]</label>
                </td>
                <td>
                    <input type="text" class="input_double" name="tel" value="[@tel]" />
                </td>
            </tr>
		</table>
	</div>
</form>
<h2 style="padding:10px" class="ui-state-highlight ui-corner-all">[#balance] : [@balance]</h2>