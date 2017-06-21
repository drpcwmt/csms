function addMailBook($btn){
	var con=$btn.attr('con');
	var conId = $btn.attr('con_id');
	var sysId = $btn.attr('sys_id');
	
	var module = {
		name: 'mailbook',
		data: 'new&con='+con+'&con_id='+conId+'&sys_id='+sysId,
		title: getLang('mailbook'),
		div: "new_mailbook"
	}
	
	dialogOpt = {
		buttons: [{ 
			text: getLang('save'), 
			modal:true,
			click: function() { 
				if(validateForm('#new_mailbook form')){
					var submitSave = {
						name: 'mailbook',
						param: 'save&sys_id='+sysId,
						post: $('#new_mailbook form').serialize(),
						callback: function(answer){
							var $fieldset = $btn.parents('.mailbook_holder').eq(0);
							var $ul = $fieldset.find('ul');
							$ul.append('<li item_id="'+answer.id+'" class="hoverable ui-state-default ui-corner-all"><b>'+$('#new_mailbook input[name="mail"]').val()+'</b> <a class="rev_float ui-state-default hoverable mini_circle_button" module="mailbook" action="deleteMailBook" sys_id="'+sysId+'" rel="'+answer.id+'"><span class="ui-icon ui-icon-trash"></span></a></li>');
							iniButtonsRoles();
							$('#new_mailbook').dialog('close');
						}
					}
					getModuleJson(submitSave);
				} else {return false;}
	
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		width:450,
		height:150,
		minim:false
	}
	openAjaxDialog(module, dialogOpt);	
}

function deleteMailBook($btn){
	var id=$btn.attr('rel');
	var sysId = $btn.attr('sys_id');
	var deletePhone = {
		name: 'mailbook',
		param: 'delete&sys_id='+sysId,
		post: 'id='+id,
		callback: function(answer){
			var $li = $btn.parents('li').eq(0);
			$li.fadeOut().remove();
		}
	}
	getModuleJson(deletePhone);
}