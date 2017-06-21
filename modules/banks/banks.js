// JavaScript Document
function openBanks(){
	var module ={};
	module.name = 'banks';
	module.title = getLang('banks');
	module.div = '#account_main_td';
	module.data = '';
	loadModule(module);
		
}

function openBank($btn){
	var module ={};
	module.name = 'banks';
	module.title = getLang('banks');
	module.div = $('#BanksDetails');
	module.data = 'code='+$btn.attr('bank_code');
	loadModule(module);
}