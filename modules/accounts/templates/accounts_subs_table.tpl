<div class="ui-corner-all ui-widget-content scoop transparent_div" id="sub_acc_list">
	<div class="toolbox">
    	<a module="accounts" action="newAccount" rel="[@main_code]" title="[#new]">[#new_account]<span class="ui-icon ui-icon-document"></span></a>
        <a action="exportTable" rel="#sub_acc_list">[#export]<span class="ui-icon ui-icon-disk"></span></a>
    	<a rel="#sub_acc_list" class="print_but"><span class="ui-icon ui-icon-print"></span>[#print]</a>
    </div>
    <div class="hidden showforprint ui-state-highlight" style="text-align:center">
    	<h2>[@acc_name]</h2>
        <h3>[#year]: [@report_year]</h3>
    </div>
    <table class="result">
        <thead>
            <tr>
                <th width="20" rowspan="2" class="unprintable">&nbsp;</th>
                <th rowspan="2">[#title]</th>
                <th width="78" rowspan="2">[#code]</th>
                <th width="25" rowspan="2">[#currency]</th>
                <th width="170" colspan="2">[#start_balance]</th>
                <th width="170" colspan="2">[#transactions]</th>
                <th width="170" colspan="2">[#total]</th>
                <th width="170" colspan="2">[#total_transactions]</th>
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