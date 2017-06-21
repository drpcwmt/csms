// Calendre js
function iniCalender(){
	$('.calender').tooltip({items:'a', content: function() { return $(this).find('.tooltip').html()}, html: true});
}

function openCalendarDay($but){
	var cur = $but.attr('day');
	var module ={};
	module.name = 'calendar';
	module.data = 'day='+cur; //"day="+cur;
	module.title = getLang('calender');
	module.div = 'MS_dialog-calender_infos';
	var dialogOpt = {
		width:600,
		height:400,
		title:getLang('calender'),
		maxim:false,
		minim:false,
		modal:true,
		buttons: [{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openAjaxDialog(module, dialogOpt);	
}

function reloadCalendarDay(){
	var $dialog = $('#MS_dialog-calender_infos');
	var cur = $dialog.find('input[name="day"]').val();
	var module ={};
	module.name = 'calendar';
	module.data = 'day='+cur; //"day="+cur;
	module.title = getLang('calender');
	module.div = 'MS_dialog-calender_infos';
	module.mute = true;
	loadModule(module);	
}


function loadCalenderTable(){
	var module ={};
	module.name = 'calendar';
	module.data = '';
	module.title = getLang('calender');
	module.div = '#module_calendar';
	module.mute =true,
	module.callback = function(){
		iniCalender()
	}
	loadModule(module);
}

function addEvents($but){
	var module ={};
	module.name = 'calendar';
	module.data = "eventsform&day="+($but.attr('date')? $but.attr('date') : '');
	module.title = getLang('events');
	module.div = 'MS_dialog-events';
	var dialogOpt = {
		width:900,
		height:500,
		title:getLang('events'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if(validateForm('#addEventForm')){
					if($("#tot_con").val() == '' ){
						var cfmSchool = {
							width:450,
							height:160,
							title:getLang('events'),
							maxim:false,
							minim:false,
							div: 'MS_dialog_events_cfmSchool',
							buttons: [{ 
								text: getLang('yes'), 
								click: function() {
									$("#tot_con").val('etab-0') 
									saveEvent();
									$(this).dialog('close');
								}
							}, {
								text: getLang('no'), 
								click: function() { 
									$(this).dialog('close');
								}
							}]
						};
					
						openHtmlDialog(getLang('add_all_school_event'), cfmSchool)
					} else {
						saveEvent()
					}
				} else {
					saveEvent()
				}
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
		
	openAjaxDialog(module, dialogOpt)
}

function saveEvent(){
	var submitSave = {
		name: 'calendar',
		param: 'save_event',
		post: $('#addEventForm').serialize(),
		callback: function(answer){
			reloadCalendarDay();	
			loadCalenderTable();
			$('#MS_dialog-events').dialog('close');
		}
	}
	getModuleJson(submitSave);
}

function evalEventType(){
	var type = $('#event_type').val();
	if(type==0	){
		$('#time_fieldset').fadeOut();
	} else {
		$('#time_fieldset').fadeIn();
	}
}

function addbrowseStudents(){
	var module = {};
		module.name = "students";
		module.data = 'events&stdfp';
		module.div = "MS_dialog-student";
		module.title= getLang('students')
	var dialogOpt = {
		width:840,
		height:600,
		title:getLang('events'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				if($('#tot_con').val() != ''){
					var Recivers = $('#tot_con').val().split(',');
				} else {
					var Recivers = new Array;
				}
				$('#MS_dialog-student input[name="std_id[]"]:checked').each(function(){
					addCons('student-'+$(this).val(), $(this).parent().next('td').html()+'<em class="mini"> ('+getLang('student')+')</em>');
				})
				$('#MS_dialog-student input[name="class[]"]:checked').each(function(){
					addCons('class-'+$(this).val(), $(this).parent().text()+'<em class="mini"> ('+getLang('class')+')</em>');
				})
				$('#MS_dialog-student input[name="group[]"]:checked').each(function(){
					addCons('group-'+$(this).val(), $(this).parent().text()+'<em class="mini"> ('+getLang('group')+')</em>');
				})
				$('#MS_dialog-student').dialog('close');
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

function addCons(con, conName){
	if($('#tot_con').val() != ''){
		var Recivers = $('#tot_con').val().split(',');
	} else {
		var Recivers = new Array;
	}
	
	if(Recivers.indexOf(con) == -1){
		Recivers.push(con);
		$("#tot_con_text").append('<li class="ui-state-default hoverable">'+conName+'<span class="close ui-icon ui-icon-close" action="removeCon" con="'+con+'" /></span>');
	}
	$('#tot_con').val(Recivers.join(','));
	initiateJquery();
}

function removeCon($but){
	var con = $but.attr('con');
	
	if($('#tot_con').val() != ''){
		var Recivers = $('#tot_con').val().split(',');
	} else {
		var Recivers = new Array;
	}
	var index = Recivers.indexOf(con);
	if(index> -1){
		Recivers.splice(index, 1);
	}
	$('#tot_con').val(Recivers.join(','));	
	var $li = $but.parents('li').eq(0);
	$li.fadeOut().remove();
}

function addConByName(con){
	var html = '<form id="add_con_by_name"><table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="event_con_sug_div" type="text" class="input_double" /><input id="event_con_inp" class="autocomplete_value" type="hidden" /></td></tr></table></form>';

	var dialogOpt = {
		width:480,
		height:160,
		title:getLang(con),
		maxim:false,
		minim:false,
		div: 'MS_dialog_events_byname',
		buttons: [{ 
			text: getLang('ok'), 
			click: function() { 
				if(validateForm('#add_con_by_name')){
					conName = $('#MS_dialog_events_byname #event_con_sug_div').val();
					conId = $('#MS_dialog_events_byname #event_con_inp').val();
					addCons(con+'-'+conId, conName+'<em class="mini"> ('+getLang(con)+')</em>');
					$('#MS_dialog_events_byname').dialog('close');
				}
			}
		}, {
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};

	openHtmlDialog(html, dialogOpt)
	if(con == 'student'){
		loadModuleJS('students');
		setStudentAutocomplete('#MS_dialog_events_byname #event_con_sug_div','1');
	} else if(con == 'parent'){
		loadModuleJS('parents');
		setParentAutocomplete('#MS_dialog_events_byname #event_con_sug_div');
	} else {
		loadModuleJS('employers');
		setEmployerAutocomplete('#MS_dialog_events_byname #event_con_sug_div', 'status=1&group='+con);
	}
	
}

function addEventCon($but){
	var con = $but.attr('con')
	var conFilter = $but.attr('confilter');	
	if(con =='student' && conFilter == 'browse'){
		addbrowseStudents();
	} else if(con =='etab' && conFilter == '0'){
		addCons('etab-0', getLang('school'));
	} else {
			// all group
		if(conFilter ==0 ){
			addCons(con+'-0', getLang(con+'s'));
			// by Name
		} else if(conFilter == 'name'){
			addConByName(con);
		} else {
			var con = $but.attr('con');
			if(con.indexOf('-')){
				c = con.split('-');
				title = getLang(c[1]);
				addCons(con+'-'+conFilter, $but.text()+'<text class="mini">'+title+'</text>');
			} else {
				addCons($but.attr('con')+'-'+conFilter, $but.text()+'<text class="mini">'+getLang(con)+'</text>');
			}
			
			/*var module = {};
				module.name = "calendar";
				module.data = 'events&addcon='+con+'&confilter='+conFilter;
				module.div = "MS_dialog-addCon";
				module.title= getLang('event')
			var dialogOpt = {
				width:400,
				height:400,
				title:getLang(con),
				maxim:false,
				minim:false,
				buttons: [{ 
					text: getLang('add'), 
					click: function() { 
						$('#MS_dialog-addCon input[type="checkbox"]:checked').each(function(){
							addCons('student-'+$(this).val(), $(this).parent().html());
						})
						$('#MS_dialog-addCon').dialog('close');
					}
				},{ 
					text: getLang('close'), 
					click: function() { 
						$(this).dialog('close');
					}
				}]
			}
			openAjaxDialog(module, dialogOpt);*/
		}
		
	}
}


function delHoliday($but){
	var id = $but.attr('holidayid');
	var submitSave = {
		name: 'calendar',
		param: 'delete',
		post: 'hol='+id,
		callback: function(answer){
			$but.parents('tr').fadeOut().remove();
			loadCalenderTable();
		}
	}
	getModuleJson(submitSave);
}

function deleteEvent($but){
	var id = $but.attr('eventid');
	var submitSave = {
		name: 'calendar',
		param: 'delete',
		post: 'event='+id,
		callback: function(answer){
			$but.parents('tr').fadeOut().remove();
			loadCalenderTable();
		}
	}
	getModuleJson(submitSave);
}

function deleteHoliday($but){
	var id = $but.attr('holidayid');
	var submitSave = {
		name: 'calendar',
		param: 'delete',
		post: 'holidayid='+id,
		callback: function(answer){
			$but.parents('tr').fadeOut().remove();
			loadCalenderTable();
		}
	}
	getModuleJson(submitSave);
}

function editEvent($but){
	var id = $but.attr('eventid');
	var module ={};
	module.name = 'calendar';
	module.data = "openevent&event_id="+ $but.attr('eventid');
	module.title = getLang('events');
	module.div = 'MS_dialog-events';
	var dialogOpt = {
		width:900,
		height:500,
		title:getLang('events'),
		maxim:false,
		minim:false,
		modal:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				if(validateForm('#addEventForm') != false){
					if($("#tot_con").val() == '' ){
						var cfmSchool = {
							width:450,
							height:160,
							title:getLang('events'),
							maxim:false,
							minim:false,
							div: 'MS_dialog_events_cfmSchool',
							buttons: [{ 
								text: getLang('yes'), 
								click: function() {
									$("#tot_con").val('etab-0') 
									saveEvent();
									$(this).dialog('close');
								}
							}, {
								text: getLang('no'), 
								click: function() { 
									$(this).dialog('close');
								}
							}]
						};
					
						openHtmlDialog(getLang('add_all_school_event'), cfmSchool)
					} else {
						saveEvent()
					}
				} else {
					saveEvent()
				}
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]

	}

	openAjaxDialog(module, dialogOpt);
}

