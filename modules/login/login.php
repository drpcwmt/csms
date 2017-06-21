<?php
## Login main 
if(isset($_GET['dologin'])){
	$answer = array();
	$answer['error'] = '';
	$result = Login::doLogin($_POST);
	
	if($result !== true){
		$answer['login'] = 'no';
		$answer['errorlogin'] = $result;
	} else {
		$answer['login'] = 'ok';
		$answer['year'] = $_SESSION['year'];
	} 
	echo json_encode($answer);
	
} else {
	echo Login::logingPage();
}
?>