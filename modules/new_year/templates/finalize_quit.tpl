<form id="new_year_wizard_form">
    <input type="hidden" name="finalize" value="1" />
    <h2>[#step] 2</h2>
    <h2>[#quit_list]</h2>
    <div class="toolbox">
        <a module="students" action="suspendStudent"><span class="ui-icon ui-icon-plus"></span>[#add]</a>
    </div>
    <div id="finalize_quit_table">
        [@quit_table]
    </div>
</form>   