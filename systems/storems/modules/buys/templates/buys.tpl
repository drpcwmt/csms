<div id="command_div-[@id]">
    <div class="toolbox">
        <a action="addBuyPayment" title="F10" class="[@but_deliver_hidden]"> [#payment] <span class="ui-icon ui-icon-tag"></span></a>
        <a action="delevierBuy" class="[@but_deliver_hidden]"> [#deliver] <span class="ui-icon ui-icon-cart"></span></a>
        <a action="saveBuy" title="F2"> [#save] <span class="ui-icon ui-icon-disk"></span></a>
        <a action="resetbuy" class="[@but_reset_hidden]"> [#reset] <span class="ui-icon ui-icon-refresh"></span></a>
    </div>
    <div class="tabs">
        <ul>
            <li><a href="#buy_detail">[#buy_order]</a></li>
            <li><a href="index.php?module=contener&com_id=[@id]">[#contener]</a></li>
        </ul>
        <div id="buy_detail">
            <form id="buy_form-[@id]" onsubmit="return nextBuyField();" action="#">
                <input type="hidden" name="id" value="[@id]" />
                <div class="dashed">
                    <table width="100%" cellspacing="0">
                        <tr>
                            <td width="120" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#supplier] <span class="astrix">*</span></label>
                            </td>
                            <td>
                                <input type="text" class="input_double required" name="supplier_name" value="[@supplier_name]" />
                                <input id="supplier_id"  name="supplier_id" class="autocomplete_value" type="hidden" value="[@supplier_id]" />
                            </td>
                             <td width="120" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#total] </label>
                            </td>
                            <td>
                                <input type="text" disabled id="buy_total" name="tot" value="[@tot]"  class="input_half" />
                            </td>
                        </tr>
                        <tr>
                           <td width="120" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#date] <span class="astrix">*</span></label>
                            </td>
                            <td>
                                <input type="text" class="required mask-date datepicker" name="date" value="[@date]" />
                            </td>
                            <td width="120" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#code]</label>
                            </td>
                            <td>
                                <input type="text" disabled id="id_label" value="[@id]" class="input_half" />
                            </td>
                        </tr>
                    </table>
            
                    <table class="result buys_item">
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
                            [@buys_items_trs]
                            <tr class="new_buy_tr">
                                <td style="padding:1px 2px">
                                    <button type="button" action="removeBuyItem" class="ui-state-default ui-corner-all hoverable circle_button"><span class="ui-icon ui-icon-close"></span></button>
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="item_id[]" class="input_half no-corner" update="getItemData" />
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
                            <tr class="new_buy_tr">
                                <td style="padding:1px 2px">
                                    <button type="button" action="removeBuyItem" class="ui-state-default ui-corner-all hoverable circle_button hidden"><span class="ui-icon ui-icon-close"></span></button>
                                </td>
                                <td style="padding:0">
                                    <input type="text" name="item_id[]" class="input_half no-corner" update="getItemData" onfocus="addNewItem(this)" />
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
                </div>
            </form>
        </div>
    </div>
</div>