function openBonus(){
	var module = {
		name: 'bonus',
		data: '',
		title: getLang('bonus'),
		div: '#home_content'
	}
	loadModule(module);
}

function openBonusByJob($btn){
	var jobId = $btn.attr('job_id') ? $btn.attr('job_id') : $('#bonus_job_id').val();
	var module = {
		name:'bonus',
		data: 'bonusbyjob='+jobId+'&'+$('#bonus_form').serialize(),
		title:getLang('bonus'),
		div: '#bonus_daily_tbody',
		callback: function(){
			$('#bonus_job_id').val(jobId);
		}
	}
	loadModule(module);
}

function addBonus($btn){
	var jobId = $('#bonus_job_id').val();
	var module ={};
	module.name= 'bonus';
	module.title = getLang('bonus');
	module.data= 'new_form';
	module.type= 'GET';
	module.cache= false;
	module.div = 'add_bonus_dialog';
	var dialogOpt = {
		width:600,
		height: 460,
		modal:false,
		maxim:true,
		minim:true,
		cache:false,
		modal:false,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var module = {
					name: 'bonus',
					param: 'save',
					post: $('#bonus_add_form').serialize(),
					callback: function(){
						var $vBut = $('<button>').attr('job_id', jobId);
						openBonusByJob($vBut)
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
			setEmployerAutocomplete('#bonus_add_form input[name="emp_name"]', 'job_code='+jobId);
			setDefAutocomplete($('#bonus_add_form input[name="comments"]'), configFile.database, 'bonus');
		}
	}
	openAjaxDialog(module, dialogOpt)
}

function deleteBonus($btn){
	var bonusId = $btn.attr('bonus_id');
	var module = {
		name: 'bonus',
		param: 'delete',
		post: 'id='+bonusId,
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();	
		}
	}
	getModuleJson(module);
}
