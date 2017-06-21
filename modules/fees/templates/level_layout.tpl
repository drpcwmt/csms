<div class="ui-corner-bottom ui-widget-content transparent_div level_fees" id="level_fees-[@level_id]">
  <form name="fess_form">
    <div class="toolbox">
        <a action="saveFees" con="level" con_id="[@level_id]" sms_id="[@sms_id]" title="[#save]">[#save]<span class="ui-icon ui-icon-disk"></span></a>
        <a action="newFees" con="level" con_id="[@level_id]" sms_id="[@sms_id]" title="[#new]">[#new]<span class="ui-icon ui-icon-plus"></span></a>
        <a action="LoadPayments" con="level" con_id="[@level_id]" sms_id="[@sms_id]" title="[#payments]">[#payments]<span class="ui-icon ui-icon-calendar"></span></a>
        <a action="print_pre" rel="level_fees-[@level_id]" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>
    
	<h2 class="title">[@level_name]</h2>
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
        	[@level_fees_rows]
        </tbody>
     </table>
     
   </form>
</div>