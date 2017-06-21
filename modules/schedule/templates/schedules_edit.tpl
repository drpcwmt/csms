<div style="height:500px; overflow:auto">
	<form name="schedule_form"  class="ui-state-highlight ui-corner-all" style="padding:8px; margin-bottom:10px">
        <input type="hidden" name="con" value="[@con]" />
        <input type="hidden" name="con_id" value="[@con_id]" />
        <input type="hidden" name="sessions" />
        <ul style="margin:0; padding:0; list-style:none">
            <li class="ui-corner-all ui-state-default hoverable clickable selectable" action="selectEditOpt">
                <h3 style="margin:5px"><input type="radio"  name="type"  value="default" checked />[#default_week]</h3>
            </li>
            <li class="ui-corner-all ui-state-default hoverable clickable selectable" action="selectEditOpt">
                <h3 style="margin:5px"><input type="radio"  name="type"  value="special" />[#specific_dates]</h3>
            </li>
            <li class="ui-corner-bottom ui-widget-content special_date_opt hidden">
                <table width="100%" cellpadding="0" >
                  <tr>
                    <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#from]</label></td>
                    <td><input class=" datepicker required" name="b_date" type="text" update="reloadEditTimeTable"></td>
                  </tr>
                  <tr>
                    <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#till]</label></td>
                    <td><input class="mask-date datepicker required"  name="e_date" type="text" update="reloadEditTimeTable"></td>
                  </tr>
                  <tr>
                    <td colspan="2">
                        <ul style="margin:0 10px; padding:0; list-style:none">
                            <li><label><input type="checkbox" name="hide_ds" value="1" update="reloadEditTimeTable"/>[#hide_default_structure]</label></li>
                        </ul>
                    </td>
                  </tr>
                </table>
            </li>
         </ul>
         <label><input type="checkbox" name="hide_lessons" value="1" update="reloadEditTimeTable"/>[#hide_lessons]</label> 
         <br>
         <label><input type="checkbox" name="hide_is" value="1" update="reloadEditTimeTable"/>[#hide_parent_structure]</label>  
    </form>

  <div class="tabs">
    <ul>
      <li><a href="#lessons_tab" ><span class="ui-icon ui-icon-note def_float"></span> [#lessons]</a></li>
      <li><a href="#sessions_tab" ><span class="ui-icon ui-icon-clock def_float"></span> [#sessions]</a></li>
      <li><a href="#reset_tab" ><span class="ui-icon ui-icon-trash def_float"></span> [#reset]</a></li>
    </ul>
    <div id="lessons_tab">
        <h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-accordion-icons ui-corner-all hoverable hand"  style="padding:0.6em; display:block; margin-top:2px" >
        	<a action="attributLesson">
                <span class="ui-accordion-header-icon ui-icon ui-icon-bookmark def_float" style="margin:-2px 10px 0 0"></span>
                [#attribut_lesson]
            </a>
        </h3>
       <div class="session_accordion">
        	<h3><a>[#generate_lesson]</a></h3>
            <div  style="padding:8px">
            	<form name="generate_lesson_form" class="ui-state-highlight ui-corner-all">
                
                </form>
            </div>
        </div>
    </div>
    <div id="sessions_tab">
        <h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-accordion-icons ui-corner-all hoverable hand"  style="padding:0.6em; display:block; margin-top:2px" action="joinSession">
        	<span class="ui-accordion-header-icon ui-icon ui-icon-link def_float" style="margin:-2px 10px 0 0"></span>
            [#merge]
        </h3>
       <div class="session_accordion">
        	<h3><a>[#resize]</a></h3>
            <div style="padding:8px">
            	<form class="ui-corner-all ui-state-highlight" name="resise_session_form">
                	<table cellpadding="0" width="100%">
                    	<tr>
                        	<td width="100" valign="middel">
                            	<label class="label ui-widget-header ui-corner-left">[#start]</label>
                            </td>
                            <td>
                            	<input type="text" class="mask-time input_half ui-state-default ui-corner-right required" name="begin" />
                            </td>
                        </tr>
                    	<tr>
                        	<td width="100" valign="middel">
                            	<label class="label ui-widget-header ui-corner-left">[#end]</label>
                            </td>
                            <td>
                            	<input type="text" class="mask-time input_half ui-state-default ui-corner-right required" name="end" />
                            </td>
                        </tr>
                    	<tr>
                        	<td width="100" valign="middel">
                            	<label class="label ui-widget-header ui-corner-left">[#max]</label>
                            </td>
                            <td>
                            	<div style="width:75px; font-size:12px; padding:4px" class="ui-widget-content ui-corner-right max_session_time">&nbsp;</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                           		<button class="ui-corner-all ui-state-default hoverable button" action="resizeSession" type="button">
                                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                    [#resize]
                                </button>
                            </td>
                        </tr>
                   	</table>
                 </form>
            </div>
            <h3><a>[#add_session]</a></h3>
            <div  style="padding:8px">
            	<form name="add_session_form" class="ui-state-highlight ui-corner-all">
                    <input type="hidden" name="def_day" value="[@def_day_val]" />
            		<table width="100%" cellpadding="0" >
                      <tr>
                        <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#lesson_time_begin]</label></td>
                        <td><input class="mask-time input_half" name="lesson_time_begin" update="upEndTime" type="text"></td>
                      </tr>
                      <tr>
                        <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#end]</label></td>
                        <td><input class="mask-time input_half" name="lesson_time_end" update="upLessonTime" type="text"></td>
                      </tr>
                      <tr>
                        <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#time]</label></td>
                        <td><input class="input_half" name="lesson_time" update="upEndTime" type="text"></td>
                      </tr>
                      <tr>
                        <td colspan="2">
                            <span class="buttonSet">
                                <input type="radio"  name="break_type" id="break_type1" value="c" checked="checked"/>
                                <label for="break_type1">[#court]</label>
                                <input type="radio"  name="break_type" id="break_type0" value="b" />
                                <label for="break_type0">[#break]</label>
                            </span>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" align="center">
                            <ul class="def_day_selector">
                                <li val="0" class="ui-state-default [@day_0_class_active] ui-corner-left hand">[@day_0_name]</li>
                                <li val="1" class="ui-state-default [@day_1_class_active] hand">[@day_1_name]</li>
                                <li val="2" class="ui-state-default [@day_2_class_active] hand">[@day_2_name] </li>
                                <li val="3" class="ui-state-default [@day_3_class_active] hand"> [@day_3_name] </li>
                                <li val="4" class="ui-state-default [@day_4_class_active] hand"> [@day_4_name] </li>
                                <li val="5" class="ui-state-default [@day_5_class_active] hand"> [@day_5_name] </li>
                                <li val="6" class="ui-state-default [@day_6_class_active] ui-corner-right hand"> [@day_6_name] </li>
                          </ul>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" align="center">
                        <button class="ui-corner-all ui-state-default hoverable button" action="submitNewSession" type="button">
                                <span class="ui-icon ui-icon-plus"></span>
                                [#add]
                            </button>
                        </td>
                      </tr>
                   </table>
            	</form>
    		</div>
            <h3><a>[#auto_generate]</a></h3>
            <div style="padding:8px">
            	<form class="ui-corner-all ui-state-highlight" name="autogen_form">
                    <input type="hidden" name="def_day" value="[@def_day_val]" />
                	<table cellpadding="0" width="100%">
                    	<tr>
                        	<td width="100" valign="middel">
                            	<label class="label ui-widget-header ui-corner-left">[#day_time_begin]</label>
                            </td>
                            <td>
                            	<input type="text" class="mask-time input_half ui-state-default ui-corner-right required" name="day_time_begin" value="[@day_time_begin]"  placeholder="[@day_time_begin]">
                            </td>
                        </tr>
                    	<tr>
                        	<td width="100" valign="middel">
                            	<label class="label ui-widget-header ui-corner-left">[#day_time_end]</label>
                            </td>
                            <td>
                            	<input type="text" class="mask-time input_half ui-state-default ui-corner-right required" name="day_time_end"  value="[@day_time_end]" placeholder="[@day_time_end]">
                            </td>
                        </tr>
                      <tr>
                        <td colspan="2" align="center">
                            <ul class="def_day_selector">
                                <li val="0" class="ui-state-default [@day_0_class_active] ui-corner-left hand">[@day_0_name]</li>
                                <li val="1" class="ui-state-default [@day_1_class_active] hand">[@day_1_name]</li>
                                <li val="2" class="ui-state-default [@day_2_class_active] hand">[@day_2_name] </li>
                                <li val="3" class="ui-state-default [@day_3_class_active] hand"> [@day_3_name] </li>
                                <li val="4" class="ui-state-default [@day_4_class_active] hand"> [@day_4_name] </li>
                                <li val="5" class="ui-state-default [@day_5_class_active] hand"> [@day_5_name] </li>
                                <li val="6" class="ui-state-default [@day_6_class_active] ui-corner-right hand"> [@day_6_name] </li>
                          </ul>
                        </td>
                      </tr>
                    	<tr>
                        	<td width="100" valign="middel">
                            	<label class="label ui-widget-header ui-corner-left">[#session_time]</label>
                            </td>
                            <td>
                            	<input type="text" class="input_half ui-state-default ui-corner-right required" name="time" >
                            </td>
                        </tr>
                        <tr>
                        	<td width="100" valign="middel">
                            	<label class="label ui-widget-header ui-corner-left">[#breaks]</label>
                             </td>
                             <td>
                             	<input type="text" update="displayBreakTimes" value="2" class="input_half ui-state-default ui-corner-right" name="breaks">
                             </td>
                        </tr>
                        <tr>
                        	<td colspan="2" align="center">
                                <ol style="padding:5px; margin:0 10px" id="breaks_ul">
                                    <li>
                                        <label style="width:80px; display:inline-block" class="label ui-widget-header ui-corner-left">[#break] 1:</label><input type="text" class="input_half ui-state-default ui-corner-right required" name="break_time1"> [#minuts]
                                    </li>
                                     <li>
                                        <label style="width:80px; display:inline-block" class="label ui-widget-header ui-corner-left">[#break] 2:</label><input type="text" class="input_half ui-state-default ui-corner-right required" name="break_time2"> [#minuts]
                                    </li>
                                </ol>
                            </td>
                        </tr>
                    	<tr>
                            <td colspan="2" align="center">
                           		<button class="ui-corner-all ui-state-default hoverable button" action="autoGenerateSessions" type="button">
                                    <span class="ui-icon ui-icon-gear"></span>
                                    [#generate]
                                </button>
                            </td>
                        </tr>
                    </table>
            	</form>
            </div>
            <h3><a>[#copy_from]</a></h3>
            <div style="padding:8px">
				<form name="copy_form" class="ui-corner-all ui-state-highlight">
                	<table cellpadding="0" width="100%">
                    	<tr>
                        	<td width="80" valign="top">
                            	<label class="label ui-widget-header ui-corner-left">[#from]</label>
                            </td>
                            <td>
                            	<select name="copycon" id="copycon"  class="combobox" update="updateCopyConid">[@avaible_cons]</select>
                            	<select name="copyconid" id="copyconId"  class="combobox" >[@first_con_ids]</select>
                            </td>
                        </tr>
                    	<tr>
                            <td colspan="2" align="center">
                           		<button class="ui-corner-all ui-state-default hoverable button" action="copySessionTime" type="button">
                                    <span class="ui-icon ui-icon-copy"></span>
                                    [#copy]
                                </button>
                            </td>
                          </tr>
                    </table>
            	</form>
            </div>
    	</div>
    </div>
    <div id="reset_tab">
    	<form name="reset_form">
           <fieldset class="ui-state-highlight">
            <ul style="margin:0; padding:0; list-style:none">
                <li class="ui-corner-all ui-state-default hoverable clickable selectable"  action="selectResetDate">
                    <h3 style="margin:5px"><input type="radio"  name="del_type"  value="lessons" checked />[#lessons]</h3>
                </li>
                <li class="ui-corner-all ui-state-default hoverable clickable selectable" action="selectResetDate">
                    <h3 style="margin:5px"><input type="radio"  name="del_type"  value="sessions" />[#sessions_and_lessons]</h3>
                </li>
            </ul>
           </fieldset>
           <fieldset class="ui-state-highlight">
            <ul style="margin:0; padding:0; list-style:none">
                <li class="ui-corner-all ui-state-default hoverable clickable selectable hidden del_selc_li" action="selectResetOpt">
                    <h3 style="margin:5px"><input type="radio"  name="del_radio"  value="selc" />[#delete_selection]</h3>
                </li>
               <li class="ui-corner-all ui-state-default hoverable clickable selectable" action="selectResetOpt">
                    <h3 style="margin:5px"><input type="radio"  name="del_radio"  value="exp" checked />[#reset_to_def]</h3>
                </li>
                <li class="ui-corner-all ui-state-default hoverable clickable selectable" action="selectResetOpt">
                    <h3 style="margin:5px"><input type="radio"  name="del_radio"  value="all" />[#reset_all]</h3>
                </li>
            </ul>
            <table width="100%" cellpadding="0" >
              <tr>
              	<td colspan="2" align="center">
                	<button class="ui-corner-all ui-state-default hoverable button" action="submitReset" type="button">
                    	<span class="ui-icon ui-icon-trash"></span>
                    	[#reset]
                    </button>
                </td>
              </tr>
            </table>
          </fieldset>
		</form>
    </div>
  </div>
</div>
