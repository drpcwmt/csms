<form class="ui-widget-content ui-corner-bottom transparent_div" id="late_form">
	<input type="hidden" name="sms_id" value="[@sms_id]" />
    <div class="toolbox">
        <a action="print_pre" rel="#late_form" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
    </div>
	<fieldset class="ui-state-highlight">
        <table border="0" cellspacing="0">
          <tr>
            <td width="120" valign="middel">
                <label class="label ui-widget-header ui-corner-left reverse_align" >[#level]</label>
            </td>
            <td width="200" >
                <select name="level_id" class="combobox">
                    [@levels_opts]
                </select>
            </td>
            <td width="120" valign="middel">
                <label class="label ui-widget-header ui-corner-left reverse_align" >[#fees]</label>
            </td>
            <td width="200">
                <select name="fees_acc" class="combobox">
                    [@fees_opts]
                </select>
            </td>
            <td>
            	<button class="ui-state-default hoverable" type="button" module="fees" action="searchLateList">
                	<span class="ui-icon ui-icon-search"></span>
                    [#search]
                </button>
            </td>
          </tr>
       </table> 
 	</fieldset>
    
    <div class="hidden showforprint">
        <h4>[@today]</h4>
        <h2 align="center">[#late_list]</h2>
        <h3 align="center">[@school_name]</h3>
    </div>
    <table class="tablesorter">
        <thead>
            <tr>
                <th class="unprintable [@prvlg_std_read]" width="20">&nbsp;</th>
                <th>[#name]</th>
                <th>[#level]</th>
                <th width="60">[#currency]</th>
                <th width="120">[#total]</th>
                <th width="120">[#paid]</th>
                <th width="120">[#rest]</th>
            </tr>
        </thead>
        <tbody>
            [@tbody_trs]
        </tbody>
        <tfoot>
            [@tfoot_trs]
        </tfoot>
    </table>
</form>       