<div class="tabs">
	<ul>
    	<li><a href="#group_settings_tab">[#settings]</a></li>
        <li><a href="#group_routes_tab">[#routes]</a></li>
    </ul>
    <div id="group_settings_tab">
      <form name="bus_fess_form">
        <div class="toolbox">
            <a action="saveBus" title="[#save]">[#save]<span class="ui-icon ui-icon-disk"></span></a>
            <a action="newRouteFees" group_id="[@group_id]" busms_id="[@busms_id]" sms_id="[@sms_id]" title="[#new]">[#new]<span class="ui-icon ui-icon-plus"></span></a>
            <a action="print_pre" rel="bus_fees-[@group_id]" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
        </div>
        
        <h2 class="title">[#group]: [@group_id]</h2>
        <table class="tableinput">
            <thead>
                <tr>
                    <th>[#title]</th>
                    <th width="90">[#value]</th>
                    <th width="120">[#currency]</th>
                    <th width="90">[#discountable]</th>
                    <th width="90">[#acc_code]</th>
                    <th class="unprintable" width="20">&nbsp;</th>
               </tr>
            </thead>
            <tbody>
                [@bus_fees_rows]
            </tbody>
         </table>
         
       </form>
    </div>
    <div id="group_routes_tab">
    
    </div>
</div>