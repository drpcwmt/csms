<form class="ui-state-highlight">
	<input type="hidden" name="split_id" value="[@split_id]" />
	<input type="hidden" name="sms_id" value="[@sms_id]" />
    <input type="hidden" name="currency" value="[@currency]" />
    <table border="0" cellspacing="0" width="100%">
    	<tr>
            <td class="reverse_align" valign="middel" width="120">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#material]</label>
            </td>
            <td valign="top">
               <div class="fault_input">
               	[@service_name]
               </div>
            </td>
        </tr>  

    	<tr>
            <td class="reverse_align" valign="middel" width="120">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#fees]</label>
            </td>
            <td valign="top">
               <div class="fault_input">[@fees] EGP</div>
            </td>
        </tr>  
    	<tr>
            <td class="reverse_align" valign="middel" width="120">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label>
            </td>
            <td valign="top">
               <input type="text" name="paid" /> 
            </td>
        </tr>  
    	<tr>
            <td class="reverse_align" valign="middel" width="120">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label>
            </td>
            <td valign="top">
               <input type="text" name="date" class="datepicker mask-date" />
            </td>
        </tr>  
      </tr>
          <tr>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#payment_type]</label></td>
            <td valign="top">
                <select name="payment_mode" class="combobox required" update="toogleBanksOpts" module="ingoing">
                    <option value="cash">[#cash]</option>
                    <option value="visa">[#visa]</option>
                </select>
                
                
             </td>
          </tr>
          <tr class="hidden banks_opts">
            <td class="reverse_align" valign="middel" width="120">&nbsp;</td>
            <td valign="top">
                
                <select name="bank" class="combobox">
                    <option value="[@this_code]"></option>
                    [@banks_opts]
                </select>
             </td>
          </tr>
	 </table>
</form>     