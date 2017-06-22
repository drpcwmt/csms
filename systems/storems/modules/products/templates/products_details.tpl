<form name="prod_details" id="prod_details-[@id]">
  	<input name="id" value="[@id]"  type="hidden"/>
    <input name="sub_id" value="[@sub_id]"  type="hidden"/>
    <input name="cat_id" value="[@cat_id]"  type="hidden"/>
    <table width="100%" cellspacing="2">
        <tr>
            <td valign="top">
                <div class="dashed">
                    <table width="100%" cellspacing="0">
                    	<tr>
                        <td width="100" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#code]</label>
                            </td>
                            <td>
                                <div class="fault_input ui-corner-right " style="width:75px">[@code]</div>
                            </td>
                        </tr>
                        <tr>
                            <td width="100" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#name] <span class="astrix">*</span></label>
                            </td>
                            <td colspan="3">
                                <input type="text" class="input_double required" name="title" value="[@title]" />
                          </td>
                      </tr>
                        <tr>
                            <td width="100" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#category] <span class="astrix">*</span></label>
                            </td>
                            <td colspan="3">
                                <div class="fault_input">[@cat_name]</div>
                          </td>
                      </tr>     
                        <tr>
                            <td width="100" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left"><span class="astrix">*</span></label>
                            </td>
                            <td colspan="3">
                                <div class="fault_input">[@sub_name]</div>
                          </td>
                      </tr>     
                        <tr>
                            <td width="100" valign="middel" >
                                <label class="label ui-widget-header ui-corner-left">[#price]</label>
                            </td>
                            <td colspan="3">
                                <input type="text" name="price" value="[@price]"/>
                          </td>
                      </tr>     
                      <tr>
                        <td width="100" valign="top" class="reverse_align">
                            <label class="label ui-widget-header ui-corner-left">[#barcode]</label>
                        </td>
                        <td valign="top">
                          <input type="text" name="barcode" value="[@barcode]" />
                        <div id="bcTarget" class="barcode" value="[@barcode_tag]"></div>
                        </td>
                      </tr>
                      <tr>
                        <td width="100" valign="top" class="reverse_align">
                            <label class="label ui-widget-header ui-corner-left">[#unit]</label>
                        </td>
                        <td colspan="3" valign="top">
                          <select name="unit">
                          	[@units_opts]
                          </select>
                          </td>
                      </tr>
                      <tr>
                        <td width="100" valign="top" class="reverse_align">
                            <label class="label ui-widget-header ui-corner-left">[#contener]</label>
                        </td>
                        <td colspan="3" valign="top">
                          <input type="text" name="contener" value="[@contener]" class="input_half">
                          </td>
                      </tr>
                      <tr>
                        <td width="100" valign="top" class="reverse_align">
                            <label class="label ui-widget-header ui-corner-left">[#notes]</label>
                        </td>
                        <td colspan="3" valign="top">
                          <textarea name="comment">
                          	[@comment]
                          </textarea>
                          </td>
                      </tr>
                    </table>
              </div>
            </td>
            <td width="20%" valign="top">
                <div class="dashed">
                    <a class="hand" onclick="changProdcThumb([@id])">
                        <img width="145" height="145" src="[@icon_path]" title="[#change]"  />
                    </a>
                </div>
            </td>
        </tr>
    </table>
          
        
</form>   
