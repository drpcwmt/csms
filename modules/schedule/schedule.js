// JavaScript Document
	// Read Schedule
function openSchedule($but){
	if($('#schedule_home_div').length == 0){
		$('#home_content').html('<div id="schedule_home_div" class="scope"></div>');
	}
	var module = {};
	module.name = 'schedule';
	module.div = '#schedule_home_div';
	module.title = getLang('my_schedule');
	module.data = 'con='+$but.attr('con')+'&con_id='+$but.attr('con_id');
	module.callback = function(){
		initTimeTable($('#schedule_home_div .week_line_ul li:first'))
	}
	loadModule(module);
	
}

function toggleDisplayEvents(handler){
	var $dialog = $(handler).parents('.ui-dialog');
	if($dialog.find('.calender_events:visible').length > 0){
		$(handler).html('<span class="ui-button-text"><span class="ui-icon ui-icon-calendar"></span>'+getLang('show_event')+'</span>');
		$dialog.find('.calender_events').hide();
	} else{
		$(handler).html('<span class="ui-button-text"><span class="ui-icon ui-icon-calendar"></span>'+getLang('hide_events')+'</span>');
		$dialog.find('.calender_events').show();
	}
}


function loadTimeTable($but){
	var $scope = $but.parents('.scope').eq(0);
	var weekNo = $but.attr('weekno');
	if($scope.find('.schedule-week-holder[weekno="'+weekNo+'"]').length > 0){
		$scope.find('.schedule-week-holder').hide();
		$scope.find('.schedule-week-holder[weekno="'+weekNo+'"]').show();
	} else {
		var con = $scope.attr('con');
		var conId = $scope.attr('con_id');
		var module = {
			name: "schedule",
			data: 'load_week&con='+con+'&con_id='+conId+'&week_no='+weekNo,
			type: "GET",
			div : $scope.find('.schedule-scroll'),
			title: getLang('schedule'),
			append: true,
			async: true,
			callback: function(){
				initTimeTable($but);
				$scope.find('.schedule-week-holder').hide();
				$scope.find('.schedule-week-holder[weekno="'+weekNo+'"]').show();
			}	
		}
		loadModule(module);
	}
}

function initTimeTable($but){
	var scope;
	if($but.hasClass('ui-tabs-anchor')){
		var $tab = $but.parents('.tabs').eq(0);
		$scope = $tab.find('.ui-tabs-panel:visible');
	} else {
		$scope = $but.parents('.scope');
	}
	iniLessonTooltip($scope);
	iniIndecitor($scope);
	$scope.find(".week_line li").unbind( "click" );
	$scope.find(".week_line li").click(function(){
		$scope.find(".week_line li").removeClass('ui-state-active');
		$(this).addClass('ui-state-active');
		loadTimeTable($(this));
	})	
}

function iniLessonTooltip($scope){
	$scope.find('.day_timeline').tooltip({items:'.tooltip-div', content: function() { return $(this).next('.tooltip').html()}, html: true});
}

function iniIndecitor($scope){
	$scope.find(".day_timeline li").hoverIntent(
		function(){
			var top = parseInt($(this).css('top'))+1;
			var height = $(this).outerHeight();
			var pad = ($(this).height() - 9 ) /2;
			$('li.indecitor').animate({
				opactcity: .5,
				top: top+'px',
				height:(height-pad-2)+'px'
			}, 300,'linear').css('padding-top', pad+'px').html(height+' mins');
		},
		function(){}
	).disableSelection();		
}
	// EDIT Schedules
function openEditDialog($but){ 
	var $scope = $but.parents('.scope').eq(0);
	var con = $scope.attr('con');
	var conId = $scope.attr('con_id');
	
	var module = {};
	module.name = 'schedule';
	module.data = 'con='+con+'&con_id='+conId+'&edit';
	module.title = getLang('schedule');
	module.type ="GET";
	module.div = 'dialog-edit_schedule';
	module.cache = false;
	module.callback = function(){ initEditScheduleForm();};

	dialogOpt = {
		buttons:[{ 
			text: getLang('close'), 
			click: function() { 
				var $weekLi = $scope.find('.week_line_ul li.ui-state-active');
				loadTimeTable($weekLi);
				$(this).dialog('close');
			}
		}],
		width:'90%',
		height:600,
		modal:true,
		callback: function(){
			initDefaulDaySelector();
			$('.session_accordion').accordion({active:false, collapsible:true, heightStyle: "content"});
		}
	}
	openAjaxDialog(module, dialogOpt);	
}

