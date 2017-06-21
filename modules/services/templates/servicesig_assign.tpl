<form id="services-form" class="ui-state-highlight ui-corner-all" style="padding:5px">
	<input type="hidden" name="con" value="[@con]" />
    <input type="hidden" name="con_id" value="[@con_id]" />
    <table  border="0" cellspacing="0">
        <tr>
            <td class="reverse_align" width="120" valign="middel"> 
                <label class="label ui-widget-header ui-corner-left">[#materials]</label>
            </td>
            <td>
                <select class="combobox" name="material" update="loadIgService" >[@mat_opts]</select>
            </td>
        </tr>
	</table>
    <div id="service_select_div">
        <table class="tablesorter">
            <thead>
                <tr>
                    <th >&nbsp;</th>
                    <th width="60">Nov</th>
                    <th width="60">Jan</th>
                    <th width="60">Jun</th>
                </tr>
            </thead>
            <tbody>
            	[@trs]
            </tbody>
         </table>
   </div>
</form>