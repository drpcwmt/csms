<form id="search_account_form">
	<fieldset>
      <legend>[#search]</legend>
        <table width="100%" cellspacing="0" border="0">
            <tbody>
              <tr>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#code]</label></td>
                <td>
                    <span class="account_code" style="width:105px;float:right">
                    <input name="acc_code_main" style="width:40px" value="[@acc_code_main]" maxlength="5" class="main_code required"/>
                    <input style="width:40px" name="acc_code_sub" value="[@acc_code_sub]" maxlength="5" class="sub_code required"/>
                    </span>
                </td>
              </tr>
              <tr>
                <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#description]</label></td>
                <td ><input type="text" value="[@description]" name="title" id="title"  class="input_double" /></td>
              </tr>
            </tbody>
        </table>
    </fieldset>
</form>    