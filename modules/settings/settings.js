// settings.js
/**************initiated **************/
function iniSettings(){
	$('.listMenuUl li').click(function(){
		var $li = $(this);
		var rel = $li.attr('rel');
		$("#setting_div_td .setting_divs").not("#"+rel).fadeOut( 300)
		setTimeout('$("#'+rel+'").show(310).fadeIn()', 310)
	})
}

function submitSettings(){
	if(validateForm('#settings_form')){
		var data =$('#settings_form').serialize();
		MS_jsonRequest("settings", data, '')		
	} else{
		MS_alert('<img src="assets/img/error.png" /><span class="title_wihte">'+answer.error+'<span><br>'+getlang('fill_req_fields'));
		return false;
	}
}

// users functions

function openUser($but){
	var group = $but.attr('group');
	var userId = $but.attr('userid');
	var dialogId = 'MS_dialog_users-'+group+'-'+userId
	var module ={};
	module.name= 'settings';
	module.title = getLang('users');
	module.data= 'users&user_data&group='+group+'&user_id='+userId;
	module.type= 'GET';
	module.div = dialogId;
	module.cache = false;
	
	dialogId = '#'+dialogId;
	module.callback = function(){
		if( $(dialogId).find('input[name="user_name"]').val() != ''){
			name = $(dialogId).find('input[name="user_name"]').val()
			$(dialogId).dialog({'title': getLang('user')+': '+name});
		}
	}
	
	var dialogOpt = {
		width:600,
		height:500,
		title:getLang('user'),
		minim:false,
		maxim:false,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if(validateForm(dialogId+' #new_user_form')){
					if($(dialogId+' #password').val() != $(dialogId+' #password2').val()){
						MS_alert('<img src="assets/img/warning.png" />'+getLang('error-password_mismatch'));
						$(dialogId+' #password').val('').addClass('ui-state-error');
						$(dialogId+' #password2').val('').addClass('ui-state-error');
					} else {
						$(dialogId+' form[name="user_form"]').find('input, select').removeAttr('disabled');
						MS_jsonRequest('settings&users&save_user', $(dialogId+' form[name="user_form"]').serialize(), function(answer){
							$(dialogId).dialog("close");
						});
					}
				}
			}
		}, { 
			text: getLang('print'), 
			click: function() { 
				printTag(dialogId+' #user_infos');
			}
		}, { 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	
	openAjaxDialog(module, dialogOpt)
}

function addUser(){
	var dialogId = 'MS_dialog_users-new'
	var module ={};
	module.name= 'settings';
	module.title = getLang('new_user');
	module.data= 'users&new';
	module.div = dialogId;
	dialogId = '#'+dialogId;
	module.callback = function(){
		setEmployerAutocomplete(dialogId +' input[name="user_name"]', '')
	}
	var dialogOpt = {
		width:450,
		height:300,
		title:getLang('user'),
		minim:false,
		maxim:false,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if(validateForm(dialogId+' #new_user_form')){
					if($(dialogId+' #password').val() != $(dialogId+' #password2').val()){
						MS_alert('<img src="assets/img/warning.png" />'+getLang('error-password_mismatch'));
						$(dialogId+' #password').val('').addClass('ui-state-error');
						$(dialogId+' #password2').val('').addClass('ui-state-error');
					} else {
						$(dialogId+' form[name="user_form"]').find('input, select').removeAttr('disabled');
						MS_jsonRequest('settings&users&save_user', $(dialogId+' form[name="user_form"]').serialize(), function(answer){
							$(dialogId).dialog("close");
						});
					}
				}
			}
		}, { 
			text: getLang('print'), 
			click: function() { 
				printTag(dialogId+' #user_infos');
			}
		}, { 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	
	openAjaxDialog(module, dialogOpt)
}

function setAutocompFieldType(){
	var Group = $('#group').val();
	if(Group == 7 ) { // student
		setStudentAutocomplete('#user_name', '1');
	} else if(Group == 8 ) { // parents
		setParentAutocomplete('#user_name', '');
	} else {
		setEmployerAutocomplete('#user_name', '')
	}
}

function generateUsername(){
	if($('#user_name').val() != '' && $('#user_id').val() != ''){
		var name = $('#user_name').val();
		var moduleData = new Array;
		var module = {};
		module.name = "settings";
		module.title = getLang('check_login_name');
		module.data = 'chknewuser='+name;
		moduleData.push(module);
	
		MS_jsonRequest('settings', 'chknewuser='+name, "$('#name').val(answer.login)")	
	} else if($('#user_name').val() == ''){
		MS_alert('<img src="assets/img/warning.png" />'+getLang('error-must_type_name'));
	}else if($('#user_id').val() == ''){
		MS_alert('<img src="assets/img/error.png" />'+getLang('error-name_not_found'));
	}
}

// Generate passord
var keylist="abcdefghijklmnopqrstuvwxyz123456789";
var temp='';
function generatepass(plength){
	temp=''
	for (i=0;i<plength;i++)
	temp+=keylist.charAt(Math.floor(Math.random()*keylist.length))
	return temp
}


function generatePassword(){
	var pass =  generatepass(7);
	$('#password').val(pass);
	$('#password2').val(pass);
	$('#generatedPass').html(pass).addClass('ui-widget-content ui-corner-all');
}

