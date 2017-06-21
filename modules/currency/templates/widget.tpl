<!--<span class="usdegp"><img src="assets/img/mini_loading.gif" height="24" />-->
<fieldset id="currency_widget">
	<legend>[#currency]</legend>
    <h5 class="last_sync">[@last_sync]</h6>
    <h3 style="direction:ltr">1 USD = <span class="usdegp">[@usd_to_egp]</span> EGP</h3>
    <h3 style="direction:ltr">1 EUR = <span class="euregp">[@eur_to_egp] EGP</span></h3>
    <table >
    	<tr>
        	<td class="reverse_align" valign="middel" width="80"><label class="label ui-widget-header ui-corner-left reverse_align">[#value]</label></td>
            <td valign="top">
                <input type="text" name="currency_value" id="widget_currency_value" />
             </td>
             <td class="reverse_align" valign="middel" width="80"><label class="label ui-widget-header ui-corner-left reverse_align">[#result]</label></td>
            <td valign="top">
               <div class="fault_input" id="convert_result"></div>
             </td>
            
        </tr>
        <tr>
        	<td>
            	<label class="label ui-widget-header ui-corner-left reverse_align">[#from]</label>
            </td>
            <td>
            	<select name="from" class="combobox" id="widget_currency_from">
                    [@currency_opts]
                </select>
            </td>
        	<td width="80">
            	<label class="label ui-widget-header ui-corner-left reverse_align">[#to]</label>
            </td>
            <td>
            	<select name="to" class="combobox" id="widget_currency_to">
                    [@currency_opts]
                </select>
            </td>
		</tr>
        <tr>
         <td colspan="4" align="center">
            <button type="button" module="currency" action="convertCurrency" class="ui-corner-all ui-state-default hoverable">[#convert]</button>
         </td>
    </table>
</fieldset>