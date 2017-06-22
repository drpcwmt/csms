// Resources
// Main resources functions
	// the call for resource page from resource menu
function openResource($item){
	var module ={};
	module.name = 'resources';
	module.data = "templ="+$item.attr('rel');
	module.title = getLang($item.attr('rel'));
	module.div = '#resource_main_div';
	module.callback = function(){
		initiateJquery();
		$('#resource_list li:first').addClass('ui-state-active').click();
	}
	loadModule(module)
}

	// resource list functions
function filterResourceList(val){
	$('#resource_list li').each(function(){
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

function resetSearchMenu($inp){
	if($inp instanceof jQuery === false){
		$inp = $($inp);
	}
	$inp.val(getLang('search'));
	filterResourceList('');
}
	
	// open resource from list 
function openResourceInfos($item){
	var itemId = $item.attr('itemid')
	var template= $item.attr('rel');
	var modules = new Array();
	var callback
	var module ={};
	module.name = 'resources';
	module.data = "templ="+template+'&itemid='+itemId;
	module.title = getLang(template);
	module.div = '#resource_content';
	modules.push(module);
	
	if(template== 'classes'){
		module.callback = function(){ setEmployerAutocomplete('#emp_sug_div', '')};
	} else if(template== 'materials'){
		module.callback = function(){/*iniMatServicesList()*/};
		services = {};
		services.name= 'services';
		services.title= getLang('materials');
		services.data = 'mat_services='+itemId;
		services.div = '#mat_services_div';
		modules.push(services);
	} else if(template== 'profs'){
		services = {};
		services.name= 'services';
		services.title= getLang('materials');
		services.data = 'list&con=prof&con_id='+itemId;
		services.div = '#services_list_div';
		modules.push(services);
	} else if(template== 'supervisors'){
		services = {};
		services.name= 'services';
		services.title= getLang('materials');
		services.data = 'list&con=supervisor&con_id='+itemId;
		services.div = '#services_list_div';
		modules.push(services);
	} else if(template== 'halls'){
		module.callback = function() {
			schedule = {};
			schedule.name= 'schedule';
			schedule.title= getLang('schedule');
			schedule.data = 'con=hall&con_id='+itemId;
			schedule.div = '#schedule_container';
			schedule.callback = function(){initTimeTable($('#schedule_container ul:first'))}
			loadModule(schedule);
		}
	} else if(template== 'tools'){
		module.callback = function() {
			schedule = {};
			schedule.name= 'schedule';
			schedule.title= getLang('schedule');
			schedule.data = 'con=tool&con_id='+itemId;
			schedule.div = '#schedule_container';
			schedule.callback = function(){initTimeTable($('#schedule_container ul:first'))}
			loadModule(schedule);
		}
	}
	loadMultiModules(modules, '')
}

function reloadResourceItem(){
	if($('#resource_list li.ui-state-active').length > 0){
		openResourceInfos($('#resource_list li.ui-state-active'));
	}
}

	// common save resource function
function saveResource($but){
	var $scope = $but.parents('.scope').eq(0);
	var newResource = $scope.find('form input[name="id"]').val() != '' ? false : true;
	resourceType = $but.attr('resourcetype');
	var submitSave = {
		name: 'resources',
		param: 'save&templ='+resourceType,
		post: $scope.find('form').serialize(),
		callback: function(answer){
			$('text.holder-'+resourceType+'-'+answer.id).html(answer.title);
		}
	}
	getModuleJson(submitSave);
}

/*********************************************************/
// New resource
function newResourceItem($but){
	var templ = $but.attr('templ');
	var module ={};
	module.name = 'resources';
	module.data = "templ="+templ+'&new';
	module.title = getLang('new');
	module.div = 'MS_dialog-resource';
	var dialogOpt = {
		width:460,
		height:300,
		title:getLang(templ),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				if(validateForm('#new_resource_form')){
					var submitSave = {
						name: 'resources',
						param: 'save&templ='+templ,
						post: $('#new_resource_form').serialize(),
						callback: function(answer){
							evalNewResource(answer, templ);
							$('#MS_dialog-resource').dialog('close');
						}
					}
					getModuleJson(submitSave);
				}
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			iniNewResourceEmpAuto();
			if(templ =='principals'){
				colectChkox();
			} 
		}
	}
		
	openAjaxDialog(module, dialogOpt)
}

function evalNewResource(answer, templ){
	var $ul = $('#resource_list ul.listMenuUl');
	$ul.append('<li action="openResourceInfos" class="hoverable clickable ui-stat-default ui-corner-all" rel="'+templ+'" itemid="'+answer.id+'"><text class="holder-'+templ+'-'+answer.id+'">'+answer.title+'</text></li>');

	initiateJquery();
	$ul.find('li[itemid="'+answer.id+'"]').click();
	$('#MS_dialog-resource').dialog('close');

	/*if($('#template').val() == 'materials'){ // material
		var  newName = window.direction == 'rtl' ? $('#new_resource_form input[name="name_rtl"]').val() : $('#new_resource_form input[name="name_ltr"]').val();
		var color = '#'+$('#new_resource_form input[name="color"]').val();
		$ul.append('<li  val="'+resourceId+'" class="hoverable clickable ui-stat-default ui-corner-all" onclick="openResourceInfos( '+resourceId+')">'+
			'<span class="color" style="background-color:'+color+'"></span>'+
			'<label>'+newName+'</label>'+
		'</li>');
	} else if($('#template').val()=='profs' || $('#template').val()=='principals' || $('#template').val()=='supervisors'){ // Profs,Principals,supervisors
		var  newName = $('#new_employer_name').val() ;
		$ul.append('<li val="'+resourceId+'" class="hoverable clickable ui-stat-default ui-corner-all" onclick="openResourceInfos( '+resourceId+')">'+newName+'</li>');
	} else if($('#template').val()=='levels' || $('#template').val()=='classes'){ // level, classes
		var  newName = window.direction == 'rtl' ? $('#new_resource_form input[name="name_rtl"]').val() : $('#new_resource_form input[name="name_ltr"]').val();
		$ul.append('<li val="'+resourceId+'" class="hoverable clickable ui-stat-default ui-corner-all" onclick="openResourceInfos( '+resourceId+')">'+newName+'</li>');
	} else { // halls, tools, 
		newName = $('#new_resource_form input[name="name"]').val();
		$ul.append('<li val="'+resourceId+'" class="hoverable clickable ui-stat-default ui-corner-all" onclick="openResourceInfos( '+resourceId+')">'+newName+'</li>');
	}*/
}


function iniNewResourceEmpAuto(){
	if($('#new_employer_name').length > 0){
		setEmployerAutocomplete("#new_employer_name", "");
	}	
}

/*********************************************************/
	//// Delete resource item
function deleteResourceItem($but){
	var itemType = $but.attr('resourcetype');
	var itemId = $but.attr('itemid');
	var $li = $('#resource_list li.ui-state-active');

	var buttons = [{ 
		text: getLang('yes'), 
		click: function() { 
			var submitDelete = {
				name : 'resources',
				param: 'templ='+itemType+'&delete&id='+itemId,
				post : '',
				callback: function(){
					$('#resource_list li.ui-state-active').fadeOut().remove();
					$("#MS_dialog_confirm").dialog("close");	
					$('#resource_list li:first').click()
				}
			}
			getModuleJson(submitDelete);
		}
	},
	{
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('confirm', getLang('confirm'), getLang('cfm-delete'), 300, 250, buttons, true)
}



/*********************************************************/
	// classes functions
function openClass($but){
	var classId = $but.attr('classid');
	var module ={};
	module.name= 'resources';
	module.title = getLang('class');
	module.data= 'templ=classes&itemid='+classId;
	module.type= 'GET';
	module.div = 'MS_dialog_classes-'+classId;
	
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('classes'),
		maxim:true,
		minim:true,
		buttons:  [{ 
		text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

/*********************************************************/
	// Levels funcions
function openLevel($but){
	var levelId = $but.attr('levelid');
	var module ={};
	module.name= 'resources';
	module.title = getLang('level');
	module.data= 'templ=levels&itemid='+levelId;
	module.type= 'GET';
	module.div = 'MS_dialog_levels-'+levelId;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('levels'),
		maxim:true,
		minim:true,
		buttons:  [{ 
		text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

/*********************************************************/
	// material functions
function updateMat(){
	MS_jsonRequest('resources&templ=materials', $('#material-infos').serialize(), 
		function(answer){
			var  newName = configFile.direction == 'rtl' ? $('#mat_name_ar').val() : $('#mat_name_en').val();
			var $curLi = $('.listMenuUl').find('.ui-state-active');
			$curLi.find('label').html(newName);
			$curLi.find('.color').css({'background-color': '#'+$('input[name="color"]').val()});
		}
	);
}

function reloadMaterialService(){
	services = {};
	services.name= 'services';
	services.title= getLang('materials');
	services.data = 'mat_services='+$('#mat_id').val();
	services.div = '#mat_services_div';
	loadModule(services)
}

function renameMatSub($btn){
	var $a = $btn.parents('a').eq(0);
	var subId = $btn.attr('sub_id');
	var title = $a.find('text.sub_holder-'+subId).text();
	
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:100px; float:left">'+getLang('name')+': </label></td><td><input name="title" type="text" class="input_double" value="'+title+'" /></td></tr></table></form>';
	var dialogOpt = {
		width:450,
		height:250,
		title:getLang('skills'),
		div: 'rename_mat_sub',
		buttons:  [{ 
			text: getLang('rename'), 
			click: function() { 
				var rename = {
					name : 'resources',
					param: 'templ=materials&rename_sub',
					post : 'id='+subId+'&'+$('#rename_mat_sub form').serialize(),
					callback: function(){
						$('text.sub_holder-'+subId).text($('#rename_mat_sub input[name="title"]').val());
						$('#rename_mat_sub').dialog('close')
					}
				}
				getModuleJson(rename);			
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt)
	
}
function newMaterialSub($btn){
	var mat_id = $btn.attr('mat_id');
	html = '<form id="newSub" class="ui-corner-all ui-state-highlight" style="padding:5px" ><input type="hidden" name="id" /><table width="100%" cellspacing="0" border="0"><tbody><tr><td width="120" valign="middel"><label class="label ui-widget-header ui-corner-left">'+getLang('title')+'</label></td><td><input type="hidden" value="'+mat_id+'" name="mat_id"><input type="text" value="" class="input_double" name="title"></td></tr></tbody></table></form>';
	var dialogOpt = {
		width:450,
		height: 180,
		maxim:false,
		minim:false,
		modal:true,
		div: 'new_sub_dialog',
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var $form = $('#newSub');
				var submitSave = {
					name: 'resources',
					param: 'templ=materials&savesub',
					post: $form.serialize(),
					callback: function(answer){
						$('#material-sub-'+mat_id).append(answer.item);
						$('#material-sub-'+mat_id).accordion('destroy').accordion()
						$('#new_sub_dialog').dialog('close');
					}
				}
				getModuleJson(submitSave);
				
			}
		}, {
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt)
}

function deleteMatSub($btn){
	var delSub = {
		name : 'resources',
		param: 'templ=materials&del_sub',
		post : 'id='+$btn.attr('sub_id'),
		callback: function(){
			var $tr = $btn.parents('h3').eq(0);
			$tr.next('div').fadeOut().remove();
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(delSub);			
}

function addSkill($btn){
	var subId = $btn.attr('sub_id');
	var module ={};
	module.name = 'resources';
	module.data = 'templ=materials&skills&add_skill&sub_id='+subId;
	module.title = getLang('materials');
	module.div = 'MS_dialog-skills';

	var dialogOpt = {
		width:450,
		height: 350,
		maxim:false,
		minim:false,
		modal:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var $form = $('#MS_dialog-skills form');
				var submitSave = {
					name: 'resources',
					param: 'templ=materials&skills&saveskill',
					post: $form.serialize(),
					callback: function(answer){
						$('#new_skill_dialog').dialog('close');
					}
				}
				getModuleJson(submitSave);
				
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

function editSkill($btn){
	var skillId = $btn.attr('skill_id');
	var module = {
		name: 'resources',
		data: 'templ=materials&skills&edit_skill&skill_id='+skillId,
		title: getLang('skills'),
		div: 'edit_skill-'+skillId
	}

	var dialogOpt = {
		width:450,
		height: 350,
		maxim:false,
		minim:false,
		modal:true,
		title:getLang( 'edit'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var submitSave = {
					name: 'resources',
					param: 'templ=materials&skills&saveskill',
					post: $('#edit_skill-'+skillId+' form').serialize(),
					callback: function(answer){
						$('#edit_skill-'+skillId).dialog('close');
					}
				}
				getModuleJson(submitSave);
				
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt);
}

function deleteSkill($btn){
	var delSkill = {
		name : 'resources',
		param: 'templ=materials&skills&del_skill',
		post : 'id='+$btn.attr('skill_id'),
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(delSkill);			
}

/*********************************************************/
	// Profs functions
function openProf($but){
	var profId = $but.attr('profid');
	var module ={};
	module.name= 'resources';
	module.title = getLang('profs');
	module.data= 'templ=profs&itemid='+profId;
	module.type= 'GET';
	module.div = 'MS_dialog_profs-'+profId;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('profs'),
		maxim:true,
		minim:true,
		buttons:  [{ 
		text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function importProfs(){
	var buttons = [{ 
		text: getLang('ok'), 
		click: function() { 
			var submitUpdate = {
				name : 'resources',
				param: 'templ=profs&import',
				post :'',
				callback: function(answer){
					if(parseInt(answer.num) > 0){
						MS_alert('<h3>'+answer.num+' '+getLang('prof_imported')+'</h3>');
					} else {
						MS_alert('<h3>'+getLang('no_prof_imported')+'</h3>');
					}
					$("#MS_dialog_import").dialog("close");
					var module ={};
					module.name = 'resources';
					module.data = "templ=profs";
					module.title = getLang('profs');
					module.div = '#resource_main_div';
					module.callback = function(){
						$('#resource_list li:first').addClass('ui-state-active').click();
					}
					loadModule(module)
				}
			}
			getModuleJson(submitUpdate);
		}
	}, {
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('import', getLang('import'), getLang('cfm-import_profs'), 300, 180, buttons, true)
} 

/*********************************************************/
	// Supervisor functions
function openSupervisor($but){
	var superId = $but.attr('superid');
	var module ={};
	module.name= 'resources';
	module.title = getLang('supervisors');
	module.data= 'templ=supervisors&itemid='+superId;
	module.type= 'GET';
	module.div = 'MS_dialog_supervisors-'+superId;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('supervisors'),
		maxim:true,
		minim:true,
		buttons:  [{ 
		text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

/*********************************************************/
	// Principal
function openPrincipal($but){
	var principalId = $but.attr('principalid');
	var module ={};
	module.name= 'resources';
	module.title = getLang('principal');
	module.data= 'templ=principals&itemid='+principalId;
	module.type= 'GET';
	module.div = 'MS_dialog_principals-'+principalId;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('principal'),
		maxim:true,
		minim:true,
		buttons:  [{ 
		text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function deletePricipalLevel($but){
	var principalId = $but.attr('principalid');
	var levelId = $but.attr('levelid');
	var submitDelete = {
		name : 'resources',
		param: 'templ=principals&dellevel',
		post : 'principal_id='+principalId+'&level_id='+levelId,
		callback: function(){
			var principal = {
				rel: 'principals',
				itemid:principalId
			}
			openResourceInfos($('#resource_list li.ui-state-active')); 
		}
	}
	getModuleJson(submitDelete);
}

function updatePrincipalLevels($but){
	var principalId = $but.attr('principalid');
	var module ={};
	module.name = 'resources';
	module.data = "templ=principals&itemid="+principalId+'&updateform';
	module.title = getLang('principal');
	module.div = 'MS_dialog-principal-'+principalId;

	var dialogOpt = {
		width:500,
		height:600,
		title:getLang('levels'),
		maxim:true,
		minim:true,
		callback: function(){
			colectChkox();
		},
		buttons:   [{ 
			text: getLang('save'), 
			click: function() { 
				var submitUpdate = {
					name : 'resources',
					param: 'templ=principals&save',
					post : $('#principal-form-'+principalId).serialize(),
					callback: function(){
						reloadResourceItem(); 
						$('#MS_dialog-principal-'+principalId).dialog("close")
					}
				}
				getModuleJson(submitUpdate);
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

/************ coordinator ********************/
function openPrincipal($but){
	var coordinatorId = $but.attr('coordinator_id');
	var module ={};
	module.name= 'resources';
	module.title = getLang('principal');
	module.data= 'templ=coordinators&itemid='+coordinatorId;
	module.type= 'GET';
	module.div = 'MS_dialog_coordinators-'+coordinatorId;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('coordinators'),
		maxim:true,
		minim:true,
		buttons:  [{ 
		text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function deleteCoordinatorLevel($but){
	var coordinatorId = $but.attr('coordinator_id');
	var levelId = $but.attr('levelid');
	var submitDelete = {
		name : 'resources',
		param: 'templ=coordinators&dellevel',
		post : 'coordinator_id='+coordinatorId+'&level_id='+levelId,
		callback: function(){
			var coordinator = {
				rel: 'principals',
				itemid:coordinatorId
			}
			openResourceInfos($('#resource_list li.ui-state-active')); 
		}
	}
	getModuleJson(submitDelete);
}

function updateCoordinatorLevels($but){
	var coordinatorId = $but.attr('coordinator_id');
	var module ={};
	module.name = 'resources';
	module.data = "templ=coordinators&itemid="+coordinatorId+'&updateform';
	module.title = getLang('coordinators');
	module.div = 'MS_dialog-coordinator-'+coordinatorId;

	var dialogOpt = {
		width:500,
		height:600,
		title:getLang('coordinators'),
		maxim:true,
		minim:true,
		buttons:   [{ 
			text: getLang('save'), 
			click: function() { 
				var submitUpdate = {
					name : 'resources',
					param: 'templ=coordinators&save',
					post : $('#MS_dialog-coordinator-'+coordinatorId+' form').serialize(),
					callback: function(){
						reloadResourceItem(); 
						$('#MS_dialog-coordinator-'+coordinatorId).dialog("close")
					}
				}
				getModuleJson(submitUpdate);
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

function colectChkox(){
	$('.chkTable input[type="checkbox"]').change(function(){
		var $checktable = $(this).parents('.chkTable');
		var $form = $(this).parents('form');
		var $outputField = $form.find('.colectChkox_value');
		var levels = new Array;
		$checktable.find('input:checked').each(function(){
			var itemValue = $(this).val()
			if(levels.indexOf(itemValue) == -1){
				levels.push(itemValue);
			}
		});
		$outputField.val(levels.join(','));
	});
}

/*********************************************************/
	// halls
function openHall($but){
	hallId = $but.attr('hallid');
	var module ={};
	module.name= 'resources';
	module.title = getLang('hall');
	module.data= 'templ=halls&itemid='+hallId;
	module.type= 'GET';
	module.div = 'MS_dialog_halls-'+hallId;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('levels'),
		maxim:true,
		minim:true,
		buttons:  [{ 
		text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}


/*********************************************************/
	// Tools
function openTool($but){
	var toolId = $but.attr('toolid');
	var module ={};
	module.name= 'resources';
	module.title = getLang('tool');
	module.data= 'templ=tools&itemid='+toolId;
	module.type= 'GET';
	module.div = 'MS_dialog_tools-'+toolId;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('levels'),
		maxim:true,
		minim:true,
		buttons:  [{ 
		text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

