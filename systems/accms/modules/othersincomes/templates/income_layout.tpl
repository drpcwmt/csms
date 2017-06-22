<h2>[@title]</h2>
<div class="tabs">
	<ul>
    	<li><a href="#transaction_tab">[#transactions]</a></li>
        <li><a href="#settings">[#settings]</a></li>
    </ul>
    <div id="transaction_tab">
        <div class="ui-state-highlight">
            <table class="result">
                <thead>
                    <tr>
                        <th width="100">[#currency]</th>
                        <th>[#expenses]</th>
                        <th>[#incomes]</th>
                        <th>[#profit]</th>
                    </tr>
                </thead>
                <tbody>
                    [@totals_trs]
                </tbody>
            </table>
        </div>
        
        <table class="tablesorter">
            <thead>
                <tr>
                    <th class="unprintable" width="20">&nbsp;</th>
                    <th>[#code]</th>
                    <th>[#debit]</th>
                    <th>[#credit]</th>
                    <th>[#currency]</th>
                    <th>[#date]</th>
                    <th>[#notes]</th>
                </tr>
            </thead>
            <tbody>
                [@transactions_trs]
            </tbody>
        </table>    
    </div>
    <div id="settings">
    	[@settings_tab]
    </div>
</div>    