function submitNewUser(){
	if(validateForm('#new_user_form')){
		if($('#password').val() != $('#password2').val()){
			MS_alert('<img src="assets/img/warning.png" />'+getLang('error-password_mismatch'));
			$('#password').val('').addClass('ui-state-error');
			$('#password2').val('').addClass('ui-state-error');
		} else {
			MS_jsonRequest('settings', $('#new_user_form').serialize(), "$('#MS_dialog_settings').dialog('close')")
		}
	} else return false;
}

function deleteUser($but){
	var group = $but.attr('group');
	var userId = $but.attr('userid');
	var deleteUser = {
		name: 'settings',
		param: 'users&del',
		post: 'user_id='+userId+'&group='+group, 
		callback: function(){
			var $tr = $but.parents('tr').eq(0);
			$tr.fadeOut();
		}
	}
	getModuleJson(deleteUser);
}

function listGroup($btn){
	var group = $btn.attr('group');
	var module ={};
	module.name= 'settings';
	module.title = getLang('group');
	module.data= 'listgroup='+group;
	module.div = 'MS_dialog_group';
	var buttons = [{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 600, 400, false, "")
}

function regenerateUsers($btn){
	var group = $btn.attr('group');
	var module ={};
	module.name= 'settings';
	module.title = getLang('users');
	module.data= 'generate_users&group='+group;
	module.div ='MS_dialog_generate_users';
	var dialogOpt = {
		width:600,
		height:550,
		title:getLang('search'),
		buttons: [{ 
		text: getLang('ok'), 
		click: function() { 
			$(this).dialog('close');
		}
	}]
	}
	openAjaxDialog(module, dialogOpt);
}

// Documents functions
function enableDocsSetting(){
	$('#docs_table .ui-state-default').removeClass('disabled').removeAttr('disabled');
	$('#docs_table .ui-button').css('visibility', 'visible');
}

function disableDocsSetting(){
	$('#docs_table .ui-state-default').addClass('disabled').attr('disabled', 'disabled');
	$('#docs_table .ui-button').css('visibility', 'hidden');
}

// layout functions
function changeLogo(){
	uploadFile('attachs/img', 'logo.png', true, "reloadImg('#main-logo'); reloadImg('#settings-logo')")	
}

function changeHeader(){
	uploadFile('attachs/img', 'header.jpg', true,  "reloadImg('#settings-header')")	
}

function changeFooter(){
	uploadFile('attachs/img', 'footer.jpg', true, "reloadImg('#settings-footer')")	
}

// privilege windows
function openPrvlg($but){
	var group = $but.attr('group');
	var module ={};
	module.name= 'settings';
	module.title = getLang('privileges');
	module.data= 'prvlg='+group;
	module.div = 'MS_dialog_privilege';
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			submitPrlvg(group);
		}
	},
	{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 600, 450, false, "$('#prvlg_list li:first').click()")
}

function openPrvlgByKey($btn){
	var divName = '#prvlg_'+ $btn.attr('rel');
	$('#prvlg_form div').not(divName).hide();
	$(divName).show('500');
}

function submitPrlvg(group){
	var save = {
		name:'settings',
		param: "save&prvlg="+group,
		post: $('#MS_dialog_privilege #prvlg_form').serialize(),
		callback: function(){
			$('#MS_dialog_privilege').dialog('close');
		}
	}
	getModuleJson(save);
}

function chlAllPrvlg(){
	var $curDiv = $('#prvlg_form div:visible');
	$curDiv.find('input[type="checkbox"]').each(function(){
		$(this).attr('checked', 'checked');
	});
}

function recordWeekDay(day){
	$(day).toggleClass('ui-state-active ui-state-default');
	var dayArr = new Array();
	$("#new_week_day li").each(function(){
		if($(this).hasClass('ui-state-default')){
			dayArr.push($(this).attr('val'));
		}
	})
	$('#weekend').val(dayArr.join(','));
}


function toggleLog(but){
	$(but).next('table.tablesorter').toggle('blind', 500);
}

function openSysTools(){
	var module ={};
	module.name= 'system';
	module.title = getLang('system_tools');
	module.data= '';
	module.div = 'MS_dialog_system_tools';
	var buttons = [{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, true, 800, 600, false, "", true, true)
	
}

function activateSys(onOff){
	if(onOff == '1'){
		MS_mysqlAjaxUpdate(window.DB_student, 'settings', 'value=1', "key_name", 'system_stat')
	} else {
		createHtmlDialog('system_stat', getLang('system_stat'), getLang('system_msg-1'), 300, 220,  [{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		},
		{ 
			text: getLang('put_offline'), 
			click: function() { 
				MS_mysqlAjaxUpdate(window.DB_student, 'settings', 'value=0', "key_name", 'system_stat', '$("#MS_dialog_system_stat").dialog("close")')
			}
		}], true);
	}
}

function assignJobPrvlg($inp){
	var param;
	if($inp.is(':checked')){
		p = 'add';
	} else {
		p = 'delete'
	}
	var jobId = $inp.attr('job_id');
	var $form = $inp.parents('form');
	var userId = $form.find('input[name="user_id"]').val();
	var submitJob = {
		name: 'settings',
		param: 'jobprvlg&'+p,
		post: 'user_id='+userId+'&job_id='+jobId, 
	}
	getModuleJson(submitJob);
	
}