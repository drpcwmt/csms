<form name="profil_form">
	<input type="hidden" name="profil_id" value="[@id]" />
    <fieldset class="ui-state-highlight">
        <legend>[#profil_fees]</legend>
          <table border="0" cellspacing="0" width="100%">
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#title]</label>
                </td>
                <td valign="top">
                  <input  name="profil_title" value="[@title]" type="text" class="input_double required" />
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#discount]</label>
                </td>
                <td valign="top">
                  <input  name="discount" value="[@discount]" type="text" class="input_half" /> % [#or] [@currency]
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#exclude_from_fees]</label>
                </td>
                <td valign="top">
                  <input  name="exclude" value="[@exclude]" type="text" class="input_half" />  [@currency]
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#bus_discount]</label>
                </td>
                <td valign="top">
                  <input  name="bus_discount" value="[@bus_discount]" type="text" class="input_half" /> % [#or] [@currency]
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#lib_discount]</label>
                </td>
                <td valign="top">
                  <input  name="lib_discount" value="[@lib_discount]" type="text" class="input_half" /> % [#or] [@currency]
                </td>
              </tr>
              <!-- <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#admission]</label>
                </td>
                <td valign="top">
                  <input  name="admission" value="[@admission]" type="text"  class="input_half" /> [@currency]
                </td>
              </tr>
               <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#book_ins]</label>
                </td>
                <td valign="top">
                  <input  name="book_ins" value="[@book_ins]" type="text"  class="input_half" /> [@currency]
                </td>
              </tr>-->
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#late_interest]</label>
                </td>
                <td valign="top">
                   <span class="buttonSet">
                      <input name="interest" id="interest_on" [@interest_on] value="1" type="radio" />
                      <label for="interest_on">[#yes]</label>
                      <input name="interest" id="interest_off" [@interest_off] value="0" type="radio" />
                      <label for="interest_off">[#no]</label>          
                    </span>
                </td>
              </tr>
          </table>
    </fieldset>
    
   <!-- <fieldset class="ui-state-highlight">
        <legend>[#payments]</legend>
         <span class="buttonSet">
          <input name="payments_type" id="payments_type_off" [@payments_type_off] value="0" type="radio"  />
          <label for="payments_type_off" action="toogleDatesTable">[#follow_level_payment]</label>
          <input name="payments_type" id="payments_type_on" [@payments_type_on] value="1" type="radio"/>
          <label for="payments_type_on"action="toogleDatesTable">[#custom]</label>          
        </span>
        [@dates_table]
    </fieldset>-->
    
    <fieldset>
        <legend>[#school_fees]</legend>
        <div class="toolbox">
            <a action="newFees" con="profil" con_id="[@id]" sms_id="[@sms_id]" std_id="[@std_id]" title="[#new]">[#new]<span class="ui-icon ui-icon-document"></span></a>
        </div>
        
        <table class="tableinput">
            <thead>
                <tr>
                    <th>[#title]</th>
                    <th width="90">[#value]</th>
                    <th width="120">[#currency]</th>
                    <th width="60">[#discountable]</th>
                    <th width="60">[#annual_increase]</th>
                    <th width="90">[#acc_code]</th>
                    <th class="unprintable" width="20">&nbsp;</th>
               </tr>
            </thead>
            <tbody>
                [@profil_fees_rows]
            </tbody>
         </table>
    </fieldset>
</form>