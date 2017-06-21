<page style="margin-top:20px">
	<page_header class="page_header">
    	<img src="attachs/img/[@header]" width="100%" />
    </page_header>
    <div class="ui-widget-content receipt" >
        <h4>[#date]: [@date]</h4>
        <h4>[#recete_no]: [@no]</h4>
      <h2 style="text-align:center">[#receipt]</h2>
        
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                <td valign="middle">
                    <div class="ui-widget-content ui-corner-all" style="padding:4px; font-weight:bold">[@to_name]</div>
                  
                </td>
                <td width="120" valign="middle">
                    <div class="ui-widget-content ui-corner-all" style="padding:4px; font-weight:bold">[@to_code]</div>
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="top" width="120" rowspan="2">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#value]<br /></label></td>
                <td colspan="2" valign="middle">
                    <div class="ui-widget-content ui-corner-all" style="padding:4px; font-weight:bold">[@value] [@currency]</div>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                    <div class="ui-widget-content ui-corner-all" style="padding:4px; font-weight:bold">[@str_value]</div>
                </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel"><span class="label ui-widget-header ui-corner-left reverse_align">[#payment_type]</span></td>
                <td colspan="2" valign="middle">
                    <div class="ui-widget-content ui-corner-all" style="padding:4px; font-weight:bold">[@payment_type]</div>
                    </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="middel" width="120">
                    <label class="label ui-widget-header ui-corner-left reverse_align">[#notes]</label></td>
                <td colspan="2" valign="middle">
                    <div class="ui-widget-content ui-corner-all" style="padding:4px; font-weight:bold">[@recete_notes]</div>
                </td>
              </tr>
        </table>
        <h4 class="reverse_align">[@user_name]</h4>
        <h4 class="reverse_align">[#safe_code]: [@safe_code]</h4>
    </div>
    <page_footer class="page_footer"></page_footer>
</page>