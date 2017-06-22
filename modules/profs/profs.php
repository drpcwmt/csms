<?php
if( isset($_GET['import'])){
		if(getPrvlg("resource_edit_profs")){
			echo json_encode(Profs::_import());
		} else {
			echo write_error($lang['no_privilege']);	
		}
	} else {
		echo Resources::loadItemsLayout($resource_type);
	} 

?>