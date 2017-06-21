<?php

// Default body -> routes list
$sql = "SELECT * FROM routes";
if(isset($_GET['w'])){
	$where = str_replace(',', ' AND ', $_GET['w']);
	$where = str_replace('-', '=', $where);
	$sql .= ' WHERE '.$where;
}

$routes = do_query_resource($sql, MySql_Database);
if(mysql_num_rows($routes) > 0){
	$routes_list_trs="";
	while($route = mysql_fetch_assoc($routes)){
		$driver = new Employers($route['driver_id']);
		$matron = new Employers($route['matron_id']);
		$route_id = $route['id'];
		$route_m = do_query("SELECT MIN(arrival_time), MAX(arrival_time) FROM routes_parcour WHERE route_id=$route_id AND timming='m'",MySql_Database);
		$route_e = do_query("SELECT MIN(arrival_time), MAX(arrival_time) FROM routes_parcour WHERE route_id=$route_id AND timming='e'",MySql_Database);
		$count_std = count(do_query_array("SELECT con_id FROM route_std WHERE route_id=$route_id", MySql_Database));
		$routes_list_trs .= write_html('tr', '',
			write_html('td', 'class="unprintable"', 
				write_html('button', 'class="ui-state-default hoverable" style="width:24px; height:24px" onclick="openRouteInfos('. $route_id.')"',  write_icon('extlink')) 
			).
			write_html('td', '', $route['id']).
			write_html('td', '', $route['region']).
			write_html('td', '', getBusCodeFromId($route['bus_id'])).
			write_html('td', '', $driver->getName()).
			write_html('td', '', $matron->getName()).
			write_html('td', '', $count_std).
			write_html('td', '', unixToTime($route_m['MIN(arrival_time)'])).
			write_html('td', '', unixToTime($route_m['MAX(arrival_time)'])).
			write_html('td', '', unixToTime($route_e['MIN(arrival_time)'])).
			write_html('td', '', unixToTime($route_e['MAX(arrival_time)']))
		);		
	}
	
	
	$routes_table = write_html('div', 'id="route_list" class="ui-widget-content ui-corner-all"',
		write_html('table', 'class="tablesorter"',
			write_html('thead', '',
				write_html('tr', '', 
					write_html('th', 'rowspan="2" width="16" class="unprintable" style="background-image:none"', '&nbsp;').
					write_html('th', 'rowspan="2"', $lang['no.']).
					write_html('th', 'rowspan="2"', $lang['region']).
					write_html('th', 'rowspan="2"', $lang['bus_code']).
					write_html('th', 'rowspan="2"', $lang['driver']).
					write_html('th', 'rowspan="2"', $lang['matron']).
					write_html('th', 'rowspan="2"', $lang['count_std']).
					write_html('th', 'colspan="2" style="background-image:none"', $lang['morning']).
					write_html('th', 'colspan="2" style="background-image:none"', $lang['evening'])
				).
				write_html('tr', '', 
					write_html('th', '', $lang['departure']).
					write_html('th', '', $lang['arrival']).
					write_html('th', '', $lang['departure']).
					write_html('th', '', $lang['arrival'])
				)
			).
			write_html('tbody', '', $routes_list_trs)
		)
	);
} else {
	$routes_table = write_error($lang['no_routes_avaible']);	
}

?>