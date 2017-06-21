<fieldset >
  <legend class="ui-widget-header ui-corner-all">[#server_hrms]</legend>
  <table border="0" cellspacing="0">
    <tbody>
      <tr>
        <td class="reverse_align" valign="middel" width="120">
        	<label class="label ui-widget-header ui-corner-left reverse_align">[#enable]</label>
        </td>
        <td valign="top"><span class="buttonSet">
          <input class="ui-corner-right" name="hrms_server" id="hrms_server1" [@hrms_server_on] value="1" type="radio">
          <label for="hrms_server1">[#on]</label>
          
          <input name="hrms_server" id="hrms_server0" value="0" [@hrms_server_off] type="radio">
          <label for="hrms_server0">[#off]</label>
          </span></td>
      </tr>
      <tr>
        <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">IP</label></td>
        <td valign="top"><span class="buttonSet ui-buttonset MS_buttonset">
          <input class="ui-corner-right" name="hrms_server_ip" id="hrms_server_ip" value="[@hrms_server_ip]" [@hrms_server_ip_disabled] type="text">
          </span></td>
      </tr>
      <tr>
        <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">URL</label></td>
        <td valign="top"><span class="buttonSet ui-buttonset MS_buttonset">
          <input class="ui-corner-right" name="hrms_server_name" id="hrms_server_name" value="[@hrms_server_name]" [@hrms_server_name_disabled] type="text">
          </span></td>
      </tr>
    </tbody>
  </table>
</fieldset>
<div class="toolbox">
    <a module="connections" action="newConnection">[#new]<span class="ui-icon ui-icon-document"></span></a>
 </div>
[@servers_table]
