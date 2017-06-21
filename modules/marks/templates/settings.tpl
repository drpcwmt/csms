<div class="ui-corner-all ui-widget-content setting_divs hidden" id="setting_marks">
  <fieldset class="ui-widget-content ui-corner-all">
    <table>
      <tbody>
        <tr>
          <td width="220" valign="middel" class="reverse_align">
          	<label class="label ui-widget-header ui-corner-left reverse_align">[#auto_approv_exp]</label>
          </td>
          <td valign="top">
          	<span class="buttonSet">
            <input type="radio" [@autoapprove_on_chk] value="1" id="auto_approv1" name="auto_approv" />
            <label for="auto_approv1">[#on]</label>
            <input type="radio" [@autoapprove_off_chk] value="0" id="auto_approv0" name="auto_approv" />
            <label for="auto_approv0">[#off]</label>
            </span></td>
        </tr>
        <tr>
          <td valign="middel" class="reverse_align">
          	<label class="label ui-widget-header ui-corner-left reverse_align">[#std_can_see_unlocked_term]</label>
          </td>
          <td valign="top"><span class="buttonSet ui-buttonset MS_buttonset">
            <input type="radio" value="1" id="std_can_see_unlocked_term1" name="std_can_see_unlocked_term" class="ui-helper-hidden-accessible ui-corner-right">
            <label for="std_can_see_unlocked_term1" class="ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left" role="button" aria-disabled="false" aria-pressed="false"><span class="ui-button-text">On</span></label>
            <input type="radio" checked="checked" value="0" id="std_can_see_unlocked_term0" name="std_can_see_unlocked_term" class="ui-helper-hidden-accessible ui-corner-right">
            <label for="std_can_see_unlocked_term0" class="ui-state-active ui-button ui-widget ui-state-default ui-button-text-only ui-corner-right" role="button" aria-disabled="false" aria-pressed="true"><span class="ui-button-text">Off</span></label>
            </span></td>
        </tr>
        <tr>
          <td valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left reverse_align">Students and parents can see exams schedules for exam that haven't passed yet.</label></td>
          <td valign="top"><span class="buttonSet ui-buttonset MS_buttonset">
            <input type="radio" checked="checked" value="1" id="std_can_see_preset_exams1" name="std_can_see_preset_exams" class="ui-helper-hidden-accessible ui-corner-right">
            <label for="std_can_see_preset_exams1" class="ui-state-active ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left" role="button" aria-disabled="false" aria-pressed="true"><span class="ui-button-text">On</span></label>
            <input type="radio" value="0" id="std_can_see_preset_exams0" name="std_can_see_preset_exams" class="ui-helper-hidden-accessible ui-corner-right">
            <label for="std_can_see_preset_exams0" class="ui-button ui-widget ui-state-default ui-button-text-only ui-corner-right" role="button" aria-disabled="false" aria-pressed="false"><span class="ui-button-text">Off</span></label>
            </span></td>
        </tr>
        <tr>
          <td valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left reverse_align">Teacher can read student marks for other subject.</label></td>
          <td valign="top"><span class="buttonSet ui-buttonset MS_buttonset">
            <input type="radio" value="1" id="prof_can_see_other_marks1" name="prof_can_see_other_marks" class="ui-helper-hidden-accessible ui-corner-right">
            <label for="prof_can_see_other_marks1" class="ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left" role="button" aria-disabled="false" aria-pressed="false"><span class="ui-button-text">On</span></label>
            <input type="radio" checked="checked" value="0" id="prof_can_see_other_marks0" name="prof_can_see_other_marks" class="ui-helper-hidden-accessible ui-corner-right">
            <label for="prof_can_see_other_marks0" class="ui-state-active ui-button ui-widget ui-state-default ui-button-text-only ui-corner-right" role="button" aria-disabled="false" aria-pressed="true"><span class="ui-button-text">Off</span></label>
            </span></td>
        </tr>
      </tbody>
    </table>
  </fieldset>
  <fieldset class="ui-widget-content ui-corner-all">
    <legend class="ui-widget-header ui-corner-all">Reports</legend>
    <table width="100%" cellspasing="0" ,="">
      <tbody>
        <tr>
          <td valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left reverse_align">Add the grading scale to report</label></td>
          <td valign="top"><span class="buttonSet ui-buttonset MS_buttonset">
            <input type="radio" checked="checked" value="1" id="add_grad_to_cert1" name="add_grad_to_cert" class="ui-helper-hidden-accessible ui-corner-right">
            <label for="add_grad_to_cert1" class="ui-state-active ui-button ui-widget ui-state-default ui-button-text-only ui-corner-left" role="button" aria-disabled="false" aria-pressed="true"><span class="ui-button-text">On</span></label>
            <input type="radio" value="0" id="add_grad_to_cert0" name="add_grad_to_cert" class="ui-helper-hidden-accessible ui-corner-right">
            <label for="add_grad_to_cert0" class="ui-button ui-widget ui-state-default ui-button-text-only ui-corner-right" role="button" aria-disabled="false" aria-pressed="false"><span class="ui-button-text">Off</span></label>
            </span></td>
        </tr>
        <tr>
          <td width="160" valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left reverse_align">Report remarks</label></td>
          <td valign="top"><textarea name="cert_remarks">The letter grade on this report card is based on a variety of assesments used throughout the quarter.  These include; homework, class participation, lab reports, projects, tests, quizzes, portfolios and major assignments.</textarea></td>
        </tr>
        <tr>
          <td valign="top" class="reverse_align"><label class="label ui-widget-header ui-corner-left reverse_align">Generate options</label></td>
          <td valign="top"><ul style="list-style:none; padding:0; margin:0">
              <li style="padding:3px; width:150px" class="ui-corner-all ui-state-default">
                <input type="checkbox" checked="checked" name="option_generate_head" value="1" class="ui-corner-right">
                Head page</li>
              <li style="padding:3px; width:150px" class="ui-corner-all ui-state-default">
                <input type="checkbox" checked="checked" name="option_generate_cert" value="1" class="ui-corner-right">
                Reports</li>
              <li style="padding:3px; width:150px" class="ui-corner-all ui-state-default">
                <input type="checkbox" checked="checked" name="option_generate_skills" value="1" class="ui-corner-right">
                Skills</li>
              <li style="padding:3px; width:150px" class="ui-corner-all ui-state-default">
                <input type="checkbox" checked="checked" name="option_generate_exams" value="1" class="ui-corner-right">
                Exams results</li>
              <li style="padding:3px; width:150px" class="ui-corner-all ui-state-default">
                <input type="checkbox" checked="checked" name="option_generate_appr" value="1" class="ui-corner-right">
                Appreciations</li>
              <li style="padding:3px; width:150px" class="ui-corner-all ui-state-default">
                <input type="checkbox" checked="checked" name="option_generate_absents" value="1" class="ui-corner-right">
                Absents</li>
              <li style="padding:3px; width:150px" class="ui-corner-all ui-state-default">
                <input type="checkbox" checked="checked" name="option_generate_behavior" value="1" class="ui-corner-right">
                Behavior</li>
            </ul></td>
        </tr>
      </tbody>
    </table>
  </fieldset>
</div>
