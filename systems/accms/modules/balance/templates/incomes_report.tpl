<div id="incomes_report_div" class="ui-widget-content scoop transparent_div">
    <div class="toolbox">
        <a action="print_pre" rel="#incomes_report_div" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>
    <h2 align="center">[#income_report]</h2>
    <h3 align="center"> [#year] [@cur_year]</h3>
    <table class="result">
    	<thead>
        	<tr>
            	<th>[#title]</th>
                <th width="120">[#code]</th>
                <th width="60">[#currency]</th>
                <th width="120">[#value]</th>
                <th width="120">[#total]</th>
            </tr>
        <tbody>
            <tr>
                <td colspan="5" style="background-color: #e6eeee;"><h4>[#incomes]</h4></td>
            </tr>
            [@incomes_trs]
            <tr>
                <td><h4>[#total_incomes]</h4></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="center"><b>[@total_incomes]</b></td>
            </tr>
            <tr>
                <td colspan="5" style="background-color: #e6eeee;"><h4>[#expenses]</h4></td>
            </tr>
            [@expenses_trs]
            <tr>
                <td><h4>[#total_expenses]</h4></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="center"><b>[@total_expenses]</b></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>[#total_net]</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="center"><b>[@total_net]</b></th>
            </tr>
        </tfoot>
    </table>
</div>