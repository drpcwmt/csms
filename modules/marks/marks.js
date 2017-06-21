function openMarks($but){
	var con = $but.attr('con'), conId = $but.attr('con_id');
	var module = {};
	module.name = "marks";
	module.data = 'con='+con+'&con_id='+conId;
	module.div = $('#home_content');
	module.callback = function(){
		initExamTable()
	}
	loadModule(module);
}

function initExamTable(){
	$('.exam_tabel').tooltip({items:'td.result_cell', content: function() { return $(this).find('.tooltip').html()}, html: true});
}
/********************** Exams ********************/
function reloadMarks($inp){
	var termId = $inp.val();
	var $tabs = $inp.parents('.tabs').eq(0);
	var con = $tabs.attr('con');
	var conId = $tabs.attr('con_id');
	var module = {};
	module.name = "marks";
	module.data = 'reload_marks&term_id='+termId+'&con='+con+'&con_id='+conId;
	module.div = $tabs.find('.exam_table');
	module.callback = function(){
		initExamTable()
	}
	loadModule(module);
}

function loadExam($but){
	var termId = $but.attr('termid');
	var serviceId = $but.attr('serviceid');
	var examNo = $but.attr('examno');
	var $tabs = $but.parents('.scope').eq(0);
	var con = $tabs.attr('con');
	var conId = $tabs.attr('con_id');
	var module = {};
	module.name = "marks";
	module.data = 'loadexam&term_id='+termId+'&con='+con+'&con_id='+conId+'&service_id='+serviceId+'&exam_no='+examNo;
	module.div = 'MS_dialog-exams_result-'+serviceId+'-'+termId+'-'+examNo;
	module.title =  getLang('exams');
	module.cache = false;
	module.callback = function(answer){
		activateResults("#"+module.div); 
		checkApprove("#"+module.div);
	}

	dialogOpt = {
		buttons:[{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		},{ 
			text: getLang('print'), 
			click: function() { 
				print_pre('#'+module.div+' .exam_result_form');
			}
		}],
		width:700,
		height:600,
		modal:true,
		title: getLang('exam')+': '+examNo
	}
	openAjaxDialog(module, dialogOpt);	
}

function appprovExam($but){
	var examId = $but.attr('examid');
	var $scope = $but.parents('.exam_result_form');
	var con = $scope.find('input[name=con]').val();
	var conId = $scope.find('input[name=con_id]').val();
	MS_jsonRequest('marks&approve_exam&con='+con+'&con_id='+conId,'exam_id='+examId, function(){
		$but.hide();
		$but.next('.unlock').show();
		$scope.find('.approved_tag').fadeIn();
		$scope.find('input').attr('disabled', 'disabled');
		$scope.find('input[name=approved]').val(1);
		checkApprove( '#'+$but.parents('.ui-dialog-content').attr('id') );
	});
}

function unAppprovExam($but){
	var examId = $but.attr('examid');
	var $scope = $but.parents('.exam_result_form');
	var con = $scope.find('input[name=con]').val();
	var conId = $scope.find('input[name=con_id]').val();
	MS_jsonRequest('marks&unapprove_exam&con='+con+'&con_id='+conId,'exam_id='+examId, function(){
		$but.hide();
		$but.prev('.lock').show();
		$scope.find('.approved_tag').fadeOut();
		$scope.find('input[name=approved]').val(0);
		$scope.find('input').attr('disabled', false);
		checkApprove( '#'+$but.parents('.ui-dialog-content').attr('id') );
	});
}

function checkApprove(dialog){
	var approved = $(dialog).find('input[name=approved]').val();
	if( approved != '1'){
		var buttons = [{ 
			text: getLang('save'), 
			click: function() { 
				var con =  $(dialog).find('input[name=con]').val();
				var conId =  $(dialog).find('input[name=con_id]').val();
				MS_jsonRequest('marks&subexam&con='+con+'&con_id='+conId, $(dialog+' .exam_result_form').serialize(), function(){
					$(dialog).dialog('close'); 
					var $examDiv = $('#exams_tabs_div-'+con+'-'+conId);
					var $inp = $examDiv.find('select[name="terms"]');
					reloadMarks($inp);
				});
			}
		},{ 
			text: getLang('print'), 
			click: function() { 
				print_pre('#'+module.div+' .exam_result_form');
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(dialog).dialog('close');
			}
		}];
		$(dialog).dialog("option",'buttons', buttons);
	} else  {
		$(dialog+' input, '+ dialog +' select').attr('disabled', "disabled");
		var buttons = [{ 
			text: getLang('print'), 
			click: function() { 
				print_pre('#'+module.div+' .exam_result_form');
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(dialog).dialog('close');
			}
		}];
		$(dialog).dialog("option",'buttons', buttons);
	}
}

