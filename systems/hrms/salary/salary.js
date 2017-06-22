function openSalaryReport(){
	var module = {
		name: 'salary',
		data: 'salary_report',
		div: '#salary_main_td',
		title: getLang('salary_report')
	}
	loadModule(module)
}

function openInsurReport(){
	var module = {
		name: 'salary',
		data: 'insur_report',
		div: '#salary_main_td',
		title: getLang('salary_report')
	}
	loadModule(module)
}

function updateInsurReport($select){
	var $form = $select.parents('form');
	var module = {
		name: 'salary',
		data: 'insur_report&'+$form.serialize(),
		div: '#salary_main_td',
		title: getLang('salary_report')
	}
	loadModule(module)
}

function openTaxGainReport(){
	var module = {
		name: 'salary',
		data: 'gain_tax_report',
		div: '#salary_main_td',
		title: getLang('salary_report')
	}
	loadModule(module)
}

function openSalaryApprove(){
	var module = {
		name: 'salary',
		data: 'approve',
		div: '#salary_main_td',
		title: getLang('salary_report')
	}
	loadModule(module)
}

function newProfil($but){
	var module ={};
	module.name= 'salary';
	module.title = getLang('salary');
	module.data= 'profil&new';
	module.div = 'MS_dialog_profil_new';
	var dialogOpt = {
		width:1000,
		height:400,
		title:getLang('salary'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var $form = $('#MS_dialog_profil_new form');
				var module = {
					name: 'salary',
					param: 'profil&save',
					post: $form.serialize(),
					callback:function(answer){
						$form.find('input[name="id"]').val(answer.id);
						var $list = $('#salary_main_td .list_menu');
						$list.append('<li action="openProfil" profil_id="'+answer.id+'" class="ui-state-default hoverable">'+$form.find('input[name="title"]').val()+'</li>');
						initiateJquery();
						$list.find('li:last').click();
					}
				}
				getModuleJson(module);
			}
		}, { 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]/*, 
		callback: function(){
			$(".sortable").sortable({
				stop:function(){
					updateProfilOrder();
				}
			})
		}*/
	}
	
	openAjaxDialog(module, dialogOpt)
}


function saveProfil($but){
	var $form = $but.parents('form');
	var module = {
		name: 'salary',
		param: 'profil&save',
		post: $form.serialize(),
		callback:function(answer){
			$form.find('input[name="id"]').val(answer.id);
			$form.find('[profil_id]').attr('profil_id', answer.id)
		}
	}
	getModuleJson(module);
}

function deleteProfil($but){
	var profilId = $but.attr('profil_id');
	var module = {
		name: 'salary',
		param: 'profils&delete',
		post: 'profil_id='+profilId,
		callback:  function(){
			var $list = $('#salary_main_td .list_menu');
			$list.find('li.ui-state-active').fadeOut().remove();
			$list.find('li.ui-state-active:first').click();
		}
	}
	getModuleJson(module);
}

function updateProfilOrder(){
	var credits = new Array;
	$('#credit_ul li').each(function(){
		if($(this).attr('rel') != ''){
			credits.push($(this).attr('rel'));
		} else {
			return false;
		}
	})

	var debits = new Array;
	$('#debit_ul li').each(function(){
		if($(this).attr('rel') != ''){
			debits.push($(this).attr('rel'));
		} else {
			return false;
		}
	})
	var data = 'credit='+credits.join(',')+'&debit='+debits.join(',');
	var upadte = {
		name : 'salary',
		param : 'profil&order',
		title : getlang('salary'),
		post : data
	}
	getModuleJson(update);
	
}

function newElmnt($btn){
	var dir = $btn.attr('rel');
	var profilId = $btn.attr('profil_id');
	var $table = $btn.parents('table').eq(0);
	var $target = $table.find('#'+dir+'_ul');
	var module ={};
	module.name= 'salary';
	module.title = getLang('salary');
	module.data= 'profil&newelement&type='+dir;
	module.div = 'MS_dialog_profil_new_element';
	module.callback = function(){
		$('#MS_dialog_profil_new_element input[name="profil_id"]').val(profilId);
		$("#elmnt_field").val(dir);
	}
	var dialogOpt = {
		width:500,
		height:430,
		title:getLang('salary'),
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var $form = $('#MS_dialog_profil_new_element .ui-tabs-panel:visible form');
				if(validateForm($form)){
					var module = {
						name: 'salary',
						param: 'profil&saveelmnt',
						post: $form.serialize(),
						callback: function(answer){
							$target.append(answer.html);
							initiateJquery();
							 $('#MS_dialog_profil_new_element').dialog('close')
						}
					}
					getModuleJson(module);
				}
			}
		}, { 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			loadModuleJS('accounts');
			formatAccountCode($('#MS_dialog_profil_new_element'))
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function delElment($btn){
	var elemntId = $btn.attr('elemnt_id');
	var profilId = $btn.attr('profil_id');
	var module = {
		name: 'salary',
		param: 'profil&delelmnt',
		post: 'profil_id='+profilId+'&elemnt_id='+elemntId,
		callback: function(answer){
			var $li = $btn.parents('li').eq(0);
			$li.fadeOut().remove();
		}
	}
	getModuleJson(module);
}

function openSalaryProfils(){
	var module = {
		name:'salary',
		data: 'profil',
		title:getLang('profil'),
		div: '#salary_main_td',
		callback: function(){
		}
	}
	loadModule(module);
}
function openProfil($btn){
	var profilId = $btn.attr('profil_id');
	var module = {
		name:'salary',
		data: 'profil&profil_id='+profilId,
		title:getLang('profil'),
		div: '#profil_data',
		callback: function(){
		}
	}
	loadModule(module);
}

function openSalarySheet($btn){
	var module ={};
	module.name= 'salary';
	module.title = getLang('salary');
	module.data= 'sheet&emp_id='+$btn.attr('emp_id');
	module.div = 'MS_dialog_salary-'+$btn.attr('emp_id');
	var dialogOpt = {
		width:1050,
		height:600,
		title:getLang('salary'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		},{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($(this));
				$(this).dialog('close');
			}
		}], 
		callback: function(){
		}
	}
	
	openAjaxDialog(module, dialogOpt)
}

function updateSalaryReport($btn){
	var $form = $btn.parents('form');
		var module = {
		name: 'salary',
		data: 'salary_report&'+$form.serialize(),
		div: '#salary_main_td',
		title: getLang('salary_report')
	}
	loadModule(module)

}

function openSalaryEditor($btn){
	var $form = $btn.parents('form');
		var module = {
		name: 'salary',
		data: 'salary_editor',
		div: '#salary_main_td',
		title: getLang('salary_editor')
	}
	loadModule(module)
}

function updateSalaryEditor($btn){
	var $form = $btn.parents('form');
		var module = {
		name: 'salary',
		data: 'salary_editor&'+$form.serialize(),
		div: '#salary_main_td',
		title: getLang('salary_report')
	}
	loadModule(module)

}

function saveSalaryEditor($btn){
	var $form = $('#salary_editor_form');
	var module = {
		name: 'salary',
		param: 'salary_editor&save',
		post: $form.serialize(),
	}
	getModuleJson(module);
}