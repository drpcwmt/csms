<h2 id="driver_name_title" class="title"> [@name] </h2>
<div class="tabs">
	<ul>
    	<li><a href="#licence_info">[#licences]</a></li>
        <li><a href="#personel_info">[#personel_info]</a></li>
    </ul>
    <div id="licence_info">
        <div class="toolbox">
            <a action="saveDriver"> [#save] <span class="ui-icon ui-icon-disk"></span> </a>
            <a action="deleteDriver"> [#delete] <span class="ui-icon ui-icon-close"></span></a>
            <a action="print_but" rel="#drivers_list"> [#print] <span class="ui-icon ui-icon-print"></span></a>
        </div>


    	<form>
        	<input type="hidden" name="id" id="driver_id" value="[@id]" />
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
             <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#license_no]</label></td>
                <td><input type="text" value="[@lic_no]" id="lic_no" name="lic_no" /></td>
             </tr>
             <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#lic_type]</label></td>
                <td><input type="text" value="[@lic_type]" id="lic_type" name="lic_type" /></td>
             </tr>
             <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align ">[#issue_date]</label></td>
                <td><input class="datepicker mask-date" type="text" value="[@issue_date]" id="issue_date" name="issue_date" /></td>
             </tr>
             <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#exp_date]</label></td>
                <td><input class="datepicker mask-date" type="text" value="[@exp_date]" id="exp_date" name="exp_date" /></td>
             </tr>
          </table>
        </form>
        <div class="driver_route_table">
        	[@routes]
        </div>
    </div>
    <div id="personel_info">
    	[@personel_info]
    </div>
</div>