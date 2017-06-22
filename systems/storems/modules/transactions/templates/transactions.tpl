<div id="[@transaction_type]_div-[@id]">
    <div class="tabs">
        <ul>
            <li><a href="#transaction_detail">[@title]</a></li>
            <li><a href="index.php?module=contener&[@transaction_type]_id=[@id]">[#contener]</a></li>
        </ul>
        <div id="transaction_detail">
        	<h3 align="center" class="showforprint hidden">[@title]</h3>
            <form name="transaction_form" onsubmit="return nextCommandField();" action="#" class="transactions_form">
                <input type="hidden" name="id" value="[@id]" />
                <input type="hidden" name="status" value="[@status]" />
                <fieldset class="ui-state-highlight ui-corner-all">
                    <table width="100%" cellspacing="0">
                        <tr>
                          <td valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[@from_title] <span class="astrix">*</span></label></td>
                          <td>
                          	 [@from_name]
                             <input name="from_id" class="autocomplete_value" type="hidden" value="[@from_id]" />
                             <input name="from" type="hidden" value="[@from]" />
                          </td>
                          <td width="120" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#code]</label></td>
                          <td><input type="text" disabled class="id_label input_half" value="[@id]" /></td>
                        </tr>
                        <tr>
                            <td width="120" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[@to_title] <span class="astrix">*</span></label>
                            </td>
                            <td>
                                [@to_name]
                                <input name="to_id" class="autocomplete_value" type="hidden" value="[@to_id]" />
                                <input name="to" type="hidden" value="[@to]" />
                                
                               
                            </td>
                            <td width="120" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#status]</label></td>
                            <td><div class="fault_input status" style="width:75px">[@status]</div></td>
                        </tr>
                        <tr>
                           <td width="120" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#date] <span class="astrix">*</span></label>
                            </td>
                            <td>
                                <input type="text" class="required mask-date datepicker" name="issue_date" value="[@issue_date]" />
                            </td>
                            <td valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#total]</label></td>
                            <td><input type="text" disabled name="total" value="[@total]"  class="input_half" /></td>
                        </tr>
                        <tr>
                          <td valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#delivery_date]</label></td>
                          <td><input type="text" class="required mask-date datepicker" name="delivery_date" value="[@delivery_date]" /></td>
                          <td valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">[#paid]</label></td>
                          <td><input type="text" disabled name="paid" value="[@paid]"  class="input_half" /></td>
                        </tr>
                        <tr>
                          <td valign="middel" class="reverse_align">&nbsp;</td>
                          <td><label><input name="shipping" value="1" type="checkbox" [@shipping_check] />[#include_shipping]</label></td>
                          <td valign="middel" class="reverse_align">&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                    </table>
            	</fieldset>
                    <table class="result items_list">
                        <thead>
                            <tr>
                                <th class="unprintable" style="background-image:none" width="18">&nbsp;</th>
                                <th width="73">[#code]</th>
                                <th>[#name]</th>
                                <th width="73">[#quantity]</th>
                                <th width="73">[#price]</th>
                                <th width="73">[#total]</th>
                            </tr>
                        </thead>
                        <tbody>
                            [@items_trs]
                            <tr class="new_command_tr [@new_command_tr]">
                                <td style="padding:1px 2px" class="unprintable">
                                    <button type="button" action="removeTransactionItem" class="ui-state-default ui-corner-all hoverable circle_button"><span class="ui-icon ui-icon-close"></span></button>
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="item_id[]" class="input_half no-corner" update="getProductData" />
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="name[]" class="item_name input_double no-corner" />
                                    <input class="autocomplete_value" type="hidden" />
                                    <button type="button" module="products" action="openProduct" prodid="" class="ui-state-default ui-corner-all hoverable circle_button hidden"><span class="ui-icon ui-icon-extlink"></span></button>
                               </td>
                                <td style="padding:0">
                                    <input type="text" name="quantity[]" class="input_half no-corner" />
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="price[]" class="input_half no-corner" />
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="total[]" class="input_half no-corner" disabled />
                                </td>
                            </tr>
                            <tr class="new_command_tr">
                                <td style="padding:1px 2px"  class="unprintable">
                                    <button type="button" action="removeTransactionItem" class="ui-state-default ui-corner-all hoverable circle_button hidden"><span class="ui-icon ui-icon-close"></span></button>
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="item_id[]" class="input_half no-corner" update="getProductData" onfocus="addNewItem(this)" />
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="name[]" class="item_name input_double no-corner" />
                                    <input class="autocomplete_value" type="hidden" />
                                    <button type="button" module="products" action="openProduct" prodid="" class="ui-state-default ui-corner-all hoverable circle_button hidden"><span class="ui-icon ui-icon-extlink"></span></button>
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="quantity[]" class="input_half no-corner"  />
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="price[]" class="input_half no-corner" [@disabled_prvlg]/>
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="total[]" class="input_half no-corner" disabled />
                                </td>
                            </tr>
                       </tbody>
                   </table>
            </form>
        </div>
    </div>
</div>