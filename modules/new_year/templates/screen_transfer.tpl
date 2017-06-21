<fieldset>
	<input type="hidden" name="wizard_step" id="wizard_step" value="2" />'.
	<legend>[#options]</legend>
	<ul class="new_year_options">
    	<li> 
        	<label class="ui-widget-content ui-corner-all">
            	<input type="checkbox" value="1" name="transfer_stds" checked="checked"/>
                [#transfer_students]
                <br>&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="[@pro_opt_hidden]">
                	<input type="checkbox" value="1" name="all_marks" [@transfer_std_attr]  />
                    [#all_marks]
                </span>
            </label>
        </li>
    </ul>
	<ul class="new_year_options [@pro_opt_hidden]">
    	<li> 
        	<label class="ui-widget-content ui-corner-all" action="selectMaterials">
            	<input type="checkbox" value="1" name="copy_service" checked="checked"/>
                [#copy_material]
            </label>
        </li>
    	<li> 
        	<label class="ui-widget-content ui-corner-all">
            	<input type="checkbox" value="1" name="generate_optional_groups" checked="checked"/>
                [#generate_optional_groups]
            </label>
        </li>
    	<li> 
        	<label class="ui-widget-content ui-corner-all" action="selectGroupReligion">
            	<input type="checkbox" value="1" name="generate_religion_groups" checked="checked"/>
                [#generate_religion_groups]
            </label>
            <table id="religion_table" border="0" cellspacing="0" style="margin:8px 20px" class="hidden">
				<tr>
					<td width="120" valign="middel">
						<label class="label ui-widget-header ui-corner-left"> [#islamic_subject]</label>
                    </td>
                    <td>
                    	<select name="ser_muslim" class="combobox">[@mat_opts]</select>
                    </td>
                </tr>
				<tr>
					<td width="120" valign="middel">
						<label class="label ui-widget-header ui-corner-left"> [#christian_subject]</label>
                    </td>
                    <td>
                    	<select name="ser_christian" class="combobox">[@mat_opts]</select>
                    </td>
                </tr>
			</table>
        </li>
    	<li> 
        	<label class="ui-widget-content ui-corner-all">
            	<input type="checkbox" value="1" name="copy_schedule" checked="checked"/>
                [#copy_schedule_structure]
            </label>
        </li>
    	<li> 
        	<label class="ui-widget-content ui-corner-all" action="selectTerms">
            	<input type="checkbox" value="1" name="copy_terms" checked="checked"/>
                [#copy_terms]
            </label>
            <div id="terms_div" class="hidden" style="margin:8px 20px">
				<table class="result">
					<thead>
                    	<tr>
                        	<th>[#name]</th>
                            <th>[#begin_date]</th>
                            <th>[#end_date]</th>
                         </tr>
                    </thead>
                    <tbody>
                    	[@terms_trs]
                    </tbody>
                </table>
            </div>
        </li>
    </ul>
</fieldset>