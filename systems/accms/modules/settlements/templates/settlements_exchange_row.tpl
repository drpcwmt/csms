<tr>
	<td>
    	<input class="input_half debit" type="text" name="debit[]" value="[@debit]"/>
    </td>
    <td>
    	<input class="input_half credit" type="text" name="credit[]" value="[@credit]"/>
    <td>
       <select class="combobox" name="currency[]"  update="showRate" >
            [@currency_options]
        </select>
    </td>
    <td>
        <input type="text" class="half_input rate" name="rate[]" value="[@rate]" />
    </td>
     <td>
     	<span class="account_code">
    		<input name="acc_code_main[]" style="width:40px" value="[@main_code]" maxlength="5" class="main_code required"/>
            <input style="width:40px" name="acc_code_sub[]" value="[@sub_code]" maxlength="5" class="sub_code required"/>
        </span>
    </td>
     <td>
   	   <input type="text" class="input_double acc_title" name="title[]" value="[@title]"/>
    </td>
</tr>  