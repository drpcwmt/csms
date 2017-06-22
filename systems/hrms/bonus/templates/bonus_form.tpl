<form id="bonus_add_form">
	<fieldset class="ui-state-highlight">
        <table width="100%" cellspacing="0">
            <tr>
               <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
               <td>
               		<input type="text" value=""  name="emp_name" class="input_double" />
               		<input type="hidden" class="autocomplete_value" name="emp_id" />
               </td>
            </tr>
            <tr>
               <td><label class="label ui-widget-header ui-corner-left reverse_align">[#reason]</label></td>
               <td><input type="text" class="input_double" name="comments" /></td>
            </tr>
         </table>
     </fieldset>
     <fieldset>
     	<legend>[#bonus_type]</legend>
        <ul style="list-style:none; padding:0; margin:5px">
            <li class="ui-state-default hoverable ui-corner-all" style="padding:3px; margin-bottom:3px" onClick="$(this).find('input:radio').attr('checked', 'checked')">
            	<input type="radio" id="bonus_once" name="paid_type" value="once" checked /> [#bonus_once]
                <table width="100%" cellspacing="0">
                    <tr>
                       <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#month]</label></td>
                       <td>
                       	<select name="month" class="combobox">
                        	[@months_select]
                       	</select>
                       </td>
                    </tr>
                    <tr>
                       <td><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                       <td><input type="text" class="mask-date datepicker" name="date" value="[@today]" /></td>
                    </tr>
                    <tr>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label></td>
                        <td>
                            <input type="text" name="value" id="value"class="input_half" />
                        	<select name="value_type" class="combobox">
                            	<option value="value_by_day">[#value_by_day]</option>
                                <option value="value_by_cash">[#value_by_cach]</option>
                            </select>
                         </td>
                     </tr>
                </table>
            </li>
            <li class="ui-state-default hoverable ui-corner-all" style="padding:3px; margin-bottom:3px" onClick="$(this).find('input:radio').attr('checked', 'checked')">
                <input type="radio" id="bonus_multi" name="paid_type" value="multi" />[#bonus_multi]
                <table class="tableinput">
                	<thead>
                    	[@ths]
                    </thead>
                    <tbody>
                    	<tr>
                    		[@tds]
                        </tr>
                    </tbody>
                </table>                
           </li>
        </ul>
     </fieldset>
</form>
         
                