<fieldset class="ui-widget-content ui-corner-all"> 
	<legend class="ui-widget-header ui-corner-all">[#school_fees]</legend>
    <input type="hidden" name="wizard_step" id="wizard_step" value="3" />
    <div class="ui-state-highlight ui-corner-all"> 
    	<h3><label><input type="radio" name="fees_action" checked="checked" value="0" onClick="$('#annual_percent').attr('disabled', true)"/>[#skip_fees_calc]</label></h3>
        <h3><label><input type="radio" name="fees_action" value="1" onClick="$('#annual_percent').attr('disabled', false)" />[#apply_fees_calc]</label></h3>
        <table>
			<tr>
				<td width="120" valign="middel">
					<label class="label ui-widget-header ui-corner-left">[#percent]</label>
				</td>
                <td>
					<input id="annual_percent" type="text"  name="percent" value="[@percent]" disabled="disabled" class="half_input" /> %
                </td>
            </tr>
        </table>
    </div>
    <table class="tablesorter MS_tablesorter">
    	<thead>
        	<tr>
            	<th>[#name]</th>
                <th>[#currency]</th>
                <th>[@old_year]</th>
                <th>[@new_year]</th>
            </tr>
        <tbody>
          [@level_trs]
        </tbody>
    </table>
</fieldset> 
