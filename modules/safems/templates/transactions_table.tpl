<div class="scoop">
	<div class="toolbox">
    	<a action="printScoop">[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>
    <table class="tablesorter">
        <thead>
            <tr>
                <th class="{sorter:false} unprintable" width="20">&nbsp;</th>
                <th width="60">[#debit]</th>
                <th width="60">[#credit]</th>
                <th width="40">[#currency]</th>
                <th width="40">[#payment_type]</th>
                <th>[#account]</th>
                <th width="80">[#code]</th>
                <th width="80">[#cost_center]</th>
                <th class="{sorter: false}">[#notes]</th>
                <th width="40">[#no.]</th>
                <th width="120" class="dateFormat-ddmmyyyy">[#time]</th>
            </tr>
        </thead>
        <tbody>
            [@transactions_trs]
        </tbody>
    </table>   
    [@total_table] 
</div>