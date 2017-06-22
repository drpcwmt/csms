<form id="add_others_form">
	<input name="to" value="[@to_code]" type="hidden" />
    <input name="type" value="cash" type="hidden" />
	<fieldset class="ui-state-highlight">
      <legend>[#add]</legend>
        <table width="100%" cellspacing="0" border="0">
            <tbody>
              <tr>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label></td>
                <td>
                    <input type="text" name="value" class="required"/>
                </td>
                <td valign="middel">
                	<label class="label ui-widget-header ui-corner-left reverse_align">[#currency]</label>
                </td>
                <td>
                     <select class="combobox" name="currency" style="width:75px">
                        [@currency_opts]
                    </select>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                <td valign="top">
                    <input type="text" class="datepicker mask-date" name="date" value="[@date]" />
                </td>
              </tr>
              <tr>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#code]</label></td>
                <td width="60">
                    <span class="account_code" style="width:105px;float:right">
                    <input name="from_main" style="width:40px" value="[@acc_code_main]" maxlength="5" class="main_code required"/>
                    <input style="width:40px" name="from_sub" value="[@acc_code_sub]" maxlength="5" class="sub_code required"/>
                    </span>
                </td>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#description]</label></td>
                <td ><input type="text" value="[@description]" name="from_name"  class="required input_double" main_code="4"></td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#school]</label></td>
                <td colspan="3" valign="top"><select name="ccid" class="combobox">
                  
                          	[@schools_opts]
                          
                </select></td>
              </tr>
              <tr>
                <td width="100" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#notes]</label></td>
                <td colspan="3">
                    <textarea name="notes" style="width:560px"></textarea>
                </td>
              </tr>
              <tr>
                <td valign="middel">&nbsp;</td>
                <td>&nbsp;</td>
                <td valign="middel">&nbsp;</td>
                <td><button type="button" action="submitOtherOutgoing" class="ui-corner-all ui-state-default hoverable">[#add]</button></td>
              </tr>
            </tbody>
        </table>
    </fieldset>
</form>    