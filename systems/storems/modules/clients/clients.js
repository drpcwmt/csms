function openGroup($but){
	$('#groups_list li').removeClass('ui-state-active');
	$but.addClass('ui-state-active');
	var groupId = $but.attr('groupid');
	var module = {
		name: 'clients',
		data: 'group_id='+groupId,
		title: getLang('clients'),
		div: 'clients_div'
	}
	loadModule(module);
}

function openClient($but){
	var clientId = $but.attr('clientid');
	var productModule = {
		name: 'clients',
		title: getLang('clients'),
		data: 'client_id='+clientId,
		type: 'GET',
		div: 'client_'+clientId
	}
	
	var dialogOpt = {
		width:800,
		height:600,
		title:getLang('clients'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveClient(clientId);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			var $dialog = $('#client_'+clientId);
			$dialog.dialog("option", "title", $('#client_'+clientId+' input[name="name"]').val());
			$dialog.find('form .ena_auto').focus(function(){
		});
		}
	}
	
	openAjaxDialog(productModule, dialogOpt)
}

function saveClient(clientId){
	if(validateForm('#client_'+clientId+ ' form')){
		var module = {
			name: 'products',
			param: 'save',
			post: $('#client_'+clientId+ ' form').serialize(),
			async:false,
			callback: function(answer){
				$('#client_'+clientId).dialog('close');
				$(".label-client-"+clientId).html($('#client_'+clientId+' input[name="name"]').val())
			}
		}
		getModuleJson(module);
	} else {
		return false;
	}
}