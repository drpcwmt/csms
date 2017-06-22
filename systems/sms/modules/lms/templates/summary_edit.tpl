<form class="summary_form [@class_id] scope" serviceid="[@service_id]" >
	<input type="hidden" name="id" value="[@id]" />
	<input type="hidden" name="service_id" value="[@service_id]" />
	<input type="hidden" name="attachements" class="attachemets_field"  value="[@attachements_value]"/>
    [@lesson_id_field]
	<table width="100%" cellspacing="5" cellpadding="0" border="0">
    	<tr>
        	<td width="75%" valign="top">
                <fieldset class="ui-state-highlight"> 
                    <legend>[#book]</legend>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#book]</label></td>
                            <td>
                                <button class="ui-state-default hoverable def_float" action="editBook" serviceid="[@service_id]" type="button" style="padding:0; height:24px">
                                    <span class="ui-icon ui-icon-plus"></span>
                                </button>
                                <select class="required ui-state-default" style="height:24px" name="book_id" serviceid="[@service_id]">[@book_id_options]</select>
                            </td>
                            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#chapter]</label></td>
                            <td>
                                 <button class="ui-state-default hoverable def_float" action="editChapter" type="button" style="padding:0; height:24px ">
                                    <span class="ui-icon ui-icon-plus"></span>
                                </button>
                               <select class="required ui-state-default" style="height:24px" name="chapter_id">[@chapter_id_options]</select>
                            </td>
                       </tr>
                         <tr>
                            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#title]</label></td>
                            <td colspan="3"><input type="text" name="title" class="required input_double ui-state-default" value="[@title]"/><input type="hidden" class="autocomplete_value" name="id" value="[@id]"/></td>
                        </tr>
                   </table>
                </fieldset>
            </td>
            <td valign="top">
                <fieldset class="ui-corner-all ui-widget-content">
                	<legend class="ui-widget-header ui-corner-all">[#attachements]&nbsp;
                    	<button module="documents" action="attachFile" style="padding:0px" class="ui-corner-all ui-state-default hoverable" type="button">
                        	<span class="ui-icon ui-icon-plus"></span>
                        </button>
                    </legend>
                    <table class="result fixed attachemets_table">[@attachements_list]</table>
                </fieldset>            
            </td>
        </tr>
        <tr>
        	<td colspan="2">
            	<textarea name="summary" class="tinymce" style="min-height:350px">[@summary]</textarea>
            </td>
        </tr>
    </table>
</form>   