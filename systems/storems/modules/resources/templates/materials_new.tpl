<form id="new_resource_form" class="ui-state-highlight ui-corner-all" style="padding:5px">
    <input type="hidden" id="mat_id" name="id" value="" />
    <table width="100%" border="0" cellspacing="0">
        <tr>
            <td width="120" valign="middel"> 
                <label class="label ui-widget-header ui-corner-left"> [#name_en]</label>
            </td>
            <td>
                <input type="text" id="mat_name_en" name="name_ltr" value="" />
            </td>
        </tr>
        <tr>
            <td width="120" valign="middel"> 
                <label class="label ui-widget-header ui-corner-left"> [#color]</label>
            </td>
            <td>
            	<select name="color" class="color_picker" id="mat_color"> [@colors_opts]</select>
            </td>
        </tr>
        <tr>
            <td width="120" valign="middel"> 
                <label class="label ui-widget-header ui-corner-left"> [#name_ar]</label>
            </td>
            <td>
                <input type="text" id="mat_name_ar" name="name_rtl" value="" />
            </td>
        </tr>
        <tr>
            <td width="120" valign="middel"> 
                <label class="label ui-widget-header ui-corner-left"> [#group]</label>
            </td>
            <td>
                <select name="group_id" class="combobox" id="group_id">[@groups_opts]</select> 		
            </td>
        </tr>
    </table>
</form>            