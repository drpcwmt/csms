<div class="tabs">
	<ul>
		<li><a href="#route_detail_div">[#details]</a></li>
		<li><a href="#members_div">[#members]</a></li>
	</ul>
	<div id="route_detail_div">
		<div class="toolbox">
			<a action="saveRoute" route_id="[@id]"><span class="ui-icon ui-icon-disk"></span>[#save]</a>
			<a action="deleteRoute" route_id="[@id]"><span class="ui-icon ui-icon-trash"></span>[#delete]</a>
			<a action="addTarget" route_id="[@id]"><span class="ui-icon ui-icon-plus"></span>[#add_address]</a>
			<a class="print_but" rel="#route_detail_div"><span class="ui-icon ui-icon-print"></span>[#print]</a>
		</div>
        
		<div class="showforprint hidden"> 
			<h2>[#the_route]</h2>
		</div>
        
		[@route_data]
        
		<div class="tabs">	
            <ul>
                <li><a href="#m_parcour_tab">[#morning]</a></li>
                <li><a href="#e_parcour_tab">[#evening]</a></li>
            </ul>
            <div id="m_parcour_tab">
                [@parcour_table_m]
            </div>
            <div id="e_parcour_tab">
                [@parcour_table_e]
            </div>
        </div>
    </div>
	<div id="members_div">
		<div class="toolbox">
			<a action="addMember" con="std" route_id="[@id]"><span class="ui-icon ui-icon-plus"></span>[#add_student]</a>
			<a action="addMember" con="emp" route_id="[@id]"><span class="ui-icon ui-icon-plus"></span>[#add_employer]</a>
			<a class="print_but" rel="#members_div"><span class="ui-icon ui-icon-print"></span>[#print]</a>
		</div>
        <div class="hidden showforprint">
        	<h2>[#route]: [@id] - [@region]</h2>
        </div>
		<table class="tablesorter">
			<thead>
				<tr> 
					<th width="20" class="unprintable">&nbsp;</th>
                    <th width="20" class="unprintable">&nbsp;</th>
                    <th width="20" class="unprintable">&nbsp;</th>
					<th>[#name]</th>
                    <th>[#school]</th>
					<th width="120">[#type]</th>
				</tr>
			</thead>
			<tbody> 
            	[@members_trs]
            </tbody>
        </table>
	</div>
</div>
