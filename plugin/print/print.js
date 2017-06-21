function print_tab($but){
	var $container = $but.parents('.ui-tabs-panel').eq(0);
	printTag($container)
}

function printDialog($dialog){
	if($dialog.find('.tabs').length>0){
		$dialog = getCurrentTab($dialog);
	} 
	printTag($dialog)
}

function printScoop($btn){
	$scoop = $btn.parents('.scoop').eq(0);
	printTag($scoop)
}

function getCurrentTab($container){
	if($container.find('.tabs').length>0){
		return getCurrentTab($container.find('.tabs'));
	} else {
		var $tab = $container.find('.tabs');
		return $tab.find('.ui-tabs-panel:visible');
	}
}

function print_pre($but){
	container = $but.attr('rel');
	var $higherLayer = returnHigherLayer();
	var $content = $higherLayer.find(container);
	if($content.length > 0 && $content.html() != ''){
		printTag($content);
	} else {
		MS_alert("<h3>Can't find printable area: "+container+' <h3>');
		return false;
	} 
}

function printTag($tag){
	var data;
	if($tag instanceof jQuery){
		data = $tag.html();
	} else if($($tag).length == 0){
		MS_alert("Can't find printable area");
		return false;
	} else {
		data = $($tag).html();
	}
	var html = '<form id="print_form" target="_blank" method="POST" action="index.php?plugin=print" class="ui-corner-all ui-state-highlight">'+
		'<input id="print_content" name="print_content" type="hidden"  />'+
		'<table>'+
			'<tr>'+
				'<td valign="top" width="100"><label class="label reverse_align" float:left">'+getLang('options')+': </label></td>'+
				'<td width="100"><input name="header" type="checkbox" value="1" checked="checked" />'+getLang('header')+'<br/>'+
					'<input name="footer" type="checkbox" value="1" />'+getLang('footer')+'<br/>'+
					'<input name="signature" type="checkbox" value="1" class="ui-corner-right">'+getLang('signature')+'<br/>'+
					'<span class="buttonSet">'+
					'<input type="radio" name="orientation" id="orientation_1" value="1" checked="checked"/><label for="orientation_1">'+getLang('portrai')+'</label>'+
					'<input type="radio" name="orientation" id="orientation_2" value="2" /><label for="orientation_2">'+getLang('landscap')+'</label>'+
					'</span>'+
				'</td>'+
			'</tr>'+
			'<tr>'+
				'<td valign="top"><label class="label reverse_align" float:left">'+getLang('title')+': </label></td>'+
				'<td>'+
					'<input name="title" type="text" class="input_double" />'+
				'</td>'+
			'</tr>'+
		'</table>'+
	'</form>';
	
	var buttons = [{ 
		text: getLang('print'), 
		click: function() { 
			$('#print_form').submit();
			$(this).dialog('close');

		}
	}, { 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('print', getLang('print'), html, 440, 250, buttons, false);
	$("#print_content").val(data);
	$('.buttonSet').buttonset();
}

function px2cm(px) {
  var d = $("<div/>").css({ position: 'absolute', top : '-1000cm', left : '-1000cm', height : '1000cm', width : '1000cm' }).appendTo('body');
  var px_per_cm = d.height() / 1000;
  d.remove();
  return px / px_per_cm;
}

function initPrintStyle(){
	var printBody = $('body');
	var bodyHeight = px2cm(printBody.outerHeight()) ;
	/*if(bodyHeight > 29){
		$('.page_footer').css('position', 'absolute');
	}*/

	// replace select 
	$(printBody).find('select').each(function(){
		$(this).replaceWith('<div class="ui-widget-content fault_input ui-corner-right">'+$(this).find('option:selected').text()+'</div>');
	})
	
	// rabio button set
	$(printBody).find('.buttonSet').each(function(){
		var $label = $(this);
		var $span =  $label.find('span');
	//	$(this).replaceWith('<div class="ui-widget-content fault_input ui-corner-right" >'+$span.html()+'</div>');

		$label.find('input[type="radio"]').each(function(){
			if($(this).is(':checked')){
				$(this).replaceWith('<span class="ui-widget-content fault_input ui-corner-right" >'+$('label[for="'+$(this).attr('id')+'"]').html()+'</span>')
				//$('label[for="'+$(this).attr('id')+'"]').show();
			} else {
				$('label[for="'+$(this).attr('id')+'"]').hide();
			}
		})
	});

	$('.ui-state-default').removeClass('ui-state-default').addClass('ui-widget-content');
//	$('.ui-state-active').removeClass('ui-state-active').addClass('ui-widget-content');
//	$('.ui-widget-header').removeClass('ui-widget-header').addClass('ui-widget-content');

	/*$(printBody).find('img').each(function(){
		$(this).attr('src', $(this).attr('src'));
	})*/
	
	// Checkbox
	$(printBody).find('input[type="checkbox"]').each(function(){
		if($(this).attr('checked') == 'checked'){
			$(this).replaceWith('<span class="ui-icon ui-icon-check def_float"></span>');
		} else {
			var $label = $(this).parent('label');
			$label.hide();
		}
	})

	// replace the inputs by those values
	$(printBody).find('input[type="text"]').each(function(){
		var cl = $(this).attr('class');
		var val = $(this).val() != '' ? $(this).val() : '&nbsp;';
		$(this).replaceWith('<div class="fault_input ui-corner-right" style="width:'+($(this).width())+'px">'+val+'</div>');
	})
	
	// replace the textarea by those values
	$(printBody).find('textarea').each(function(){
		$(this).replaceWith($(this).val());
	})
}