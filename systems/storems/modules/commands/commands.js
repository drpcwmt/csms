// Commands Js


function initCommand(){	
	var $form = $('form[name="command_form"]');
	setClientsAutocomplete($form.find('input[name="client_name"]'));
	
	$form.find('input[name="name[]"]').focus(function(){
		$(this).select();
		setProductsAutocomplete($(this));
	})
	
	$form.find('input[name="name[]"]').change(function(){
		if($(this).attr('term') && $(this).attr('term') !=''){
			var $tr = $(this).parents('tr').eq(0);
			$tr.find('input[name="item_id[]"]').val($(this).attr('term'));
			getItemData($(this));
		}
	});
	
	$form.find('input[name="quantity[]"], input[name="price[]"]').focus(function(){
		$(this).select();
	});
	
	$form.find('input[name="client_name"]').focus(function(){
		$(this).val('');
		$form.find('input[name="client_id"]').val('');
		$(this).autocomplete('search');
	});

	// quantity Update
	$form.find('input[name="quantity[]"]').change(function(){
		recalculateCommand($form);
	});
	
	// Price Update
	$form.find('input[name="price[]"]').change(function(){
		recalculateCommand($form);
	});
	
	
	// Shortcuts
	$form.bind('keydown', function(e){
		if(e.which == 113) {
			saveCommand($form);
			return false;
		} else if(e.which == 114) {
			searchCommand();
			return false;
		} else if(e.which == 121) {
			var $higherLayer = returnHigherLayer();
			addPayment($higherLayer.find('form[name="command_form"]'));
			return false;
		} else if(e.which == 13) {
			setItemFocus($form);
			return false;
		}
	});
	
	setItemFocus($form);
}

function setItemFocus($form){
	var $table = $form.find('.commands_item');
	var $newItem = $table.find("input[value='']:visible:first");
	$newItem.focus();
}

function recalculateCommand($form){
	var total = 0;
	var $tbody = $form.find('.commands_item tbody');
	
	$tbody.find('tr').each(function(){
		$tr = $(this);
		if($tr.find('input[name="item_id[]"]').val() != ""){
			var quantity = $tr.find('input[name="quantity[]"]').val();
			var price = $tr.find('input[name="price[]"]').val();
			var itemTotal = parseInt(quantity) * parseInt(price);
			$tr.find('input[name="total[]"]').val(itemTotal);
			total = total + itemTotal;
		}
	})
	
	$form.find('#command_total').val(total);
	
}

function nextCommandField(){
//	setItemFocus();
	return false;
}

function addNewItem(inp){
	if(inp instanceof jQuery){
		$inp = inp;
	} else {
		var $inp = $(inp);
	}
	var $tr = $inp.parents('tr').eq(0);
	$tr.find('button[action="removeCommandItem"]').show();
	var $tbody = $inp.parents('tbody').eq(0);
	var $form = $inp.parents('form').eq(0);

	if($tr.index() +1  == $tbody.find('tr').length){
		$tbody.append( '<tr>'+$tr.html()+'</tr>');
		var $nextTr = $tr.next('tr');
		$nextTr.find('input').removeClass('MS_formed_update');
		$nextTr.find('button').removeClass('MS_formed');
		
			// Name				
		setProductsAutocomplete($nextTr.find('input[name="name[]"]'));
			
			// quantity Update
		$nextTr.find('input[name="quantity[]"]').change(function(){
			recalculateCommand($form);
		});
		
			// Price Update
		$nextTr.find('input[name="price[]"]').change(function(){
			recalculateCommand($form);
		});
			
			// focus
		$nextTr.find('input[name="quantity[]"], input[name="price[]"]').focus(function(){
			$(this).select();
		});

			// Keyboard shortcuts
		$nextTr.bind('keydown', function(e){
			if(e.which == 113) {
				saveCommand($form)
				return false;
			} else if(e.which == 114) {
				searchCommand();
				return false;
			} else if(e.which == 121) {
				addPayment($inp);
				return false;
			} else if(e.which == 13) {
				setItemFocus($form);
				return false;
			}
		});
		
	} 
	initiateJquery();
}

