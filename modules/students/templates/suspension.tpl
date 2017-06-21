<div id="temp_suspension" class="ui-state-highlight">
    <form name="temp_suspension" class="ui-corner-all ui-state-highligh">
      <input type="hidden" name="id" value="[@std_id]" />
        <table width="100%" cellspacing="1" cellpadding="0" border="0">
          <tbody>
            <tr>
              <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#to]</label></td>
              <td valign="top" colspan="2" class="def_align">
                <select name="status" class="combobox">
                    <option value="0">[#final_suspension]</option>
                    <option value="2">[#waiting_list]</option>
                    <option value="3">[#reservations]</option>
                    <option value="4">[#request]</option>
                    <option value="5">[#graduated]</option>
                </select>
              </td>
            </tr>
            <tr>
              <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
              <td valign="top" colspan="2" class="def_align">
                <input type="text" class="ui-state-default ui-corner-right mask-date" name="quit_date" value="[@date]">
              </td>
            </tr>
           <tr>
              <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#reason]</label></td>
              <td valign="top" colspan="2" class="def_align">
                <input name="suspension_reason" class="input_double suspension_reason" type="text" />
              </td>
            </tr>
          </tbody>
        </table>
  </form>
</div>
