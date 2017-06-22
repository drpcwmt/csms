<form class="ui-state-highlight ui-corner-all unprintable " id="new_resource_form">
  <input type="hidden" id="class_id" name="id" class="ui-corner-right">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
        <td><input type="text" name="name_ltr" id="class_name_en" class="ui-corner-right"></td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#name_ar]</label></td>
        <td><input type="text" name="name_rtl" id="class_name_ar" class="ui-corner-right"></td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#level]</label></td>
        <td>
        	<select id="level_id" class="combobox " name="level_id">
				[@levels_options]
          </select>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#resp]</label></td>
        <td>
        	<input type="text" class="input_double ui-corner-right ui-autocomplete-input" id="new_employer_name" >
          	<input type="hidden" name="resp" class="autocomplete_value ui-corner-right">
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#hall]</label></td>
        <td>
        	<select id="room_no" class="combobox " name="room_no">
           		[@halls_options]
          	</select>
        </td>
      </tr>
    </tbody>
  </table>
</form>
