<div id="financial_report_div" class="ui-widget-content scoop transparent_div">
    <div class="toolbox">
        <a action="print_pre" rel="#financial_report_div" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>
    <h2 align="center">[#financial_report]</h2>
    <h3 align="center"> [#year] [@cur_year]</h3>
    <table class="tableinput">
        <tbody>
            <tr>
                <td valign="top" style="background-color: #e6eeee; vertical-align:top">
                    <h4>[#assets]</h4>
                    <table width="100%">
                        [@assets_items]
                    </table>
                </td>
                <td valign="top" style="background-color: #e6eeee; vertical-align:top">
                    <h4>[#dues] [#ownership]</h4>
                    <table width="100%" cellspacing="1">
                        [@dues_items]
                        <tr>
                        	<td><b>[#net_profit]</b></td>
                            <td align="center"><b>EGP</b></td>
                            <td>&nbsp;</td>
                            <td><b>[@profit]</b></td>
                        </tr>
                    </table>
                 </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>
                    [@total_assets]
                </th>
                <th>
                    [@total_dues]
                </th>
            </tr>
        </tfoot>
    </table>
</div>