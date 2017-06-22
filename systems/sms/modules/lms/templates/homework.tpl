<form class="scope homework_form-[@id]">
	<input type="hidden" name="id" value="[@homeworkInpValue]" />
	<input type="hidden" name="lesson_id" value="[@lesson_id]" />
    <input type="hidden" name="service_id" value="[@service_id]" />
	<input type="hidden" name="attachements" class="attachemets_field"  value="[@attachements_value]"/>
	<table class="layout" width="100%">
    	<tr>
        	<td valign="top">
            	[@exercise_toolbox]
            	<div class="homework_exercise">
                	[@exercice_html]
                </div>
            </td>
            <td width="30%"  valign="top" class="ui-state-highlight">
            	<table cellspacing="0" width="100%" border="0">
                	<tr>
                    	<td><label class="label ui-widget-header ui-corner-left">[#date]</label></td>
                        <td><input type="text" value="[@date]" class="datepicker mask-date" name="date"></td>
                   	</tr>
                	<tr>
                    	<td><label class="label ui-widget-header ui-corner-left">[#begin_time]</label></td>
                        <td><input type="text" value="[@time]" class="mask-time" name="time"></td>
                   	</tr>
                	<tr>
                    	<td><label class="label ui-widget-header ui-corner-left">[#time]</label></td>
                        <td><input type="text" value="[@duration]" name="duration" class="input_half"> [#minutes]</td>
                   	</tr>
                   	<tr>
                        <td><label class="label ui-widget-header ui-corner-left">[#points]</label></td>
                        <td> <input type="text" value="0" name="points" class="input_half"> [#points]</td>
                    </tr>
                    <tr>
                    	<td><label class="label ui-widget-header ui-corner-left">[#answer_date]</label></td>
                        <td> <input type="text" value="[@answer_date]" class="datepicker mask-date" name="answer_date"></td>
                    </tr>
                    <tr>
                    	<td><label class="label ui-widget-header ui-corner-left">[#mark_reported]</label></td>
                        <td>
                        	<span class="buttonSet">
                                <input type="radio" value="1" id="mark1[@id]" name="marks" [@selectMarkOn]/>
                                <label for="mark1[@id]">[#yes]</label>
                                <input type="radio" value="0" id="mark0[@id]" name="marks" [@selectMarkOff]/>
                                <label for="mark0[@id]">[#no]</label>
                            </span>
                        </td>
                    </tr>
                </table>
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
   </table>
</div>   
