  <div id="halls-infos-[@id]" class="scope">
    <form name="halls-infos-[@id]">
      [@toolbox]
      <div class="ui-corner-all ui-state-highlight">
          <input type="hidden" value="[@id]" name="id" >
          <table border="0" cellspacing="0" width="100%">
            <tbody>
              <tr>
                <td valign="middel" width="120"><label class="label ui-widget-header ui-corner-left">[#hall]</label></td>
                <td><input class="ui-state-default ui-corner-right" id="hall_name" name="name" value="[@name]" type="text"></td>
              </tr>
              <tr>
                <td valign="middel" width="120"><label class="label ui-widget-header ui-corner-left">[#room]</label></td>
                <td><input class="ui-state-default ui-corner-right" id="room_no" name="room_no" value="[@room_no]" type="text"></td>
              </tr>
              <tr>
                <td valign="middel" width="120"><label class="label ui-widget-header ui-corner-left">[#max_size]</label></td>
                <td><input class="ui-state-default ui-corner-right" id="max_size" name="max_size" value="[@max_size]" type="text"></td>
              </tr>
            </tbody>
          </table>
       </div>
    </form>
    <div id="schedule_container"></div>
  </div>
