// Global function
// Ver 3 Beta

	// Javascript Library
window.loadedModules = new Array;

	// Json config File
window.configFile = ''
$.ajax({
    url: 'config/config.json.php',
    dataType: 'json',
    async: true,
    success: function(data) {
        configFile = data;
		// Check Debug Mode
		if(configFile.debugMode == '1'){
			setTimeout(function(){
				$('body').append('<div class="debug_mode_label">Debug Mode</div>')
				$('.debug_mode_label').fadeIn(1500);
			}, 1000)
		}


      //  return data;
    }
});
	

	// Handel session ajax response

$.ajaxSetup({
	statusCode: {
		404: function() {
			MS_alert('<h2><img src="assets/img/error.png" /> '+getLang('error')+'</h2>');
			showLoading('hide');
			return false;
		},
		500: function() {
			MS_alert('<h2><img src="assets/img/error.png" /> '+getLang('error')+'</h2>');
			showLoading('hide');
			return false;
		}
	},
	success: function(e, xhr, settings) {
		if(xhr.responseText == 'session timeout'){
			disconect(getLang('timeout'));
			return false;
		} 
	}
});

// load  startup Scripts
function loadMSScripts(scripts){
	var scrLeft = document.documentElement.scrollLeft;
	var scrTop = document.documentElement.scrollTop;
	var Top = ($(window).height() / 2) + scrTop;
	var Left = ($(window).width() / 2) + scrLeft;
	$("#loading_main_div").css({top: Top, left:(Left-200)});									 	
	var files = scripts.split(',');
	var count = files.length;
	var step = 100/count;
	
	showLoading('show', '', true);
	for(x=0; x<count; x++){
		if( files[x].indexOf('.js') > 0){
			dtype= 'script';
		} else {
			dtype= 'text';
		}
		$("#loading_main_div span.stat").removeClass('ui-state-error').html(files[x]);
		$.ajax({
			url : files[x],
			async:false,
			dataType: dtype,
			success:function(answer){
				if( files[x].indexOf('.css') > 0){
					var html = '<style>'+answer+'</style>';
					$('head').append(html);
				}
				var value = $( "#loading_progress" ).progressbar( "option", "value" );
				showLoading('progress', (value+step));
			}
		})
	}
	showLoading('progress', 100);
	showLoading('hide');
}

function loadModuleJS(MS_moduleName){
	if(window.loadedModules.indexOf(MS_moduleName) == -1){
		$.ajax({
			url: 'modules/'+MS_moduleName+'/'+MS_moduleName+'.js',
			dataType: "script",
			async: false,
			success: function(){
				window.loadedModules.push(MS_moduleName);
			}
		});
	}
}

function loadPluginJS(MS_pluginName){
	if(window.loadedModules.indexOf(MS_pluginName) == -1){
		$.ajax({
			url: 'plugin/'+MS_pluginName+'/'+MS_pluginName+'.js',
			dataType: "script",
			async: false,
			success: function(answer){
				$('head').append('<script type="text/javascript">'+answer+'</script>');
				window.loadedModules.push(MS_pluginName);
			}
		});
	}
}

function loadModule(module, callback){
	var type =  module.type ? module.type : 'GET';
	var muted = module.mute && module.mute == true? true: false;
	
	if(!muted) { 
		showLoading('show', module.title, module.async);
	}
	
	loadModuleJS(module.name);
	if(!muted) {
		showLoading('progress',50);
	}
	if(module.div){
		MS_aJaxRequest(module, type, true, 
			function(answer){
				if(module.div instanceof jQuery){
					var $div = module.div;
				} else {
					var divId = module.div;
					var moduleId = divId.indexOf('#') == 0 ? module.div : '#'+module.div;
					var $div = $(moduleId);
				}
				if(module.append){
					$div.append(answer);
				} else {
					$div.html(answer);
				}
				if(!muted) {
					showLoading('progress',100);
				}
				initiateJquery();
				if(module.callback){
					processCallback(module.callback, answer);
				}
				processCallback(callback);
				if(!muted) {
					showLoading('hide');
				}
			}
		)
	}
}

