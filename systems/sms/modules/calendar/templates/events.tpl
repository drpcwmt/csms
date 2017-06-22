<table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td width="40%" valign="top">
    	<form style="padding:5px" class="ui-corner-all ui-widget-default " id="addEventForm">
          <input type="hidden" name="tot_con" id="tot_con" value="[@tot_con]" />
          <input type="hidden" value="[@id]" id="event_id" name="id" class="ui-state-default ui-corner-right">
          <div style="padding:5px" class="ui-corner-all ui-state-highlight">
            <table cellspacing="0" border="0">
              <tbody>
                <tr>
                  <td width="100" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left ">[#event]</label>
                  </td>
                  <td>
                    <div id="event_select_div">
                        <a onClick="$('#event_select_div').hide();$('#new_type').show();" style="width:22px; height:22px" class="icon_button hoverable ui-state-default"><span class="ui-icon ui-icon-plus"></span></a>
                      <select class="combobox " onChange="evalEventType()" id="event_type" name="event_type">
                        <option value=""></option>
                        [@events_options]
                      </select>
                    </div>
                    <input type="text" id="new_type" class="hidden input_double ui-state-default ui-corner-right" name="new_type"></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td><input type="checkbox" checked="checked" value="1" name="alert" class="ui-state-default ui-corner-right">
                    Send messages to alert</td>
                </tr>
              </tbody>
            </table>
          </div>
          <table width="100%">
            <tbody>
              <tr>
                <td>
                <fieldset class="ui-widget-content ui-corner-all">
                    <legend class="ui-widget-header ui-corner-all">[#date]</legend>
                    <table cellspacing="1" border="0">
                      <tbody>
                        <tr>
                          <td width="60" valign="middel" class="reverse_align">
                            <label class="label ui-widget-header ui-corner-left">[#from]</label>
                          </td>
                          <td><input type="text"  value="[@begin_date]" id="from" name="begin_date" class="datepicker mask-date required"></td>
                        </tr>
                        <tr>
                          <td width="60" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#till]</label></td>
                          <td><input type="text" value="[@end_date]" id="till" name="end_date" class="datepicker mask-date" /></td>
                        </tr>
                      </tbody>
                    </table>
                  </fieldset></td>
              </tr>
              <tr>
                <td width="50%"><fieldset id="time_fieldset" class="ui-widget-content ui-corner-all">
                  <legend class="ui-widget-header ui-corner-all">[#time]</legend>
                  <table width="100%" cellspacing="1" border="0">
                    <tbody>
                      <tr>
                        <td width="60" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#from]</label></td>
                        <td><input type="text" id="event_from" name="begin_time" class="mask-time half_input" value="[@begin_time]"/></td>
                      </tr>
                      <tr>
                        <td width="60" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#till]</label></td>
                        <td><input type="text" id="event_till" name="end_time" class="mask-time half_input" value="[@end_time]" /></td>
                      </tr>
                    </tbody>
                  </table>
                </fieldset></td>
              </tr>
            </tbody>
          </table>
          <fieldset class="ui-widget-content ui-corner-all">
            <legend class="ui-widget-header ui-corner-all">[#comments]</legend>
            <textarea id="comments" rows="3" style="width: 99%;" name="comments">[@comments]</textarea>
          </fieldset>
        </form>
    </td>
    <td valign="top">
      <fieldset class="ui-widget-content ui-corner-all">
        <legend class="ui-widget-header ui-corner-all">[#concerning]</legend>
        	<table width="100%">
            	<tr>
                	<td width="154" valign="top"> 
                    	<fieldset class="ui-corner-all ui-state-highlight">
                        	<legend>[#add]</legend>
                        	[@con_menu]
                        </fieldset>
                    </td>
                 	<td valign="top">
                      <ul id="tot_con_text" class="ui-corner-all ui-widget-content">
                      [@cons_list]
                      </ul>
                    </td>
                </tr>
            </table>
      </fieldset>
    </td>
  </tr>
</table>