function selectEditOpt($li){
	var $scope =$li.parents('.scope');
	var type = $li.find('input[name="type"]').val();
	if(type == 'default'){
		$scope.find('.week_line_ul li').removeClass('ui-state-active');
		$scope.find('.week_line_ul li:first').addClass('ui-state-active');
		loadTimeTable($scope.find('.week_line_ul li:first'));
		
		$scope.find('.special_date_opt').slideUp().fadeOut();
		$li.find('input[name="type"]').attr('checked', 'checked');
		
		var $specialLi = $li.next('li');
		$specialLi.removeClass('ui-corner-top').addClass('ui-corner-all');
		
		var $form = $li.parents('form');
		$form.find('input[type="text"]').val(''); 
		$form.find('input[name="hide_ds"]').prop('checked', false);
	} else {
		$scope.find('.special_date_opt').slideDown().fadeIn();
		$li.find('input[name="type"]').attr('checked', 'checked');
		$li.addClass('ui-corner-top').removeClass('ui-corner-all');
	}
		
}

function loadEditTimeTable($but){
	var $scope = $('#dialog-edit_schedule');
	var $weekTimeline = $but.parents('ul.week_line_ul');
	$weekTimeline.find('li').removeClass('ui-state-active');
	$but.addClass('ui-state-active');
	var weekNo = $but.attr('weekno');
	var $form  = $scope.find('form[name="schedule_form"]');
	var data = 'edit&reload&week_no='+weekNo+'&'+$form.serialize();	
	var module = {};
	module.name = "schedule";
	module.data = data;
	module.type="GET";
	module.div = $scope.find(".schedule-scroll");
	module.title = getLang('schedule');
	module.callback = function(){
		$('#dialog-edit_schedule input[name="sessions"]').val('');
		initEditScheduleForm()
	};
	loadModule(module);
}

function reloadEditTimeTable(){
	var $scope = $('#dialog-edit_schedule');
	var $but = $scope.find('.week_line_ul li.ui-state-active');
	loadEditTimeTable($but);
}

function initEditScheduleForm(){
	var $scope = $('#dialog-edit_schedule');
	iniIndecitor($scope);
		
	$scope.find(".week_line li").unbind( "click" );
	$scope.find(".week_line li").click(function(){
		loadEditTimeTable($(this));
	})

	$scope.find(".day_timeline").each(function(){
		iniDayLineUl($(this));
	});
}

function iniDayLineUl($ul){
	var $scope = $('#dialog-edit_schedule');
	$ul.find("li").click(function(){
		$(this).toggleClass("ui-state-highlight faded");
		$scope.find('input[name="sessions"]').val('');
		var count = 0;
		var list = new Array();
		var mergeFound = false;
		$scope.find(".schedule-week-holder li").each(function(index){
			if($(this).hasClass('ui-state-highlight')){
				list.push($(this).attr("val"));
				count++;
			}
		});
		$scope.find('input[name="sessions"]').val(list.join(','));
		
			// delete selectio show or hide
		if(count> 0){
			$scope.find('.del_selc_li').fadeIn();
		} else {
			$scope.find('.del_selc_li').fadeOut();
		}
			// resize value
		if(count == 1 ){
			var begin = $(this).attr('begin');
			var end = $(this).attr('end');
			var max;	
			var $nextSession = $(this).next('li');
			if($nextSession.length>0){
				max = unixToTime($nextSession.attr('begin'));
			} else {
				var $autoGenerateForm = $scope.find('form[name="autogen_form"]')
				max = $autoGenerateForm.find('input[name="day_time_end"]').attr('placeholder');
			}
			var $form = $('form[name="resise_session_form"]');
			$form.find('input[name="begin"]').val(unixToTime(begin));
			$form.find('input[name="end"]').val(unixToTime(end));
			$form.find('.max_session_time').html(max);
		}
	}).
	disableSelection().css('cursor', 'pointer').
	css('cursor', 'pointer');
	
	//$ul.sortable();
}

