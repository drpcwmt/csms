// Home.js
var barcode = new Audio('assets/audio/barcode.mp3');
barcode.volume = 0.2;

initiateHomeScreen();

function initiateHomeScreen(){
	
	loadModuleJS('commands');
	initCommand();
	
	var toLoad = new Array;
		// Messages
	/*if($("#home_content").find("#home_commands").length > 0){
		commands = {
			name: 'commandes',
			data: 'newForm',
			div: "#home_content #home_commands",
			title:getLang('orders'),
			async:true,
			mute:true,
			callback : function(){
				iniCommandsModule();
			}
		}
		toLoad.push(messages);
	}*/

	if(toLoad.length > 0){
		loadMultiModules(toLoad, function(){
			$('#home_content img.loading').fadeOut();
		}, true);
	}

	barcode.play();
}


function setCatAutocomplete(input, param){
	var source = 'index.php?module=categorys&autocomplete=cats';
	if(param && param !=''){
		source += '&'+param;
	}
	if(input instanceof jQuery){
		$input = input;
	} else {
		var $input = $(input);
	}
	$input.autocomplete({
		source: source,	
		minLength: 2,
		select: function(event, ui) {
			var name = ui.item.name ? ui.item.name : '';
			$input.val(name);
			$input.attr('term',ui.item.id);
			if($input.nextAll('input.autocomplete_value')){
				$input.nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($input.nextAll('div.ui-state-error')){
				$input.nextAll('div.ui-state-error').fadeOut().remove();
			}
			return false;
		},	
		search: function(event, ui) {
			$input.attr('term', '');	
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
				.append( "<a>" + name +"</a>" )
				.appendTo( ul );
		}
	};
	
	$input.after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	$input.focus(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});
	
	$input.blur(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeOut();
	});
	$input.keypress(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function setClientsAutocomplete(input, param){
	var source = 'index.php?module=clients&autocomplete';
	if(param && param !=''){
		source += '&'+param;
	}
	
	if(input instanceof jQuery){
		$input = input;
	} else {
		var $input = $(input);
	}

	$(input).autocomplete({
		source: source,	
		minLength: 0,
		select: function(event, ui) {
			var name = ui.item.name ? ui.item.name : '';
			$input.val(name);
			$input.attr('term',ui.item.id);
			if($input.nextAll('input.autocomplete_value')){
				$input.nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($input.nextAll('div.ui-state-error')){
				$input.nextAll('div.ui-state-error').fadeOut().remove();
			}
			return false;
		},	
		search: function(event, ui) {
			$input.attr('term', '');	
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
			var name = item.name ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( "<a>" + name+"</a>" )
				.appendTo( ul );
		}
	};
	
	$input.after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	$input.focus(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});
	
	$input.blur(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeOut();
	});
	$input.keypress(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function setSuppliersAutocomplete(input, param){
	var source = 'index.php?module=suppliers&autocomplete';
	if(param && param !=''){
		source += '&'+param;
	}
	
	if(input instanceof jQuery){
		$input = input;
	} else {
		var $input = $(input);
	}

	$(input).autocomplete({
		source: source,	
		minLength: 0,
		select: function(event, ui) {
			var name = ui.item.name ? ui.item.name : '';
			$input.val(name);
			$input.attr('term',ui.item.id);
			if($input.nextAll('input.autocomplete_value')){
				$input.nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($input.nextAll('div.ui-state-error')){
				$input.nextAll('div.ui-state-error').fadeOut().remove();
			}
			return false;
		},	
		search: function(event, ui) {
			$input.attr('term', '');	
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
			var name = item.name ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( "<a>" + name+"</a>" )
				.appendTo( ul );
		}
	};
	
	$input.after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	$input.focus(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});
	
	$input.blur(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeOut();
	});
	$input.keypress(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}


function setProductsAutocomplete(input, param){
	var source = 'index.php?module=products&autocomplete';
	if(param && param !=''){
		source += '&'+param;
	}
	if(input instanceof jQuery){
		$input = input;
	} else {
		var $input = $(input);
	}
	$input.autocomplete({
		source: source,	
		minLength: 0,
		select: function(event, ui) {
			var name = ui.item.name ? ui.item.name : '';
			$input.val(name);
			$input.attr('term',ui.item.id);
			if($input.nextAll('input.autocomplete_value')){
				$input.nextAll('input.autocomplete_value').val(ui.item.id);
			}
			if($input.nextAll('div.ui-state-error')){
				$input.nextAll('div.ui-state-error').fadeOut().remove();
			}
			return false;
		},	
		search: function(event, ui) {
			$input.attr('term', '');	
			$input.nextAll('input.autocomplete_value').val('');
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
			var name = item.name ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( "<a>" + name+ ' <span class="autocomplete_cat"> '+ item.category +' </span></a>' )
				.appendTo( ul );
		}
	};
	
	$input.after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	$input.focus(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});
	
	$input.blur(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeOut();
	});
	$input.keypress(function(){
		$input.next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}
