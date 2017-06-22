<form class="holder_question scope">
	<input type="hidden" name="id" value="[@id]" />
    <input type="hidden" name="service_id" value="[@std_id]" />
    <input type="hidden" name="answer" value="[@answer]" />
    <div> 
        <span class="rev_float">[@points]&nbsp;[#point]</span>
        <b>[#q_short]: <text class="holder_question-[@question_id]">[@question]</text></b><br>
        
        <div style="background-color:#cdcdcd; padding:2px; margin-top:2px" class="model_answer">
            <em><b>[#a_short]: </b></em>
        	<ol>
            	[@bool_html]
            </ol>
        </div>
   </div>
</form>    