<h2 class="title">[@code]</h2>
<div class="tabs">
	<ul>
    	<li><a href="#insert_tab">[#add]</a></li>
        <li><a href="#school_levels">[#browse]</a></li>
        <li><a href="#school_fees_settings">[#school_fees]</a></li>
        <li><a href="index.php?module=routes&groups&busms_id=[@busms_id]&sms_id=[@sms_id]">[#bus]</a></li>
        <li><a href="index.php?module=books&sms_id=[@sms_id]">[#books]</a></li>
        <li><a href="#payment_tab">[#payments]</a></li>
        <li><a href="index.php?module=sms&totals&sms_id=[@sms_id]">[#total]</a></li>
        <li><a href="index.php?module=sms&late_list&sms_id=[@sms_id]">[#late_list]</a></li>
    </ul>
    <div id="insert_tab">
        <table width="100%">
            <tr>
                <td width="50%" valign="top">
                    <div class="tabs">
                        <ul>
                            <li><a href="#student_search_tab">[#by_student]</a></li>
                            <li><a href="#import_fees">[#multiple]</a></li>
                        </ul>
                         <div id="student_search_tab">
                            [@addForm]
                         </div>
                        <div id="import_fees">
                            <form>
                                <div class="toolbox">
                                    <a action="uploadFeesSheet">[#import]<span class="ui-icon ui-icon-transferthick-e-w"></span></a>
                                    <a action="openBankSheet" sms_id="[@sms_id]">[#bank_sheet]<span class="ui-icon ui-icon-print"></span></a>
                                </div>
                                <input type="hidden" name="ccid" value="[@sms_id]"/>
                                <div id="import_data"></div>
                            </form>
                        </div>
                	</div>
                </td>
                <td id="student_layout_td" valign="top">
                </td>
            </tr>
        </table>            
         
    </div>
    <div id="school_levels">
         <table border="0" cellspacing="5" width="100%" class="scope">
            <tr>
                <td id="resource_list" class="resource_list" valign="top" width="305">
                    [@toolbox]
                    <div class="scrolableLayout" style="max-height:430px; overflow:auto">
                        <ul class="list_menu listMenuUl sortable" rel="[@item_type]" >
                            [@levels_list]
                        </ul>
                    </div>
                </td>
                <td id="level_content" valign="top">[@level_layout]</td>
            </tr>
        </table>
    </div>
    <div id="school_fees_settings">
        <table border="0" cellspacing="5" width="100%" class="scope">
            <tr>
                <td id="resource_list" class="resource_list" valign="top" width="305">
                    [@toolbox]
                    <div class="scrolableLayout" style="max-height:430px; overflow:auto">
                        <ul class="list_menu listMenuUl sortable" rel="[@item_type]" >
                            [@items_list]
                        </ul>
                    </div>
                </td>
                <td id="resource_content" valign="top">[@level_fees_layout]</td>
            </tr>
        </table>
	</div>
    <div id="payment_tab">
    	<form>
        	[@payment_table]
        </form>
    </div>
</div>
