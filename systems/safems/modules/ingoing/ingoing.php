<?php
## Ingoing

if(isset($_GET['incomes'])){
	if(isset($_GET['list'])){
		$dates = array();
		$year = getNowYear();
		$dates['begin_date'] = isset($_GET['begin_date']) ? dateToUnix($_GET['begin_date']) : $year->begin_date;
		$dates['end_date'] = isset($_GET['end_date']) ?dateToUnix($_GET['end_date']) : $year->end_date;
		$type = $_GET['type'];
		echo Ingoing::getList($type, $dates, isset($_GET['begin_date']), isset($_GET['direction']) ? $_GET['direction'] : 'ingoing');
		
	} elseif(isset($_GET['save'])){
		$post = $_POST;
		$post['date'] = isset($post['date']) ? dateToUnix($post['date']) : time();
		$nts = new I18N_Arabic('Numbers');
		$nts->setFeminine(1);
		$ingoing = Ingoing::addNewPayment($post);
		if($ingoing != false){
			$recete = $ingoing->getRecete();
			echo json_encode(array('error'=>'', 'recete' => $recete));
		} else {
			echo json_encode_result(false);
		}
	}
} elseif(isset($_GET['type'])){
	$type = safeGet('type');
	if($type == 'admission'){
		echo Ingoing::loadAdmisionLayout();
	} else {
		echo Ingoing::loadLayout($type);
	}
} elseif(isset($_GET['others'])){
	if(isset($_GET['autocomplete'])){
		print Ingoing::OthersAutocomplete($_GET['term']);
		
	} elseif(isset($_GET['save'])){
		$nts = new I18N_Arabic('Numbers');
		$nts->setFeminine(1);
		$post = $_POST;
		$post['date'] = dateToUnix($post['date']);
		$ingoing = Ingoing::addNewPayment($post);
		if($ingoing != false){
			$recete = $ingoing->getRecete();
			echo json_encode(array('error'=>'', 'recete' => $recete));
		} else {
			echo json_encode_result(false);	
		}
	} else {
		echo Ingoing::othersLayout();
	}
} elseif(isset($_GET['print_recete'])){
	$nts = new I18N_Arabic('Numbers');
	$ingoing = new Ingoing(safeGet('print_recete'));
	echo $ingoing->getRecete();
} else {
	echo Ingoing::loadMainLayout();
}
?>
