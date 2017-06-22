<?php
## Balance 

if(isset($_GET['start_bal'])){
	echo StartBalance::loadMainLayout();
} elseif(isset($_GET['save_start_balance'])){
	print json_encode( StartBalance::_save($_POST));
	
} else if(isset($_GET['start_acc'])){
	$main = safeGet($_GET['start_acc']);
	echo StartBalance::loadAccount($main);
	
} else if(isset($_GET['financialreport'])){
	$report = new FinancialReport(isset($_GET['cc']) ? safeGet($_GET['cc']) : '');
	//$report->currency = '';
	echo $report->loadLayout();
	
} else if(isset($_GET['incomesreport'])){
	$incomes = new IncomesReport();	
	echo $incomes->loadMainLayout(isset($_GET['cc']) ? safeGet($_GET['cc']) : '');
	
} elseif(isset($_GET['damages'])){	
	if(isset($_GET['damages_acc'])){
		$main = safeGet($_GET['damages_acc']);
		echo Damages::loadMainAccount($main);
	} elseif(isset($_GET['savedamages'])){
		echo json_encode_result(Damages::saveDamages($_POST));
	} elseif(isset($_GET['trans'])){
		echo json_encode_result(Damages::createTransaction());
	} else {
		echo Damages::loadMainLayout();
	}
	
} else {
	
	// defaul body
	echo fillTemplate('modules/balance/templates/balance_menu.tpl', array()).
	write_html('div', 'id="balance_module_body"', '');
}
?>