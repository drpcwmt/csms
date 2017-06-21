<form class="student_search">
    <fieldset class="ui-state-highlight">
        <input type="hidden" name="ccid" value="[@ccid]" />
        <table border="0" cellspacing="0">
            <tbody>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#year]</label></td>
                <td valign="top">
                    <select name="year" class="combobox">
                        [@year_opts]
                    </select>
                 </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                <td valign="top">
                  <input class="input_double required" name="name" id="ingoing_student_name" sms_id="[@sms_id]" type="text">
                  <input name="std_id" class="autocomplete_value required" type="hidden" />
                  <button type="button" action="submitSearchStudentFees" sms_id="[@sms_id]" class="ui-corner-all ui-state-default hoverable">[#search]</button>
                  </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label></td>
                <td valign="top">
                  <input type="text" name="value" class="required" autocomplete="off" />
                 </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                <td valign="top">
                  <input type="text" name="date" class="required mask-date datepicker" value="[@today]" />
                 </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#currency]</label></td>
                <td valign="top">
                    <select name="currency" class="combobox required">
                        [@currency_opts]
                    </select>
                 </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#payment_type]</label></td>
                <td valign="top">
                    <select name="type" class="combobox required" update="toogleBanksOpts" module="ingoing">
                        <option value="cash">[#cash]</option>
                        <option value="visa">[#visa]</option>
                        <option value="transfer">[#bank_transfer]</option>
                        <option value="deposit">[#bank_deposit]</option>
                    </select>
                    
                    
                 </td>
              </tr>
              <tr class="hidden banks_opts">
                <td class="reverse_align" valign="middel" width="120">&nbsp;</td>
                <td valign="top">
                    
                    <select name="bank" class="combobox">
                        <option value="[@this_code]"></option>
                        [@banks_opts]
                    </select>
                 </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#incomes]</label></td>
                <td valign="top">
                    <select name="rel" class="combobox">
                    </select>
                 </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#payments]</label></td>
                <td valign="top">
                    <select name="dates" class="combobox">
                    </select>
                 </td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                    <button type="button" action="submitNewPayment" sms_id="[@sms_id]" class="ui-corner-all ui-state-default hoverable">[#add]</button>
                </td>
              </tr>
            </tbody>
        </table>
    </fieldset>

</form>
