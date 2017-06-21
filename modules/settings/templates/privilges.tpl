<h3>[@group_name]</h3>
<table border="0" cellspacing="5" width="100%">
	<tr>
    	<td id="prvlg_list" valign="top" width="30%">
        	<ul class="list_menu listMenuUl">
            	[@modules]
            </ul>
        </td>
        <td valign="top">
         <div class="toolbox">
            <a action="chlAllPrvlg"><span class="ui-icon ui-icon-check"></span>[#chk_all]</a>
        </div>
       	<form id="prvlg_form" editable="[@editable]">
            	<input type="hidden" name="group" value="[@group_id]" />
                <input type="hidden" name="user_id" value="[@user_id]" />
                [@privilge_divs]
            </form>
            
        </td>
    </tr>
</table>