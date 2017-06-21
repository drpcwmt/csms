[@toolbox]
<h2 class="title">[@student_name] </h2>
<h3>[#level]: [@level_name]</h3>

  [@notes]
 <div class="tabs">
 	<ul>
    	<li><a href="#student_ingoing">[#ingoing]</a></li>
        <li><a href="#student_payments">[#fees]</a></li>
        <li><a href="#student_paids">[#paids]</a></li>
    </ul>
    <div id="student_ingoing">
        <h2 class="hidden showforprint title" style="text-align:center">[@student_name]</h2>
        <h3 class="hidden showforprint" style="text-align:center">[#school_fees_table]</h3>
        <h3 class="hidden showforprint" style="text-align:center">[@level_name] - [@year_name]</h3>
    	[@fees_summary]
    </div>
    <div id="student_payments">        
    	<div class="toolbox">
             <a action="newFees" sms_id="[@sms_id]" con="student" con_id="[@id]" class="[@prvlg_edit_fee]">[#add]<span class="ui-icon ui-icon-plus"></span></a>
             <a action="LoadPayments" con="student" con_id="[@id]" sms_id="[@sms_id]" class="[@prvlg_edit_fee]">[#payments]<span class="ui-icon ui-icon-calendar"></span></a>
        	 <a action="print_pre" rel="#student_payments">[#print]<span class="ui-icon ui-icon-print"></span></a>
        </div>
    	<fieldset>
        	<legend>[#fees]</legend>
            [@due_table]
    	</fieldset>
    </div>
    <div id="student_paids">
		[@ingoing_table]
	</div>
</div>