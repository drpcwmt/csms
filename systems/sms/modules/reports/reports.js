// JavaScript Document
function changeQuitedLevel($select){
	var $form =$select.parents('form');
	var module ={};
	module.name= 'reports';
	module.title = getLang('quit_list');
	module.data= 'req=quit&'+$form.serialize();
	module.type= 'GET';
	module.div = '#MS_dialog_quit_list';
	loadModule(module)
}

function openReservationReport($select){
	var $form =$select.parents('form');
	var module ={};
	module.name= 'reports';
	module.title = getLang('suspension_lists');
	module.data= 'req=suspend&'+$form.serialize();
	module.type= 'GET';
	module.div = '#MS_dialog_suspend_list';
	loadModule(module)
}

function changeRedoubleLevel($select){
	var $form =$select.parents('form');
	var module ={};
	module.name= 'reports';
	module.title = getLang('redouble_report');
	module.data= 'redoubling&'+$form.serialize();
	module.type= 'GET';
	module.div = '#MS_dialog_suspend_list';
	loadModule(module)
}

function changeWaitingLevel($select){
	var $form =$select.parents('form');
	var module ={};
	module.name= 'reports';
	module.title = getLang('waiting_list');
	module.data= 'waiting&'+$form.serialize();
	module.type= 'GET';
	module.div = '#MS_dialog_suspend_list';
	loadModule(module)
}

function openSchoolStatic($but){
	if($but.val()){
		var etabId = '&etab_id=' + $but.val();
	} else if($but.attr('etabid')){
		var etabId = '&etab_id=' + $but.attr('etabid');
	} else {
		var etabId = '';
	}
	var module ={};
	module.name= 'reports';
	module.title = getLang('school_statics');
	module.data= 'statics'+etabId;
	module.type= 'GET';
	module.cache-false;
	module.div = 'MS_dialog_school_static';
	var dialogOpt = {
		width:900,
		height: 600,
		modal:false,
		maxim:true,
		minim:true,
		cache:false,
		modal:false,
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
		}]
	}
	openAjaxDialog(module, dialogOpt)
}


function openRegistrationReport($but){
	var module ={};
	module.name= 'reports';
	module.title = getLang('reg_report');
	module.data= 'reg_report';
	module.type= 'GET';
	module.cache-false;
	module.div = 'MS_dialog_reg_report';
	var dialogOpt = {
		width:900,
		height: 600,
		modal:false,
		maxim:true,
		minim:true,
		cache:false,
		modal:false,
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
		}]
	}
	openAjaxDialog(module, dialogOpt)

}

function toogleStaticElement(elmnt){
	if($('#statics_table .'+elmnt+':first').css('display') != 'none'){
		$('#statics_table .'+elmnt).hide();	
	} else {
		$('#statics_table .'+elmnt).show();	
	}
}

function schoolBalance(){
	var module ={};
	module.name = 'reports';
	module.data = "balance";
	module.title = getLang('school_balance');
	module.div = 'MS_dialog-balance';
	var dialogOpt = {
		width:900,
		height: 600,
		maxim:true,
		minim:true,
		modal:false,
		buttons: [{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		},{ 
			text: getLang('detailed'), 
			click: function() { 
				$('#MS_dialog-balance .class_tr').show(500);
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function openQuitList($but){
	var module ={};
	module.name= 'reports';
	module.title = getLang('quit_list');
	module.data= 'req=quit&reason='+$but.attr('reason');
	module.type= 'GET';
	module.div = 'MS_dialog_quit_list';
	
	var dialogOpt = {
		width:900,
		height: 600,
		modal:false,
		buttons:  [{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function openSuspendedList($but){
	var module ={};
	module.name= 'reports';
	module.title = getLang('reservations');
	module.data= 'req=reservation';
	module.type= 'GET';
	module.div = 'MS_dialog_suspend_list';
	
	var dialogOpt = {
		width:900,
		height: 600,
		modal:false,
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
		}]
	}
	openAjaxDialog(module, dialogOpt)
	
}

function openRedoublingList($but){
	var module ={};
	module.name= 'reports';
	module.title = getLang('redoubling_report');
	module.data= 'redoubling';
	module.div = 'MS_dialog_redoubling_list';
	
	var dialogOpt = {
		width:900,
		height: 600,
		modal:false,
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
		}]
	}
	openAjaxDialog(module, dialogOpt)	
}

function openHomeStudentList($but){
	var con = $but.attr('con');
	var conId = $but.attr('conid');
	var module = {};
	module.name = 'reports';
	module.div ='#home_content';
	module.title = getLang('students');
	module.data = 'list&con='+con+'&con_id='+conId;
	loadModule(module);
}

function openStudentList($but){
	var con = $but.attr('con');
	var conId = $but.attr('conid');
	var module = {};
	module.name = 'reports';
	module.div =  'students-list-'+con+'-'+conId;
	module.title = getLang('students_lists');
	module.data = 'list&con='+con+'&con_id='+conId;
	var dialogOpt = {
		width:900,
		height: 600,
		maxim:true,
		minim:true,
		modal:false,
		buttons: [{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function openReport(file){
	var html = '<table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('language')+': </label></td><td><select id="report_lang" ><option value="en">En</option><option value="ar">Ar</option><option value="fr">Fr</option></select></td></tr></table>';

	var buttons = [{ 
		text: getLang('ok'), 
		click: function() { 
			var module ={};
			module.name= 'reports';
			module.title = getLang('reports');
			module.data= file+'&lang='+$("#report_lang").val();
			module.type= 'GET';
			module.div = 'MS_dialog_reports';
			var dialogOpt = {
				width:1000,
				height:600,
				title:getLang('reports'),
				maxim:true,
				minim:true,
				buttons: [{ 
				text: getLang('print'), 
				click: function() { 
					printDialog($(this));
				}
			},{ 
				text: getLang('close'), 
				click: function() { 
					$(this).dialog('close');
				}
			}]
			}
			openAjaxDialog(module, dialogOpt)
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('search_std_name', getLang('search_std_name'),  html, 470, 170, buttons)
	
}

function ministryBalance($but){
	module = {
		name: 'reports',
		data: 'ministryreport',
		title: getLang('ministry_balance'),
		div: 'ministry_report',
		callback: function(){
			
		}
	}
	var dialogOpt = {
		width:900,
		height: 600,
		maxim:true,
		minim:true,
		modal:false,
		buttons: [{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}

function openWaitingList($but){
	module = {
		name: 'reports',
		data: 'waiting',
		title: getLang('waiting_list'),
		div: 'waiting_report',
		callback: function(){
			
		}
	}
	var dialogOpt = {
		width:700,
		height: 600,
		maxim:true,
		minim:true,
		modal:false,
		buttons: [{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt)
}


function searchRegReport($btn){
	var $form = $btn.parents('form');
	var module = {
		name : 'reports',
		data: 'reg_report&'+$form.serialize(),
		div: '#reg_result_div',
		title: getLang('reg_report')
	}
	loadModule(module);
}