var loadingTime = new Date();
var beforeLoading = loadingTime.getTime();
function showLoading(key, value, overlay){
	var step = 100 / parseInt(window.configFile.maxExec);
	
	if(key == 'show'){
		if(overlay){
			var loadingIndex = $("#loading_main_div").css("z-index");
			$('#loading_overlay').css("z-index", (loadingIndex-1));
			$('#loading_overlay').fadeIn();
		}
		var beforeLoading = loadingTime.getTime();
		$( "#loading_progress" ).progressbar({value: 1});
		var nowVal = 2 * step;
		setTimeout('showLoading("progress", '+nowVal+')', 1000);
		if(value){
			if($("#loading_main_div").children('span.stat').length == 0){
				$("#loading_main_div").append('<span class="stat"></span>');
			}
			$("#loading_main_div span.stat").html(value);
		} else {
			$("#loading_main_div span.stat").html('&nbsp;');
		}
		$("#loading_main_div").fadeIn(300);
		setTimeout("showLoading('hide')", parseInt(window.configFile['maxExec']) * 1000);
	} else if(key == 'hide'){
		$("#loading_main_div, #loading_overlay").fadeOut(300);
	} else if(key == 'progress'){
		if(value > $( "#loading_progress" ).progressbar( "option", "value")){
			$( "#loading_progress" ).progressbar( "option", "value", value);
			var now = loadingTime.getTime();
			var nowVal = ((now - beforeLoading) /1000) * step;
			if(nowVal > value){
				value = nowVal;
			}
		}
		setTimeout('showLoading("progress", '+value+')', 1000);
	}else if(key == 'title'){
		$("#loading_main_div span.stat").html(value);
	}
}

var step = 0;
function loadMultiModules(modules, callback, mute){
	var counts = modules.length;
	var module = modules[step];
	if(!mute){
		showLoading('show', module.title, module.async);
	}
	var maxSteps = counts;
		// load JS
	loadModuleJS(module.name);
	if(!mute){
		showLoading('progress', ((step/maxSteps*2)*100) );
	}
		// load HTML
	var type = module.type ? module.type : 'GET';
	var Mcallback = module.callback ? module.callback : '';
	var answer = MS_aJaxRequest(module, type, true, function(answer){
		isObj = module.div instanceof jQuery;
		if(isObj){
			var $div = module.div;
		} else {
			var $div = $(module.div);
		}
		if(module.append){
			$div.append(answer);
		} else {
			$div.html(answer);
		}
		if(!mute){
			showLoading('progress', ((step+1 /maxSteps*2)*100) );
		}
		initiateJquery();
		processCallback(Mcallback, answer);
		if(step < (counts-1) ){
			step++;
			loadMultiModules(modules, callback);
		} else {
			// last module request
			if(callback != ''){
				processCallback(callback,answer);
			}
			step = 0;
			if(!mute){
				showLoading('hide');
			}
		}
	});
}

function loadModuleToDiv(modules, callback){
	var answer ={};
	var counts = modules.length;
	var steps = 100/counts;
	$( "#loading_progress" ).progressbar( "option", "value", 5);
	if($("#loading_main_div").children('span.stat').length > 0){
	} else {
		$("#loading_main_div").append('<span class="stat"></span>');
	}
	var $statsDiv = $("#loading_main_div span.stat").html('');

	$("#loading_main_div").fadeIn(300, function(){
		for(x=0; x< counts; x++){
			var module = modules[x];
			loadModuleJS(module.name);
			var url = 'index.php?module='+module.name;
			var targetDiv = module.div;
			var title = module.title;
			var data = module.data ? module.data : '';
			var type = module.type ? module.type : 'GET';
			var callback = module.callback ? module.callback : '';
			$statsDiv.html(title);
			var answer = MS_aJaxRequest(module, type, false, function(answer){			
				var $higherLayer = returnHigherLayer();
				if($($higherLayer).find(targetDiv).html()){
					$($higherLayer).find(targetDiv).html(answer);
				} else {
					$(targetDiv).html(answer);
				}
				initiateJquery();
	
				var value = $( "#loading_progress" ).progressbar( "option", "value" );
				$('#loading_progress').progressbar( "option", "value", (value+steps) );
			});
			if( x == counts - 1 ){
				eval(callback); // this command is depricated and should be not used in future
			}
		}
	});
	$('#loading_progress').progressbar( "option", "value", 100 );
	$("#loading_main_div").fadeOut(500);
}

