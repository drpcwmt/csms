<form id="new_account_form">
	<input type="hidden" name="level" value="[@level]" />
    <input type="hidden" name="precode" value="[@acc_code_main]" />
    <fieldset class="ui-state-highlight ui-corner-all">
        <table width="100%" cellspacing="0" border="0">
            <tr>
              <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#code]</label></td>
              <td><span class="account_code" style="width:105px;float:right">
                <input name="acc_code_main" style="width:40px" value="[@acc_code_main]" maxlength="5" class="main_code" disabled/>
                <input style="width:40px" name="acc_code_sub" value="[@value]" maxlength="[@max_length]" class="sub_code required"/>
              </span></td>
              <td width="200" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#description]</label></td>
              <td ><input type="text" value="[@description]" name="title" id="title"  class="required input_double"></td>
            </tr>
            <tr class="[@currency_combobox]">
              <td width="120" valign="top" >
                <label class="label ui-widget-header ui-corner-left reverse_align" >[#currency]</label>
              </td>
              <td colspan="3" valign="top">
                <select name="currency" class="combobox">
                    [@currency_opts]
                </select>          
              </td>
            </tr>
            <tr>
              <td valign="top">
                <label class="label ui-widget-header ui-corner-left reverse_align" >[#notes]</label>
              </td>
              <td colspan="3">
                <textarea name="notes"></textarea>
              </td>
            </tr>
        </table>
    </fieldset>
    <table width="100%" cellspacing="0" border="0">
        <tr>
          <td valign="top">
          	<fieldset class="[@start_bal_hidden]">
            	<legend>[#start_balance]</legend>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td valign="top" >
                        <label class="label ui-widget-header ui-corner-left reverse_align" >[#type]</label>
                      </td>
                      <td valign="top">
                        <span class="buttonSet">
                            <input type="radio" value="credit" name="type" id="credit_radio" checked="checked" />
                            <label for="credit_radio">[#credit]</label>
                            <input type="radio" value="debit" name="type" id="debit_radio"/>
                            <label for="debit_radio">[#debit]</label>
                        </span>
                        
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" >
                        <label class="label ui-widget-header ui-corner-left reverse_align" >[#value]</label>
                      </td>
                      <td valign="top">
                      	 <input name="value" type="text" value="0" class="input_half"/>
                      </td>
                   </tr>
                </table>
            </fieldset>
            <fieldset class="[@damage_hidden]">
            	<legend>[#damages]</legend>
                <label><input type="checkbox" value="1" name="damage_acc" />[#create_damage_acc]</label>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                      <td valign="top" width="120" >
                        <label class="label ui-widget-header ui-corner-left reverse_align" >[#damage_percent]</label>
                      </td>
                      <td valign="top">
                        <input name="damages" style="width:40px" type="text" value="0" maxlength="3"/> %
                      </td>
                </tr>
                <tr>
                  <td valign="top" >
                    <label class="label ui-widget-header ui-corner-left reverse_align" >[#start_balance]</label></td>
                   <td valign="top"><input type="text" name="damage_total" class="input_half" value="0"/></td>
                </tr>
              </table>
            </fieldset>
              </td>
              <td valign="top">
                <fieldset class="[@cc_hidden]">
                    <legend>[#cost_center]</legend>
                    <table border="0" cellspacing="1">
                       <tr>
                          <td valign="top"  width="120">
                    			<label class="label ui-widget-header ui-corner-left reverse_align" >[#cost_center]</label>
                           </td>
                   			<td valign="top">
                            	<select name="group_id" class="combobox">
                                	[@ccs_opts]
                                </select>
                            </td>
                        </tr>
                      </table>
                </fieldset>
              </td>
            </tr>
          </table>
          </td>
        </tr>
    </table>
  </fieldset>
</form>