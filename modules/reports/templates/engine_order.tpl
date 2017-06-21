<h3>[#order]</h3>
<fieldset class="ui-state-highlight">
    <legend>[#principal_order]</legend>
    <table  border="0" cellspacing="0">
        <tr>
            <td width="120" valign="middel">
                <label class="label ui-widget-header ui-corner-left"> [#order_by]</label>
            </td>
            <td>
                <select id="order_by" name="order_1" class="combobox">
                	[@order_by]
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label>
                    <input type="checkbox" name="grouped" value="1" />
                    [#each_par_page]
                </label>
            </td>
        </tr>
	</table>
</fieldset>            

<fieldset>
    <legend>[#secondary_order]</legend>
    <table  border="0" cellspacing="0">
        <tr>
            <td width="120" valign="middel"> 
                <label class="label ui-widget-header ui-corner-left"> [#order_by]</label>
            </td>
            <td>
                <select name="order_2" class="combobox">[@order_by]</select>
            </td>
        </tr>
        <tr>
            <td width="120" valign="middel"> 
                <label class="label ui-widget-header ui-corner-left"> [#order_by]</label>
            </td>
            <td>
                <select name="order_3" class="combobox">[@order_by]</select>			
            </td>
        </tr>
        <tr>
            <td width="120" valign="middel"> 
                <label class="label ui-widget-header ui-corner-left"> [#order_by]</label>
            </td>
            <td>
                <select name="order_4" class="combobox">[@order_by]</select>		
            </td>
        </tr>	
    </table>
</fieldset>