// Load modules 
function loadMainModules( MS_moduleName, MS_moduleTitle, data, callback){
	var $dataMainTd =$('#main_modules_td');	
	var moduleDiv = 'module_'+MS_moduleName;
	var $moduleDiv = $dataMainTd.children('#'+moduleDiv);
	var module = {};
	module.name = MS_moduleName;
	module.title = MS_moduleTitle;
	module.div = '#'+moduleDiv;
	module.data = data;
	module.append= false;
	module.callback= function(){
		toogleModule('#'+moduleDiv);
		if(callback){
			eval(callback+'()');
		}
	};
		// insert div if not exists
	if($moduleDiv.length == 0 ){
		$dataMainTd.append('<div id="'+moduleDiv+'" class="MS_ModuleDiv hidden"></div>');
		loadModule(module);
		// reload div if visible
	}else if($dataMainTd.find('#'+moduleDiv+':visible').length > 0){
		loadModule(module);
	} else{
		toogleModule('#'+moduleDiv);
	}
}

function toogleModule(moduleDiv){
	$('div.MS_ModuleDiv').not(moduleDiv).fadeOut();
	setTimeout("$('"+moduleDiv+"').slideDown().fadeIn()", 500);
}

// DIALOGS  
function createHtmlDialog(id, title, html, width, height, buts, modal){
	if($('#MS_dialog_'+id).length > 0){
		$('#MS_dialog_'+id).html(html).dialog({buttons: buts}).dialog('open');
	} else {
		$dialog = $('<div>');
		$dialog.attr('title', title);
		$dialog.attr('id', 'MS_dialog_'+id);
		$dialog.addClass('hidden');
		if(html != '') $dialog.html(html);
		$dialog.appendTo(document);
		$dialog.dialog({
			modal: modal && modal==true ? true : false,
			width: width && width!='' ? width : 400,
			height: height && height!='' ? height : 300,
			buttons: buts 
		});
	}
	formStyle('#MS_dialog_'+id);	
}

function createAjaxDialog(module, buts, cache, width, height, modal, callback, minimizable, maximizable){
	if($('#'+module.div).length > 0){
		module.div = '#'+module.div
		var $moduleDiv = $(module.div);
		$moduleDiv.dialog({title:module.title});
		$moduleDiv.dialog({buttons: buts});
		loadModule(module, "$('"+module.div+"').dialog('open');"+callback);
	} else { 
		$dialog = $('<div>');
		$dialog.attr('title', module.title);
		$dialog.attr('id', module.div);
		$dialog.appendTo(document);
		$dialog.dialog({
			modal: modal && modal==true ? true : false,
			width: width && width!='' ? width : 0,
			height: height && height!='' ? height : 0,
			buttons: buts,
			autoOpen: false
		});
		var dialogExtendOptions = {
		  "closable" : true,
		  "maximizable" :maximizable ==true ? true : false ,
		  "minimizable" : minimizable ==false ? false : true ,
		  "minimizeLocation" :  'left',
		  "collapsable" : false,
		  "dblclick" : false,
		  "titlebar" : false
		};
		$dialog.dialogExtend(dialogExtendOptions);
		module.data += '&dialog';
		module.div = '#'+module.div;
		loadModule(module, "$('"+module.div+"').dialog('open');"+callback);
	}
}

function openAjaxDialog(module, opts){
	var buttons = opts.buttons ? opts.buttons : false;
	var width = opts.width ? opts.width : 0;
	var height = opts.height ? opts.height : 0;
	var maxim = opts.maxim && opts.maxim==true ? true : false;
	var minim = opts.minim && opts.minim==true ? true : false;
	var callback = opts.callback ? opts.callback : '';
	var cache = opts.cache && opts.cache==true ? true : false;
	var modal = opts.modal && opts.modal==true ? true : false;
	
	module.data += '&dialog';
	moduleId = module.div.indexOf('#') == 0 ? module.div : '#'+module.div;
	if($(moduleId).length > 0 ){
		var $moduleDiv = $(moduleId);
		$moduleDiv.dialog({title:module.title});
		$moduleDiv.dialog({buttons: buttons});
		if(cache == false || window.configFile.debugMode == 1){
			loadModule(module, function(){
				$moduleDiv.dialog('open');
				if(opts.callback) processCallback(opts.callback);
			});
		} else {
			$moduleDiv.dialog('open');
			processCallback(opts.callback);
		}
	} else { 
		$dialog = $('<div>');
		$dialog.attr('title', module.title);
		$dialog.attr('id', module.div);
		$dialog.appendTo(document);
		$dialog.dialog({
			modal: modal ,
			width: width,
			height: height ,
			buttons: buttons,
			autoOpen: false
		});
		var dialogExtendOptions = {
		  "closable" : true,
		  "maximizable" :maxim,
		  "minimizable" : minim ,
		  "minimizeLocation" :  'left',
		  "collapsable" : false,
		  "dblclick" : false,
		  "titlebar" : false
		};
		$dialog.dialogExtend(dialogExtendOptions);
		
		loadModule(module, function(){
			$dialog.dialog('open');
			processCallback(opts.callback);			
		});
		
	}
}

