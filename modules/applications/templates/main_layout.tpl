<div class="ui-corner-top ui-widget-header">
	<h2 class="reverse_align big_title">[#applications]</h2>
</div>
<div class="ui-corner-bottom ui-widget-content transparent_div" >
    <table border="0" cellspacing="5" width="100%" class="scope">
		<tr>
			<td id="resource_list" class="resource_list" valign="top" width="305">
            	[@toolbox]
            	<div class="scrolableLayout" style="max-height:430px; overflow:auto">
                    <ul class="list_menu listMenuUl"  >
                    	<li action="getAppliList" rel="1" class="hoverable clickable ui-stat-default ui-corner-all">[#new] [@count_new]</li>
                        <li action="getAppliList" rel="2" class="hoverable clickable ui-stat-default ui-corner-all"> Interview [@count_interview1]</li>
                        <li action="getAppliList" rel="3" class="hoverable clickable ui-stat-default ui-corner-all">Accepted [@count_accepted]</li>
                        <li action="getAppliList" rel="4" class="hoverable clickable ui-stat-default ui-corner-all">Rejected [@count_rejected]</li>
                    </ul>
                </div>
            </td>
            <td id="application_list" valign="top">[@appli_list]</td>
		</tr>
    </table>
</div>