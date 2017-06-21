// JavaScript Document
function setAccDescAutocomplete(input){
	var source = 'index.php?module=accounts&autocomplete=description';
	$(input).autocomplete({
		source: source,	
		minLength: 2,
		select: function(event, ui) {
			var title = ui.item.title ? ui.item.title : '';
			$(input).val(title);
			var $tr = $(input).parents('tr').eq(0);
			var $accCode = $tr.find('.account_code');
			if($accCode.find('input.main_code').length > 0){
				$accCode.find('input.main_code').val(ui.item.main_code);
				$accCode.find('input.sub_code').val(ui.item.sub_code);
			} else {
				$form = $(input).parents('form');
				$form.find('input.main_code').val(ui.item.main_code);
				$form.find('input.sub_code').val(ui.item.sub_code);
			}
			return false;
		},	
		search: function(event, ui) {
			$(input).attr('term', '');	
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		if(item.error){
			MS_alert('<h3 class="title_white"><img src="assets/img/error.png" />'+item.error+'</h3>');
			return $( '<li class="ui-state-error ui-corner-all"></li>' )
				.data( "item.autocomplete", item )
				.append( '<a>' + item.error+"</a>" )
				.appendTo( ul );
			//return false;
		} else {
			var name = item.title ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( '<a>' + name+"</a>" )
				.appendTo( ul );
		}
	};
	
	$(input).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	
	$(input).blur(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeOut().remove();
	});
	$(input).keypress(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function formatAccountCode($div){
	$div.find('input.sub_code, input.main_code').each(function(){
		$(this).mask("99999");
		$(this).keyup(function(){
			var val = $(this).val();
			if(val > 0 && val.length == 5) {
				$(this).next('input').focus();
				if($(this).hasClass('sub_code')){
					var $tr = $(this).parents('tr').eq(0);
					var main = $tr.find('.main_code').val();
					var sub = $tr.find('.sub_code').val();
					if($tr.find('input.acc_title').length > 0){
						getAccountName(	main+sub, $tr.find('input.acc_title'));
					} else {
						var $form = $(this).parents('form');
						if($form.find('input.acc_title').length > 0){
							getAccountName(	main+sub, $form.find('input.acc_title'));
						}
					}
				}
			}
		});
	});
	
	$div.find('.cc').mask("9");
}

function getAccountName(acc, $input){
	var module = {
		name: 'accounts',
		param: 'getname',
		post: 'acc='+acc,
		callback: function(answer){
			$input.val(answer.title);
		}
	}
	getModuleJson(module);
}

function printTree(){
	var module = {
		name: 'accounts',
		param: 'printtree',
		post: '',
		callback: function(answer){
			var $div = $('<div></div>');
			$div.html(answer.html);
			printTag($div);
		}
	}
	getModuleJson(module);
}

function searchAccount($but){
	var module ={};
	module.name = 'accounts';
	module.title = getLang('accounts');
	module.div = 'search_dialog';
	module.data = 'searchform';
	module.callback = function(){
		formatAccountCode($('#search_dialog .account_code'));
		setAccDescAutocomplete('#search_dialog input[name="title"]');
	}
	var dialogOpt = {
		width:600,
		height:250,
		title:getLang('accounts'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				if(validateForm('#search_account_form')){
					var fullCode= $('#search_dialog input[name="acc_code_main"]').val()+$('#search_dialog input[name="acc_code_sub"]').val();
					var $btn = $('<button code="'+fullCode+'"></button>');
					openSubAcc($btn);
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
	
	openAjaxDialog(module, dialogOpt)
}

function submitTransTable($btn){
	var $form = $btn.parents('form');
	$tab = $btn.parents('.ui-tabs-panel').eq(0);
	var module = {
		name: 'accounts',
		data: 'transactions&'+$form.serialize(),
		title: getLang('transactions'),
		div	: $tab.find('.trans_list tbody')
	}
	loadModule(module);
	
}
	// Total Transactions
function openTotals(){
	var module ={};
	module.name = 'accounts';
	module.title = getLang('accounts');
	module.div = 'account_main_td';
	module.data = 'totals';
	loadModule(module);
}

	// Tree and accounts management
	
function openTree(){
	var module ={};
	module.name = 'accounts';
	module.title = getLang('accounts');
	module.div = '#account_main_td';
	module.data = 'tree';
	loadModule(module);
}

function newMain($but){
	var parent = $but.attr('rel');
	var module ={};
	module.name= 'accounts';
	module.title = getLang('new_account');
	module.data= 'newmain&parent='+parent;
	if($but.attr('level')!==false){
		module.data += '&level='+$but.attr('level');
	} 
	module.type= 'GET';
	module.div = 'new_main_account';
	var dialogOpt = {
		width:600,
		height:300,
		title:getLang('new_account'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		},{ 
			text: getLang('save'), 
			click: function() { 
				if(validateForm('#new_main_account form')){
					var submitSave = {
						name : 'accounts',
						param: 'savenewmain',
						post : $('#new_account_form').serialize(),
						callback: function(){
							$('#new_main_account form').dialog('close');
						}
					}
					getModuleJson(submitSave);
				}
			}
		}],
		callback: function(){
			//iniSettlements('#new_settlement_diff')
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function openMainAccount($but, Reload){
	var $accordionLI = $but.parents('h3.ui-accordion-header').eq(0);
	if(!Reload && $accordionLI.hasClass('ui-accordion-header-active')){
		return false;
	}
	$('#accounting_tree_div .tree_list h3').removeClass('current');
	$accordionLI.addClass('current');
	var module ={};
	module.name = 'accounts';
	module.title = getLang('accounts');
	module.div = $('#treeAccountDetails');
	module.data = 'openacc='+$but.attr('rel');
	loadModule(module);
}

function saveMainCode($but){
	var $form = $but.parents('form.account_infos');
	var submitSave = {
		name : 'accounts',
		param: 'savecode',
		post : $form.serialize(),
		muted: false,
		callback: function(){
			
		}
	}
	getModuleJson(submitSave);
	
}

function deleteMainCode($but){
	var $form = $but.parents('form.account_infos');
	var submitSave = {
		name : 'accounts',
		param: 'del_acc',
		post : $form.serialize(),
		muted: false,
		callback: function(){
			$('#accounting_tree_div .current').fadeOut().remove();
		//	$('#accounting_tree_div .tree_list').accordion('refresh');
			$('#treeAccountDetails').html('');
		}
	}
	getModuleJson(submitSave);
}


function newAccount($but){
	var parent = $but.attr('rel');
	var module ={};
	module.name= 'accounts';
	module.title = getLang('new_account');
	module.data= 'newacc&parent='+parent;
	module.type= 'GET';
	module.div = 'new_account';
	var dialogOpt = {
		width:650,
		height:500,
		title:getLang('new_account'),
		maxim:true,
		minim:true,
		cache : false,
		buttons: [{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		},{ 
			text: getLang('save'), 
			click: function() { 
				if(validateForm('#new_account_form')){
					var submitSave = {
						name : 'accounts',
						param: 'savenewacc',
						post : $('#new_account form').serialize(),
						callback: function(answer){
							var $table = $but.parents('table').eq(0);
							$("#sub_acc_list .result").append(answer.tr);
							$('#new_account').dialog('close');
						}
					}
					getModuleJson(submitSave);
				}
			}
		}],
		callback: function(){
			//iniSettlements('#new_settlement_diff')
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function checkAccCC($inp){
	var $fieldSet = $inp.parents('fieldset').eq(0);
	var $li = $inp.parents('li').eq(0);
	var $val = $li.find('input[type="text"]');
	var $select = $fieldSet.find('select');
	if($select.val() != '0' && $inp.is(':checked')){
		$val.fadeIn();
	} else {
		$val.fadeOut();
	}
}

function openSubAcc($but){
	var code = $but.attr('code');
	var module ={};
	module.name= 'accounts';
	module.title = getLang('new_account');
	module.data= 'openacc='+code;
	module.div = 'account-'+code;
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('Accounts'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var submitSave = {
					name : 'accounts',
					param: 'savecode',
					post : $('#account-'+code+' form.account_infos').serialize(),
					muted: false,
					callback: function(){
						$('#account-'+code).dialog('close');
					}
				}
				getModuleJson(submitSave);
				
				
			}
		},{
			text:getLang('delete'),
			click: function(){
				var $form = $('#account-'+code+' form.account_infos');
				var submitdelete = {
					name : 'accounts',
					param: 'del_acc',
					post : $form.serialize(),
					muted: false,
					callback: function(answer){
						if(answer && answer.error == ''){
							var $opener = $('#treeAccountDetails').find('button[code="'+code+'"]');
							var $tr = $opener.parents('tr').eq(0);
							$tr.fadeOut().remove();
							$('#account-'+code).dialog('close');
						}
					}
				}
				getModuleJson(submitdelete);
				
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			var $title = $('#account-'+code).find('input[name="title"]');
			$('#account-'+code).dialog('option','title',getLang('account')+': '+ $title.val());
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function closeDay($btn){
	var module ={};
	module.name= 'accounts';
	module.title = getLang('new_account');
	module.data= 'closeday&date='+$('#closeday_date').val();
	module.div = 'close_day';
	var dialogOpt = {
		width:1000,
		height:600,
		title:getLang('close_day'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var closeDay = {
					name : 'accounts',
					param: 'closeday&save&date='+$('#closeday_date').val(),
					post : $('#close_day_form').serialize(), //$('#close_day form').serialize(),
					muted: false,
					callback: function(){
						$('#close_day').dialog('close');
					}
				}
				getModuleJson(closeDay);
				
				
			}
		},{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
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