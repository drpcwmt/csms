function openTree(){
	var module ={};
	module.name = 'accounts';
	module.title = getLang('accounts');
	module.div = '#reportsMainDiv';
	module.data = 'tree';
	loadModule(module);
}

function RePrintRecete($btn){
	if($btn.attr('rel') == 'in'){
		var module_name = 'ingoing';
	} else {
		var module_name = 'outgoing';
	}
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('code')+': </label></td><td><input type="text" class="required" id="print_recete_id" /></td></tr></table></form>';
	dialogOpt = {
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				var module = {
					name: module_name,
					title: getLang('recete'),
					data: 'print_recete='+$('#print_recete_id').val(),
					div: 'reprint_div',
					callback : ''
				}
				dialog = {
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
					}],
					width:600,
					height:400,
					minim:false,
					div: 'dialog_recete',
					title: 'recete'
				}
				openAjaxDialog(module, dialog);
				$(this).dialog('close');
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		width:350,
		height:170,
		minim:false,
		maxim:false,
		div: 'dialog_recete',
		title: 'recete'
	}
	openHtmlDialog(html, dialogOpt)
}
