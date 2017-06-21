// JavaScript Document

function searchRouteByNo(){
	var html = '<form><table><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('no.')+': </label></td><td><input id="search_route_no_inp"  type="text" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:170,
		div:'MS_dialog_search_route_code',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				openRouteByNo($('#search_route_no_inp').val());
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
}

function openRouteByNo(routeNo){
	var module = {};
	module.name = 'routes';
	module.data = 'route_no='+routeNo;
	module.title = getLang('routes');
	module.div = 'RouteDetails-'+routeNo;
	module.callback = function(){
		setEmployerAutocomplete('#RouteDetails-'+routeNo+' #driver_name', 'job_code=7');
		setEmployerAutocomplete('#RouteDetails-'+routeNo+' #matron_name', 'job_code=2');
	/*var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	}
	
	$(".route_timming tbody").sortable({helper: fixHelper}).disableSelection();*/
	}
	var dialogOpt = {
		width:900,
		height:600,
		title:getLang('Routes'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
			var $form = $('#RouteDetails-'+routeNo+' form');
				var submitSave = {
					name : 'routes',
					param: 'saveroute',
					post : $form.serialize(),
					callback: function(){
						var routeId = $form.find('input[name="id"]').val();
						$('text.holder-route-'+routeId).html(routeId+' - '+ $form.find('input[name="region"]').val()); 	
					}
				}
				getModuleJson(submitSave);	
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	
	openAjaxDialog(module, dialogOpt)
};


function SearchRouteByName(){
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('name')+': </label></td><td><input id="search_route_name" type="text" class="input_double" /><input id="search_route_no_inp" class="autocomplete_value" type="hidden" /></td></tr></table></form>';

	var dialogOpt = {
		width:500,
		height:200,
		div:'MS_dialog_search_route_name',
		title:getLang('search'),
		buttons: [{ 
			text: getLang('search'), 
			click: function() { 
				var routeId = $('#MS_dialog_search_route_name #search_route_no_inp').val();
				if(routeId != ''){
					$but = $('<button>').attr('route_id', routeId);
					openRoute($but)
					$('#MS_dialog_search_route_name').dialog('close');
				} else {
					$('#MS_dialog_search_route_name').append('<div class="ui-state-error ui-corner-all" style="margin-top:15px">( '+$('#MS_dialog_search_route_name #search_route_name').val()+' )'+getLang('error_not_item_found')+'</div>');
				}
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
	var $sugField = $('#MS_dialog_search_route_name #search_route_name');
	setRouteAutocomplete($sugField);
}



function openRoute($btn){
	var routeId = $btn.attr('route_id');
	var busmsId = $btn.attr('busms_id');
	var module = {};
	module.name = 'routes';
	module.data = 'route_id='+routeId+'&busms_id='+busmsId;
	module.title = getLang('routes');
	module.div = 'RouteDetails-'+routeId;
	module.callback = function(){
		setAutocompleteDriver('#RouteDetails-'+routeId+' #driver_name');
		setAutocompleteMatron('#RouteDetails-'+routeId+' #matron_name');
	/*var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	}
	
	$(".route_timming tbody").sortable({helper: fixHelper}).disableSelection();*/
	}
	var dialogOpt = {
		width:900,
		height:600,
		title:getLang('Routes'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
			var $form = $('#RouteDetails-'+routeId+' form');
				var submitSave = {
					name : 'routes',
					param: 'saveroute',
					post : $form.serialize(),
					callback: function(){
						var routeId = $form.find('input[name="id"]').val();
						$('text.holder-route-'+routeId).html(routeId+' - '+ $form.find('input[name="region"]').val()); 	
					}
				}
				getModuleJson(submitSave);	
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	
	openAjaxDialog(module, dialogOpt)
};

// Autocomplete
function setRouteAutocomplete(input){
	var source = 'index.php?module=routes&route_autocomplete';
	$(input).autocomplete({
		source: source,	
		minLength: 2,
		select: function(event, ui) {
			var name = ui.item.name ? ui.item.name : '';
			$(input).val(name);
			$(input).attr('term',ui.item.id);
			if($(input).nextAll('input.autocomplete_value')){
				$(input).nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($(input).nextAll('div.ui-state-error')){
				$(input).nextAll('div.ui-state-error').fadeOut().remove();
			}
			return false;
		},	
		search: function(event, ui) {
			$(input).attr('term', '');	
			$(input).nextAll('input.autocomplete_value').val('');
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		if(item.error){
			MS_alert('<h3 class="title_wihte"><img src="assets/img/error.png" />'+item.error+'</h3>');
			return $( '<li class="ui-state-error ui-corner-all"></li>' )
				.data( "item.autocomplete", item )
				.append( '<a>' + item.error+"</a>" )
				.appendTo( ul );
			//return false;
		} else {
			var name = item.label ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( "<a>" + name+"</a>" )
				.appendTo( ul );
		}
	};
	
	$(input).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	$(input).focus(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});
	
	$(input).blur(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeOut();
	});
	$(input).keypress(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}


function setAutocompleteMatron(input){
	var source = 'index.php?module=resources&templ=matrons&matrons_autocomplete';
	$(input).autocomplete({
		source: source,	
		minLength: 2,
		select: function(event, ui) {
			var name = ui.item.name ? ui.item.name : '';
			$(input).val(name);
			$(input).attr('term',ui.item.id);
			if($(input).nextAll('input.autocomplete_value')){
				$(input).nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($(input).nextAll('div.ui-state-error')){
				$(input).nextAll('div.ui-state-error').fadeOut().remove();
			}
			return false;
		},	
		search: function(event, ui) {
			$(input).attr('term', '');	
			$(input).nextAll('input.autocomplete_value').val('');
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		if(item.error){
			MS_alert('<h3 class="title_wihte"><img src="assets/img/error.png" />'+item.error+'</h3>');
			return $( '<li class="ui-state-error ui-corner-all"></li>' )
				.data( "item.autocomplete", item )
				.append( '<a>' + item.error+"</a>" )
				.appendTo( ul );
			//return false;
		} else {
			var name = item.label ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( "<a>" + name+"</a>" )
				.appendTo( ul );
		}
	};
	
	$(input).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	$(input).focus(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});
	
	$(input).blur(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeOut();
	});
	$(input).keypress(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function setAutocompleteDriver(input){
	var source = 'index.php?module=resources&templ=drivers&drivers_autocomplete';
	$(input).autocomplete({
		source: source,	
		minLength: 2,
		select: function(event, ui) {
			var name = ui.item.name ? ui.item.name : '';
			$(input).val(name);
			$(input).attr('term',ui.item.id);
			if($(input).nextAll('input.autocomplete_value')){
				$(input).nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($(input).nextAll('div.ui-state-error')){
				$(input).nextAll('div.ui-state-error').fadeOut().remove();
			}
			return false;
		},	
		search: function(event, ui) {
			$(input).attr('term', '');	
			$(input).nextAll('input.autocomplete_value').val('');
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		if(item.error){
			MS_alert('<h3 class="title_wihte"><img src="assets/img/error.png" />'+item.error+'</h3>');
			return $( '<li class="ui-state-error ui-corner-all"></li>' )
				.data( "item.autocomplete", item )
				.append( '<a>' + item.error+"</a>" )
				.appendTo( ul );
			//return false;
		} else {
			var name = item.label ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( "<a>" + name+"</a>" )
				.appendTo( ul );
		}
	};
	
	$(input).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	$(input).focus(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});
	
	$(input).blur(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeOut();
	});
	$(input).keypress(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function filterRoutesList(val){
	val = $.trim(val);
	$('#routes_list li').each(function(){
		var li = $.trim($(this).text());
		if(val != ''){
			liLower = li.toLowerCase();
			if(li.indexOf(val) > 0 || liLower.indexOf(val) > 0){
				$(this).fadeIn();
			} else {
				$(this).fadeOut();
			}
		} else {
			$(this).fadeIn();
		}
	})
}

function resetRoutesFilterList($btn){
	var $input = $btn.prev('input');
	filterRoutesList('');
	$input.val(getLang('search'));
}

function newRoute($btn){
	var module = {};
	module.name = 'routes';
	module.data = 'newroute'+($btn.attr('group_id')? '&group_id='+$btn.attr('group_id'):'');
	module.title = getLang('new_route');
	module.div = 'MS_dialog-routes';
	module.callback = function(){
		setEmployerAutocomplete('#MS_dialog-routes #driver_name', 'job_code=7');
		setEmployerAutocomplete('#MS_dialog-routes #matron_name', 'job_code=2');
	}

	var dialogOpt = {
		width:600,
		height:300,
		title:getLang('Routes'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var submitSave = {
					name : 'routes',
					param: 'saveroute',
					post : $('#route_form').serialize(),
					callback: function(){
						$("#MS_dialog-routes").dialog("close"); 
						//reloadRoutesList();
					}
				}
				getModuleJson(submitSave);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	};
	
	openAjaxDialog(module, dialogOpt)
};

function saveRoute($btn){
	var $scoop  = $btn.parents('.ui-tabs-panel').eq(0);
	var $form = $scoop.find('form');
	var submitSave = {
		name : 'routes',
		param: 'saveroute',
		post : $form.serialize(),
		callback: function(){
			var routeId = $form.find('input[name="id"]').val();
			$('text.holder-route-'+routeId).html(routeId+' - '+ $form.find('input[name="region"]').val()); 	
		}
	}
	getModuleJson(submitSave);	
}

function deleteRoute($btn){
	var submitDelete = {
		name : 'routes',
		param: 'deleteroute',
		post : 'route_id='+$btn.attr('route_id'),
		callback: function(){
			$('#routes_list li.ui-state-active').fadeOut().remove();
			$('#routes_list li:first').click();
		}
	}
	getModuleJson(submitDelete);	
}
function reloadRoutesList(){
	var module ={};
	module.name = 'routes';
	module.data = "routes";
	module.title = getLang('routes');
	module.div = '#route_list';
	loadModuleToDiv(new Array(module), "")

}




function addTarget($btn){
	var routeId = $btn.attr('route_id');
	var module = {};
	module.name = 'routes';
	module.data = 'new_target&route_id='+routeId;
	module.title = getLang('routes');
	module.div = 'MS_dialog-routes_targets';
	
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			var submitSave = {
				name : 'routes',
				param: 'addTarget&save',
				post : $('#new_target_form').serialize(),
				callback: function(){
					var module ={};
					module.name = 'routes';
					module.data = "reload_parcour&route_id="+routeId;
					module.title = getLang('routes');
					module.div = '#route_parcour_div';
					module.callback = function(){
						//iniRouteDetails();
					}
					loadModule(module);
				}
			}
			getModuleJson(submitSave);
		}
	},{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	
	createAjaxDialog(module, buttons, false, 420, 240, false, 'resetTargetForm()')	
}

function resetTargetForm(){
	$('#new_target_form #address').val('');
	$('#new_target_form #arrival_time').val('');
}


function updateTarget(targetId, time){
	var routeId  = $("#MS_dialog-routes_details #id").val();
	var update = {
		name : 'routes',
		param: 'route_id='+routeId,
		post : 'target_id='+targetId+'&arrival_time='+time,
	}
	getModuleJson(submitSave);
}

function deleteTarget($btn){
	var routeId  = $('#MS_dialog-routes_details input[name="id"]').val();
	MS_jsonRequest('routes&route_id='+$btnrouteId, 'del_target='+targetId, 'evalNewTarget('+routeId+')' );	
}

function addMember($btn){
	var con = $btn.attr('con');
	var routeId = $btn.attr('route_id');
	var module = {
		name: 'routes',
		data: 'addmember&con='+con+($btn.attr('route_id') ? '&route_id='+$btn.attr('route_id') : ''),
		title: getLang('add_member'),
		div: 'MS_dialog-new_member',
	}
	
	var dialogOpt = {
		width:420,
		height:260,
		title:getLang('add_member'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var submitSave = {
					name : 'routes',
					param: 'addmember&save',
					post : $('#MS_dialog-new_member form').serialize(),
					callback: function(answer){
						$scoop = $btn.parents('.ui-tabs-panel').eq(0);
						$scoop.find('table.tablesorter tbody').append(answer.html);
						$('#MS_dialog-new_member form input[name="name"').val('');
						$('#MS_dialog-new_member form input[name="con_id"').val('');
						//$("#MS_dialog-new_member form").dialog("close"); 
					}
				}
				getModuleJson(submitSave);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			var $select = $('#newMemberForm select[name="cc_id"]');
			if(con == 'std'){
				loadModuleJS('students');
				setStudentAutocomplete('#newMemberForm input[name="name"]', '1', $select.val());
			} else {
				loadModuleJS('employers');
				setEmployerAutocomplete('#newMemberForm input[name="name"]','', $select.val());
			}
		}
	};
	
	openAjaxDialog(module, dialogOpt)
}

function deleteMember($btn){
	var submitDelete = {
		name : 'routes',
		param: 'delmember',
		post : 'con='+$btn.attr('con')+'&con_id='+$btn.attr('con_id')+'&route_id='+$btn.attr('route_id'),
		callback: function(answer){
			$btn.parents('tr').eq(0).fadeOut().remove();
		}
	}
	getModuleJson(submitDelete);
}

function changeMemberCC($select){
	var $form = $select.parents('form');
	var con = $form.find('input[name="con"]').val()
	var $sugInp = $form.find('input[name="name"]');
	if($sugInp && $sugInp.hasClass('ui-autocomplete-input')){	
		if(con == 'std'){
		$('#newMemberForm input[name="name"]').attr('sms_id', $select.val());	
			loadModuleJS('students');
			setStudentAutocomplete('#newMemberForm input[name="name"]', '1');
		} else {
			loadModuleJS('employers');
			setEmployerAutocomplete('#newMemberForm input[name="name"]','1', $select.val());
		}
	}
}

function openRouteGroup($but){
	var groupId = $but.attr('itemid');
	var busmsId= $but.attr('busms_id');
	var smsId = $but.attr('sms_id');
	var module = {};
	module.name = 'routes';
	module.data = 'groups&group_id='+groupId+'&busms_id='+busmsId+($but.attr('sms_id') ? '&sms_id='+$but.attr('sms_id') : '');
	module.title = getLang('bus');
	module.div = '#groups_resource_content';
	loadModule(module);
}

function moveMember($btn){
	var con=$btn.attr('con');
	var conId=$btn.attr('con_id');
	
	var html='<form><input type="hidden" name="con" value="'+con+'"/><input type="hidden" name="con_id" value="'+conId+'"/><table><tbody><tr><td width="120"><label class="label reverse_align ">'+getLang('route')+': </label></td><td><input class="required" type="text" name="route_id"></td></tr></tbody></table></form>';
	var dialogOpt = {
		div:'moveMemberDialog',
		width:320,
		height:200,
		title:getLang('move'),
		maxim:false,
		minim:false,
		overlay:true,
		buttons: [{ 
			text: getLang('move'), 
			click: function() { 
				var submitSave = {
					name : 'routes',
					param: 'movemember',
					post : $('#moveMemberDialog form').serialize(),
					callback: function(answer){						
						$("#moveMemberDialog").dialog("close"); 
					}
				}
				getModuleJson(submitSave);
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			var $select = $('#newMemberForm select[name="cc_id"]');
			if(con == 'std'){
				loadModuleJS('students');
				setStudentAutocomplete('#newMemberForm input[name="name"]', '1', $select.val());
			} else {
				loadModuleJS('employers');
				setEmployerAutocomplete('#newMemberForm input[name="name"]','', $select.val());
			}
		}
	}
	openHtmlDialog(html, dialogOpt);
}


function searchMembers($btn){
	var con = $btn.attr('con');
	var module = {
		name: 'routes',
		data: 'addmember&con='+con,
		title: getLang('routes'),
		div: 'MS_dialog-search',
	}
	
	var dialogOpt = {
		width:420,
		height:260,
		title:getLang('search_member'),
		maxim:false,
		minim:false,
		buttons: [{ 
			text: getLang('ok'), 
			click: function() { 
				var $button = $('<button>');
				if(con=='std'){
					$button.attr('std_id', $('#MS_dialog-search input[name="con_id"]').val());
					$button.attr('sms_id', $('#MS_dialog-search select[name="cc_id"]').val());
					openStudent($button);
				} else {
					$button.attr('emp_id', $('#MS_dialog-search input[name="con_id"]').val());
					$button.attr('hrms_id', $('#MS_dialog-search select[name="cc_id"]').val());
					openEmployer($button);
				}
			}
		},{ 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			var $select = $('#newMemberForm select[name="cc_id"]');
			if(con == 'std'){
				loadModuleJS('students');
				setStudentAutocomplete('#newMemberForm input[name="name"]', '1', $select.val());
			} else {
				loadModuleJS('employers');
				setEmployerAutocomplete('#newMemberForm input[name="name"]','1', $select.val() );
			}
		}
	};
	
	openAjaxDialog(module, dialogOpt)
}

function listBySchool($btn){
	var smsId = $btn.attr('sms_id');
	var module = {
		name: 'routes',
		data: 'members_by_school&sms_id='+smsId,
		div: '#module_route_main'
	}
	loadModule(module);
}

function updateMemberTime($btn){
	var routeId = $btn.attr('route_id');
	var con = $btn.attr('con');
	var con_id = $btn.attr('con_id');
	var cc_id = $btn.attr('cc_id');
	var r = $btn.attr('rel');
	var $tr = $btn.parents('tr').eq(0);
	var address_id = $tr.find('.address_id').val();
	var time = $btn.val();
	var save = {
		name : 'routes',
		param: 'updateMemberTime',
		post : 'route_id='+routeId+'&con='+con+'&con_id='+con_id+'&cc_id='+cc_id+'&r='+r+'&address_id='+address_id+'&time='+time,
		callback: function(answer){			
		
		}
	}
	getModuleJson(save);
}

function openRouteMap($btn){
	var routeId = $btn.attr('route_id');
	var r = $btn.attr('rel');
	var locations = {
		name : 'routes',
		param: 'map_locations',
		post : 'route_id='+routeId+'&r='+r,
		callback: function(answer){						
			if(markers.length > 0){
				clearMarkers();
			}
			map.setZoom(12);
			var mapinfowindow  = new google.maps.InfoWindow();
			
			$.each(answer.locations, function(i, item){
				var LatLng
				if(item.lng!= null){
					 LatLng = {lat: item.lat, lng: item.lng};
					  marker =new google.maps.Marker({
						  position: LatLng,
						  map: map,
						  title: item.info+' - '+item.time
						});
					markers.join(marker);
								 
				} else {
					results = getGeoCode(item.address, function(locations){
						if(locations != false){
							marker = addMarker(locations[0].geometry.location);
						}
					});
				}
				
				//map.setCenter(LatLng);
					
					//SetMapsInfoWindows(markers[0], $btn.attr('infos'));
					//if($btn.attr('info') != $btn.attr('info')!=''){
				content = item.info;
				
				/*google.maps.event.addListener(marker,'click', (function(marker,content,mapinfowindow){ 
					return function() {
						mapinfowindow.setContent(content);
						mapinfowindow.open(map,marker);
					};
				})(marker,content,mapinfowindow)); */
					//}
					
				document.getElementById("map-display").setAttribute('style','visibility:visible;');
			});
		}
	}
	getModuleJson(locations);
}

function toogleAddParcourTd($btn){
	var $div = $btn.parents('.parcour_main').eq(0);
	var $nonList = $div.find('.add_parcour_td');
	$span = $btn.find('span');
	if($nonList.is(":visible")){
		$nonList.hide();
		$span.removeClass('ui-icon-plus').addClass('ui-icon-arrowthickstop-1-e');
	} else {
		$nonList.fadeIn();
		$span.removeClass('ui-icon-arrowthickstop-1-e').addClass('ui-icon-plus');
	}
}