<form class="holder_question scope">
	<input type="hidden" name="id" value="[@id]" />
    <input type="hidden" name="type" value="[@type]" />
    <input type="hidden" name="service_id" value="[@service_id]" />
    <input type="hidden" name="std_id" value="[@std_id]" />
    <div> 
        <span class="rev_float">[@points] [#points]</span>
        <b>[#q_short]: <text class="holder_question-[@question_id]">[@question]</text></b><br>
        
        <div style="background-color:#cdcdcd; padding:2px; margin-top:2px" class="model_answer">
            <em><b>[#a_short]: </b></em><textarea name="answer" [@size]>[@answer]</textarea>
        </div>
   </div>
</form>    