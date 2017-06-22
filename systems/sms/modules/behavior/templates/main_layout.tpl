<div class="tabs">
	<ul>
    	<li><a href="#add_behavior_tab">[#add]</a></li>
        <li><a href="#search_behavior_tab">[#search]</a></li>
    </ul>
    <div id="add_behavior_tab">
    	<div class="toolbox">
        	<a action="addBehavior"><span class="ui-icon ui-icon-plus"></span>[#add]</a>
            <a action="print_tab"><span class="ui-icon ui-icon-print"></span>[#print]</a>
            <a action="exportTable" rel="#add_behavior_tab"><span class="ui-icon ui-icon-disk"></span>[#export]</a>
        </div>
    	<form class="ui-state-highlight ui-corner-all" style="margin:5px">
        	<table cellspacing="0" border="0">
                <tr>
                    <td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#date]</label></td>
                    <td valign="middel" colspan="2"><input type="text" class="datepicker mask-date" name="day" value="[@cur_date]" /><button type="button" class="hoverable ui-corner-all ui-state-default" style="margin:0px 30px" action="reloadDailyBehavior"><span class="ui-icon ui-icon-search"></span>[#search]</button></td>
                </tr>
            </table>
        </form>
        <table class="tablesorter">
        	<thead>
            	<tr>
                	<th width="20">[#ser]</th>
                    <th>[#student]</th>
                    <th>[#class]</th>
                    <th>[#behavior]</th>
                    <th width="60">[#date]</th>
                    <th width="60">[#lesson_no]</th>
                    <th>[#sanction]</th>
                    <th>[#user]</th>
                    <th>[#notes]</th>
                    <th width="20">&nbsp;</th>
                </tr>
             </thead>
             <tbody>
             	[@daily_list]
             </tbody>
        </table>
    </div>
    <div id="search_behavior_tab">
    
    </div>
</div>