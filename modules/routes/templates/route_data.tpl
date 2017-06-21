<form id="route_form" class="ui-corner-all ui-state-highlight"> 
    <input type="hidden" id="id" name="id" value="[@id]"/>
    <table width="100%" cellpadding="0" border="0">
        <tr>
            <td width="120">
                <label class="label ui-widget-header ui-corner-left">[#no.]</label>
            </td>
            <td>
              <input type="text" id="no" name="no" value="[@no]"/>
            </td>
            <td width="120"><label class="label ui-widget-header ui-corner-left">[#group]</label></td>
            <td><select name="group_id" id="group_id" class="combobox">
               
                    [@group_opts]
                
            </select></td>
            
        </tr>
        <tr>
            <td width="120">
                <label class="label ui-widget-header ui-corner-left">[#region]</label>
            </td>
            <td>
                <input type="text" id="region" name="region" value="[@region]"/>
            </td>
            <td><label class="label ui-widget-header ui-corner-left">[#bus_code]</label></td>
            <td><select name="bus_id" id="bus_id" class="combobox">
               
                    [@bus_opts]
                
            </select></td>
        </tr>
        <tr>
            <td width="120">
                <label class="label ui-widget-header ui-corner-left">[#driver]</label>
            </td>
            <td colspan="3">
                <input type="text" id="driver_name" class="input_double required" value="[@driver_name]"/>
                <input type="hidden" id="driver_id" name="driver_id"  class="autocomplete_value" value="[@driver_id]"/>
                <em class="[@new_hidden]">[#tel]: [@driver_tel]</em>
            </td>
        </tr>
        <tr>
            <td width="120">
                <label class="label ui-widget-header ui-corner-left">[#matron]</label>
            </td>
            <td colspan="3">
                <input type="text" id="matron_name" class="input_double required" value="[@matron_name]"/>
                <input type="hidden" id="matron_id" name="matron_id"  class="autocomplete_value" value="[@matron_id]"/>
                <em class="[@new_hidden]">[#tel]: [@matron_tel]</em>
            </td>
        </tr>
    </table>
</form>
