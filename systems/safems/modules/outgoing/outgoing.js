function openOutOthers($but){
	var module ={};
	module.name = 'outgoing';
	module.data = "others";
	module.title = getLang('incomes');
	module.div = '#outgoingMainDiv';
	module.callback = function(){
		loadModuleJS('accounts');
		formatAccountCode($('#add_others_form .account_code'));
		setAccDescAutocomplete('#add_others_form input[name="from_name"]');
	}
	loadModule(module);
}	

function submitOtherOutgoing($but){
	var $form = $but.parents('form');
	if(!validateForm($form)){
		return false;
	}
	var data = $form.serialize();
	var savePayment = {
		name: 'outgoing',
		param: 'others&save',
		post: data,
		async:false,
		callback: function(answer){
			dialogOpt = {
				buttons: [{ 
					text: getLang('print'), 
					click: function() { 
						printDialog($(this));
			
					}
				}, { 
					text: getLang('close'), 
					click: function() { 
						$(this).dialog('close');
					}
				}],
				width:600,
				height:400,
				minim:false,
				div: 'dialog_recete',
				title: 'recete'
			}
			openHtmlDialog(answer.recete, dialogOpt)
		}
	}
	getModuleJson(savePayment);
}

function openDeposit($but){
	var module ={};
	module.name = 'outgoing';
	module.data = "deposit";
	module.title = getLang('deposit');
	module.div = '#outgoingMainDiv';
	module.callback = function(){
	}
	loadModule(module);
}	

function openRefund($but){
	var smsId= $but.attr('sms_id');
	var tta ={};
	tta.name = 'outgoing';
	tta.data = "refund&sms_id="+smsId;
	tta.title = getLang('refund');
	tta.div = '#outgoingMainDiv';
	tta.callback = function(){
		loadModuleJS('students');
		setStudentAutocomplete('#refund_student_name', '1,3,0');
	}
	loadModule(tta);
}	

function submitSearchRefund($but){
	var $form = $but.parents('form');
	if(validateForm($form)){
		var stdId = $form.find('input[name="std_id"]').val();
		var ccId = $form.find('input[name="ccid"]').val();
		var tableRefund = {
			name: 'outgoing',
			data: 'refund&refund_table&sms_id='+ccId+'&'+$form.serialize(),
			title: getLang('students'),
			div: '#std_refundable_div',
			callback: function(){
			//	$("#refund_table_td button[action='submitRefund']").attr('sms_id', ccId);
			}
		}
		loadModule(tableRefund);
	}
}

