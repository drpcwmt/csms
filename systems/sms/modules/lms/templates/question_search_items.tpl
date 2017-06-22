<li questionid="[@question_id]">
    <input type="hidden" name="question" value="[@question_id]" />
    <input type="hidden" name="points" value="[@point]" />
    <input type="hidden" name="time" value="[@time]" />
	<div class="ui-widget-header ui-corner-top def_align" style="padding:2px;">
        <em>
            <text class="holder-book-[@book_id]">[@book_name]</text> &raquo;
            <text class="holder-chapter-[@chapter_id]">[@chapter_name]</text> &raquo;
            <text class="holder-summary_title-[@summary_id]">[@summary_title]</text>
        </em>
    </div>
    <div class="ui-widget-content ui-corner-bottom" style="padding:2px">
    	<table width="100%">
        	<tr>
                <td width="20" valign="top">
                    <input type="checkbox" name="selected_question" class="setect_question_but" />
                	<a class="hand def_float ui-state-default ui-corner-all hoverable remove_question_but" action="removeQuestion">
                    	<span class="ui-icon ui-icon-close"></span>
                    </a>
                	<a class="hand def_float ui-state-default ui-corner-all hoverable open_question_but" action="editQuestion" module="lms" questionid="[@question_id]" serviceid="[@service_id]">
                    	<span class="ui-icon ui-icon-pencil"></span>
                    </a>
                    <a class="hand def_float ui-state-default ui-corner-all hoverable points_question_but" style="width:16px; height:16px; text-align:center;" title="[#points]" action="changeQuestionPoints">
                    	[@point]
                    </a>
                </td>
                <td valign="top">
                    <b>[#q_short]: <text class="holder_question-[@question_id]">[@question]</text></b><br>
                    <div style="background-color:#cdcdcd; padding:2px; margin-top:2px" class="model_answer">
                        <em><b>[#a_short]: </b><text class="holder_question_answer-[@question_id]">[@answer]</text></em>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</li>
