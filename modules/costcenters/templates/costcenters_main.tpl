 <h3 class="title">[#cost_centers]</h3>
<div class="tabs">
	<ul>
    	<li><a href="#costcenter_list">[#list]</a></li>
        <li><a href="#costcenter_groups">[#groups]</a></li>
    </ul>
    <div id="costcenter_list">
        <div class="toolbox">
            <a module="costcenters" action="newCC">[#new]<span class="ui-icon ui-icon-plus"></span></a>
            <a action="print_pre" rel="#costcenter_list" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
        </div>
        <table class="result">
            <thead>
                <tr>
                    <th class="unprintable" width="20">&nbsp;</th>
                    <th width="60">[#code]</th>
                    <th width="180">[#name]</th>
                    <th width="120">[#expenses]</th>
                    <th width="120">[#incomes]</th>
                    <th width="120">[#net_profit]</th>
                    <th width="120">[#students]</th>
                    <th>[#notes]</th>
                </tr>
            </thead>
            <tbody>
                [@rows]
            </tbody>
            <tbody>
                [@tfoot]
            </tbody>
        </table>	
    </div>
	<div id="costcenter_groups">
        <div class="toolbox">
            <a module="costcenters" action="newCCgroup">[#new]<span class="ui-icon ui-icon-plus"></span></a>
            <a action="print_pre" rel="#costcenter_groups" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
        </div>
    	<table class="result">
        	<thead>
            	<tr>
                	<th width="20" class="{sorter:false} unprintable">&nbsp;</th>
                    <th width="20">[#code]</th>
                    <th>[#title]</th>
                    [@members_ths]
                </tr>
            </thead>
            <tbody>
            	[@groups_list]
            </tbody>
        </table>
    </div>
</div>
