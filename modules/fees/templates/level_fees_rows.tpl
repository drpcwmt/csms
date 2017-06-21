<tr>
	<input type="hidden" name="id[]" value="[@id]" />
	<td><input type="text" name="title[]" value="[@title]" class="input_double" /></td>
    <td align="center"><input type="text" name="value[]" value="[@value]" class="input_half"/></td>
    <td align="center">
    	<select class="combobox" name="currency[]" style="width:75">
            [@currency_opts]
        </select>
    </td>
    <td align="center">
        <span class="ui-icon ui-icon-check [@disc_hidden]"></span>
	</td>
    <td align="center">
        <span class="ui-icon ui-icon-check [@annual_hidden]"></span>
	</td>
    <td align="center">
      <span class="account_code" style="float:right">
        <input name="main_code[]" style="width:40px" value="[@main_code]" maxlength="5" class="main_code" />
        <input style="width:40px" name="sub_code[]" value="[@sub_code]" maxlength="5" class="sub_code required"/>
      </span>
    </td>
    <td align="center">
    	<button class="ui-state-default hoverable circle_button" type="button" action="deleteFees" sms_id="[@sms_id]" fees_id="[@id]" ><span class="ui-icon ui-icon-close"></span></button>
    </td>
</tr>