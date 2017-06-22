<form id="overtime_add_form">
	<fieldset>
        <table width="100%" cellspacing="0">
            <tr>
               <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
               <td>
               		<input type="text" value=""  name="emp_name" class="input_double" />
               		<input type="hidden" class="autocomplete_value" name="emp_id" />
               </td>
            </tr>
            <tr>
               <td><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
               <td><input type="text" class="mask-date datepicker" name="date" value="[@today]" /></td>
            </tr>
            <tr>
                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#begin_time]</label></td>
                <td>
                    <input type="text" class="mask-time input-half" name="begin" value="[@begin_time]" />
                 </td>
             </tr>
            <tr>
               <td><label class="label ui-widget-header ui-corner-left reverse_align">[#end_time]</label></td>
               <td><input type="text" class="mask-time input-half" name="end" /></td>
            </tr>
            <tr>
              <td valign="top">
              	<label class="label ui-widget-header ui-corner-left reverse_align">[#notes]</label></td>
              <td><textarea name="notes">[@notes]</textarea></td>
            </tr>
         </table>
     </fieldset>
</form>
         
                