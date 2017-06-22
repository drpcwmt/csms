/************ Matron ****************/
function filterMatronList(val){
	$('#matrons_list li').each(function(){
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


function importMatrons() {
	var module ={};
	module.name = 'resources';
	module.data = "templ=matrons&import&form";
	module.title = getLang('matrons');
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
					param: 'templ=matrons&import',
					post: $('#MS_dialog_import form').serialize(),
					title: getLang('import'),
					callback: function(){
						var $newBtn = $('<a>').attr('rel', 'matrons');
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

function openMatron($matron) {
	var matronId = $matron.attr('matron_id');
	var hrmsId = $matron.attr('hrms_id');
	var module ={};
	module.name = 'resources';
	module.data = "templ=matrons&matron_id="+matronId+"&hrms_id="+hrmsId;;
	module.title = getLang('matrons');
	module.div = '#matron_content';
	// module.callback = function(){ iniMatron(driverId) }
	loadModule(module)
}

function iniMatron(matronId){
	var module ={};
	module.name = 'routes';
	module.data = "w=matron_id-"+matronId;
	module.title = getLang('routes');
	module.div = '#matron_route_table';
	loadModuleToDiv(new Array(module), "")
}

function deleteMatron($delete_btn){
 	var html = $('#matron_name_title').clone();
	var matron_id = $delete_btn.attr('matron_id');

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_delete_matron',
		title:getLang('matrons'),
		buttons: [{
			text: getLang('delete'),
			click: function() {
				var del  = {
					name: 'resources',
					param: 'templ=matrons&del_matron',
					post: 'id=' + matron_id,
					title: getLang('matrons'),
					callback: function(){
						$('#resource_list li[matron_id="' + matron_id  + '"]' ).remove();
						openMatron($('#resource_list li').first());
						$('#resource_list li').first().addClass('ui-state-active');
						$('#MS_dialog_delete_matron').dialog('close');
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

function newMatron($but) {
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="search_name" type="text" class="input_double" /><input id="search_id_inp" class="autocomplete_value" type="hidden" name="matron_id" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_new_matron',
		title:getLang('matrons'),
		buttons: [{
			text: getLang('add'),
			click: function() {
				var save  = {
					name: 'resources',
					param: 'templ=matrons&add_matron',
					post: $('#MS_dialog_new_matron form').serialize(),
					title: getLang('matrons'),
					callback: function(){
						$('#resource_list').append('<li action="openMatron" matron_id="' + $('#search_id_inp').val() + '" class="clickable hoverable ui-state-default ui-corner-all" >' + $('#search_name').val() +'</li>');
						initiateJquery();
						$('#MS_dialog_new_matron').dialog('close');
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
	setEmployerAutocomplete('#MS_dialog_new_matron #search_name', 'job_code=2');
}