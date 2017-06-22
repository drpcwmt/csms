<form>
	<input type="hidden" name="id" value="[@id]" />
    <div class="dashed">
    
        <table width="100%">  
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#name]</label>
                </td>
                <td>
                    <input type="text" name="name" value="[@name]" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#resp]</label>
                </td>
                <td>
                    <input type="text" class="input_double required" value="[@resp_name]" name="emp_sug_div" id="emp_sug_div">
                    <input type="hidden" value="" class="autocomplete_value" id="resp" name="resp">
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
                    <label class="label ui-widget-header ui-corner-left">[#address]</label>
                </td>
                <td>
                    <input type="text" name="address" value="[@address]" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#tel]</label>
                </td>
                <td>
                    <input type="text" name="tel" value="[@tel]" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#max_content]</label>
                </td>
                <td>
                    <input type="text" name="max_content" value="[@max_content]" />
                </td>
            </tr>
        </table>
    </div>
</form>       