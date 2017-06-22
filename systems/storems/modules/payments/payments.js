// JavaScript Document
// PaymentsV
function addPayment($form){
	var transId = $form.find('input[name="id"]').val();
	var to = $form.find('input[name="to"]').val();
	var to_id = $form.find('input[name="to_id"]').val();
	var from = $form.find('input[name="from"]').val();
	var from_id = $form.find('input[name="from_id"]').val();
	var amount = $form.find('input[name="total"]').val();
	var paid = $form.find('input[name="paid"]').val();
	
	if(parseInt(amount) > 0){

		var module = {
			name: 'payments',
			title: getLang('payments'),
			data: 'newpayment',
			type: 'GET',
			div: 'payment_new',
			callback: function(){ 
				$('#payments_form input[name="amount"]').val(parseInt(amount) - parseInt(paid));
				$('#payments_form input[name="trans_id"]').val(transId);
				$('#payments_form input[name="to"]').val(from);
				$('#payments_form input[name="to_id"]').val(from_id);
				$('#payments_form input[name="from"]').val(to);
				$('#payments_form input[name="from_id"]').val(to_id);
				$('#payments_form input[name="amount"]').focus(function(){
					$(this).select();
				});
			}
		}
		
		var dialogOpt = {
			width:400,
			height:300,
			title:getLang('payments'),
			maxim:false,
			minim:false,
			buttons: [{ 
				text: getLang('pay_recive'), 
				click: function() { 
					savePayment($form,2);
				}
			},{ 
				text: getLang('pay'), 
				click: function() { 
					savePayment($form, 3);
				}
			},{ 
				text: getLang('cancel'), 
				click: function() { 
					$(this).dialog('close');
				}
			}],
			callback: function(){
				$('#payments_form').bind('keydown', function(e){
					if(e.which == 13 || e.which == 10) {	
						var status;
						var  delivery_date = $form.find('input[name="delivery_date"]').val();
						DD = $.datepicker.parseDate('dd/mm/yy', delivery_date);
						if(new Date() < DD ){
							status = 4;
						} else {
							if($form.find('input[name="shipping"]:checked')){
								status = 2;
							} else {
								staus = 3;
							}
						}

						savePayment($form, status); 
						$('#payment_new').dialog('close');
						return false;
					}
				});
			}
		}
		
		openAjaxDialog(module, dialogOpt);
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/warning.png" />'+getLang('error-total_command')+'</h2>');
		return false;
	}
}

function updateTransaction($form, status){
	var $payForm = $('#payments_form');
	var paid = parseInt($form.find('input[name="paid"]').val());
	var amount = parseInt($payForm.find('input[name="amount"]').val());
	var total = $form.find('input[name="total"]');
	
	$form.find('input[name="paid"]').val(paid+amount);
	if($form.find('input[name="status"]').val()!=status){
		$form.find('input[name="status"]').val(status).trigger('change');
	}
	if(status == '2'){
		$form.find('div.status').html(getLang('command_shipping'));
	} else if(status == '3'){
		$form.find('div.status').html(getLang('command_delivered'));
	}else if(status == '4'){
		$form.find('div.status').html(getLang('command_reserved'));
	}
}

function savePayment($form, status){
	var $payForm = $('#payments_form');
	if (parseInt($payForm.find('input[name="amount"]').val()) > 0 ){
		var module = {
			name: 'payments',
			param: 'savepayment',
			post: $payForm.serialize(),
			muted: false,
			callback: function(){
				updateTransaction($form, status)
				$('#payment_new').dialog('close');
			}
		}
		getModuleJson(module);
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/warning.png" />'+getLang('error-total_paid')+'</h2>');
		return false;
	}
}

function tooglePaymentOptions($select){
	if($select.val() == 'transfer' || $select.val() == 'others' ){
		$('#paymentOptionTr').show();
	} else {
		$('#paymentOptionTr').val('').hide();
	}
}