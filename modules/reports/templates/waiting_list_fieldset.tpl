<fieldset>
	<legend>[@level_name]</legend>
    <table width="100%" cellspacing="1" cellpadding="0" border="0">
    	<tr>
            <td width="120"><label class="label reverse_align ui-widget-header">[#registred]</label></td>
            <td>
                <div class="fault_input">[@tot_registred]</div>
            </td>
        </tr>
    	<tr>
            <td><label class="label reverse_align ui-widget-header">[#waiting_list]</label></td>
            <td>
                <div class="fault_input">[@tot_waiting]</div>
            </td>
        </tr>
    </table>
    <table class="tableinput">
    	<thead>
        	<tr>
            	<th width="20" class="unprintable">&nbsp;</th>
                <th>[#name]</th>
                <th>[#join_date]</th>
                <th>[#class]</th>
            </tr>
        </thead>
    	<tbody>
        	[@trs]
        </tbody>
    </table>
</fieldset>