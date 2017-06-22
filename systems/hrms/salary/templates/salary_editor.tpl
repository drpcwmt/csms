<div class="ui-widget-content transparent_div scoop">
	<div class="toolbox">
    	<a action="printScoop">[#print]<span class="ui-icon ui-icon-print"></span></a>
        <a action="saveSalaryEditor">[#save]<span class="ui-icon ui-icon-disk"></span></a>
    </div>
    <form class="ui-state-highlight" style="padding:5px 10px">
        <table width="100%" cellspacing="1" cellpadding="0" border="0">
            <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#job]</label></td>
                <td colspan="3">
                    <select name="job_id" class="combobox" update="updateSalaryEditor">[@job_opts]</select>
              </td>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#cost_center]</label></td>
                <td colspan="3">
                    <select name="cc" class="combobox" update="updateSalaryEditor">[@ccs_opts]</select>
              </td>
            </tr>
        </table>
    </form>
    <h2 class="title">[@job_name]</h2>
    <h3>[#count]: [@count_emps]</h3>
    <form id="salary_editor_form">
        <table class="tableinput">
            <thead>
                <tr>
                    <th width="20" class="unprintable">&nbsp;</th>
                    <th>[#code]</th>
                    <th>[#name]</th>
                    <th>[#position]</th>
                    <th>[#join_date]</th>
                    <th>[#current_basic]</th>
                    <th>[#current_var]</th>
                    <th>[#current_allowances]</th>
                    <th class="unprintable">[#basic]</th>
                    <th class="unprintable" width="60">[#currency]</th>
                    <th class="unprintable">[#var]</th>
                    <th class="unprintable" width="60">[#currency]</th>
                    <th class="unprintable">[#allowances]</th>
                    <th class="unprintable" width="60">[#currency]</th>
                    <th>[#profil]</th>
                </tr>
           </thead>
           <tbody>
            [@trs]
           </tbody>
        </table>
    </form>
</div> 
