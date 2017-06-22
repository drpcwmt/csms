<form name="settlement-[@id]" class="settlement_form">
	<input type="hidden" value="[@id]" name="id" >
    <div class="ui-state-highlight ui-corner-all">
      <table width="100%" cellspacing="0" border="0">
        <tbody>
          <tr class="[@id_hidden]">
            <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" >[#code]</label></td>
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
                    <input type="radio" name="approve" id="exchange_approve_off-[@id]" value="0" [@approve_off] />
                    <label for="exchange_approve_off-[@id]">[#queued]</label>
                    <input type="radio" name="approve" id="exchange_approve_on-[@id]" value="[@approve]" [@approve_on] />
                    <label for="exchange_approve_on-[@id]">[#ok]</label>
                 </span>
              </td>
          </tr>
          <tr>
            <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
            <td>
                <input type="text" class="mask-date datepicker" value="[@date]" name="date" />
            </td>
            <td width="120" valign="middel">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#notes]</label></td>
            <td valign="middel" colspan="3">
            	<textarea name="notes" style="font-size:12px">[@notes]</textarea>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <table class="tableinput">
        <thead>
            <tr>
                <th width="80">[#debit]</th>
                <th width="80">[#credit]</th>
                <th width="104">[#currency]</th>
                <th width="80">[#rate]</th>
                <th width="80">[#code]</th>
                <th>[#title]</th>
            </tr>
        </thead>
        <tbody>
            [@settlement_rows]                
        </tbody>
    </table>
</form>