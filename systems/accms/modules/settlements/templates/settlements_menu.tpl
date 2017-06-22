<ul class="nav">
	<li class="[@insert_transactions]">
    	<a class="ui-state-default hoverable" module="settlements" action="newSettlDiff">[#new_settlement_manual]</a>
    </li>
    <!--<li class="[@insert_transactions]">
    	<a class="ui-state-default hoverable" module="settlements" action="newAssets">[#new_assets]</a>
    </li>
    <li class="[@insert_transactions]">
    	<a class="ui-state-default hoverable">[#banks_accounts]</a>
        <span class="ui-icon ui-icon-triangle-1-s"></span>
        <ul>
        	<li><a class="ui-state-default hoverable" module="settlements" action="newSettlBank" rel="out">[#bankout]</a></li>
            <li><a class="ui-state-default hoverable" module="settlements" action="newSettlBank" rel="in">[#bankin]</a></li>
        </ul>
    </li>
    <li class="[@capital_transactions]">
    	<a class="ui-state-default hoverable " module="settlements" action="newSettlCapital" >[#capital]</a>
    </li>-->
    <li class="[@insert_transactions]">
    	<a class="ui-state-default hoverable" module="settlements" action="newExchange">[#exchange]</a>
    </li>
    <li class="[@read_transactions]">
    	<a class="ui-state-default hoverable" module="settlements" action="transDailyList">[#settlement_daily_list]</a>
    </li>
    <li class="[@read_transactions]">
        <a class="ui-state-default hoverable">[#search]</a>
        <span class="ui-icon ui-icon-triangle-1-s"></span>
        <ul>
        	<li><a class="ui-state-default hoverable" module="settlements" action="searchTransById" >[#by_id]</a></li>
            <li><a class="ui-state-default hoverable" module="settlements" action="searchTransAdv" >[#advanced]</a></li>
        </ul>
    </li>
</ul>