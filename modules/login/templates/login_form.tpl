<div id="loading_main_div" class="hidden ui-widget-content ui-corner-all">
  <h3>[#loading]</h3>
  <div id="loading_progress">
  </div>
</div>

<div class="ui-widget-content ui-corner-all transparent_div" align="center" style="padding:50px; margin:50px; position:relative">
	
  <h3 class="title" style="position:absolute; bottom:20px; right:20px">[@systemVersion]</h3>
  <form action="index.php" method="post" name="loginForm" id="loginForm" autocomplete="off">
    <img border="0" src="[@logo_path]">
    <h1>[@systemName]</h1>
    <div style="padding:15px; display: inline-flex;" class="ui-state-highlight ui-corner-all">
      <table cellspacing="0" border="0">
        <tbody>
          <tr>
            <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#name]</label></td>
            <td><input type="text" class="ui-state-default ui-corner-right" id="user" name="user" autocomplete="off"></td>
          </tr>
          <tr>
            <td width="100" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#password]</label></td>
            <td><input type="password" class="required ui-state-default ui-corner-right" title="Password" id="pass" name="pass" autocomplete="off"></td>
          </tr>
          <tr>
          	<td>&nbsp;</td>
            <td>
            	<label><input type="checkbox" id="keep_signin" checked="checked" />[#keep_signin]</label>
            </td>
          </tr>
          <tr>
            <td class="reverse_align" colspan="2"><button class="hand hoverable ui-corner-all ui-state-default" type="button" onclick="submitLoginForm()">[#login_but] <span class="ui-icon ui-icon-check"></span></button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </form>
	<div class="ui-state-error ui-corner-all hidden" id="login_error_div" align="center" style="padding:20px; margin:5px 30%;" >[@error]</div>
</div>