function getItemData($inp){
	var $form = $inp.parents('form').eq(0);
	var $tr = $inp.parents('tr').eq(0);
	var id = $tr.find('input[name="item_id[]"]').val();
	var module = {
		name: 'products',
		param: 'data&prod_id='+id,
		post: '',
		muted: true,
		async:true,
		callback: function(answer){
			$tr.find('input[name="name[]"]').val(configFile.uilang=='ar' ? answer.name_rtl : answer.name_ltr);
			$tr.find('input[name="quantity[]"]').val(configFile.selling_item=='item' ? (answer.price ? 1 : 0) : answer.contener);
			$tr.find('input[name="price[]"]').val(answer.price);
			$tr.find('input[name="total[]"]').val(configFile.selling_item=='item' ? answer.price : (answer.price * answer.contener));
			$tr.find('input[name="quantity[]"]').focus().select();
			$tr.find('button[action="openProduct"]').attr('prodid', answer.id);
			$tr.find('button[action="openProduct"]').show();
			barcode.play();
			recalculateCommand($form);
		}
	}
	getModuleJson(module);
}

function removeCommandItem($but){
	var $form = $but.parents('form').eq(0);
	var $tr = $but.parents('tr').eq(0);
	$tr.fadeOut().remove();
	recalculateCommand($form);
	
}

function saveCommand($form){
	var module = {
		name: 'commands',
		param: 'save',
		post: $form.serialize()+'&tot='+$form.find('input[name="tot"]').val(),
		muted: false,
		async:false,
		callback: function(answer){
			$form.find('input[name="id"]').val(answer.id);
			$form.find('#id_label').val(answer.id);
		}
	}
	getModuleJson(module);
}

// Search Commands

function searchCommand(){
	var module = {
		name: 'commands',
		title: getLang('search'),
		data: 'search_form',
		type: 'GET',
		div: 'search_form_dialog',
		callback: function(){ 
			setClientsAutocomplete($('#command_search_form input[name="client_name"]'));
		}
	}
		
	var dialogOpt = {
		width:600,
		height:500,
		title:getLang('search'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('open'), 
			click: function() { 
				openCommand(comId);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			$('#command_search_form input[name="com_id"]').focus();
			
			$('#command_search_form').bind('keydown', function(e){
				if(e.which == 13) {	
					if($('#command_search_form input[name="com_id"]').val() != ''){
						openCommand($('#command_search_form input[name="com_id"]').val());
						$('#search_form_dialog').dialog('close');
					}
					return false;
				}
			});
		}
	}		
	openAjaxDialog(module, dialogOpt);
}

function getCommandsByClient(){
	if($('#command_search_form input[name="client_id"]').val() != ''){
		var module = {
			name: 'commands',
			params: 'search',
			data: 'client_id='+$('#command_search_form input[name="client_id"]').val(),
			title: getLang('commands'),
			div: '#command_search_form .command_search_resuls',
			type: 'POST'
		}
		loadModule(module);
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/warning.png" />'+getLang('error-undefined_client')+':</h2>');
		return false;
	}
}

function openSearchCommand($but){
	var comId = $but.attr('comid');
	openCommand(comId);
	$('#search_form_dialog').dialog('close');
	
}

function openCommand(comId){
	var module = {
		name: 'commands',
		title: getLang('orders'),
		data: 'com_id='+comId,
		type: 'GET',
		div: 'dialog-commands_'+comId,
		callback: function(){ 
			initCommand();
		}
	}
		
	var dialogOpt = {
		width:800,
		height:600,
		title:getLang('sell_order'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('open'), 
			click: function() { 
				saveCommand(comId);
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
