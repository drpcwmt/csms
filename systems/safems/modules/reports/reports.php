<?php
## Ingoing

if(isset($_GET['accounttree'])){
	echo Accounts::loadTreeLayout();
	
} else {
	$layout = new Layout();
	$layout->acc_code = $this_system->getSettings('this_acc_code');
	$layout->menu = fillTemplate('modules/reports/templates/reports_menu.tpl', $layout);
	$layout->template = 'modules/reports/templates/main_layout.tpl';
	echo $layout->_print();

}
?>
