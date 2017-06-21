<form id="newMemberForm">
   <input type="hidden" name="act_id" value="[@act_id]" />
    <table border="0" cellspacing="0">
        <tbody>
          <tr>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#school]</label></td>
            <td valign="top">
              <select name="cc_id" class="combobox" update="changeMemberCC">
                [@schools_opts]
              </select>
             </td>
          </tr>
          <tr>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
            <td valign="top">
              <input type="text" name="name"  class="input_double required" id="student_name" sms_id="[@sms_id]"/>
              <input name="std_id" class="autocomplete_value required" type="hidden">
             </td>
          </tr>
		</tbody>
	</table>    
</form>   