function validateScheduleForm(){
	var $form = $('#dialog-edit_schedule form[name="schedule_form"]');
	var type = $form.find('input[name="type"]:checked').val();
	var error = 0;
	if(type != 'default'){
		$form.find('input.required').each(function(){
			if($(this).val() == ''){
				$(this).addClass('ui-state-error');
				error++;
			}
		});
	}
	if(error > 0 ){
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('fill_req_fields')+'</h2>');
		return false;
	} else {
		return true;
	}
}


function initDefaulDaySelector(){
	var $scope = $('#dialog-edit_schedule');	
	$scope.find(".def_day_selector li").click(function(){
		$(this).toggleClass("ui-state-active");
		var $ul = $(this).parent('ul');
		var $form = $ul.parents('form');
		var $def_input = $form.find('input[name="def_day"]');
		$def_input.val("");
		var list = new Array();
		$ul.find("li").each(function(){
			if($(this).hasClass("ui-state-active")){
				list.push($(this).attr("val"));
			}
		});
		$def_input.val(list.join(","));
	});
}

	// Sessions
	// join session
function joinSession($but){
	var $scope = $but.parents('.scope');
	sessions_str = $('#dialog-edit_schedule form[name="schedule_form"] input[name="sessions"]').val();
	if(sessions_str == ''){
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('nothing_selected')+'</h2>');
	} else {
		if(validateScheduleForm()){
			var sessions = sessions_str.split(',');
			if(sessions.length < 2){
				MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('join_error-select_two')+'</h2>');
				return false;
			}
			var date = '';
			var ses = new Array;
			for(x=0; x<sessions.length; x++){
				var session = sessions[x];
				s = session.split('-');
				if(date!= '' && date != s[0]){
					MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('join_error-same_date')+'</h2>');
					return false
				} else {
					date = s[0];
					ses.push(s[1]);
				}
			}
			ses.sort();
			for(x=0; x<(ses.length-1); x++){	
				if((parseInt(ses[x])+1) != parseInt(ses[x+1])){
					MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('join_error-non_sequential')+'</h2>');
					return false;
				}
			}
			var data = $('#dialog-edit_schedule form[name="schedule_form"]').serialize();
			MS_jsonRequest('schedule&edit&join', data, function(answer){
				var thisCon = $('#dialog-edit_schedule form[name="schedule_form"] input[name="con"]').val();
				var thisConId = $('#dialog-edit_schedule form[name="schedule_form"] input[name="con_id"]').val();
				$.each(answer.days, function(key, val) {
					var thisDay = key;
					var $contentTd = $('.schedule-holder[con="'+thisCon+'"][con_id="'+thisConId+'"]').find('ul.day_timeline[day="'+thisDay+'"]').parents('td').eq(0);
					$contentTd.html(val);
					iniDayLineUl($contentTd.find('ul.day_timeline'));
					iniIndecitor($contentTd);
				});
				$('#dialog-edit_schedule form[name="schedule_form"] input[name="sessions"]').val('');
			});
		}
	}
}
	// resize sessions
function resizeSession($but){
	if($('#dialog-edit_schedule form[name="schedule_form"] input[name="sessions"]').val() != ''){
		if(validateScheduleForm() && validateForm('#dialog-edit_schedule form[name="resise_session_form"]')){
			var end =timeToUnix( $('#dialog-edit_schedule form[name="resise_session_form"] input[name="end"]').val());
			var max  = timeToUnix($('#dialog-edit_schedule form[name="resise_session_form"] .max_session_time').html());
			if(end > max){
				MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('session_exceed_limit')+'</h2>');
			} else {
				var data = $('#dialog-edit_schedule form[name="schedule_form"]').serialize()+'&'+$('#dialog-edit_schedule form[name="resise_session_form"]').serialize();
				MS_jsonRequest('schedule&edit&resize', data, function(answer){
					var thisCon = $('#dialog-edit_schedule form[name="schedule_form"] input[name="con"]').val();
					var thisConId = $('#dialog-edit_schedule form[name="schedule_form"] input[name="con_id"]').val();
					$.each(answer.days, function(key, val) {
						var thisDay = key;
						var $contentTd = $('.schedule-holder[con="'+thisCon+'"][con_id="'+thisConId+'"]').find('ul.day_timeline[day="'+thisDay+'"]').parents('td').eq(0);
						$contentTd.html(val);
						iniDayLineUl($contentTd.find('ul.day_timeline'));
						iniIndecitor($contentTd);
					});
					$('#dialog-edit_schedule form[name="schedule_form"] input[name="sessions"]').val('');
				});
			}
		}
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('nothing_selected')+'</h2>');
	}
}
	// manual add session
