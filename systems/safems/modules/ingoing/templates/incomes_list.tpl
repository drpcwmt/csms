<div class="toolbox">
     <a action="print_tab" rel="#student_ingoing">[#print]<span class="ui-icon ui-icon-print"></span></a>
</div>

<form class="ui-state-highlight">
	<input type="hidden" name="income_code" value="[@income_code]" />
    <input type="hidden" name="type" value="[@type]" />
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
          </tr>
          <tr>
            <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#direction]</label></td>
            <td valign="top">
              <select name="direction">
              	<option value="ingoing">[#ingoing]</option>
                <option value="outgoing">[#outgoing]</option>
              </select>
             </td>
          	<td colspan="2" align="center">
            	<button type="button" action="submitIncomeList" class="ui-corner-all ui-state-default hoverable">[#search]</button>
            </td>
          </tr>
        </tbody>
    </table>
</form>

<h2 class="title">[#total]: <span id="applications_total">[@total]</span></h2>
<table class="tablesorter">
	<thead>
    	<tr>
        	<th width="30">[#ser]</th>
            <th>[#date]</th>
            <th>[#client]</th>
            <th>[#school]</th>
            <th>[#notes]</th>
            <th>[#value]</th>
        </tr>
    </thead>
    <tbody id="app_list_tbody">
    	[@app_list_trs]
    </tbody>
</table>

</table>