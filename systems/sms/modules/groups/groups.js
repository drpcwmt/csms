// JavaScript Document
// main Layout
function openGroups(){
	var module ={};
	module.name = 'groups';
	module.data = "";
	module.title = getLang('groups');
	module.div = '#resource_main_div';
	module.callback = function(){
		initiateJquery();
		$('#group_list li:first').addClass('ui-state-active').click();
	}
	loadModule(module)
	
}

function openEtabsGroup($btn){
	var groupId = $btn.attr('group_id');
	var module ={};
	module.name= 'groups';
	module.title = getLang('groups');
	module.data= 'group_id='+groupId;
	module.type= 'GET';
	module.div = '#group_content';
	module.callback = function(){
		if($('#group_content').find('input[name="editable"]').length>0 && $('#group_content').find('input[name="editable"]').val() == '1') {
			setEmployerAutocomplete($('#group_content').find('.sug_emp'), '');
		}
	}
	loadModule(module);
}

function openGroup($but){
	var groupId = $but.attr('groupid');
	var module ={};
	module.name= 'groups';
	module.title = getLang('groups');
	module.data= 'group_id='+groupId;
	module.type= 'GET';
	module.div = 'MS_dialog_groups-'+groupId;
	var dialogOpt = {
		width:900,
		height:600,
		title:getLang('groups'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			initGroupDialog('#MS_dialog_groups-'+groupId)
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function initGroupDialog(dialog){
	$dialog=$(dialog);
	if($dialog.find('input[name="editable"]').length>0 && $dialog.find('input[name="editable"]').val() == '1') {
		setEmployerAutocomplete($dialog.find('.sug_emp'), '');
		$dialog.dialog( "option", "title", getLang('group')+': '+$dialog.find('input[name="name"]').val() );
		var buttons = [{
			text: getLang('save'), 
			click: function() { 
				if(validateForm(dialog+' form')){
					var submitSave = {
						name: 'groups',
						param: 'save',
						post: $(dialog+' form').serialize(),
						callback: function(answer){
							var $ul = $('#group_list ul.listMenuUl');
							$ul.find('li[group_id="'+answer.id+'"]').click();
							$dialog.dialog('close');

						}
					}
					getModuleJson(submitSave);
						
				}
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}];
		$dialog.dialog({buttons: buttons});
	} else {
		$dialog.find('input, textarea').attr('disabled', 'disabled');
	}
}


function newGroup($btn){
	var parent = $btn.attr('parent');
	var parentId = $btn.attr('parent_id');
	var module ={};
	module.name = 'groups';
	module.data = 'new&parent='+parent+'&parent_id='+parentId;
	module.title = getLang('group');
	module.div = 'MS_dialog-new_group';
	var dialogOpt = {
		width:480,
		height:280,
		title:getLang('groups'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if(validateForm('#MS_dialog-new_group form')){
					var submitSave = {
						name: 'groups',
						param: 'save',
						post: $('#MS_dialog-new_group form').serialize(),
						callback: function(answer){
							if($btn.attr('parent') == 'etab'){
								var $ul = $('#group_list ul.listMenuUl');
								$ul.append('<li action="openEtabsGroup" class="hoverable clickable ui-stat-default ui-corner-all"  group_id="'+answer.id+'"><text class="holder-groups-'+answer.id+'">'+answer.title+'</text></li>');
							
								initiateJquery();
								$ul.find('li[group_id="'+answer.id+'"]').click();
							} else {
								// reload list
								var module ={};
								module.name= 'groups';
								module.title = getLang('group');
								module.data= 'list&reload&parent='+parent+'&parent_id='+parentId;
								module.div = '#group_list_div';
								loadModule(module);

							}
							$('#MS_dialog-new_group').dialog('close');
						}
					}
					getModuleJson(submitSave);
						
				}
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

function addGroupStd(but){
	var $dialog = $(but).parents('.ui-dialog');
	var filter = $dialog.find('input[name="parent"]').val() + '_id='+ $dialog.find('input[name="parent_id"]').val()
	var module = {};
	module.name = "students";
	module.data = 'stdfp&'+filter;
	module.div = "MS_dialog-student";
	module.title= getLang('students')
	var buttons = [{ 
		text: getLang('add'), 
		click: function() { 
			var oldStd = $dialog.find('#tot_con').val()
			if( oldStd != ''){
				var Recivers = oldStd.split(',');
			} else {
				var Recivers = new Array;
			}
			$('#MS_dialog-student input[name="std_id[]"]:checked').each(function(){
				if(Recivers.indexOf($(this).val()) == -1){
					Recivers.push($(this).val());
					
					var stdName = $(this).parent().next('td').html();
					$dialog.find(".student_list_tale tbody").append('<tr><td class="unprintable" style="border: 0px none;"><button onclick="openStudentInfos('+$(this).val()+')" style="width:24px; height:24px" class="ui-state-default hoverable"><span class="ui-icon ui-icon-person"></span></button></td><td class="unprintable" style="border: 0px none;"><button onclick="removeStdFromGroup('+$(this).val()+', this)" style="width:24px; height:24px" class="ui-state-default hoverable"><span class="ui-icon ui-icon-close"></span></button></td><td style="text-align: left; border: 0px none;">'+stdName+'</td></tr>');
				}
			})
			$dialog.find('#tot_con').val(Recivers.join(','));
			$('#MS_dialog-student').dialog('close');
		}
	},{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 840, 600, false, '');
}

function addGroupStdAuto(but){
	var $dialog = $(but).parents('.ui-dialog-content');
	var parent = $dialog.find('input[name="parent"]').val();
	var parentId = $dialog.find('input[name="parent_id"]').val();
	var serviceId = $dialog.find('select[name="service_id"]').val();
	service_options = new Array;
	$dialog.find('select[name="service_id"] option').each(function(){
		service_options.push('<option value="service_id='+$(this).val()+'">'+$(this).text()+'</option>');
	})
	var html = '<div style="padding:5px" class="ui-corner-all ui-state-highlight"><table><tr><td width="120" valign="middel" class="reverse_align"><label class="label ui-widget-header ui-corner-left">'+getLang('criteria')+'</label></td><td><select id="import_criteria" class="combobox">'+service_options.join('')+'<option value="sex=1">'+getLang("sex")+'='+getLang('male')+'</option><option value="sex=2">'+getLang("sex")+'='+getLang('female')+'</option><option value="religion=1">'+getLang("religion")+'='+getLang('muslim')+'</option><option value="religion=2">'+getLang("religion")+'='+getLang('christian')+'</option></select></td></tr></table></div>';
			
	var buttons = [{ 
		text: getLang('ok'), 
		click: function() { 
			MS_jsonRequest('groups&autoimport&parent='+parent+'&parent_id='+parentId, $('#import_criteria').val(), "$('#MS_dialog_autoImportStd').dialog('close');evalAutoImport(answer.stds, '"+$dialog.attr('id')+"')")	;
		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog("autoImportStd", getLang('import'), html, 300, 200, buttons, true)	
}

function evalAutoImport(stds, dialog){
	$dialog= $('#'+dialog);
	var std;
	var oldStd = $dialog.find('#tot_con').val()
	if( oldStd != ''){
		var Recivers = oldStd.split(',');
	} else {
		var Recivers = new Array;
	}
	
	var total = 0;
	for(std in stds){
		if(Recivers.indexOf(std) == -1){
			total++;
			Recivers.push(std);
			var $tbody = $dialog.find(".student_list_tale tbody")
			$tbody.append('<tr><td class="unprintable" style="border: 0px none;"><button onclick="openStudentInfos('+std+')" style="width:24px; height:24px" class="ui-state-default hoverable"><span class="ui-icon ui-icon-person"></span></button></td><td class="unprintable" style="border: 0px none;"><button onclick="removeStdFromGroup('+std+', this)" style="width:24px; height:24px" class="ui-state-default hoverable"><span class="ui-icon ui-icon-close"></span></button></td><td style="text-align: left; border: 0px none;">'+stds[std]+'</td></tr>');
		}
	}
	MS_alert('<h3>'+total+' '+getLang('students_found')+'</h3>');

	$dialog.find('#tot_con').val(Recivers.join(','));
}

function removeStdFromGroup(stdId, but){
	var $dialog = $(but).parents('.ui-dialog-content');
	var oldStd = $dialog.find('#tot_con').val()
	var Recivers = oldStd.split(',');
	var newRecivers = new Array;
	for(x=0;x<Recivers.length; x++){
		if(Recivers[x] != stdId){
			newRecivers.push(Recivers[x]);
		}
	}
	$dialog.find('#tot_con').val(newRecivers.join(','));
	$(but).parents('tr').fadeOut();
}

function deleteGroup($but){
	var buttons = [{ 
		text: getLang('yes'), 
		click: function() { 
			var module = {
				name: 'groups',
				post: 'id='+$but.attr('groupid'),
				param: 'delete',
				callback: function(){
					$('#MS_dialog_cfm_del_group').dialog('close');
					if($but.hasClass('circle_button')){
						$but.parents('tr').eq(0).fadeOut().remove();
					} else {
						openGroups();
					}
				}
			}
			getModuleJson(module);
		}
	}, { 
		text: getLang('no'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	var html = '<div class="ui-corner-all ui-state-highlight" style="padding:5px">'+getLang('cfm_del_group')+'</div>';
	createHtmlDialog("cfm_del_group", getLang('delete'), html, 300, 200, buttons, true)	
	
}

function saveGroup($btn){
	var $form = $btn.parents('form');
	if(validateForm($form)){
		var submitSave = {
			name: 'groups',
			param: 'save',
			post: $form.serialize(),
			
		}
		getModuleJson(submitSave);			
	}
}