<div style="padding:10px; margin-bottom:5px" class="ui-corner-all ui-state-highlight">
	[@approved_img]
    <input type="hidden" value="[@exam_id]" name="id" />
    <input type="hidden" value="[@con]" name="con" />
    <input type="hidden" value="[@con_id]" name="con_id" />
    <input type="hidden" value="[@approved]" name="approved" />
    <table width="100%" cellspacing="0" border="0" cellpadding="0">
    	<tr>
        	<td width="100" valign="middel" class="reverse_align">
            	<label class="label ui-widget-header ui-corner-left">[#title]</label>
            </td>
            <td>
            	<input type="text" name="title" value="[@title]" [@title_disabled />
            </td>
            <td width="100" valign="middel" class="reverse_align">
            	<label class="label ui-widget-header ui-corner-left">[#material]</label>
            </td>
            <td>
            	<span class="ui-widget-content ui-corner-right fault_input">[@service_name]</span>
            </td>
         </tr>
         <tr>
         	<td valign="middel" class="reverse_align">
            	<label class="label ui-widget-header ui-corner-left">[#date]</label>
            </td>
            <td>
            	<input type="text" class="datepicker" value="[@date]" name="date" [@date_disabled] />
            </td>
            <td valign="middel" class="reverse_align">
            	<label class="label ui-widget-header ui-corner-left">[#exam_no]</label>
            </td>
            <td>
            	<span class="ui-widget-content ui-corner-right fault_input">[@exam_no]</span>
            </td>
        </tr>
        <tr>
        	<td valign="middel" class="reverse_align">
            	[@value_td_label]
            </td>
            <td>
            	[@value_td_input]
            </td>
            <td valign="middel" class="reverse_align">
            	<label class="label ui-widget-header ui-corner-left">[#term]</label>
            </td>
            <td>
            	<span class="ui-widget-content ui-corner-right fault_input">[@term_title]</span>
            </td>
        </tr>
        <tr>
        	<td valign="middel" class="reverse_align">
            	<label class="label ui-widget-header ui-corner-left">[#max]</label>
            </td>
            <td>
            	<input type="text" class="input_half" value="[@max]" name="max" [@max_disabled] />
            </td>
            <td valign="middel" class="reverse_align">
            	<label class="label ui-widget-header ui-corner-left">[#students]</label>
            </td>
            <td>
            	<span class="ui-widget-content ui-corner-right fault_input">[@students_count]</span>
            </td>
       </tr>
       <tr>
       		<td valign="middel" class="reverse_align">
        		<label class="label ui-widget-header ui-corner-left">[#min]</label>
            </td>
            <td>
            	<input type="text" class="input_half" value="[@min]" name="min" [@min_disabled] />
            </td>
            <td valign="middel" class="reverse_align">
            	<label class="label ui-widget-header ui-corner-left">[#avrage]</label>
            </td>
            <td>
            	<span class="ui-widget-content ui-corner-right fault_input">[@avrage]</span>
            </td>
       </tr>
   </table>
</div>