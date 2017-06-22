<div class="ui-widget-content transparent_div scoop">
    <div class="toolbox">
        <a action="printScoop"><span class="ui-icon ui-icon-print"></span>[#print]</a>
    </div>
    <h2 class="showforprint hidden" align="center">[#salary_report]</h2>
    <form  class="ui-state-highlight ui-corner-all">
        <table width="100%" cellspacing="1" cellpadding="0" border="0">
            <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#month]</label></td>
                <td>
                    <select name="month" class="combobox" >[@months_select]</select>
              </td>
                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#cost_center]</label></td>
                <td>
                	<select name="cc" class="combobox" >[@cc_opts]</select>
                </td>
                <td width="80" rowspan="2">
                	<button class="ui-state-default hoverable" action="updateSalaryReport" style="width:60px; height:60px">[#search]</button>
                </td>
            </tr>
            <tr>
                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#profil]</label></td>
                <td>
                    <select name="profil_id" class="combobox" >[@profil_opts]</select>
              </td>
                <td><label class="label ui-widget-header ui-corner-left reverse_align">[#payment_mode]</label></td>
                <td>
                	[@payment_mode_select]         
                </td>
            </tr>
        </table>
    </form>
    [@emps_table]    
</div>