<?php
## Products main activity

if(isset($_GET['newform'])){
	if($prvlg->_chk('product_add')){
		echo Products::newForm(safeGet('sub_id'));
	} else {
		echo write_error('no_privileges');
	}
}elseif(isset($_GET['prod_id'])){
	$prod = new Products(safeGet($_GET['prod_id']));
	if(isset($_GET['data'])){
		$prod->error = '';
		echo json_encode($prod);
	} else {
		echo $prod->loadLayout();
	}
} elseif(isset($_GET['save'])){
	if($prvlg->_chk('product_add')){
		echo Products::_save($_POST);
	} else {
		echo json_encode_result(array('error'=>write_error($lang['no_privileges'])));
	}
} elseif(isset($_GET['autocomplete'])){
	$value = safeGet($_GET['term']);
	echo Products::getAutocomplete($value); // json out
} else {
	echo Products::loadMainLayout();
} 

?>