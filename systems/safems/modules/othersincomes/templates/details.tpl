<form id="other_incomes_form-[@id]">
    <input type="hidden" name="id" value="[@id]" />
	<div class="toolbox ">
    	<a action="saveActivity" act_id="[@act_id]" class="[@hidden]">[#save]<span class="ui-icon ui-icon-disk"></span></a>
        <a action="print_tab" >[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>
    <fieldset class="ui-state-highlight ui-corner-all">
        <table width="100%" cellspacing="0" border="0">
            <tr>
              <td  valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#title]</label></td>
              <td colspan="3" ><input type="text" value="[@title]" name="title" id="title"  class="required input_double"></td>
            </tr>
            <tr>
              <td  valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#type]</label></td>
              <td colspan="3" ><select name="parent" class="combobox">[@type_opts]</select></td>
            </tr>
            <tr>
              <td  valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#cost_center]</label></td>
              <td colspan="3" ><select name="cc" class="combobox required">[@cc_opts]</select></td>
            </tr>
            <tr>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#expenses_account]</label></td>
                <td width="60">
                    <span class="account_code" style="width:105px;float:right">
                    <input name="expenses_code_main" style="width:40px" value="[@expenses_code_main]" maxlength="5" class="main_code"/>
                    <input style="width:40px" name="expenses_code_sub" value="[@expenses_code_sub]" maxlength="5" class="sub_code"/>
                    </span>
                </td>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#description]</label></td>
                <td ><input type="text" value="[@expenses_name]" name="expenses_name"  class=" input_double acc_title"></td>
            </tr>  
            <tr>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#incomes_account]</label></td>
                <td width="60">
                    <span class="account_code" style="width:105px;float:right">
                    <input name="incomes_code_main" style="width:40px" value="[@incomes_code_main]" maxlength="5" class="main_code"/>
                    <input style="width:40px" name="incomes_code_sub" value="[@incomes_code_sub]" maxlength="5" class="sub_code"/>
                    </span>
                </td>
                <td width="100" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#description]</label></td>
                <td ><input type="text" value="[@incomes_name]" name="incomes_name"  class=" input_double acc_title"></td>
            </tr>  
        </table>
    </fieldset>
    [@prices_table]
</form>