function upEndTime($inp){
	var $scope = $inp.parents('.scope');
	var $timeField = $scope.find('input[name="lesson_time"]');
	var $timeBeginField = $scope.find('input[name="lesson_time_begin"]');
	var $timeEndField = $scope.find('input[name="lesson_time_end"]');

	var seconds = parseInt($timeField.val()) * 60;
	var timeBegin = timeToUnix($timeBeginField.val());
	$timeEndField.val( unixToTime( timeBegin + seconds ) )
} 

function upLessonTime($inp){
	var $scope = $inp.parents('.scope');
	var $timeField = $scope.find('input[name="lesson_time"]');
	var $timeBeginField = $scope.find('input[name="lesson_time_begin"]');
	var $timeEndField = $scope.find('input[name="lesson_time_end"]');

	var timeBegin = timeToUnix($timeBeginField.val());
	var timeEnd = timeToUnix($timeEndField.val());
	var val = (timeEnd  - timeBegin ) / 60 ;
	$timeField.val( (val > 1 ? val : '') )
}

function submitNewSession($but){ 
	var $scope=$but.parents('.scope');
	if(validateScheduleForm()){
		if($scope.find('input[name="lesson_time_begin"]').val() =='') {
			$scope.find('input[name="lesson_time_begin"]').addClass('ui-state-error');
			MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('must_fill_begin_time')+'</h2>');	
		} else if($scope.find('input[name="lesson_time_end"]').val() =='') {
			$scope.find('input[name="lesson_time_end"]').addClass('ui-state-error');
			MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('must_fill_end_time')+'</h2>');	
		} else if($scope.find('input[name="type"]:checked').length == 0) {
			MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('must_setect_session_type')+'</h2>');		
		} else {
			var data = $scope.find('form[name="add_session_form"]').serialize()+'&'+$('#dialog-edit_schedule form[name="schedule_form"]').serialize();
			MS_jsonRequest('schedule&edit&submitsessions', data, function(answer, $but){
				reloadEditTimeTable($but);
				$('input[name="lesson_time_begin"]').val($('input[name="lesson_time_end"]').val());
				upEndTime($('input[name="lesson_time_begin"]'));
			});
		} 
	}
}

	// auto generate sessions
function displayBreakTimes($inp){
	var countBreaks = parseInt($inp.val());
	var difference = (countBreaks - $('#breaks_ul li').length);
	var count = $('#breaks_ul li').length;
	for(x=1; x<= Math.abs(difference); x++){
		var index = count+x;
		if(difference > 0){
			$('#breaks_ul').append('<li><label class="label ui-widget-header ui-corner-left required" style="width:100px; display:inline-block">'+getLang('break')+' '+ index +':</label><input type="text" name="break_time'+ index +'" class="input_half ui-corner-right ui-state-default" /> '+getLang('minuts')+'</li>');
		} else {
			$('#auto_generate_sessions li:last').fadeOut().remove();
		}
	}
}

function autoGenerateSessions(){
	if(validateScheduleForm() && validateForm('#dialog-edit_schedule form[name="autogen_form"]')){
		var data = $('#dialog-edit_schedule form[name="schedule_form"]').serialize();
		data += '&'+$('#dialog-edit_schedule form[name="autogen_form"]').serialize();
		
		MS_jsonRequest(
			'schedule&edit&autogensession', 
			data,
			function(answer){
				reloadEditTimeTable();
			}
		);
	}
}
	// copy
function updateCopyConid($input){
	var con = $input.val();
	var data = $('#dialog-edit_schedule form[name="schedule_form"]').serialize()
	var module = {};
	module.name = 'schedule';
	module.title = getLang('schedule');
	module.data = 'edit&copyfrom&getcon='+con+'&'+data;
	module.div = '#copyconId';
	module.type = 'GET';
	module.async =false;
	module.callback = function(){
		$('#dialog-edit_schedule form select[name="copycon"]').combobox('destroy').combobox();
	}
	loadModule(module);
}

