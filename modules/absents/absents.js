// Absents
function openAbsents(){
	var module = {};
	module.name = 'absents';
	module.div ='#home_content';
	module.title = getLang('absents');
	module.data = '';
	loadModule(module);
}

function openOutPermissions(){
	var module = {};
	module.name = 'absents';
	module.div ='#home_content';
	module.title = getLang('absents');
	module.data = 'permis';
	loadModule(module);
}


function insertStdAbsent(){
	var html = '<form><table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="abs_std_name" type="text" class="input_double" /><input id="abs_std_id_inp" name="std_id[]"  class="autocomplete_value" type="hidden" /><input type="hidden" name="date" value="'+$('#absent_cur_date').val()+'" /></td></tr></table></form>';

	var buttons = [{ 
		text: getLang('add'), 
		click: function() { 
			MS_jsonRequest('absents&daily&add', $('#MS_dialog_search_std_abs form').serialize(), "$('#abs_std_name').val(''); reloadDailyAbsent()");
		}
	},
	{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	
	createHtmlDialog('search_std_abs', getLang('search_std_name'),  html, 500, 200, buttons);
	loadModuleJS('students');
	setStudentAutocomplete('#abs_std_name', '1');
}

function reloadDailyAbsent(){
	var module = {};
	module.name = 'absents';
	module.data = 'daily&day='+$("#absent_cur_date").val();
	module.div = '#absents_daily_tab';
	loadModuleToDiv(new Array(module), '');
}

function deleteAbs(id){
	MS_jsonRequest('absents&del', 'id='+id, "reloadDailyAbsent()");
}

function addill(chk,id){
	if($(chk).attr('checked')){
		var val = 1;
	} else {
		var val = 0;
		
	}
	MS_jsonRequest('absents&daily&update',  'id='+id+'&ill='+val, '');	
}

function addjustify(chk,id){
	if($(chk).attr('checked')){
		var val = 1;
	} else {
		var val = 0;
		
	}
	MS_jsonRequest('absents&daily&update',  'id='+id+'&justify='+val, '');	
}

function addAbsComments(id, comment){
	var html= '<form><input type="hidden" name="id" value="'+id+'" /><textarea name="comments" row="7">'+(comment && comment!='' ? comment : '')+'</textarea></from>';
	var buttons = [{ 
		text: getLang('send'), 
		click: function() { 
			MS_jsonRequest('absents&daily&update',  $('#MS_dialog_search_std_abs form').serialize(), 'reloadDailyAbsent()');
			$(this).dialog('close');
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('search_std_abs', getLang('absent_comment'),  html, 470, 170, buttons)
}

function insertClassAbsent(){
	var module = {};
	module.name = 'students';
	module.data = 'stdfp';
	module.title = getLang('students');
	module.div = 'absents_class';
	var buttons = [{ 
		text: getLang('add'), 
		click: function() { 
			if($('#absents_class input:checked').length>0){
				var date = $('#absent_cur_date').val();
				var data = $('#absents_class form').serialize()+'&date='+date;
				MS_jsonRequest('absents&daily&add', data, "reloadDailyAbsent()");
				$(this).dialog('close');
			} else {
				MS_alert('<h3>'+getLang('error-must_select_student')+'</h3>');
				return false;
			}
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 840, 600, false, '');
}

function submitAbsentListSearch(){
	var module = {};
	module.name = "absents";
	module.data = "abslist&"+$('#absent_list_form').serialize()+'&'+$("#absent_list_terms").val();
	module.div = "#absent_list_div";
	module.title = getLang('absents');
	loadModule(module);
}

function submitAbsentRateSearch(){
	var module = {};
	module.name = "absents";
	module.data = "rate&"+$('#absent_rate_form').serialize()+'&'+$(".absent_rate_terms").val();	
	module.div = "#absents_rate_div";
	module.title = getLang('absents');
	loadModule(module);
}

function reloadTerms($select){
	var $form = $select.parents('form');
	var $field = $form.find('.absent_rate_terms');
	var module = {
		name: "absents",
		data: "period_list&"+$form.serialize(),
		div : $field,
		title: getLang('absents'),
		callback: function(){
			$field.removeClass("MS_formated"); 
			iniCombobox();
		}
	}
	loadModule(module);
}

function initAbsentChart(){
	var dataFile = "index.php?module=absents&chart&"+$('#absent_rate_form').serialize();
	swfobject.embedSWF(
		"assets/swf/open-flash-chart.swf", "chartDiv_absrate", "320", "200",
		"9.0.0", "expressInstall.swf",
		{"data-file":dataFile}
	);	
}

function enlargeChart($btn){
	var src = $btn.find('img').attr('src');
	var html = '<img src="'+src+'" width="100%" height="100%" />';

	var dialogOpt = {
		width:700,
		height:400,
		div:'MS_dialog_search_std_code',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
}

// permissions
function initPermisson(){
	$('#permission_day').change(function(){
		 loadPerResult()
	})
}

function loadPerResult(){
	var module = {};
	module.name = 'absents';
	module.div ='#home_content';
	module.title = getLang('absents');
	module.data = 'permis&day='+$('#permission_day').val();
	loadModuleToDiv(new Array(module), 'initPermisson()');
}

function openPermissionForm(){
	var module = {};
	module.name = 'absents';
	module.data = 'permis&add_permission_form';
	module.title = getLang('permition_out');
	module.div = 'permision_dialog';
	var buttons = [{ 
		text: getLang('add'), 
		click: function() { 
			addPermis();
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 680, 440, false, '$("#permission_form_date").blur()');
}

function insertClassPermis(){
	var module = {};
	module.name = 'students';
	module.data = 'permis&stdfp';
	module.title = getLang('students');
	module.div = 'permission_class';
	var buttons = [{ 
		text: getLang('add'), 
		click: function() { 
			$('#permission_class input:checked').each(function(){
				var stdId = $(this).val();
				var $tr = $(this).parents('tr');
				var stdName = $tr.find('td').eq(1).text();
				var cur_val = $('#stdIds').val();
				if(cur_val.indexOf(stdId) < 0) {
					var newTr = 
					$("#permission_std_table").append('<tr><td>'+stdName+'</td></tr>');
					if(cur_val != ''){
						$('#stdIds').val(cur_val+','+stdId);
					} else {
						$('#stdIds').val(stdId);
					}
				}
			})
			$(this).dialog('close');
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 840, 600, false, '$("#hour").focus()');
}

function insertStdPermission(){
	var html = '<form><table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="permis_std_name" type="text" class="input_double" /><input id="permis_std_id_inp" name="std_id[]"  class="autocomplete_value" type="hidden" /></td></tr></table></form>';

	var buttons = [{ 
		text: getLang('add'), 
		click: function() { 
			if($('#permis_std_id_inp').val() != ''){
				var stdName = $('#permis_std_name').val();
				var stdId = $('#permis_std_id_inp').val();
				var cur_val = $('#stdIds').val();
				if(cur_val.indexOf(stdId) < 0) {
					var newTr = 
					$("#permission_std_table").append('<tr><td>'+stdName+'</td></tr>');
					if(cur_val != ''){
						$('#stdIds').val(cur_val+','+stdId);
					} else {
						$('#stdIds').val(stdId);
					}
				}
			}
			$(this).dialog('close');
		}
	},
	{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	
	createHtmlDialog('search_std_permis', getLang('search_std_name'),  html, 470, 170, buttons);
	loadModuleJS('students');
	setStudentAutocomplete('#permis_std_name', '1');
}

function addPermis(){
	var ids;
	if($('#stdIds').val() !=''){
		ids = $('#stdIds').val();
	} else {
		MS_alert(getLang('error-must_select_student')); 
		return false;
	}
	
	if($('#hour').val() == ''){
		$('#hour').addClass('ui-state-error');
		return false;
	} else {
		$('#hour').removeClass('ui-state-error');
	}
	var data  = $('#permission_form').serialize();
	var module = {
		name: 'absents',
		param: 'permis',
		post: data,
		callback: function(){
			loadPerResult();
			$('#permision_dialog').dialog('close')
		}
	}
	getModuleJson(module);

}

function addPermisComments(id, comment){
	var html= '<form><input type="hidden" name="id" value="'+id+'" /><textarea name="comments" row="7">'+(comment && comment!='' ? comment : '')+'</textarea></from>';
	var buttons = [{ 
		text: getLang('send'), 
		click: function() { 
			MS_jsonRequest('absents&permis&updateper',  $('#MS_dialog_search_std_permis form').serialize(), 'loadPerResult()');
			$(this).dialog('close');
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('search_std_permis', getLang('absent_comment'),  html, 470, 170, buttons)
}


function deletePer($but){
	var module = {
		name: 'absents',
		param: 'delper',
		post: 'id='+$but.attr('absentid'),
		callback: function(){
			loadPerResult()
		}
	}
	getModuleJson(module);
}

function submitStdAbsentList(){
	var $higherLayer = returnHigherLayer();
	var $form = $higherLayer.find('#std_list_form');
	var module = {};
	module.name = "absents";
	module.data = "abslist&"+$form.serialize()+'&'+$("#std_absent_list_terms").val();
	module.div = "#std_absent_list_div";
	module.title = getLang('absents');
	loadModule(module);
}