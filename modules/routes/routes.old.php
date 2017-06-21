<?php
## BUS MS 
## Bus Main file

// New route form
if(isset($_GET['newroute'])){
	$driver = false;
	echo write_html('form', 'id="new_route_form"', 
		write_html('table', '',
			write_html('tr', '',
				write_html('td', 'width="120"',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['no.'])
				).
				write_html('td', '',
					'<input type="text" id="id" name="id"  class="required"/>'
				)
			).
			write_html('tr', '',
				write_html('td', 'width="120"',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['region'])
				).
				write_html('td', '',
					'<input type="text" id="region" name="region" class="required" />'
				)
			).
			write_html('tr', '',
				write_html('td', 'width="120"',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['driver'])
				).
				write_html('td', '',
					'<input type="text" id="driver_name" class="input_double required"/>'.
					'<input type="hidden" id="driver_id" name="driver_id" class="autocomplete_value"/>'
				)
			).
			write_html('tr', '',
				write_html('td', 'width="120"',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['matron'])
				).
				write_html('td', '',
					'<input type="text" id="matron_name" class="input_double required"/>'.
					'<input type="hidden" id="matron_id" name="matron_id" class="autocomplete_value"/>'
				)
			).
			write_html('tr', '',
				write_html('td', 'width="120"',
					write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['bus_code'])
				).
				write_html('td', '',
					write_html_select('name="bus_id" id="bus_id" class="combobox"', getBusList(true), '')
				)
			)
		)
	);
	exit;
}

// Submit New Route
if(isset($_POST['region'])){
	setJsonHeader();
	$error = false;
	$chk = do_query("SELECT id FROM routes WHERE id='".$_POST['id']."'", BUS_Database);
	if($chk != false && $chk['id'] != ''){
		if(!do_query_edit("UPDATE routes SET region='".$_POST['region']."', bus_id='".$_POST['bus_id']."', driver_id='".$_POST['driver_id']."', matron_id='".$_POST['matron_id']."' WHERE id=".$_POST['id'], BUS_Database)){
			$error = true;
		} 
		$route_id = $_POST['id'];
	} else {
		$route_id = insertToTable('routes', $_POST);
		if($route_id == false){
			$error =true;
		}
	}
	if($error == false){
		echo "{\"error\" : \"\", \"id\" : \"$route_id\"}";
	} else {
		echo "{\"error\" : \"".$lang['error_updating']."\"}";
	}
	exit;
}

if(isset($_GET['route_id'])){
	include('routes_details.php');
	exit;
}

require_once('routes_list.php');

if(isset($_GET['w'])){
	echo write_html('h2', 'class="title"', $lang['routes']).
	$routes_table;
	exit;
}

$routes_toolbar= write_html('div',' class="toolbox"', 
	write_html('a', 'onclick="newRoute()"',$lang['new'].write_icon('plus')).
	write_html('a', 'onclick="deleteRoutes()"',$lang['delete_all'].write_icon('close')).
	write_html('a', 'class="print_but" rel="#route_list"',$lang['print_list'].write_icon('print'))
);

echo write_html('div', 'id="routes_div" class="ui-widget-content ui-corner-all"', 
	write_html('h2', 'class="title"', $lang['routes']).
	$routes_toolbar.
	$routes_table
);

?>
