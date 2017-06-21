function openMessage($but){
	// check if fired from message module or elsewhere
	var msgId = $but.attr('msgid');
	var module = {};
	module.name = "messages";
	module.data = 'read&msg_id='+msgId;
	module.title =  getLang('messages');
	module.cache = false;
	if($but.parents('#module_messages').length > 0){
		module.div = '#msg_content';
		$('#msg_content').fadeOut(function(){
			loadModule(module);
		})
		module.callback = function(){
			$('#msg_content').fadeIn()
		}
	} else {
		module.div = 'MS_dialog-message-'+msgId;
		module.callback = function(){
			$but.parent('li').fadeOut().remove();
		}
		dialogOpt = {
			buttons:[{ 
				text: getLang('print'), 
				click: function() { 
					print_pre('#MS_dialog-message-'+msgId+'	 #message-form');
				}
			},{ 
				text: getLang('close'), 
				click: function() { 
					$(this).dialog('close');
				}
			}],
			width:700,
			height:600,
			modal:true,
			title: getLang('message')
		}
		openAjaxDialog(module, dialogOpt);	
	}
}

function getCurIds(){
	var multi = $('input[name="select_mail"]:checked').length;
	var idStr;
	if(multi > 0){
		var ids = new Array();
		$('input[name="select_mail"]:checked').each(function(){
				ids.push( $(this).val());
		});
		var idStr = ids.join(',');
	} else if($('#cur_msg_id').val() && $('#cur_msg_id').val() != ''){
		var idStr = $('#cur_msg_id').val()
	} else {
		idStr = false;
	}
	return idStr;
}

function reloadView(){
	var type = $('#view_select').val();
	var module = {};
	module.name = 'messages';
	module.div = '#messages_tab';
	module.title = getLang(type);
	module.data = 'type=messages&';
	if(type == 'inbox'){
		module.data += 'inbox';
	} else if(type == 'sent'){
		module.data += 'sent';
	} else if(type == 'trash'){
		module.data += 'trash';
	}
	loadModuleToDiv(new Array(module), "");
}

function markUnread(){
	var ids = getCurIds();
	if(ids != false){
		MS_jsonRequest('messages&unseen='+ids, '', "reloadInbox()")	;
	}
}

function loadCompose(reply, Forward){
	var module = {};
	module.name = "messages";
	module.div = "MS_dialog-compose";
	module.title= getLang('compose')
	module.data = 'compose';
	var msgId = $('#cur_msg_id').val();
	if(reply =='reply'){
		module.data += '&reply='+msgId;
	}
	if(Forward =='forward'){
		module.data += '&forward='+msgId;
	}
	
	var dialogOpt = {
		width:840,
		height:600,
		title:getLang('compose'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('send'), 
			click: function() { 
				if($('#reciver_value').val() != ''){
					var mod = {
						name:'messages',
						param: 'send',
						post: $('#compose_form').serialize(),
						type:'POST',
						callback: function(){
							$('#MS_dialog-compose').dialog('close');
						}
					}
					getModuleJson(mod);
				} else {
					MS_alert('<img src="assets/img/error.png" /><h3>'+ getLang('must_seclect_recipient')+'</h3>')
				}
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)	
}

function deleteMsg(){
	var ids = getCurIds();
	if(ids != false){
		MS_jsonRequest('messages&delete='+ids, '', "reloadView()")	;
	}
}	

function restoreMsg(){
	var ids = getCurIds();
	if(ids != false){
		MS_jsonRequest('messages&restore='+ids, '', "reloadView()")	;
	}
}	

function deleteSysMsg(){
	var ids = getCurIds();
	if(ids != false){
		MS_jsonRequest('messages&delsysmsg='+ids, '', "reloadSysMsg()")	;
	}	
}

function reloadSysMsg(){
	var module = {};
	module.name = "messages";
	module.data = 'type=system';
	module.div = $("#sys_list").parents('.ui-tabs-panel');
	module.title= getLang('system')
	loadModuleToDiv(new Array(module), "");
}

function addReciver(type){
	if(type =='student'){
		var module = {};
		module.name = "students";
		module.data = 'stdfp';
		module.div = "MS_dialog-student";
		module.title= getLang('students')
		var buttons = [{ 
			text: getLang('add'), 
			click: function() { 
				if($('#reciver_value').val() != ''){
					var Recivers = $('#reciver_value').val().split(',');
				} else {
					var Recivers = new Array;
				}
				$('#MS_dialog-student input[name="std_id[]"]:checked').each(function(){
					if(Recivers.indexOf(type+'-'+$(this).val()) == -1){
						Recivers.push(type+'-'+$(this).val());
						var stdName = $(this).parent().next('td').html();
						$("#reciver_name").append('<span class="reciver ui-state-default hoverable">'+stdName+'<span class="close ui-icon ui-icon-close" onclick="removeRecipient(this, '+type+'-'+$(this).val()+')"/></span>');
					}
				})
				$('#reciver_value').val(Recivers.join(','));
				$('#MS_dialog-student').dialog('close');
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}];
		createAjaxDialog(module, buttons, false, 840, 600, false, '');
	} else {
		var html = '<table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="emp_sug_div" type="text" class="input_double" /><input id="search_id_inp" class="autocomplete_value" type="hidden" /></td></tr></table>';
		var buttons = [{ 
			text: getLang('ok'), 
			click: function() { 
				if($('#reciver_value').val() != ''){
					var Recivers = $('#reciver_value').val().split(',');
				} else {
					var Recivers = new Array;
				}
				$("#reciver_name").append('<span class="reciver ui-state-default hoverable">'+$("#emp_sug_div").val()+'<span class="close ui-icon ui-icon-close" onclick="removeRecipient(this, '+type+'-'+$(this).val()+')"/></span>');
				Recivers.push(type+'-'+$('#search_id_inp').val())
				$('#reciver_value').val(Recivers.join(','));
				$('#MS_dialog_profs').dialog('close');
			}
		}, {
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}];
		createHtmlDialog('profs', getLang(type), html, 460, 150, buttons, true);
		if(type == 'parent'){
			setParentAutocomplete('#MS_dialog_profs #emp_sug_div');
		} else {
			setEmployerAutocomplete('#MS_dialog_profs #emp_sug_div', 'status-1&group='+type);
		}
	}
}

function removeRecipient(but, recpId){
	var Recivers = $('#reciver_value').val().split(',');
	var newRecivers = new Array;
	for(x=0;x<Recivers.length; x++){
		if(Recivers[x] != recpId){
			newRecivers.push(Recivers[x]);
		}
	}
	$('#reciver_value').val(newRecivers.join(','));
	$(but).parent().fadeOut();
}

function loadMsgList(but, page, type){
	var $target = $(but).parents('.msg_list-messages').eq(0);
	var module = {};
	module.name = "messages";
	module.data = 'type='+type+'&list&page='+page;
	module.div = $target;
	module.title= getLang('messages');
	module.callback = function(){
	//	$target.find('tbody tr:first').find('td').eq(2).click();
	}
	loadModule(module);
	
		
}
