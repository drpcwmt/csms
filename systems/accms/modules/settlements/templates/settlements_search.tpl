<form class="ui-corner-all ui-state-highlight">
	<table cellspacing="0" border="0" width="100%">
      <tr>
        <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#begin_date]</label></td>
        <td valign="top"><input name="begin_date" type="text" class="mask-date datepicker" value="[@begin_date]" /></td>
        <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#end_date]</label></td>
        <td valign="top"><input name="end_date" type="text" class="mask-date datepicker" value="[@end_date]" /></td>
      </tr>
      <tr>
            <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#account]</label></td>
            <td><span class="account_code">
                    <input name="main_code" style="width:40px" maxlength="5" class="main_code "/>
                    <input style="width:40px" name="sub_code" maxlength="5" class="sub_code "/>
              </span></td>
             <td colspan="2">
               <input class="input_double title" name="title" style="margin:0;border: 1px #dddddd solid; border-radius: 0px" />
            </td>

          </tr>
                <tr>
            <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#cost_center]</label></td>
            <td valign="top" colspan="3">
            	<select class="combobox" name="cc" >
                	[@ccs_opts]
                </select>
            </td>
          </tr>

                <tr>
            <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#total]</label></td>
            <td valign="top" colspan="3">
            	<select name="pram_select" style="width:50px">
                    <option value="=">=</option>
                    <option value="&lt;">&lt;</option>
                    <option value="&gt;">&gt;</option>
                    <option value="&lt;=">&lt;=</option>
                    <option value="&gt;=">&gt;=</option>
               </select>
               <input type="text" name="total" />
            </td>
          </tr>
          <tr>
            <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#currency]</label></td>
            <td colspan="3" valign="top">
           	  <select class="combobox" name="currency" >
                	[@currency_opts]
                </select>
            </td>
          </tr>

          <tr>
            <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#user]</label></td>
            <td colspan="3" valign="top">
            	<select class="combobox" name="user_id" >
                	[@users_opts]
                </select>
            </td>
          </tr>

          <tr>
            <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#status]</label></td>
            <td colspan="3" valign="top">
            	<select class="combobox" name="approved" >
                	<option value=" ">[#all]</option>
                    <option value="1">[#done]</option>
                    <option value="0">[#queued]</option>
                </select>
            </td>
          </tr>
    </table>
</form>