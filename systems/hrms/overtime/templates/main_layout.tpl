<h2 class="title">[#overtime]</h2>
<div class="tabs">
	<ul> 
    	<li><a href="#overtime_insert_tab">[#add]</a></li>
        <li><a href="#overtime_report_tab">[#reports]</a></li>
    </ul>
    <div id="overtime_insert_tab">
        <table width="100%">
            <tr>
                <td width="305" valign="top">
                    <ul class="list_menu listMenuUl">
                        [@job_list]
                    </ul>
                </td>
                <td valign="top">
                	<div class="toolbox">
                    	<a action="addOvertime" class="[@add_overtime_hidden]"><span class="ui-icon ui-icon-plus"></span>[#add]</a>
                        <a action="print_tab"><span class="ui-icon ui-icon-print"></span>[#print]</a>
                    </div>
                    <form class="ui-state-highlight" id="overtime_form"> 
                    	<input type="hidden" name="job_id" id="overtime_job_id" value="[@job_id]" />		
                        <table width="100%" cellspacing="1" cellpadding="0" border="0">
                            <tr>
                                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                                <td colspan="3">
                                    <input type="text" name="date" value="[@today]" class="datepicker mask-date" update="openOvertimeByJob" />
                              </td>
                            </tr>
                        </table>
                    </form>                    
                    <table class="tablesorter">
                        <thead>
                            <tr>
                                <th class="{sorter:false}" width="20">&nbsp;</th>
                                <th>[#name]</th>
                                <th>[#job]</th>
                                <th width="60">[#begin_time]</th>
                                <th width="60">[#end_time]</th>
                                <th width="60">[#value]</th>
                            </tr>
                        </thead>
                        <tbody id="overtime_daily_tbody">
                            [@daily_trs]
                        </tbody>
                    </table>                
				</td>
            </tr>
        </table>
        
	</div>
    <div id="overtime_report_tab">
    	<div class="toolbox">
        	<a action="print_tab"><span class="ui-icon ui-icon-print"></span>[#print]</a>
        </div>
    	<h2 class="showforprint hidden" align="center">[#overtime_report]</h2>
    	<form id="discounts_report_form" class="ui-state-highlight ui-corner-all">
            <table width="100%" cellspacing="1" cellpadding="0" border="0">
                <tr>
                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#month]</label></td>
                    <td colspan="3">
                        <select name="month" class="combobox" update="updateAbsentReport">[@months_select]</select>
                  </td>
                </tr>
                <tr>
                    <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#job]</label></td>
                    <td colspan="3">
                        <select name="job_id" class="combobox" update="updateDiscountsReport">[@job_opts]</select>
                  </td>
                </tr>
            </table>
        </form>
        <table class="tablesorter">
            <thead>
                <th width="20" class="unprintable {sorter:false}">&nbsp;</th>
                <th>[#name]</th>
                <th>[#position]</th>
                <th width="60" align="center">[#count]</th>
                <th width="60" align="center">[#hours]</th>
                <th width="60" align="center">[#value]</th>
            </thead>
            <tbody id="overtime_list_tbody">
                [@report_trs]       
            </tbody>
        </table>    
    </div>
</div>   