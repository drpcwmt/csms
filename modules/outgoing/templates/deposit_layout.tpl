<h2>[#deposit]</h2>
<form id="newDeposit">
    <fieldset class="ui-state-highlight">
        <input type="hidden" name="to" value="[@to_code]" />
        <table border="0" cellspacing="0">
            <tbody>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                <td valign="top">
                  <input type="text" name="date" value="[@today]" class="mask-date datepicker" />
                  </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                <td valign="top">
                  <select name="from" class="combobox">
                    [@from_opts]
                  </select>
                  </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label></td>
                <td valign="top">
                  <input type="text" name="value" class="required" />
                  <select name="currency" class="combobox">
                    [@currency_opts]
                  </select>
                 </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#notes]</label></td>
                <td valign="top">
                  <textarea name="notes"></textarea>
                 </td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                    <button type="button" action="submitDeposit"  class="ui-corner-all ui-state-default hoverable">[#add]</button>
                </td>
              </tr>
            </tbody>
        </table>
    </fieldset>
</form>                          