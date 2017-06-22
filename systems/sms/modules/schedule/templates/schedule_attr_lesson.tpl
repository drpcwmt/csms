<fieldset style="width:250px">
    <input type="hidden" name="rule[]" value="[@rule]" />
    <legend>[#week] [@week_no]</legend>
    <table width="100%" cellpadding="0" cellspacing="0"  class="lesson_table" >
        <tr class="division_tr [@group_hide_class]">
            <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#group]</label></td>
            <td>
                <select name="lesson_con_id[]" update="reloadService" class="required">
                    [@avaible_student_options]
                </select>
            </td>
        </tr>
        <tr>
            <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#material]</label></td>
            <td>
                <select name="service[]" update="reloadProfs" class="required">
                    [@avaible_services_options]
                </select>
            </td>
        </tr>
        <tr>
            <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#prof]</label></td>
            <td>
                <select name="profs[]"  update="reloadHalls" class="required">
                    [@avaible_profs_options]
                </select>
            </td>
        </tr>
        <tr>
            <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#hall]</label></td>
            <td>
                <select name="halls[]">
                    [@avaible_halls_options]
                </select>
            </td>
        </tr>
    </table>
</fieldset>