function copySessionTime(){
	if(validateScheduleForm() && validateForm('#dialog-edit_schedule form[name="copy_form"]')){
		var data = $('#dialog-edit_schedule form[name="schedule_form"]').serialize();
		data += '&'+$('#dialog-edit_schedule form[name="copy_form"]').serialize();
		
		MS_jsonRequest(
			'schedule&edit&submitcopy', 
			data,
			function(answer){
				reloadEditTimeTable();
			}
		);
	}
}

	// Lessons
function attributLesson($but){
	if( $('#dialog-edit_schedule input[name="sessions"]').val() !=''){
		var module ={
			name : 'schedule',
			data : 'edit&attrlessonform&'+$('#dialog-edit_schedule form[name="schedule_form"]').serialize(),
			title: getLang('schedule'),
			div  : 'edit_schedule_squences',
			cache: false
		};
		
		var dialogOptions = {
			width: '500',
			height: '540',
			minim: false,
			maxim: false,
			cache:false,
			modal:true,
			buttons:[{ 
				text: getLang('save'), 
				click: function() { 
					submitLessonForm()
				}
			},{ 
				text: getLang('close'), 
				click: function() { 
					$(this).dialog('close');
				}
			}]
		};
		openAjaxDialog(module,dialogOptions);
	} else {
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('nothing_selected')+'</h2>');
	}
}

function setFrequency($but){
	var freq = $but.attr('value');
	var $tr = $('#lessons_table tr.division_weeks_tr:first');
	var countWeeks = $tr.children('td').length -1;
	var lessonHtml = $tr.find('td').eq(1).html();
	if(freq > 1){
		$('#edit_schedule_squences').dialog('option', 'width', (freq*280)+60 );
	} else {
		$('#edit_schedule_squences').dialog('option', 'width', 500 );
	}
	 $("#edit_schedule_squences").dialog("option", "position", "center");
	if(freq > countWeeks){
		$('#lessons_table tr.division_weeks_tr').each(function(){
			var $thisTr = $(this);
			for(x=(countWeeks+1); x<=freq; x++){
				$thisTr.append('<td>'+lessonHtml+'</td>');
			}	
		});
	} else if(freq < countWeeks){
		$('#lessons_table tr.division_weeks_tr').each(function(){
			var $thisTr = $(this);
			for(x=(countWeeks+1); x>(freq+1); x--){
				$thisTr.children('td:last').fadeOut().remove();
			}	
		});
	}
	refreshLessonAttribut();
}

function setDivision($but){
	var groups = $but.attr('value');
	var countTrs = $('#lessons_table tr.division_weeks_tr').length;
	var $tr = $('#lessons_table tr.division_weeks_tr:first');
	var trHtml = $tr.html();
	if(groups > countTrs){
		for(x=(countTrs+1); x<=groups; x++){
			$('#lessons_table').append('<tr class="division_weeks_tr">'+trHtml+'</tr>');
		}	
	} else if(groups < countTrs){
		for(x=groups; x<countTrs; x++){
			$('#lessons_table').find('tr.division_weeks_tr:last').fadeOut().remove();
		}	
	}
	if(groups == 1){
		$('#lessons_table .division_tr').fadeOut();
		$('#lessons_table .division_tr select option:first').attr('selected', 'selected');
		reloadService($('#lessons_table .division_tr select:first'));
		$('#lessons_table .division_weeks_tr td:first h3').html(getLang('all'));
	} else {
		var index = 1;
		$('#lessons_table .division_weeks_tr').each(function(){
			$(this).find('td:first h3').html(getLang('group')+' - '+ index);
			index++;
		});
		$('#lessons_table .division_tr').fadeIn();
	}
	refreshLessonAttribut();
}

function refreshLessonAttribut(){
	var $table = $('#lessons_table');
	$table.find('.MS_formed_update').removeClass('MS_formed_update');
	iniInputUpdate();
	$('#lessons_table tr.division_weeks_tr').each(function(){
		var $thisTr = $(this);
		var countWeeks = $thisTr.children('td').length -1;
		$thisTr.find('td').each(function(){
			if($(this).index()>0){
				$(this).find('legend').html(getLang('week')+ ' '+ $(this).index());
				$(this).find('input[name="rule[]"]').val($(this).index()+'/'+countWeeks);
			}
		})
	});	
}

