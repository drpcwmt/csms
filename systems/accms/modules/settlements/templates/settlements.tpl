<form name="settlement-[@id]" class="settlement_form">
	<input type="hidden" value="[@id]" name="id" >
    <div class="ui-state-highlight ui-corner-all">
      <table width="100%" cellspacing="0" border="0">
        <tbody>
          <tr class="[@id_hidden]">
            <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#reg_no]</label></td>
            <td colspan="3">
            	<div class="fault_input">[@id]</div>
            </td>
          <tr>
            <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#user]</label></td>
            <td width="300">
                <div class="fault_input">[@user_name]</div>
                <input type="hidden" name="user_id" value="[@user_id]"/>    
            </td>
            <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align unprintable">[#status]</label></td>
            <td >
            	<span class="buttonSet unprintable">
                    <input type="radio" name="approve" id="approve_off-[@id]" value="0" [@approve_off] />
                    <label for="approve_off-[@id]">[#queued]</label>
                    <input type="radio" name="approve" id="approve_on-[@id]" value="[@approve]" [@approve_on] />
                    <label for="approve_on-[@id]">[#ok]</label>
                 </span>
              </td>
          </tr>
          <tr>
            <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
            <td>
                <input type="text" class="mask-date datepicker" value="[@date]" name="date" />
            </td>
            <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#currency]</label></td>
            <td>
            	<span class="currency_combobox">
                    <select class="combobox" name="currency" update="showRate">
                        [@currency_options]
                    </select>
                    <input type="text" class="input_half required [@rate_hidden]" value="[@rate]" name="rate" />
                </span>
            </td>
          </tr>
          <tr>
            <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#total]</label></td>
            <td colspan="3">
                <div class="fault_input">
                	[@total_trans]
                </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <table class="tableinput">
        <thead>
            <tr>
                <th width="20" class="unprintable">&nbsp;</th>
                <th width="80">[#debit]</th>
                <th width="80">[#credit]</th>
                <th width="80">[#code]</th>
                <th width="72">[#cost_center]</th>
                <th>[#description]</th>
                <th>[#notes]</th>
            </tr>
        </thead>
        <tbody>
            [@settlement_rows]                
        </tbody>
    </table>
</form>