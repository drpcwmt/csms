<div class="tabs">
	<ul>
        <li><a href="#suppliers_trans_tab">[#transactions]</a></li>
    	<li><a href="#suppliers_products">[#products]</a></li>
    	<li><a href="#suppliers_details_tab">[#infos]</a></li>
    </ul>
    <div id="suppliers_trans_tab">
        <div class="toolbox">
            <a action="newBuy" module="buys" supid="[@id]">[#new] <span class="ui-icon ui-icon-plus"></span></a>
            <a action="searchBuys">[#search] <span class="ui-icon ui-icon-search"></span></a>
        </div>
    	[@transactions_trs]
    </div>
    <div id="suppliers_products">
    	<form id="supppliers_products_form-[@id]" >
        	<input name="sup_id" value="[@id]" type="hidden" />
        	<fieldset class="ui-state-highlight ui-corner-all">
            	<legend>[#add]</legend>
                <table class="result" style="margin-bottom:2px">
                    <thead>
                        <tr>
                            <th width="73">[#id]</th>
                            <th>[#name]</th>
                            <th width="73">[#price]</th>
                            <th width="73">[#code]</th>
                            <th width="28" style="background-image:none" class="unprintable">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 0px; border: 0px none;">
                                <input type="text" update="getSupplierProductData" class="input_half no-corner" name="item_id" />
                            </td>
                            <td style="padding: 0px; border: 0px none;">
                                <input type="text" class="item_name input_double no-corner" name="name" />
                                <input type="hidden" class="autocomplete_value" />
                            </td>
                            <td style="padding: 0px; border: 0px none;">
                                <input type="text" class="input_half no-corner" name="price">
                            </td>
                            <td style="padding: 0px; border: 0px none;">
                                <input type="text" class="input_half no-corner" name="barcode">
                            </td>
                            <td style="padding: 0px; border: 0px none;">
                                <button type="button" action="addNewSupplierProduct" class="ui-state-default ui-corner-all hoverable hand">
                                    [#add]
                                </button>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </fieldset>
            <table class="tablesorter products_list">
                <thead>
                    <tr>
                        <th width="20" style="background-image:none" class="unprintable">&nbsp;</th>
                        <th width="20" style="background-image:none" class="unprintable">&nbsp;</th>
                        <th width="73">[#id]</th>
                        <th>[#name]</th>
                        <th width="73">[#price]</th>
                        <th width="73">[#code]</th>
                        <th width="50">[#quantity]</th>
                    </tr>
                </thead>
                <tbody>
                    [@products_trs]
                </tbody>
            </table>
        </form>
    </div>
    <div id="suppliers_details_tab">
    	[@details_tab]
    </div>
</div>