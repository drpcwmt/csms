<div class="tabs">
	<ul>
    	<li><a href="#skills_tabs_div-[@con]-[@con_id]">[#per_skill]</a></li>
        <li><a href="#skills_per_std-[@con]-[@con_id]">[#per_student]</a></li>
    </ul>
    <div id="skills_tabs_div-[@con]-[@con_id]">
        <form class="skill_form">
            <input type="hidden" name="con" value="[@con]"/>
            <input type="hidden" name="con_id" value="[@con_id]"/>
            [@toolbox]
            [@skill_table]
       </form>
    </div>
    <div id="skills_per_std-[@con]-[@con_id]">
    	<form class="skill_std_form">
            [@toolbox]
            <table class="tablesorter">
                <thead>
                    <tr>
                        <th class="{sorter:false}" width="20">&nbsp;</th>
                        <th>[#name]</th>
                        <th class="{sorter:false}" width="20">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    [@stds_trs]
                
                </tbody>
            </table>
    	</form>
    </div>
</div>