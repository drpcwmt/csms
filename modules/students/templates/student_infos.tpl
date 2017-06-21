<form name="form_student_data" >
  <input type="hidden" value="[@editable]" id="student_editable">
  <input type="hidden" value="[@std_id]" id="std_id" name="std_id">
  <input type="hidden" value="[@parent_id]" id="parent_id" name="parent_id">
  <input type="hidden" value="[@guardians]" id="guardians" name="guardians">
  <input type="hidden" value="[@sms_id]" id="sms_id" name="sms_id">  
  <table width="100%" border="0" cellpacing="5" class="student_data_table">
      <tr>
        <td valign="top"><fieldset style="margin:5px" class="ui-widget-content ui-corner-all">
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
              <tbody>
                <tr class="[@insert_hidden]">
                  <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#id]</label></td>
                  <td valign="top" class="def_align">
                  	<div class="fault_input ui-corner-right ">[@id]</div>
                  </td>
                  <td width="120" valign="top" class="def_align"><label class="ui-widget-header ui-corner-left reverse_align  [@ig_mode]">[#cand_no]</label></td>
                  <td valign="top" class="def_align"><input type="text" name="cand_no" value="[@cand_no]" class="[@ig_mode]" /></td>
                </tr>
                <tr class="[@insert_hidden]">
                  <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#join_date]</label></td>
                  <td valign="top" class="def_align">
                  	<input type="text" value="[@join_date]" class="mask-date required" name="join_date">
                  </td>
                <tr>
                  <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#name](En)</label></td>
                  <td valign="top" colspan="3" class="def_align"><input type="text" value="[@name]" class="required" id="name" name="name"><input type="text" value="[@middle_name]" name="middle_name" class="input_double" /></td>
                </tr>
                <tr>
                  <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#last_name](En)</label></td>
                  <td valign="top" colspan="3" class="def_align"><input type="text" value="[@last_name]"  name="last_name"></td>
                </tr>
                <tr>
                  <td valign="top" class="reverse_align" colspan="3"><input type="text" dir="rtl" class="input_double rev_float required ui-corner-left" value="[@name_ar]" id="name_ar" name="name_ar"></td>
                  <td width="120" valign="middel"><label style="width:120px;" class=" def_align label ui-widget-header ui-corner-right">[#name_ar]</label></td>
                </tr>
              </tbody>
            </table>
          </fieldset>
          <fieldset style="margin:5px" class="ui-widget-content ui-corner-all">
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#birth_date]</label></td>
                  <td valign="top" colspan="2"><input type="text" class="mask-date" value="[@birth_date]" id="birth_date" name="birth_date" title="Date must be inserted in the format (dd/mm/yyyy)"></td>
                  <td width="120">&nbsp;</td>
                </tr>
                <tr>
                  <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#birth_country]</label></td>
                  <td valign="top"><input type="text" class="ena_auto " value="[@birth_country]" id="birth_country" name="birth_country"></td>
                  <td valign="top"><input type="text" dir="rtl" class="ena_auto rev_float ui-corner-left" value="[@birth_country_ar]" id="birth_city_ar" name="birth_country_ar"></td>
                  <td valign="middel"><label class="label ui-widget-header ui-corner-right ">[#rtl_birth_country]</label></td>
                </tr>
                <tr>
                  <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#ltr_nationality]</label></td>
                  <td valign="top"><input type="text" class="ena_auto " value="[@nationality]" id="nationality" name="nationality"></td>
                  <td valign="top"><input type="text" dir="rtl" class="rev_float ena_auto ui-corner-left" value="[@nationality_ar]" id="nationality_ar" name="nationality_ar"></td>
                  <td valign="middel"><label class="label ui-corner-right">[#rtl_nationality]</label></td>
                </tr>
                <tr>
                  <td valign="middel"><label class="label reverse_align">[#national_id]</label></td>
                  <td valign="top"><input type="text" value="[@national_id]" id="national_id" name="national_id"></td>
                  <td valign="middel"><label class="label reverse_align">[#reg_no]</label></td>
                  <td valign="top"><input type="text" value="[@reg_no]" id="reg_no" name="reg_no"></td>
                </tr>
                <tr>
                  <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#sex]</label></td>
                  <td valign="top">
                      <span class="buttonSet">
                        <input type="radio" name="sex" id="sex1-[@id]" value="1" [@sex_1_checked] />
                        <label for="sex1-[@id]">[#male]</label>
                        <input type="radio" name="sex" id="sex2-[@id]" value="2" [@sex_2_checked] />
                        <label for="sex2-[@id]">[#female]</label>
                     </span>
                  </td>
                 <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#religion]</label></td>
                  <td valign="top">
                      <span class="buttonSet">
                        <input type="radio" name="religion" id="religion1-[@id]" value="1" [@religion_1_checked] />
                        <label for="religion1-[@id]">[#muslim]</label>
                        <input type="radio" name="religion" id="religion2-[@id]" value="2" [@religion_2_checked] />
                        <label for="religion2-[@id]">[#christian]</label>
                     </span>
                  </td>
                </tr>
              </tbody>
            </table>
         </fieldset>  
         <fieldset class="addressbook_holder">
         	<legend>[#address]  <a module="addressbook" con="student" con_id="[@id]" sys_id="[@sms_id]" action="addAddressBook" class="mini_circle_button ui-state-default hoverable unprintable " style="display:[@editable_btn]" ><span class="ui-icon ui-icon-plus"></span></a><a module="addressbook" con="student" con_id="[@id]" sys_id="[@sms_id]" action="copyAddressBook" class="mini_circle_button ui-state-default hoverable unprintable " title="[#copy]" style="display:[@editable_btn]" ><span class="ui-icon ui-icon-copy"></span></a></legend>
            [@address_div]
         </fieldset>  
        <table width="100%" cellspacing="0">
            <tr>
                <td width="50%" valign="top">
                  <fieldset style="margin:5px" class="ui-widget-content ui-corner-all">
                    <legend class="ui-widget-header ui-corner-all">[#optional_languages]</legend>
                    <table width="100%" cellspacing="1" cellpadding="0" border="0">
                      <tbody>
                        <tr>
                          <td width="85" valign="middel"><span class="label ui-widget-header ui-corner-left reverse_align">[#first_lang]</span></td>
                          <td valign="middel">
                            <select class="combobox" name="lang_1">[@optional_service_opts_1]</select>
                          </td>
                        </tr>
                        <tr>
                          <td width="85" valign="middel"><span class="label ui-widget-header ui-corner-left reverse_align">[#second_lang]</span></td>
                          <td valign="middel">
                            <select class="combobox " name="lang_2">[@optional_service_opts_2]</select>
                          </td>
                        </tr>
                        <tr>
                          <td width="85" valign="middel"><span class="label ui-widget-header ui-corner-left reverse_align">[#third_lang]</span></td>
                          <td valign="middel">
                            <select class="combobox " name="lang_3">[@optional_service_opts_3]</select>
                           </td>
                        </tr>
                      </tbody>
                    </table>
                  </fieldset>
              </td>
              <td>
                <fieldset style="margin:5px" class="ui-widget-content ui-corner-all">
                    <legend class="ui-widget-header ui-corner-all">[#old_sch]</legend>
                    <table width="100%" cellspacing="1" cellpadding="0" border="0">
                      <tbody>
                        <tr>
                          <td width="85" valign="middel"><span class="label ui-widget-header ui-corner-left reverse_align">[#name]</span></td>
                          <td valign="middel">
                            <input name="old_sch" type="text" id="old_sch" value="[@old_sch]" />
                          </td>
                        </tr>
                        <tr>
                          <td width="85" valign="middel"><span class="label ui-widget-header ui-corner-left reverse_align">[#level]</span></td>
                          <td valign="middel">
                            <input name="old_sch_grade" type="text" id="old_sch_grade"  value="[@old_sch_grade]" />
                           </td>
                        </tr>
                      </tbody>
                    </table>
                  </fieldset>
               </td>
            </tr>
           </table>
        </td>
        <td valign="top" width="300">     	
            <fieldset style="text-align:center; position:relative" class="ui-state-highlight ui-corner-all">
        		[@class_div]
                <table width="100%" cellpadding="0" cellspacing="0" class="[@quit_hidden]">
                	<tr>
                    	<td valign="top"><label class="label ui-widget-header ui-corner-left reverse_align ">[#quit_date]</label></td>
                          <td valign="top" colspan="3" class="def_align"><input type="text" value="[@quit_date]" class="mask-date" name="quit_date"></td>
                    </tr>
             	</table>   	
           	</fieldset>
           
        	<fieldset style="text-align:center; position:relative" class="ui-widget-content ui-corner-all">
            	[@img_div]
         	</fieldset>
          
          
          
          	<fieldset class="notebook_holder">
            	<legend>[#notes] <a module="notebook" con="student" con_id="[@id]" action="addNoteBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]"><span class="ui-icon ui-icon-plus"></span></a></legend>
                [@notes_div]
            </fieldset>

          	<fieldset  class="phonebook_holder">
            	<legend>[#tel] <a module="phonebook" con="student" con_id="[@id]" action="addPhoneBook" class="mini_circle_button ui-state-default hoverable unprintable " style="display:[@editable_btn]" sys_id="[@sms_id]"><span class="ui-icon ui-icon-plus"></span></a></legend>
                [@phone_book]
            </fieldset>
          
          	<fieldset class="mailbook_holder">
            	<legend>[#mail] <a module="mailbook" con="student" con_id="[@id]" action="addMailBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]" ><span class="ui-icon ui-icon-plus"></span></a></legend>
                [@mail_book]
            </fieldset>
            
           <fieldset style="margin:5px" class="ui-widget-content ui-corner-all">
            <legend class="ui-widget-header ui-corner-all">[#bus]</legend>
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td width="85" valign="bottom"><span class="label ui-widget-header ui-corner-left reverse_align">[#no.]</span></td>
                  <td valign="middel">
                  	[@bus_div]
                  </td>
                </tr>
              </tbody>
            </table>
          </fieldset>

         <fieldset style="margin:5px" class="ui-widget-content ui-corner-all">
            <legend class="ui-widget-header ui-corner-all">[#locker]</legend>
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td width="85" valign="middel"><span class="label ui-widget-header ui-corner-left reverse_align">[#no.]</span></td>
                  <td valign="middel"><input name="locker" type="text" id="locker" value="[@locker]"></td>
                </tr>
              </tbody>
            </table>
          </fieldset>
       </td>
    </tr>
  </table>
</form>