function refreshSelectGroup($select){
	var $tr = $select.parents('tr.division_weeks_tr');
	var value = $select.val();
	$tr.find('td:first h3').html($select.find('option:selected').html());
	$tr.find('select[name="lesson_con_id[]"] option[value='+value+']').each(function(){
		$(this).attr('selected', 'selected');
	});
}

function reloadService($select){
	refreshSelectGroup($select);
	var $fieldset = $select.parents('fieldset');
	var $selectService = $fieldset.find('select[name="service[]"]');
	var con_str=$fieldset.find('*[name="lesson_con_id[]"]').val();
	var d = $select.val().split('-');
	var con=d[0];
	var conId = d[1];
	var module = {};
	module.name = 'schedule';
	module.data = 'edit&reloadservices&con='+con+'&con_id='+conId;
	module.title = getLang('schedule');
	module.div = $selectService
	loadModule(module, function(){
		var $tr = $select.parents('tr.division_weeks_tr');
		$tr.find('select[name="service[]"]').html($selectService.html());
	});
	
}

function reloadProfs($select){
	var $fieldset = $select.parents('fieldset');
	var $selectService = $fieldset.find('select[name="service[]"]');
	if($selectService.val() != ''){
		var service = $selectService.val();
		var con_str=$fieldset.find('*[name="lesson_con_id[]"]').val();
		var d = con_str.split('-');
		var con=d[0];
		var conId = d[1];
	
		var module = {};
		module.name = 'schedule';
		module.title = getLang('schedule');
		module.data = 'edit&reloadprofs&service='+service+'&'+$('#dialog-edit_schedule form[name="schedule_form"]').serialize();
		if( $select.find('option:selected').attr('all')!= 'all' ){
			module.data += '&avaible';
		}
		module.div = $fieldset.find('select[name="profs[]"]');
		loadModule(module);
	}
}

function reloadHalls($select){
	var $fieldset = $select.parents('fieldset');
	var con_str=$fieldset.find('*[name="lesson_con_id[]"]').val();
	var d = con_str.split('-');
	var con=d[0];
	var conId = d[1];

	var module = {};
	module.name = 'schedule';
	module.title = getLang('schedule');
	module.data = 'edit&reloadhalls&'+$('#dialog-edit_schedule form[name="schedule_form"]').serialize();
	if( $select.find('option:selected').attr('all')!= 'all' ){
		module.data += '&avaible';
	}
	module.div = $fieldset.find('select[name="hall[]"]');
	loadModule(module);
}

function submitLessonForm(){
	var Error = false;
	var $table = $('#lessons_table');

	$table.find('select').each(function(){
		if($(this).val() == ''){
			$(this).addClass("ui-state-error");
			Error = true;
		}
	});
		
	if(Error){
		MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error-empty_files')+'</h2>');
		return false;
	} else {
		var data = $('#edit_schedule_squences form').serialize()+'&'+$('#dialog-edit_schedule form[name="schedule_form"]').serialize();
		MS_jsonRequest('schedule&edit&submitlesson', data, function(answer){
			$('#edit_schedule_squences').dialog('close');
			var thisCon = $('#dialog-edit_schedule form[name="schedule_form"] input[name="con"]').val();
			var thisConId = $('#dialog-edit_schedule form[name="schedule_form"] input[name="con_id"]').val();
			$.each(answer.cells, function(key, val) {
				var thisSeq = key;
				$('.schedule-holder[con="'+thisCon+'"][con_id="'+thisConId+'"]').find('li[val="'+thisSeq+'"]').html(val).removeClass('faded').removeClass('ui-state-highlight')
			});
			$('#dialog-edit_schedule form[name="schedule_form"] input[name="sessions"]').val('');
		});
	}
}

	// Reset
function selectResetDate($li){
	$li.find('input').attr('checked', 'checked');
}

function selectResetOpt($li){
	$li.find('input[name="del_radio"]').attr('checked', 'checked');
}

