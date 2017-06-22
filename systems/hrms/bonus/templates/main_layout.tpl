<h2 class="title">[#bonus]</h2>
<div class="tabs">
	<ul> 
    	<li><a href="#bonus_insert_tab">[#add]</a></li>
        <li><a href="#bonus_report_tab">[#reports]</a></li>
    </ul>
    <div id="bonus_insert_tab">
        <table width="100%">
            <tr>
                <td width="305" valign="top">
                    <ul class="list_menu listMenuUl">
                        [@job_list]
                    </ul>
                </td>
                <td valign="top">
                	<div class="toolbox">
                    	<a action="addBonus" class="[@add_bonus_hidden]"><span class="ui-icon ui-icon-plus"></span>[#add]</a>
                        <a action="print_tab"><span class="ui-icon ui-icon-print"></span>[#print]</a>
                    </div>
                    <form class="ui-state-highlight" id="bonus_form"> 
                    	<input type="hidden" name="job_id" id="bonus_job_id" value="[@job_id]" />		
                        <table width="100%" cellspacing="1" cellpadding="0" border="0">
                            <tr>
                                <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                                <td colspan="3">
                                    <input type="text" name="date" value="[@today]" class="datepicker mask-date" update="openBonusByJob" />
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
                                <th>[#reason]</th>
                                <th width="60">[#value]</th>
                                <th width="60">[#days]</th>
                            </tr>
                        </thead>
                        <tbody id="bonus_daily_tbody">
                            [@daily_trs]
                        </tbody>
                    </table>                
				</td>
            </tr>
        </table>
        
	</div>
    <div id="bonus_report_tab">
    	<div class="toolbox">
        	<a action="print_tab"><span class="ui-icon ui-icon-print"></span>[#print]</a>
        </div>
    	<h2 class="showforprint hidden" align="center">[#bonus_report]</h2>
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
                <th>[#count]</th>
                <th>[#reason]</th>
                <th width="60">[#value]</th>
                <th width="60">[#cash]</th>
                <th width="60">[#total]</th>
            </thead>
            <tbody id="bonus_list_tbody">
                [@report_trs]       
            </tbody>
        </table>    
    </div>
</div>   