<h2 class="title">[@code]</h2>
<div class="tabs">
	<ul>
    	<li><a href="#insert_tab">[#add]</a></li>
        <li><a href="index.php?module=fees&browse&con=school&con_id=&sms_id=[@sms_id]">[#browse]</a></li>
        <li><a href="index.php?module=fees&con=school&con_id=&sms_id=[@sms_id]">[#school_fees]</a></li>
        <li><a href="index.php?module=fees&bus_fees&routes&amp;groups&sms_id=[@sms_id]">[#bus]</a></li>
        <li><a href="index.php?module=fees&book_fees&amp;sms_id=[@sms_id]">[#books]</a></li>
        <li><a href="index.php?module=fees&reserved&amp;sms_id=[@sms_id]">[#reservations]</a></li>
        <li><a href="#payment_tab">[#payments]</a></li>
        <li><a href="index.php?module=fees&amp;totals&amp;sms_id=[@sms_id]">[#total]</a></li>
        <li><a href="index.php?module=fees&amp;late_list&amp;sms_id=[@sms_id]">[#late_list]</a></li>
        <li><a href="index.php?module=sms&balance&amp;sms_id=[@sms_id]">[#school_balance]</a></li>
    </ul>
    <div id="insert_tab">
        <table width="100%">
            <tr>
                <td width="45%" valign="top">
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
    <div id="payment_tab">
    	<form>
        	[@payment_table]
        </form>
    </div>
</div>
