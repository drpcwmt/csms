<?php
## Category Manager Activity

/*if(isset($_GET['list'])){
	if($_GET['list'] == 'cats'){
		echo Categorys::getCatsList(); // json out
	} elseif($_GET['list'] == 'subs'){
		if(isset($_GET['cat_id'])){
			$cat = new Categorys(safeGet($_GET['cat_id']));
			$subs = $cat->getSubsList(); 
			if(isset($_GET['view']) && $_GET['view'] == 'options'){
				$sub_array = objectsToArray($subs);
				echo write_html('option', 'value=""', '').write_select_options($sub_array);
			} else {
				// json out
				echo $subs;
			}
		} else {
			echo json_encode(array('error'=> $lang['error']));	
		}
			
	} elseif($_GET['list'] == 'prods'){
		if(isset($_GET['sub_id'])){
			$sub = new Subcategorys(safeGet($_GET['sub_id']));
			$view = isset($_GET['view']) && $_GET['view'] == 'list' ? 'list' : 'icon';
			echo $sub->getProductHtml($view); // json out
		} else {
			echo write_error($lang['error']);	
		}
			
	}
}*/
if(isset($_GET['printtree'])){
		echo Categorys::printTree();

} elseif(isset($_GET['searchform'])){
	if($prvlg->_chk('tree_read')){
		echo Categorys::loadSearchLayout();
	} else {
		echo write_error($lang['error_no_privilege']);
	}

} elseif(isset($_GET['sublist'])){
	$cat = new Categorys(safeGet('cat_id'));
	$subs = $cat->getSubCat();
	echo write_select_options(objectsToArray($subs));	
	
} elseif(isset($_GET['newsub'])){
	echo SubCategorys::newSubCat(safeGet('cat_id'), isset($_GET['sub_id']) ? safeGet('sub_id'): '');

} elseif(isset($_GET['savesub'])){
	echo SubCategorys::_save($_POST);
	
} elseif(isset($_GET['autocomplete'])){
	$value = safeGet($_GET['term']);
	
	if($_GET['autocomplete'] == 'cats'){
		echo Categorys::getAutocompleteCats($value); // json out
	} elseif($_GET['autocomplete'] == 'subs'){
		if(isset($_GET['cat_id'])){
			$cat = new Categorys(safeGet($_GET['cat_id']));
			echo $cat->getAutocompleteSubs($value); // json out
		} else {
			echo json_encode(array('error'=> $lang['error']));	
		}
	}
	
} else if(isset($_GET['save'])){
	echo Categorys::_save($_POST);

} else if(isset($_GET['cat_id'])){
	$category = new Categorys(safeGet('cat_id'));
	echo $category->loadLayout();
	
} else if(isset($_GET['sub_id'])){
	$sub_category = new SubCategorys(safeGet('sub_id'));
	echo $sub_category->loadLayout();


} else {
	echo Categorys::loadMainLayout();	
}
