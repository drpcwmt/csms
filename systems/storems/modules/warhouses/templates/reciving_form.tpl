<div class="toolbox">
	<a action="saveRecivingOrder">[#save]<span class="ui-icon ui-icon-disk"></span></a>
</div>
<form name="reciving_form">
	<input type="hidden" name="war_id" value="[@war_id]" />
	<table width="100%">
    	<tr>
        	<td valign="top" width="33%">
                <div class="dashed">
                    <div class="accordion">
                        <h3>[#from_supplier]</h3>
                        <div style="padding:5px">
                            <table width="100%">  
                                <tr>
                                    <td width="80" valign="middel" class="reverse_align">
                                        <label class="label ui-widget-header ui-corner-left">[#supplier]</label>
                                    </td>
                                    <td>
                                        <input type="text" name="supplier" id="reciver_supplier" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="80" valign="middel" class="reverse_align">
                                        <label class="label ui-widget-header ui-corner-left">[#order_no]</label>
                                    </td>
                                    <td>
                                        <input type="text" name="supplier_order_no" id="supplier_order_no"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <h3>[#from_warhouse]</h3>
                        <div style="padding:5px">
                            <table width="100%">  
                                <tr>
                                    <td width="80" valign="middel" class="reverse_align">
                                        <label class="label ui-widget-header ui-corner-left">[#supplier]</label>
                                    </td>
                                    <td>
                                        <select name="warhouse" class="combobox">
                                            [@warhouses_opts]
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="80" valign="middel" class="reverse_align">
                                        <label class="label ui-widget-header ui-corner-left">[#order_no]</label>
                                    </td>
                                    <td>
                                        <input type="text" name="war_order_no" id="war_order_no" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <h3>[#others]</h3>
                        <div style="padding:5px">
                            <table width="100%">  
                                <tr>
                                    <td width="80" valign="middel" class="reverse_align">
                                        <label class="label ui-widget-header ui-corner-left">[#others]</label>
                                    </td>
                                    <td>
                                        <select name="others" class="combobox">
                                            [@others_opts]
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
			</td>
           	<td valign="top">
            	<table class="tablesorter">
                	<thead>
                    	<tr>
                        	<th width="24" style="background-image:none">&nbsp;</th>
                            <th>[#name]</th>
                            <th width="80">[#quantity]</th>
                		</tr>
                    </thead>
                    <tbody>
                    	<tr>
                        	<td>
                            	<button class="ui-corner-all ui-state-default circle_button"><span class="ui-icon ui-icon-close"></span></button>
                            </td>
                            <td>
                            	<input type="text" class="input_double" name="product_name[]" />
                                <input type="hidden" name="prod_id[]" class="autocomplete_value" />
                            </td>
                            <td>
                            	<input type="text" class="input_half" name="quantity[]" />
                            </td>
                        </tr>
                   	</tbody>
                </table>
            </td>
        </tr>
    </table>
</form>
