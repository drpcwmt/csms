var uploadQueue = new Array;


function uploadFile(destination, filename, overwrite, callback, multi){	
	var module ={};
	module.name= 'upload';
	module.title = getLang('upload');
	module.data= 'uploadform';
	if(destination != ''){
		module.data += '&dest='+destination;
	} 
	if(filename != ''){
		module.data += '&filename='+filename;
	}
	if(overwrite != ''){
		module.data += '&overwrite';
	}
	if(multi != ''){
		module.data += '&multi='+multi;
	}
	
	module.div = 'MS_dialog_'+module.name;
	dialogOpt = {
		buttons: [{ 
			text: getLang('close'), 
			click: function() { 
				eval(callback);
				$(this).dialog('close');
			}
		}],
		width:400,
		height:300,
		minim:false,
		callback: function(){
			initUploadFrorm();
		}
	}
	openAjaxDialog(module, dialogOpt);	
}

function initUploadFrorm(){
	//uploadQueue = new Array;
	var $upload = $('#upload_field');
	$upload.change(function(){
		for (var i = 0; i < this.files.length; i++) {
			var thisFile = this.files[i];
			if(uploadQueue.indexOf(thisFile) == -1){
				uploadQueue.push(thisFile);
			}
		}
		submitUploads();
	});
}

function submitUploads(){
	var maxFiles = uploadQueue.length;
	for(var x=0; x<maxFiles; x++){
		fileName = uploadQueue[x].name;
		fileSize = uploadQueue[x].size;
		if(fileSize < configFile.maxUpload){
			var trHtml = '<tr id="tr-'+fileSize+'">'+
				'<td><span>'+fileName+'</span></td>'+
				'<td width="120" align="center"><progress id="prog-'+fileSize+'"></progress></td>'+
				'<td width="22"><button type="button" class="cancelUpload hand circle_button ui-state-default hoverable" title="'+getLang('close')+'"><span class="ui-icon ui-icon-close"></button></td>'+
			'</tr>';
			$('#upload_table').append(trHtml);
			initiateJquery();
			submitForm(uploadQueue[x]);
		} else {
			MS_alert('<h2><img src="assets/img/error.png"/> '+getLang('error')+'</h2>'+fileName+' '+getLang('filesize_exceed'));
			uploadQueue.splice(x, 1);
		}
	}
}

function submitForm(file){
	//uploadQueue.shift(file)
	var index = uploadQueue.indexOf(file);
	var formData = new FormData();

	formData.append('file', file);
	formData.append('dest', $('#destination').val());

	if($('#filename').length >0 && $('#filename').val() != ''){
		formData.append('filename', $('#filename').val());
	}
	if($('#overwrite').length >0 && $('#overwrite').val() == '1'){
		formData.append('overwrite', '1');
	}

	if($('#autoconvert:checked').length >0 ){
		formData.append('autoconvert', '1');
	}
	//$('#upload_table tr').remove();

	var $tr = $("#tr-"+file.size);
	upload = $.ajax({
        url: 'index.php?module=upload',
        type: 'POST',
        data: formData,
        xhr: function() {  // custom xhr
            myXhr = $.ajaxSettings.xhr();
			$tr.find('.cancelUpload').click(function(){
				myXhr.abort();	
				uploadQueue.splice(index, 1);
			});
            if(myXhr.upload){ // check if upload property exists
                myXhr.upload.addEventListener('progress',function(e){
					if(e.lengthComputable){
						$tr.find('progress').attr({value:e.loaded,max:e.total});
					}
				}, false);
            }
            return myXhr;
        },
        //Ajax events
        beforeSend: function(){
			$tr.find('.stat').addClass('miniloading').removeClass('ui-icon ui-icon-clock');
		},
        success: function(answer){
			uploadQueue.splice(index, 1);
			if(answer.indexOf('Error:') == 0){
				$tr.find('.cancelUpload').attr('title', getLang('error')).removeClass('ui-state-default hoverable hand').addClass('ui-widget-content');
				$tr.find('.cancelUpload span').removeClass('ui-icon-close').addClass('ui-icon-alert');
				MS_alert('<h3 class="title_wihte"><img src="assets/img/error.png" />'+answer+'</h3>');
			} else {
			//	$tr.find('.stat').removeClass('miniloading').addClass('ui-icon-check');
				$tr.find('.cancelUpload span').removeClass('ui-icon-close').addClass('ui-icon-check');
				$tr.find('.cancelUpload').attr('title', getLang('ok')).removeClass('ui-state-default hoverable hand').addClass('ui-widget-content');
			}	
			$tr.find('progress').fadeOut();
			$('#upload_field').val('');
		},
        error:function(){
			uploadQueue.splice(index, 1);
		//	$tr.find('.stat').removeClass('miniloading').addClass('ui-icon-alert');
			$tr.find('.cancelUpload').attr('title', getLang('error')).removeClass('ui-state-default hoverable hand').addClass('ui-widget-content');;
			$tr.addClass('ui-state-error');
			$tr.find('.cancelUpload span').removeClass('ui-icon-close').addClass('ui-icon-notice');
		},
        //Options to tell JQuery not to process data or worry about content-type
        cache: false,
        contentType: false,
        processData: false
    });
}

