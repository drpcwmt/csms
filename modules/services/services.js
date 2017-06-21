function openService(button){
	if($(button).attr('serviceid') && $(button).attr('serviceid') != '') {
		var serviceId = $(button).attr('serviceid');
		var module ={};
		module.name= 'services';
		module.title = getLang('materials');
		module.data= 'details&service_id='+serviceId;
		module.type= 'GET';
		module.div = 'MS_dialog_service-'+serviceId;
		var dialogOpt = {
			width:1000,
			height:600,
			title:getLang('materials'),
			maxim:true,
			minim:true,
			buttons:  [{ 
				text: getLang('close'), 
				click: function() { 
					$(this).dialog('close');
				}
			}],
			callback: function (){
				var $title = $('#MS_dialog_service-'+serviceId).find('h3.title');
				$('#MS_dialog_service-'+serviceId).dialog('option', 'title', $title.html());
			}
		}
		openAjaxDialog(module, dialogOpt)
	} else {
		var $td = $(button).parent();
		var $tr = $td.parent();
		var lvl = $tr.attr('lvl');
		var mat_id = $('#mat_id').val();
		newServiceCfm(levelId, mat_id);
	}
}

function deleteService($but){
	var con = $but.attr('con'), conId = $but.attr('conid'), serviceId=$but.attr('serviceid');
	MS_jsonRequest('services&delete&id='+serviceId+'&con='+con+'&con_id='+conId, '', function(){
		$("#service-"+serviceId).fadeOut().remove()
	});	
}

