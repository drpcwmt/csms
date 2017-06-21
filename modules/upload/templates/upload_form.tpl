<form action="" method="post" enctype="multipart/form-data" name="upload_form" id="upload_form">
	<div class="ui-corner-all ui-state-highlight" style="padding:5px">[#max_size_upload]: [@max_size]</div>
	<div class="toolbox">
		<a onclick="$('#upload_field').click()">
        	<span class="ui-icon ui-icon-plus"></span>[#select]
        </a>
    </div>
    <label> 
		<input type="checkbox" value="1" id="autoconvert" checked="checked" />
		[#autoconvert]
	</label>
    
    <input type="file" name="files[]" [@multiple] class="hidden" id="upload_field" />
	<input type="hidden" value="[@destination]" id="destination" />
	[@extra]
    
	<table id="upload_table" class="result" width="100%" border="0" cellspacing="1" cellpadding="2">
    
    </table>
</form>