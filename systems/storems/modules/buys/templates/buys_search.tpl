<form id="command_search_form">
    <div class="accordion">
        <h3>[#command_no]</h3>
        <div>
            <table width="100%" cellspacing="0">
                <tr>
                    <td width="100" valign="middel" class="reverse_align">
                        <label class="label ui-widget-header ui-corner-left">[#id]</label>
                    </td>
                    <td>
                        <input type="text" name="com_id" id="com_id"/>
                  </td>
                </tr>
            </table>
        </div>
        <h3>[#by_client]</h3>
        	<div>
            	<div  class="dashed">
                    <table width="100%" cellspacing="0">
                        <tr>
                            <td width="100" valign="middel" class="reverse_align">
                                <label class="label ui-widget-header ui-corner-left">[#client_name]</label>
                            </td>
                            <td>
                                <input type="text" class="input_double" name="client_name" />
                                <input id="client_id"  name="client_id" class="autocomplete_value" type="hidden" />
                          </td>
                        </tr>
                        <tr>
                            <td width="100" valign="middel" class="reverse_align">&nbsp;</td>
                            <td>
                                <button type="button" action="getCommandsByClient" class="ui-state-default ui-corner-all hoverable"> [#search] <span class="ui-icon ui-icon-search"></span></button>
                          </td>
                        </tr>
                    </table>
                </div>
                <div class="command_search_resuls" style="max-height:250px; overflow:auto">
                
                </div>
           </div>
       </div>
</form>
