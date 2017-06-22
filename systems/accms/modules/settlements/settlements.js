// JavaScript Document
function iniSettlements(settlDiv){
	if($(settlDiv).find('input[name="approve"]:checked').val() != '0' ){
		$(settlDiv).find('input:not([type=radio], [type=hidden]) ').attr('disabled', 'disabled');
		$(settlDiv).find('select').attr('disabled', 'disabled');
		$(settlDiv).find('textarea').attr('disabled', 'disabled');
		$(settlDiv).find('button').attr('disabled', 'disabled').hide();
	} else {
		var $div = $(settlDiv).find('.tableinput');
		/*$div.find('tr:last input').focus(function(){
			$tr = $(this).parents('tr').eq(0);
			$tr.clone().insertBefore($tr); 
			$tr.prev('tr').find('input:first').focus();
			iniSettlements(settlDiv);
		});*/
		
		loadModuleJS('accounts');
		
		$div.find('tbody tr').not(':last').each(function(index, element) {
			formatAccountCode( $(this).find('.account_code'))
			setAccDescAutocomplete($(this).find('input.acc_title'));
		   
		});
	
		$div.find('.tableinput input:first').focus();
	}
}

function showRate($select){
	var $tr = $select.parents('tr').eq(0);
	var $rate = $tr.find('input[name="rate"], input[name="rate[]"]');
	var cur = $select.val();
	var module = {
		name: 'currency',
		param: 'convert=1&from='+cur+'&to=EGP',
		post: '',
		callback: function(answer){
			$rate.val(answer.result);
			$rate.fadeIn();
		}
	}
	getModuleJson(module);
}

function removeTrans($btn){
	var $tr = $btn.parents('tr').eq(0);
	$tr.fadeOut().remove();
}

function addTrans($btn){
	var $table = $btn.parents('.tableinput').eq(0);
	var $tr = $btn.parents('tr').eq(0);
	var $lastTr = $tr.prev('tr');
	$lastTr.clone().insertBefore($tr);
	$newTr = $tr.prev('tr').eq(0); 
	$newTr.find('input').val('');
	$newTr.find('button').removeClass('MS_formed');
	$newTr.find('input:first').focus();
	formatAccountCode( $newTr.find('.account_code'))
	setAccDescAutocomplete($newTr.find('input.acc_title'));
	iniButtonsRoles();
}

