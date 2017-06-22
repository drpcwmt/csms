// Buys Js

function initBuy($form){
	setKeyboardShortcuts('f2', function($focus){saveFocusedBuy($focus)});
	$form.find('input[name="status"]').change(function(){
		var buyId = $form.find('input[name="id"]').val();
		saveBuy($form);
	});
}

function newBuy($but){
	var dialogId = 'dialog-buys_new';
	var dialogForm = '#'+dialogId+' form[name="transaction_form"]';

	var module = {
		name: 'buys',
		title: getLang('buys'),
		type: 'GET',
		data: 'newbuy&'+ ($but.attr('supid') ? 'sup_id='+$but.attr('supid') : '')+'&'+ ($but.attr('prodid') ? 'prod_id='+$but.attr('prodid') : ''),
		div: dialogId,
		callback: function(){ 
			initTransactionForm($(dialogForm));
			initBuy($(dialogForm));
		}
	}
		
	var dialogOpt = {
		width:800,
		height:600,
		title:getLang('buy_order'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveBuy($(dialogForm));
			}
		},{ 
			text: getLang('pay'), 
			click: function() { 
				saveBuy($(dialogForm));
				addPayment($(dialogForm));
			}
		},{ 
			text: getLang('reset'), 
			click: function() { 
				resetTranslations($(dialogForm)); ///
			}
		},{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($('#'+dialogId));
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

function openBuy(buyId){
	var dialogId = 'dialog-buys_'+buyId
	var dialogForm = '#'+dialogId+' form[name="transaction_form"]'
	var module = {
		name: 'buys',
		title: getLang('buys'),
		data: 'buy_id='+buyId,
		type: 'GET',
		div: dialogId,
		callback: function(){ 
			initTransactionForm($(dialogForm));
			initBuy($(dialogForm));
		}
	}
		
	var dialogOpt = {
		width:800,
		height:600,
		title:getLang('buy_order'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveBuy($(dialogForm));
			}
		},{ 
			text: getLang('pay'), 
			click: function() { 
				saveBuy($(dialogForm));
				addPayment($(dialogForm));
			}
		},{ 
			text: getLang('delete'), 
			click: function() { 
				deleteBuys($(dialogForm)); ///
			}
		},{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($('#'+dialogId));
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			
		}
	}		
	openAjaxDialog(module, dialogOpt);
}


function saveBuy($form){
	if(validateForm($form)){
		if($form.find('input[name="id"]').val() == 'new'){
			$form.find('input[name="id"]').val('')
		}
		if($form.find('input[name="total"]').val()!='0'){
			var module = {
				name: 'buys',
				param: 'save',
				post: $form.serialize()+'&total='+$form.find('input[name="total"]').val(),
				muted: false,
				async:false,
				callback: function(answer){
					$form.find('input[name="id"]').val(answer.id);
					$form.find('#id_label').val(answer.id);
				}
			}
			getModuleJson(module);
		} else {
			MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error-total_command')+'</h2>');
			return false
		}
	} else return false;
}

function saveFocusedBuy($focus){
	var $form = $focus.parents('form').eq(0);
	if($form){
		saveBuy($form);
	}
}

function saveBuyBut($but){
	var buyId =$but.attr('buyid');
	var $form = $('#buy_div-'+buyId+' form[name="transaction_form"]');
	saveBuy($form);
}

function openBuysBut($but){
	var buyId =$but.attr('buyid');
	openBuy(buyId);
}

// Search Buys
function searchBuys(){
	var module = {
		name: 'buys',
		title: getLang('search'),
		data: 'search_form',
		type: 'GET',
		div: 'search_buys_form_dialog',
		callback: function(){ 
			setSuppliersAutocomplete($('#buy_search_form input[name="supplier_name"]'));
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
				openBuy(buyId);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			$('#buy_search_form input[name="com_id"]').focus();
			
			$('#buy_search_form').bind('keydown', function(e){
				if(e.which == 13) {	
					if($('#buy_search_form input[name="_id"]').val() != ''){
						openBuy($('#buy_search_form input[name="buy_id"]').val());
						$('#search_buys_form_dialog').dialog('close');
					}
					return false;
				}
			});
		}
	}		
	openAjaxDialog(module, dialogOpt);
}

function getBuysBySupplier(){
	if($('#buy_search_form input[name="sup_id"]').val() != ''){
		var module = {
			name: 'buys',
			params: 'search',
			data: 'sup_id='+$('#buy_search_form input[name="sup_id"]').val(),
			title: getLang('buys'),
			div: '#buy_search_form .buys_search_resuls',
			type: 'POST'
		}
		loadModule(module);
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/warning.png" />'+getLang('error-undefined_client')+':</h2>');
		return false;
	}
}

function openSearchBuys($but){
	var buyId = $but.attr('buyid');
	openBuy(buyId);
	$('#search_buys_form_dialog').dialog('close');
	
}

function deleteBuys($form){
	var buyId = $form.find('input[name="id"]');
	if(!buyId || buyId=='' || buyId=='new'){
		resetTranslations($form);
	} else {
		var html = '<div class="ui-corner-all ui-state-highlight"><h3>'+getLang('cfm_delete_buy')+'</h3></div>';
		var dialogOpt = {
			width:200,
			height:150,
			div:'cfm_delete_buy_dialog',
			title:getLang('new_cat'),
			buttons: [{ 
				text: getLang('delete'), 
				click: function() { 
					var module = {
						name: 'buys',
						param: 'deletebuy',
						post: 'buy_id='+buyId,
						callback: function(answer){
							$('tr.buy_tr-'+buyId).fadeOut().remove();
							$('#cfm_delete_buy_dialog').dialog('close');
						}
						}
						getModuleJson(module);
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
}

function resetTranslations($form){
//	alert($(form).html())
	$form.find('button[action="removeTransactionItem"]').hide();
	$form.find('button[action="openProduct"]').hide();
	$form.find('input[name="total"]').val('0');
	$form.find('.items_list tbody tr').not('.new_command_tr').fadeOut().remove();
	$form.find('.items_list input').val('');	
}