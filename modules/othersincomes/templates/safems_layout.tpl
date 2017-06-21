<h2>[@title]</h2>
<div class="tabs">
	<ul>
    	<li><a href="#others_incomes_tab">[#ingoing]</a></li>
        <li><a href="#others_settings_tab">[#settings]</a></li>
    </ul>
    <div id="others_incomes_tab">
        <h3>[@incomes_acc_title]</h3>
        <div class="toolbox">
        	<a action="addActivtyMemberPerStd" act_id="[@id]">[#by_student]<span class="ui-icon ui-icon-plus"></span></a>
            <a action="addActivtyMemberBrowse" act_id="[@id]">[#browse]<span class="ui-icon ui-icon-plusthick"></span></a>
	    	<a action="print_tab">[#print] <span class="ui-icon ui-icon-print"></span></a>
        </div>
    	<table class="tablesorter member_table">
        	<thead>
            	<tr>
                	<th rowspan="2" class="{sorter:false}" width="20">&nbsp;</th>
                	<th rowspan="2" class="{sorter:false}" width="20">&nbsp;</th>
                	<th rowspan="2" class="{sorter:false}" width="20">&nbsp;</th>
                    <th rowspan="2">[#student]</th>
                    <th rowspan="2">[#class]</th>
                    <th colspan="[@count_curs]">[#paid]</th>
				</tr>
                <tr>
                	[@cur_trs]
                </tr>
            </thead>
            <tbody>
            	[@members_trs]
            </tbody>
         </table>                    
                    
                    
    </div>
    <div id="others_settings_tab">
        [@settings_tab]
    </div>
</div>