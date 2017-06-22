<div class="ui-widget-content ui-corner-all transparent_div">
    <div class="toolbox">
        <a module="categorys" action="newCat" class="[@edit_hidden]">[#new_cat] <span class="ui-icon ui-icon-plusthick"></span></a>
        <a action="printTree" title="[#print_tree]">[#print_category_list]<span class="ui-icon ui-icon-print"></span></a>
        <a action="changeView" rel="icon" class="ui-state-active clickable"><img src="assets/img/icon_view.png" width="16" height="16" /></a>
        <a action="changeView" rel="list" class="clickable" ><img src="assets/img/list_view.png" width="16" height="16" /></a>
       <!-- <a module="categorys" action="newSubcat">[#new_sub] <span class="ui-icon ui-icon-plus"></span></a>-->
    </div>
    
    <table class="layout" width="100%">
    	<tr>
        	<td width="33%" valign="top">
                <div class="tree_list" id="tree_list">
                    [@cats_list]
                </div>
            </td>
            <td valign="top">
                <div id="category_details_div">
            		[@cat_detail]
                </div>
            </td>
        </tr>
        <tr>
    
    </table>
</div>