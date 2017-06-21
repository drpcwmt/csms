// Balance

function startBalance(){	
	var module ={};
	module.name = 'balance';
	module.title = getLang('start_balance');
	module.div = $('#balance_module_body');
	module.data = 'start_bal';
	module.callback = function(){
		$('#balance_module_body input').focus(function(){
			$(this).select();
		});
	}
	loadModule(module);
}

function saveStartBalance(){
	var module = {
		name: 'balance',
		param: 'save_start_balance',
		post: $('#start_balance_form').serialize()
	}
	getModuleJson(module);
}


function openStartBal($but){
	var module ={};
	module.name = 'balance';
	module.title = getLang('start_balance');
	module.div = $('#start_bal_data_td');
	module.data = 'start_acc='+$but.attr('rel');
	loadModule(module);
}

function financialReport($but){
	var module ={};
	module.name = 'balance';
	module.title = getLang('financial_report');
	module.div = $('#balance_module_body');
	module.data = 'financialreport';
	loadModule(module);
}

function incomeReport($but){
	var module ={};
	module.name = 'balance';
	module.title = getLang('financial_report');
	module.div = $('#balance_module_body');
	module.data = 'incomesreport';
	loadModule(module);
}

function damageCalculator(){
	var module ={};
	module.name = 'balance';
	module.title = getLang('damages');
	module.div = $('#balance_module_body');
	module.data = 'damages';
	loadModule(module);
}

function openAccDamages($but){
	var module ={};
	module.name = 'balance';
	module.title = getLang('damages');
	module.div = $('#damages_data_td');
	module.data = 'damages&damages_acc='+$but.attr('rel');
	loadModule(module);
}


function saveDamages($btn){
	var submitSave = {
		name : 'balance',
		param: 'damages&savedamages',
		post : $('#damages_form').serialize(),
		callback: function(){
		}
	}
	getModuleJson(submitSave);
}

function createDamageTransaction(){
	var submitSave = {
		name : 'balance',
		param: 'damages&trans',
		post : '',
		callback: function(answer){
			loadModuleJS('settlements');
			var $btn = $('<button>').attr('trans_id', answer.id);
			openTrans($btn);
			
		}
	}
	getModuleJson(submitSave);
}