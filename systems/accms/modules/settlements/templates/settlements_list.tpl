<div class="ui-corner-all ui-widget-content scoop transparent_div" id="transactions_list">
	<div class="toolbox">
    	<a rel="#transactions_list" class="print_but"><span class="ui-icon ui-icon-print"></span>[#print]</a>
    </div>
    <form class="ui-ocrner-all ui-state-highlight [@form_hidden]">
		<table>
        	<tr>
                <td width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align" >[#date]</label></td>
                <td>
                    <input type="text" name="date" value="[@today]" class="mask-date datepicker"/>
                    <button type="button" action="changeListDate" class="hoverable ui-state-default" style="padding: 3px 6px;"><span class="ui-icon ui-icon-search"></span>[#search]</button>
                </td>
            </tr>
        </table>
    </form>
    <table class="tablesorter">
    	<thead>
        	<tr>
            	<th width="20" class="unprintable">&nbsp;</th>
                <th width="20" class="unprintable">&nbsp;</th>
                <th width="80">[#reg_no]</th>
                <th width="100">[#total]</th>
                <th width="80">[#currency]</th>
                <th>[#description]</th>
                <th width="120">[#user]</th>
                <th width="100">[#date]</th>
            </tr>
        </thead>
        <tbody id="trans_list_tbody">
        	[@trs]
        </tbody>
    </table>	
    [@error_nothing_match]
</div>