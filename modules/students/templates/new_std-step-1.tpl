<input type="hidden" id="wizardSteps" value="1" />
<input type="hidden" id="new_std_id" value=""/>
<div id="newStudentForm">
	<div id="newStudentDiv">
		<div id="step-1" class="ui-widget-content items">
			<form id="first_step_form" style="margin:20px 40px 0px 40px;">
                <div class="ui-state-highlight" style="margin-bottom:50px" >
                    <table width="100%" border="0" cellspacing="1" cellpadding="0">
                        <tr>
                            <td width="100" valign="middel" style="text-align:right"> 
                                <label class="label ui-widget-header ui-corner-left">[#level]</label>
                            </td>
                            <td valign="middel">
                                <select name="level" class="combobox" id="level_select">[@levels_opts]</select>
                            </td>
                        </tr>
                        <tr>
                            <td width="100" valign="middel" style="text-align:right">
                                <label class="label ui-widget-header ui-corner-left">[#year]</label>
                            </td>
                            <td valign="middel">
                                <select name="year" class="combobox requires">[@years_opts]</select>
                            </td>
                        </tr>
                    </table>
                    <ul style="list-style:none">
                        <li> 
                            <h3>
                              <input type="radio" name="insertType" value="1" checked onclick="$('#old_student_name, #new_student_name').toggle()"/>
                                [#new_file]
                            </h3>
                        </li>
                        <li> 
                            <h3>
                              <input type="radio" name="insertType" value="0" onclick="$('#old_student_name, #new_student_name').toggle()"/>
                                [#new_std_reinscription]
                            </h3>
                        </li>
                    </ul>
                </div>
                <fieldset>
                	<legend>[#student]</legend>
                    <input type="hidden" name="std_id" value=""/>
                    <table id="new_student_name" width="100%" cellspacing="1" cellpadding="0" border="0">
                      <tbody>
                        <tr>
                          <td width="120" valign="top">
                            <label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label>
                          </td>
                          <td valign="top" colspan="3" class="def_align">
                            <input type="text" value="[@name]" class="required" name="name">
                          </td>
                        </tr>
                        <tr>
                          <td width="120" valign="top">
                            <label class="label ui-widget-header ui-corner-left reverse_align">[#middle_name]</label>
                          </td>
                          <td valign="top" colspan="3" class="def_align">
                            <input type="text" value="[@middle_name]" name="middle_name" class="input_double" />
                          </td>
                        </tr>
                        <tr>
                          <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#last_name](En)</label></td>
                          <td valign="top" colspan="3" class="def_align"><input type="text" value="[@last_name]"  name="last_name" class="required"></td>
                        </tr>
                        <tr>
                          <td valign="top" class="reverse_align" colspan="3"><input type="text" dir="rtl" class="input_double rev_float required ui-corner-left" value="[@name_ar]" id="name_ar" name="name_ar"></td>
                          <td width="120" valign="middel"><label style="width:120px;" class=" def_align label ui-widget-header ui-corner-right">[#name_ar]</label></td>
                        </tr>
                      </tbody>
                    </table>
                    <table id="old_student_name" class="hidden" width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="120" valign="middel" style="text-align:right"> 
                                <label class="label ui-widget-header ui-corner-left"> [#student_name]</label>
                            </td>
                            <td valign="middel">
                                <input type="text" id="newSugName"  class="input_double"/>
                                <input type="hidden" id="old_std_id" name="id" class="autocomplete_value" />
                            </td>
                        </tr>
                    </table>
            	</fieldset>		
            </form>
		</div>
        <div id="step-2" class="ui-widget-content items" ></div>
        <div id="step-3" class="ui-widget-content items" ></div>
        <div id="step-4" class="ui-widget-content items" ></div>
        <div id="step-5" class="ui-widget-content items" ></div>
	</div>
</div>    