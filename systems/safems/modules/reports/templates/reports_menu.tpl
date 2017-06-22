<ul class="nav">
    <li>
    	<a class="ui-state-default hoverable" action="openTree">[#tree]</a>
    </li>
    <li>
    	<a class="ui-state-default hoverable" module="accounts" action="openSubAcc" code="[@acc_code]">[#account]</a>
    </li>
    <li>
    	<a class="ui-state-default hoverable" module="safems" action="openDailyReport">[#daily_report]</a>
    </li>
    <li>
    	<a class="ui-state-default hoverable">[#transaction_report]</a>
        <span class="ui-icon ui-icon-triangle-1-s"></span>
        <ul>
            <li>
                <a class="ui-state-default hoverable" module="safems" action="openSafeTransactions" code="[@acc_code]">[#cash]</a>
            </li>
            <li>
                <a class="ui-state-default hoverable" module="safems" action="openBankTrans">[#banks]</a>
            </li>
            <li>
                <a class="ui-state-default hoverable" module="safems" action="searchTrans">[#search]</a>
            </li>
        </ul>
    </li>
    <li>
    	<a class="ui-state-default hoverable">[#recete]</a>
        <ul>
        	<li><a class="ui-state-default hoverable" module="safems" action="RePrintRecete" rel="in">[#ingoing]</a></li>
            <li><a class="ui-state-default hoverable" module="safems" action="RePrintRecete" rel="out">[#outgoing]</a></li>
        </ul>
    </li>
</ul>