function openOutPermission(){
	var module = {
		name: 'permissionout',
		data: '',
		title: getLang('permissionout'),
		div: '#home_content'
	}
	loadModule(module);
}

function openPermissionoutByJob($btn){
	var jobId = $btn.attr('job_id') ? $btn.attr('job_id') : $('#permissionout_job_id').val();
	var module = {
		name:'permissionout',
		data: 'permissionoutbyjob='+jobId+'&'+$('#permissionout_form').serialize(),
		title:getLang('permissionout'),
		div: '#permissionout_daily_tbody',
		callback: function(){
			$('#permissionout_job_id').val(jobId);
		}
	}
	loadModule(module);
}

function addPermissionout($btn){
	var jobId = $('#permissionout_job_id').val();
	var module ={};
	module.name= 'permissionout';
	module.title = getLang('permissionout');
	module.data= 'new_form&job_id='+jobId;
	module.type= 'GET';
	module.cache= false;
	module.div = 'add_permissionout_dialog';
	var dialogOpt = {
		width:450,
		height: 330,
		modal:false,
		maxim:true,
		minim:true,
		cache:false,
		modal:false,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var module = {
					name: 'permissionout',
					param: 'save',
					post: $('#permissionout_add_form').serialize(),
					callback: function(){
						var $vBut = $('<button>').attr('job_id', jobId);
						openPermissionoutByJob($vBut)
					}
				}
				getModuleJson(module);
				 
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			setEmployerAutocomplete('#permissionout_add_form input[name="emp_name"]', 'job_code='+jobId);
		}
	}
	openAjaxDialog(module, dialogOpt)
}

function deletePermissionout($btn){
	var permissionoutId = $btn.attr('permissionout_id');
	var module = {
		name: 'permissionout',
		param: 'delete',
		post: 'id='+permissionoutId,
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();	
		}
	}
	getModuleJson(module);
}

function editPermis($inp){
	var $tr = $inp.parents('tr').eq(0);
	var permisId = $tr.find('input[name="permissionout_id"]').val();
	var val = $inp.attr('type') == 'checkbox' ? ($inp.is(':checked') ? 1 : 0) : $inp.val();
	var module = {
		name: 'permissionout',
		param: 'update',
		post: 'id='+permisId+'&'+$inp.attr('name')+'='+val,
		callback: function(answer){
			if(answer.value || answer.value=='0'){
				$tr.find('input[name="value"]').val(answer.value);
			}
		}
	}
	getModuleJson(module);
}
