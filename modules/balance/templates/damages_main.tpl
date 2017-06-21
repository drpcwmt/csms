<div class="ui-corner-bottom ui-widget-content transparent_div" id="damages_div">
<div class="toolbox">
    <a action="print_pre" rel="#damages_data_td" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    <a action="createDamageTransaction"  title="[#create_transaction]">[#create_transaction]<span class="ui-icon ui-icon-refresh"></span></a>
</div>
    <h3 class="title">[#damages]</h3>
    <table width="100%">
    	<tr>
        	<td width="300" valign="top">
            	<div class="tree_list">
            		[@tree]
                </div>
            </td>
            <td valign="top" id="damages_data_td">
            	<table class="result">
                	<thead>
                    	<tr>
                        	<th>[#code]</th>
                            <th>[#title]</th>
                            <th>[#total]</th>
                        </tr>
                    </thead>
                    <tbody>
                    	[@trs]
                    </tbody>
                    <tfoot>
                    	[@tfoot]
                    </tfoot>
             	</table>
             </td>
          </tr>
      </table>	
</div>