<form class="scoop">
	<div class="[@toolbox_placeholder]">
        <div class="toolbox">
            <a action="saveProfil">[#save]<span class="ui-icon ui-icon-disk"></span></a>
            <a action="deleteProfil" profil_id="[@id]">[#delete]<span class="ui-icon ui-icon-close"></span></a>
        </div>
    </div>
    <fieldset class="ui-state-highlight">
        <legend>[#settings]</legend>
        <input type="hidden" name="id" value="[@id]" />
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
           
          <tr>
            <td valign="top">
                <table width="100%" cellspacing="1" cellpadding="0" border="0">
                <tr class="[@profil_name_tr]">
                    <td><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                    <td colspan="3"><input type="text" value="[@title]" name="title" class="input_double" /></td>
                    </tr>
                      <tr>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#permis_allowness]</label></td>
                        <td><input type="text" value="[@permis_allowness]" id="permis_allowness" name="permis_allowness" class="input_half" /></td>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#working_days]</label></td>
                        <td>
                            <div class="week_warper">
                                <input id="working_days" type="hidden" value="0,1,2,3,4" name="working_days">
                              <ul id="new_week_day">
                                <li class="ui-state-active" action="recordWeekDay" val="0" style="cursor:pointer"> Sun </li>
                                <li class="ui-state-active" action="recordWeekDay" val="1" style="cursor:pointer"> Mon </li>
                                <li class="ui-state-active" action="recordWeekDay" val="2" style="cursor:pointer"> Tue </li>
                                <li class="ui-state-active" action="recordWeekDay" val="3" style="cursor:pointer"> Wed </li>
                                <li class="ui-state-active" action="recordWeekDay" val="4" style="cursor:pointer"> Thu </li>
                                <li class="ui-state-default" action="recordWeekDay" val="5" style="cursor:pointer"> Fri </li>
                                <li class="ui-state-default" action="recordWeekDay" val="6" style="cursor:pointer"> Sat </li>
                              </ul>
                           </div>
                        </td>
        
                      </tr>
                      <tr>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#over_time]</label></td>
                        <td>
                            <input type="text" value="[@overtime]" id="overtime" name="overtime" class="input_half" /> EGP/ 
                            <span class="buttonSet">
                                <input type="radio" value="hours" name="overtime_per" checked="" id="profil-[@id]-overtime_per_hours">
                                <label for="profil-[@id]-overtime_per_hours">
                                    [#hours]
                                </label>
                                <input type="radio" value="session" name="overtime_per" id="profil-[@id]-overtime_per_session">
                                <label for="profil-[@id]-overtime_per_session">
                                    [#period]
                                </label>
                                
                           </span>   
                        </td>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#begin_time]</label></td>
                    <td><input type="text" value="[@begin_time]" id="begin_time" name="begin_time" class="mask-time" /></td>
                    
                      </tr>
                      <tr>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#conventional_absent]</label></td>
                        <td><input type="text" value="[@absent_conv]" id="absent_conv" name="absent_conv" class="input_half" /></td>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#end_time]</label></td>
                    <td><input type="text" value="[@end_time]" id="end_time" name="end_time" class="mask-time" /></td>
                      </tr>
                      <tr>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#ill_absents]</label></td>
                        <td colspan="3"><input type="text" value="[@absent_ill]" id="absent_ill" name="absent_ill" class="input_half" /></td>
                      </tr>
                      <tr>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#abs_conv_value]</label></td>
                        <td colspan="3"><input type="text" value="[@abs_conv_value]" id="abs_conv_value" name="abs_conv_value" class="input_double" dir="ltr" /></td>
                      </tr>
                      <tr>
                        <td><label class="label ui-widget-header ui-corner-left reverse_align">[#abs_ill_value]</label></td>
                        <td colspan="3"><input type="text" value="[@abs_ill_value]" id="abs_ill_value" name="abs_ill_value" class="input_double" dir="ltr" /></td>
                      </tr>
                </table>
            </td>
          </tr>
        </table>
    </fieldset>
    <fieldset class="[@salary_sheet_fieldset]">
        <legend>[#salary]</legend>
        <table class="tableinput">
            <thead>
                <tr>
                    <th>[#credit_txt]
                    <button type="button" module="salary" action="newElmnt" rel="credit" profil_id="[@id]" class="circle_button hoverable ui-state-default" title="[#new]"><span class="ui-icon ui-icon-plus"></span></button>
                    </th>
                    <th>[#debit_txt]
                    <button type="button" module="salary" action="newElmnt" rel="debit" profil_id="[@id]" class="circle_button hoverable ui-state-default" title="[#new]"><span class="ui-icon ui-icon-plus"></span></button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td valign="top" style="vertical-align:top">
                        <ul class="profilul sortable" id="credit_ul" style="list-style:none; padding:0; margin:5px">
                            [@credit_ul]
                        </ul>
                    </td>
                    <td valign="top" style="vertical-align:top">
                        <ul class="profilul sortable" id="debit_ul" style="list-style:none; padding:0; margin:5px">
                            [@debit_ul]
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</form>