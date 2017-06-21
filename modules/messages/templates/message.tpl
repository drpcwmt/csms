<form name="message-form">
  <div style="padding:5px; font-weight:bold;" class="ui-corner-top ui-state-highlight">
    <input type="hidden" value="[@id]" id="cur_msg_id">
    <table width="100%" class="ui-header">
      <tbody>
        <tr>
          <td><h3>[#from]: [@sender_name]</h3></td>
          <td width="250"><h4 style="margin:2px">[@day_str]</h4></td>
        </tr>
        <tr>
          <td colspan="2"><h4 style="margin:2px">[#to]: [@reciver_name]</h4></td>
        </tr>
        <tr>
          <td colspan="2"><h4 style="margin:2px">[#title]: [@title]</h4></td>
        </tr>
      </tbody>
    </table>
  </div>
  <div style="padding:5px; min-height:200px;" class="ui-corner-bottom ui-widget-content ui-content">
    [@content]
  </div>
</form>
