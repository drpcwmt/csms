<form class="unprintable ui-state-highlight ui-corner-all">
	<table cellspacing="0">
    	<tr>
        	<td><label class="label ui-widget-header ui-corner-left reverse_align">[#level]:</label></td>
            <td>
            	<select name="level_id">
                	[@levels_opts]
                </select>
            </td>
        	<td><label class="label ui-widget-header ui-corner-left reverse_align">[#view]:</label></td>
            <td>
            	<select name="sex">
                	<option value="">[#all]</option>
                    <option value="1">[#boys]</option>
                    <option value="2">[#girls]</option>
                </select>
            </td>
        	<td><label class="label ui-widget-header ui-corner-left reverse_align">[#order]:</label></td>
            <td>
            	<select name="order">
                	<option value="sex">[#sex]</option>
                    <option value="age">[#age]</option>
                </select>
            </td>
            <td>
            	<button type="button" action="searchRegReport" class="ui-state-default hoverable">[#search]<span class="ui-icon ui-icon-search"></span></button>
            </td>
		</tr>
    </table>
</form> 