<h2 class="title">[#admission]</h2>
<div class="tabs">
	<ul>
    	<li><a href="#newIncome">[#add]</a></li>
        <li><a href="index.php?module=ingoing&incomes&list&type=[@type]">[#reports]</a></li>
    </ul>
    <div id="newIncome">
        <form id="newIncome">
            <fieldset class="ui-state-highlight">
                <input type="hidden" name="currency" value="[@def_currency]" />
                <input type="hidden" name="notes" value="[@notes]" />
                <table border="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#year]</label></td>
                        <td valign="top" width="300">
                          <select name="to">
                          	[@to_opts]
                           </select>
                         </td>
                        <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                        <td valign="top"><input type="text" name="date" value="[@cur_date]" class="datepicker mask-date"/></td>
                      </tr>
                      <tr>
                        <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#school]</label></td>
                        <td colspan="3" valign="top">
                          <select name="ccid" class="combobox" update="changeIncomeCC">
                          	[@schools_opts]
                          </select>
                         </td>
                      </tr>
                      <tr>
                        <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                        <td colspan="3" valign="top">
                          <input class="input_double required" name="name" type="text">
				<input name="from_sub" class="autocomplete_value" type="hidden" value="">
                <input name="from_main" type="hidden" value="[@from_main]">
                         </td>
                      </tr>
                      <tr>
                        <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label></td>
                        <td colspan="3" valign="top">
                          <input type="text" name="value" class="required" /> [@def_currency]
                         </td>
                      </tr>
                      <tr>
                        <td class="reverse_align" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#payment_type]</label></td>
                        <td colspan="3" valign="top">
                            <select name="type" class="combobox required" update="toogleBanksOpts">
                              <option value="cash">[#cash]</option>
                              <option value="visa">[#visa]</option>
                            </select>
                            <span class="banks_opts hidden">
                              <select name="bank" class="combobox">
                                        [@banks_opts]
                              </select>
                            </span>
                        </td>
                      </tr>
                     
                      <tr>
                      	<td colspan="4" align="center">
                        	<button type="button" action="submitIncomes"  class="ui-corner-all ui-state-default hoverable">[#add]</button>
						</td>
                      </tr>
                    </tbody>
                </table>
			</fieldset>
		</form>                          
    </div>
</div>