function openHtmlDialog(html, opts){
	var buttons = opts.buttons ? opts.buttons : false;
	var width = opts.width ? opts.width : 0;
	var height = opts.height ? opts.height : 0;
	var maxim = opts.maxim && opts.maxim==true ? true : false;
	var minim = opts.minim && opts.minim==true ? true : false;
	var callback = opts.callback ? opts.callback : '';
	var cache = opts.cache && opts.cache==true ? true : false;
	var modal = opts.modal && opts.modal==true ? true : false;
	
	dialogId = opts.div.indexOf('#') == 0 ? opts.div : '#'+opts.div;
	if($(dialogId).length > 0){
		$(dialogId).html(html).dialog({buttons: buttons}).dialog('open');
	} else { 
		$dialog = $('<div>');
		$dialog.attr('title', opts.title);
		$dialog.attr('id', opts.div);
		$dialog.appendTo(document);
		$dialog.dialog({
			modal: modal ,
			width: width,
			height: height ,
			buttons: buttons,
			autoOpen: false
		});
		var dialogExtendOptions = {
		  "closable" : true,
		  "maximizable" :maxim,
		  "minimizable" : minim ,
		  "minimizeLocation" :  'left',
		  "collapsable" : false,
		  "dblclick" : false,
		  "titlebar" : false
		};
		$dialog.dialogExtend(dialogExtendOptions);
	}
	$(dialogId).html(html).dialog('open');
	initiateJquery();
}

// LOW LEVEL AJAX
function processCallback(callback, answer){
	if( typeof callback === "function"){
		callback(answer);
	} else {
		eval(callback);
	}
}

function MS_aJaxRequest(module, type, async, callback){
	var url = 'index.php?module='+module.name;
	if(module.params){
		url += '&'+module.params;
	}
	
	var data = module.data ? module.data : ''
	var async
	if(module.async){
		async = module.async;
	} else {
		async = !async || async!=true ? false : true
	}
	if(module.type){
		type = module.type;
	} 
	var answer = $.ajax({
		url : url,
		async: async,
		type: type ,
		data : data,
		success:function(answer, textStatus, xhr){
			if(xhr.responseText == "session timeout"){
				disconect(getLang('timeout'));
			} else {
				if(callback!= false){
					processCallback(callback, answer);
				}
			} 
		}
	}).responseText
	return answer;
}

function MS_jsonRequest(module, data, callback){
//	$('body *').css('cursor', 'wait');
	$.ajax({
		dataType: "json",
		url: 'index.php?module='+module,
		type:"POST",
		data: data,
		async:false,
		success: function(answer, textStatus, xhr){
	//		$('body *').css('cursor', 'auto');
			if(answer['error'] != ""){
				MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error')+'</h2>'+answer.error);
				return false;
			} else{
				MS_alert('<h2 class="title_wihte"><img src="assets/img/success.png" /> '+getLang('done')+'</h2>');
				processCallback(callback, answer);
			} 
			return answer;
		},
		error: function(jqXHR, textStatus, errorThrown) {
	//		$('body *').css('cursor', 'auto');
			if(jqXHR.responseText == "session timeout"){
				disconect(getLang('timeout'));
			} else {
				MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error')+'</h2>'+jqXHR.responseText);
			}
		}
	})
}

