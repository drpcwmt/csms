<?php
## Warhouses main activity
require_once('clients.class.php');
//$products =  new Products();

if(isset($_GET['newform'])){
	echo Clients::newForm();
} elseif(isset($_GET['group_id'])){
	$group_id = safeGet('group_id');
	//$cc = new CostcentersGroup($group_id);
	$groups = Clients::getGroupByCC($group_id);
	$clients = Clients::getList($first_group_id);
	$layput = new stdClass();
	$layout->clients_trs = '';
	foreach($clients as $client){
		$layout->clients_trs .= write_html('tr', '',
			write_html('td', '',
				write_html('button', 'class="ui-corner-all ui-state-default hoverable circle_button" action="openClient" clientid="'.$client->id.'"', write_icon('extlink'))
			).
			write_html('td', '',
				write_html('text', 'class="label-client-'.$client->id.'"', $client->getName())
			).
			write_html('td', '',
				$client->getBalance()
			)
		);
	}
	echo fillTemplate("modules/clients/templates/clients_list.tpl", $layout);
	
} elseif(isset($_GET['client_id'])){
	$client = new Clients(safeGet($_GET['client_id']));
	echo $client->_toDetails();
} elseif(isset($_GET['save'])){
	echo Clients::_save($_POST);
} elseif(isset($_GET['autocomplete'])){
	$value = safeGet($_GET['term']);
	echo Clients::getAutocomplete($value); // json out

} else {
	echo Clients::loadMainLayout();
} 

?>