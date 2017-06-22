// Jquery initiate

/*$(document).bind('drop dragover', function (e) {
	e.preventDefault();
});*/

/*$('a').click( function (e) {
	e.preventDefault();
	return false;
});*/

$(document).on('focusin', function(e) {
    if ($(e.target).closest(".mce-window, .moxman-window").length) {
		e.stopImmediatePropagation();
	}
});


/**********************************************************/
function initiateJquery(){
//$(':not("input, select, textarea")').disableSelection()
	iniMsUi();
	iniButtons();
	iniClickable();
	iniHoverable();
	iniSelectable();
	iniSuperfishMenus();
	initToolbox();
	iniTablesorter();
	iniPrint();
	iniSortableList();
	iniButtonsRoles();
	iniInputUpdate();
	initAccordion();
	initTabs();
	iniCombobox();
	iniForms();
	iniAceEditor();
	if(configFile.MapsApiKey && typeof google === 'object' && typeof google.maps === 'object'){
		//alert('gogle');
		//iniGoogleMap();
	}
}

function iniMsUi(){
	$('.buttonSet').each(function(){
		if(!$(this).hasClass('MS_buttonset')){
			$(this).addClass('MS_buttonset');
			$('.buttonSet').buttonset();
		}
	});

		// Fieldset
	$('fieldset').not('.ui-state-highlight').addClass('ui-widget-content ui-corner-all');
	$('fieldset legend').addClass('ui-widget-header ui-corner-all');

		// Spinner
	$('.spinner').spinner();
		// ProgressBar
	$('.progressbar').progressbar();

		// buuton class
	$('.clickable, .selectable').not().addClass('ui-state-default');
		// tooltip
	$('[title!=""]').tooltip({position: { my: "left top", at: "left+5 bottom" }});

		// select all checkbox

	$('input.select_all[type="checkbox"]').click(function() {
		var $table = $(this).closest('table');
		var checkboxes = $table.find(':checkbox');
		if($(this).is(':checked')) {
			checkboxes.prop('checked', true);
			$(this).prop('checked', true);
		} else {
			checkboxes.prop('checked', false);
		}
		return false;
	});

	/*$('input.select_all[type="checkbox"]').click(function(){
		var handler = this;
		var $table = $(handler).parents('table').eq(0);
		var $tbody = $table.find('tbody');
		if(handler.checked ){
			alert('cheked');
			$table.find('input[type="checkbox"]').each(function(){
				this.checked = true;
			});
		} else {
			alert('unchecked');
			$table.find('input[type="checkbox"]').each(function(){
				this.checked = false;
			});
		}
	})*/
}

function initTabs(){
	$('.tabs').each(function(){
		if($(this).hasClass('MS_formed') == false){
			$(this).tabs({
			//	heightStyle: "auto",
				beforeLoad: function( event, ui ){
					if ( ui.tab.data( "loaded" ) && configFile.debugMode == '0') {
					  event.preventDefault();
					  return false;
					}
					ui.jqXHR.success(function() {
					  ui.tab.data( "loaded", true );
					});
					var url = ui.ajaxSettings.url;
					if( url.indexOf('module')){
						var x = url.split('module=');
						var y = x[1].split('&');
						moduleName = y[0];
						moduleTitle = moduleName[0].toUpperCase()+ moduleName.slice(1);
						$("#loading_main_div").find('.stat').html(moduleTitle.replace('_', ' '));
						$("#loading_main_div").fadeIn(500);
						loadModuleJS(moduleName);
					}
					return true;
				},
				/*beforeActivate: function( event, ui ) {
					if(ui.newTab.attr('module' && ui.newPanel.html() != '')){
						var module={
							name:ui.newTab.attr('module'),
							data:ui.newTab.attr('module_data'),
							div :ui.newPanel
						}
						callback = ui.newTab.attr('after') ? ui.newTab.attr('after') : '';
						loadModule(module, callback);
					}
				},*/
				ajaxOptions: {
					success: function( xhr, status, index ) {
						if(xhr.responseText == "session timeout"){
							disconect(getLang('timeout'));
						}
					}
				},
				load: function( event, ui ){
					$("#loading_main_div").fadeOut();
					var $curIndex = $(ui.tab);
					if($curIndex.attr('after')){
						var func = $curIndex.attr('after');
						if (typeof window[func] === "function") {
							window[func]($curIndex);
						} else {
							alert(func+' is undefined');
						}
					}
					initiateJquery();
				}
			}).find( ".ui-tabs-nav" ).sortable({ axis: "x" });
			$(this).addClass('MS_formed');
		}
	});
}

