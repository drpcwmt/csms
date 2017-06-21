<h3>[#filter]</h3>
<div>
   	<input type="hidden" name="main_param" id="list_main_param" />
    <input type="hidden" name="params" id="list_params" />
    <div id="params_elemnt" class="hidden">
    	<select id="fields_select">[@filter_opts]</select>
    </div>
	<div id="param_content">
        <div class="toolbox">
            <a onclick="insertParam()"><span class="ui-icon ui-icon-plus"></span>[#add]</a>
            <a onclick="clearParam()"><span class="ui-icon ui-icon-trash"></span>[#clear]</a>
        </div>        
        <fieldset id="param_div">
        	[@status_opts]
        </fieldset>
    </div>
</div>
