<form class="ui-state-highlight" >
   <input type="hidden" name="act_id" value="[@act_id]" />
   <input type="hidden" name="std_id" value="[@std_id]" />
   <input type="hidden" name="cc_id" value="[@cc_id]" />
    <table border="0" cellspacing="0">
        <tbody>
          <tr>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label></td>
            <td valign="top">
            	<input class="half_input" name="value" type="text" />
             </td>
          </tr>
          <tr>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#currency]</label></td>
            <td valign="top">
              <select name="currency" class="combobox">
                [@curs_opts]
              </select>
             </td>
          </tr>
          <tr>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
            <td valign="top">
            	<input class="mask-date datepicker" name="date" type="text" value="[@date]" />
             </td>
          </tr>
		</tbody>
	</table>    
</form>   