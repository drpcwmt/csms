// JavaScript Document

function openWarhouse($but){
	$('#wars_list li').removeClass('ui-state-active');
	$but.addClass('ui-state-active');
	var warId = $but.attr('warid');
	var module = {
		name: 'warhouses',
		data: 'war_id='+warId,
		title: getLang('warhouses'),
		div: 'warhouses_div',
		callback: function(){ setEmployerAutocomplete('#warhouses_div #emp_sug_div', '')}
	}
	loadModule(module);
}

function newWarhouse($but){
	$('#wars_list li').removeClass('ui-state-active');
	var module = {
		name: 'warhouses',
		data: 'newform',
		title: getLang('warhouses'),
		div: 'warhouses_new',
		callback: function(){ setEmployerAutocomplete('#warhouses_new #emp_sug_div', '')}
		
	}
	var dialogOpt = {
		width:600,
		height:300,
		title:getLang('warhouses'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if(saveWarhouse($('#warhouses_new form')) != false){
					$(this).dialog('close');
				}
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		
	}
	
	openAjaxDialog(module, dialogOpt)
}

function saveWarhouse($form){
	if(validateForm($form)){
		var module = {
			name: 'warhouses',
			param: 'save',
			post: $form.serialize(),
			async:false,
			callback: function(answer){
				$form.find('input[name="id"]').val(answer.id);
			}
		}
		getModuleJson(module);
	} else {
		return false;
	}
}

