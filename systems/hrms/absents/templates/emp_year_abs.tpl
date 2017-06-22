<div class="toolbox">
	<a module="absents" action="getAbsentList" emp_id="[@id]">[#list] <span class="ui-icon ui-icon-script"></span></a>
</div>
<table class="table-header-rotated">
	<thead>
    	<th>&nbsp;</th>
        [@ths]
        <th class="rotate">
        	<div>
            	<span>[#total]</span>
            </div>
        </th>
    </thead>
    <tbody>
    	<tr>
        	<th class="row-header">[#ill]</td>
            [@ill_tds]
            <td>[@ill_total]</td>
        </tr>
    	<tr>
        	<th class="row-header">[#approved]</td>
            [@approv_tds]
            <td>[@approv_total]</td>
        </tr>        
    	<tr>
        	<th class="row-header">[#total]</td>
            [@total_tds]
            <td>[@total_all]</td>
        </tr>
    </tbody>
</table>