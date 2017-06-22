<form style="padding:5px" class="ui-corner-all ui-widget-default " id="addBehaviortForm">
    <table width="100%" border="0" cellspacing="2" cellpadding="0">
      <tr>
        <td valign="top">
            <div style="padding:5px" class="ui-corner-all ui-state-highlight">
                <table cellspacing="0" border="0">
                  <tbody>
                    <tr>
                      <td width="100" valign="middel" class="reverse_align">
                        <label class="label ui-widget-header ui-corner-left ">[#date]</label>
                      </td>
                      <td>
                        <input type="text" name="date" value="[@date]" class="mask-date datepicker" />
                        <input type="checkbox" checked="checked" value="1" name="alert" class="ui-state-default ui-corner-right">
                        [#send_parent_notification]
                      </td>
                    </tr>
                    <tr>
                      <td width="100" valign="middel" class="reverse_align">
                        <label class="label ui-widget-header ui-corner-left ">[#behavior]</label>
                      </td>
                      <td>
                        <div id="behavior_select_div">
                            <a class="[@add_new_behav_hidden] ui-state-default hoverable icon_button" action="addNewPattern" style="width:22px; height:22px"><span class="ui-icon ui-icon-plus"></span></a>
                          <select class="combobox required" name="pattern" style="width:300px">
                            <option value=""></option>
                            [@patterns_opts]
                          </select>
                        </div>
                    </tr>
                    <tr>
                      <td width="100" valign="middel" class="reverse_align">
                        <label class="label ui-widget-header ui-corner-left ">[#sanction]</label>
                      </td>
                      <td>
                        <div id="sanction_select_div">
                            <a class="ui-state-default hoverable icon_button" onClick="$('#sanction_select_div').hide();$('#new_sanction').show();" style="width:22px; height:22px"><span class="ui-icon ui-icon-plus"></span></a>
                          <select class="combobox" name="sanction">
                            <option value=""></option>
                            [@sanctions_opts]
                          </select>
                        </div>
                        <input type="text" id="new_sanction" class="hidden input_double" name="new_sanction">
                    </tr>
                    <tr>
                        <td><label class="label reverse_align ui-widget-header ui-corner-left">[#student]</label></td>
                        <td>
                            <input id="behavior_std_name" type="text" class="input_double"><input id="behavior_std_id_inp" class="autocomplete_value" type="hidden">
                            <button type="button" action="behaviorBrowseStds" class="ui-state-default hoverable"><span class="ui-icon ui-icon-extlink "></span>[#browse]</button>
                        </td>
                    </tr>
                  </tbody>
                </table>
              <fieldset class="ui-widget-content ui-corner-all">
                    <legend class="ui-widget-header ui-corner-all">[#notes]</legend>
                    <textarea id="comments" rows="3" style="width: 99%;" name="notes">[@notes]</textarea>
              </fieldset>
           </div>      
        </td>
        <td width="40%" valign="top">
             <fieldset class="ui-widget-content ui-corner-all">
                <legend class="ui-widget-header ui-corner-all">[#student]</legend>
                  <ul id="tot_con_text" class="ui-corner-all ui-widget-content">
                    [@cons_list]
                  </ul>
              </fieldset>              
        </td>
      </tr>
    </table>
</form>