function initAccordion(){
	$('.accordion').each(function(){
		if($(this).hasClass('MS_formed') == false){
			$(this).accordion({
				active: false,
				collapsible: true,
				autoHeight : false,
				clearStyle : true
			});
			$(this).addClass('MS_formed');
		}
	});
}

function iniSortableList(){
	$('.sortable').each(function(){
		if(!$(this).hasClass('MS_sortable')){
			$(this).addClass('MS_sortable')
			$(this).sortable({
				update: function(event, ui) {
					if($(this).attr('rel') && $(this).attr('rel')!= ''){
						var items = $(this).attr('rel');
						var itemOrder = new Array
						$(this).find('li').each(function(){
							itemOrder.push($(this).attr('itemid'));
						});
						$.ajax({
							dataType: "json",
							url: 'index.php?plugin=sortable&sort',
							type: 'POST',
							data: "itemOrder="+itemOrder.join(',')+"&items="+items,
							success: function(ans, textStatus, xhr){
								if(ans['error'] != ""){
									MS_alert(' <h2 class="title_wihte"><img src="assets/img/error.png" />'+getLang('error')+'</h2>'+ans.error);
									return false;
								} else{
									MS_alert('<h2 class="title_wihte"><img src="assets/img/success.png" /> '+getLang('done')+'</h2>');
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
						})
					}
				}
			});
		}
	})
}

function iniTablesorter(){
	$.tablesorter.defaults.widgets = ['zebra']; 
	$('.tablesorter').each(function(){
		if(!$(this).hasClass('MS_tablesorter')){
			$(this).addClass('MS_tablesorter')
			$(this).tablesorter();
			if(!$(this).hasClass('nohover')){
				$(this).find('td').hover(function(){
					var $tr = $(this).parent('tr');
					$tr.find('td').addClass('tablesorter_hover'); //({border:'0px', color:'#000', 'background-color':'#8dbdd8'});
				},
				function(){
					var $tr = $(this).parent('tr');
					$tr.find('td').removeClass('tablesorter_hover');
				})
			}
		//	$(this).find('th, td').disableSelection();
		} else{
			$(".tablesorter").trigger("update");
		}
	});

	$('.result').each(function(){
		if(!$(this).hasClass('MS_formed')){
			$(this).addClass('MS_formed')
			if(!$(this).hasClass('nohover')){
				$(this).find('td').hover(function(){
					var $tr = $(this).parent('tr');
					$tr.find('td').addClass('tablesorter_hover'); //({border:'0px', color:'#000', 'background-color':'#8dbdd8'});
				},
				function(){
					var $tr = $(this).parent('tr');
					$tr.find('td').removeClass('tablesorter_hover');
				})
			}
			$(this).find('th, td').disableSelection();
		}
	});
}

function iniButtons(){
	$('.button').each(function(){
		if(!$(this).hasClass('MS_button')){
			$(this).addClass('MS_button ui-state-default hoverable ui-corner-all');
			var icons = {};
			if($(this).attr('icon') &&$(this).attr('icon') != ''){	icons.primary = 'ui-icon-'+ $(this).attr('icon');}
			if($(this).attr('icon2') && $(this).attr('icon2') != ''){ icons.secondary = 'ui-icon-'+ $(this).attr('icon2');}
			$(this).button({
				text: $(this).text(),
				icons: icons
			});
		}
	})

}

function iniHoverable(){
	$('.hoverable').each(function(){
		if(!$(this).hasClass('MS_hoverable') && !$(this).hasClass('MS_clickable')){
			$(this).hover(
				function(){
					$(this).addClass('ui-state-hover');
				},
				function(){
					$(this).removeClass('ui-state-hover');
				}
			)
		}
	});
}

function iniClickable(){
	$('.clickable').each(function(){
		if(!$(this).hasClass('MS_clickable')){
			$(this).bind({
				click: function() {
					var $parent = $(this).parent();
					$parent.children().removeClass('ui-state-active');
					$(this).addClass('ui-state-active');
				}
			});
		}
	})
}

function iniSelectable(){
	$('ul.selectable').each(function(){
		var ulSelect = this
		if(!$(ulSelect).hasClass('MS_selectable')){
			$(ulSelect).children('li').each(function(){
				$(this).bind({
					click: function() {
						if($(this).hasClass('ui-state-active')){
							$(this).removeClass('ui-state-active');
							$(this).find('.ui-icon').removeClass('ui-icon-check').fadeOut();
						} else {
							$(this).addClass('ui-state-active');
							$(this).find('.ui-icon').addClass('ui-icon-check').fadeIn();
						}
						var values = '';
						$(ulSelect).children('li.ui-state-active').each(function(){
							values.push($(this).attr('value'));
						});
						$(ulSelect).find('input.selectable_value').val(values.join(','));
					}

				});
			})
		}
	})

}

function iniSuperfishMenus(){
	$('.nav').each(function(){
		if(!$(this).hasClass('MS_clickable')){
			//$(this).children('a').addClass('ui-state-default hoverable');
			$(this).superfish().addClass('MS_clickable');
		}
	}).disableSelection();
}

function initToolbox(){
	$('.toolbox').each(function(){
		if(!$(this).hasClass('MS_toolbox')){
			$(this).addClass('MS_toolbox');
			$(this).find('.ui-spinner').removeClass('ui-corner-all').addClass('ui-state-default ');
			$(this).find('.spinner').css('color', '#4297D7');
			$(this).wrap('<div class="ui-corner-all ui-widget-header toolbox_warper"></div>');
			$(this).buttonset({
				items: $(this).find('a')
			});
			if($(this).children().length == 1){
				$(this).children().addClass("ui-corner-all").removeClass('ui-corner-right ui-corner-left');
			}
		}
		$(this).find('a').disableSelection();
	})
}

// Forms
function iniForms(){
	$('form').each(function(){
		var $form = $(this)
		if(!$form.hasClass('MS_formed')){
			$form.addClass('MS_formed')
			$form.prepend('<input type="hidden" class="this_form_modified" value="0" />');
			$form.find('input[type="text"], select').each(function(){
				//$(this).addClass('MS_formed');
				$(this).focus(function(){
					$(this).removeClass('ui-state-error');
				})
				$(this).change(function(){
					$form.find('.this_form_modified').val('1')
				});
				if($(this).hasClass('required') && $(this).val() == ""){
					$(this).addClass('ui-state-error');
				}
			})
			$form.find('textarea').each(function(){
				$(this).focus(function(){
					$(this).removeClass('ui-state-error');
				})
				$(this).change(function(){
					$form.find('.this_form_modified').val('1')
				});
			})
			$form.find('input[type="checkbox"], input[type="radio"]').each(function(){
				$(this).change(function(){
					$form.find('.this_form_modified').val('1')
				});
			})

			$form.submit(function(e){
				e.preventDefault();
				return false;
			});
			formStyle($form);
			$form.find('input:first').focus();
			iniColorPicker();
			formatMaskInput();
			initTinymce();
			
			// disable
			if($form.attr('editable') && $form.attr('editable') == '0'){
				$form.find('input, select, textarea').attr('disabled', 'disabled');
				$form.find('.ui-combobox-toggle').hide();
			}
		}
	})
}

function formStyle(form){
//	$(form).find('input').not('.ui-state-default, .spinner, .MS_formed').addClass('ui-widget-content');
	$(form).find('input').not('.no-corner, .ui-combobox-input, .ui-corner-left, .spinner, .MS_formed').addClass('ui-corner-right');
	$(form).find('.label').addClass('ui-widget-header');
	$(form).find('.label').not('.ui-corner-right').addClass('ui-widget-header ui-corner-left reverse_align');
	$(form).find('.ui-spinner').removeClass('ui-corner-all').addClass('ui-corner-right');
	//$(form).find('.spinner').css('color', '#4297D7')
		// fieldset

	// add tooltip to date and time fields
	$("input.mask-date").attr('title', getLang('tooltip_date')).tooltip();
	$("input.mask-time").attr('title', getLang('tooltip_time')).tooltip();
	// init datepicker
	$('.datepicker').datepicker({dateFormat:'dd/mm/yy'});
	// init required
	//$('.required').after('<span class="mini_link">*</span>');
}

function loadTinyMce(){

	$.ajax({
		url: 'assets/js/tinymce/jquery.tinymce.min.js',
		dataType: "script",
		async: false,
	});
	$.ajax({
		url: 'assets/js/tinymce/tinymce.min.js',
		dataType: "script",
		async: false,
	});
	tinyMCE.baseURL = "http://"+window.location.hostname+"/assets/js/tinymce";// trailing slash important
	tinyMCE.suffix = '.min';
}

function initTinymce(){
	$('.tinymce').each(function(){
		var $form = $(this)
		if(!$form.hasClass('MS_formed')){
			//$form.addClass('MS_formed');
			if( !$.fn.tinymce){
				loadTinyMce();
			}
			if($form.attr('serviceid')){
				var ext_img_list = "assets/js/img_list.js.php?service_id="+$form.attr('serviceid');
			} else {
				var ext_img_list ="assets/js/img_list.js.php";
			}
			tinymce.init({
			  selector: '.tinymce',
			 	plugins: [
					'advlist autolink lists link image charmap print preview hr anchor pagebreak',
					'searchreplace wordcount visualblocks visualchars code fullscreen',
					'insertdatetime media nonbreaking save table contextmenu directionality',
					'emoticons template paste textcolor colorpicker textpattern imagetools'
				],
				toolbar1: 'insertfile undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media pagebreak emoticons | fontselect fontsizeselect | spellchecker | fullscreen',
				menu: {
					file: {title: 'File', items: 'print preview save'},
					edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall | searchreplace'},
					insert: {title: 'Insert', items: 'link image media | insertdatetime charmap anchor pagebreak'},
					view: {title: 'View', items: 'fullscreen | code | visualaid visualblocks'},
					format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
					table: {title: 'Table', items: 'inserttable tableprops deletetable cell row column'},
				},
				image_advtab: true,
			  	relative_urls: false,
			  	remove_script_host: false,
				image_list : ext_img_list,
			  	content_css: [
					'//'+window.location.hostname+'/assets/css/common.css',
					'//'+window.location.hostname+'/assets/css/special.css',
					'//'+window.location.hostname+'/assets/css/themes/default/jquery.ui.theme.css'
				],
				setup: function (editor) {
					editor.on('change', function () {
						editor.save();
					});
				}
			});
			//	return true;
			/*$form.tinymce({
				theme: 'modern',
				plugins: [
					'advlist autolink lists link image charmap print preview hr anchor pagebreak',
					'searchreplace wordcount visualblocks visualchars code fullscreen',
					'insertdatetime media nonbreaking save table contextmenu directionality',
					'emoticons template paste textcolor colorpicker textpattern imagetools'
				],
				toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media pagebreak | fontselect fontsizeselect | spellchecker | fullscreen',
				toolbar2: 'print preview | forecolor backcolor emoticons',
				image_advtab: true,
				menu: {
					edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall | searchreplace'},
					insert: {title: 'Insert', items: 'link image media | insertdatetime charmap anchor pagebreak'},
					view: {title: 'View', items: 'fullscreen | code | visualaid visualblocks'},
					format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
					table: {title: 'Table', items: 'inserttable tableprops deletetable cell row column'},
				},
				content_css: [
					'//'+window.location.hostname+'/assets/css/common.css',
					'//'+window.location.hostname+'/assets/css/special.css'
				]
			});*/
		}
	});
}

function resetForm(form){
	$(form).find('input')
	 .not(':button, :submit, :reset')
	 .val('')
	 .removeAttr('checked')
	 $(form).find('select').each(function(){
		$(this).children('option').eq(0).attr('selected', 'selected');
	})
	 $(form).find('textarea').html('');
}

function validateForm(form){
	var valid = true;
	var missingArray =new Array();
	$(form).find('.required').each(function(){
		if($(this).val() ==''){
			$(this).addClass('ui-state-error');
			valid = false
			missingArray.push($(this).attr('title') != '' ? $(this).attr('title') : $(this).attr('name'));
		} else {
			$(this).removeClass('ui-state-error');
		}
	})
	$(form).find('select.required').each(function(){
		if($(this).children('option').length== 0 || $(this).val() ==''){
			$(this).addClass('ui-state-error');
			if($(this).hasClass('combobox')){
				$(this).next('.ui-combobox').find('input').addClass('ui-state-error');
			}
			valid = false
			missingArray.push($(this).attr('title') != '' ? $(this).attr('title') : $(this).attr('name'));
		} else {
			$(this).removeClass('ui-state-error');
			if($(this).hasClass('combobox')){
				$(this).next('.ui-combobox').find('input').removeClass('ui-state-error');
			}
		}
	})
	if(!valid){
		MS_alert('<img src="assets/img/warning.png" />'+getLang('fill_req_fields')+ '</br>'+ missingArray.join('</br>'));
	}
	return valid
}

function formatMaskInput(){
	$('input.mask-date').not('.masked').each(function(){
		$(this).mask("99/99/9999").addClass('masked');
	})
	$('input.mask-time').not('.masked').each(function(){
		$(this).mask("99:99").addClass('masked');
	})

	$('input.mask-phone').not('.masked').mask("9999999999").addClass('masked');
	$('input.mask-zip').not('.masked').mask("99999").addClass('masked');
}

function iniColorPicker(){
	jQuery('select.color_picker').colourPicker({
		ico:	'assets/img/jquery.colourPicker.gif',
		title:	getLang('select')
	})
}

function iniCombobox(){
	$('select.combobox').each(function(){
		if(!$(this).hasClass('MS_formed')){
			$(this).combobox();
			$(this).addClass("MS_formed");
		}
	})
}


function iniPrint(){
	$('.print_but').unbind("click");
	$('.print_but').click( function(){
			print_pre($(this));
	});
}

function iniButtonsRoles(){
	$('a[action], button[action], li[action], td[action], div[action], span[action]').each(function(){
		if($(this).hasClass('MS_formed') == false){
			$(this).click(function(){
				if($(this).attr('module') && $(this).attr('module')!='' ){
					loadModuleJS($(this).attr('module'));
				} else if($(this).attr('plugin') && $(this).attr('plugin')!=''){
					loadPluginJS($(this).attr('plugin'));
				};
				var action = $(this).attr('action');
				if (typeof window[action] === "function") {
					window[action]($(this));
				} else {
					alert(action+' is undefined');
				}
			});
			$(this).addClass('MS_formed');
		}
	});
}

function iniInputUpdate(){
	$('input[update], select[update]').each(function(){
		if($(this).hasClass('MS_formed_update') == false){
			$(this).change(function(){
				if($(this).attr('module') && $(this).attr('module')!=''){
					loadModuleJS($(this).attr('module'));
				}
				var update = $(this).attr('update');
				if (typeof window[update] === "function") {
					window[update]($(this));
				} else {
					alert(update+' is undefined');
				}
			});
			$(this).addClass('MS_formed_update');
		}
	});
}

function iniAceEditor(){
			$('.aceEditor').each(function(){
				if($(this).hasClass('MS_formed') == false){
					var editor = ace.edit($(this).attr('id'));
					ace.config.set('basePath', 'assets/js/ace');
					editor.getSession().setUseWorker(false);
					//editor.setTheme("ace/theme/monokai");
					editor.getSession().setMode("ace/mode/html");
					$(this).addClass('MS_formed');
				}
			});
		/*$.getScript("assets/js/ace/ace.js", function(){
			$('.aceEditor').each(function(){
				if($(this).hasClass('MS_formed') == false){
					var editor = ace.edit($(this).attr('id'));
					ace.config.set('basePath', 'assets/js/ace');
					editor.getSession().setUseWorker(false);
					//editor.setTheme("ace/theme/monokai");
					editor.getSession().setMode("ace/mode/html");
					$(this).addClass('MS_formed');
				}
			});
		});*/
}

/***************** Goolge maps ************************/
var geocoder;
var map;
var markers = [];
var locations = [];


function iniGoogleMap(){
	if($('#map-display').length == 0){
		$('body').append(
			'<div id="map-display">'+
    			'<div class="close" onclick="$(\'#map-display\').fadeOut();"></div>'+
        		'<div id="map-canvas"></div>'+
			'</div>'
		);
	}
	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(30.160, 31.439);
	var mapOptions = {
		zoom: 18,
		center: latlng
	}
	map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
}



function getGeoCode(address, fn){
	locations = geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == 'OK') {
			fn(results);
		} else {
			MS_alert(getLang('error')+': ' + status);
			fn(false);
		}
	});
}

function addMarker(location, draggable) {
	var marker = new google.maps.Marker({
	  position: location,
	  draggable: draggable,
	  map: map
	});
	markers.push(marker);
	return marker;
}
  
function clearMarkers() {
	for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(null);
	}
}
