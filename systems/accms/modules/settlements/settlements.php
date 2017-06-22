<?php
## Settlements 
if(isset($_GET['new'])){
	echo Settlements::loadLayout('new');
}elseif(isset($_GET['trans_id'])){
	echo Settlements::loadLayout(safeGet($_GET['trans_id']));
}elseif(isset($_GET['save_trans'])){
	echo Settlements::_save($_POST);

	// search Form
}elseif(isset($_GET['search_form'])){
	echo Settlements::loadsearchFrom();

	// search result
}elseif(isset($_GET['search'])){
	
	$post = array();
	foreach($_GET as $key=>$value){
		$post[$key]=safeGet($value);
	}
	echo Settlements::getList($post);

	// Daily list
}elseif(isset($_GET['list'])){
	$post['date'] = $_GET['list'] !='' ? safeGet($_GET['list']) : date('d').'/'.date('m').'/'.date('Y');
	echo Settlements::getList($post);
} elseif(isset($_GET['new_exchange'])){
	echo Settlements::newExchange();
} elseif(isset($_GET['save_exchange'])){
	echo Settlements::saveExchange($_POST);
	
} else {
	// defaul body
	echo Settlements::loadMainLayout();
}
?>