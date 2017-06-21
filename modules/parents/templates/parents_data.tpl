<form name="form_parent_data">
  <input type="hidden" value="[@editable]" class="editable">
  <input type="hidden" value="[@id]" id="parent_form_id" name="id" >
    

  <table width="100%" cellspacing="0" cellpadding="0" border="0" >
      <tr>
        <td width="50%" valign="top">
          <fieldset class="ui-corner-all ui-widget-content father">
            <legend>[#father]</legend>
              <table width="100%" cellspacing="1" cellpadding="0" border="0">
                <tbody>
                  <tr>
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#ltr_name]</label></td>
                    <td valign="top"><input type="text" class="input_double required" value="[@father_name]" id="father_name" name="father_name" dir="ltr"></td>
                  </tr>
                  <tr>
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#rtl_name]</label></td>
                    <td valign="top"><input type="text" dir="rtl" class="input_double" value="[@father_name_ar]" id="father_name_ar" name="father_name_ar" /></td>
                  </tr>
                </tbody>
              </table>
              <table width="100%" cellspacing="1" cellpadding="0" border="0" class="father_data [@father_data_hide]">
              	<tbody>
                  <tr class="father_parental_right father_data parental_right_tr">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#parental_right]</label></td>
                    <td valign="top">
                    	<input type="checkbox" value="1" name="father_resp" [@father_resp_check]>
                    </td>
                  </tr>
                  <tr class="father_data">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#spoken_lang]</label></td>
                    <td valign="top">
                    	<input type="text" value="[@father_lang]" id="father_lang" name="father_lang" />
                    </td>
                  </tr>
                  <tr class="phonebook_holder father_data">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#tel] <a module="phonebook" con="father" con_id="[@id]" action="addPhoneBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]" ><span class="ui-icon ui-icon-plus"></span></a></label></td>
                    <td valign="top">
                    	[@father_phone_book]
                    </td>
                  </tr>
                  
                  <tr class="mailbook_holder father_data">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#mail] <a module="mailbook" con="father" con_id="[@id]" action="addMailBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]" ><span class="ui-icon ui-icon-plus"></span></a></label></td>
                    <td valign="top">
                    	[@father_mail_book]
                    </td>
                  </tr>
                  <tr class="addressbook_holder father_data">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#address]  <a module="addressbook" con="father" con_id="[@id]" action="addAddressBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]" ><span class="ui-icon ui-icon-plus"></span></a><a module="addressbook" con="father" con_id="[@id]" sys_id="[@sms_id]" action="copyAddressBook" class="mini_circle_button ui-state-default hoverable unprintable " title="[#copy]" style="display:[@editable_btn]" ><span class="ui-icon ui-icon-copy"></span></a></label></td>
                    <td valign="top">
                    	[@father_address_div]
                    </td>
                  </tr>
              </tbody>
              </table>
              <fieldset class="father_data [@father_data_hide]">
                <legend>[#occupation]</legend>  
                <label><input type="checkbox" value="1" name="father_emp" [@father_emp_check]>[#parent_employer]</label>
                <table width="100%" cellspacing="1" cellpadding="0" border="0">  
                    <tbody>  
                  <tr>
                    <td width="120" class="reverse_align"><span class="label ui-widget-header ui-corner-left">[#position]</span></td>
                    <td valign="top"><input type="text" value="[@father_job]" class=" input_double" id="father_job" name="father_job"></td>
                  </tr>
                   <tr>
                    <td width="120" class="reverse_align"><span class="label ui-widget-header ui-corner-left">[#position] (Ar)</span></td>
                    <td valign="top"><input type="text" value="[@father_job_ar]" class=" input_double" id="father_job_ar" name="father_job_ar"></td>
                  </tr>
                  <tr>
                    <td width="120" class="reverse_align"><span class="label ui-widget-header ui-corner-left">[#at]</span></td>
                    <td valign="top"><input type="text" value="[@father_job_at]" class=" input_double" id="father_job_at" name="father_job_at"></td>
                  </tr>
                  
                  <td width="120" class="reverse_align" valign="top"><span class="label ui-widget-header ui-corner-left">[#qualification]</span></td>
                    <td valign="top"><textarea name="father_qualification">[@father_qualification]</textarea></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
          </fieldset>
          
        <!-- Mother data -->
        <fieldset class="ui-corner-all ui-widget-content mother">
            <legend>[#mother]</legend>
<table width="100%" cellspacing="1" cellpadding="0" border="0">
                <tbody>
                  <tr>
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#ltr_name]</label></td>
                    <td valign="top"><input type="text" class="input_doubleii " value="[@mother_name]" id="mother_name" name="mother_name" dir="ltr"></td>
                  </tr>
                  <tr>
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#rtl_name]</label></td>
                    <td valign="top"><input type="text" dir="rtl" class="input_double" value="[@mother_name_ar]" id="mother_name_ar" name="mother_name_ar" /></td>
                  </tr>
                </tbody>
              </table>
              <table width="100%" cellspacing="1" cellpadding="0" border="0" class="mother_data [@mother_data_hide]">
              	<tbody>
                  <tr class="mother_parental_right mother_data parental_right_tr">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#parental_right]</label></td>
                    <td valign="top">
                    	<input type="checkbox" value="1" name="mother_resp" [@mother_resp_check]>
                    </td>
                  </tr>
                  <tr class="mother_data">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#spoken_lang]</label></td>
                    <td valign="top">
                   	  <input type="text" value="[@mother_lang]" id="mother_lang" name="mother_lang" />
                    </td>
                  </tr>
                  <tr class="phonebook_holder mother_data">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#tel] <a module="phonebook" con="mother" con_id="[@id]" action="addPhoneBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]" ><span class="ui-icon ui-icon-plus"></span></a></label></td>
                    <td valign="top">
                    	[@mother_phone_book]
                    </td>
                  </tr>
                  
                  <tr class="mailbook_holder mother_data">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#mail] <a module="mailbook" con="mother" con_id="[@id]" action="addMailBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]" ><span class="ui-icon ui-icon-plus"></span></a></label></td>
                    <td valign="top">
                    	[@mother_mail_book]
                    </td>
                  </tr>
                  <tr class="addressbook_holder mother_data">
                    <td width="120" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#address]  <a module="addressbook" con="mother" con_id="[@id]" action="addAddressBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]" ><span class="ui-icon ui-icon-plus"></span></a><a module="addressbook" con="mother" con_id="[@id]" sys_id="[@sms_id]" action="copyAddressBook" class="mini_circle_button ui-state-default hoverable unprintable " title="[#copy]" style="display:[@editable_btn]" ><span class="ui-icon ui-icon-copy"></span></a></label></td>
                    <td valign="top">
                    	[@mother_address_div]
                    </td>
                  </tr>
              </tbody>
          </table>
            <fieldset class="mother_data [@mother_data_hide]">
                <legend>[#occupation]</legend>  
                <label><input type="checkbox" value="1" name="mother_emp" [@mother_emp_check]>[#parent_employer]</label>
                <table width="100%" cellspacing="1" cellpadding="0" border="0">  
                    <tbody>  
                  <tr>
                    <td width="120" class="reverse_align"><span class="label ui-widget-header ui-corner-left">[#position]</span></td>
                    <td valign="top"><input type="text" value="[@mother_job]" class=" input_double" id="mother_job" name="mother_job"></td>
                  </tr>
                  <tr>
                    <td width="120" class="reverse_align"><span class="label ui-widget-header ui-corner-left">[#position](Ar)</span></td>
                    <td valign="top"><input type="text" value="[@mother_job_ar]" class=" input_double" id="mother_job_ar" name="mother_job_ar"></td>
                  </tr>
                  <tr>
                    <td width="120" class="reverse_align"><span class="label ui-widget-header ui-corner-left">[#at]</span></td>
                    <td valign="top"><input type="text" value="[@mother_job_at]" class=" input_double" id="mother_job_at" name="mother_job_at"></td>
                  </tr>
                  
                  <td width="120" class="reverse_align" valign="top"><span class="label ui-widget-header ui-corner-left">[#qualification]</span></td>
                    <td valign="top"><textarea name="mother_qualification">[@mother_qualification]</textarea></td>
                  </tr>
                </tbody>
              </table>
          </fieldset>
        </fieldset>
        </td>
        <td width="30%" valign="top">
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
                  <tr>
                    <td width="120" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#family_id]</label></td>
                    <td valign="top"><div class="fault_input family_id">[@id]</div></td>
                  </tr>        
                  <tr>
                    <td width="120" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#sons]</label></td>
                    <td valign="top"><div class="fault_input">[@count_sons]</div></td>
                  </tr>        
             </table>
            <fieldset class="notebook_holder">
                <legend>[#notes] <a module="notebook" con="parent" con_id="[@id]" action="addNoteBook" class="mini_circle_button ui-state-default hoverable unprintable" style="display:[@editable_btn]" sys_id="[@sms_id]" ><span class="ui-icon ui-icon-plus"></span></a></legend>
                [@notes_div]
            </fieldset>          
            <fieldset>
                <legend>[#status]</legend>
                <ul style="list-style:none; margin:0px 10px; padding:0">
                    <li><label><input type="radio" name="status" value="1" update="updateSocStat" [@status-1-selected] />[#married]</label></li>
                    <li><label><input type="radio" name="status" value="2" update="updateSocStat" [@status-2-selected]/>[#divorced]</label></li>
                    <li><label><input type="radio" name="status" value="3" update="updateSocStat" [@status-3-selected]/>[#father_deceased]</label></li>
                    <li><label><input type="radio" name="status" value="4" update="updateSocStat" [@status-4-selected]/>[#mother_deceased]</label></li>
                    <li><label><input type="radio" name="status" value="5" update="updateSocStat" [@status-5-selected]/>[#both_parents_deceased]</label></li>
                </ul>
            </fieldset>
        </td>
      </tr>
    </tbody>
  </table>
</form>
