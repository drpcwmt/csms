// JavaScript Document

function openAbsents(){
	var module = {
		name: 'absents',
		data: '',
		title: getLang('absents'),
		div: '#home_content'
	}
	loadModule(module);
}

function openAbsByJob($btn){
	var jobId = $btn.attr('job_id') ? $btn.attr('job_id') : $('#abs_job_id').val();
	var module = {
		name:'absents',
		data: 'absbyjob='+jobId+'&'+$('#abs_add_form').serialize(),
		title:getLang('absents'),
		div: '#abs_daily_tbody',
		callback: function(){
			$('#abs_job_id').val(jobId);
		}
	}
	loadModule(module);
}

function addAbsents($btn){
	var jobId = $('#abs_job_id').val();
	var module ={};
	module.name= 'employers';
	module.title = getLang('absents');
	module.data= 'browse&action=select&job_id='+jobId;
	module.type= 'GET';
	module.cache= false;
	module.div = 'browseJob';
	var dialogOpt = {
		width:600,
		height: 500,
		modal:false,
		maxim:true,
		minim:true,
		cache:false,
		modal:false,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var module = {
					name: 'absents',
					param: 'insert',
					post: $('#abs_add_form').serialize()+'&'+$('#browseJob form').serialize(),
					callback: function(){
						var $vBut = $('<button>').attr('job_id', jobId);
						openAbsByJob($vBut)
					}
				}
				getModuleJson(module);
				 
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function addAbsComments($btn){
	var $tr = $btn.parents('tr').eq(0);
	var absId = $tr.find('input[name="abs_id"]').val();
	var comment = $btn.prev('p').text();
	var html= '<form><input type="hidden" name="id" value="'+absId+'" /><textarea name="comments" row="7">'+(comment && comment!='' ? comment : '')+'</textarea></from>';
	var dialogOpt = {
		width:470,
		height: 170,
		modal:false,
		maxim:true,
		minim:true,
		cache:false,
		modal:false,
		title:getLang('notes'),
		div:'absent_note_dialog',
		buttons: [{ 
			text: getLang('ok'), 
			click: function() { 
				var module = {
					name: 'absents',
					param: 'update',
					post: $('#absent_note_dialog form').serialize(),
					callback: function(){
						$btn.prev('p').html($('#absent_note_dialog textarea').val());
						$('#absent_note_dialog').dialog('close');
					}
				}
				getModuleJson(module);
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
}

function editAbs($inp){
	var $tr = $inp.parents('tr').eq(0);
	var absId = $tr.find('input[name="abs_id"]').val();
	var val = $inp.attr('type') == 'checkbox' ? ($inp.is(':checked') ? 1 : 0) : $inp.val();
	var module = {
		name: 'absents',
		param: 'update',
		post: 'id='+absId+'&'+$inp.attr('name')+'='+val,
		callback: function(answer){
			if(answer.value || answer.value=='0'){
				$tr.find('input[name="value"]').val(answer.value);
			}
		}
	}
	getModuleJson(module);
}

function deleteAbs($btn){
	var absId = $btn.attr('abs_id');
	var module = {
		name: 'absents',
		param: 'delete',
		post: 'id='+absId,
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();	
		}
	}
	getModuleJson(module);
}

function updateAbsentReport($select){
	var $form = $select.parents('form');
	var module= {
		name:'absents',
		data:'report&'+$form.serialize(),
		div: '#absent_list_tbody',
		title:getLang('absents')
	}
	loadModule(module);
}

function getAbsentList($btn){
	var empId = $btn.attr('emp_id');
	var module ={};
	module.name= 'absents';
	module.title = getLang('absents');
	module.data= 'getlist&emp_id='+empId;
	module.type= 'GET';
	module.cache= false;
	module.div = 'absentList-'+empId;
	var dialogOpt = {
		width:400,
		height: 600,
		modal:false,
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}