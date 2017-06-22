<form id="payments_form" name="payments_form">
    <input type="hidden" name="trans_id" />
	<input type="hidden" name="to" />
	<input type="hidden" name="to_id" />
	<input type="hidden" name="from" />
	<input type="hidden" name="from_id" />
    
    <div class="dashed">
        <table width="100%" cellspacing="0">
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#amount] <span class="astrix">*</span></label>
                </td>
                <td>
                    <input type="text" class="required" name="amount" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#date] <span class="astrix">*</span></label>
                </td>
                <td>
                    <input type="text" class="mask-date datepicker" name="recive_date" value="[@date]" />
                </td>
            </tr>
            <tr>
                <td width="120" valign="middel" class="reverse_align">
                    <label class="label ui-widget-header ui-corner-left">[#type] <span class="astrix">*</span></label>
                </td>
                <td>
                    <select name="type" update="tooglePaymentOptions" class="combobox">
                        <option value="cash">[#cash]</option>
                        <option value="visa">[#visa]</option>
                        <option value="transfer">[#money_transfer]</option>
                        <option value="others">[#others]</option>
                    </select>
                </td>
            </tr>
            <tr class="hidden" id="paymentOptionTr">
                <td width="120" valign="middel" class="reverse_align" valign="top">
                    <label class="label ui-widget-header ui-corner-left">[#comments]</label>
                </td>
                <td>
                    <textarea name="comments"></textarea>
                </td>
            </tr>
        </table>
    </div>
</form>   