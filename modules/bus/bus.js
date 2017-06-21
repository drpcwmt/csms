// JavaScript Document

function newRouteFees($but){
	var groupId = $but.attr('group_id');
	var busmsId = $but.attr('busms_id');
	var smsId = $but.attr('sms_id');
	var module ={
		name: 'bus',
		data: 'new_fees&busms_id='+busmsId+'&sms_id='+smsId+'&group_id='+groupId,
		title: getLang('bus_fees'),
		div: 'MS_dialog-new_fees',
		
	}
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() { 
			if(validateForm('#MS_dialog-new_fees form')){
				var total = 0;
				$('#MS_dialog-new_fees form').find('input.payment_values').each(function(){
					if($(this).val() != ''){
						total = total + parseInt($(this).val());
					}
				});
				if(total == parseInt($('#MS_dialog-new_fees input[name="value"]').val())){
					var submitSave = {
						name: 'bus',
						param: 'new_fees&busms_id='+busmsId+'&sms_id='+smsId+'&save&group_id='+groupId,
						post: $('#MS_dialog-new_fees form').serialize(),
						callback: function(){
							var $tab = $but.parents('.ui-tabs-panel').eq(1);
							openBusGroup($tab.find('.list_menu li.ui-state-active'));
							$('#MS_dialog-new_fees').dialog('close');
						}
					}
					getModuleJson(submitSave);
				} else {
					MS_alert('<img src="assets/img/error.png"><h2>'+getLang('payment_dont_match')+'</h2>');
				}
			} else {return false;}

		}
	}, { 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:550,
		height:600,
		minim:false,
		modal: true,
		callback: function(){
			loadModuleJS('accounts');
			formatAccountCode($('#MS_dialog-new_fees'))
		}
	}
	openAjaxDialog(module, dialogOpt);	
}

function applyBusFeesToall($but){
	var $form = $but.parents('form');
	var $table = $form.find('.tableinput');
	var $first_inp = $table.find('input').eq(0);
	var first_inp_val = $first_inp.val();
	$table.find('input').each(function(){
		$(this).attr('checked', true);
	});
}

function deleteBusFees($but){
	var fees_id = $but.attr('fees_id');
	var smsId = $but.attr('sms_id');
	var deleteFees = {
		name: 'bus',
		param: 'del_fees&sms_id='+smsId,
		post: 'fees_id='+fees_id,
		callback: function(){
			var $tr = $but.parents('tr').eq(0);
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(deleteFees);
}

function openBusGroup($but){
	var groupId = $but.attr('itemid');
	var busmsId= $but.attr('busms_id');
	var smsId = $but.attr('sms_id');
	var module = {};
	module.name = 'bus';
	module.data = 'group_id='+groupId+'&busms_id='+busmsId+'&sms_id='+smsId;
	module.title = getLang('bus_fees');
	module.div = '#bus_resource_content';
	loadModule(module);
}
