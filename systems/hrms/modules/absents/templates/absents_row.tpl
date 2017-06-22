<tr>
	<input type="hidden" name="abs_id" value="[@id]" />
	<td>
    	<button type="button" class="ui-state-default hoverable circle_button [@edit_absent_hidden]" action="deleteAbs" abs_id="[@id]"><span class="ui-icon ui-icon-trash"></span></button>
    </td>
    <td>[@name]</td>
    <td>[@job_title]</td>
    <td align="center"><input type="checkbox" value="1" [@approved_chk] update="editAbs" name="approved" /></td>
    <td align="center"><input type="checkbox" value="1" [@ill_chk] update="editAbs" name="ill" /></td>
    <td>[@longAbs]</td>
    <td><input type="text" value="[@value]" update="editAbs" name="value" class="input_half" [@value_disabled] /></td>
    <td><p style="margin:1px 2px; float:right">[@comments]</p>
    	<button class="ui-state-default hoverable circle_button [@edit_absent_hidden]" action="addAbsComments" abs_id="[@id]"><span class="ui-icon ui-icon-pencil"></span></button>
    </td>
</tr>   
     