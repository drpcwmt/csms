<div class="toolbox">
     <a action="print_tab">[#print]<span class="ui-icon ui-icon-print"></span></a>
</div>

<form class="ui-state-highlight [@form_hidden]">
	<input type="hidden" name="acc" value="[@full_code]"/>
	<table border="0" cellspacing="0">
        <tbody>
          <tr>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#begin_date]</label></td>
            <td valign="top">
              <input class="datepicker mask-date" name="begin_date" value="[@begin_date]" type="text">
            </td>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#end_date]</label></td>
            <td valign="top">
              <input type="text"class="datepicker mask-date" name="end_date" value="[@end_date]" />
             </td>
          	<td colspan="4" align="center">
            	<button type="button" module="accounts" action="submitTransTable" class="ui-corner-all ui-state-default hoverable"><span class="ui-icon ui-icon-search"></span> [#search]</button>
            </td>
          </tr>
        </tbody>
    </table>
</form>
<div class="trans_list">
    <table class="tablesorter">
        <thead>
            <tr>
                <th width="20" class="unprintable">&nbsp;</th>
                <th width="60">[#code]</th>
                <th width="80">[#debit]</th>
                <th width="80">[#credit]</th>
                <th width="60">[#currency]</th>
                <th width="90" class="{ dateFormat: "ddmmyyyy" }">[#date]</th>
                <th>[#notes]</th>
            </tr>
        </thead>
        <tbody id="app_list_tbody">
            [@trs]
        </tbody>
        <tfoot>
            [@tfoot]
        </tfoot>
    </table>
</div>
