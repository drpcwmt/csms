<form class="holder_question scope">
	<input type="hidden" name="id" value="[@id]" />
    <input type="hidden" name="type" value="[@type]" />
    <input type="hidden" name="std_id" value="[@std_id]" />
    <input type="hidden" name="answer" value="[@answer]" />
    <div> 
        <span class="rev_float">[@points]&nbsp;[#point]</span>
        <b>[#q_short]: <text class="holder_question-[@question_id]">[@question]</text></b><br>
        
        <div style="background-color:#cdcdcd; padding:2px; margin-top:2px" class="model_answer">
            <em><b>[#a_short]: </b></em>
            <ul style="list-style:none; text-align:center">
                <li class="hand ui-corner-all ui-state-default hoverable clickable selectable [@value_true_class]"action="setAnswer" val="true" style="display:inline-block; width:150px; text-align:center;">
                    <h3>[#true] <img src="assets/img/success.png" width="24" height="24"  style="vertical-align:middle;"/></h3>
                </li>
                <li class="hand ui-corner-all ui-state-default hoverable clickable selectable [@value_false_class]" action="setAnswer" val="false" style="display:inline-block; width:150px; text-align:center">
                    <h3>[#false] <img src="assets/img/error.png" width="24" height="24" style="vertical-align:middle;" /></h3>
                </li>
            </ul>
        </div>
   </div>
</form>    