<form>
	<input type="hidden" name="term_id" value="[@term_id]" />
    <input type="hidden" name="skill_id" value="[@id]" />
    <fieldset class="ui-state-highlight">
    	<legend>[@service_name]</legend>
        <h3>[@sub_name]</h3>
        <h4>[@sub_sub]</h4>
        <b><em>[@title]</em></b>
   </fieldset>     
    <table class="tableinput">
        <thead>
            <tr>
                <th width="20">[#ser]</th>
                <th>[#name]</th>
                <th width="200">[#results]</th>
            </tr>
         </thead>
         <tbody>
            [@std_table_tbody]
         </tbody>
     </table>
</form>