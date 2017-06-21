<form name="inscription_form" class="ui-corner-all ui-state-highlight" style="padding:7px">
    <input type="hidden" name="status" value="1" />
    <input type="hidden" name="id" value="[@std_id]" />
    <table width="100%" cellspacing="1" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
          <td valign="top" colspan="2" class="def_align">
            <input type="text" class="ui-state-default ui-corner-right mask-date datepicker" name="join_date" value="[@date]">
          </td>
        </tr>
        <tr>
          <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#class]</label></td>
          <td valign="top" colspan="2" class="def_align">
            <select name="class_id" class="combobox required">[@classes_opts]</select>
          </td>
        </tr>
      </tbody>
    </table>
</form>
