// Driver functions
function filterDriverList(val){
	$('#drivers_list li').each(function(){
		var li = $(this).html();
		if(val != ''){
			liLower = li.toLowerCase();
			if(li.indexOf(val) >= 0 || liLower.indexOf(val) >= 0){
				$(this).fadeIn();
			} else {
				$(this).fadeOut();
			}
		} else {
			$(this).fadeIn();
		}
	})
}


function importDrivers(){
	var module ={};
	module.name = 'resources';
	module.data = "templ=drivers&import&form";
	module.title = getLang('drivers');
	module.div = "MS_dialog_import";

	var dialogOpt = {
		width:400,
		height:250,
		modal: true,
		buttons: [{
			text: getLang('import'),
			click: function() {
				var imp = {
					name : 'resources',
					param: 'templ=drivers&import',
					post: $('#MS_dialog_import form').serialize(),
					title: getLang('import'),
					callback: function(){
						var $newBtn = $('<a>').attr('rel', 'drivers');
						openResource($newBtn);
						$('#MS_dialog_import').dialog("close")
					}
				}
				getModuleJson(imp);
			}
		}, {
			text: getLang('cancel'),
			click: function() {
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog( module, dialogOpt);
}

function openDriver($driver){
	var driverId = $driver.attr('driver_id');
	var hrmsId = $driver.attr('hrms_id')
	var module ={};
	module.name = 'resources';
	module.data = "templ=drivers&driver_id="+driverId+"&hrms_id="+hrmsId;
	module.title = getLang('drivers');
	module.div = '#driver_content';
	// module.callback = function(){ iniDriver(driverId) }
	loadModule(module)
}

function iniDriver(driverId){
	var module ={};
	module.name = 'routes';
	module.data = "w=driver_id-"+driverId;
	module.title = getLang('routes');
	module.div = '#driver_route_table';
	loadModuleToDiv(new Array(module), "")
}

function newDriver($but){
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="search_name" type="text" class="input_double" /><input id="search_id_inp" class="autocomplete_value" type="hidden" name="driver_id" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_new_driver',
		title:getLang('drivers'),
		buttons: [{
			text: getLang('add'),
			click: function() {
				var save  = {
					name: 'resources',
					param: 'templ=drivers&add_driver',
					post: $('#MS_dialog_new_driver form').serialize(),
					title: getLang('drivers'),
					callback: function(){
						$('#resource_list').append('<li action="openDriver" driver_id="' + $('#search_id_inp').val() + '" class="clickable hoverable ui-state-default ui-corner-all" >' + $('#search_name').val() +'</li>');
						initiateJquery();
						$('#MS_dialog_new_driver').dialog('close');
					}
				}
				getModuleJson(save);
			}
		},{
			text: getLang('cancel'),
			click: function() {
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
	initiateJquery();
	setEmployerAutocomplete('#MS_dialog_new_driver #search_name', 'job_code=7');
}

function saveDriver(driverId){
	var save  = {
		name: 'resources',
		param: 'templ=drivers&save',
		post: $('#licence_info form').serialize(),
		title: getLang('drivers')
	}
	getModuleJson(save);
}

function deleteDriver(){
	var html = $('#driver_name_title').clone();
	var driver_id = $('#driver_id').val();

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_delete_driver',
		title:getLang('drivers'),
		buttons: [{
			text: getLang('delete'),
			click: function() {
				var del  = {
					name: 'resources',
					param: 'templ=drivers&del_driver',
					post: 'id=' + driver_id,
					title: getLang('drivers'),
					callback: function(){
						$('#resource_list li[driver_id="' + driver_id  + '"]' ).remove();
						var $FirstLi = $('#resource_list').find('li').eq(0);
						openDriver($FirstLi);
						$('#resource_list li').first().addClass('ui-state-active');
						$('#MS_dialog_delete_driver').dialog('close');
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