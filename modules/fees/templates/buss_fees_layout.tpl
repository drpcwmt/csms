  <form name="bus_fess_form">
    <div class="toolbox ">
        <a action="saveFees" con="bus" con_id="[@group_id]" busms_id="[@busms_id]" sms_id="[@sms_id]" title="[#save]" class="[@prvlg_group_fees_edit]">[#save]<span class="ui-icon ui-icon-disk"></span></a>
        <a action="newFees" con="bus" con_id="[@group_id]" busms_id="[@busms_id]" sms_id="[@sms_id]" title="[#new]" class="[@prvlg_group_fees_edit]">
        	[#new]<span class="ui-icon ui-icon-plus"></span></a>
        <a action="LoadPayments" con="bus" con_id="[@group_id]" sms_id="[@sms_id]" title="[#payments]" class="[@prvlg_group_fees_edit]">[#payments]<span class="ui-icon ui-icon-calendar"></span></a>
        <a action="print_pre" rel="bus_fees-[@group_id]" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>
    
    <h2 class="title">[#group]: [@group_name]</h2>
    <table class="tableinput [@prvlg_group_fees_read]">
        <thead>
            <tr>
                <th>[#title]</th>
                <th width="90">[#value]</th>
                <th width="120">[#currency]</th>
                <th width="60">[#discountable]</th>
                <th width="60">[#annual_increase]</th>
                <th width="90">[#acc_code]</th>
                <th class="unprintable" width="20">&nbsp;</th>
           </tr>
        </thead>
        <tbody>
            [@bus_fees_rows]
        </tbody>
     </table>
   </form>