/************** Appreciations **************************/
function saveAppr($but){
	var $scope = $but.parents('.tabs').eq(0);
	var con = $scope.attr('con');
	var conId = $scope.attr('con_id');
	var submitSave = {
		name: 'marks',
		param: 'appreciation&submitappr&con='+con+'&con_id='+conId,
		post: $scope.find('.appreciation_form').serialize(),
	}
	getModuleJson(submitSave);
}

function reloadAppr($inp){
	var $tabs = $inp.parents('.tabs').eq(0);
	var con = $tabs.attr('con');
	var conId = $tabs.attr('con_id');
	var $apprDiv = $("#appr_tabs_div-"+con+"-"+conId);
	var termId = $apprDiv.find('select[name=terms]').val();
	var serviceId = $apprDiv.find('select[name=services]').val();
	var module = {};
	module.name = "marks";
	module.data = 'appreciation&reload&con='+con+'&con_id='+conId+'&term_id='+termId+'&service_id='+serviceId;
	module.div = $apprDiv.find('.appr_table_div');
	loadModule(module);
}

/************************** Edit ****************/
function editMarks($but){
	var con = $but.attr('con');
	var conId = $but.attr('conid');	
	var module ={
		name: 'marks',
		data: 'edit&terms_form&con='+con+'&con_id='+conId,
		title: getLang('marks'),
		div: 'MS_dialog-terms'
	}
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() { 
			var submitSave = {
				name: 'marks',
				param: 'edit&save_edit&con='+con+'&con_id='+conId,
				post: $('#reset_form').serialize(),
				callback: function(){
					var $select = $('#exams_tabs_div-'+con+'-'+conId+' select[name="terms"]');
					reloadMarks($select);
					$('#MS_dialog-terms').dialog('close');
				}
			}
			getModuleJson(submitSave);

		}
	}, { 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:840,
		height:600,
		modal:true,
		minim:false,
		maxim:true
	}
	openAjaxDialog(module, dialogOpt);	
	
}

function selectAllExams($but){
	var $div = $but.parents('div.ui-tabs-panel').eq(0);
	$div.find('td.result_cell').toggleClass('ui-state-active');
}

function loadExamInfo($but){
	var con = $but.attr('con');
	var conId = $but.attr('conid');	
	var exams = new Array;
	$('#mark_edit_div-'+con+'-'+conId+' #exam_tabel td.ui-state-active').each(function(){
		exams.push($(this).attr('serviceid')+'-'+$(this).attr('termid')+'-'+$(this).attr('examno'));
	});

	if(exams.length > 0){
		data = "cells="+exams.join(',')+'&con='+con+'&con_id='+conId;
		var module ={};
		module.name = 'marks';
		module.data = 'edit&exams&form&'+data;
		module.title = getLang('exams');
		module.div = 'MS_dialog-exams';
		dialogOpt = {
			buttons:  [{ 
				text: getLang('save'), 
				click: function() { 
					var submitSave = {
						name: 'marks',
						param: 'edit&exams&savepreset',
						post: $('#MS_dialog-exams form').serialize(),
						callback: function(){
							$('#MS_dialog-exams').dialog('close');
							var module = {};
							module.name = "marks";
							module.title = getLang("marks");
							module.data = "edit&reloadexamtable&con="+con+'&con_id='+conId;
							module.div = "#mark_edit_div-"+con+'-'+conId+' div.exam_table';
							loadModule(module);
						}
					}
					getModuleJson(submitSave);
				}
			},{ 
				text: getLang('cancel'), 
				click: function() { 
					$(this).dialog('close');
				}
			}],
			width:400,
			height:320,
			modal:true,
			minim:false,
			maxim:false
		}
		openAjaxDialog(module, dialogOpt);	
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('must_select_exam')+'</h2>');
		return false;
	}
}

