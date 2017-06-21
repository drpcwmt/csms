<div id="std_abs_div-[@std_id]">
    <div class="toolbox">
        <a rel="#std_abs_div-[@std_id]" class="print_but"><span class="ui-icon ui-icon-print"></span>[#print]</a>
        <a rel="#std_abs_div-[@std_id]" action="exportTable"><span class="ui-icon ui-icon-disk"></span>[#export]</a>
    </div>
    <form id="std_list_form" class="ui-corner-all ui-state-highlight unprintable" style="padding:5px; margin-bottom:10px">
        <input type="hidden" name="con" value="std" class="ui-corner-right">
        <input type="hidden" name="std_id" value="[@std_id]" class="ui-corner-right">
        <div align="center" class="hidden showforprint">
          <h2>[@name]</h2>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#period]</label></td>
              <td>
                <select update="submitStdAbsentList()" class="combobox">
                    [@periods_opts]
                </select>
               </td>
            </tr>
          </tbody>
        </table>
	</form>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
      <tr>
        <td valign="top">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">[#total]</label></td>
                <td><div class="fault_input">[@std_total_abs]</div></td>
              </tr>
              <tr>
                <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">[#ill_abs_days]</label></td>
                <td><div class="fault_input">[@ill_abs_days]</div></td>
              </tr>
              <tr>
                <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">[#justify]</label></td>
                <td><div class="fault_input">[@justify_abs_days]</div></td>
              </tr>
              <tr>
                <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">[#attendance_rates]</label></td>
                <td><div class="fault_input">[@std_rate]</div></td>
              </tr>
            </tbody>
          </table>
        </td>
        <td>
        	<div id="chartDiv_stdabs">
            	[@chart]
            </div>
        </td>
      </tr>
    </tbody>
  </table>
  [@list]
</div>
