<fieldset >
    <table border="0" cellspacing="0" width="100%">
      <tr>
        <td width="180" valign="middel" class="reverse_align">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#admmission_fees]</label>
        </td>
        <td valign="top">
           <input type="text" value="[@def_admission]" name="def_admission" class="input_half" /> EGP 
        </td>
      </tr>  
      <tr>
        <td width="180" valign="middel" class="reverse_align">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#book_insurrance]</label>
        </td>
        <td valign="top">
           <input type="text" value="[@book_insurrance]" name="book_insurrance" /> 
        </td>
      </tr>  
      <tr>
        <td width="180" valign="middel" class="reverse_align">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#locker_rent]</label>
        </td>
        <td valign="top">
           <input type="text" value="[@locker_rent]" name="locker_rent" /> 
        </td>
      </tr>  

      <tr>
        <td width="180" valign="middel" class="reverse_align">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#fees_annual_increasment]</label>
        </td>
        <td valign="top">
           <input type="text" value="[@fees_annual_increasment]" name="fees_annual_increasment" class="input_half" /> % 
        </td>
      </tr>  
      <tr>
        <td width="180" valign="middel" class="reverse_align">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#fees_annual_nearset]</label>
        </td>
        <td valign="top">
           <input type="text" value="[@fees_annual_nearset]" name="fees_annual_nearset" /> 
        </td>
      </tr>  
	</table>
<fieldset>
	<legend>[#Payments_dates]</legend>
    <div class="toolbox">
    	<a action="LoadDates" con="" con_id="0" sms_id="" module="fees"><span class="ui-icon ui-icon-pencil"></span>[#edit]</a>
    </div>
    [@dates_table]
</fieldset>