	// JavaScript Document
function startNewYear(){
	var module ={};
	module.name= 'new_year';
	module.title = getLang('new_year');
	module.data= 'wizard_step=0';
	module.type= 'POST';
	module.div = 'new_year_wizard';
	var dialogOpt = {
		width:900,
		height:600,
		title:getLang('new_year'),
		maxim:true,
		minim:false,
		modal:true,
		close : function(){
			if( $('#wizard_step').val() != '1'){
				cancelNewYear();
			}
		},
		buttons: [{ 
			text: getLang('next'), 
			click: function() { 
				nextYearStep();
			}
		},{ 
			text: getLang('cancel'), 
			click: function() {
				$("#new_year_wizard").dialog("close");
			}
		}]
	}
	
	openAjaxDialog(module, dialogOpt)
} 

function nextYearStep(){
	$('#new_year_wizard_form').removeClass('MS_formed')
	var curtStep = parseInt($('#wizard_step').val());
	var nextStep = (curtStep+1);
	var module ={};
	module.name = 'new_year';
	module.data = $('#new_year_wizard_form').serialize();
	module.title = getLang('new_year');
	module.div = '#new_year_wizard_form';
	module.type = 'POST';
	module.callback = function(){
		if($('#new_year_wizard_form').find('#wizard_step').val() == 'finish'){
			var buttons = [{ 
				text: getLang('finish'), 
				click: function() { 
					document.location ="index.php"
				}
			},{ 
				text: getLang('cancel'), 
				click: function() {
					cancelNewYear();
					$("#new_year_wizard").dialog("close");
				}
			}]
		} else {
			var buttons = [{ 
				text: getLang('next'), 
				click: function() { 
					nextYearStep();
				}
			},{
				text: getLang('prev'), 
				click: function() { 
					 prevYearStep()
				}
			},{ 
				text: getLang('cancel'), 
				click: function() {
					cancelNewYear();
					$("#new_year_wizard").dialog("close");
				}
			}];	
		}
		$('#new_year_wizard').dialog({buttons: buttons});
	}
	loadModule(module);
}

function prevYearStep(){
	var module = {
		name: 'new_year',
		data: $('#new_year_wizard form').serialize(),
		type : "POST",
		div: "#new_year_wizard"
	}
	loadModule(module);
}

function cancelNewYear(newYear){
	var submitDel = {
		name: 'new_year',
		post: 'resetyear=1'+(newYear==true ? '&new=1':''),
		callback: function(){
			$("#new_year_wizard").dialog('close').html('');
			//evalDeleteDb(ans)
		}
	}
	getModuleJson(submitDel);
	

}


function toggleLog(but){
	$(but).next('table.tablesorter').toggle('blind', 500);
}


function openResources($but){
	var group = $but.attr('rel');
	var module ={};
	module.name= 'resources';
	module.title = getLang(group);
	module.data= 'templ='+group;
	module.div = 'MS_dialog_'+group;
	dialogOpt = {
		buttons: [{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		width:800,
		height:580,
		minim:true,
		modal:false
	}
	openAjaxDialog(module, dialogOpt);	
}

function selectMaterials(){
	if($('input[name="copy_service"]:checked').length > 0){
		$('input[name="generate_optional_groups"]').attr('disabled', false);
		if($('input[name="generate_religion_groups"]:checked').length > 0){
			$('#religion_table').slideDown().fadeIn();
		}
	} else {
		$('input[name="generate_optional_groups"]').attr({'disabled': 'disabled', 'checked':false});
		$('#religion_table').slideUp().fadeOut();
	}
}

function selectGroupReligion(){
	if($('input[name="generate_religion_groups"]:checked').length > 0){
		if($('input[name="copy_service"]:checked').length > 0){
			$('#religion_table').slideDown().fadeIn();
		}
	} else {
			$('#religion_table').slideUp().fadeOut();
	}
}

function selectTerms(){
	if($('input[name="copy_terms"]:checked').length > 0){
		$('#terms_div').slideDown().fadeIn();
	} else {
		$('#terms_div').slideUp().fadeOut();
	}
}

function finalizeYear(){
	var module ={};
	module.name= 'new_year';
	module.title = getLang('new_year');
	module.data= 'finalize=0';
	module.type= 'POST';
	module.div = 'new_year_wizard';
	var dialogOpt = {
		width:900,
		height:600,
		title:getLang('new_year'),
		maxim:true,
		minim:false,
		modal:true,
		buttons: [{ 
			text: getLang('next'), 
			click: function() { 
				nextFinalizeStep();
			}
		},{ 
			text: getLang('cancel'), 
			click: function() {
				$("#new_year_wizard").dialog("close");
			}
		}]
	}
	
	openAjaxDialog(module, dialogOpt)	
	
}

function nextFinalizeStep(){	
	$('#new_year_wizard_form').removeClass('MS_formed');
	loadModuleJS('reports');
	var curtStep = parseInt($('#new_year_wizard input[name="finalize"]').val());
	var nextStep = (curtStep+1);
	$('#new_year_wizard input[name="finalize"]').val(nextStep)
	var module ={};
	module.name = 'new_year';
	module.data = $('#new_year_wizard_form').serialize();
	module.title = getLang('new_year');
	module.div = '#new_year_wizard_form';
	module.type = 'POST';
	module.callback = function(){
		if($('#new_year_wizard_form').find('input[name="finalize"]').val() == 'finish'){
			var buttons = [{ 
				text: getLang('finish'), 
				click: function() { 
					document.location ="index.php"
				}
			},{ 
				text: getLang('cancel'), 
				click: function() {
					$("#new_year_wizard").dialog("close");
				}
			}]
		} else {
			var buttons = new Array;
			if($('#new_year_wizard input[name="finalize"]').val() > 0){
				buttons.push({
					text: getLang('prev'), 
					click: function() { 
						 prevFinalizeStep()
					}
				});
			}
			buttons.push({ 
				text: getLang('next'), 
				click: function() { 
					nextFinalizeStep();
				}
			},{ 
				text: getLang('cancel'), 
				click: function() {
					$("#new_year_wizard").dialog("close");
				}
			});	
		}
		$('#new_year_wizard').dialog({buttons: buttons});
	}
	loadModule(module);
}

function prevFinalizeStep(){
	var thisStep = parseInt($('#new_year_wizard input[name="finalize"]').val());
	var module = {
		name: 'new_year',
		data: 'finalize='+(thisStep-1),
		type : "POST",
		div: "#new_year_wizard"
	}
	loadModule(module);
}

function addRepeaters(){
	var module = {};
		module.name = "students";
		module.data = 'events&stdfp';
		module.div = "MS_dialog-student_browse";
		module.title= getLang('students');
	var dialogOpt = {
		width:840,
		height:600,
		title:getLang('repeatrs'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var students = new Array;
				$('#MS_dialog-student_browse input[name="std_id[]"]:checked').each(function(){
					students.push($(this).val());
				})
				if(students.length > 0){
					var add = {
						name: 'new_year',
						param: 'add_repeaters',
						post: 'std_ids='+students.join(','),
						callback: function(){	
							var redoubleList = {
								name: "reports",
								data: 'redoubling',
								title: getLang('redoubling_report'),
								div: '#finalize_repeater_table'
							}
							loadModule(redoubleList);
							$('#MS_dialog-student_browse').dialog('close');
						}
					}
					getModuleJson(add);
				} else {
					MS_alert(getLang('nothing_selected'));
				}
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
		
	openAjaxDialog(module, dialogOpt);	
}