function getModuleJson(module){	
	var async = module.async ? module.async : false;
	if(module.muted && module.muted == true){
		var muted = true;
		async = true;
	} else {
	//	$('.ui-state-default').css('cursor', 'wait');
		showLoading('show', module.title, async);
		showLoading('progress', 50);
		var muted = false;
	}
	$.ajax({
		dataType: "json",
		url: 'index.php?module='+module.name + (module.param? '&'+module.param: ''),
		type: module.type ? module.type : "POST",
		data: module.post ? module.post : '',
		async: async,
		success: function(answer, textStatus, xhr){
			if(!muted) {
			//	$('.ui-state-default').css('cursor', 'auto');
				showLoading('hide');
				if(answer['error'] != ""){
					MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error')+'</h2>'+answer.error);
					return false;
				} else{
					MS_alert('<h2 class="title_wihte"><img src="assets/img/success.png" /> '+getLang('done')+'</h2>');
					
				} 
			}
			processCallback(module.callback, answer);
			return answer;
		},
		error: function(jqXHR, textStatus, errorThrown) {
			if(jqXHR.responseText == "session timeout"){
				disconect(getLang('timeout'));
			} else {
				if(!muted) {
				//	$('.ui-state-default').css('cursor', 'auto');
					showLoading('hide');
					MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error')+'</h2>'+jqXHR.responseText);
				}
				processCallback(module.callback, false);
			}
		}
	})

}

function disconect(msg){
	showLoading('progress', 100);
	var html = '';
	if(msg){
		html += '<div class="ui-corner-all ui-state-error" style="margin:7px; padding:5px" >'+msg+'</div>';
	} else {
		html += getLang('loading')+"...";
	}
	$('#page_content, #dialog-extend-fixed-container').fadeOut('slow', function(){
		showLoading('show', html);
		document.location.href = "index.php?timeout";
	});
}

