 <fieldset class="ui-state-highlight">
 	<input type="hidden" name="service_id" value="[@service_id]" />
   <legend>[#book]</legend>
   <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#book]</label></td>
            <td>
                <select class="required ui-state-default" style="height:24px" name="book_id" serviceid="[@service_id]">[@book_id_options]</select>
            </td>
            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#chapter]</label></td>
            <td>
               <select class="required ui-state-default" style="height:24px" name="chapter_id">[@chapter_id_options]</select>
            </td>
       </tr>
       <tr>
            <td><label class="ui-widget-header ui-corner-left label">[#lesson]</label></td>
            <td colspan="3">
                <input type="text" name="title" class="input_double ui-state-default" value="[@summary_title]"/>
                <input type="hidden" class="autocomplete_value" name="summary_id" value="[@summary_id]"/>
            </td>
       </tr>
   </table>
 </fieldset>
