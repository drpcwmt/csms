<div class="toolbox">
	<a rel="#absent_rate_tab" action="print_tab"><span class="ui-icon ui-icon-print"></span>[#print]</a>
</div>
<div class="showforprint hidden">
	<h2>[#attendance_rates]</h2>
</div>
<form id="absent_rate_form" class="ui-corner-all ui-state-highlight unprintable">
	<input type="hidden" name="con" value="class" />
	<table border="0" cellspacing="0">
      <tr class="[@con_id_select]">
        <td width="120" valign="middel">
        	<label class="label ui-widget-header ui-corner-left reverse_align" >[#class]</label></td>
        <td>
            <select name="con_id" class="combobox" update="reloadTerms">
            [@con_id_opts]
            </select>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel">
        	<label class="label ui-widget-header ui-corner-left reverse_align" >[#period]</label></td>
        <td>
            <select class="absent_rate_terms combobox">
            [@periods_opts]
            </select>
        </td>
      </tr>
      <tr>
        <td width="120" valign="middel">&nbsp;</td>
        <td>
            <button type="button" class="hoverable ui-corner-all ui-state-default" style="margin:0px 50px" action="submitAbsentRateSearch">
            	<span class="ui-icon ui-icon-search"></span>[#search]
            </button>
        </td>
      </tr>
	</table>
</form>
<div id="absents_rate_div">
	<table  width="100%">
    	<tr>
        	<td valign="top">
            	[@rate_table]
            </td>
            <td valign="top" width="400">
         		[@rate_chart]
            </td>
        </tr>
    </table>
</div>