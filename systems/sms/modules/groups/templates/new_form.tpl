<form class="ui-state-highlight ui-corner-all" name="group-infos-[@id]">
  <input type="hidden" value="[@id]" name="id" >
  <input type="hidden" value="[@parent]" name="parent" >
  <input type="hidden" value="[@parent_id]" name="parent_id" >
  <input type="hidden" value="[@editable]" name="editable" >
  <table width="90%" cellspacing="0" border="0">
    <tbody>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align" dir="ltr">[#name]</label></td>
        <td><input type="text" value="[@name]" name="name" class="required input_double"></td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#material]</label></td>
        <td>
            <select name="service_id" class="combobox">
                [@services_select]
            </select>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#resp]</label></td>
        <td>
            <input type="text" class="input_double sug_emp" value="[@resp_name]" name="emp_sug_div" id="emp_sug_div">
            <input type="hidden" value="[@resp]" class="autocomplete_value" id="resp" name="resp">
        </td>
      </tr>
      <tr>
        <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#comments]</label></td>
        <td>
            <textarea name="comments">[@comments]</textarea>
        </td>
      </tr>
    </tbody>
  </table>
</form>