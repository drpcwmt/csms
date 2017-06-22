function openOvertimes(){
	var module = {
		name: 'overtime',
		data: '',
		title: getLang('overtime'),
		div: '#home_content'
	}
	loadModule(module);
}

function openOvertimeByJob($btn){
	var jobId = $btn.attr('job_id') ? $btn.attr('job_id') : $('#overtime_job_id').val();
	var module = {
		name:'overtime',
		data: 'overtimebyjob='+jobId+'&'+$('#overtime_form').serialize(),
		title:getLang('overtime'),
		div: '#overtime_daily_tbody',
		callback: function(){
			$('#overtime_job_id').val(jobId);
		}
	}
	loadModule(module);
}

function addOvertime($btn){
	var jobId = $('#overtime_job_id').val();
	var module ={};
	module.name= 'overtime';
	module.title = getLang('overtime');
	module.data= 'new_form&job_id='+jobId;
	module.type= 'GET';
	module.cache= false;
	module.div = 'add_overtime_dialog';
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
					name: 'overtime',
					param: 'save',
					post: $('#overtime_add_form').serialize(),
					callback: function(){
						var $vBut = $('<button>').attr('job_id', jobId);
						openOvertimeByJob($vBut)
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
			setEmployerAutocomplete('#overtime_add_form input[name="emp_name"]', 'job_code='+jobId);
		}
	}
	openAjaxDialog(module, dialogOpt)
}

function deleteOvertime($btn){
	var overtimeId = $btn.attr('overtime_id');
	var module = {
		name: 'overtime',
		param: 'delete',
		post: 'id='+overtimeId,
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();	
		}
	}
	getModuleJson(module);
}
