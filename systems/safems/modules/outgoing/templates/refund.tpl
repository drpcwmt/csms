<div id="refund_div">
	<h2 class="title">[#school_fees_refund]: [@school_name]</h2>
    <form class="student_search">
        <fieldset class="ui-state-highlight">
            <input type="hidden" name="ccid" value="[@sms_id]" />
            <table border="0" cellspacing="0">
                <tbody>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#name]</label></td>
                    <td valign="top">
                      <input class="input_double required" name="name" id="refund_student_name" sms_id="[@sms_id]" type="text">
                      <input name="std_id" class="autocomplete_value required" type="hidden" />
                      <button type="button" action="submitSearchRefund" sms_id="[@sms_id]" class="ui-corner-all ui-state-default hoverable">[#search]</button>
                      </td>
                  </tr>
                  <tr>
                    <td class="reverse_align" valign="middel" width="120"><label class="label ui-widget-header ui-corner-left reverse_align">[#year]</label></td>
                    <td valign="top">
                      <select name="year" class="combobox">[@year_opts]</select>
                    </td>
                  </tr>
                </tbody>
            </table>
        </fieldset>
        <div id="std_refundable_div">
        </div>
    </form>
</div>            