function addService($but){
	var con = $but.attr('con'), conId = $but.attr('conid');
	var module ={};
	module.name = 'services';
	module.data = 'list&add_form&con='+con+'&con_id='+conId;
	module.title = getLang('add_service');
	module.div = 'MS_dialog-services';
	module.callback = function(){
		colectServiceChkox();
	}
	var dialogOpt = {
		width:500,
		height: 400,
		maxim:false,
		minim:false,
		modal:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				MS_jsonRequest('services&list&add_service&con='+con+'&con_id='+conId, $('#services-form').serialize(),function(){
					reloadServices(con, conId); 
					$("#MS_dialog-services").dialog("close");
				});
			}
		}, {
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function setlvlByMat($inp){
	var mat = $inp.val();
	var con = $inp.attr('con'), conId = $inp.attr('conid');
	if(con != 'level'){
		var module = {};
		module.name = 'services';
		module.data = 'list&lvlbymat='+mat+'&con='+con+'&con_id='+conId;
		module.div = '#MS_dialog-services #lvlbymat_div';
		loadModule(module)
	} else {
		return false;
	}
}

function setMatByLvl($inp){
	var levelId = $inp.val();
	var con = $inp.attr('con'), conId = $inp.attr('conid');
	if(con != 'level'){
		var module = {};
		module.name = 'services';
		module.data = 'list&matbylvl='+levelId+'&con='+con+'&con_id='+conId;
		module.div = '#MS_dialog-services #lvlbymat_div';
		loadModule(module)
	} else {
		return false;
	}
}

function colectServiceChkox(){
	var levels = new Array;
	var curItemsStr = '';
	if($('#colectChkox_value:checked').length && $('#colectChkox_value').val() != ''){
		var curItemsStr = $('#colectChkox_value').val();
	}
	var curItems = curItemsStr.split(',');
	for(x=0;x<curItems.length; x++){
		if(levels.indexOf(curItems[x]) == -1){
			levels.push(curItems[x]);
		}
	}
	
	$('#MS_dialog-services input[type="checkbox"]').click(function(){
		if($(this).attr('checked') =='checked'){
			levels.push($(this).val());
		} else {
			var index = levels.indexOf($(this).val());
			levels.splice(index, 1);
		}
		
		$('#MS_dialog-services #colectChkox_value').val(levels.join(','));
	});
}

function reloadServices(con, conId){
	services = {};
	services.name= 'services';
	services.title= getLang('materials');
	services.data = 'list&reload&con='+con+'&con_id='+conId;//+(exam != '' ?'&exam='+exam : '');
	services.div = $('#service_list-'+con+'-'+conId).parent('div.ui-tabs-panel');
	loadModule(services)
}

function reloadIgServices($select){
	var con = $select.attr('con');
	var conId = $select.attr('con_id'); 
	var exam = $select.val();
	services = {};
	services.name= 'services';
	services.title= getLang('materials');
	services.data = 'list&reload&con='+con+'&con_id='+conId+'&exam='+exam;
	services.div = $('#service_list-'+con+'-'+conId).parent('div.ui-tabs-panel');
	loadModule(services)
}


function iniMatServicesList(){
	$('#mat_services_div input').change(function(){
		var $this = $(this);
		var $td = $this.parent();
		var $tr = $td.parent();
		if($tr.find('button').attr('val') != '') {
			var serviceId = $tr.find('button').attr('val');
			var field = $this.attr('name');
			var value;
			if($this.attr('type') == 'checkbox'){
				if($this.attr('checked') == 'checked'){
					value = 1;
				} else {
					value = 0;
				}
			} else {
				value = $this.val();
			}
			MS_jsonRequest('services&update', 'service_id='+serviceId+'&field='+field+'&value='+value, '')
		} else {
			var levelId = $tr.attr('val');
			var mat_id = $('#mat_id').val();
			newServiceCfm(levelId, mat_id);
		}
	});
}

function newServiceCfm(levelId, mat_id){
	var buttons = [{ 
		text: getLang('ok'), 
		click: function() { 
			MS_jsonRequest('services&new', 'level_id='+levelId+'&mat_id='+mat_id, 'evalNewService(ans, '+levelId+');');
		}
	},
	{
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('confirm', getLang('confirm'), getLang('cfm-create-service'), 300, 120, buttons, true)
}

function evalNewService(answer, levelId){
	var serviceId = answer.id;
	$("#MS_dialog_confirm").dialog("close");
	var $tr = $('#tr-'+levelId);
	var $but = $tr.find('button')
	$but.find('span').removeClass('ui-icon-plus').addClass('ui-icon-extlink')
	$tr.find('button').attr('val', serviceId);
	if(answer.schedule == '1'){$tr.find('input[name="schedule"]').attr('checked', 'checked');}
	if(answer.mark == '1'){$tr.find('input[name="mark"]').attr('checked', 'checked');}
	if(answer.optional == '1'){$tr.find('input[name="optional"]').attr('checked', 'checked');}
	if(answer.bonus == '1'){$tr.find('input[name="bonus"]').attr('checked', 'checked');}
}



function setServiceOption(option, matId, checkbut){
	var onOff = $(checkbut).attr('checked') == 'checked' ? 1 : 0;
	MS_jsonRequest('services&setoption', 'matid='+matId+'&option='+option+'&value='+onOff, 'reloadMaterialService()');	
}

function createMakeUp(serviceId, serviceName){
	html = '<form id=Makeup_exam_form" class="ui-corner-all ui-state-highlight" style="padding:5px" ><table width="100%" cellspacing="0" border="0"><tbody><tr><td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">'+getLang('material')+'</label></td><td><input type="hidden" value="'+serviceId+'" name="service_id"><input type="text" value="'+serviceName+'"></td></tr><tr><td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">'+getLang('date')+'</label></td><td><input type="text" name="exam_date" class="datepicker mask-date" /></td></tr><tr><td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">'+getLang('max')+'</label></td><td><input type="text" value="" name="max"></td></tr><tr><td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">'+getLang('min')+'</label></td><td><input type="text" value="" name="min"></td></tr></tbody></table></form>';
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 		}
	}, {
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];	
	createHtmlDialog('Makeupexams', getLang('makeup'), html, 400, 250, buttons, true);
	initiateJquery();
}

function saveServiceSettings($but){
	var serviceId = $but.attr('serviceid');
	var $form = $('#services_settings_div-'+serviceId+' form');
	var submitSave = {
		name: 'services',
		param: '&update',
		post: $form.serialize(),
	}
	getModuleJson(submitSave);
}



function updateSkillTerm($inp){
	var param;
	var termId = $inp.val();
	if($inp.is(':checked')){
		param = 'updateskillterm';
	} else {
		param = 'deleteskillterm';	
	}
	var skillId = $inp.attr('skill_id');
	var submitSave = {
		name: 'services',
		param: 'skills&'+param,
		post: 'skill_id='+skillId+'&term_id='+termId,
		callback: function(answer){
			$('#new_skill_dialog').dialog('close');
		}
	}
	getModuleJson(submitSave);
}


/*function deleteSkill($btn){
	var skillId = $btn.attr('skill_id');
	var submitSave = {
		name: 'services',
		param: 'skills&delete',
		post: 'skill_id='+skillId,
		callback: function(answer){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(submitSave);
}
*/

function addIgService($btn){
	var submitSave = {
		name: 'services',
		param: 'ig&add',
		post: 'mat_id='+$btn.attr('mat_id')+'&lvl='+$btn.attr('lvl'),
		callback: function(answer){
			$btn.attr('action', 'openIgService');
			$btn.attr('service_id', answer.id);
			$btn.html('<span class="ui-icon ui-icon-extlink"></span>');
		}
	}
	getModuleJson(submitSave);	
}

function openIgService($btn){
	if($btn.attr('service_id')){
		var serviceId = $btn.attr('service_id');
	} else {
		var $tr = $btn.parents('tr').eq(0);
		var matId = $tr.attr('mat_id');
		var serviceId = $tr.attr('service_id');
		var lvl = $tr.attr('lvl');
		var type = $tr.attr('type');
	}
	var module ={};
	module.name= 'services';
	module.title = getLang('materials');
	module.data= 'details&service_id='+serviceId;
	module.type= 'GET';
	module.div = 'MS_dialog_service-'+serviceId;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('materials'),
		maxim:true,
		minim:true,
		buttons:  [{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function (){
			var $title = $('#MS_dialog_service-'+serviceId).find('h3.title');
			$('#MS_dialog_service-'+serviceId).dialog('option', 'title', $title.html());
		}
	}
	openAjaxDialog(module, dialogOpt)
}

function updateServiceFees($btn){
	var $form = $btn.parents('form');
	var submitSave = {
		name: 'services',
		param: 'ig&fees&save',
		post: $form.serialize()
	}
	getModuleJson(submitSave);
}

function loadIgService($inp){
	var $form = $inp.parents('form');
	var mat = $inp.val();
	var con = $form.attr('con'), conId = $inp.attr('conid');
	if(con != 'level'){
		var module = {};
		module.name = 'services';
		module.data = 'list&igservice&'+$form.serialize();
		module.div = '#MS_dialog-services';
		loadModule(module)
	} else {
		return false;
	}
}

function openServiceFees($but){
	var $tr = $but.parents('tr').eq(0);
	var matId = $tr.attr('mat_id');
	var serviceId = $tr.attr('service_id');
	var lvl = $tr.attr('lvl');

	var module ={};
	module.name = 'services';
	module.data = 'ig&fees&id='+serviceId+'&mat_id='+matId+'&lvl='+lvl,
	module.title = getLang('materials');
	module.div = 'MS_dialog-services_fees';
	var dialogOpt = {
		width:500,
		height: 250,
		maxim:false,
		minim:false,
		modal:true,
		buttons: [ {
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}
