<fieldset >
  <legend>[#insurrance]</legend>
    <table border="0" cellspacing="0" width="100%">
          <tr>
            <td width="180" valign="middel" class="reverse_align">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_min_total]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@insur_min_total]" name="insur_min_total" /> 
               EGP 
            </td>
          </tr>  
          <tr>
            <td width="180" valign="middel" class="reverse_align">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_max_total]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@insur_max_total]" name="insur_max_total" />
               EGP 
            </td>
          </tr>  
          <tr>
            <td class="reverse_align" valign="middel">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_basic_per]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@insur_basic_per]" name="insur_basic_per" class="input_half" /> 
               % 
            </td>
      </tr>  
      <tr>
            <td class="reverse_align" valign="middel">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_var_per]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@insur_var_per]" name="insur_var_per"  class="input_half"/>
%            </td>
      </tr>  
      <tr>
            <td class="reverse_align" valign="middel">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_basic_share]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@insur_basic_share]" name="insur_basic_share" class="input_half" />
%            </td>
      </tr>  
      <tr>
        <td class="reverse_align" valign="middel">
            <label class="label ui-widget-header ui-corner-left reverse_align">[#insur_var_share]</label>
        </td>
        <td valign="top">
           <input type="text" value="[@insur_var_share]" name="insur_var_share" class="input_half" />
%        </td>
      </tr>  
      
     </table>             
</fieldset>
<fieldset >
  <legend>[#tax]</legend>
    <table border="0" cellspacing="0" width="100%">
          <tr>
            <td width="180" valign="middel" class="reverse_align">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#tax_worker]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@tax_worker]" name="tax_worker" class="input_half" />
%            </td>
          </tr>  
          <tr>
            <td width="180" valign="middel" class="reverse_align">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#tax_worker_exclude]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@tax_worker_exclude]" name="tax_worker_exclude" /> 
               / [#year] 
            </td>
          </tr> 
     </table>             
</fieldset>
<fieldset >
  <legend>[#tax_stamp]</legend>
    <table border="0" cellspacing="0" width="100%">
          <tr>
            <td width="180" valign="middel" class="reverse_align">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#tax_stamp_per]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@tax_stamp_per]" name="tax_stamp_per" class="input_half" />
%            </td>
          </tr>  
          <tr>
            <td width="180" valign="middel" class="reverse_align">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#tax_stamp_exclude]</label>
            </td>
            <td valign="top">
               <input type="text" value="[@tax_stamp_exclude]" name="tax_stamp_exclude" /> 
               / [#month] 
            </td>
          </tr> 
     </table>             
</fieldset>
