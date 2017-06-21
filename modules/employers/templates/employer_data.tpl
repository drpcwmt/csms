<form name="employer_data">
    <input type="hidden" value="" id="emp_code" name="id">
    <fieldset>
        <table width="100%" cellspacing="1" cellpadding="0" border="0">
        	<tr>
                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#id]</label></td>
                <td colspan="3"><div class="fault_input ui-corner-right ">[@id]</div>
              </td>
            </tr>
            <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                <td><label>
                  <input type="text" value="[@first_name_ltr]" id="first_name_ltr" name="first_name_ltr" class="input_double" >
                </label></td>
                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#name_rtl]</label></td>
                <td><input type="text" value="[@last_name_ltr]" id="last_name_ltr" name="last_name_ltr" /></td>
          </tr>
              <tr>
                <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#rtl_first_name]</label></td>
                <td><input type="text" value="[@first_name_rtl]" id="first_name_rtl" name="first_name_rtl" dir="rtl"  class="input_double"/></td>
                <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#rtl_last_name]</label></td>
                <td><input type="text" value="[@last_name_rtl]" id="last_name_rtl" name="last_name_rtl" dir="rtl"  /></td>
              </tr>
              <tr>
                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#sex]</label></td>
                <td width="300">
                	 <span class="buttonSet">
                        <input type="radio" name="sex" id="sex1-[@id]" value="1" [@sex_1_checked] />
                        <label for="sex1-[@id]">[#male]</label>
                        <input type="radio" name="sex" id="sex2-[@id]" value="2" [@sex_2_checked] />
                        <label for="sex2-[@id]">[#female]</label>
                     </span>
                </td>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#nationality]</label></td>
                <td>
                	<span class="buttonSet">
                    <input type="radio" name="nationality" id="nationality1-[@id]" value="1" [@nationality_1_checked] />
                    <label for="nationality1-[@id]">[#egyptian]</label>
                    <input type="radio" name="nationality" id="nationality2-[@id]" value="2" [@nationality_2_checked] />
                    <label for="nationality2-[@id]">[#forgein]</label>
                 </span>
                </td>
              </tr>
          <tr>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#tel]</label></td>
                <td><input type="text" value="[@tel]" id="tel" name="tel" /></td>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#mobil]</label></td>
                <td><input type="text" value="[@mobil]" id="mobil" name="mobil" /></td>
          </tr>
          <tr>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#mail]</label></td>
            <td><input type="text" value="[@mail]" id="mail" name="mail" class="input_double" /></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          </table>
      </fieldset>
          <fieldset>
              <table width="100%" cellspacing="1" cellpadding="0" border="0">
     <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#school_name]</label></td>
            <td width="300">
                <select name="school" class="combobox">
                    [@school_options]
                </select>
            </td>
            <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#join_date]</label></td>
            <td><input type="text" class="mask-date " value="[@join_date]" id="join_date" name="join_date" /></td>
              </tr>
              <tr>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#job]</label></td>
            <td><label>
              <select name="job_code" class="combobox">
                <option value="1">[#admins]</option>
                <option value="2">[#matron]</option>
                <option value="4">[#accounting]</option>
                <option value="10">[#prof]</option>
                <option value="6">[#security]</option>
                <option value="8">[#factor]</option>
                <option value="7">[#drivers]</option>
                <option value="5">[#dada]</option>
              </select>
            </label></td>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#quit_date]</label></td>
            <td><input type="text" class="mask-date " value="[@quit_date]" id="quit_date" name="quit_date" /></td>
              </tr>
          <tr>
            <td> <label class="label ui-widget-header ui-corner-left reverse_align">[#position]</label></td>
            <td colspan="3"><input type="text" value="[@job_position]" id="position" name="position"></td>
            </tr>
          </table>
       </fieldset>
       <fieldset>
        <legend>[#id]</legend>
        <table width="100%" cellspacing="1" cellpadding="0" border="0">
          <tr>
            <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#id_type]</label></td>
            <td width="300"><select name="id_type" class="combobox">
              <option value="1">[#national_id]</option>
              <option value="2">[#passport]</option>
              <option value="3">[#drive_license]</option>
            </select></td>
            <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#id_no]</label></td>
            <td><input name="id_no" type="text" id="id_no" value="[@id_no]" /></td>
          </tr>
          <tr>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#from]</label></td>
            <td><input type="text" value="[@id_from]" id="id_from" name="id_from" /></td>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
            <td><input type="text" class="mask-date " value="[@id_date]" id="id_date" name="id_date" /></td>
          </tr>
          <tr>
            <td> </td>
            <td>&nbsp;</td>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#expiration_date]</label></td>
            <td><input type="text" class="mask-date " value="[@id_exp_date]" id="id_exp_date" name="id_exp_date"></td>
          </tr>
        </table>
    </fieldset>
    <fieldset>
    	<legend>[#comments]</legend>
        <textarea rows="5" cols="45" id="comments" name="comments">[@comments]</textarea>
    </fieldset>
</form>