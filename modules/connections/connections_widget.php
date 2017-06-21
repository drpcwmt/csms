<?php
## currency widget

$connections_widget = new Layout();

$servers = array();

	if($this_system->getSettings('busms_server') == '1'){
		$servers[] = $this_system->getBusms();
	}
	if($this_system->getSettings('safems_server') == '1'){
		$servers[] = $this_system->getSafems();
	}
	if($this_system->getSettings('busms_server') == '1'){
		$servers[] = $this_system->getBusms();
	}
	if($this_system->getSettings('accms_server') == '1'){
		$servers[] = $this_system->getAccms();
	}
	if($this_system->getSettings('libms_server') == '1'){
		$servers[] = $this_system->getLibms();
	}

if(!in_array($this_system->type, array('sms', 'hrms'))){
	$systems = do_query_array("SELECT * FROM connections ORDER BY type ASC");
	foreach($systems as $sys){
		switch($sys->type){
			case 'sms':
				$servers[] = new SMS($sys->id);
			break;
			case 'hrms':
				$servers[] = new HrMS($sys->id);
			break;
			case 'busms':
				$servers[] = new BusMS($sys->id);
			break;
			case 'accms':
				$servers[] = new AccMS($sys->id);
			break;
			case 'safems':
				$servers[] = new SafeMS($sys->id);
			break;
			case 'storems':
			//	$servers[] = new StoreMS($sys->id);
			break;
			case 'libms':
		//		$servers[] = new LibMS($sys->id);
			break;
			
		}
		
	}
}
$trs = array();
foreach($servers as $server){
	$trs[] = write_html('tr', '',
		'<input type="hidden" name="url" value="'.$server->url.'" />'.
		write_html('td', 'width="24"', '<img src="assets/img/mini_loading.gif" width="20" height="20" class="status" />').
		write_html('td', '', 
			'<img src="http://'.$server->url.'/'.$server->getLogo().'" style="vertical-align:middle" width="20" height="20" /> '.
			$server->getName()
		)
	);
}

$widget = write_html('fieldset', 'id="connections_widget" style="max-height:130px; overflow:auto"',
	write_html('legend', '', $lang['servers']).
	write_html('table', 'class="result" id="connections_tables"',
		implode('', $trs)
	)
);