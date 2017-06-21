// JavaScript Document

function addMedicalVisit($but){
	var data = '';
	if($but.attr('stdid')){
		data = 'std_id='+$but.attr('stdid');
	}
	var module = {
		name :'medical',
		title: getLang('medical'),
		data: 'visite_form&'+data,
		div: 'medical_form',
		callback: function(){
			setStudentAutocomplete( $('#medical_form input[name="student_name"]'), '1')
		}
	}
	
	var dialogOpt = {
		width: 600,
		height: 400,
		minim:false,
		maxim:false,
		buttons: [{
			text: getLang('save'),
			click: function(){
				var saveVisit = {
					name:'medical',
					title	: getLang('medical'),
					param: 'save_visit',
					post: $('#medical_form form').serialize(),
					callback :function(answer){
						$('.medical_hisory_ul[stdid="'+answer.id+'"]').prepend(answer.html);
					}
				}
				getModuleJson(saveVisit);
				$(this).dialog('close');
			}
		}, {
			text: getLang('cancel'),
			click: function(){
				$(this).dialog('close');	
			}
		}]
	}
		
	openAjaxDialog(module, dialogOpt);	
}

function deleteVisite($but){
	var delVisit = {
		name:'medical',
		title	: getLang('medical'),
		param: 'delete_visit',
		post: 'visit_id='+$but.attr('visitid'),
		callback :function(answer){
			$but.parent('li').fadeOut().remove();
		}
	}
	getModuleJson(delVisit);
	
}
