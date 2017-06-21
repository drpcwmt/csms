<form name="user_form" class="ui-state-highlight ui-corner-all" style="padding:5px">
  <table cellspacing="0" border="0">
    <tbody>
      <tr>
        <td width="120" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">Group</label></td>
        <td valign="top">
            <select update="setAutocompFieldType" id="group" class="required combobox" name="group" [@disable_for_update]>[@group_select_val]
          </select>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel" class="reverse_align">
            <label class="label ui-widget-header ui-corner-left">[#name]</label></td>
            <td valign="top">
                <input name="user_name" [@disable_for_update] type="text" class="input_double required ui-state-default ui-corner-right" id="user_name" value="[@user_reel_name]"  />
                <input type="hidden" class="autocomplete_value ui-state-default ui-corner-right" id="user_id" name="user_id" value="[@user_id]" />
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="120" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#login_name]</label></td>
        <td valign="top"><input name="name" type="text" class="required ui-state-default ui-corner-right" id="name" value="[@name]" >
          <span onClick="generateUsername()" class="mini_link unprintable [@hide_for_update]">Generate</span></td>
      </tr>
      <tr>
        <td width="120" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#password]</label></td>
        <td valign="top"><input name="password" type="[@password_type]" class="required ui-state-default ui-corner-right" id="password" value="[@password]">
          <span onClick="generatePassword()" class="mini_link unprintable" >Generate</span> <span style="padding:3px 10px; font-weight:bold; margin:0px 15px" id="generatedPass"></span></td>
      </tr>
      <tr class="unprintable">
        <td width="120" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#cfm_password]</label></td>
        <td valign="top"><input type="password" class="required ui-state-default ui-corner-right" id="password2" name="password2"></td>
      </tr>
      <tr class="unprintable">
        <td width="120" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#language]</label></td>
        <td valign="top"><select class="required combobox" id="def_lang" name="def_lang">
            [@ui_lang_select]
          </select>
        </td>
      </tr>
    </tbody>
  </table>
</form>
[@docs_div]
[@last_login]
