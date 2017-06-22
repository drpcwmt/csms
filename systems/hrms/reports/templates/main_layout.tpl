<div class="ui-widget-content transparent_div scoop">
    <table width="100%">
        <tr>
            <td id="reports_main_td">
                <div class="toolbox">
                    <a action="printScoop">[#print]<span class="ui-icon ui-icon-print"></span></a>
                </div>
               <form  class="ui-state-highlight ui-corner-all">
                    <table width="100%" cellspacing="1" cellpadding="0" border="0">
                        <tr>
                            <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#month]</label></td>
                            <td>
                                <select name="month" class="combobox" >[@months_opts]</select>
                          </td>
                            <td><label class="label ui-widget-header ui-corner-left reverse_align">[#cost_center]</label></td>
                            <td>
                                <select name="cc" class="combobox" >[@cc_opts]</select>
                            </td>
                            <td width="80" rowspan="2">
                                <button class="ui-state-default hoverable" action="subRewDis" style="width:60px; height:60px">[#search]</button>
                            </td>
                        </tr>
                        <tr>
                            <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#job]</label></td>
                            <td>
                                <select name="job_id" class="combobox" >[@jobs_opts]</select>
                          </td>
                        </tr>
                    </table>
                </form>
                <div id="raw_des_div">
                    [@table]
                </div>
            </td>
        </tr>
    </table>
</div>    