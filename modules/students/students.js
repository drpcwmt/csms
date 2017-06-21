// students.js
function openSeachStudentByName(){
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="search_std_name" type="text" class="input_double" /><input id="search_std_id_inp" class="autocomplete_value" type="hidden" /></td></tr></table><label><input type="checkbox" id="search_std_name_regonly" checked="checked" update="initSearchStdName" />'+getLang('registred_std_only')+'</label></form>';

	var dialogOpt = {
		width:500,
		height:200,
		div:'MS_dialog_search_std_name',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				submitSearchStudent('#MS_dialog_search_std_name');
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
	//initiateJquery();
	initSearchStdName();
}

function initSearchStdName(){
	var $sugField = $('#MS_dialog_search_std_name #search_std_name');
	var $checkbox = $('#MS_dialog_search_std_name #search_std_name_regonly');
	if($checkbox.is(":checked")){
		setStudentAutocomplete($sugField, '1,3');
	} else {
		setStudentAutocomplete($sugField, '0,1,2,3,4,5');
	}
}
function openSeachStudentById(){
	var html = '<form><table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('id')+': </label></td><td><input id="search_std_id_inp"  type="text" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_search_std_code',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				submitSearchStudent('#MS_dialog_search_std_code');
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
}

function openSeachStudentByCandNo(){
	var html = '<form><table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('cand_no')+': </label></td><td><input id="search_std_cand_no"  type="text" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_search_std_cand',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				var candToId = {
					name: 'students',
					param: 'cand_to_id',
					post: 'cand_no='+$('#search_std_cand_no').val(),
					title: getLang('students'),
					callback: function(answer){
						$but = $('<button>').attr('std_id', answer.id);
						openStudent($but);
						$('#MS_dialog_search_std_cand').dialog('close');
					}
				}
				getModuleJson(candToId);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);	
}

function submitSearchStudent(dialog){
	var stdId = $(dialog+' #search_std_id_inp').val();
	if(stdId != ''){
		$but = $('<button>').attr('std_id', stdId);
		openStudent($but)
		$(dialog).dialog('close');
	} else {
		$(dialog).append('<div class="ui-state-error ui-corner-all" style="margin-top:15px">( '+$(dialog+' #search_std_name').val()+' )'+getLang('error_not_item_found')+'</div>');
	}
}