function submitReset($but){
	var $scope = $but.parents('.scope');
	var data = $scope.find('form[name="reset_form"]').serialize()+'&'+$('#dialog-edit_schedule form[name="schedule_form"]').serialize();
	var deleteWhat = $scope.find('input[name="del_type"]:checked').val();
	var type = $scope.find('input[name="del_radio"]:checked').val();
	if(type == 'selc'){
		if($scope.find('input[name="selcLessons"]').val() == '') { 
			MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('nothing_selected')+'</h2>');
			return false;
		}
	} else if(type == 'spf'){
		if($scope.find('input[name="del_begin_date"]').val() == '' || $scope.find('input[name="del_end_date"]').val()) {
			if($scope.find('input[name="del_begin_date"]').val() == '') {
				$scope.find('input[name="del_begin_date"]').addClass('ferror');
			}
			if($scope.find('input[name="del_end_date"]').val() == '') {
				$scope.find('input[name="del_end_date"]').addClass('ferror');
			}
			MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('fill_req_fields')+'</h2>');
			return false;
		} else {
			$scope.find('input[name="del_begin_date"], input[name="del_end_date"]').removeClass('ferror');
		}
	}

	MS_jsonRequest('schedule&edit&reset', data, function(answer){
		if(type == 'selc'){
			var thisCon = $('#dialog-edit_schedule form[name="schedule_form"] input[name="con"]').val();
			var thisConId = $('#dialog-edit_schedule form[name="schedule_form"] input[name="con_id"]').val();
			if(deleteWhat == 'lessons'){
				if(answer.cells){
					$.each(answer.cells, function(key, val) {
						var thisSeq = key;
						$('.schedule-holder[con="'+thisCon+'"][con_id="'+thisConId+'"]').find('li[val="'+thisSeq+'"]').html(val).removeClass('faded').removeClass('ui-state-highlight')
					});
				}
			} else {
				var sessions_str = $('#dialog-edit_schedule form[name="schedule_form"] input[name="sessions"]').val();
				var sessions = sessions_str.split(',');
				for(x=0; x<sessions.length; x++){
					$('.schedule-holder[con="'+thisCon+'"][con_id="'+thisConId+'"]').find('li[val="'+sessions[x]+'"]').fadeOut().remove();
				}
			}
		} else {
			reloadEditTimeTable();	
		}
	});
}

// ************************************* Statics 
function addBlackTime(but){
	var $parent = $(but).parents('.ui-widget-content');
	var con = $parent.find('#schedule_con').val();
	var conId = $parent.find('#schedule_con_id').val();
	var module = {};
	module.name = 'schedule';
	module.data = 'static&apf&con='+con+'&con_id='+conId;
	module.title = getLang('schedule');
	module.div = 'schedule_black_squences';
	var buttons = [{ 
		text: getLang('ok'), 
		click: function() { 
			if(validateForm('#schedule_black_squences #add_form')){
				if($('#schedule_black_squences #add_form').find('input[name="time_type"]:checked').val() == 0){
					if($('#schedule_black_squences #add_form').find('input[name="def_day"]').val() == ''){
						MS_alert('<img src="assets/img/error.png" />'+getLang('must_select_least_day'));
						return false;
					}
				}
				MS_jsonRequest('schedule&static&add&con='+con+'&con_id='+conId, $('#schedule_black_squences #add_form').serialize(), '$("#schedule_black_squences").dialog("close");');
			}
		}
	},{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 450, 260, true, 'initDefaulDaySelector()', false);	
}

function evalBlackSeqSubmit(ans){
	$('#lesson_time_begin').val('');
	$('#lesson_time_end').val('');
	$('#lessons_time').val('');
	$('#dialog_from').dialog('close');
	loadTimeTable();
}

function deleteBlackTime(thisSeq, seqId){
	var $spot = $('#thisSeq');
	var html = '<div class="ui-corner-all ui-state-highlight" style="padding:5px 10px"><h3>'+getLang('delete_black_time')+'</h3></div>';
	var buttons = [{ 
		text: getLang('delete'), 
		click: function() { 
			MS_jsonRequest('schedule&static&delc&con='+$('#schedule_con').val()+'&con_id='+$('#schedule_con_id').val(),'ids='+seqId, '$("#black_time-'+seqId+'").fadeOut()');
			$(this).dialog('close');

		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('DeleteSeqTime', getLang('schedule'), html, 440, 220, buttons, false);
	
}

