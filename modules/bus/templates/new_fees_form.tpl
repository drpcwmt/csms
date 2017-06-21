<form>
    <input type="hidden" name="route_group_id" value="[@group_id]" />
    <fieldset class="ui-state-highlight">
        <table border="0" cellspacing="0">
              <tr>
                <td class="reverse_align" valign="middel" width="100">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#title]</label>
                </td>
                <td valign="top" colspan="3">
                  <input class="input_double required" name="title" type="text">
                </td>
              </tr>
              <tr>
               <td class="reverse_align" valign="middel" width="100">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label>
                </td>
                <td valign="top" colspan="3">
                  <input name="value" type="text" class="required">
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="100">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#acc_code]</label>
                </td>
                <td valign="top">
                  <span class="account_code" style="float:right">
                    <input name="main_code" style="width:40px" value="[@main_code]" maxlength="5" class="main_code required" />
                    <input style="width:40px" name="sub_code" value="[@sub_code]" maxlength="5" class="sub_code required"/>
                  </span>
                </td>
                <td class="reverse_align" valign="middel" width="100">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#type]</label>
                </td>
                <td valign="top">
                	<span class="buttonSet">
                      <input name="type" id="debit" checked value="debit" type="radio" >
                      <label for="debit">[#debit]</label>
                      <input name="type" id="credit" value="credit" type="radio" >
                      <label for="credit">[#credit]</label>          
                    </span>
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="100">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#currency]</label>
                </td>
                <td valign="top">
                     <select class="combobox" name="currency" style="width:75px">
                        [@currency_opts]
                    </select>
                </td>
                <td class="reverse_align" valign="middel" width="100">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#discountable]</label>
                </td>
                <td valign="top">
                   <span class="buttonSet">
                      <input name="discount" id="discount_on" checked value="1" type="radio" />
                      <label for="discount_on">[#yes]</label>
                      <input name="discount" id="discount_off" value="0" type="radio" />
                      <label for="discount_off">[#no]</label>          
                    </span>
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="100">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#annual_increase]</label>
                </td>
                <td valign="top" colspan="3">
                    <span class="buttonSet">
                        <input name="increase" id="increase_on" checked  value="1" type="radio" />
                        <label for="increase_on">[#yes]</label> 
                        <input name="increase" id="increase_off"  value="0" type="radio" />
                      	<label for="increase_off">[#no]</label>          
                    </span>
                </td>
                <!--<td class="reverse_align" valign="middel" width="100">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#late_interest]</label>
                </td>
                <td valign="top">
                      <input type="text" class="input_half" name="interest" maxlength="2" value="0" style="width:30px" /> % /  <input type="text" class="input_half" name="interest_period" value="1" style="width:30px"/> [#days]
                </td>-->
              </tr>
        </table>
    </fieldset>
    
    <fieldset>
    	<legend>[#payments]</legend>
        <table class="tableinput">
            <thead>
                <tr>[@payments_ths]</tr>
           	</thead>
            <tbody>
            	<tr>[@payments_tds]</tr>
            </tbody>
    	</table>
    </fieldset>
    
    <div class="toolbox">
        <a action="applyBusFeesToall" title="[#apply_to_all]">[#apply_to_all]<span class="ui-icon ui-icon-disk"></span></a>
    </div>
    
    <table class="tableinput">
    	<thead>
        	<tr>
            	<th>[#group]</th>
                <th width="150">[#value]</th>
            </tr>
        </thead>
        <tbody>
        	[@groups_values_trs]
        </tbody>
    </table>
    
</form>

