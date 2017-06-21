<fieldset >
    <table border="0" cellspacing="0" width="100%">
          <tr>
            <td width="180" valign="middel" class="reverse_align">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#this_main_code]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@this_acc_code]" name="this_acc_code" /> 
            </td>
          </tr>  
          <tr>
            <td class="reverse_align" valign="middel">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#admission_acc]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@admission_acc]" name="admission_acc" /> 
            </td>
      </tr>  
      <tr>
            <td width="180" valign="middel" class="reverse_align">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#admission_acc_adv]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@admission_acc_adv]" name="admission_acc_adv" /> 
            </td>
      </tr>  
      <tr>
            <td class="reverse_align" valign="middel">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#income_book_acc]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@income_book_acc]" name="income_book_acc" /> 
            </td>
      </tr>  
      <tr>
            <td class="reverse_align" valign="middel">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_book_acc]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@insur_book_acc]" name="insur_book_acc" /> 
            </td>
      </tr>  
      <tr>
        <td class="reverse_align" valign="middel">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_locker_acc]</label>
        </td>
        <td valign="top">
           <input type="text" value="[@insur_locker_acc]" name="insur_locker_acc" /> 
        </td>
      </tr>
      <tr>
        <td class="reverse_align" valign="middel">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_locker_acc]</label>
        </td>
        <td valign="top">
           <input type="text" value="[@office_fees_refund_acc]" name="office_fees_refund_acc" /> 
        </td>
      </tr>  
      <tr>
        <td class="reverse_align" valign="middel">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#default_cost_center]</label>
        </td>
        <td valign="top">
          <select name="cc_group_id" class="combobox" >
          	[@ccs_opts]
          </select>
        </td>
      </tr>  
      <tr>
        <td class="reverse_align" valign="middel">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#default_bank]</label>
        </td>
        <td valign="top">
          <select name="def_bank" class="combobox" >
          	[@banks_opts]
          </select>
        </td>
      </tr>  
      
     </table>             
</fieldset>