<tr>
    <td style="vertical-align:top" class="unprintable">
    	<button class="ui-state-default hoverable circle_button" type="button" title="[#delete]" action="removeTrans"><span class="ui-icon ui-icon-trash"></span></button>
    </td>
	<td>
    	<input class="input_half debit" name="debit[]" value="[@debit]"/>
    </td>
    <td>
    	<input class="input_half credit" name="credit[]" value="[@credit]"/>
    </td>
     <td>
     	<span class="account_code">
    		<input name="acc_code_main[]" style="width:40px" value="[@main_code]" maxlength="5" class="main_code required"/>
            <input style="width:40px" name="acc_code_sub[]" value="[@sub_code]" maxlength="5" class="sub_code required"/>
        </span>
    </td>
    <td>
        <select style="width:80px" name="acc_code_cc[]"  class="cc">
            [@cc_opts]
        </select>
    </td>
     <td>
   	   <input class="input_double acc_title" name="title[]" value="[@title]"/>
    </td>
    <td>
    	<textarea name="notes[]" style="font-size:12px">[@notes]</textarea>
    </td>
</tr>  