<tr>
	<input type="hidden" name="permissionout_id" value="[@id]" />
    <td>
    	<button type="button" class="ui-state-default hoverable circle_button [@permissionout_remove_hidden]" action="deletePermissionout" permissionout_id="[@id]"><span class="ui-icon ui-icon-trash"></span></button>
    </td>
    <td>[@name]</td>
    <td>[@job_title]</td>
    <td widtd="60" align="center">[@begin]</td>
    <td widtd="60" align="center">[@end]</td>
    <td widtd="60" align="center">[@count]</td>
    <td widtd="60" align="center"><input type="text" value="[@value]" update="editPermis" name="value" class="input_half" [@value_disabled] /></td>
</tr>
