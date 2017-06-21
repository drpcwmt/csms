<div class="tabs scope" con="[@con]" con_id="[@con_id]">
	<ul>
    	<li><a href="#mark_edit_div-[@con]-[@con_id]">[#exams]</a></li>
        <li><a href="#mark_times_div-[@con]-[@con_id]">[#settings]</a></li>
        <li><a href="#mark_addons_div-[@con]-[@con_id]">[#addons]</a></li>
    </ul>
    <div id="mark_edit_div-[@con]-[@con_id]">
        [@toolbox]
        <div class="exam_table">
            [@mark_table]
        </div>
    </div>
    <div id="mark_times_div-[@con]-[@con_id]">
    	<form>
            [@terms_table]
            [@settings]
        </form>
    </div>
     <div id="mark_addons_div-[@con]-[@con_id]">
    	[@addons_table]
    </div>
</div>
