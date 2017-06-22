<div class="ui-widget-content transparent_div scoop">
    <div class="toolbox">
        <a action="printScoop"><span class="ui-icon ui-icon-print"></span>[#print]</a>
    </div>
    <h2 class="showforprint hidden" align="center">[#insur_report]</h2>
    <form id="absents_report_form" class="ui-state-highlight ui-corner-all">
        <table width="100%" cellspacing="1" cellpadding="0" border="0">
            <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#month]</label></td>
                <td colspan="3">
                    <select name="month" class="combobox" update="updateInsurReport">[@months_select]</select>
              </td>
            </tr>
            <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#job]</label></td>
                <td colspan="3">
                    <select name="job_id" class="combobox" update="updateInsurReport">[@job_opts]</select>
              </td>
            </tr>
        </table>
    </form>
    <table class="tablesorter">
        <thead>
            <tr>
                <th width="20" class="unprintable {sorter:false}">&nbsp;</th>
                <th width="80">[#code]</th>
                <th width="80">[#insur_no]</th>
                <th>[#name]</th>
                <th>[#position]</th>
                <th width="80">[#salary_basic]</th>
                <th width="80">[#salary_var]</th>
                <th width="80">[#insur_soc_share]</th>
                <th width="80">[#insur_emp_share]</th>
                <th width="80">[#total]</th>
            </tr>
        </thead>
        <tbody id="insur_list_tbody">
            [@report_trs]       
        </tbody>
        <tfoot>
        	<tr>
            	<th class="unprintable">&nbsp;</th>
                <th colspan="6">[#total]</th>
                <th>[@total_soc]</th>
                <th>[@total_emps]</th>
                <th>[@total]</th>
            </tr>
        </tfoot>
    </table>    
</div>