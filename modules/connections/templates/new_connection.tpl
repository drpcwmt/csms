<form name="new_connections" >
	<fieldset>
    	<legend>[#new_server]</legend>
          <table border="0" cellspacing="0" width="100%">
            <tbody>
              <tr class="tr_type_hidden">
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#type]</label></td>
                <td valign="top">
                	[@system_type_inp]
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">IP</label></td>
                <td valign="top">
                  <input class="ui-corner-right" name="ip" id="server_ip" value="[@ip]" type="text">
                  </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">URL</label></td>
                <td valign="top">
                  <input class="ui-corner-right" name="url" id="server_name" value="[@url]"  type="text">
                </td>
              </tr>
               <tr>
                <td class="reverse_align" valign="top" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#cost_center]</label></td>
                <td valign="top">
                	<input name="ccid" id="ccid" value="[@ccid]"  type="text">
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="top" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#notes]</label></td>
                <td valign="top">
                 <textarea name="notes">[@notes]</textarea>
                </td>
              </tr>
            </tbody>
          </table>
	</fieldset>
</form>
