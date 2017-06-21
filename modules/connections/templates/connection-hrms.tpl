<table width="100%">
	<tr>
    	<td width="50%" valign="top">
             <fieldset >
              <legend class="ui-widget-header ui-corner-all">[#server_busms]</legend>
              <table border="0" cellspacing="0">
                <tbody>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120">
                        <label class="label ui-widget-header ui-corner-left reverse_align">[#enable]</label>
                    </td>
                    <td valign="top"><span class="buttonSet">
                      <input class="ui-corner-right" name="busms_server" id="busms_server1" [@busms_server_on] value="1" type="radio">
                      <label for="busms_server1">[#on]</label>
                      
                      <input name="busms_server" id="busms_server0" value="0" [@busms_server_off] type="radio">
                      <label for="busms_server0">[#off]</label>
                      </span></td>
                  </tr>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">IP</label></td>
                    <td valign="top"><span class="buttonSet  MS_buttonset">
                      <input class="ui-corner-right" name="busms_server_ip" id="busms_server_ip" value="[@busms_server_ip]" [@busms_server_ip_disabled] type="text">
                      </span></td>
                  </tr>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">URL</label></td>
                    <td valign="top"><span class="buttonSet MS_buttonset">
                      <input class="ui-corner-right" name="busms_server_name" id="busms_server_name" value="[@busms_server_name]" [@hrms_server_name_disabled] type="text">
                      </span></td>
                  </tr>
                </tbody>
              </table>
         </fieldset>
 
              <fieldset >
              <legend class="ui-widget-header ui-corner-all">[#server_libms]</legend>
              <table border="0" cellspacing="0">
                <tbody>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120">
                        <label class="label ui-widget-header ui-corner-left reverse_align">[#enable]</label>
                    </td>
                    <td valign="top"><span class="buttonSet">
                      <input class="ui-corner-right" name="libms_server" id="libms_server1" [@libms_server_on] value="1" type="radio">
                      <label for="libms_server1">[#on]</label>
                      
                      <input name="libms_server" id="libms_server0" value="0" [@libms_server_off] type="radio">
                      <label for="libms_server0">[#off]</label>
                      </span></td>
                  </tr>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">IP</label></td>
                    <td valign="top"><span class="buttonSet  MS_buttonset">
                      <input class="ui-corner-right" name="libms_server_ip" id="libms_server_ip" value="[@libms_server_ip]" [@libms_server_ip_disabled] type="text">
                      </span></td>
                  </tr>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">URL</label></td>
                    <td valign="top"><span class="buttonSet MS_buttonset">
                      <input class="ui-corner-right" name="libms_server_name" id="libms_server_name" value="[@libms_server_name]" [@hrms_server_name_disabled] type="text">
                      </span></td>
                  </tr>
                </tbody>
              </table>
         </fieldset>
          
         </td>
      	<td valign="top">
            <fieldset >
              <legend class="ui-widget-header ui-corner-all">[#server_safems]</legend>
              <table border="0" cellspacing="0">
                <tbody>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120">
                        <label class="label ui-widget-header ui-corner-left reverse_align">[#enable]</label>
                    </td>
                    <td valign="top"><span class="buttonSet">
                      <input class="ui-corner-right" name="safems_server" id="safems_server1" [@safems_server_on] value="1" type="radio">
                      <label for="safems_server1">[#on]</label>
                      
                      <input name="safems_server" id="safems_server0" value="0" [@safems_server_off] type="radio">
                      <label for="safems_server0">[#off]</label>
                      </span></td>
                  </tr>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">IP</label></td>
                    <td valign="top"><span class="buttonSet  MS_buttonset">
                      <input class="ui-corner-right" name="safems_server_ip" id="safems_server_ip" value="[@safems_server_ip]" [@safems_server_ip_disabled] type="text">
                      </span></td>
                  </tr>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">URL</label></td>
                    <td valign="top"><span class="buttonSet  MS_buttonset">
                      <input class="ui-corner-right" name="safems_server_name" id="safems_server_name" value="[@safems_server_name]" [@safems_server_name_disabled] type="text">
                      </span></td>
                  </tr>
                </tbody>
              </table>
         </fieldset>

        <fieldset >
          <legend class="ui-widget-header ui-corner-all">[#server_accms]</legend>
          <table border="0" cellspacing="0">
            <tbody>
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#enable]</label>
                </td>
                <td valign="top"><span class="buttonSet">
                  <input class="ui-corner-right" name="accms_server" id="accms_server1" [@accms_server_on] value="1" type="radio">
                  <label for="accms_server1">[#on]</label>
                  
                  <input name="accms_server" id="accms_server0" value="0" [@accms_server_off] type="radio">
                  <label for="accms_server0">[#off]</label>
                  </span></td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">IP</label></td>
                <td valign="top"><span class="buttonSet  MS_buttonset">
                  <input class="ui-corner-right" name="accms_server_ip" id="accms_server_ip" value="[@accms_server_ip]" [@accms_server_ip_disabled] type="text">
                  </span></td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">URL</label></td>
                <td valign="top"><span class="buttonSet  MS_buttonset">
                  <input class="ui-corner-right" name="accms_server_name" id="accms_server_name" value="[@accms_server_name]" [@accms_server_name_disabled] type="text">
                  </span></td>
              </tr>
            </tbody>
          </table>
        </fieldset>
		</td>
     </tr>
</table>
[@servers_table]