<div class="toolbox">
    <a module="fees" action="savePaymentsDates" sms_id="[@sms_id]" con="[@con]" con_id="[@con_id]">[#save]<span class="ui-icon ui-icon-print"></span></a>
    <a module="fees" action="addDates">[#edit]<span class="ui-icon ui-icon-plus"></span></a>
</div>
<table class="tableinput [@dates_table_hidden] dates_table">
    <thead>
        <tr>
        	<th width="20">&nbsp;</th>
            <th>[#title]</th>
            <th width="150">[#from]</th>
            <th width="150">[#end]</th>
        </tr>
    </thead>
    <tbody>
        [@dates_rows]
    </tbody>
</table>