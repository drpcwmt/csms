// Employers

function setEmployerAutocomplete(input, param){
	var source = 'index.php?module=employers&autocomplete';
	if(param && param !=''){
		source += '&w='+param;
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
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		var name = item.label ? item.label : '';
		return $( '<li></li>' )
			.data( "item.autocomplete", item )
			.append( "<a>" + name+"</a>" )
			.appendTo( ul );
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

function newEmployer(){
	var module = {
		name: 'employers',
		data: 'new',
		title: getLang('employers'),
		div : 'MS_dialog-new_emp'
	}
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('Employers'),
		maxim:true,
		minim:true,
		buttons:  [{ 
			text: getLang('add'), 
			click: function() { 
				if(validateForm('#MS_dialog-new_emp form[name="employer_data"]')){
					var submitSave = {
						name: 'employers',
						param: 'save',
						post: $('#MS_dialog-new_emp form[name="employer_data"]').serialize(),
						callback: function(answer){
							var $id = $('#MS_dialog-new_emp form[name="employer_data"]').find('input[name="id"');
							$id.val(answer.id);
							$('#MS_dialog-new_emp').dialog('close');
						}
					}
					getModuleJson(submitSave);
				}
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			iniEmployerModule('#MS_dialog-new_emp')
		}
	}
	openAjaxDialog(module, dialogOpt)
}

function openSeachByName(){
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="search_name" type="text" class="input_double" /><input id="search_id_inp" class="autocomplete_value" type="hidden" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_search_name',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				submitSearchEmp('#MS_dialog_search_name');
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
	initiateJquery();
	setEmployerAutocomplete('#MS_dialog_search_name #search_name');
}

function openSeachById(){
	var html = '<form><table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('id')+': </label></td><td><input id="search_id_inp"  type="text" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_search_code',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				submitSearchEmp('#MS_dialog_search_code');
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

function submitSearchEmp(dialog){
	var empId = $(dialog+' #search_id_inp').val();
	if(empId != ''){
		$but = $('<button>').attr('empid', empId);
		openEmployer($but)
		$(dialog).dialog('close');
	} else {
		$(dialog).append('<div class="ui-state-error ui-corner-all" style="margin-top:15px">( '+getLang('error_not_item_found')+'</div>');
	}
}

function openEmployer($but){
	var empCode = $but.attr('empid');
	var module ={};
	module.name= 'employers';
	module.title = getLang('employer');
	module.data= 'id='+empCode;
	module.type= 'GET';
	module.div = 'MS_dialog_employers-'+empCode;
	var dialogOpt = {
		width:1050,
		height:600,
		title:getLang('employer'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			iniEmployerModule('#MS_dialog_employers-'+empCode)
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function iniEmployerModule(dialog){
	var $dialog= $(dialog);
	$dialog.dialog( "option", "title", getLang('employer')+': '+$dialog.find('input[name="name_rtl"]').val() );
	if($dialog.find('#employer_editable').length>0 && $dialog.find('#employer_editable').val() == '1') {
		setAutoEmpFileds(dialog);
		
		var buttons = [];
		buttons.push({
			text: getLang('save'), 
			click: function() { 
				submitEmployerData(dialog);
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
		$dialog.find('form[name="employer_data"] input, form[name="form_employer_data"] textarea').attr('disabled', 'disabled');
		$dialog.find('form[name="employer_data"] .buttonSet label').not('.ui-state-active').hide();
		$dialog.find('form[name="employer_data"] .buttonSet label.ui-state-active').removeClass('ui-state-active');
		$dialog.find('form[name="employer_data"] .ui-combobox-toggle').hide();
	}
}

function submitEmployerData(dialog){
	var $dialog= $(dialog);

	if($dialog.find('form[name="employer_data"] .this_form_modified').val() == '1'){
		if(validateForm(dialog+' form[name="employer_data"]')){
			var save = {
				name: 'employers',
				param: 'save',
				post: $dialog.find('form[name="employer_data"]').serialize(),
				title: getLang('Employer')
			}
			getModuleJson(save);
		}
	}
}

function setAutoEmpFileds(dialog){
	var $dialog= $(dialog);
	$dialog.find('form[name="employer_data"] .ena_auto').focus(function(){
		setDefAutocomplete($(this), configFile.database, 'employer_data');	
		$(this).autocomplete('search');
	});
}

function openJob($but){
	var jobId = $but.attr('job_id');
	var module = {
		name:'employers',
		data:'jobs&list&job_id='+jobId,
		div: '#job_content'
	}
	loadModule(module);
}
	
function saveJobProfil($but){
	var $form = $but.parents('form');
	var save = {
		name: 'salary',
		param: 'profil&save',
		post: $form.serialize(),
		title: getLang('profil')
	}
	getModuleJson(save);
}

function recordWeekDay($btn){
	$btn.toggleClass('ui-state-active ui-state-default');
	var $warper = $btn.parents('.week_warper').eq(0);
	var dayArr = new Array();
	$warper.find("li").each(function(){
		if($(this).hasClass('ui-state-active')){
			dayArr.push($(this).attr('val'));
		}
	})
	$warper.find('input[name="working_days"]').val(dayArr.join(','));
}
