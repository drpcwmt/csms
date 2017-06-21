// JavaScript Document
function openSchoolFees($but){
	var school_id = $but.attr('school_id');
	var module ={};
	module.name = 'fees';
	module.data = "sms_id="+school_id;
	module.title = getLang('school_fees');
	module.div = '#ingoingMainDiv';
	module.callback = function(){
		loadModuleJS('students');
		$('#ingoing_student_name').attr('sms_id', school_id);
		setStudentAutocomplete('#ingoing_student_name', '1,3,0');
	}
	loadModule(module);
}


function LoadPayments($but){
	var con = $but.attr('con');
	var conId = $but.attr('con_id');
	var smsId = $but.attr('sms_id');
		var module ={
		name: 'fees',
		data: 'loadpayments&sms_id='+smsId+'&con='+con+'&con_id='+conId,
		title: getLang('school_fees'),
		div: 'MS_dialog-payment-'+con+'-'+conId
	}
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() { 
			var submitSave = {
				name: 'fees',
				param: 'savepayments&sms_id='+smsId+'&con='+con+'&con_id='+conId,
				post: $('#MS_dialog-payment-'+con+'-'+conId+' form').serialize(),
				callback: function(){
					$('#MS_dialog-payment-'+con+'-'+conId).dialog('close');
					LoadPayments($but);
				}
			}
			getModuleJson(submitSave);

		}
	}, { 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:800,
		height:400,
		minim:true,
	}
	openAjaxDialog(module, dialogOpt);	
}

function LoadDates($but){
	var con = $but.attr('con');
	var conId = $but.attr('con_id');
	var smsId = $but.attr('sms_id');
		var module ={
		name: 'fees',
		data: 'loaddates&sms_id='+smsId+'&con='+con+'&con_id='+conId,
		title: getLang('times'),
		div: 'MS_dialog-dates-'+con+'-'+conId
	}
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() { 
			var submitSave = {
				name: 'fees',
				param: 'savedates&sms_id='+smsId+'&con='+con+'&con_id='+conId,
				post: $('#MS_dialog-dates-'+con+'-'+conId+' form').serialize(),
				callback: function(){
					$('#MS_dialog-dates-'+con+'-'+conId).dialog('close');
				}
			}
			getModuleJson(submitSave);

		}
	}, { 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:440,
		minim:true,
	}
	openAjaxDialog(module, dialogOpt);	
}


function savePaymentsDates($btn){
	var smsId = $btn.attr('sms_id') ? $btn.attr('sms_id') : '';
	var con = $btn.attr('con') ? $btn.attr('con') : '';
	var conId = $btn.attr('con_id') ? $btn.attr('con_id') : '';
	var $form = $btn.parents('form');
	var submitSave = {
		name: 'fees',
		param: 'savedates&sms_id='+smsId+'&con='+con+'&con_id='+conId,
		post: $form.serialize()
	}
	getModuleJson(submitSave);
}

