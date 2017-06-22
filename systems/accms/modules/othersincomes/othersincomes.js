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
			setAccDescAutocomplete('#new_others_incomes input[name="expenses_name"]');
			setAccDescAutocomplete('#new_others_incomes input[name="incomes_name"]');
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