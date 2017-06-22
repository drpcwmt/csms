<?php
 ## othersincomes

if(isset($_GET['open_income'])){
	$income = new OthersIncomes(safeGet('open_income'));
	echo $income->loadLayout();
} elseif(isset($_GET['newincome'])){
	echo OthersIncomes::newIncomeForm(); 
} elseif(isset($_GET['saveincome'])){
	echo OthersIncomes::_save($_POST); 
} else {
	echo OthersIncomes::loadMainLayout(); 
}
?>