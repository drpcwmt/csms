function newConnection($but){
	var $form = $but.parents('form');
	var $ccId = $form.find('input[name="id"]');
	if($ccId.val() == ''){
		if(saveCC($form) == false){
			return false;
		}
		if($ccId.val() == ''){
			return false;
		}
	}
	
	var type = $but.attr('rel');
	var module ={};
	module.name= 'connections';
	module.title = getLang('new');
	module.data= 'newconexion';
	if($but.attr('rel')){
		module.data+= '&type='+type;
	}
	if($ccId){
		module.data+= '&ccid='+$ccId.val();
	}
	module.type= 'GET';
	module.div = 'new_connection';

	var dialogOpt = {
		width:500,
		height:300,
		div:'new_connection',
		title:getLang('cost_centers'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var saveCon = {
					name: 'connections',
					param: 'saveconection&ccid='+$ccId.val(),
					post: $('#new_connection form').serialize(),
					title: getLang('cost_centers')
				}
				getModuleJson(saveCon);
				$(this).dialog('close');
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function syncServer($but){
	var id = $but.attr('rel');
	var sync = {
		name: 'connections',
		param: 'sync',
		post: 'id='+id,
		title: getLang('cost_centers'),
		callback: function(answer){
			MS_alert(answer.results+' '+getLang('records_updated'));	
		}
	}
	getModuleJson(sync);
}