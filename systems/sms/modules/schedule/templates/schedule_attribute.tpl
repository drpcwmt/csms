<form name="attribute_lesson_form">
	<fieldset  class="ui-corner-all ui-state-highlight">
        <table width="100%" cellpadding="0">
            <tr>
                <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#frequency]</label></td>
                <td>
                    <ul style="list-style:none; padding:0; margin:0">
                        <li style="display:inline; height:14px; padding:4px" action="setFrequency" value="1" class="ui-state-default hoverable clickable hand [@frequency-1_class]" >[#frequnecy-1]</li><li style="display:inline; height:14px; padding:4px" action="setFrequency" value="2" class="ui-state-default hoverable clickable hand [@frequency-2_class]" >[#frequnecy-2]</li><li style="display:inline; height:14px; padding:4px" action="setFrequency" value="4"  class="ui-state-default hoverable clickable ui-corner-right hand [@frequency-4_class]" >[#frequnecy-4]</li>
                    </ul>
                </td>
            </tr>
            <tr class="[@division_tr_class]">
                <td valign="middel" width="100"><label class="label ui-widget-header ui-corner-left">[#students]</label></td>
                <td>
                    <ul style="list-style:none; padding:0; margin:0" class="group_division_ul">
                        <li style="display:inline; height:14px; padding:4px" action="setDivision" value="1" class="ui-state-default hoverable hand clickable [@division-1_class]" >[#all_student]</li><li style="display:inline; height:14px; padding:4px" action="setDivision" value="2" class="ui-state-default clickable hoverable hand ui-corner-right [@division-2_class]" >[#groups]</li>
                    </ul>
                </td>
            </tr>
        </table>
    </fieldset>
    <div id="schedule_weeks_div">
    	<table cellspacing="2" class="tableinput" id="lessons_table">
        	[@lessons_content]
        </table>
    </div>
</form>