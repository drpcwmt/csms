function openParentSeachByName(){
	var html = '<table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="search_parent_name" type="text" class="input_double" /><input id="search_parent_id_inp" class="autocomplete_value" type="hidden" /></td></tr></table>';

	var buttons = [{ 
		text: getLang('search'), 
		click: function() { 
			submitSearchParent('#MS_dialog_search_parent');
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('search_parent', getLang('search_parent'),  html, 500, 200, buttons)
	setParentAutocomplete('#search_parent_name');
}

function openParentSeachById(){
	var html = '<table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('id')+': </label></td><td><input id="search_parent_id_inp"  type="text" /></td></tr></table>';

	var buttons = [{ 
		text: getLang('search'), 
		click: function() { 
			submitSearchParent('#MS_dialog_search_parent');
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('search_parent', getLang('search_parent'),  html, 400, 170, buttons)
}

function submitSearchParent(dialog){
	var $dialog = $(dialog);
	var parentId = $dialog.find('#search_parent_id_inp').val();
	if(parentId != ''){
		$dialog.dialog('close');
		var $btn = $('<button>');
		$btn.attr('parentid', parentId);
		openParent($btn)
	} else {
		$dialog.append('<div class="ui-state-error ui-corner-all" style="margin-top:15px">( '+$('#search_parent_id_inp').val()+' )'+getLang('error_not_item_found')+'</div>');
	}
}

function getParentlist($but){
	var params = $but.attr('field') == 'all' ? 'all' : $but.attr('field')+'='+$but.attr('rel');
	var module = {};
	module.name = 'parents';
	module.div ='#home_content';
	module.title = getLang('parent_list');
	module.data = 'list&'+params;
	loadModule(module);
}

function iniParentModule(dialog){
	var $dialog= $(dialog);
	var $parentForm = $dialog.find('form[name="form_parent_data"]');
	if($parentForm.find('.editable').length > 0 && $parentForm.find('.editable').val() == '1') {
		iniParentSearch(dialog, $parentForm.find('input[name="father_name"]') );
		$parentForm.find('.ena_auto').focus(function(){
			setDefAutocomplete($(this), configFile.DB_student, 'parents');	
			$(this).autocomplete('search');
		});	

	} else {
		$parentForm.find('input, textarea').attr('disabled', 'disabled');
		$parentForm.find('.buttonSet label').not('.ui-state-active').hide();
		$parentForm.find('.buttonSet label.ui-state-active').removeClass('ui-state-active');
		$parentForm.find('.ui-combobox-toggle').hide();
	}

}

function iniParentDialog(dialog){
	var $dialog= $(dialog);
	$dialog.dialog( "option", "title", getLang('parent')+': '+$dialog.find('input[name="father_name"]').val() );
	if($dialog.find('.editable').length>0 && $dialog.find('.editable').val() == '1') {
		var buttons = [{
			text: getLang('save'), 
			click: function() { 
				submitParentData(dialog);
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}];
		$dialog.dialog({buttons: buttons});
	}
}

function iniParentSearch(dialog, $input){
	var $dialog= $(dialog);
	var $parentForm = $dialog.find('form[name="form_parent_data"]');
	$input.autocomplete({
		source:'index.php?module=parents&parent_autocomplete',
		minLength: 2,
		select: function(event, ui) {
			for( var key in ui.item){
				// checkboxs
				if($parentForm.find('input[name="'+key+'"]').attr('type') =="checkbox") {
					if(ui.item[key] == '1'){
						$parentForm.find('input[name="'+key+'"]').attr('checked', 'checked'); 
					} 
				// Radios
				} else if ($parentForm.find('input[name="'+key+'"]:first').attr('type') =="radio") { 
						$parentForm.find('input[name="'+key+'"]').each(function(){
							if($(this).val() == ui.item[key]){
								$(this).attr('checked', 'checked'); 
							} else{
								$(this).removeAttr('checked'); 
							}
						})
				// Text
				} else if ($parentForm.find('input[name="'+key+'"]:first').attr('type') =="text"){
					$parentForm.find('input[name="'+key+'"]').val(ui.item[key]);
				}
			}
			$parentForm.find('fieldset.father .phonebook_holder ul').replaceWith(ui.item['father_phone_book']);
			$parentForm.find('fieldset.father .addressbook_holder ul').replaceWith(ui.item['father_address_div']);
			$parentForm.find('fieldset.father .mailbook_holder ul').replaceWith(ui.item['father_mail_book']);
			$parentForm.find('fieldset.mother .phonebook_holder ul').replaceWith(ui.item['mother_phone_book']);
			$parentForm.find('fieldset.mother .addressbook_holder ul').replaceWith(ui.item['mother_address_div']);
			$parentForm.find('fieldset.mother .mailbook_holder ul').replaceWith(ui.item['mother_mail_book']);
			$parentForm.find('.notebook_holder ul').replaceWith(ui.item['notebook']);
			$parentForm.find('input[name="id"]').change();
			$parentForm.find('.buttonSet').buttonset("refresh");
			$parentForm.find('a[con_id]').attr('con_id', ui.item['id']);
			$parentForm.find('.family_id').val(ui.item['id']);
			$parentForm.find('#parent_form_id').val(ui.item['id']);
			return false;
		},	
		/*search: function(event, ui) {
			$parentForm.find('input[type="text"]').not($input).val('');
			$parentForm.find('input[type="checkbox"]').removeAttr('checked');	
		},*/
		open: function() {
			$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
		},
		close: function() {
			$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		return $( '<li></li>' )
			.data( "item.autocomplete", item )
			.append( "<a>" + item[$input.attr('name')] +"</a>" )
			.appendTo( ul );
	};
		
	$input.focus(function(){
		$(this).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="resetParentForm(\''+dialog+' \')" ></span>');
	});
	
	$input.blur(function(){
		setTimeout(function(){
			$(this).next('.ui-icon-arrowrefresh-1-w').fadeOut().remove();
		}, 500)
	});
	$input.keypress(function(){
		$(this).next('.ui-icon-arrowrefresh-1-w').fadeIn();
	});
}

function submitParentData(dialog){
	var $dialog= $(dialog);
	if($dialog.find('form[name="form_parent_data"] .this_form_modified').val() == '1'){
		if(validateForm(dialog+' form[name="form_parent_data"]')){
			MS_jsonRequest('parents', $dialog.find('form[name="form_parent_data"]').serialize(),function(answer){
				$dialog.find('form[name="form_parent_data"] input[name="id"]').val(answer.id);
				$dialog.find('form[name="form_parent_data"] input[name="id"]').change();
				$dialog.find('form[name="form_parent_data"] .this_form_modified').val(0);
			});
		}
	}
}

function resetParentForm(dialog){
	if($(dialog).hasClass('ui-dialog-content')){
		var $dialog= $(dialog);
	} else {
		var $dialog = $(dialog).parents('.ui-dialog-content');
		dialog = '#'+$dialog.attr('id');
	}
	resetForm(dialog+' form[name="form_parent_data"]');
	$dialog.find('fieldset.father .phonebook_holder ul').html('');
	$dialog.find('fieldset.father .addressbook_holder ul').html('');
	$dialog.find('fieldset.father .mailbook_holder ul').html('');
	$dialog.find('fieldset.mother .phonebook_holder ul').html('');
	$dialog.find('fieldset.mother .addressbook_holder ul').html('');
	$dialog.find('fieldset.mother .mailbook_holder ul').html('');
	$dialog.find('.notebook_holder ul').html('');
	$dialog.find('a[con_id]').attr('con_id', '');
	$dialog.find('.family_id').val('');
	$dialog.find('#parent_form_id').val('');
	$dialog.find('form[name="form_parent_data"] .this_form_modified').val(1);
	$dialog.find('form[name="form_parent_data"] .editable').val(1);
	$dialog.find('form[name="form_parent_data"] input[name="id"]').change();
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

function mergeParents(){
	var data = $('#parent_list_form').serialize();
	MS_jsonRequest('parents&merge', data, 'reloadParents()');
}

function deleteParents(){
	var data = $('#parent_list_form').serialize();
	var buttons = [{ 
		text: getLang('yes'), 
		click: function() { 
			$(this).dialog('close');
			MS_jsonRequest('parents&delete', data, 'reloadParents()');
		}
	},
	{
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('confirm', getLang('confirm'), getLang('cfm-delete'), 300, 150, buttons, true)
}

function reloadParents(){
	var params = $('#parent_list_form #parent_params').val();
	var module = {};
	module.name = 'parents';
	module.div ='#home_content';
	module.title = getLang('parent_list');
	module.data = 'list&'+params;
	loadModule(module);
}

function setParentAutocomplete(input){
	var source = 'index.php?module=parents&parent_autocomplete';
	$(input).autocomplete({
		source: source,	
		minLength: 1,
		select: function(event, ui) {
			var name = ui.item.father_name;
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
		return $( '<li></li>' )
			.data( "item.autocomplete", item )
			.append( "<a>" +  item.father_name+"</a>" )
			.appendTo( ul );
	};
	
	$(input).focus(function(){
		$(this).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');
	});
	
	$(input).blur(function(){
		setTimeout(function(){
			$(this).next('span').fadeOut().remove();
		}, 500)
	});
	$(input).keypress(function(){
		$(this).next('.ui-icon-arrowrefresh-1-w').fadeIn();
	});
}

function openParent($but){
	var parentCode = $but.attr('parentid');
	var module ={};
	module.name= 'parents';
	module.title = getLang('parents');
	module.data= 'id='+parentCode;
	module.type= 'GET';
	module.div = 'MS_dialog_parent-'+parentCode;
	module.callback = function(){
		iniParentModule('#'+module.div);
		iniParentDialog('#'+module.div)
	}

	var dialogOpt = {
		width: 1000,
		height: 600,
		modal:false,
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt);
}

function updateSocStat($inp){
	var $tab = $inp.parents('.ui-dialog-content').eq(0);	
	var $fatherFields = $tab.find('fieldset.father .father_data');
	var $motherFields = $tab.find('fieldset.mother .mother_data');
	if($inp.val() == '1'){
		$tab.find('.parental_right_tr').fadeOut();
		$tab.find('.parental_right_tr input').attr('checked', true);
		$fatherFields.show();
		$motherFields.show();
	} else if($inp.val() == '2'){
		$tab.find('.parental_right_tr').fadeIn();
		$fatherFields.show();
		$motherFields.show();
	} else if($inp.val() == '3'){
		$fatherFields.hide();
		$motherFields.show();
	} else if($inp.val() == '4'){
		$fatherFields.show();
		$motherFields.hide();
	} else if($inp.val() == '5'){
		$fatherFields.hide();
		$motherFields.hide();
	}
}