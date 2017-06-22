// JavaScript Document

function newIncome($btn){
	var module ={};
	module.name= 'othersincomes';
	module.title = getLang('others_incomes');
	module.data= 'newincome';
	module.div = 'new_others_incomes';
	var dialogOpt = {
		width:600,
		height:300,
		title:getLang('new'),
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
				if(validateForm('#new_others_incomes form')){
					var submitSave = {
						name : 'othersincomes',
						param: 'saveincome',
						post : $('#new_others_incomes form').serialize(),
						callback: function(answer){
							$('#otherincomes_list').append('<li income_id="'+answer.id+'"  class="hoverable clickable ui-stat-default ui-corner-all ui-state-active" action="openIncome"><text class="holder-income'+answer.id+'">'+answer.title+'</text></li>');
							var $newBtn = $('<button>').attr('income_id', answer.id);
							openIncome($newBtn);
							$('#new_others_incomes').dialog('close');
						}
					}
					getModuleJson(submitSave);
				}
			}
		}],
		callback: function(){
			loadModuleJS('accounts');
			formatAccountCode($('#new_others_incomes .account_code'));
			setAccDescAutocomplete('#new_others_incomes input[name="expenses_name"]','1');
			setAccDescAutocomplete('#new_others_incomes input[name="incomes_name"]', '1');
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function openIncome($btn){
	var incomeId =$btn.attr('income_id');
	var module ={};
	module.name = 'othersincomes';
	module.title = getLang('others_incomes');
	module.div = '#incomes_content';
	module.data = 'open_income='+incomeId;
	loadModule(module);
}

function addActivtyMemberPerStd($btn){
	var actId = $btn.attr('act_id');
	var module = {
		name: 'othersincomes',
		data: 'addmember&act_id='+actId,
		title: getLang('others_incomes'),
		div: 'MS_dialog-new_member',
	}
	
	var dialogOpt = {
		width:420,
		height:260,
		title:getLang('add_member'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var submitSave = {
					name : 'othersincomes',
					param: 'addmember&save',
					post : $('#MS_dialog-new_member form').serialize(),
					callback: function(answer){
						$scoop = $btn.parents('.ui-tabs-panel').eq(0);
						$scoop.find('table.tablesorter tbody').append(answer.html);
						$('#MS_dialog-new_member form input[name="name"').val('');
						$('#MS_dialog-new_member form input[name="std_id"').val('');
						//$("#MS_dialog-new_member form").dialog("close"); 
					}
				}
				getModuleJson(submitSave);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			var $select = $('#newMemberForm select[name="cc_id"]');
			loadModuleJS('students');
			setStudentAutocomplete('#newMemberForm input[name="name"]', '1', $select.val());
		}
	};
	
	openAjaxDialog(module, dialogOpt)
}


function payActivity($btn){
	var actId = $btn.attr('act_id');
	var stdId = $btn.attr('std_id');
	var ccId = $btn.attr('cc_id');
	var $tab =  $btn.parents('.ui-tabs-panel').eq(0);
	var module = {
		name: 'othersincomes',
		data: 'newpay&act_id='+actId+'&std_id='+stdId+'&cc_id='+ccId,
		title: $tab.find('h2').text(),
		div: 'MS_dialog-new_pay',
	}
	
	var dialogOpt = {
		width:420,
		height:220,
		title: $tab.find('h2').text(),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var submitSave = {
					name : 'othersincomes',
					param: 'newpay&save',
					post : $('#MS_dialog-new_pay form').serialize(),
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
						reloadMembersTable($tab, actId);
						 $('#MS_dialog-new_pay').dialog('close')
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

function refundActivity($btn){
	var actId = $btn.attr('act_id');
	var stdId = $btn.attr('std_id');
	var ccId = $btn.attr('cc_id');
	var $tab =  $btn.parents('.ui-tabs-panel').eq(0);
	var module = {
		name: 'othersincomes',
		data: 'newpay&act_id='+actId+'&std_id='+stdId+'&cc_id='+ccId,
		title: $tab.find('h2').text(),
		div: 'MS_dialog-new_pay',
	}
	
	var dialogOpt = {
		width:420,
		height:220,
		title: $tab.find('h2').text(),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var submitSave = {
					name : 'othersincomes',
					param: 'refundpay&save',
					post : $('#MS_dialog-new_pay form').serialize(),
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
						reloadMembersTable($tab, actId);
						 $('#MS_dialog-new_pay').dialog('close')
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

function reloadMembersTable($tab, actId){
	var module = {
		name: 'othersincomes',
		data: 'reload_members&act_id='+actId,
		title: getLang('others_incomes'),
		div: $tab.find('.member_table tbody')
	}
	loadModule(module);
}

function saveActivity($btn) { 
	var $form =$btn.parents('form');
	if(validateForm($form)){
		var submitSave = {
			name : 'othersincomes',
			param: 'saveincome',
			post : $form.serialize(),
			callback: function(answer){
			}
		}
		getModuleJson(submitSave);
	}
}

function removeMember($btn){
	var actId = $btn.attr('act_id');
	var stdId = $btn.attr('std_id');
	var ccId = $btn.attr('cc_id');
	var submitSave = {
		name : 'othersincomes',
		param: 'remove_member',
		post : 'act_id='+actId+'&std_id='+stdId+'&cc_id='+ccId,
		callback: function(answer){
			$tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(submitSave);
}
	
function changeMemberCC($select){
	var $form = $select.parents('form');
	var $sugInp = $form.find('input[name="name"]');
	if($sugInp && $sugInp.hasClass('ui-autocomplete-input')){		
			loadModuleJS('students');
			$('#newMemberForm input[name="name"]').attr('sms_id', $select.val());
			setStudentAutocomplete('#newMemberForm input[name="name"]', '1', $select.val());
	}
}


function syncActivitys(){
	var submitSave = {
		name : 'othersincomes',
		param: 'sync_activitys',
		post : '',
		callback: function(){
			var module = {
				name: 'othersincomes',
				div: '#module_othersincomes',
				data: '',
				title: getLang('othersincomes')
			}
			loadModule(module);
		}
	}
	getModuleJson(submitSave);
}