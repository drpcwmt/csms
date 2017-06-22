<form id="new_year_wizard_form">
	<input type="hidden" name="wizard_step" id="wizard_step" value="1" />
	<div style="margin:10px 40px; padding:10px" class="ui-state-highlight ui-corner-all">
		<h3 style="padding-bottom:30px; text-align:center">[#new_year_setup] [@this_year] / [@next_year]</h3>
		<table border="0" cellspacing="0">
			<tr>
				<td width="120" valign="middel">
					<label class="label ui-widget-header ui-corner-left">[#begin_date]</label>
				</td>
                <td>
					<input type="text" id="begin_date" name="begin_date" class="mask-date datepicker" value="01/09/[@this_year]" />
                </td>
            </tr>
			<tr>
				<td width="120" valign="middel">
					<label class="label ui-widget-header ui-corner-left">[#end_date]</label>
				</td>
                <td>
					<input type="text" id="end_date" name="end_date" class="mask-date datepicker"  value="30/06/[@next_year]"/>
                </td>
            </tr>
		</table>
		
        <fieldset>
			<legend>[#resources]</legend>
            <table border="0" cellspacing="0">
                <tr>
                    <td width="120" valign="middel">
                        <label class="label ui-widget-header ui-corner-left">[#levels]</label>
                    </td>
                    <td>
                    	<a class="icon_button hoverable" action="openResources" rel="levels" title="[#edit]">
                        	<span class="ui-icon ui-icon-extlink"></span>
                        </a>
                        <div class="fault_input">[@count_levels]</div>
                    </td>
                </tr>
                <tr>
                    <td width="120" valign="middel">
                        <label class="label ui-widget-header ui-corner-left">[#profs]</label>
                    </td>
                    <td>
                    	<a class="icon_button hoverable" action="openResources" rel="profs" title="[#edit]">
                        	<span class="ui-icon ui-icon-extlink"></span>
                        </a>
                        <div class="fault_input">[@count_profs]</div>
                    </td>
                </tr>
                <tr>
                    <td width="120" valign="middel">
                        <label class="label ui-widget-header ui-corner-left">[#supervisors]</label>
                    </td>
                    <td>
                    	<a class="icon_button hoverable" action="openResources" rel="supervisors" title="[#edit]">
                        	<span class="ui-icon ui-icon-extlink"></span>
                        </a>
                        <div class="fault_input">[@count_supervisors]</div>
                    </td>
                </tr>
                <tr>
                    <td width="120" valign="middel">
                        <label class="label ui-widget-header ui-corner-left">[#principals]</label>
                    </td>
                    <td>
                    	<a class="icon_button hoverable" action="openResources" rel="principals" title="[#edit]">
                        	<span class="ui-icon ui-icon-extlink"></span>
                        </a>
                        <div class="fault_input">[@count_principals]</div>
                    </td>
                </tr>
                <tr>
                    <td width="120" valign="middel">
                        <label class="label ui-widget-header ui-corner-left">[#halls]</label>
                    </td>
                    <td>
                    	<a class="icon_button hoverable" action="openResources" rel="halls" title="[#edit]">
                        	<span class="ui-icon ui-icon-extlink"></span>
                        </a>
                        <div class="fault_input">[@count_halls]</div>
                    </td>
                </tr>
            </table>
		</fieldset>
    </div>
</form>    
