<?php 
## Connections
if(isset($_GET['newconexion'])){
	$type = isset($_GET['type']) ? safeGet($_GET['type']) : '';
	echo Connections::LoadNewConection($type);

} elseif(isset($_GET['saveconection'])){
	$_POST['ccid'] = safeGet($_GET['ccid']);
	echo Connections::saveConnection($_POST);
	// submit new Cost center
	
} elseif(isset($_GET['sync'])){
	$conx_id = $_POST['id'];
	$conx = do_query_obj("SELECT * FROM connections WHERE id=$conx_id");
	if($conx->type == 'sms'){
		$system = new SMS($conx_id);
		print_r(json_encode($system->SyncStudentsAcc()));
	}
	
} elseif(isset($_GET['do_ping'])){
	$server = safeGet('do_ping');
	$answer['error'] = '';
	$ctx = stream_context_create(array('http'=>
		array(
			'timeout' => 5, 
		)
	));
	$url = file_get_contents("http://$server/?ping", false, $ctx);
	if($url != false){
		$result = json_decode($url);
		if(isset($result->last_sync) && $result->last_sync != ''){
			echo json_encode_result(array('result'=>'1'));
		} else {
			echo json_encode_result(array('error' => $lang['error1']));	
		}
	}else {
		echo json_encode_result(array('error' => $lang['error1']));	
	}
	exit;
}
?>