function refundItem($btn){
	var mainCode = $btn.attr('main_code');
	var subCode = $btn.attr('sub_code');
	$form  = $btn.parents('form');
	var $tr = $btn.parents('tr').eq(0);
	var val = $tr.find('td').eq(1).text();
	var cur = $tr.find('td').eq(2).text();
	var note = getLang('refund')+': '+$tr.find('td').eq(3).text();
	
	var html = '<form class="ui-state-highlight"><input type="hidden" name="currency" value="'+cur+'" /><input type="hidden" name="note" value="'+note+'" /><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('amount')+': </label></td><td><div class="fault_input">'+val+'</div> '+cur+'</td></tr><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('refund')+': </label></td><td><input type="text" name="refund" class="required" value="0" /></td></tr><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('discount_val')+': </label></td><td><input type="text" name="discount" class="required" value="0" /></td></tr><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('date')+': </label></td><td><input type="text" name="date" class="required datepicker mask-date" value="0" /></td></tr></table></form>';
	var dialogOpt = {
		width:400,
		height:250,
		div:'MS_dialog_refund',
		title:getLang('refund'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if( validateForm($('#MS_dialog_refund form')) ){
					var $valField = $('#MS_dialog_refund form input[name="discount"]'); 
					if(parseInt($valField.val()) > parseInt(val)){
						$valField.addClass('ui-state-error');
						MS_alert('<h2><img src="assets/img/error.png" /> '+getLang('error')+'</h2>');	
						return false;
					} 
					refund = {
						name: 'outgoing',
						param: 'refund&save&sms_id='+$form.find('input[name="ccid"]').val(),
						post: $('#MS_dialog_refund form').serialize()+'&'+$form.serialize()+'&main_code='+mainCode+'&sub_code='+subCode+'&value='+val,
						async:false,
						callback: function(answer){
							if(answer.recete && answer.recete!=''){
								dialogOpt = {
									buttons: [{ 
										text: getLang('print'), 
										click: function() { 
											printDialog($(this));
								
										}
									}, { 
										text: getLang('close'), 
										click: function() { 
											$(this).dialog('close');
										}
									}],
									width:600,
									height:400,
									minim:false,
									div: 'dialog_recete',
									title: 'recete'
								}
								openHtmlDialog(answer.recete, dialogOpt);
							} else {
								MS_alert('<h2><img src="assets/img/error.png" /> '+getLang('error')+'</h2>');	
							}
							$('#MS_dialog_refund').dialog('close');
						}
					}
					getModuleJson(refund);
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


function refundService($btn){ // safems function
	var serviceId = $btn.attr('service_id');
	var exam = $btn.attr('exam');
	$form  = $btn.parents('form');
	var $tr = $btn.parents('tr').eq(0);
	var val = $tr.find('td').eq(1).text();
	var cur = $tr.find('td').eq(2).text();
	var note = getLang('refund')+': '+$tr.find('td').eq(3).text();
	
	var html = '<h3>'+note+'</h3><form class="ui-state-highlight"><input type="hidden" name="currency" value="'+cur+'" /><input type="hidden" name="exam" value="'+exam+'" /><input type="hidden" name="service_id" value="'+serviceId+'" /><input type="hidden" name="note" value="'+note+'" /><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('amount')+': </label></td><td><div class="fault_input">'+val+' '+cur+'</td><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('date')+': </label></td><td><input type="text" name="date" class="required datepicker mask-date" value="0" /></td></tr></table></form>';
	var dialogOpt = {
		width:400,
		height:250,
		div:'MS_dialog_refund',
		title:getLang('refund'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if( validateForm($('#MS_dialog_refund form')) ){
					var $valField = $('#MS_dialog_refund form input[name="discount"]'); 
					if(parseInt($valField.val()) > parseInt(val)){
						$valField.addClass('ui-state-error');
						MS_alert('<h2><img src="assets/img/error.png" /> '+getLang('error')+'</h2>');	
						return false;
					} 
					refund = {
						name: 'outgoing',
						param: 'refund_service&save&sms_id='+$form.find('input[name="ccid"]').val(),
						post: $('#MS_dialog_refund form').serialize()+'&'+$form.serialize()+'&service_id='+serviceId+'&exam='+exam+'&value='+(val-parseInt($('#MS_dialog_refund input[name="discount"]').val())),
						async:false,
						callback: function(answer){
							if(answer.recete && answer.recete!=''){
								dialogOpt = {
									buttons: [{ 
										text: getLang('print'), 
										click: function() { 
											printDialog($(this));
								
										}
									}, { 
										text: getLang('close'), 
										click: function() { 
											$(this).dialog('close');
										}
									}],
									width:600,
									height:400,
									minim:false,
									div: 'dialog_recete',
									title: 'recete'
								}
								var $tr = $btn.parents('tr').eq(0);
								$tr.fadeOut().remove();
								openHtmlDialog(answer.recete, dialogOpt);
							} else {
								MS_alert('<h2><img src="assets/img/error.png" /> '+getLang('error')+'</h2>');	
							}
							$('#MS_dialog_refund').dialog('close');
						}
					}
					getModuleJson(refund);
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
	initiateJquery();
	
}

/*function recalculateRefund($inp){
	var $form = $('#refund_table_td').find('form');
	total = new Array;
	$form.find(':checkbox').each(function(){
		var $tr = $(this).parents('tr').eq(0);
		var val = $tr.find('td').eq(2).html();
		var cur = $tr.find('td').eq(3).html();
		if(!(cur in total)){
			total[cur] = 0;
		}
		if($(this).is(':checked')){
			total[cur] += parseInt(val);
		}
	});
	for (var cur in total){
		if(total[cur]>0){
			$("#refund_total-"+cur).html(total[cur]+' '+cur);
		} else {
			$("#refund_total-"+cur).html('');
		}
	}
}

function refundFees($btn){
	var mainCode = $btn.attr('main_code');
	var subCode = $btn.attr('sub_code');
	$form  = $btn.parents('form');
	var $tr = $btn.parents('tr').eq(0);
	var val = $tr.find('td').eq(2).text();
	var cur = $tr.find('td').eq(3).text();
	
	var html = '<form><input type="hidden" name="currency" value="'+cur+'" /><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('amount')+': </label></td><td><div class="fault_input">'+val+' '+cur+'</td></tr><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('discount_val')+': </label></td><td><input type="text" name="discount" class="required" value="0" />'+cur+'</td></tr><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('date')+': </label></td><td><input type="text" name="date" class="required datepicker mask-date" value="0" /></td></tr></table></form>';
	var dialogOpt = {
		width:400,
		height:220,
		div:'MS_dialog_refund',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() {	
				if( validateForm($('#MS_dialog_refund form')) ){
					var $valField = $('#MS_dialog_refund form input[name="discount"]'); 
					if(parseInt($valField.val()) > parseInt(val)){
						$valField.addClass('ui-state-error');
						MS_alert('<h2><img src="assets/img/error.png" /> '+getLang('error')+'</h2>');	
						return false;
					} 				
					var saveRefund = {
						name: 'outgoing',
						param: 'refund&save&&sms_id='+$form.find('input[name="ccid"]').val(),
						post: $('#MS_dialog_refund form').serialize()+'&'+$form.serialize()+'&main_code='+mainCode+'&sub_code='+subCode+'&value='+(val-parseInt($('#MS_dialog_refund input[name="discount"]').val())),
						callback: function(answer){
							submitSearchRefund($('#refund_div button[action="submitSearchRefund"'));
							dialogOpt = {
								buttons: [{ 
									text: getLang('print'), 
									click: function() { 
										printDialog($(this));
							
									}
								}, { 
									text: getLang('close'), 
									click: function() { 
										$(this).dialog('close');
									}
								}],
								width:600,
								height:400,
								minim:false,
								div: 'dialog_recete',
								title: 'recete'
							}
							openHtmlDialog(answer.recete, dialogOpt)
						}
					}
				}
				getModuleJson(saveRefund);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
}*/

function submitDeposit($but){
	var $form = $but.parents('form');
	if(!validateForm($form)){
		return false;
	}
	var data = $form.serialize();
	var savePayment = {
		name: 'outgoing',
		param: 'deposit&save',
		sync:false,
		post: data,
		callback: function(answer){
			dialogOpt = {
				buttons: [{ 
					text: getLang('print'), 
					click: function() { 
						printDialog($(this));
			
					}
				}, { 
					text: getLang('close'), 
					click: function() { 
						$(this).dialog('close');
					}
				}],
				width:600,
				height:400,
				minim:false,
				div: 'dialog_recete',
				title: 'recete'
			}
			openHtmlDialog(answer.recete, dialogOpt)
		}
	}
	getModuleJson(savePayment);
	
}