<div class="ui-widget-content transparent_div">
     <table border="0" cellspacing="5" width="100%" class="scope">
        <tr>
            <td id="incomes_list" class="incomes_list" valign="top" width="305">
                <div class="toolbox">
                    <a action="newIncome" class="[@hidden]">[#new]<span class="ui-icon ui-icon-document"></span></a>
                    [@sync_button]
                </div>
                <div class="scrolableLayout" style="max-height:430px; overflow:auto">
                    <ul class="list_menu listMenuUl sortable" rel="otherincomes" id="otherincomes_list" >
                        [@incomes_list]
                    </ul>
                </div>
            </td>
            <td id="incomes_content" valign="top">[@incomes_content]</td>
        </tr>
    </table>
</div>