<div class="toolbox">
    <a action="print_tab" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
</div>
<div class="hidden showforprint">
	<h4>[@today]</h4>
    <h2 align="center">[#income_report] [@year_name]</h2>
    <h3 align="center">[@school_name]</h3>
</div>
<table class="result">
	<thead>
    	<tr>
        	<th>[#level]</th>
            <th>[#account]</th>
            <th width="80">[#currency]</th>
            <th width="80">[#students]</th>
            <th width="120">[#total]</th>
            <th width="120">[#paid]</th>
            <th width="120">[#rest]</th>
        </tr>
    </thead>
    <tbody>
    	[@tbody_trs]
    </tbody>
    <tfoot>
    	[@tfoot_trs]
    </tfoot>
</table>
	