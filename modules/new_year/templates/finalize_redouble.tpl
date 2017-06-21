<form id="new_year_wizard_form">
    <input type="hidden" name="finalize" value="0" />
    <h2>[#step] 1</h2>
    <h2>[#redoubling_report]</h2>
    <div class="toolbox">
        <a action="addRepeaters"><span class="ui-icon ui-icon-plus"></span>[#add]</a>
    </div>
    <div id="finalize_repeater_table">
        [@repeater_table]
    </div>
</form>   