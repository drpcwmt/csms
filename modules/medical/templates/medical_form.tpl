<form action="" method="get" name="medical_form">
	<div class="ui-corner-all ui-state-highlight">
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
            <td colspan="3">
                <input name="student_name" type="text" class="input_double required" value="[@student_name]" />
                <input type="hidden" name="std_id" value="[@std_id]" class="autocomplete_value" />
            </td>
            </tr>
          <tr>
            <td>
                <label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label>
            </td>
            <td><input type="text" name="visit_date" id="visit_date" class="mask-date datepicker" /></td>
            <td>
                <label class="label ui-widget-header ui-corner-left reverse_align">[#time]</label>
            </td>
            <td><input type="text" name="visit_time" id="visit_time" class="mask-time input_half" /></td>
          </tr>
        </table>
	</div>    
<fieldset style="margin-top:5px">
	<legend>[#symptoms]</legend>
    <textarea name="symptoms"></textarea>
</fieldset>

<fieldset>
	<legend>[#response]</legend>
    <textarea name="response"></textarea>
</fieldset>
</form>