<div class="tabs">
	<ul>
    	<li><a href="#cc_data">[#details]</a></li>
        <li><a href="index.php?module=costcenters&incomereport=[@id]">[#income_report]</a></li>
        <!--<li><a href="index.php?module=balance&financialreport&cc=[@id]">[#financial_report]</a></li>-->
        <li><a href="index.php?module=sms&statics&sms_id=[@sms_id]">[#school_statics]</a></li>
        <li><a href="index.php?module=sms&balance&sms_id=[@sms_id]">[#school_balance]</a></li>
        <li><a href="index.php?module=sms&totals&sms_id=[@sms_id]">[#school_fees]</a></li>
        <li><a href="index.php?module=sms&late_list&sms_id=[@sms_id]">[#late_list]</a></li>
        <li><a href="#cc_servers">[#servers]</a></li>
    </ul>
    <div id="cc_data">
    	
        <form class="ui-state-highlight" style="padding:5px">
          <table border="0" cellspacing="0" width="100%">
            <tbody>
              <tr>
                <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#code]</label></td>
                <td valign="top">
                  <input type="text" name="id" value="[@id]" /> 
                  </td>
              </tr>
              <tr>
                <td class="reverse_align" valign="top" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                <td valign="top">
                  <input name="title"  value="[@title]" type="text" class="required">
                  </td>
              </tr>
              <tr>
                <td class="reverse_align" width="120" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#notes]</label></td>
                <td>
                  <textarea name="notes">[@notes]</textarea>
                </td>
              </tr>
            </tbody>
          </table>
       </form>
       <fieldset>
       	<legend>[#transactions]</legend>
        <table border="0" cellspacing="0" width="100%">
            <tbody>
              <tr>
                <td width="100" valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#begin_date]</label></td>
                <td>
                    <input type="text" name="begin_date" value="[@begin_date]" class="datepicker mask-date"/>
                </td>
                <td valign="top"><label class="label ui-widget-header ui-corner-left reverse_align">[#end_date]</label></td>
                <td><input type="text" name="end_date" value="[@end_date]" class="datepicker mask-date"/></td>
                <td><button type="button" action="submitCCTransSearch" class="ui-corner-all ui-state-default hoverable">[#search]</button></td>
              </tr>
            </tbody>
          </table>
              <table class="tablesorter">
        <thead>
            <tr>
                <th width="68">[#debit]</th>
                <th width="68">[#credit]</th>
                <th width="92">[#code]</th>
                <th>[#description]</th>
                <th>[#notes]</th>
            </tr>
        </thead>
        <tbody>
            [@transactions_rows]                
        </tbody>
    </table>

       </fieldset>
    </div>
    <div id="cc_servers">
        <form name="cost_center_connection">
          <input type="hidden" name="id" value="[@id]" /> 
          <fieldset>
                <div class="toolbox">
                    <a module="connections" title="[#new]" action="newConnection">[#new]<span class="ui-icon ui-icon-document"></span></a>
                 </div>
                [@system_table]
           </fieldset>
        
        </form>
    	
    </div>
    