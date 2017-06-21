<div class="ui-corner-all ui-widget-content scoop transparent_div" id="total_balance_list">
	<div class="toolbox">
    	<a rel="#total_balance_list" class="print_but"><span class="ui-icon ui-icon-print"></span>[#print]</a>
    </div>
    <div class="hidden showforprint ui-state-highlight" style="text-align:center">
    	<h2>[#total_transactions]</h2>
        <h3>[#year]: [@report_year]</h3>
    </div>
    <table class="result">
        <thead>
            <tr>
                <th width="20" rowspan="2" class="unprintable">&nbsp;</th>
                <th rowspan="2">[#title]</th>
                <th width="80" rowspan="2">[#code]</th>
                <th width="40" rowspan="2">[#currency]</th>
                <th width="180" colspan="2">[#start_balance]</th>
                <th width="180" colspan="2">[#transactions]</th>
                <th width="180" colspan="2">[#total]</th>
                <th width="180" colspan="2">[#total_transactions]</th>
            </tr>
            <tr>
                <th>[#debit]</th>
                <th>[#credit]</th>
                <th>[#debit]</th>
                <th>[#credit]</th>
                <th>[#debit]</th>
                <th>[#credit]</th>
                <th>[#debit]</th>
                <th>[#credit]</th>
            </tr>
        </thead>
        <tbody>
            [@balance_rows]
        </tbody>
        [@tfoot]
    </table>	
</div>