<form class="ui-state-highlight ui-corner-all unprintable " id="new_resource_form">
  <input type="hidden" id="level_id" name="id">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
        <td><input type="text" name="name_ltr" id="level_name_en" class="ui-corner-right"></td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#name_ar]</label></td>
        <td><input type="text" name="name_rtl" id="level_name_ar" class="ui-corner-right"></td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#etab]</label></td>
        <td>
        	<select id="etab_id" class="combobox " name="etab_id">
				[@etabs_options]
          </select>
        </td>
      </tr>
    </tbody>
  </table>
</form>
