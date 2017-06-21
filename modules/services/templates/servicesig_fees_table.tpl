<form>
	<input type="hidden" name="id" value="[@id]" />
    <div class="toolbox">
        <a action="updateServiceFees">[#save] <span class="ui-icon ui-icon-disk"></span></a>
    </div>
    <table class="tableinput">
        <thead>
            <tr>
                <th width="80" rowspan="2">[#type]</th>
                <th colspan="2">Nov.</th>
                <th colspan="2">Jan.</th>
                <th colspan="2">Jun</th>
            </tr>
            <tr>
                <th>[#fees]</th>
                <th>Reg</th>
                <th>[#fees]</th>
                <th>Reg</th>
                <th>[#fees]</th>
                <th>Reg</th>
           </tr>
        </thead>
        <tbody>
            [@trs]
        </tbody>
    </table>
    
    <fieldset style="width:50%">
    	<legend>[#remarking]</legend>
        <table class="tableinput">
            <thead>
                <tr>
                    <th>[#service]</th>
                    <th>[#paper]</th>
                    <th>[#fees]</th>
                </tr>
            </thead>
            <tbody>
                [@remarking_trs]
            </tbody>
        </table>
	</fieldset>        
</form>