// EXPORT
function exportTable($but){
	container = $but.attr('rel');
	var $xml = $("<table>");
	var $higherLayer = returnHigherLayer();
	var $content = $higherLayer.find(container);
	$content.find(".tooltip").remove();
	$content.find(".tablesorter").each(function() {
		$th = $(this).find('th');
		$("tr:has(>td)", $(this)).each(function() {
			var $row = $("<row>");
			$xml.append($row);
		
			$("td", this).each(function(i) {
				if(!$(this).hasClass('unprintable')){
					$row.append($('<cell name="'+$th.eq(i).text()+'">').text($(this).text()));
				}
			})
		});
	});
	$content.find(".result").each(function() {
		$th = $(this).find('th');
		$("tr:has(>td)", $(this)).each(function() {
			var $row = $("<row>");
			$xml.append($row);
		
			$("td", this).each(function(i) {
				if(!$(this).hasClass('unprintable')){
					$row.append($('<cell name="'+$th.eq(i).text()+'">').text($(this).text()));
				}
			})
		});
	});
	var html = '<form id="export_form" target="_blank" method="POST" action="index.php?plugin=xml" class="ui-corner-all ui-state-highlight">'+
		'<textarea name="data" class="hidden"><table>'+$xml.html()+'</table></textarea>'+
		'<table>'+
			'<tr>'+
				'<td valign="top" width="100"><label class="label reverse_align" float:left">'+getLang('format')+': </label></td>'+
				'<td >'+
					'<select name="type">'+
						'<option value="xml">XML</option>'+
						'<option value="csv">Excel</option>'+
					'</select>'
				'</td>'+
			'</tr>'+
		'</table>'+
	'</form>';
	var buttons = [{ 
		text: getLang('export'), 
		click: function() { 
			$('#export_form').submit();
			$(this).dialog('close');

		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('export', getLang('export'), html, 320, 200, buttons, false);
}

