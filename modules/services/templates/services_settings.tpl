<div id="services_settings_div-[@id]">
	<div class="toolbox">
    	<a action="saveServiceSettings" serviceid="[@id]">
        	<span class="ui-icon ui-icon-disk"></span>
            [#save]
        </a>
    </div>
	<form class="ui-state-highlight ui-corner-all ">
  		<input type="hidden" id="service_id" name="id" value="[@id]">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#schedule]</label></td>
        <td>
        	<span class="buttonSet">
				<input type="radio"  name="schedule" id="schedule1-[@id]" value="1" [@schedule_on] /><label for="schedule1-[@id]" >[#on]</label>
				<input type="radio"  name="schedule" id="schedule0-[@id]" value="0" [@schedule_off] /><label for="schedule0-[@id]">[#off]</label>'
			</span>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#marks]</label></td>
        <td>
        	<span class="buttonSet">
				<input type="radio"  name="mark" id="mark1-[@id]" value="1" [@mark_on] /><label for="mark1-[@id]" >[#on]</label>
				<input type="radio"  name="mark" id="mark0-[@id]" value="0" [@mark_off] /><label for="mark0-[@id]">[#off]</label>'
			</span>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#gradding_shell]</label></td>
        <td>
				<button module="marks" action="newGrading" class="ui-state-default hoverable def_float" style="padding:2px">
					<span class="ui-icon ui-icon-plus"></span>
                </button>
				<button module="marks" action="viewGrading" class="ui-state-default hoverable def_float" style="padding:2px">
					<span class="ui-icon ui-icon-extlink"></span>
                </button>
                <select name="gradding" id="grading_list" class="combobox">[@gradin_opts]</select>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#optional]</label></td>
        <td>
        	 <span class="buttonSet">
				<input type="radio"  name="optional" id="optional1-[@id]" value="1" [@optional_on] /><label for="optional1-[@id]" >[#on]</label>
				<input type="radio"  name="optional" id="optional0-[@id]" value="0" [@optional_off] /><label for="optional0-[@id]">[#off]</label>
			</span>
        </td>
      </tr>

      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#bonus]</label></td>
        <td>
        	<span class="buttonSet">
				<input type="radio"  name="bonus" id="bonus1-[@id]" value="1" [@bonus_on] /><label for="bonus1-[@id]" >[#on]</label>
				<input type="radio"  name="bonus" id="bonus0-[@id]" value="0" [@bonus_off] /><label for="bonus0-[@id]">[#off]</label>
			</span>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#coeffcient]</label></td>
        <td>
        	<input type="text" class="input_half"name="coef" value="[@coef]" />
        </td>
      </tr>
      <tr>
        <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#exam_no]</label></td>
        <td><input type="text" class="input_half" name="exam_no" value="[@exam_no]" /></td>
      </tr>
      <tr>
        <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#target]</label></td>
        <td><input type="text" class="input_half" name="target" value="[@target]" /></td>
      </tr>
      <tr>
        <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#min]</label></td>
        <td><input type="text" class="input_half" name="min" value="[@min]" /></td>
      </tr>
    </tbody>
  </table>
</form>
</div>
    	