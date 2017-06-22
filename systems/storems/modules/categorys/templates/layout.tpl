<div class="ui-widget-content ui-corner-all transparent_div scoop">
	<form>
        <div class="toolbox">
            <a module="products" cat_id="[@id]" action="newProduct">[#new_prod] <span class="ui-icon ui-icon-document"></span></a>
            <a action="saveCat">[#save] <span class="ui-icon ui-icon-disk"></span></a>
            <a action="deleteCat">[#delete] <span class="ui-icon ui-icon-trash"></span></a>
            <a action="printScoop" title="[#print]">[#print]<span class="ui-icon ui-icon-print"></span></a>
        </div>
    	<input type="hidden" name="id" value="[@id]" />
        <fieldset  class="ui-state-highlight">
            <table width="100%" cellspacing="0">
                <tr>
                    <td width="100" valign="middel" class="reverse_align">
                        <label class="label ui-widget-header ui-corner-left">[#name] <span class="astrix">*</span></label>
                    </td>
                    <td >
                        <input type="text" class="input_double required" name="title" value="[@title]" />
                    </td>
                </tr>
                <tr>
                    <td width="100" valign="middel" class="reverse_align">
                        <label class="label ui-widget-header ui-corner-left">[#code] <span class="astrix">*</span></label>
                    </td>
                    <td >
                        <div class="fault_input">[@full_code]</div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <h2 class="hidden showforprint titlt">[@cat_name]</h2>
	[@items]
</div>