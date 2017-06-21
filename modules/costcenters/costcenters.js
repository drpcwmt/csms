// Cost center

function openCC($but){
	var ccId = $but.attr('code');
	var module ={};
	module.name= 'costcenters';
	module.title = getLang('cost_center');
	module.data= 'opencc='+ccId;
	module.type= 'GET';
	module.div = 'costcenter-'+ccId;

	var dialogOpt = {
		width:850,
		height:600,
		div:'cc-'+ccId+'_dialog',
		maxim: true,
		minim: true,
		title:getLang('cost_centers'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveCC($('#costcenter-'+ccId+' form'));
				$('#cc-'+ccId+'_dialog').dialog('close');
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

function newCC($but){
	var module ={};
	module.name= 'costcenters';
	module.title = getLang('cost_center');
	module.data= 'newcc';
	module.type= 'GET';
	module.div = 'new_costcenter';

	var dialogOpt = {
		width:600,
		height:500,
		div:'newCC_dialog',
		maxim: true,
		title:getLang('cost_centers'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveCC($('#new_costcenter form'));
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

function saveCC($form){
	if(validateForm($form)){
		var saveCC = {
			name: 'costcenters',
			param: 'savecc',
			post: $form.serialize(),
			title: getLang('cost_centers'),
			async: false,
			callBack: function(answer) {
				var $id = $form.find('input[name="id"]');
				$id.val(answer.id);
			}
		}
		getModuleJson(saveCC);
		return true
	} else {
		return false;
	}
}


function openCCgroup($but){
	var groupId = $but.attr('code');
	var module ={};
	module.name= 'costcenters';
	module.title = getLang('cost_center');
	module.data= 'opengroup='+groupId;
	module.type= 'GET';
	module.div = 'costcenter-group-'+groupId;

	var dialogOpt = {
		width:850,
		height:600,
		div:'cc-group-'+groupId+'_dialog',
		maxim: true,
		minim: true,
		title:getLang('cost_centers'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var $form = $('#costcenter-group-'+groupId+' form');
				if(validateForm($form)){
					var saveGroup = {
						name: 'costcenters',
						param: 'savegroup',
						post: $form.serialize(),
						title: getLang('cost_centers'),
						async: false,
						callBack: function(answer) {
							var $id = $form.find('input[name="id"]');
							$id.val(answer.id);
						}
					}
					getModuleJson(saveGroup);
					return true
				} else {
					return false;
				}
				$('#cc-group-'+groupId+'_dialog').dialog('close');
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

function newCCgroup($but){
	var module ={};
	module.name= 'costcenters';
	module.title = getLang('cost_center');
	module.data= 'newgroup';
	module.type= 'GET';
	module.div = 'new_costcenter_group';

	var dialogOpt = {
		width:600,
		height:500,
		div:'newCC_dialog',
		maxim: true,
		title:getLang('cost_centers'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var $form = $('#new_costcenter_group form');
				if(validateForm($form)){
					var saveGroup = {
						name: 'costcenters',
						param: 'savegroup',
						post: $form.serialize(),
						title: getLang('cost_centers'),
						async: false,
						callBack: function(answer) {
							var $id = $form.find('input[name="id"]');
							$id.val(answer.id);
						}
					}
					getModuleJson(saveGroup);
					return true
				} else {
					return false;
				}
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

