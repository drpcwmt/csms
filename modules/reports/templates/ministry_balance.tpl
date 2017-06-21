<fieldset>
	<h4>[@school_name]</h4>
    <h3>[#school_balance] [#year] [@year]</h3>
	<h3>[@date]</h3>
</fieldset>
<table class="tablesorter">
	<thead>
    	<tr>
        	<th rowspan="2">[#level]</th>
            <th rowspan="2">[#count_classes]</th>
            <th colspan="3">[#male]</th>
            <th colspan="3">[#female]</th>
            <th rowspan="2">[#total]</th>
        </tr>
    	<tr>
            <th>[#muslim]</th>
            <th>[#christian]</th>
        	<th>[#total]</th>
            <th>[#muslim]</th>
            <th>[#christian]</th>
            <th>[#total]</th>
        </tr>
     </thead>
     <tbody>
     	[@rows] 
     </tbody>
     <tfoot>
     	<tr>
        	<th>[#total]</th>
            <th>[@total_class]</th>
            <th>[@total_boy_m]</th>
        	<th>[@total_boy_c]</th>
            <th>[@total_boy]</th>
            <th>[@total_girl_m]</th>
        	<th>[@total_girl_c]</th>
            <th>[@total_girl]</th>
            <th>[@total]</th>
         </tr>
    </tfoot>
</table>