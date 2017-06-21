function addNoteBook($btn){
	var con=$btn.attr('con');
	var conId = $btn.attr('con_id');
	var sysId = $btn.attr('sys_id');
	
	var module = {
		name: 'notebook',
		data: 'new&con='+con+'&con_id='+conId+'&sys_id='+sysId,
		title: getLang('notebook'),
		div: "new_notebook"
	}
	
	dialogOpt = {
		buttons: [{ 
			text: getLang('save'), 
			modal:true,
			click: function() { 
				if(validateForm('#new_notebook form')){
					var submitSave = {
						name: 'notebook',
						param: 'save&sys_id='+sysId,
						post: $('#new_notebook form').serialize(),
						callback: function(answer){
							var $fieldset = $btn.parents('.notebook_holder').eq(0);
							var $ul = $fieldset.find('ul');
							$ul.append('<li item_id="'+answer.id+'" class="hoverable ui-state-highlight ui-corner-all" style="border: 1px solid #CCC"><span>'+$('#new_notebook textarea[name="note"]').val()+'</span> <a class="rev_float ui-state-default hoverable mini_circle_button" module="notebook" action="deleteNoteBook" rel="'+answer.id+'" sys_id="'+sysId+'"><span class="ui-icon ui-icon-trash"></span></a></li>');
							iniButtonsRoles();
							$('#new_notebook').dialog('close');
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
		height:250,
		minim:false
	}
	openAjaxDialog(module, dialogOpt);	
}

function deleteNoteBook($btn){
	var id=$btn.attr('rel');
	var sysId = $btn.attr('sys_id');
	var deletePhone = {
		name: 'notebook',
		param: 'delete&sys_id='+sysId,
		post: 'id='+id,
		callback: function(answer){
			var $li = $btn.parents('li').eq(0);
			$li.fadeOut().remove();
		}
	}
	getModuleJson(deletePhone);
}