// autocomplete
function setDefAutocomplete($input, db, table){
	var field = $input.attr('name');
	var source = 'index.php?common=autocomplete';
	source += '&db='+db+'&t='+table+'&f='+field+'&w='+field;
	$input.autocomplete({
		source: source,	
		minLength: 0,
		select: function(event, ui) {
			var label = ui.item[getFieldShortName(field)];
			$input.val(label);
			var $form = $input.parents('form');
			$form.find('input.this_form_modified').val(1);
			if($input.attr('onchange') != '' ){
				eval($input.attr('onchange'));
			}
			return false;
		},	
		search: function(event, ui) {
			$input.attr('term', '');	
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		return $( '<li></li>' )
			.data( "item.autocomplete", item )
			.append( "<a>" + item[getFieldShortName(field)]+"</a>" )
			.appendTo( ul );
	};
		
	$input.blur(function(){
		setTimeout(function(){
			$input.next('span').fadeOut().remove();
		}, 300)
	});
	$input.keypress(function(){
		$input.after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');
		$input.next('.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function clearSugField(a){
	$(a).fadeOut().remove();
	$(a).prev('input.ui-autocomplete-input').val('');
}

// CUSTOM WIDGETS
function MS_alert(html){
	var id =  new Date().getTime();
	$('#ballons_div').append('<div id="ballon-'+id+'"><span class="close"></span>'+html+'</div>');
	$('#ballon-'+id).fadeIn(2000).slideDown(2000);
	$('#ballon-'+id).find('.close').click(function(){
		$('#ballon-'+id).fadeOut().slideUp().remove();
	});
	setTimeout("$('#ballon-"+id+"').fadeOut().slideUp().remove()", 5000)
}

// Utilities
// date & time
function dateFormat(date, format) {
    format = format.replace("dd", (date.getDate() < 10 ? '0' : '') + date.getDate()); // Pad with '0' if needed
    format = format.replace("mm", (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1)); // Months are zero-based
    format = format.replace("yyyy", date.getFullYear());
    return format;
}
					  
function timeToUnix(str){
	var d = str.split(':');
	var date = new Date(1970,0,1,d[0], d[1]);
	return date.getTime() / 1000;
}

function unixToTime(sec){
	var date = new Date(sec * 1000)
	var h = date.getHours() +'';
	if(h.length == 1) h = '0'+h;
	var m = date.getMinutes();
	return  h + ':' + m;
}

function unixTimeFormat(sec){
	d = Number(sec);
	var h = Math.floor(d / 3600);
	var m = Math.floor(d % 3600 / 60);
	var s = Math.floor(d % 3600 % 60);
	return ((h > 0 ? h + ":" : "") + (m > 0 ? (h > 0 && m < 10 ? "0" : "") + m + ":" : "0:") + (s < 10 ? "0" : "") + s); 
}

function reloadImg(img){
	var src = $(img).attr('src');
	var file_seg = src.split('timestamp');
	if(file_seg[0].indexOf('?') > 0){
		$(img).attr('src', file_seg[0]+'&timestamp=' + new Date().getTime());
	} else {
		$(img).attr('src', file_seg[0]+'?timestamp=' + new Date().getTime());
	}
}

function supports_html5_storage() {
  try {
    return 'localStorage' in window && window['localStorage'] !== null;
  } catch (e) {
    return false;
  }
}

function launchFullScreen(element) {
	var el = document.documentElement
		, rfs = // for newer Webkit and Firefox
			   el.requestFullScreen
			|| el.webkitRequestFullScreen
			|| el.mozRequestFullScreen
			|| el.msRequestFullScreen
	;
	if(typeof rfs!="undefined" && rfs){
	  rfs.call(el);
	} else if(typeof window.ActiveXObject!="undefined"){
	  // for Internet Explorer
	  var wscript = new ActiveXObject("WScript.Shell");
	  if (wscript!=null) {
		 wscript.SendKeys("{F11}");
	  }
	}
}


function getFieldShortName(field){
	if(field.indexOf(' AS ') > -1){
		fs = field.split(' AS ');
		return fs[1];
	} else if(field.indexOf('.') > -1){
		fs = field.split('.');
		return fs[2];
	} else {
		return field;
	}	
}


function returnHigherLayer(){
	var eq_higher = 0;
	var eq =0;
	var index_highest = 0;
	if($('.ui-dialog .ui-widget-content:visible').length > 0){
		$('.ui-dialog:visible').each(function(){
			var index_current = parseInt($(this).css("z-index"), 10);
			if(index_current > index_highest) {
				index_highest = index_current;
				eq_higher = eq;
			}
			eq++;
		})
		return $('.ui-dialog:visible').not('#loading_main_div').eq(eq_higher);
	} else {
		return $('body');
	}
}

function displayNewMesageAlert(index, alerts){
	if(alerts	 && alerts[index]){
		MS_alert('<h2 class="title_wihte"><img src="assets/img/warning.png" />'+getLang('messages')+':</h2>'+alerts[index]);
	}
	if(index<alerts.length-1){
		setTimeout(function(){
			displayNewMesageAlert((index+1), alerts);
		},
			5000
		);	
	}
}

function getNewMsgCount(){
	$.ajax({
		dataType: "json",
		url: 'index.php?module=messages&count_msg',
		type:"POST",
		success: function(ans, textStatus, xhr){
			setTimeout('getNewMsgCount()', 120000);
			if(parseInt(ans.count) != 0){
				var oldVal = parseInt($('#count_new_msgs').text());
				//oldVal = oldVal.substring(oldVal.indexOf(')'),1);
				//oldVal = oldVal.substring(0,1);
				if(parseInt(ans.count) > parseInt(oldVal)){
					MS_alert('<h3>'+ (parseInt(ans.count) - parseInt(oldVal))+' '+getLang('new_msg_recived')+'</h3>')
				}
				$('#count_new_msgs').html('('+ans.count+')');
			} else {
				$('#count_new_msgs').html('');
			}
			if(ans.alerts && ans.alerts.length>0){
				displayNewMesageAlert(0, ans.alerts);
				if($('#module_messages').length > 0){
					var messageModule = {
						name:'messages',
						mute:true,
						div:'#module_messages'
					}
					loadModule(messageModule);
				}
			}			
		},
		error: function(jqXHR, textStatus, errorThrown) {
			if(jqXHR.responseText == "session timeout"){
				$('#loading_progress').progressbar( "option", "value", 100 );
				html = getLang('loading')+"...";
				createHtmlDialog('', '', false, html, 300, 200, '', true)
				$("#loading_main_div").fadeIn(500);
				document.location = "index.php?timeout";
			} else {
				MS_alert('<h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error')+'</h2>'+jqXHR.responseText);
			}
		}
	});
}

function checkIfMobile(){
	if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|OperaMini/i.test(navigator.userAgent)){
		return true;
	} else {
		return false;
	}
}
