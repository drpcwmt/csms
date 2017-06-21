<form class="ui-state-highlight">
	<input type="hidden" name="std_id" value="[@std_id]" />
	<input type="hidden" name="sms_id" value="[@sms_id]" />
    <table border="0" cellspacing="0" width="100%">
    	<tr>
            <td class="reverse_align" valign="middel" width="120">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#material]</label>
            </td>
            <td valign="top">
               <ul class="list_menu listMenuUl">
                    [@opts]
               </ul>
            </td>
        </tr>  

    	<tr>
            <td class="reverse_align" valign="middel" width="120">
                <label class="label ui-widget-header ui-corner-left reverse_align">[#fees]</label>
            </td>
            <td valign="top">
               <input type="text" name="fees" /> EGP
            </td>
        </tr>  
	 </table>
</form>    