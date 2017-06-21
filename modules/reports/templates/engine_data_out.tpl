<h4>[#show_std_infos]</h4>
<fieldset>
    <legend onclick="$('#others_ul,#parent_ul').slideUp();$('#student_ul').slideDown()"  style="cursor:pointer;"> [#student_data] <span style="float:right" class="ui-icon ui-icon-triangle-1-s"></span> </legend>
    <div id="student_ul" class="info_group">
    <table width="100%">
    	<tr>
        	<td width="50%">
                <ul>
                    <li>
                        <label>
                          <input type="checkbox" value="1" name="serial" checked="checked" />
                          [#show_serial]
                        </label>
                    </li>
                    <li>
                        <label>
                          <input type="checkbox" name="fields[]" value="csms_sms.student_data.id" >
                          [#id]</label>
                    </li>
           		</ul>
           </td>
           <td>
           		<ul>
                    <li>
                        <label>
                          <input type="checkbox" value="1" name="signature"  />
                          [#signature]
                        </label>
                    </li>
                    <li>
                        <label>
                          <input type="checkbox" name="fields[]" value="csms_sms.student_data.cand_no" >
                          [#cand_no]</label>
                    </li>
                </ul>
          </td>
        </tr>
    </table>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td valign="top" width="50%">
                <ul>
                  <li>
                    <label>
                      <input type="checkbox" name="fields[]" value="csms_sms.student_data.name" >
                      [#name](En)</label>
                  </li>
                  <li>
                    <label>
                      <input type="checkbox" name="fields[]" value="csms_sms.student_data.nationality" >
                      [#nationality]</label>
                  </li>
                  <li>
                    <label>
                      <input type="checkbox" name="fields[]" value="csms_sms.student_data.birth_country" >
                      [#birth_country]</label>
                  </li>
                  <li>
                    <label>
                      <input type="checkbox" name="fields[]" value="csms_sms.student_data.address" >
                      [#address]</label>
                  </li>
                  
                </ul>
             </td>
              <td valign="top" width="50%">
                <ul>
                  <li>
                    <label>
                      <input type="checkbox" name="fields[]" value="csms_sms.student_data.name_ar" >
                      [#name]</label>
                  </li>
                  <li>
                    <label>
                      <input type="checkbox" name="fields[]" value="csms_sms.student_data.nationality_ar" >
                      [#rtl_nationality]</label>
                  </li>
                  <li>
                    <label>
                      <input type="checkbox" name="fields[]" value="csms_sms.student_data.birth_country_ar" >
                      [#rtl_birth_country]</label>
                  </li>
                  <li>
                    <label>
                      <input type="checkbox" name="fields[]" value="csms_sms.student_data.address_ar" >
                      [#rtl_address]</label>
                  </li>
                 
                </ul>
             </td>
          </tr>
       </table>
        <ul>
            <li>
                <label>
                  <input type="checkbox" name="fields[]" value="csms_sms.student_data.status" >
                  [#status]</label>
            </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.sex" >
              [#sex]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.religion" >
              [#religion]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.tel" >
              [#tel]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.mail" >
              [#mail]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.birth_date" >
              [#birth_date]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.national_id" >
              [#id_no]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.reg_no" >
              [#reg_no]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.bus_code" >
              [#bus_code]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.old_sch" >
              [#old_sch]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.join_date" >
              [#join_date]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.quit_date" >
             [#quit_date]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.locker" >
              [#locker]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.suspension_reason" >
              [#suspension_reason] </label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.suspension_till_date" >
              [#suspension_till_date]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.lang_1" >
              [#lang_1]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.lang_2" >
             [#lang_2]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.lang_3" >
              [#lang_3]</label>
          </li>
          <li>
            <label>
              <input type="checkbox" name="fields[]" value="csms_sms.student_data.bank_id" >
             [#account_no]</label>
          </li>
        </ul>
	</div>
</fieldset>

<fieldset>
   <legend onclick="$('#student_ul,#others_ul').slideUp();$('#parent_ul').slideDown()" style="cursor:pointer;">
       [#parents_infos]
       <span style="float:right" class="ui-icon ui-icon-triangle-1-s"></span>
   </legend>
   <div id="parent_ul" class="hidden info_group">
        <label>
          <input type="checkbox" name="fields[]" value="csms_sms.student_data.parent_id" class="ui-corner-right" />
			[#parent_id]
        </label>
        <label>
          <input type="checkbox" name="fields[]" value="csms_sms.parents.status" class="ui-corner-right" />
			[#status]
        </label>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td valign="top" width="50%">
				<fieldset>
                	<legend>[#father]</legend>
                        <ul>
                          <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_name"  />
                              [#name](En)</label>
                          </li>
                          <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_name_ar"  />
                              [#name_ar]</label>
                          </li>
                           <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_address"  />
                               [#address]
                            </label>
                          </li>
                         <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_address_ar"  />
                               [#address_ar]
                            </label>
                          </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_tel"  />[#tel]
                            </label>
                        </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_mail"  />[#mail]
                            </label>
                        </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_job"  />[#job]
                            </label>
                        </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_job_ar"  />[#job](Ar)
                            </label>
                        </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.father_job_at"  />[#job_at]
                            </label>
                        </li>
                	</ul>
				</fieldset>
              </td>
              <td>
              <td valign="top" width="50%">
				<fieldset>
                	<legend>[#mother]</legend>
                    <ul>
                      <li>
                        <label>
                          <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_name"  />
                          [#name](En)</label>
                      </li>
  	                  <li>
                        <label>
                          <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_name_ar"  />
                          [#name_ar]</label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_address"  />
                          [#address]
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_address_ar"  />
                          [#address_ar]
                        </label>
                      </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_tel"  />
                              [#tel]
                            </label>
                        </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_mail"  />
                              [#mail]
                            </label>
                        </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_job"  />
                              [#job]
                            </label>
                        </li>
                         <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_job_ar"  />
                              [#job](Ar)
                            </label>
                        </li>
                        <li>
                            <label>
                              <input type="checkbox" name="fields[]" value="csms_sms.parents.mother_job_at"  />
                              [#job_at]
                            </label>
                        </li>
                    </ul>
   				</fieldset>
              </td>
           	</tr>
        </table>
    </div>
</fieldset>
<fieldset>
	<legend onclick="$('#student_ul,#parent_ul').slideUp();$('#others_ul').slideDown()" style="cursor:pointer;">
   		[#other_infos]
		<span style="float:right" class="ui-icon ui-icon-triangle-1-s"></span>
    </legend>
        <ul id="others_ul" class="hidden"> 
            <li >
                <label > 
                    <input type="checkbox" name="fields[]" value="[@year_db].classes.[@name_dirc] AS class_name" />
                    [#class]
                </label>
            </li>
            <li >
                <label > 
                    <input type="checkbox" name="fields[]" value="csms_sms.levels.[@name_dirc] AS level_name" />
                    [#level]
                </label>
            </li>
            <li >
                <label > 
                    <input type="checkbox" name="fields[]" value="[@year_db].classes_std.new_stat AS new_stat" />
                    [#redouble_stat]
                </label>
            </li>
            <li >
                <label > 
                    <input type="checkbox" name="extras[]" value="absents" />
                    [#total_absent]
                </label>
            </li>
            <li >
                <label > 
                    <input type="checkbox" name="extras[]" value="brothers" />
                    [#brothers]
                </label>
            </li>
            <li >
                <label > 
                    <input type="checkbox" name="extras[]" value="age" />
                    [#age_in_first_oct]
                </label>
            </li>
            <li >
                <label > 
                    <input type="checkbox" name="extras[]" value="login" />
                    [#login]
                </label>
            </li>
        </ul>
        <span style="float:right" class="ui-icon ui-icon-triangle-1-s"></span>
    
