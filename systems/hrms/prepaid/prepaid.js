// JavaScript Document

function openNewPrepaid(){
	var module ={};
	module.name= 'prepaid';
	module.title = getLang('prepaid');
	module.data= 'new';
	module.div = 'MS_dialog_prepaid_new';
	var dialogOpt = {
		width:600,
		height:400,
		title:getLang('prepaid'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var $form = $('#MS_dialog_prepaid_new form');
				var module = {
					name: 'prepaid',
					param: 'save',
					post: $form.serialize(),
				}
				getModuleJson(module);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		},{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
				$(this).dialog('close');
			}
		}], 
		callback: function(){
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}