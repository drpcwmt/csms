 <form>
    <table class="layout scope" width="100%">
        <input type="hidden" name="service_id" value="[@service_id]" />
        <input type="hidden" name="id" value="[@exercice_id]" />
        <tr>
            <td valign="top" width="40%">
                [@question_bank]
            </td>
            <td valign="top" align="center"  width="60%">
                <fieldset class="ui-corner-all ui-state-highlight">
                	<legend>[#exercise]</legend>
                    <table width="100%" cellpadding="0" cellspacing="0">
                       <tr>
                            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#book]</label></td>
                            <td><select class="required ui-state-default" style="height:24px" name="book_id" serviceid="[@service_id]"  update="searchQuestion">[@book_id_options]</select></td>
                            <td>&nbsp;</td>
                            <td>
                                <span class="buttonSet">
                                    <input type="radio" value="1" id="privatetrue" name="private" [@selectprivateOn]/>
                                    <label for="privatetrue">[#private]</label>
                                    <input type="radio" value="0" id="privatefalse" name="private" [@selectPrivateOff]/>
                                    <label for="privatefalse">[#public]</label>
                                </span>
                            </td>
                       </tr>
                       <tr>
                            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#chapter]</label></td>
                            <td><select class="required ui-state-default" style="height:24px" name="chapter_id"  update="searchQuestion">[@chapter_id_options]</select></td> 
                            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#layout]</label></td>
                            <td>
                            	<select name="layout" class="ui-state-default">
                                	<option value="slide">[#slide]</option>
                                    <option value="page">[#one_page]</option>
                                    <option value="pages">[#multi_pages]</option>
                                </select>
                            </td>
                        </tr>    
                       <tr>
                            <td><label class="ui-widget-header ui-corner-left label">[#lesson]</label></td>
                            <td>
                                <input type="text" name="title" class="input_double ui-state-default" after="searchQuestion"/>
                                <input type="hidden" class="autocomplete_value" name="summary_id" />
                            </td>
                            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#questions]</label></td>
                            <td><div class="ui-widget-content ui-corner-right count_questions" style="width:150px; font-size:12px; padding:4px">&nbsp;[@count_questions]</div></td>
                       </tr>
                        <tr>
                            <td width="15%"><label class="ui-widget-header ui-corner-left label">[#title]</label></td>
                            <td><input type="text" name="exercise_title" class="input_double required ui-state-default"  /></td>
                            <td><label class="ui-widget-header ui-corner-left label">[#time]</label></td>
                            <td>
                            	<div class="ui-widget-content ui-corner-right total_time" style="width:150px; font-size:12px; padding:4px">&nbsp;[@total_time]</div>
                            </td>
                       </tr>
                        <tr>
                        	<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><label class="ui-widget-header ui-corner-left label">[#points]</label></td>
                            <td><div class="ui-widget-content ui-corner-right total_points" style="width:150px; font-size:12px; padding:4px">&nbsp;[@total_points]</div></td>
                       </tr>
                   </table>
                </fieldset>
                [@toolbox]
                <ul class="pageNav">[@page_nav]</ul>
                <br clear="all" style="float:none" />
                <ol class="questions_list ui-widget-content ui-corner-all connectedSortable"></ol>
            </td>
        </tr>
    </table>
</form>