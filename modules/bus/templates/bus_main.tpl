<div class="scoop transparent_div ui-widget-content">
    <table width="100%" cellspacing="2">
        <tr>
            <td width="300" valign="top">
                <div class="toolbox">
                    <span style="margin:0px 7px 0px 2px">
                        <input class="ui-state-default ui-corner-left" type="text" onkeyup="filterList(this.value)" onfocus="$(this).val('')" value="[#search]" onblur="$(this).val('[#search]')" />
                        <text style="padding:3px" action="resetFilterList" class="hoverable ui-state-default ui-corner-right">
                            <span class="ui-icon ui-icon-refresh"></span>
                        </text>

                    </span>
                    <a action="newBus"> <span class="ui-icon ui-icon-plus"></span></a>
                </div>
				<div class="scrolableLayout" style="max-height:430px; overflow:auto">
                    <ul id="resource_list" class="list_menu listMenuUl">
                        [@list_buss]
                    </ul>
                </div>
            </td>
            <td id="bus_content" valign="top">
                [@bus_layout]
            </td>
        </tr>
    </table>
</div>