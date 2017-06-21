<div class="tabs">
	<ul>
    	<li><a href="#acc_[@full_code]-details">[#details]</a></li>
        <li><a href="#acc_[@full_code]-transactions">[#transactions]</a></li>
        [@extra_tab_li]
        <!--<li><a href="index.php?module=settlements&search&main_code=[@main_code]&sub_code=[@sub_code]&">[#transactions]</a></li>-->
    </ul>
    <div id="acc_[@full_code]-details">
    	<form class="account_infos">
        	<div class="toolbox">
            	<a module="accounts" action="saveMainCode" title="[#save]" class="[@main_code_only]">[#save]<span class="ui-icon ui-icon-disk"></span></a>
                <a action="print_tab" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
            </div>
        	<input type="hidden" name="code" value="[@full_code]"/>
            <fieldset class="ui-state-highlight">
                <legend>[@title]</legend>
                <table width="100%" cellspacing="0" border="0">
                  <tbody>
                    <tr>
                      <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#code]</label></td>
                      <td width="150"><span class="account_code" style="width:105px;float:right">
                        <input name="main_code" style="width:40px" value="[@main_code]" maxlength="5" class="main_code" />
                        <input style="width:40px" name="sub_code" value="[@sub_code]" maxlength="[@max_length]" class="sub_code required [@hideSubCode]"/>
                      </span></td>
                      <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#title]</label></td>
                      <td ><input type="text" value="[@title]" name="title" id="title"  class="required input_double"></td>
                    </tr>
                    <tr>
                      <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#currency]</label></td>
                      <td width="60">
                      	<span class="currency_combobox">
                            <select class="combobox" name="currency" >
                                [@currency_opts]
                            </select>
						</span>
                      </td>
                      <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#cost_center]</label></td>
                      <td >
                        <select class="combobox" name="group_id" >
                            [@ccs_opts]
                        </select>                     
                      </td>
                    </tr>
                    <tr>
                      <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#damages]</label></td>
                      <td width="60" colspan="3">
                      	<input type="text" name="damage" value="[@damage]" class="input_half" /> %
                      </td>
                    </tr>
                    <tr>
                      <td valign="top">
                        <label class="label ui-widget-header ui-corner-left reverse_align" >[#notes]</label>
                      </td>
                      <td colspan="3">
                        <textarea name="notes">[@notes]</textarea>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </fieldset>
        </form>
        <fieldset>
        	<legend>[#total_transactions]</legend>
            [@account_balance]
        </fieldset>
    </div>
     <div id="acc_[@full_code]-transactions">
     	[@transactions_table]
     </div>
     [@extra_tab_div]
</div>
