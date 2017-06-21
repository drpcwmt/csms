function addPhoneBook($btn){
	var con=$btn.attr('con');
	var conId = $btn.attr('con_id');
	var sysId = $btn.attr('sys_id');
	var module = {
		name: 'phonebook',
		data: 'new&con='+con+'&con_id='+conId+'&sys_id='+sysId,
		title: getLang('phonebook'),
		div: "new_phonebook"
	}
	
	dialogOpt = {
		buttons: [{ 
			text: getLang('save'), 
			modal:true,
			click: function() { 
				if(validateForm('#new_phonebook form')){
					var submitSave = {
						name: 'phonebook',
						param: 'save&sys_id='+sysId,
						post: $('#new_phonebook form').serialize(),
						callback: function(answer){
							var $fieldset = $btn.parents('.phonebook_holder').eq(0);
							var $ul = $fieldset.find('ul');
							$ul.append('<li item_id="'+answer.id+'" class="hoverable ui-state-default ui-corner-all"><b>'+$('#new_phonebook input[name="tel"]').val()+'</b> <span class="mini">'+$('#new_phonebook input[name="title"]').val()+'</span><a class="rev_float ui-state-default hoverable mini_circle_button" module="phonebook" action="deletePhoneBook" rel="'+answer.id+'" sys_id="'+sysId+'"><span class="ui-icon ui-icon-trash"></span></a></li>');
							iniButtonsRoles();
							$('#new_phonebook').dialog('close');
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
		height:200,
		minim:false
	}
	openAjaxDialog(module, dialogOpt);	
}

function deletePhoneBook($btn){
	var id=$btn.attr('rel');
	var sysId = $btn.attr('sys_id');
	var deletePhone = {
		name: 'phonebook',
		param: 'delete&sys_id='+sysId,
		post: 'id='+id,
		callback: function(answer){
			var $li = $btn.parents('li').eq(0);
			$li.fadeOut().remove();
		}
	}
	getModuleJson(deletePhone);
}