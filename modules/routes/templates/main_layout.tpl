
<div id="route_main_div">
    <table width="100%" cellpadding="0" border="0">
        <tr>
            <td width="250" valign="top">
            	<div class="ui-widget-content">    
                    <div class="toolbox">
                        <span style="margin:0px 7px 0px 2px">
                            <input class="ui-state-default ui-corner-left" type="text" onkeyup="filterRoutesList(this.value)" onfocus="$(this).val('')" value="[#search]" onblur="$(this).val('[#search]')" />
                            <text style="padding:3px" action="resetRoutesFilterList" class="hoverable ui-state-default ui-corner-right">
                                <span class="ui-icon ui-icon-refresh"></span>
                            </text>
    
                        </span>
                    </div>
                    <div id="routes_list">
                    	<ul class="list_menu listMenuUl sortable" rel="routes">
                        [@list]
                        </ul>
                    </div>
                 </div>
            </td>
            <td id="RouteDetails" valign="top">
            	[@route_details]
            </td>
        </tr>
    </table>
</div>