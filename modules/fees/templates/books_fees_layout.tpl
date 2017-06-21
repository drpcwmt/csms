<form>
    <div class="toolbox">
        <a action="saveFees" con="books" con_id="[@level_id]" sms_id="[@sms_id]" title="[#save]">[#save]<span class="ui-icon ui-icon-disk"></span></a>
        <a action="newFees" con="books" con_id="[@level_id]" sms_id="[@sms_id]" title="[#new]">
        	[#new]<span class="ui-icon ui-icon-plus"></span></a>
        <a action="LoadPayments" con="books" con_id="[@level_id]" sms_id="[@sms_id]" title="[#payments]">[#payments]<span class="ui-icon ui-icon-calendar"></span></a>
        <a action="print_pre" rel="books_fees-[@level_id]" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>

    <table class="tableinput">
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
            [@books_fees_rows]
        </tbody>
     </table>
</form>