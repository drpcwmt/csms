// students.js
function iniStudentModule(stdCode){
	// history
	var module = {};
	module.name = 'borrow';
	module.div = '#member_history';
	module.data = 'borrowlist&dialog&std_id='+stdCode;
	module.title = getLang('history');
	loadModuleToDiv(new Array(module), '')
}

function iniAdminModule(empCode){
	// history
	var module = {};
	module.name = 'borrow';
	module.div = '#member_history';
	module.data = 'borrowlist&dialog&emp_id='+empCode;
	module.title = getLang('history');
	loadModuleToDiv(new Array(module), '')
}


function openMemberInfos( code, type, sch){
	var module ={};
	module.name= 'members';
	module.title = getLang('members');
	module.div = 'MS_dialog_'+module.name;
	var buttons = [{ 
		text: getLang('print'), 
		click: function() { 
			print_pre('#member_data');
		}
	}, { 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	if(type == 'std'){
		module.data= 'stdid='+code;
		var callback  = 'iniStudentModule("'+sch+'-'+code+'")';
	} else if(type == 'emp'){
		module.data= 'empid='+code;
		var callback  = 'iniAdminModule("'+sch+'-'+code+'")';
	}
	
	if(sch && sch != ''){
		module.data += '&server='+sch;
	}
	createAjaxDialog(module, buttons, false, 880, 600, true, callback)
}

function openBorrowByCon(con, conId){
	var module ={};
	module.name= 'borrow';
	module.title = getLang('borrow');
	module.data= 'new_borrow&con='+con+'&con_id='+conId;
	module.div = 'MS_dialog_'+module.name;
	var buttons = [{
		text:getLang('save'),
		click: function(){
			submitBorrowForm("$('#MS_dialog_"+module.name+"').dialog('close')");
		}
	}, { 
		text: getLang('print'), 
		click: function() { 
			print_pre('#borrow_form');
		}
	},{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 820, 440, false, 'iniBorrowForm()')
}
