function submitThisForm(button, callback){
	$("#loading_main_div").fadeIn();
	var $form = $(button).parents("form");
	MS_jsonRequest("system", $form.serialize(), '$("#loading_main_div").fadeOut();'+(callback ? callback :''));

}

	// backup
function doBackup($but){
	var $form = $but.parents("form");
	initiateBackupProgress();
	var module = {
		name : 'system',
		post : $form.serialize(),
		muted: true,
		title: getLang('backup'),
		async: true,
		callback : function(answer){
			if(answer.error == ''){
				$('#backup_form .progress').progressbar( "option", "value", 100);
				$('#backup_form').find('.success').fadeIn();
				$('#backup_form .fail , #backup_form .progress').fadeOut();
				$("#system_backupForm table.result tbody").prepend('<tr>'+
					'<td>'+
						'<button rel="'+answer.filepath+'" title="Download" class="circle_button hoverable ui-state-default" action="downloadBackup">'+
							'<span class="ui-icon ui-icon-arrowthick-1-s"></span>'+
						'</button>'+
					'</td>'+
					'<td>'+
						'<button rel="'+answer.filepath+'" title="Restore" class="circle_button hoverable ui-state-default" action="restoreBackup">'+
							'<span class="ui-icon ui-icon-arrowreturnthick-1-w"></span>'+
						'</button>'+
					'</td>'+
					'<td>'+
						'<button rel="'+answer.filepath+'" title="Delete" class="circle_button hoverable ui-state-default" action="deleteBackup">'+
							'<span class="ui-icon ui-icon-close"></span>'+
						'</button>'+
					'</td>'+
					'<td>'+answer.filename+'</td>'+
					'<td>'+answer.type+'</td>'+
					'<td>'+answer.time+'</td>'+
					'<td>'+answer.size+'</td>'+
				'</tr>');
				iniMsUi();
				iniButtonsRoles();
			} else {
				$('#backup_form .fail').fadeIn();
				$('#backup_form .success, #backup_form .progress').fadeOut();
				MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error')+'</h2>'+answer.error);
			}
		}
	}
	getModuleJson(module);
}

function initiateBackupProgress(){
	if($('#backup_form input[name="sql"]:checked').length > 0 ){
		var $tr = $('#backup_form input[name="sql"]:checked').parents('tr').eq(0);
		$tr.find('.success').hide();
		$tr.find('.fail').hide();
		$('#backup_mysql_progressbar').show();
		$('#backup_mysql_progressbar').progressbar();
		getbackupProgressValue("sql");
	}
	if($('#backup_form input[name="file"]:checked').length > 0){
		var $tr = $('#backup_form input[name="file"]:checked').parents('tr').eq(0);
		$tr.find('.success').hide();
		$tr.find('.fail').hide();
		$('#backup_files_progressbar').show();
		$('#backup_files_progressbar').progressbar();
		getbackupProgressValue("file");
	}
}	

function getbackupProgressValue(backup){
	var module = {
		name : 'system',
		param: 'action=backup&progress='+backup,
		muted: true,
		title: '',
		async :true,
		callback : function(answer){
			var $field = backup == 'sql' ? $('#backup_mysql_progressbar') : $('#backup_files_progressbar');
			var $tr = $field.parents('tr').eq(0);
			var $success = $tr.find('.success');
			var $fail = $tr.find('.fail');
			if(answer.error == ''){
				$field.progressbar( "option", "value", answer.progress);
				if(answer.progress < 0 && answer.progress < 100 ){
					getbackupProgressValue(backup);
				} 
			}
		}
	}
	getModuleJson(module);
}

function deleteBackup($but){
	var link = $but.attr('rel');
	var module = {
		name : 'system',
		param: 'action=backup&remove',
		post : 'file='+link,
		title: getLang('delete'),
		async: true,
		callback: function(answer){
			$but.parents('tr').eq(0).fadeOut().remove();
		}
	}
	getModuleJson(module);
}

function restoreBackup($but){
	var link = $but.attr('rel');
	showLoading('show', "Please wait database backup is being generated. It may take a few minute depending on database size, please do not refresh or close the browser window. ", true);
	showLoading('progress', 100);
	var module = {
		name : 'system',
		param: 'action=backup&restore',
		post : 'file='+link,
		title: getLang('restore'),
		async: true,
		modal:true,
		callback: function(answer){
			if(answer.error == ''){
				showLoading('hide');
				var dialogOpt = {
					width : 300,
					height:200,
					modal:true,
					div: 'restart_dialog',
					buttons:[{ 
						text: getLang('reload'), 
						click: function() { 
							$(this).dialog('close');
							window.location = 'index.php';
						}
					}]
				}
				openHtmlDialog('<h3>'+getLang('restoration_completed')+'</h3>', dialogOpt);
			}
		}
	}
	getModuleJson(module);
}

function downloadBackup($but){
	var link = $but.attr('rel');
	window.open(link,'download','');
}

// DIctionary
function submitDictionary($btn){
	var dictionary = {
		name: 'system',
		param: 'action=dictionary&lang='+$btn.attr('lang'),
		post: $('#dictionary-form').serialize()
	}
	getModuleJson(dictionary);
}

function deleteThis(but){
	var $tr = $(but).parents("tr");
	$tr.fadeOut().remove();
}

/*function addWord(){
	$("#lang_table tbody").prepend('<tr><td>&nbsp;</td><td><input name="index[]" type="text" /></td><td><input name="en[]"/></td></tr>');
}*/

function changeDictLang($select){
	module = {
		name: 'system',
		data:'action=dictionary&lang='+$select.val(),
		div: $('#lang_table tbody'),
		callback: function(){
			$('#dictionary-form	h3').html($('#lang_table tbody').find('tr').length+' '+getLang('words'));
		}
	}
	loadModule(module);
}

function importDictionary($but){
	var html = '<form><table width="100%"><tr><td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">Server IP:</label></td><td valign="top"><input type="text" name="server" value="127.0.0.1"></td></tr><tr><td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">Server IP:</label></td><td valign="top"><input type="text" name="database" value="csms"></td></tr><tr><td width="120"><label class="label ui-widget-header ui-corner-left reverse_align">Lang:</label></td><td valign="top"><select name="lang" class="combobox""><option value="ar">Ar</option><option value="en">En</option><option value="fr">Fr</option><option value="de">De</option></select></td></tr></table></form>';
	
	var dialogOpt = {
		width : 400,
		height:250,
		modal:true,
		div: 'import_dict',
		buttons:[{ 
			text: getLang('import'), 
			click: function() { 
				var module = {
					name: 'system',
					param:'action=dictionary&import_dict',
					post: $('#import_dict form').serialize(),
				}
				getModuleJson(module);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				var $select = $('#dictionary-form select[name="lang"]');
				changeDictLang($select);
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
}

function toogleEditorUl($btn){
	var $ul = $btn.next('ul');
	if($ul.is(':hidden')){
		$ul.slideDown();
	} else {
		$ul.slideUp();
	}
}

function openTbl($but){
	var file = $but.attr('rel');
	var module ={};
	module.name = 'system';
	module.data = 'open_tpl='+file; 
	module.title = 'Editor';
	module.div = '#editor_data_td';
	/*module.callback = function(){
		var body_document = $('#code_ifram').contents().get(0);
		body_document.open();
		body_document.write($('#file_data').val());
		body_document.close();
		body_document.designMode = 'on';
	}*/
	loadModule(module);	
}