function newSettlDiff(){
	var module ={};
	module.name= 'settlements';
	module.title = getLang('new_settlement');
	module.data= 'new';
	module.type= 'GET';
	module.div = 'new_settlement_diff';
	var dialogOpt = {
		width:900,
		height:500,
		title:getLang('new_settlement'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				submitTrans($('#new_settlement_diff'));
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
		}],
		callback: function(){
			iniSettlements('#new_settlement_diff')
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function newExchange(){
	var module ={};
	module.name= 'settlements';
	module.title = getLang('new_settlement');
	module.data= 'new_exchange';
	module.type= 'GET';
	module.div = 'new_settlement_exchange';
	var dialogOpt = {
		width:900,
		height:500,
		title:getLang('exchange'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				submitExchange($('#new_settlement_exchange'));
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
		}],
		callback: function(){
			iniExchange('#new_settlement_exchange')
		}
	}
	openAjaxDialog(module, dialogOpt)
}

function iniExchange(settlDiv){
	if($(settlDiv).find('input[name="approve"]:checked').val() != '0' ){
		$(settlDiv).find('input:not([type=radio], [type=hidden]) ').attr('disabled', 'disabled');
		$(settlDiv).find('select').attr('disabled', 'disabled');
		$(settlDiv).find('textarea').attr('disabled', 'disabled');
		$(settlDiv).find('button').attr('disabled', 'disabled').hide();
	} else {
		var $div = $(settlDiv).find('.tableinput');
		loadModuleJS('accounts');
		$div.find('tbody tr').each(function(index, element) {
			formatAccountCode( $(this).find('.account_code'))
			setAccDescAutocomplete($(this).find('input.acc_title'));
		   
		});
		$div.find('.tableinput input:first').focus();
	}
}

function openTrans($btn){
	var transId = $btn.attr('trans_id');
	var module ={};
	module.name= 'settlements';
	module.title = getLang('new_settlement');
	module.data= 'trans_id='+transId;
	module.type= 'GET';
	module.div = 'transaction-'+transId;
	var dialogOpt = {
		width:900,
		height:500,
		title:getLang('settlements'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if($('#transaction-'+transId+' select[name="currency[]"]').length > 1){
					submitExchange($('#transaction-'+transId), $btn);
				} else {
					submitTrans($('#transaction-'+transId), $btn);
				}
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
		}],
		callback: function(){
			if($('#transaction-'+transId+' select[name="currency[]"]').length > 1){
				iniExchange('#transaction-'+transId);
			} else {
				iniSettlements('#transaction-'+transId);
			}
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

Number.prototype.round = function(p) {
  p = p || 10;
  return parseFloat( this.toFixed(p) );
};

function submitTrans($div, $btn){
	var $table = $div.find('.tableinput tbody');	
	var valid = true;
	var debit = 0;
	var credit = 0;
	
	$table.find('tr').each(function(){
		$(this).find('input.debit').each(function(i,n){
			var val = $(n).val();
			if(val != ''){
				debit += Math.round(val * 100) / 100;
			}
		})
		$(this).find('input.credit').each(function(i,n){
			var val = $(n).val();
			if(val != ''){
				credit += Math.round(val * 100) / 100 ; 
				/*//parseInt($(n).val(),10);*/
			}
		})
	});	
	if(debit==0 || debit != credit){
		var dif = debit - credit;
		if(dif > 0){
			var dif_txt = getLang('debit')+': '+ Math.round(dif * 100) / 100;
		} else {
			var dif_txt = getLang('credit')+': '+ + Math.round(dif * -100) / 100;
		}
		MS_alert('<h3><img src="assets/img/error.png"/>'+getLang('error')+': '+getLang('not_equal')+' '+ dif_txt+'</h3>');
		return false;
	}

	$table.find('tr').each(function(){	
		if(($(this).find('input').eq(0).val() != '' || $(this).find('input').eq(1).val() != '') &&
			($(this).find('input').eq(2).val() == '' || $(this).find('input').eq(3).val() == '' ||$(this).find('input').eq(4).val() == '')){
			$(this).find('.account_code input').addClass('ui-state-error');
			MS_alert('<h3><img src="assets/img/error.png"/>'+getLang('error')+': '+getLang('missing')+'</h3>');
			valid = false;
			return false;
		} else {
			$(this).find('.account_code input').removeClass('ui-state-error');
		}
	});

	if(valid){
		if(validateForm( $div.find('form'))){
			var module = {
				name: 'settlements',
				param: 'save_trans',
				post: $div.find('form').serialize(),
				callback: function(answer){
					$new = $('<button>').attr('trans_id', answer.id);
					openTrans($new)
					if($btn && $btn.parents('#transactions_list').length){
						var $tr = $btn.parents('tr').eq(0);
						$tr.replaceWith(answer.tr);	
						iniButtonsRoles();
					}
					$div.dialog('close');
				}
			}
			getModuleJson(module);
		}
	} else {
		MS_alert('<h3><img src="assets/img/error.png"/>'+getLang('error')+'</h3>');
	}
}

function submitExchange($div, $btn){
	var $table = $div.find('.tableinput tbody');	
	totalDebit = 0;
	totalCredit = 0;	
	valid = true;
	
	$table.find('tr').each(function() {
		var $debitField = $(this).find('input.debit');
		var $creditField = $(this).find('input.credit');
		var rateField = $(this).find('input.rate').val() != '' ? parseInt($(this).find('input.rate').val()):1;
        totalDebit +=  $debitField.val()!='' ? (parseInt($debitField.val()) * rateField) : 0;
        totalCredit +=  $creditField.val()!='' ? (parseInt($creditField.val()) * rateField) : 0;
		if($(this).find('select').val() != 'EGP' && $(this).find('input.rate').val() == '' && ($debitField.val()!='' || $creditField.val()!= '')){
			$(this).find('input.rate').addClass('ui-state-error');
			valid = false;
		}
		if($(this).find('input.acc_title').val() == '' && ($debitField.val()!='' || $creditField.val()!= '')){
			$(this).find('input.acc_title').addClass('ui-state-error');
			valid = false;
		}
   });
   if(valid == false){	
		MS_alert('<img src="assets/img/warning.png" />'+getLang('fill_req_fields'));
		return false;   
   }
	if(totalDebit > 0 && totalDebit==totalCredit){
		var module = {
			name: 'settlements',
			param: 'save_exchange',
			post: $div.find('form').serialize(),
			callback: function(answer){
				if($btn && $btn.parents('#transactions_list').length){
					var $tr = $btn.parents('tr').eq(0);
					$tr.replaceWith(answer.tr);	
					iniButtonsRoles();
				}
				$div.dialog('close');
			}
		}
		getModuleJson(module);
	} else {
		MS_alert('<h3><img src="assets/img/error.png"/>'+': '+getLang('not_equal')+' '+ (totalDebit-totalCredit)+'</h3>');
	}
}

function searchTransById(){
	var html = '<form><table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('id')+': </label></td><td><input id="search_id_inp"  type="text" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_search_code',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				var transId = $('#MS_dialog_search_code #search_id_inp').val();
				if(transId != ''){
					$but = $('<button>').attr('trans_id', transId);
					openTrans($but)
					$('#MS_dialog_search_code').dialog('close');
				} else {
					$('#MS_dialog_search_code').append('<div class="ui-state-error ui-corner-all" style="margin-top:15px">( '+getLang('error_not_item_found')+'</div>');
				}
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

function transDailyList($btn){
	var module = {
		name:'settlements',
		data:'list',
		div: '#trans_main_td'
	}
	loadModule(module);
		
}

function changeListDate($btn){
	var $form = $btn.parents('form');
	var module = {
		name:'settlements',
		data:'list='+$form.find('input[name="date"]').val(),
		div: '#trans_main_td'
	}
	loadModule(module);
}

function searchTransAdv(){
	var module ={};
	module.name= 'settlements';
	module.title = getLang('search');
	module.data= 'search_form';
	module.type= 'GET';
	module.div = 'search_form-dialog';
	var dialogOpt = {
		width:650,
		height:350,
		title:getLang('search'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				var module = {
					name:'settlements',
					data:'search&'+$('#search_form-dialog form').serialize(),
					div: '#trans_main_td',
					title: getLang('transactions'),
					callback: function(){
						$('#search_form-dialog').dialog('close');
					}
				}
				loadModule(module);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			loadModuleJS('accounts');
			formatAccountCode( $('#search_form-dialog .account_code'))
			setAccDescAutocomplete($('#search_form-dialog input.title'));
			 
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}