function resetTerms($but){
	var con = $but.attr('con');
	var conId = $but.attr('conid');		
	var module ={};
	module.name = 'marks';
	module.data = 'edit&reset&form&con='+con+'&con_id='+conId,
	module.title = getLang('terms');
	module.div = 'MS_dialog-terms_reset';
	dialogOpt = {
		buttons:  [{ 
		text: getLang('apply'), 
			click: function() { 
				$('#applyTo').val($('#calc_applyto').val());
				var submitSave = {
					name: 'marks',
					params: 'edit&reset&save&con='+con+'&con_id='+conId,
					data:  $('#reset_form').serialize(),
					type: 'POST',
					div: '#MS_dialog-terms_reset',
					callback : function(){
						$('#MS_dialog-terms_reset').dialog('option', 'buttons', [{ 
							text: getLang('done'), 
							click: function() { 
								var submitSave = {
									name: 'marks',
									param: 'edit&save_edit&con='+con+'&con_id='+conId,
									post: $('#MS_dialog-terms_reset form').serialize(),
									callback: function(){
										$('#MS_dialog-terms_reset').dialog('close');
										var mod = {};
										mod.name = "marks";
										mod.title = getLang("marks");
										mod.data = "edit&reloadexamtable&con="+con+'&con_id='+conId;
										mod.div = "#mark_edit_div-"+con+'-'+conId+' div.exam_table';
										loadModule(mod);
									}
								}
								getModuleJson(submitSave);
							}
						}]);
					}
				}
				loadModule(submitSave);
				
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		width:700,
		height:320,
		modal:true,
		minim:false,
		maxim:false
	}
	openAjaxDialog(module, dialogOpt);	
}

function saveCertTempl(){
	MS_jsonRequest('marks&terms&cert_templ', $('#template_form').serialize(), "reloadCertificates();")	;
}

function reloadCertificates(){
	var $tab = $('#template_form').parents('.ui-tabs-panel');
	var tab_id = $tab.attr('id');
	var module = {};
	module.name = "marks";
	module.data = "reports&con="+$('#template_form input[name="con"]').val()+"&con_id="+$('#template_form input[name="con_id"]').val();
	module.div = '#'+tab_id;
	loadModule(module);
}

function writeTermExam(value){
	$('#exam_no').val(value)
	if(value=='1'){
		$('#exam_no').show();
	} else {
		$('#exam_no').hide();
	}
}

function viewHideOption(value){
	$('#calc_option_div div').hide();
	if(value=='marks'){
		$('#perc_opt').show();
	}
}

function revalueNextBegin(Binput){
	var thisDate = $(Binput).val();
	if(thisDate != ''){
		var d = thisDate.split('/');
		var date = new Date(parseInt(d[2]), (parseInt(d[1])-1), (parseInt(d[0])+1) ,0,0);
		var $tr = $(Binput).parents('tr');
		var $Ninput = $tr.next('tr').find('input[name="begin_date[]"]');
		$Ninput.val(dateFormat(date, 'dd/mm/yyyy'));
	}
}

function approveTerm(){
	var txt, param;
	var termId = $("#cur_term").val();
	var approved = $("#term_approved").val() == '1'? true : false;
	if(termId == 'false'){ // hole level case
		if(!approved){
			txt = getLang('cfm_approv_level');
			param = 'approve_level';
		} else {
			txt = getLang('cfm_unapprov_level');
			param = 'unapprove_level';
		}
	} else {
		if(!approved){
			txt = getLang('cfm_approv_term');
			param = 'approve_term';
		} else {
			txt = getLang('cfm_unapprov_term');
			param = 'unapprove_term';
		}
	}
	var html = '<fieldset class="ui-corner-all ui-state-highlight">'+txt+'</fieldset>';
	var buttons = [{ 
		text: getLang('yes'), 
		click: function() { 
			var con = $('#marks_form input[name="con"]').val();
			var conId = $('#marks_form input[name="con_id"]').val();
			MS_jsonRequest('marks&terms&'+param+'&'+$('#marks_form').serialize(), "id="+termId, "$('#MS_dialog_aprvTerm').dialog('close');reloadMarks();generateCertificateCfm('"+con+"', "+conId+", "+termId+")")	;
		}
	}, { 
		text: getLang('no'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog("aprvTerm",(approved ? getLang('unlock') : getLang('lock')), html, 300, 200, buttons, true)	
}

/************************** Gradding ****************/
function newGrading(){
	var module ={};
	module.name = 'marks';
	module.data = 'gradding&newgrad';
	module.title = getLang('gradding_shell');
	module.div = 'MS_dialog-gradding';
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			var submitSave = {
				name: 'marks',
				param: '&gradding&submit_gradding',
				post: $('#gradding_form').serialize(),
				callback: function(answer){
					evalSubmitGradding(answer)
				}
			}
			getModuleJson(submitSave);
		}
	},{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	
	createAjaxDialog(module, buttons, false, 380, 500, false, '')	
}

function newTitle(){
	$('#gradding_table tbody').append('<tr><td><input type="text" style="width:120px" name="title[]" class="ui-state-default"></td><td><input type="text" style="width:40px" name="min[]" class="ui-state-default"> %</td><td><input type="text" style="width:40px" name="max[]" class="ui-state-default"> %</td></tr>');
}

function evalSubmitGradding(answer){
	$('#grading_list option').each(function(){
		$(this).removeAttr("selected");
	});
	if($('#gradding_form input[name="id"]').val() == ''){
		$('#grading_list').append('<option value="'+answer.id+'" selected="selected">'+answer.name+'</option>');
		$('#grading_list').next('span').find('input').val(answer.name);

	}
	$('#MS_dialog-gradding').dialog('close');
}

function viewGrading(){
	if($('#grading_list').val() != ''){
		var gradId = $('#grading_list').val();
		var module ={};
		module.name = 'marks';
		module.data = 'gradding&viewgrad='+gradId;
		module.title = getLang('gradding_shell');
		module.div = 'MS_dialog-gradding';
		var buttons = [{ 
			text: getLang('save'), 
			click: function() { 
				var submitSave = {
					name: 'marks',
					param: '&gradding&submit_gradding',
					post: $('#gradding_form').serialize(),
					callback: function(answer){
						evalSubmitGradding(answer)
					}
				}
				getModuleJson(submitSave);		
			}
		},{ 
			text: getLang('delete'), 
			click: function() { 
				deleteGrading();
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}];
		
		createAjaxDialog(module, buttons, false, 380, 500, false, '')	
	} else {
		return false;
	}
}

function deleteGrading(){
	if($('#gradding_form input[name="id"]').val() !=''){
		var gradId = $('#gradding_form input[name="id"]').val();
		MS_jsonRequest('marks&gradding&delete_gradding', 'id='+gradId, " evalDeleteGradding(answer)")	;
	} else {
		$('#MS_dialog-gradding').dialog('close');
	}
}

function evalDeleteGradding(answer){
	$('#grading_list option[value="'+answer.id+'"]').remove();
	$('#grading_list').next('span').find('input').val('');
	$('#MS_dialog-gradding').dialog('close');
}



/************************** Exams ***************************************/


function activateResults(dialog){
	$(dialog+' input.result').change(function(){
		var $td = $(this).parents('td');
		var $gradTd = $td.next('td');
		var ExamMax = $(dialog+' #exam_result input[name="max"]').val();
		var $tr = $td.parents('tr');
		var $chk = $tr.find('input[type="checkbox"]');
		
		var module = {};
		module.name = "marks";
		module.data = "gradding&getgrad="+$(dialog+" #exam_level_id").val()+'&max='+ExamMax+'&res='+$(this).val()
		module.div = $gradTd;
		//loadModuleToDiv(new Array(module), "");
		
		if($(this).val() != ""){
			$chk.attr('checked', 'checked');
		} else {
			$chk.attr('checked', '');
		}
		colorizeResult(dialog);
	})
	$(dialog+' select.result').change(function(){
		var $td = $(this).parents('td');
		var $gradTd = $td.next('td');
		var ExamMax = $(dialog+' #exam_result input[name="max"]').val();
		var $tr = $td.parents('tr');
		var $chk = $tr.find('input[type="checkbox"]');
		if($(this).val() != ""){
			$chk.attr('checked', 'checked');
		} else {
			$chk.attr('checked', '');
		}
	});
	$(dialog+" .tablesorter").trigger("update");
}

function colorizeResult(dialog){
		var valmax = parseInt($(dialog+' input[name="max"]').val());
		var valmin = parseInt($(dialog+' input[name="min"]').val());
	$(dialog+' input.result').each(function(){
		var result = parseInt($(this).val());
		if (result == ''){
			$(this).css("border-color", "red");
		} else if(result < valmin){
			$(this).css("color", "red");
		} else if(result >= valmin && result < valmax){
			$(this).css("color", "green");
		} else if (result == valmax){
			$(this).css("color", "blue");
		} else if (result > valmax){
			$(this).css("border-color", "orange");
			$(this).css("color", "orange");
		}
	});
}


/************** Addons **************************/
function getAddOnList(){
	var module = {};
	module.name = 'marks';
	module.data = 'terms&newaddonform&'+$('#terms_form').serialize();
	module.title = getLang('addons');
	module.div = 'MS_dialog-addons';
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			MS_jsonRequest('marks&terms&submit_add&'+$('#terms_form').serialize(), $('#new_addon_form').serialize(), "$('#MS_dialog-addons').dialog('close');reloadTermTable()")	;
		}
	},{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 500, 270, false, '')	
}

function openAddInfos(addId){
	var module = {};
	module.name = 'marks';
	module.data = 'terms&newaddonform&addid='+addId+'&'+$('#terms_form').serialize();
	module.title = getLang('addons');
	module.div = 'MS_dialog-addons';
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			MS_jsonRequest('marks&terms&submit_add&'+$('#terms_form').serialize(), $('#new_addon_form').serialize(), "$('#MS_dialog-addons').dialog('close');reloadTermTable()")	;
		}
	},{ 
		text: getLang('delete'), 
		click: function() { 
			MS_jsonRequest('marks&terms&delete_add&'+$('#terms_form').serialize(), $('#new_addon_form').serialize(), "$('#MS_dialog-addons').dialog('close');reloadTermTable()")	;
		}
	},{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 500, 270, false, '')	
}

function loadAddon(addId){
	var module ={};
	module.name = 'marks';
	module.data = 'terms&add_result_form&add_id='+addId+'&'+$('#marks_form').serialize();;
	module.title = getLang('addons');
	module.div = 'MS_dialog-addon_result';
	var buttons = [{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 500, 650, true, 'checkAddonApprov();activateResults()', false, false)	
}

function checkAddonApprov(){
 	var approved = $('#MS_dialog-addon_result #exam_approved').val();
	if( approved != '1'){
		var buttons = [{ 
			text: getLang('save'), 
			click: function() { 
				MS_jsonRequest('marks&terms&submit_addon_result', $('#MS_dialog-addon_result #exam_result').serialize(), "$('#MS_dialog-addon_result').dialog('close');reloadMarks()")	;
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}];
		$('#MS_dialog-addon_result').dialog("option",'buttons', buttons);
	} else  {
		$('#MS_dialog-addon_result #exam_result input, #MS_dialog-addon_result #exam_result select').attr('disabled', "disabled");
	}
}

function openStudentMarks(stdCode, stdName){
	var module ={};
	module.name= 'marks';
	module.title = getLang('student')+': '+stdName;
	module.data= 'con=student&con_id='+stdCode;
	module.type= 'GET';
	module.div = 'MS_dialog_students_marks-'+stdCode;
	dialogOpt = {
		buttons: [{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:1050,
		height:600,
		modal:false,
		minim:true,
		maxim:true
	}
	openAjaxDialog(module, dialogOpt);	
}


function enlargeChart(img){
	var html = '<img src="'+$(img).attr('src')+'" width="100%" />';
	var StdName = $(img).attr('alt');
	var buttons = [{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog("chart-"+StdName, StdName , html, 600, 320, buttons, false)	
	
}

/********* Certificates'************/
function reloadCertTable(){
	var $tab = $('#cert_div').parents('.ui-tabs-panel')
	var module = {};
	module.name = "marks";
	module.data = "reports&"+$('#cert_form').serialize();
	module.div = "#"+$tab.attr('id');
	loadModule(module);
	
}

function previewCertHtml($btn){
	var stdId = $btn.attr('std_id');
	var termId = $btn.attr('term_id');
	var module ={
		name: 'marks',
		title: getLang('marks'),
		data: 'reports&preview&con=student&con_id='+stdId+'&term_id='+termId,
		div : 'dialog_students_cert-'+stdId
	}
	dialogOpt = {
		buttons: [{ 
			text: getLang('print'), 
			click: function() { 
				printDialog($('#dialog_students_cert-'+stdId));
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		width:1050,
		height:600,
		modal:false,
		minim:true,
		maxim:true
	}
	openAjaxDialog(module, dialogOpt);	
}

function evalToolOpt(){
	var opt = $('#cert_tools_form #tool').val();
	if(opt == 'generate' || opt == 'regenerate' || opt == 'print'){
		$("#generate_options_tr").fadeIn();
	} else {
		$("#generate_options_tr").fadeOut();
	}
}

function loadCertTools(con, conId){
	var module ={};
	module.name= 'marks';
	module.title = getLang('tools');
	module.data= 'reports&tools&con='+con+'&con_id='+conId;
	module.type= 'GET';
	module.div = 'MS_dialog_cert-tools';
	dialogOpt = {
		buttons: [{ 
		text: getLang('ok'), 
		click: function() { 
			var option = new Array;
			$('#cert_tools_form input[name^="option_generate"]:checked').each(function(){
				option.push($(this).attr('name')+'=1');
			});
			var optionStr = option.join('&');
			var termId = $('#cert_tools_form #cur_term').val();
			var opt = $('#cert_tools_form #tool').val();
			var stdList = {
				name: 'marks',
				param: 'reports&getList&con='+con+'&con_id='+conId+'&overwrite='+(opt=='generate' ? '0' : '1'),
				post:'',
				callback: function(answer){					
					if(opt == 'print'){
						printMultiCert(answer.stdids, termId, optionStr);
					} else if(opt == 'download'){
						downloadCertificate(con, conId, termId);
					} else if(opt == 'generate' || opt == 'regenerate'){
						generateMultiCert(answer.stdids, termId, optionStr);
					} else {
						var action = {
							name: 'marks',
							param: 'reports&submit_tools&con='+con+'&con_id='+conId,
							post:$('#cert_tools_form').serialize(),
							callback: function(){
								reloadCertTable();
							}
						}
						getModuleJson(action);
					}
				}
			}
			getModuleJson(stdList);
			$(this).dialog('close');
		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:400,
		height:250,
		modal:false,
	}
	openAjaxDialog(module, dialogOpt);	
}

function generateCertificateCfm(con, conId, termId, overwrite){
	var module ={};
	module.name= 'marks';
	module.title = getLang('generate');
	module.data= 'reports&pdf_form&con='+con+'&con_id='+conId;
	module.type= 'GET';
	module.div = 'MS_dialog_cert-tools';
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() { 
			var stdIds = new Array();
			stdIds.push(conId);
			var option = new Array;
			$('#cert_tools_form input[name^="option_generate"]:checked').each(function(){
				option.push($(this).attr('name'));
			});
			var optionStr = option.join('&');
			generateMultiCert(stdIds, termId, optionStr);
			$(this).dialog('close');

		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:400,
		height:400,
		modal:false,
	}
	openAjaxDialog(module, dialogOpt);	
}

function generateMultiCert(stdIds, termId, option){
	$('#loading_main_div').hide();
	var total = stdIds.length;
	var html = '<div class="ui-corner-all ui-widget-content" style="padding:5px"><h3>'+getLang('generating')+'... <span id="cur_generate">1</span>/'+total+'</h3><div class="prograss_bar" id="generation_progress"></div><div style="margin:10px" class="UI-corner-all ui-state-highlight">'+getLang('generating_comment')+'</div>';
	var buttons = [{ 
		text: getLang('stop'), 
		click: function() { 
			window.generCert = 0;
			reloadCertTable();
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('generate_certificate', getLang('certficates'), html, 440, 250, buttons, true);
	$('#generation_progress').progressbar({value: total==1 ? 100 :1});
	window.generCert = 1;
	generateCert(stdIds.join(','), 1, termId, option);
}

function generateCert(stdIdsStr, x, termId, option){
	if(window.generCert == 1){
		var callback = '';
		stdIds = stdIdsStr.split(',');
		var stdId = stdIds[x-1];
		$("#cur_generate").html(x);
		var progVal = (x/stdIds.length) * 100;
		callback += "$('#generation_progress').progressbar('option', 'value',"+progVal+");";
		
		if(x < stdIds.length){
			callback += "generateCert('"+stdIdsStr+ "',"+ (x+1) +","+termId+", '"+option+"');";
		} else {
			callback += "$('#MS_dialog_generate_certificate').dialog('close');reloadCertTable();";

		}
		var action = {
			name: 'marks',
			param: 'reports&submit_tools&con=student&con_id='+stdId+'&cur_term='+ termId,
			post: 'tool=generate&'+option,
			callback: function(){
				//reloadCertTable();
			}
		}
		getModuleJson(action);
	}
}

function printCertificate(con, conId, termId){
	var module ={};
	module.name= 'marks';
	module.title = getLang('print');
	module.data= 'reports&pdf_form&con='+con+'&con_id='+conId;
	module.type= 'GET';
	module.div = 'MS_dialog_cert-tools';

	dialogOpt = {
		buttons: [{ 
		text: getLang('print'), 
		click: function() { 
			var stdIds = new Array();
			stdIds.push(conId);
			var option = new Array;
			$('#cert_tools_form input[name^="option_generate"]:checked').each(function(){
				option.push($(this).attr('name'));
			});
			var optionStr = option.join('&');
			printMultiCert(stdIds, termId, optionStr);
			$(this).dialog('close');
		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:400,
		height:220,
		modal:false,
		minim:true
	}
	openAjaxDialog(module, dialogOpt);	
}

function printMultiCert(stdIds, termId, option){
	$('#loading_main_div').hide();
	var output ='';
	var total = stdIds.length;
	var html = '<div class="ui-corner-all ui-widget-content" style="padding:5px"><h3>'+getLang('preparing')+'... <span id="cur_print">0</span>/'+total+'</h3><div class="prograss_bar" id="print_progress"></div><div style="margin:10px" class="ui-corner-all ui-state-highlight">'+getLang('generating_comment')+'</div>';
	html += '<form id="print_cert_form" target="_blank" method="POST" action="index.php?plugin=print" class="hidden">'+
		'<textarea name="print_content" id="print_content_textarea" ></textarea>'+
		'<input name="orientation" type="hidden" value="1"  />'+
	'</form>';

	var buttons = [{ 
		text: getLang('close'), 
		click: function() { 
			window.printCert = 0;
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('print_certificate', getLang('certficates'), html, 440, 250, buttons, true);
	$('#print_progress').progressbar({
		value: total==1 ? 100 :1
	});
	window.printCert = 1;
	printCertProg(stdIds.join(','), 1, termId, option);

}

function printCertProg(stdIdsStr, x, termId, option){
	if(window.printCert == 1){
		var callback = "$('#print_content_textarea').html($('#print_content_textarea').html()+answer);";
		stdIds = stdIdsStr.split(',');
		var stdId = stdIds[x-1];
		$("#cur_print").html(x);
		var progVal = (x/stdIds.length) * 100;
		callback += "$('#print_progress').progressbar('option', 'value',"+progVal+");";
		
		if(x < stdIds.length){
			callback += "printCertProg('"+stdIdsStr+ "',"+ (x+1) +","+termId+", '"+option+"');";
		} else {
			callback += "$('#MS_dialog_print_certificate form').submit();$('#MS_dialog_print_certificate').dialog('close');";

		}
		var data ='con=student&con_id='+stdId+'&term_id='+ termId;
		var module ={};
		module.name= 'marks';
		module.title = getLang('print');
		module.data= 'reports&preview&'+data+'&'+option;
		module.type= 'GET';
		var answer = MS_aJaxRequest(module, 'GET', true, callback);
	}
}

function downloadCertificate(con, conId, termId){
	var data = 'module=marks&reports&download_cert&con='+con+'&con_id='+conId+'&cur_term='+termId;
	window.open('index.php?'+data);
}

function approveStudent(stdId){
	var module ={};
	module.name= 'marks';
	module.title = getLang('approve');
	module.data= 'approve&final_approve&con=student&con_id='+stdId;
	module.type= 'GET';
	module.div = 'MS_dialog_final_approve';
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() {
			
		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:400,
		height:400,
		modal:true,
	}
	openAjaxDialog(module, dialogOpt);	
	
}
/*************** History *****************/
function reloadGpa($inp){
	var stdId = $inp.attr('stdid');
	var $scope = $('#gpa_div-'+stdId);
	var matId = $scope.find('select[name="services"]').val();
	var countYears = $scope.find('select[name="count_years"]').val();
		
	var $img = $scope.find('img.chart');
	$img.css({width: '48px'});
	//$img.attr('src', 'assets/img/mini_loading.gif');
	$img.load('assets/img/mini_loading.gif', function(){
		$img.attr('src', 'index.php?module=marks&gpa&chart&con=student&con_id='+stdId+'&mat_id='+matId+'&years='+countYears).css({width:'300px'})
	});

	var module = {
		name: 'marks',
		data: 'gpa&reload&con=student&con_id='+stdId+'&years='+countYears,
		div : $scope.find('.gpa_year_div'),
		title: 'GPA',
		callback: function(){
		}
	}
	loadModule(module);
}

/****************** Skills **************************/
function reloadSkills($select){
	var $form = $select.parents('form');
	var module = {};
	module.name = "marks";
	module.data = "skills&"+$form.serialize();
	module.div = $form.find(".skill_table_div");
	loadModule(module);
	
}

function openSkillsResults($btn){
	var skill_id= $btn.attr('skill_id');
	var con = $btn.attr('con');
	var conId = $btn.attr('con_id');
	var $form = $btn.parents('form');
	var termId = $form.find('select[name="terms"]').val();
	var module ={};
	module.name= 'marks';
	module.title = getLang('skills');
	module.data= 'skills&results&skill_id='+skill_id+'&con='+con+'&con_id='+conId+'&term_id='+termId;
	module.type= 'GET';
	module.div = 'MS_dialog_skills_result-'+skill_id;
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() {
			var submitSave = {
				name: 'marks',
				param: 'skills&save_results&con='+con+'&con_id='+conId,
				post: $('#MS_dialog_skills_result-'+skill_id+' form').serialize(),
				callback: function(){
					$('#MS_dialog_skills_result-'+skill_id).dialog('close');
				}
			}
			getModuleJson(submitSave);
		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}, { 
		text: getLang('print'), 
		click: function() { 
			printDialog($('#MS_dialog_skills_result-'+skill_id));
		}
	}],
		width:600,
		height:600,
		modal:true,
	}
	openAjaxDialog(module, dialogOpt);	
}

function openSkillPerStd($btn){
	var stdId = $btn.attr('std_id');
	var module = {
		name: 'marks',
		data: 'skills&con=student&con_id='+stdId+'&'+$('.skill_std_form').serialize(),
		title: getLang('skills'),
		div: 'skill_per_std-'+stdId
	}	
	dialogOpt = {
		buttons: [{ 
		text: getLang('save'), 
		click: function() {
			var submitSave = {
				name: 'marks',
				param: 'skills&save_results',
				post: $('#skill_per_std-'+stdId+' form').serialize(),
				callback: function(){
					$('#skill_per_std-'+stdId).dialog('close');
				}
			}
			getModuleJson(submitSave);
		}
	}, { 
		text: getLang('print'), 
		click: function() { 
			printDialog($('#skill_per_std-'+stdId));
		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:600,
		height:600,
		modal:true,
	}
	openAjaxDialog(module, dialogOpt);	
}

function openSkillReport($btn){
	var $form = $btn.parents('form');
	var stdId = $btn.attr('std_id');
	var module = {
		name: 'marks',
		data: 'skills&skill_report&con=student&con_id='+stdId+'&'+$form.serialize(),
		title: getLang('skills'),
		div: 'skill_per_std-'+stdId
	}	
	dialogOpt = {
		buttons: [{ 
		text: getLang('print'), 
		click: function() { 
			printDialog($('#skill_per_std-'+stdId));
		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}],
		width:600,
		height:600,
		modal:true,
	}
	openAjaxDialog(module, dialogOpt);	
}

function printSkillsReport($btn){
	var con = $btn.attr('con');
	var conId = $btn.attr('con_id');
	var $form = $btn.parents('form');

	var stdList = {
		name: 'marks',
		param: 'skills&list&con='+con+'&con_id='+conId+'&'+$form.serialize(),
		post:'',
		callback: function(answer){					
			//$('#loading_main_div').hide();
			var stdIds = answer.stdids.split(',');
			var total = stdIds.length;
			var html = '<div class="ui-corner-all ui-widget-content" style="padding:5px"><h3>'+getLang('generating')+'... <span id="cur_print">1</span>/'+total+'</h3><div class="prograss_bar" id="print_progress"></div><div style="margin:10px" class="ui-corner-all ui-state-highlight">'+getLang('generating_comment')+'</div>';
			html += '<form id="print_cert_form" target="_blank" method="POST" action="index.php?plugin=print" class="hidden">'+
				'<textarea name="print_content" id="print_content_textarea" ></textarea>'+
				'<input name="orientation" type="hidden" value="1"  />'+
				'<input type="submit" value="1"  />'+
			'</form>';
			dialogOpt = {
				buttons : [{ 
					text: getLang('close'), 
					click: function() { 
						window.printSkills = 0;
						$(this).dialog('close');
					}
				}],
				width:440,
				height:250,
				modal:true,
				div: 'print_skills_prog_dialog'
			}
			openHtmlDialog(html, dialogOpt);
			window.printSkills = 1;
			printStdSkills(answer.stdids, 1, $form.find('select[name="terms"]').val(), $form.find('select[name="services"]').val());
			$('#print_progress').progressbar({value: total==1 ? 100 :1});

		}
	}
	getModuleJson(stdList);
}

function printStdSkills(stdIdsStr, x, termId, serviceId){
	if(window.printSkills == 1){
	$('#loading_main_div').hide();
		stdIds = stdIdsStr.split(',');
		var stdId = stdIds[x-1];
		var progVal = (x/stdIds.length) * 100;		
		var module = {
			name: 'marks',
			data: 'skills&skill_report&con=student&con_id='+stdId+'&services='+serviceId+'&terms='+termId,
			title: getLang('skills'),
			muted: true,
			append:true,
			callback: function (answer){
				$('#print_progress').progressbar('option', 'value', progVal);
				if(x < stdIds.length){
					$("#cur_print").html(x);
					printStdSkills(stdIdsStr, (x+1),termId, serviceId);
				} else {
					printTag('#print_content_textarea');
					window.printSkills = 0;
			//		$('#print_cert_form').submit();
					$('#print_skills_prog_dialog').dialog('close');
		
				}
			},
			div:'#print_content_textarea'
		}	
		loadModule(module);
	}
}

function saveSkillsResults($btn){
	var stdId = $btn.attr('std_id');
	var submitSave = {
		name: 'marks',
		param: 'skills&save_results&con=student&con_id='+stdId,
		post: $btn.parents('form').serialize(),
		callback: function(){
		}
	}
	getModuleJson(submitSave);
}