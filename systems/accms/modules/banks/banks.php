<?php
## banks

if(isset($_GET['code'])){
	$bank = new Banks(safeGet($_GET['code']));
	echo $bank->loadLayout();
} else {
	echo Banks::loadMainLayout();
}

?>