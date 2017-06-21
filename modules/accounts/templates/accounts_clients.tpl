<form id="clients_account_form">
	<fieldset>
        <legend>[#search]</legend>
        <table width="100%" cellspacing="0" border="0">
            <tbody>
              <tr>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#code]</label></td>
                <td width="60">
                    <span class="account_code" style="width:105px;float:right">
                    <input name="acc_code_main" style="width:40px" value="[@acc_code_main]" maxlength="5" class="main_code required"/>
                    <input style="width:40px" name="acc_code_sub" value="[@acc_code_sub]" maxlength="5" class="sub_code required"/>
                    <input style="width:10px" name="acc_code_cc" value="[@acc_code_cc]" maxlength="1" class="cc required"/>
                    </span>
                </td>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                <td ><input type="text" value="[@description]" name="title" id="title"  class="required input_double"></td>
              </tr>
              <tr>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#cost_center]</label></td>
                <td width="60" colspan="3">
                    <select name="cc">
                    	[@cc_opts]
                    </select>
                </td>
              </tr>
              <tr>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#begin_date]</label></td>
                <td>
                    <input type="text" name="begin_date" value="[@begin_date]" class="datepicker mask-date"/>
                </td>
                <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#end_date]</label></td>
                <td><input type="text" name="end_date" value="[@end_date]" class="datepicker mask-date"/></td>
              </tr>
              <tr>
                <td valign="middel">&nbsp;</td>
                <td>&nbsp;</td>
                <td valign="middel">&nbsp;</td>
                <td><button type="button" action="submitSearchAccount" class="ui-corner-all ui-state-default hoverable">[#search]</button></td>
              </tr>
            </tbody>
        </table>
    </fieldset>
</form>    