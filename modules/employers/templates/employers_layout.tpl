<form name="employer_data">
	<input type="hidden" value="[@id]" id="emp_code" name="id">
    <input type="hidden" value="[@editable]" id="employer_editable">
    <div class="tabs">
        <ul>
            <li><a href="#employer-infos">[#personel_infos]</a></li>
            <li><a href="#employer-other">[#other_infos]</a></li>
            <li><a href="#employer-acadmic">[#data_academy]</a></li>
            <li class="[@eveluation_tab]"><a href="#evaluation_div">[#evaluation]</a></li>
            <li><a href="#employer-finic" class="[@salary_tab]">[#financial_data]</a></li>
            <li class="[@acc_tab]"><a href="#employer-account" class="[@salary_tab]">[#accounts]</a></li>
            <!--<li><a href="index.php?module=absents&emp_id=[@id]" class="[@absents_tab]">[#absents]</a></li>-->
        </ul>
      <div id="employer-infos">
        	<table width="100%" cellpadding="0" cellspacing="2">
            	<tr>
                	<td>
                    	<fieldset style="margin:5px" class="ui-widget-content ui-corner-all">
                            <table width="100%" cellspacing="1" cellpadding="0" border="0">
                                <tr>
                                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#id]</label></td>
                                    <td><div class="fault_input ui-corner-right ">[@id]</div>
                                  </td>
                                </tr>
                                <tr>
                                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#name_en]</label></td>
                                    <td><label>
                                      <input type="text" value="[@name_ltr]" id="name_ltr" name="name_ltr" class="input_double required" >
                                    </label></td>
                              </tr>
                                  <tr>
                                    <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#name_ar]</label></td>
                                    <td><input type="text" value="[@name_rtl]" id="name_rtl" name="name_rtl" dir="rtl"  class="input_double required"/></td>
                                  </tr>
                                  <tr>
                                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#nationality]</label></td>
                                    <td>
                                    	<input name="nationality" value="[@nationality]" type="text" class="ena_auto required" />
                                    </td>
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
                                  </tr>
                              <tr>
                                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#tel]</label></td>
                                    <td><input type="text" value="[@tel]" id="tel" name="tel" /></td>
                              </tr>
                              <tr>
                                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#mobil]</label></td>
                                <td><input type="text" value="[@mobil]" id="mobil" name="mobil" /></td>
                              </tr>
                              <tr>
                                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#mail]</label></td>
                                <td><input type="text" value="[@mail]" id="mail" name="mail" class="input_double" /></td>
                              </tr>
                           </table>
                            <fieldset>
                              <legend>[#notes]</legend>
                              <textarea rows="5" cols="45" id="comments" name="comments">[@comments]</textarea>
                            </fieldset>
                        </fieldset>
                    	
                    
                    

                    </td>
                    <td valign="top"><fieldset>
                   	    <table width="100%" border="0" cellspacing="1" cellpadding="0">
                              <tr>
                                <td width="100"><label class="label ui-widget-header ui-corner-left reverse_align">[#school_name]</label></td>
                                <td><select name="school" class="combobox required">
                                  
                                      
                                        [@school_options]
                                    
                                    
                                </select></td>
                              </tr>
                              <tr>
                                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#job]</label></td>
                                <td><label>
                                  <select name="job_code" class="combobox required">
                                    [@jobs_opts]
                                  </select>
                                </label></td>
                              </tr>
                              <tr>
                                <td><span class="label ui-widget-header ui-corner-left reverse_align">[#position]</span></td>
                                <td><input type="text" value="[@position]" id="position" name="position" class="ena_auto" /></td>
                              </tr>
                              <tr>
                                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#join_date]</label></td>
                                <td><input type="text" class="mask-date required" value="[@join_date]" id="join_date" name="join_date" /></td>
                              </tr>
                              <tr class="[@quit_date_field]">
                                <td><label class="label ui-widget-header ui-corner-left reverse_align ">[#quit_date]</label></td>
                                <td><input type="text" class="mask-date " value="[@quit_date]" id="quit_date" name="quit_date" /></td>
                              </tr>
                            </table>
                   	    <fieldset style="text-align:center; position:relative" class="ui-widget-content ui-corner-all">
                   	      [@img_div]
               	      </fieldset>
                        </fieldset> 
                  </td>
                </tr>
            </table>
        </div>
        
        <div id="employer-other">
       		<fieldset>
                <table width="100%" cellspacing="1" cellpadding="0" border="0">
                  <tr>
                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#ltr_address]</label></td>
                    <td width="300"><input type="text" value="[@address_ltr]" id="address_ltr" name="address_ltr" class="input_double"></td>
                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#rtl_address]</label></td>
                    <td><input type="text" value="[@address_rtl]" id="address_rtl" name="address_rtl" dir="rtl" class="input_double" /></td>
                  </tr>
                  <tr>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#ltr_city]</label></td>
                    <td><input type="text" value="[@city_ltr]" id="city_ltr" name="city_ltr"  class="ena_auto"></td>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#rtl_city]</label></td>
                    <td><input type="text" value="[@city_rtl]" id="city_rtl" name="city_rtl" dir="rtl"  class="ena_auto"/></td>
                  </tr>
                  <tr>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#ltr_country]</label></td>
                    <td><input type="text" value="[@country_ltr]" id="country_ltr" name="country_ltr"  class="ena_auto"/></td>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#rtl_country]</label></td>
                    <td><input type="text" value="[@country_rtl]" id="country_rtl" name="country_rtl" dir="rtl"  class="ena_auto"/></td>
                  </tr>
                  
                  <tr>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#zip]</label></td>
                    <td><input type="text" value="[@zip]" id="zip" name="zip" class="ena_auto" /></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
               </table>
      </fieldset>
            <fieldset>
                <table width="100%" cellspacing="1" cellpadding="0" border="0">
                  <tr>
                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#birth_date]</label></td>
                    <td width="300"><label>
                      <input type="text" class="mask-date " value="[@birth_date]" id="birth_date" name="birth_date">
                    </label></td>
                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#religion]</label></td>
                    <td>
                        <span class="buttonSet">
                            <input type="radio" name="religion" id="religion1-[@id]" value="1" [@religion_1_checked] />
                            <label for="religion1-[@id]">[#muslim]</label>
                            <input type="radio" name="religion" id="religion2-[@id]" value="2" [@religion_2_checked] />
                            <label for="religion2-[@id]">[#christian]</label>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#marital_status]</label></td>
                    <td><select id="social_stat" name="social_stat" class="combobox">
                      <option value="1" [@social_stat-1]>[#married]</option>
                      <option value="2" [@social_stat-2]>[#single]</option>
                      <option value="3" [@social_stat-3]>[#divorced]</option>
                      <option value="4" [@social_stat-4]>[#married_child]</option>
                      <option value="5" [@social_stat-4]>[#widow]</option>
                    </select></td>
                    <td>
                   	 <div [@military_stat_hidden]><label class="label ui-widget-header ui-corner-left reverse_align" >[#military_status]</label></div></td>
                    <td>
                    	<div [@military_stat_hidden]>
                            <select id="military_stat" name="military_stat" class="combobox" >
                              <option value="1" [@military_stat-1]>[#exampted]</option>
                              <option value="2" [@military_stat-2]>[#done]</option>
                              <option value="3" [@military_stat-3]>[#post_bond]</option>
                            </select>
                        </div>
                   </td>
                  </tr>
                </table>
          </fieldset>
          <fieldset>
              <legend>[#identification]</legend>
              <table width="100%" cellspacing="1" cellpadding="0" border="0">
                  <tr>
                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#id_type]</label></td>
                    <td width="300"><select name="id_type" class="combobox">
                      <option value="1" [@id_type-1]>[#national_id]</option>
                      <option value="2" [@id_type-2]>[#passport]</option>
                      <option value="3" [@id_type-3]>[#drive_license]</option>
                    </select></td>
                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#id_no]</label></td>
                    <td><input name="id_no" type="text" id="id_no" value="[@id_no]" /></td>
                  </tr>
                  <tr>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#issue_from]</label></td>
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
      </div>
      
        <div id="employer-acadmic">
            <fieldset>
                <table border="0" cellpadding="1" cellspacing="0" width="100%">
                  <tbody>
                    <tr>
                        <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#diplom]</label></td>
                        <td><input name="diplome" id="diplome" value="[@diplome]" type="text"  class="ena_auto"></td>
                    </tr>
                    <tr>
                      <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#university]</label></td>
                      <td><input name="university" type="text" id="university" value="[@university]"  class="ena_auto"/></td>
                    </tr>
                    <tr>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#graduation_year]</label></td>
                        <td><input name="diplome_year" id="diplome_year" value="[@diplome_year]" type="text"></td>
                    </tr>
                  <tr>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#degree]</label></td>
                    <td><input name="diplome_degree" id="diplome_degree" value="[@diplome_degree]" type="text"  class="ena_auto"></td>
                  </tr>
                  <tr>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#school]</label></td>
                    <td>
                        <label><input value="0" name="school_type" checked="checked" type="radio" [@school_type-0]>
                        [#national_school]</label>
                        <br>
                        <label><input value="1" name="school_type" type="radio" [@school_type-1]>
                        [#language_school]</label>
                    </td>
                    </tr>
                  <tr>
                    <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#school_name_ar]</label></td>
                    <td><input name="school_name" id="school_name" value="[@school_name]" type="text" class="input_double ena_auto"></td>
                    </tr>
                </tbody>
                </table>
            </fieldset>
            <fieldset>
            <legend>[#others]</legend>
            <textarea name="diplome_others">[@diplome_others]</textarea>
            </fieldset>
        </div>
      
      <div id="employer-finic">
      	<table width="100%" cellspacing="1">
        	<tr>
            	<td valign="top">
                	<fieldset>
                        <table width="100%" cellspacing="1" cellpadding="0" border="0">
                          <tr>
                            <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#insur_no]</label></td>
                            <td><input name="insur_no" type="text" id="insur_no" value="[@insur_no]" /></td>
                          </tr>
                          <tr>
                            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#insurance_date]</label></td>
                            <td><input type="text" value="[@insur_date]" id="insurrance_date" name="insur_date" class="mask-date" /></td>
                          </tr>
                           <tr>
                             <td><label class="label ui-widget-header ui-corner-left reverse_align">[#payment_from]</label></td>
                             <td>
                                <select name="salary_from">
                                    <option value="cash" [@salary_from-0]>[#cash]</option>
                                    <option value="bank" [@salary_from-1]>[#bank]</option>
                                </select>
                             </td>
                           </tr>
                           <tr>
                             <td><label class="label ui-widget-header ui-corner-left reverse_align">[#bank]</label></td>
                             <td>
                                <select name="bank_id">
                                    [@banks]
                                </select>
                             </td>
                           </tr>
                           <tr>
                             <td><label class="label ui-widget-header ui-corner-left reverse_align">[#account_no]</label></td>
                             <td>
                                <input name="bank_no" type="text" id="bank_no" value="[@bank_no]"  />
                             </td>
                           </tr>
                        </table>
                    </fieldset>
       		  	</td>
                <td valign="top">
                	<fieldset class="ui-state-highlight">
                        <table width="100%" cellspacing="1" cellpadding="0" border="0">
                          <tr class="small_combobox">
                            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#salary_basic]</label></td>
                            <td><input name="basic" type="text" id="basic" value="[@basic]" class="input_half" />
                              <select name="basic_cur" class="combobox">
                                [@basic_cur_lis]
                              </select></td>
                          </tr>
                          <tr class="small_combobox">
                            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#salary_var]</label></td>
                            <td><input name="var" type="text" id="var" value="[@var]" class="input_half" />
                              <select name="var_cur" class="combobox">
                                [@var_cur_lis]
                              </select></td>
                          </tr>
                          <tr class="small_combobox">
                            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#salary_allow]</label></td>
                            <td><input name="allowances" type="text" id="allowances" value="[@allowances]" class="input_half" />
                              <select name="allowances_cur" class="combobox">
                                [@allowances_cur_lis]
                              </select></td>
                          </tr>
                          <tr>
                            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#salary_profil]</label></td>
                            <td>
                                <a title="[#new]" module="salary" action="newProfil" class="icon_button hoverable ui-state-default"><span class="ui-icon ui-icon-plus"></span></a>
                                <select name="profil_id" class="combobox">
                                  [@profils_opts]
                                </select>
                             </td>
                          </tr>
                        </table>
                	</fieldset>
                    <fieldset>
                    	<legend>[@profil_name]</legend>
                        <div class="profil_summary">
                        	[@profil_summary]
                        </div>
                    </fieldset>
              	</td>
              </tr>
            </table>
            
                  
            
        </div>
        <div id="employer-salary">
        
        </div>
        <div id="employer-account">
        
        </div>
        <div id="evaluation_div">
        	[@evaluation_div]
        </div>
    </div>
</form>