function openStudent($but){
	var stdCode = $but.attr('std_id');
	var module ={};
	module.name= 'students';
	module.title = getLang('student');
	module.data= 'std_id='+stdCode;
	if($but.attr('sms_id')){
		module.data += '&sms_id='+$but.attr('sms_id');
	}
	module.type= 'GET';
	module.div = 'MS_dialog_students-'+stdCode;
	var dialogOpt = {
		width:1050,
		height:600,
		title:getLang('student'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			iniStudentModule('#MS_dialog_students-'+stdCode)
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function iniStudentModule(dialog){
	var $dialog= $(dialog);
	$dialog.dialog( "option", "title", getLang('student')+': '+$dialog.find('input[name="name"]').val() );
	if($dialog.find('#student_editable').length>0 && $dialog.find('#student_editable').val() == '1') {
		setAutoStudentFileds(dialog);
		
		var buttons = [];
		buttons.push({
			text: getLang('save'), 
			click: function() { 
				submitStudentData(dialog);
			}
		});
		buttons.push({ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		});
		$dialog.dialog({buttons: buttons});
	} else {
		$dialog.find('form[name="form_student_data"] input, form[name="form_student_data"] textarea').attr('disabled', 'disabled');
		$dialog.find('form[name="form_student_data"] .buttonSet label').not('.ui-state-active').hide();
		$dialog.find('form[name="form_student_data"] .buttonSet label.ui-state-active').removeClass('ui-state-active');
		$dialog.find('form[name="form_student_data"] .ui-combobox-toggle').hide();
	}
	if($dialog.find('form[name="form_parent_data"]').length > 0 ){
		iniStudentParents(dialog);
	}
}

function iniStudentParents(dialog){
	loadModuleJS('parents');
	iniParentModule(dialog);
	
	var $dialog= $(dialog);
	var $studentParentId = $dialog.find('form[name="form_student_data"] input[name="parent_id"]');
	var $parentId = $dialog.find('form[name="form_parent_data"] input[name="id"]');
	$parentId.change(function(){
		$studentParentId.val($(this).val());	
		$dialog.find('form[name="form_student_data"] .this_form_modified').val(1);
	});
}

function submitStudentData(dialog){
	var $dialog= $(dialog);
	
	if($dialog.find('form[name="form_parent_data"]').length > 0 ){
		submitParentData(dialog);	
	}

	if($dialog.find('form[name="form_student_data"] .this_form_modified').val() == '1'){
		if($dialog.find('form[name="form_student_data"] input[name="parent_id"]').val() == ''){
			MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('fill_parent')+'</h2>');	
		} else if(validateForm(dialog+' form[name="form_student_data"]')){
			var save = {
				name: 'students',
				param: 'save_student'+($dialog.find('input[name="sms_id"]').length > 0 ? '&sms_id='+$dialog.find('input[name="sms_id"]').val() : ''),
				post: $dialog.find('form[name="form_student_data"]').serialize(),
				callback: function(answer){
					$(dialog+' form[name="form_student_data"] .this_form_modified').val(0);
					var first_name = $(dialog+' form[name="form_student_data"] input[name="name"]').val();
					var middle_name = $(dialog+' form[name="form_student_data"] input[name="middle_name"]').val();
					var last_name = $(dialog+' form[name="form_student_data"] input[name="last_name"]').val();
					$('text.holder-student_ltr-'+answer.id).html(first_name+' '+middle_name+' '+last_name);
					$('text.holder-student_rtl-'+answer.id).html($(dialog+' form[name="form_student_data"] input[name="name_ar"]').val());
					$dialog.dialog('close');
				}
			}
			getModuleJson(save);
		}
	}
	
	if($dialog.find('form[name="medical_data_form"] .this_form_modified').val() == '1'){
		var saveMedical = {
			name: 'medical',
			param: 'save_data',
			post: $dialog.find('form[name="medical_data_form"]').serialize(),
			title: getLang('medical')
		}
		getModuleJson(saveMedical);
	}
		
	
}

function disinscripStd($but){
	var stdId = $but.attr('std_id');
	var module = {
		name:'students',
		data: 'suspension&id='+stdId,
		title: getLang('suspension'),
		div: 'suspension_dialog'
	}
	
	var dialogOpt = {
		modal: true,
		width: 500,
		height:300,
		cache:true,
		callback:function(){
			$('#suspension_dialog input[name="suspension_reason"]').focus(function(){
				setDefAutocomplete($(this), configFile.DB_student, "student_data");
				$(this).autocomplete('search');
			});
		},
		buttons: [{ 
			text: getLang('ok'), 
			click: function() { 
				var $dialog =$('#suspension_dialog');
				var data = $dialog.find('form').serialize();
				var save = {
					name: 'students',
					param: 'suspension&save',
					post: $dialog.find('form').serialize(),
					callback: function(answer){
						openStudent($but);
						$('#suspension_dialog').dialog('close');
					}
				}
				getModuleJson(save);
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

function insripStd($but){
	var stdId = $but.attr('std_id');
	var module = {
		name:'students',
		data: 'inscription&id='+stdId,
		title: getLang('inscription'),
		div: 'inscription_dialog'
	}
	
	var dialogOpt = {
		modal: true,
		width: 350,
		height:230,
		cache:true,
		buttons: [{ 
			text: getLang('ok'), 
			click: function() { 
				if(validateForm('#inscription_dialog form')){
					var data = $('#inscription_dialog form').serialize();
					MS_jsonRequest('students&inscription', data, 
						function(){
							openStudent($but);
						}
					);
					$(this).dialog('close'); 
				} 
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

/*********************** New Student *********************/

function addNewStudent(){
	var module ={};
	module.name= 'students';
	module.title = getLang('new_student');
	module.data= 'new&step=1';
	module.type= 'POST';
	module.div = 'MS_dialog_'+module.name+'-new';
	module.cache = false,
	module.callback = function(){
		setStudentAutocomplete('#newSugName', '0');
	}
	
	var dialogOpt = {
		width:990,
		height:620,
		title:getLang('new_student'),
		minim:true,
		maxim:true,
		buttons: [{ 
			text: getLang('next'), 
			click: function() { 
				nextWizardStep();
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


function loadNewStdGroup(classId){
	var module ={};
	module.name= 'students';
	module.title = getLang('class');
	module.data= 'new&step=groups&level_id='+$('#newStudentDiv #level_select').val()+'&class_id='+classId+'&year='+$('#newStudentDiv select[name="year"]').val();
	module.div = '#groups_div';
	module.type = 'POST';
	loadModule(module);
}

function SubmitStdClass(){
	$('#newStudentDiv #new_std_class_form .this_form_modified').val(0);
	var stdId = $('#newStudentDiv form[name="form_student_data"] input[name="std_id"]').val();
	var joinDate = $('#newStudentDiv form[name="form_student_data"] input[name="join_date"]').val();
	var module ={};
	module.name= 'students';
	module.params = 'new&std_id='+stdId;
	module.title = getLang('finish');
	module.div= '#step-5'
	module.data= 'step=5&'+$('#newStudentDiv #first_step_form').serialize()+'&'+$('#newStudentDiv #new_std_class_form').serialize()+'&join_date='+joinDate;
	module.type= 'POST';
	module.callback = function(){
		displayNextPage();
	}
	loadModule(module);
}

function nextWizardStep(){
	dialog = "#MS_dialog_students-new";
	var $dialog=$(dialog);
	$('#newStudentForm').removeClass('MS_formed');
	var nextStep = parseInt($('#wizardSteps').val()) + 1;
	// step 1 
	if(nextStep == 2){ // display student data
		if($('#new_std_id').val() != ''){
			displayNextPage();
		} else {
			// validat ebefore continue
			if($dialog.find('input[name="insertType"]:checked').val() == '0' ){
				if( $dialog.find('#old_std_id').val() ==""){
					MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error_find_std_name')+'</h2>');
					$dialog.find('#newSugName').focus();
					return false;
				} else {
					$('#new_std_id').val($('#old_std_id').val());
					$("#newStudentDiv .items").not('#step-1').each(function(){
						$(this).html('');	
					})				
				}
			}
			if($dialog.find('input[name="insertType"]:checked').val() == '1' ){
				if(validateForm('#first_step_form') == false){
					return false;
				}
			}
			var module ={};
			module.name= 'students';
			module.title = getLang('new_student');
			module.param=''
			module.post= 'new&step=2&'+$('#first_step_form').serialize();
			module.callback = function(answer){
				$('#new_std_id').val(answer.id);
				$('#first_step_form input[name="id"]').val(answer.id);
				$('#MS_dialog_students-new #step-2').html(answer.html);
				//setAutoStudentFileds(dialog);
				//iniStudentParents(dialog);
				loadModuleJS('parents');
				iniParentModule(dialog);
				displayNextPage();
			}
			getModuleJson(module);
		}
	} else if(nextStep =='3'){ // submit parent  data and display studentdata
		if($dialog.find('form[name="form_parent_data"] .this_form_modified').val() == '1' && validateForm('#newStudentForm form[name="form_parent_data"]')){
			submitParentData(dialog);	
		}
		var module ={};
		module.name= 'students';
		module.title = getLang('class');
		module.data= 'new&step=3&'+$('#newStudentDiv #first_step_form').serialize()+'&parent_id='+$('#newStudentDiv #parent_form_id').val();
		module.div = '#step-3';
		module.type = 'POST';
		module.callback = function(){
			displayNextPage()
		}
		loadModule(module);
		
	} else if(nextStep == '4'){ // submit student data
		if($dialog.find('form[name="form_student_data"] input[name="parent_id"]').val() == ''){
			MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('fill_parent')+'</h2>');
			return false;	
		} else {
			if(validateForm('#newStudentForm form[name="form_student_data"]')){
				var submitStudent = {
					name: 'students',
					param: 'save_student',
					post: $dialog.find('form[name="form_student_data"]').serialize(),
					callback: function(answer){
						$('#newStudentDiv form[name="form_student_data"] .this_form_modified').val(0);
						$('#newStudentDiv form[name="form_student_data"] input[name="std_id"]').val(answer.id);
						$('#newStudentDiv #first_step_form input[name="id"]').val(answer.id);
						
						var module ={};
						module.name= 'students';
						module.title = getLang('student');
						module.data= 'new&step=4&'+$('#newStudentDiv #first_step_form').serialize();
						module.div = '#step-4';
						module.type = 'POST';
						module.callback = function(){
							displayNextPage()
						}
						loadModule(module);
					}
				};
				getModuleJson(submitStudent);
			}
		}		
	} else if(nextStep =='5'){ // Submit Class && assign materials
		if($('#newStudentDiv input[name="class_id"]:checked').length > 0){
			SubmitStdClass();
		} else {
			var buttons = [{ 
				text: getLang('ok'), 
				click: function() { 
					$(this).dialog('close');
					SubmitStdClass();
				}
			}, {
				text: getLang('cancel'), 
				click: function() { 
					$(this).dialog('close');
				}
			}];
			createHtmlDialog('confirm', getLang('confirm'), '<div class="ui-corner-all ui-state-highlight"><h3><img src="assets/img/warning.png" style="vertical-align: middle" />'+getLang('warning')+'</h3><p>'+getLang('warning_select_class')+'</p></div>', 400, 220, buttons, true)

		} 
	}
}

function prevWizardStep(){
	var prevStep = parseInt($('#wizardSteps').val()) - 1;
	var left = (prevStep-1) * 950 * -1;
	$('#newStudentDiv').animate({left:left+'px' } , 500, function(){
		/*if(prevStep == 1){ 
			$("#newStudentDiv .items").not('#step-1').each(function(){
				$(this).html('');	
			})
		}*/
		$('#wizardSteps').val(prevStep);
		var buttons = [];
		if(prevStep != 1){
			buttons.push({ 
				text: getLang('prev'), 
				click: function() { 
					prevWizardStep();
				}
			});
		};
		
		buttons.push({ 
			text: getLang('next'), 
			click: function() { 
				nextWizardStep();
			}
		}, { 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		});
		$('#MS_dialog_students-new').dialog({ buttons: buttons});
	});
}

function displayNextPage(){
	var nextStep = parseInt($('#wizardSteps').val()) + 1;
	var left = parseInt($('#wizardSteps').val()) * 950 * -1;
	initiateJquery();
	$('#wizardSteps').val(nextStep);
	$('#newStudentDiv').animate({left: left+'px' } , 500);
	var buttons = [{ 
		text: getLang('prev'), 
		click: function() { 
			prevWizardStep();
		}
	}];
	if(nextStep < 5) {
		buttons.push({ 
			text: getLang('next'), 
			click: function() { 
				nextWizardStep();
			}
		});
	}

	if(nextStep ==5){ // finish
		buttons.push({ 
			text: getLang('finish'), 
			click: function() { 
				$(this).dialog('close')
			}
		},{ 
			text: getLang('finish-n-insert'), 
			click: function() { 
				finishNewWizard(true);
			}
		});
	} else {
		buttons.push({ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		});
	}
	$('#MS_dialog_students-new').dialog({ buttons: buttons}); 
}

function finishNewWizard(){
	$('#MS_dialog_students-new').dialog('close');	
	addNewStudent();
}
/******************* function *******************/
// Autocomplete
function setStudentAutocomplete(input, param, sms_id){
	var source = 'index.php?module=students&student_autocomplete';
	if(param && param !=''){
		source += '&w='+param;
	}
	if($(input).attr('sms_id')){
		source += '&sms_id='+$(input).attr('sms_id');
	} else if(sms_id){
		source += '&sms_id='+sms_id;
	}
	$(input).autocomplete({
		source: source,	
		minLength: 2,
		select: function(event, ui) {
			var name = ui.item.name ? ui.item.name : '';
			$(input).val(name);
			$(input).attr('term',ui.item.id);
			if($(input).nextAll('input.autocomplete_value')){
				$(input).nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($(input).nextAll('div.ui-state-error')){
				$(input).nextAll('div.ui-state-error').fadeOut().remove();
			}
			return false;
		},	
		search: function(event, ui) {
			$(input).attr('term', '');	
			$(input).nextAll('input.autocomplete_value').val('');
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		if(item.error){
			MS_alert('<h3 class="title_wihte"><img src="assets/img/error.png" />'+item.error+'</h3>');
			return $( '<li class="ui-state-error ui-corner-all"></li>' )
				.data( "item.autocomplete", item )
				.append( '<a>' + item.error+"</a>" )
				.appendTo( ul );
			//return false;
		} else {
			var name = item.label ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( "<a>" + name+ ((item.clas && item.clas !='') ? '<span style="color:green"> '+item.clas+'</span>' : '<span style="color:red"> *</span>' )+"</a>" )
				.appendTo( ul );
		}
	};
	
	$(input).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	$(input).focus(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});
	
	$(input).blur(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeOut();
	});
	$(input).keypress(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function setAutoStudentFileds(dialog){
	var $dialog= $(dialog);
	$dialog.find('form[name="form_student_data"] .ena_auto').focus(function(){
		setDefAutocomplete($(this), configFile.DB_student, 'student_data');	
		$(this).autocomplete('search');
	});
}

function copyRelated(inp, inputs){
	var $form =$(inp).parents('.ui-dialog-content');
	var input = inputs.split(',');
	for(x=0; x<input.length; x++){
		if($form.find(input[x]).val() == ''){
			$form.find(input[x]).val($(inp).val());
		}
	};
}

function changStdThumb(id){
	loadModuleJS('upload');
	var dir = 'attachs/files/'+id;	
	var callback = "$('#thumb-std-"+id+"').attr('src', '"+dir+"/folder.jpg?timestamp="+ new Date().getTime()+"')";
	uploadFile('attachs/files/'+id, 'folder.jpg', true, callback);	
}

function scanStdFile(id){
	var dir = 'attachs/files/'+id;	
	var callback = "$('#thumb-std-"+id+"').attr('src', '"+dir+"/folder.jpg?timestamp="+ new Date().getTime()+"')";
	scanFile('attachs/files/'+id, 'folder.jpg', true, callback);	
}


/*********************** Student List *********************/
function getStudentlist($but){
	var mainParam = $but.attr('field') ? $but.attr('field')+'='+$but.attr('rel') : '';
	var module ={};
	module.name= 'students';
	module.title = getLang('students_lists');
	module.data= 'listform';
	module.type= 'GET';
	module.div = 'MS_dialog_student_list';
	module.cache = false,
	module.callback = function(){
		$('#list_main_param').val(mainParam);
	}
	var dialogOpt = {
		width:420,
		height: 460,
		modal:false,
		cache:false,
		buttons: [{ 
			text: getLang('next'), 
			click: function() { 
				validateSelectField();
			}
		},{ 
			text: getLang('end'), 
			click: function() { 
				if($('#list_content input[name="fields[]"]:checked').length > 0){
					submitSearchForm();
					$(this).dialog('close');
				} else {
					MS_alert('<h3 class="title_wihte"><img src="assets/img/warnning.png" />'+getLang('list_no_fld_selected')+'</h3>');
				}
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

function getSavedProced(){
	var module ={};
	module.name= 'students';
	module.title = getLang('students_lists');
	module.data= 'saved_req';
	module.div = 'MS_dialog_query_list';
	var dialogOpt = {
		width:400,
		height: 500,
		modal:false,
		cache:false,
		buttons: [{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
//	setDefAutocomplete($('#search_request_name'), window.DB_student, 'list_procudures');
//	$('#search_request_name').autocomplete('search');	
}

function deleteSavedProced($btn){
	var ins = {
		name: 'students',
		param: '',
		post : 'del_req='+$btn.attr('query_id'),
		callback:  function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(ins);				
}

function openQuery($btn){
	var module ={};
	module.name= 'students';
	module.title = getLang('saved_lists');
	module.data= 'req='+$btn.attr('query_id');
	module.div = '#home_content';
	loadModule(module)
	$('#MS_dialog_search_list').dialog('close');
}

function displayNextSearchPage(){
	var left = parseInt($('#list_content').css('left')) - 420 ;
	$('#list_content').animate({left: left+'px' } , 300);
	var buttons = [{ 
		text: getLang('prev'), 
		click: function() { 
			displayPrevSearchPage();
		}
	}];
	if(left > -1260){ // finish
		buttons.push({ 
			text: getLang('next'), 
			click: function() { 
				displayNextSearchPage();
			}
		});
	}
	
	buttons.push({ 
		text: getLang('end'), 
		click: function() { 
			submitSearchForm();
		}
	}, { 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	});
	
	$('#MS_dialog_student_list').dialog({ buttons: buttons}); 
}

function displayPrevSearchPage(){
	var left = parseInt($('#list_content').css('left')) + 420 ;
	$('#list_content').animate({left: left+'px' } , 300);
	var buttons = [];
	if(left < 0){ // finish
		buttons.push({ 
			text: getLang('prev'), 
			click: function() { 
				displayPrevSearchPage();
			}
		});
	} 
		
	buttons.push({ 
		text: getLang('next'), 
		click: function() { 
			displayNextSearchPage();
		}
	},{ 
		text: getLang('end'), 
		click: function() { 
			submitSearchForm();
		}
	},{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	});
	
	$('#MS_dialog_student_list').dialog({ buttons: buttons}); 
}

function insertParam(){
	var parms = '<div class="ui-corner-all ui-widget-content filter_row" style="margin-bottom:5px">'+
		'<button type="button" action="removeFilter" class="ui-state-default hoverable circle_button"><span class="ui-icon ui-icon-trash"></span></button> '+
		'<select onchange="evalField(this)" class="selected_field" style="width:126px">'+
			$('#fields_select').html()+
		'</select>'+
	'</div>';
	$('#param_div').append(parms);
}

function clearParam(){
	$('#list_params').val('');
	$('#param_div').html('');
}

function removeFilter($btn){
	var $row = $btn.parents('div.filter_row').fadeOut().remove();
	serializeFilters();
}
	
function evalField(field){
	$(field).next('.pram_select').hide().remove();
	$(field).next('.pram_pre_select').hide().remove();
	$(field).nextAll('.values').hide().remove();
	var fieldVal = getFieldShortName($(field).val());
	if(fieldVal != ''){
		var selectableFields= new Array();
		selectableFields['sex'] = new Array();
		selectableFields['sex']['1'] = getLang('male');
		selectableFields['sex']['2'] = getLang('female');
		selectableFields['religion'] = new Array();
		selectableFields['religion']['1'] = getLang('muslim');
		selectableFields['religion']['2'] = getLang('christian');
		selectableFields['father_religion'] = new Array();
		selectableFields['father_religion']['1'] = getLang('muslim');
		selectableFields['father_religion']['2'] = getLang('christian');
		selectableFields['mother_religion'] = new Array();
		selectableFields['mother_religion']['1'] = getLang('muslim');
		selectableFields['mother_religion']['2'] = getLang('christian');
		selectableFields['father_emp'] = new Array();
		selectableFields['father_emp']['1'] = getLang('yes');
		selectableFields['father_emp']['0'] = getLang('no');
		selectableFields['mother_emp'] = new Array();
		selectableFields['mother_emp']['1'] = getLang('yes');
		selectableFields['mother_emp']['0'] = getLang('no');
		selectableFields['father_resp'] = new Array();
		selectableFields['father_resp']['1'] = getLang('yes');
		selectableFields['father_resp']['0'] = getLang('no');
		selectableFields['mother_resp'] = new Array();
		selectableFields['mother_resp']['1'] = getLang('yes');
		selectableFields['mother_resp']['0'] = getLang('no');
		
		var fieldsSelct = '';
		if(fieldVal =='lang_1'|| fieldVal == 'lang_2' || fieldVal == 'lang_3'){
			var module = {
				name: 'resources',
				data: 'templ=materials&mat_list_opt',
				div:'#dd',
				callback: function(answer){
					fieldsSelct += answer;
					$(field).after('<select class="pram_pre_select" onchange="evalCompare(this)">'+fieldsSelct+'</select>');			
				}
			}
			loadModule(module);
			
		} else if(selectableFields[fieldVal] ){
			var fieldsSelct = '<option value=""></option>';
			for(var key in selectableFields[fieldVal]){
				fieldsSelct += '<option value="'+key+'">'+selectableFields[fieldVal][key]+'</option>';
			}
			$(field).after('<select class="pram_pre_select" onchange="evalCompare(this)">'+fieldsSelct+'</select>');
		} else {
			var compare = '<select class="pram_select" style="width:70px" onchange="evalCompare(this)">'+
				'<option value=""></option>'+
				'<option value="=" >'+getLang('equal')+'</option>'+
				'<option value="!=" >'+getLang('not_equal')+'</option>'+
				'<option value="IS NULL" >'+getLang('empty')+'</option>'+
				'<option value="IS NOT NULL" >'+getLang('not_empty')+'</option>'+
				'<option value="<" >'+getLang('less')+'</option>'+
				'<option value=">" >'+getLang('greater')+'</option>'+
				'<option value="<=" >'+getLang('less_equal')+'</option>'+
				'<option value=">=" >'+getLang('grater_equal')+'</option>'+
			'</select>';
			$(field).after(compare);
		}
	}
}

function evalCompare(compareField){
	$(compareField).next('.values').hide().remove();
	var field = $(compareField).prevAll('.selected_field').val();
	if($(compareField).val()== 'IS NULL' || $(compareField).val()== 'IS NOT NULL'){
		serializeFilters();
		//addParamToField(compareField, field, ' ', $(compareField).val())
	} else if($(compareField).hasClass('pram_pre_select') ){
		serializeFilters()
		//addParamToField(compareField, field, '=', $(compareField).val())
	} else {
		var $selcted_filter = $(compareField).prevAll('.selected_field').find('option:selected');
		var db = $selcted_filter.attr('db');
		var table = $selcted_filter.attr('t');
		var fld = $selcted_filter.val();
		$(compareField).after('<input type="text" name="'+fld+'" class="values" onblur="serializeFilters()"/>');
		if(field.indexOf('_date') != -1){
			$(compareField).next('.values').addClass('mask-date');
			formatMaskInput();
		} else if(field.indexOf('tel') != -1 || field.indexOf('mail') != -1 || field.indexOf('mobil') != -1 ){
		
		} else {
			setDefAutocomplete($(compareField).next('.values'), db, table);
			$(compareField).next('.values').autocomplete('search');
		}
	}
}

function evalValue(valueField){
	var compare = $(valueField).prev('.pram_select').val();
	var field = $(valueField).prevAll('.selected_field').val();
	addParamToField(valueField, field, compare, $(valueField).val())
	//$('#list_params').val($('#list_params').val()+ ';' + field + compare + "'"+$(valueField).val()+"'");
	
}

function serializeFilters(){
	var params = new Array;
	$('#param_div .selected_field').each(function(){
		var $row = $(this).parent('div.filter_row');		
		var f = $(this).val();
		var $c =  $row.find('.pram_select');
		var v = $row.find('.values').val();
		if($c.length>0){
			if($c.val() == 'IS NULL' || $c.val() == 'IS NOT NULL'){
				params.push( f+" "+$c.val());
			} else {
				$v = $row.find('.values');
				if($v.hasClass('mask-date')){
					var d = v.split('/');
					var date = new Date(parseInt(d[2]),parseInt(d[1])-1, parseInt(d[0])-1);
					var dunix = Math.floor(date/1000); 
					v = dunix; 
				}		
				params.push( f+$c.val()+"'"+v+"'");
			}
		} else if($row.find('.pram_pre_select').length >0){
			params.push( f+"='"+$row.find('.pram_pre_select').val()+"'");	
			
		}
		
	});
	$('#list_params').val(params.join(';'))
		
}

function addParamToField(field, param, compare, value){
//	var old_params =$('#list_params').val() + ($('#list_params').val()!= '' ? ';' : '');
	var params = new Array();
	if(value == 'IS NULL' || value == 'IS NOT NULL'){
		$('#list_params').val(old_params+param+' '+value);
	} else {
		if($(field).hasClass('mask-date')){
			var d = value.split('/');
			var date = new Date(parseInt(d[2]),parseInt(d[1])-1, parseInt(d[0])-1);
			var dunix = Math.floor(date/1000); 
			value = dunix; 
		} 
		return old_params+param+compare+"'"+value+"'";
		//	$('#list_params').val(old_params+param+compare+"'"+value+"'");
	}
	return;
	/*if(old_params != ''){
		var ps = old_params.split(';');
		if(ps.length > 0){
			var fld, fld_val;
			for(var p in ps	){
				if(ps[p].indexOf('IS NULL') != -1){
					fld = ps[p].replace(' IS NULL', '');
					fld_val = ' IS NULL';
				} else if(ps[p].indexOf('IS NOT NULL') != -1) {
					fld = ps[p].replace(' IS NOT NULL', '');
					fld_val = $lang['not_empty'];
				} else if(ps[p].indexOf('!=') != -1){
					fss = ps[p].split('!=');
					fld = '!='+fss[0];
					fld_val = fss[1];
				} else if(ps[p].indexOf('<=') != -1){
					fss = ps[p].split('<=');
					fld = fss[0];
					fld_val = '<='+fss[1];
				} else if(ps[p].indexOf('>=') != -1){
					fss = ps[p].split('>=');
					fld = fss[0];
					fld_val = '>='+fss[1];
				} else if(ps[p].indexOf('>') != -1){
					fss = ps[p].split('>');
					fld = fss[0];
					fld_val = '>'+fss[1];
				} else if(ps[p].indexOf('<') != -1){
					fss = ps[p].split('<');
					fld = fss[0];
					fld_val = '<'+fss[1];
				} else if(ps[p].indexOf('=') != -1){
					fss = ps[p].split('=');
					fld = fss[0];
					fld_val = '='+fss[1];
				} 
				params[fld] = fld_val;	
			}
		}
	}
	if($(field).hasClass('mask-date')){
		var d = value.split('/');
		var date = new Date(parseInt(d[2]),parseInt(d[1])-1, parseInt(d[0])-1);
		var dunix = Math.floor(date/1000); 
		params[param] = compare + dunix; 
	} else {
		params[param] = compare+"'"+value+"'";
	}
	var final = new Array();
	for (var k in params){
		final.push(k+params[k]);
	}
	$('#list_params').val(final.join(';'));*/
}

function validateSelectField(){
	if($('#list_content input[name="fields[]"]:checked').length > 0){
		displayNextSearchPage();
	} else {
		MS_alert('<h3 class="title_wihte"><img src="assets/img/warnning.png" />'+getLang('list_no_fld_selected')+'</h3>');
	}
}

function submitSearchForm(){
	if($('#list_content input[name="fields[]"]:checked').length > 0){
		serializeFilters();
		$('#MS_dialog_student_list').dialog('close');
		var module ={};
		module.name= 'students';
		module.title = getLang('students_lists');
		module.data= $('#list_content').serialize();
		module.type= 'POST';
		module.div = '#home_content'
		
		loadModule(module)
	} else {
		MS_alert('<h3 class="title_wihte"><img src="assets/img/warnning.png" />'+getLang('list_no_fld_selected')+'</h3>');
	}	
}

function saveLastRequet(){
	var html = '<table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('title')+': </label></td><td><input id="request_name" name="name" type="text" class="input_double" /></td></tr></table>';
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			MS_jsonRequest('students',  'savereq='+$('#request_name').val(), '$("#MS_dialog_save").dialog("close")')

		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('save', getLang('save_requet'), html, 470, 170, buttons, false);
	setDefAutocomplete($('#request_name'), window.DB_student, 'list_procudures');	
}


/***********************  common student select dialog *********************/
function loadStdFromClass(classId, li){
	var $thisUl = $(li).parent('ul');
	var $thisTd = $thisUl.parent('td');
	var classTd = $thisTd.next('td');
	var classDiv = "selClassDiv-"+classId;
	if(classTd.find("#"+classDiv).length > 0){
		if(classTd.find("#"+classDiv+":visible").length == 0){
			classTd.find('div.selClass:visible').slideUp();
			classTd.find("#"+classDiv).slideDown("slow");
		}
	} else {
		classTd.find('div.selClass:visible').slideUp();
		var module ={};
		module.name= 'students';
		module.title = getLang('students_list');
		module.data= 'stdfp&class_id='+classId;
		module.div = "#"+classDiv
		classTd.append(MS_aJaxRequest(module, 'GET', false, ' iniMsUi();$("#'+classDiv+'").slideDown("slow");'));		
	}
}

function loadStdFromGroup(groupId, li){
	var $thisTd = $(li).parents('td');
	var classTd = $thisTd.next('td');
	var classDiv = "selGroupDiv-"+groupId;
	if(classTd.find("#"+classDiv).length > 0){
		if(classTd.find("#"+classDiv+":visible").length == 0){
			classTd.find('div.selClass:visible').slideUp();
			classTd.find("#"+classDiv).slideDown();
		}
	} else {
		classTd.find('div.selClass:visible').slideUp();
		var module ={};
		module.name= 'students';
		module.title = getLang('students_list');
		module.data= 'stdfp&group_id='+groupId;
		module.div = "#"+classDiv
		classTd.append(MS_aJaxRequest(module, 'GET', false, '$("#'+classDiv+'").slideDown()'));		
	}
}

function expandList(handler){
	$(handler).find('span').toggleClass('ui-icon-triangle-1-n ui-icon-triangle-1-s');
	var $li = $(handler).parents('li');
	$li.toggleClass('ui-corner-top ui-corner-all');
	if($li.next('ul:visible').length > 0){
		$li.next('ul').slideUp();
	} else {
		$li.next('ul').slideDown();
	}
}


function certScholarity($but){
	loadModuleJS('reports');
	openReport('file=cert_scolarity&std_id='+$but.attr('std_id'));
}

function certRadiation($but){
	loadModuleJS('reports');
	openReport('file=cert_radiation&std_id='+$but.attr('std_id'));
}

function suspendStudent($but){
	var module = {
		name:'students',
		data: 'suspend_std',
		title: getLang('suspension'),
		div: 'suspension_dialog'
	}
	
	var dialogOpt = {
		modal: true,
		width: 500,
		height:250,
		cache:true,
		callback:function(){
			setStudentAutocomplete('#suspension_dialog .search_std_name');
			$('input.final_suspension_reason').focus(function(){
				setDefAutocomplete($(this), configFile.DB_student, "student_data");
				$(this).autocomplete('search');
			});
		},
		buttons: [{ 
			text: getLang('ok'), 
			click: function() {
				var ins = {
					name: 'students',
					param: 'suspension',
					post : $('#suspension_dialog form').serialize(),
					callback:  function(){
						var finalize = {
							name: 'reports',
							data: 'redoubling',
							title: getLang('suspended_list'),
							div: '#finalize_quit_table'
						}
						loadModule(finalize);
						$('#suspension_dialog').dialog('close');
					}
				}
				getModuleJson(ins);				
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

function delStdRsult($btn){
	var del = {
		name: 'students',
		param: 'del_result',
		post: 'std_id='+$btn.attr('std_id'),
		title: getLang('delete'),
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove()
		}
	}
	getModuleJson(del);
}


function getBusCard($btn){
	var stdId = $btn.attr('std_id');
	var smsId = $btn.attr('sms_id');
	var module = {
		name:'students',
		data: 'bus_card&std_id='+stdId+'&sms_id='+smsId,
		title: getLang('bus'),
		div: 'bus_card_dialog'
	}
	
	var dialogOpt = {
		modal: true,
		width: 400,
		height:300,
		cache:true,
		buttons: [{ 
			text: getLang('print'), 
			click: function() {
				printDialog('#bus_card_dialog')			
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	
	openAjaxDialog(module, dialogOpt);	
}