function addDates($btn){
	var $form = $btn.parents('form');
	var $table = $form.find('table.tableinput');
	var $tbody = $table.find('tbody');
	var html = '<table cellspacing="0"><tbody><tr><td><label class="label reverse_align ui-widget-header ui-corner-left" style="width:120px; float:left">'+getLang('count')+': </label></td><td><input id="dates_add_count" type="text" </td></tr></tbody></table>';
	dialogOpt = {
			buttons: [{ 
			text: getLang('ok'), 
			click: function() { 
				if($('#dates_add_count').val() != ''){
					var tr = '<tr><input type="hidden" name="id[]"><td><button class="ui-state-default hoverable circle_button" module="fees" onclick="$(this).parents(\'tr\').eq(0).fadeOut().remove()"><span class="ui-icon ui-icon-close"></span></button></td><td><input type="text" name="title[]" class="input_double ui-corner-right"></td><td align="center"><input type="text" name="from[]"  class="mask-date datepicker"></td><td align="center"><input type="text" name="limit[]" class="mask-date datepicker"></td></tr>';
					//var $tr = $tbody.find('tr:last').clone();
					//$tr.find('input').each(function(){ $(this).val('').removeClass('hasDatepicker masked').attr('id', '')});
					//$tr.find('button').attr('date_id', '');
					$tbody.html('');
					for(var x=0; x< parseInt($('#dates_add_count').val()); x++){
						$tbody.append(tr);
					}
					$form.removeClass('MS_formed');
					initiateJquery();
				}
				$(this).dialog('close');
			}
		}, { 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		div: 'add_dates_dialog',
		width:400,
		height:150,
		minim:false
	}
	openHtmlDialog(html, dialogOpt)
}

function deletePaymentDate($btn){
	
	if($btn.attr("date_id") && $btn.attr("date_id") != ''){
		var smsId = $btn.attr('sms_id');
		var dateId = $btn.attr('date_id');
		var deleteDate = {
			name: 'fees',
			param: 'deletedates&sms_id='+smsId,
			post: 'date_id='+dateId,
			callback: function(){
				var $tr = $btn.parents('tr').eq(0);
				$tr.fadeOut().remove();	
			}
		}
		getModuleJson(deleteDate);
	} else {
		var $tr = $btn.parents("tr").eq(0);
		$tr.fadeOut().remove();
	}
}

function newFees($but){
	var con = $but.attr('con');
	var conId = $but.attr('con_id');
	var smsId = $but.attr('sms_id');
	var stdId = $but.attr('std_id');
	var $form = $but.parents('form');
	if(con=='profil' && conId=='new'){
		if(validateForm($form)){
			var submitSave = {
				name: 'fees',
				param: 'profils&sms_id='+smsId+'&save&new&'+(stdId != '' ? 'std_id='+stdId : ''),
				post: $('#MS_dialog-new_profil form').serialize(),
				callback: function(answer){
					var $profilId = $form.find('input[name="profil_id"]');
					$profilId.val(answer.profil_id);
					conId=answer.profil_id;
				}
			}
			getModuleJson(submitSave);
		} else {
			return false;
		}
	}
	var module ={
		name: 'fees',
		data: 'new_fees&sms_id='+smsId+'&con='+con+'&con_id='+conId,
		title: getLang('school_fees'),
		div: 'MS_dialog-new_fees',
		
	}
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		modal:true,
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
						name: 'fees',
						param: 'new_fees&sms_id='+smsId+'&save&con='+con+'&con_id='+conId,
						post: $('#MS_dialog-new_fees form').serialize(),
						callback: function(answer){
							if(con == 'profil'){
								$form.find('table.tableinput tbody').append(answer.tr);
								initiateJquery()
							} else {
								var $scope = $but.parents('.scope').eq(0);
								$scope.find('.list_menu li.ui-state-active').click();
							}
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
		callback: function(){
			loadModuleJS('accounts');
			formatAccountCode($('#MS_dialog-new_fees'))
		}
	}
	openAjaxDialog(module, dialogOpt);	
}

function applyFeesToall($but){
	var $form = $but.parents('form');
	var $table = $form.find('.tableinput');
	var $first_inp = $table.find('input').eq(0);
	var first_inp_val = $first_inp.val();
	$table.find('input').each(function(){
		$(this).attr('checked', true);
	});
}


function saveFees($but){ //levels
	var con = $but.attr('con');
	var conId = $but.attr('con_id');
	var smsId = $but.attr('sms_id');
	var $form = $but.parents('form');
	var submitSave = {
		name: 'fees',
		param: 'save_fees&sms_id='+smsId+'&con='+con+'&con_id='+conId,
		post: $form.serialize(),
		callback: function(){
			
		}
	}
	getModuleJson(submitSave);
}

function deleteFees($but){
	var fees_id = $but.attr('fees_id');
	var smsId = $but.attr('sms_id');
	var deleteFees = {
		name: 'fees',
		param: 'sms_id='+smsId+'&del_fees',
		post: 'fees_id='+fees_id,
		callback: function(){
			var $tr = $but.parents('tr').eq(0);
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(deleteFees);
}

function openLevelInfos($but){
	var levelId = $but.attr('itemid');
	var smsId= $but.attr('smsid');
	var module = {};
	module.name = 'fees';
	module.data = 'con=level&con_id='+levelId+'&sms_id='+smsId;
	module.title = getLang('school_fees');
	module.div = '#resource_content';
	loadModule(module);
}

function browseLevel($but){
	var levelId = $but.attr('itemid');
	var smsId= $but.attr('smsid');
	var module = {};
	module.name = 'fees';
	module.data = 'browse&con=level&con_id='+levelId+'&sms_id='+smsId;
	module.title = getLang('school_fees');
	module.div = '#level_content';
	loadModule(module);
}

function calcFees($btn) {
	var sms_id = $btn.attr('sms_id');
	var std_id = $btn.attr('std_id');
	
	var module = {
		name: 'fees',
		param: 'calcFees&sms_id=' + sms_id + '&std_id=' + std_id,
		title: getLang('student'),
		callback: function(answer){
			$tr = $btn.parents('tr').eq(0);
			$tr.find('td.total').html('<b>'+answer.total+'</b>');
			$tr.find('td.paid').html('<b>'+answer.paid+'</b>');	
			$tr.find('td.diff').html('<b>'+(parseInt(answer.total) - parseInt(answer.paid))+'</b>');
			if($btn.parents('#insert_tab').length > 0){
				var $tab = $btn.parents('#insert_tab')
				var $form = $tab.find('form.student_search');
				var $submitBtn = $form.find('button[action="submitSearchStudentFees"]');
				submitSearchStudentFees($submitBtn);
			}
		}
	}
	
	getModuleJson(module);
}

	// Payments
function submitSearchStudentFees($but){
	var $form = $but.parents('form.student_search');
	var std_id = $form.find('input[name="std_id"]').val();
	var year = $form.find('select[name="year"]').val();
	$('#student_layout_td').html('');
	if(!std_id|| std_id==''){
		MS_alert('<h3>'+getLang('cant_find_std')+'</h3>');
		return false;
	} else {
		$('#student_layout_td').html('');
		var smsId= $but.attr('sms_id');
		var module = {};
		module.name = 'fees';
		module.data = 'con=student&con_id='+std_id+'&sms_id='+smsId+'&year='+year;
		module.title = getLang('school_fees');
		module.div = '#student_layout_td';
		loadModule(module);
		
		var payOptions = {
			name: 'fees',
			param: 'new_payment&options&sms_id='+smsId+'&std_id='+std_id+'&year='+year,
			post: '',
			callback: function(answer){
				$form.find('select[name="rel"]').html(answer.fees);
				$form.find('select[name="rel"]').combobox('destroy');
				$form.find('select[name="rel"]').combobox();
				$form.find('select[name="dates"]').html(answer.dates);
				$form.find('select[name="dates"]').combobox('destroy');
				$form.find('select[name="dates"]').combobox();
			}
		}
		getModuleJson(payOptions);
	}
}

function submitNewPayment($but){
	var $form = $but.parents('form.student_search');
	var std_id = $form.find('input[name="std_id"]').val();
	if(!std_id || std_id==''){
		MS_alert('<h3>'+getLang('cant_find_std')+'</h3>');
		return false;
	}
	if($form.find('select[name="rel"] option').length == 0){
		submitSearchStudentFees($but)
		//return false;
	}
	if(!validateForm($form)){
		return false;
	}
	var data = $form.serialize();
	$form.find('input[name="value"]').val('');
	var smsId= $but.attr('sms_id');
	var savePayment = {
		name: 'fees',
		param: 'sms_id='+smsId+'&new_payment&save',
		post: data,
		async:false,
		callback: function(answer){
			if(answer.recete.length>0 && answer.recete!=''){
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
					div: 'recete_dialog',
					width:600,
					height:400,
					minim:false
				}
				openHtmlDialog(answer.recete, dialogOpt)
				submitSearchStudentFees($but);
			} else {
				MS_alert('<h2><img src="assets/img/error.png" />'+getLang('error')+'</h2>');
			}
		}
	}
	getModuleJson(savePayment);
}

function openProfil($but){
	var $form = $but.parents('form');
	var $profilId = $form.find('select');
	if( $profilId.val() == '0'){
		MS_alert('<h2>'+getLang('error_select_profil')+'</h2>');
		return false;
	} else {
		var smsId = $but.attr('sms_id');
		var module ={
			name: 'fees',
			data: 'sms_id='+smsId+'&profils&profil_id='+$profilId.val(),
			title: getLang('school_fees'),
			div: 'MS_dialog-new_profil',
			callback: function(){
				loadModuleJS('accounts');
				formatAccountCode($form)
			}
		}
		dialogOpt = {
			buttons: [{ 
			text: getLang('save'), 
			modal:true,
			click: function() { 
				if(validateForm('#MS_dialog-new_profil form')){
					var submitProfil = {
						name: 'fees',
						param: 'profils&sms_id='+smsId+'&save',
						post: $('#MS_dialog-new_profil form').serialize(),
						callback: function(){
							$('#MS_dialog-new_profil').dialog('close');
						}
					}
					getModuleJson(submitProfil);
				} else {return false;}
	
			}
		}, { 
			text: getLang('delete'), 
			click: function() { 
				var deleteProfil = {
					name: 'fees',
					param: 'profils&sms_id='+smsId+'&delete',
					post: $('#MS_dialog-new_profil form').serialize(),
					callback: function(){
						$('#MS_dialog-new_profil').dialog('close');
					}
				}
				getModuleJson(deleteProfil);
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
			
		}
		openAjaxDialog(module, dialogOpt);	
	}
}

function newProfil($but){
	var smsId = $but.attr('sms_id');
	var stdId = $but.attr('std_id');
	var module ={
		name: 'fees',
		data: 'sms_id='+smsId+'&profils&new&std_id='+stdId,
		title: getLang('school_fees'),
		div: 'MS_dialog-new_profil',
	}
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		modal:true,
		click: function() { 
			if(validateForm('#MS_dialog-new_profil form')){
				var submitSave = {
					name: 'fees',
					param: 'profils&sms_id='+smsId+'&save&new&std_id='+stdId,
					post: $('#MS_dialog-new_profil form').serialize(),
					callback: function(answer){
						var $div = $but.parents('.ui-state-highlight').eq(0);
						var $select = $div.find('#profil_select');
						var $opt = $('<option>').attr({value:answer.profil_id, selected:'selected'}).html(answer.title).appendTo($select);
						$select.combobox('destroy');
						$select.removeClass('MS_formed');
						iniCombobox()
						$('#MS_dialog-new_profil').dialog('close');
						//submitSearchStudent($but);
					}
				}
				getModuleJson(submitSave);
			} else {return false;}

		}
	}, { 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:650,
		height:600,
		minim:false
	}
	openAjaxDialog(module, dialogOpt);	
	
}


function toogleDatesTable($label){
	var $form = $label.parents('form');
	var $dateTable = $form.find('table.dates_table');
	var $radio = $('#'+$label.attr('for'));
	if($radio.val() == '1'){
		$dateTable.fadeIn();
	} else {
		$dateTable.fadeOut();
	}
}


function saveStdProfil($but){
	var $form = $but.parents('form');
	var $profilId = $form.find('select');
	if( $profilId.val() == ''){
		MS_alert('<h2>'+getLang('error_select_profil')+'</h2>');
		return false;
	} else {
		var smsId = $but.attr('sms_id');
		var stdId = $but.attr('std_id');
		var submitSave ={
			name: 'fees',
			param: 'profils&sms_id='+smsId+'&savestd',
			post: 'std_id='+stdId+'&profil_id='+$profilId.val(),
			title: getLang('school_fees'),
			callback: function(){
				if($but.parents('#insert_tab').length > 0){
					var $tab = $but.parents('#insert_tab')
					var $form = $tab.find('form.student_search');
					var $submitBtn = $form.find('button[action="submitSearchStudentFees"]');
					submitSearchStudentFees($submitBtn);
				}
			}
		}
		getModuleJson(submitSave);
	}
}

function updateStdProfil($select){
	var stdId = $select.attr('rel');
	var profilId = $select.val();
	var smsId = $select.attr('sms_id');
	var submitSave ={
		name: 'fees',
		param: 'profils&sms_id='+smsId+'&savestd',
		post: 'std_id='+stdId+'&profil_id='+profilId,
		title: getLang('school_fees'),
		callback:function(){
			var $tab = $select.parents('.ui-tabs-panel');
			var $form = $select.parents('form');
			var classId = $form.attr('class_id');
			var module= {
				name:'fees',
				data: 'browse&con=class&con_id='+classId+'&sms_id='+smsId,
				title : getLang('school_fees'),
				callback: function(answer){
					var tabId = $tab.attr('id');
					$tab.replaceWith(answer);
					//if(tabId){
				}
			}
		}
	}
	getModuleJson(submitSave);
}


function uploadFeesSheet(cc){
	var file ='bank_sheet-'+ new Date()+'.csv';
	var dir = 'attachs/tmp/';	
	var callback = function(){
		var module = {
			name: 'fees',
			dat: 'import='+file,
			div: '#import_data',
			title: getLang('import')	
		}
		loadModule(module);
	}
	loadModuleJS('upload');
	uploadFile(dir, file, true, callback);	
}

function openLateList($btn){
	var levelId = $btn.attr('level_id');
	var module = {
		name : 'fees',
		data: 'late_list'+(levelId!= '' ? '&level_id='+levelId : ''),
		title: getLang('school_fees'),
		div : '#home_content'
	}
	loadModule(module);
}

function openBankSheet($btn){
	var smsId = $btn.attr('sms_id');
	var module ={
		name: 'fees',
		data: 'sms_id='+smsId+'&bank_sheet',
		title: getLang('school_fees'),
		div: 'MS_dialog-bank_sheet',
	}
	dialogOpt = {
		buttons: [{ 
			text: getLang('export'), 
			modal:true,
			click: function() { 
				var $button = $('<button>');
				$button.attr('rel', '#MS_dialog-bank_sheet table.tablesorter');
				exportTable($button)
			}
		},{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($('#MS_dialog-bank_sheet'));
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		width:550,
		height:600,
		minim:false
	}
	openAjaxDialog(module, dialogOpt);	
}

function filterIncomes($select){
	var $div = $select.parents('.std_income_div');
	var $tbody = $div.find('table.result tbody');
	var AccCode = $select.val();
	$tbody.find('tr').each(function(){
		if(AccCode == ' ' || $(this).hasClass(AccCode)){
			$(this).show();
		} else {
			$(this).hide();
		}
	});
}

function newNotes($btn){
var con = $but.attr('con');
	var conId = $but.attr('con_id');
	var smsId = $but.attr('sms_id');
		var module ={
		name: 'fees',
		data: 'loaddates&sms_id='+smsId+'&con='+con+'&con_id='+conId,
		title: getLang('times'),
		div: 'MS_dialog-dates-'+con+'-'+conId
	}
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() { 
			var submitSave = {
				name: 'fees',
				param: 'savedates&sms_id='+smsId+'&con='+con+'&con_id='+conId,
				post: $('#MS_dialog-dates-'+con+'-'+conId+' form').serialize(),
				callback: function(){
					$('#MS_dialog-dates-'+con+'-'+conId).dialog('close');
				}
			}
			getModuleJson(submitSave);

		}
	}, { 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:440,
		minim:true,
	}
	openAjaxDialog(module, dialogOpt);	
}


function savePaymentsDates($btn){
	var smsId = $btn.attr('sms_id') ? $btn.attr('sms_id') : '';
	var con = $btn.attr('con') ? $btn.attr('con') : '';
	var conId = $btn.attr('con_id') ? $btn.attr('con_id') : '';
	var $form = $btn.parents('form');
	var submitSave = {
		name: 'fees',
		param: 'savedates&sms_id='+smsId+'&con='+con+'&con_id='+conId,
		post: $form.serialize()
	}
	getModuleJson(submitSave);
}

function newFeesNotes($btn){
	var smsId = $btn.attr('sms_id');
	var stdId = $btn.attr('std_id');
	var html = '<form><input type="hidden" name="std_id" value="'+stdId+'" /><input type="hidden" name="sms_id" value="'+smsId+'" /><fieldset><legend>'+getLang('notes')+'</legend><textarea name="notes"></textarea></fieldset></form>';
	dialogOpt = {
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var submitSave = {
					name: 'fees',
					param: 'notes&save&sms_id='+smsId,
					post: $('#add_std_note form').serialize(),
					callback: function(){
						$('#student_notes_div').append('<fieldset class="ui-state-highlight">'+$('#add_std_note textarea').val()+'</fieldset>');
						$('#add_std_note').dialog('close');
					}
				}
				getModuleJson(submitSave);
				
			}
		}, { 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		div: 'add_std_note',
		title: $btn.attr('rel'),
		width:400,
		height:220,
		minim:false
	}
	openHtmlDialog(html, dialogOpt)
}

function deleteFeesNote($btn){
	var smsId = $btn.attr('sms_id');
	var submitDel = {
		name: 'fees',
		param: 'notes&delete&sms_id='+smsId,
		post: 'id='+$btn.attr('note_id'),
		callback: function(){
			var $fieldset = $btn.parents('fieldset').eq(0);
			$fieldset.fadeOut().remove();
		}
	}
	getModuleJson(submitDel);
}

function openBooksFeesByLevel($btn){
	var leveId = $btn.attr('level_id');
	var smsId = $btn.attr('sms_id');
	var module = {};
	module.name = 'fees';
	module.data = 'book_fees&level_id='+leveId+'&sms_id='+smsId;
	module.title = getLang('books');
	module.div = '#books_resource_content';
	loadModule(module);
}


function openRouteGroupFees($btn){
	var groupId = $btn.attr('itemid');
	var smsId = $btn.attr('sms_id');
	var module = {};
	module.name = 'fees';
	module.data = 'bus_fees&group_id='+groupId+'&sms_id='+smsId;
	module.title = getLang('bus');
	module.div = '#bus_resource_content';
	loadModule(module);
}

function searchLateList($but){
	var $form =$but.parents('form');
	var module = {
		name : 'fees',
		data: 'late_list&'+$form.serialize(),
		title: getLang('school_fees'),
		div : $form.find('table.tablesorter tbody')
	}
	loadModule(module);
}

function payReservation($btn){
	var stdId = $btn.attr('std_id');
	var smsId = $btn.attr('sms_id');
		
}

function updateStdServiceFees($inp){
	var stdId =$inp.attr('std_id');
	var exam =$inp.attr('exam');
	var serviceId = $inp.attr('service_id');
	var field = $inp.attr('name');
	var value=$inp.val();
	var module = {
		name : 'fees',
		param: 'update_std_ser_fees',
		post: 'std_id='+stdId+'&service_id='+serviceId+'&exam='+exam+'&field='+field+'&value='+value,
		title: getLang('school_fees'),
	}
	getModuleJson(module);
}

function paySessionFees($btn){
	var fees = $btn.attr('fees') ? $btn.attr('fees') : '';
	var stdId =$btn.attr('std_id');
	var exam =$btn.attr('exam');
	var serviceId = $btn.attr('service_id') ? $btn.attr('service_id') : '';
	var group = $btn.attr('group') ? $btn.attr('group') : '';
	var smsId = $btn.attr('sms_id');
	var $tr = $btn.parents('tr').eq(0);
	var Paid = $btn.attr('paid');
	
	var module = {
		name: 'fees',
		data: 'pay_form&type='+fees+'&sms_id='+smsId+'&std_id='+stdId+'&exam='+exam+'&service_id='+serviceId+'&group='+group+'&paid='+Paid,
		title: $tr.find('td').eq(0).text(),
		div: 'MS_dialog-new_pay_form',
	}
	
	var dialogOpt = {
		width:420,
		height:300,
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				
				var submitSave = {
					name : 'fees',
					param: 'pay_form&save&sms_id='+smsId,
					post : $('#MS_dialog-new_pay_form form').serialize(),
					callback: function(answer){
						if(answer.recete){
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
						}
						reloadRegistrationTable($btn);
						 $('#MS_dialog-new_pay_form').dialog('close')
					}
				}
				getModuleJson(submitSave);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	
	openAjaxDialog(module, dialogOpt)
}



function removeServiceIG($btn){
	var stdId = $btn.attr('std_id');
	var smsId = $btn.attr('sms_id');
	var exam = $btn.attr('exam');
	var module = {
		name : 'services',
		param: 'ig&remove_std_service',
		post: 'std_id='+stdId+'&exam='+exam+'&sms_id='+smsId+($btn.attr('service_id') ? '&service_id='+$btn.attr('service_id') : ''),
		title: getLang('materials'),
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(module);
}

function refundServicesFees($btn){ // SMS function
	var stdId = $btn.attr('std_id');
	var smsId = $btn.attr('sms_id');
	var exam = $btn.attr('exam');
	var module = {
		name : 'fees',
		data: 'refund_std_service&std_id='+stdId+'&exam='+exam+'&sms_id='+smsId+'&service_id='+$btn.attr('service_id'),
		title: getLang('refund'),
		div: 'new_refund_form'
	}
	var dialogOpt = {
		width:420,
		height:300,
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('refund'), 
			click: function() { 
				
				var submitSave = {
					name : 'fees',
					param: 'refund_std_service&save&sms_id='+smsId,
					post : $('#new_refund_form form').serialize(),
					callback: function(){
						var $tr = $btn.parents('tr').eq(0);
						$tr.fadeOut().remove();
						$('#new_refund_form').dialog('close');
					}
				}
				getModuleJson(submitSave);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	
	openAjaxDialog(module, dialogOpt)
}

function reloadRegistrationTable($btn){
	var $form = $but.parents('form.student_search');
	var sms_id = $form.find('input[name="cc_id"]').val();
	var std_id = $form.find('input[name="std_id"]').val();
	var year = $form.find('input[name="year"]').val();
	var module = {
		name: 'fees',
		data: 'sms_id='+sms_id+'&std_id='+std_id+'year='+year,
		div: $btn.parents('.ui-tab-panel').eq(0)
	}
	loadModule(module);
}

function newSpliting($btn){
	var sms_id = $btn.attr('sms_id');
	var std_id = $btn.attr('std_id');
	var module = {
		name: 'fees',
		data: 'splitting&new&sms_id='+sms_id+'&std_id='+std_id,
		div: 'new_split-dialog',
		title: getLang('split')
	}
	var dialogOpt = {
		width:420,
		height:250,
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 		
				var submitSave = {
					name : 'fees',
					param: 'splitting&save&sms_id='+sms_id,
					post : $('#new_split-dialog form').serialize(),
					callback: function(answer){
						var $div = $btn.parents('.scoop').eq(0);
						$div.find('table').append(answer.html);
						$('#new_split-dialog').dialog('close');
					}
				}
				getModuleJson(submitSave);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	
	openAjaxDialog(module, dialogOpt)
}

function removeSplit($btn){
	var smsId =$btn.attr('sms_id');
	var splitId =$btn.attr('split_id');
	var module = {
		name : 'fees',
		param: 'splitting&remove&sms_id='+smsId,
		post: 'split_id='+splitId,
		title: getLang('split'),
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();	
		}
	}
	getModuleJson(module);
}

function paySplit($btn){
	var splitId =$btn.attr('split_id');
	var smsId =$btn.attr('sms_id');
	
	var module = {
		name: 'fees',
		data: 'splitting&pay&sms_id='+smsId+'&split_id='+splitId,
		title: $tr.find('td').eq(0).text(),
		div: 'MS_dialog-pay_split_form',
	}
	
	var dialogOpt = {
		width:420,
		height:300,
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var submitSave = {
					name : 'fees',
					param: 'splitting&pay&save&sms_id='+smsId,
					post : $('#MS_dialog-pay_split_form form').serialize(),
					callback: function(answer){
						if(answer.recete){
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
						}
						//reloadRegistrationTable($btn);
						 $('#MS_dialog-pay_split_form').dialog('close')
					}
				}
				getModuleJson(submitSave);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	
	openAjaxDialog(module, dialogOpt)
}

