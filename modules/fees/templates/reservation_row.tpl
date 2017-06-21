<tr>
	<td align="center" rowspan="[@row_span]">[@ser]</td>
	<td class="unprintable" rowspan="[@row_span]">
        <button module="students"  action="openStudent" std_id="[@id]" sms_id="[@sms_id]" class="ui-state-default hoverable circle_button"><span class="ui-icon ui-icon-person"></span></button>
    </td>
	<td rowspan="[@row_span]" class="unprintable">
        <button std_id="[@id]" action="registerStudent" class="ui-state-default hoverable circle_button" title="[#inscrip]"><span class="ui-icon ui-icon-check"></span></button>
    </td>
    <td rowspan="[@row_span]">[@name]</td>
    <td rowspan="[@row_span]">[@level]</td>
    <td align="center">[@paid]</td>
    <td align="center">[@dues]</td>
    <td align="center">[@reste]</td>
	<td align="center">[@currency]</td>    
	<td class="unprintable">
        <button std_id="[@id]" action="payReservation" class="ui-state-default hoverable circle_button"><span class="ui-icon ui-icon-plus" title="[#add]"></span></button>
    </td>
	<td class="unprintable">
        <button std_id="[@id]" action="refundReservation" class="ui-state-default hoverable circle_button" title="[#refund]"><span class="ui-icon ui-icon-arrowreturnthick-1-w"></span></button>
    </td>
</tr>