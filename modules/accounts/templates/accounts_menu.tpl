<ul class="nav">

    <li>
    	<a class="ui-state-default hoverable" action="searchAccount">[#search]</a>
    </li>
    <li>
    	<a class="ui-state-default hoverable" action="openBanks" module="banks">[#banks]</a>
        <ul>
        	[@banks_lis]
            <li><a class="ui-state-default hoverable" action="newBank">[#new_bank]</a></li>
        </ul>
    </li>
    <li>
    	<a class="ui-state-default hoverable" action="openClients" module="clients">[#clients]</a>
    </li>
<!--    <li>
    	<a class="ui-state-default hoverable" action="openProviders">[#provides]</a>
    </li>
    <li>
    	<a class="ui-state-default hoverable" action="openIncomes">[#incomes]</a>
    </li>
    <li>
    	<a class="ui-state-default hoverable" action="openExpenses">[#expenses]</a>
    </li>
-->    
	
    <li>
    	<a class="ui-state-default hoverable" action="openTotals">[#total_transactions]</a>
    </li>
    <li>
    	<a class="ui-state-default hoverable" action="openTree">[#tree]</a>
    </li>
</ul>