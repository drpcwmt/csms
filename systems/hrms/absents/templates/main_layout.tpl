<h2 class="title">[#absents]</h2>
<div class="tabs">
	<ul> 
    	<li><a href="#abs_insert_tab">[#add]</a></li>
        <li><a href="#abs_report_tab">[#reports]</a></li>
    </ul>
    <div id="abs_insert_tab">
        <table width="100%">
            <tr>
                <td width="305" valign="top">
                    <ul class="list_menu listMenuUl">
                        [@job_list]
                    </ul>
                </td>
                <td valign="top">
                	<div class="toolbox">
                    	<a action="addAbsents" class="[@add_absent_hidden]"><span class="ui-icon ui-icon-plus"></span>[#add]</a>
                        <a action="print_tab"><span class="ui-icon ui-icon-print"></span>[#print]</a>
                    </div>
                    <form class="ui-state-highlight" id="abs_add_form"> 
                    	<input type="hidden" name="job_id" id="abs_job_id" value="[@job_id]" />		
                        <table width="100%" cellspacing="1" cellpadding="0" border="0">
                            <tr>
                                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                                <td colspan="3">
                                    <input type="text" name="date" value="[@today]" class="datepicker mask-date" update="openAbsByJob" />
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
                                <th width="60">[#approved]</th>
                                <th width="60">[#ill]</th>
                                <th width="60">[#conv_abs]</th>
                                <th width="80">[#value]</th>
                                <th>[#notes]</th>
                            </tr>
                        </thead>
                        <tbody id="abs_daily_tbody">
                            [@daily_trs]
                        </tbody>
                    </table>                
				</td>
            </tr>
        </table>
        
	</div>
    <div id="abs_report_tab">
    	<div class="toolbox">
        	<a action="print_tab"><span class="ui-icon ui-icon-print"></span>[#print]</a>
        </div>
    	<h2 class="showforprint hidden" align="center">[#absent_report]</h2>
    	<form id="absents_report_form" class="ui-state-highlight ui-corner-all">
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
                        <select name="job_id" class="combobox" update="updateAbsentReport">[@job_opts]</select>
                  </td>
                </tr>
            </table>
        </form>
        <table class="tablesorter">
            <thead>
                <th width="20" class="unprintable">&nbsp;</th>
                <th>[#name]</th>
                <th>[#position]</th>
                <th width="60">[#ill_abs_days]</th>
                <th width="60">[#conv_abs]</th>
                <th width="60">[#days]</th>
                <th width="60">[#value]</th>
                <th width="60" class="[@cash_hidden]">[#cash]</th>
            </thead>
            <tbody id="absent_list_tbody">
                [@report_trs]       
            </tbody>
        </table>    
	</div>
</div>   