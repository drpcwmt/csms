<div class="tabs">
	<ul>
        <li><a href="#infos_div">[@name_ltr]</a></li>
        <li><a href="#skills_tab">[#skills]</a></li>
	</ul>
    <div id="infos_div">
        [@toolbox]
        <form class="ui-state-highlight ui-corner-all" name="materials-infos-[@id]" id="materials-infos-[@id]">
          <input type="hidden" value="[@id]" name="id" />
          <table width="100%">
            <tbody>
              <tr>
                <td valign="top">
                    <table width="100%" cellspacing="0" border="0">
                      <tr>
                        <td width="120" valign="middel">
                            <label class="label">[#name]</label></td>
                        <td><input type="text" value="[@name_ltr]" name="name_ltr" id="mat_name_en"></td>
                        <td width="120" valign="middel"><label class="label">color</label></td>
                        <td>
                            <select name="color" class="color_picker" id="mat_color">
                                [@color_picker_pallette]
                            </select>
                        </td>
                      </tr>
                      <tr>
                        <td width="120" valign="middel"><label class="label" dir="rtl">[#name_ar]</label></td>
                        <td><input type="text" value="[@name_rtl]" name="name_rtl"></td>
                        <td width="120" valign="middel"><label class="label">[#group]</label></td>
                        <td>
                            <select id="group_id" class="combobox" name="group_id">
                                [@materials_groups]
                            </select>
                       </td>
                      </tr>
                      <tr>
                        <td width="120" valign="middel"><label class="label" dir="rtl">[#full_name]</label></td>
                        <td colspan="3"><input type="text" value="[@full_name]" name="full_name" class="input_double"></td>
                      </tr>
                  </table>
                </td>
                <td width="330">
                	<ul style="list-style:none; margin:0; padding:0">
                    <li>
                      <label>
                        <input type="checkbox" checked="ckecked" onClick="setServiceOption('schedule', [@id], this)" value="1" name="schedule">
                        [#set_schedule_for_all]</label>
                    </li>
                    <li>
                      <label>
                        <input type="checkbox" checked="ckecked" onClick="setServiceOption('mark', [@id], this)" value="1" name="mark">
                        [#set_mark_for_all]</label>
                    </li>
                    <li>
                      <label>
                        <input type="checkbox" onClick="setServiceOption('optional', [@id], this)" value="1" name="optional">
                        [#set_optional_for_all]</label>
                    </li>
                    <li>
                      <label>
                        <input type="checkbox" onClick="setServiceOption('bonus', [@id], this)" value="1" name="bonus">
                        [#set_bonus_for_all]</label>
                    </li>
                  </ul></td>
              </tr>
            </tbody>
          </table>
        </form>
        [@service_table]
	</div>
    <div id="skills_tab">
        <form name="skill_form">
            <div class="toolbox">
                <a action="newMaterialSub" mat_id="[@id]">
                    <span class="ui-icon ui-icon-plus"></span>
                    [#new]
                </a>
            </div>
            <div class="accordion" id="material-sub-[@id]" >
                [@items]
            </div>
        </form>    	
    </div>
</div>