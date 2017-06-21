[@toolbox]
<h2 class="title">[@student_name] </h2>
<h3>[#level]: [@level_name]</h3>

  [@notes]
 <div class="tabs">
 	<ul>
    	<li><a href="#student_ingoing">[#ingoing]</a></li>
        <li><a href="#student_payments">[#fees]</a></li>
        <li><a href="#student_paids">[#paids]</a></li>
        [@ig_service_tab]
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
    	<form name="student_settings">
            <input type="hidden" name="std_id" value="[@id]" />
        	<fieldset class=" ui-state-highlight [@prvlg_edit_profil]">
                <table border="0" cellspacing="0" width="100%">
                      <tr>
                        <td class="reverse_align" valign="middel" width="120">
                            <label class="label ui-widget-header ui-corner-left reverse_align">Profil:</label>
                        </td>
                        <td valign="top">
                           <select class="combobox" name="profil" id="profil_select">
                                [@profils_opts]
                            </select>
                            <button type="button" action="saveStdProfil" sms_id="[@sms_id]" std_id="[@id]" class="ui-corner-all ui-state-default hoverable">[#save]</button>
                            <button type="button" action="openProfil" sms_id="[@sms_id]" std_id="[@id]" class="ui-corner-all ui-state-default hoverable">[#open]</button>
                            <button type="button" action="newProfil" sms_id="[@sms_id]" std_id="[@id]" class="ui-corner-all ui-state-default hoverable">[#new]</button>
                        </td>
                      </tr>  
                 </table>             
       		</fieldset>
		</form>
    	<fieldset>
        	<legend>[#fees]</legend>
            [@due_table]
    	</fieldset>
    </div>
    <div id="student_paids">
		[@ingoing_table]
	</div>
    [@ig_service_div]
</div>