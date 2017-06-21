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
                <label class="label ui-widget-header ui-corner-left reverse_align">[#paid]</label>
            </td>
            <td valign="top">
               <div class="fault_input">[@paid] EGP</div>
            </td>
      </tr>  
    	<tr>
            <td class="reverse_align" valign="middel" width="120">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label>
            </td>
            <td valign="top">
               <input type="text" name="value" /> 
            </td>
      </tr>  
	 </table>
</form>     