function openBusMain($btn) {
	var busId = $btn.attr('bus_id')
	var module ={};
	module.name = 'bus';
	module.data = "";
	module.title = getLang('buss');
	module.div = '#resources_content';
	// module.callback = function(){ iniBus(driverId) }
	loadModule(module)
}
function openBus($inp) {
	var busId = $inp.attr('bus_id')
	var module ={};
	module.name = 'bus';
	module.data = "bus_id="+busId;
	module.title = getLang('buss');
	module.div = '#bus_content';
	// module.callback = function(){ iniBus(driverId) }
	loadModule(module)
}

/*function iniBus(busId){
	var module ={};
	module.name = 'routes';
	module.data = "w=bus_id-"+busId;
	module.title = getLang('routes');
	module.div = '#bus_route_table';
	loadModuleToDiv(new Array(module), "")
}*/

function deleteBus($delete_btn){
 	var html = $('#bus_name_title').clone();
	var bus_id = $delete_btn.attr('bus_id');

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_delete_bus',
		title:getLang('buss'),
		buttons: [{
			text: getLang('delete'),
			click: function() {
				var del  = {
					name: 'bus',
					param: 'del_bus',
					post: 'id=' + bus_id,
					title: getLang('buss'),
					callback: function(){
						$('#resource_list li[bus_id="' + bus_id  + '"]' ).remove();
						openBus($('#resource_list li').first());
						$('#resource_list li').first().addClass('ui-state-active');
						$('#MS_dialog_delete_bus').dialog('close');
					}
				}
				getModuleJson(del);
			}
		},{
			text: getLang('cancel'),
			click: function() {
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
}

function newBus($but) {

	var dialogOpt = {
		width:600,
		height:500,
		title:getLang('buss'),
		buttons: [{
			text: getLang('add'),
			click: function() {
				if(validateForm('#new_bus_dialog form')){
					var save  = {
						name: 'bus',
						param: 'save',
						post: $('#new_bus_dialog form').serialize(),
						title: getLang('buss'),
						callback: function(answer){
							$('#new_bus_dialog').dialog('close');
							$('#resource_list').removeClass('ui-state-active');
							var $li = $('<li action="openBus" bus_id="'+answer.id+'" class="clickable hoverable ui-state-default ui-corner-all ui-state-active">'+answer.code+'</li>');
							$('#resource_list').append($li);
							openBus($li);
						}
					}
					getModuleJson(save);
				}
			}
		},{
			text: getLang('cancel'),
			click: function() {
				$(this).dialog('close');
			}
		}]
	}
	var module ={};
	module.name = 'bus';
	module.data = "new_bus";
	module.title = getLang('buss');
	module.div = 'new_bus_dialog';

	openAjaxDialog(module, dialogOpt);
}

function saveBus(){
	var save  = {
		name: 'bus',
		param: 'save',
		post: $('#bus_form').serialize(),
		title: getLang('buss'),
	}
	getModuleJson(save);
}