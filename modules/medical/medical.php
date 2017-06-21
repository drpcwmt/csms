<?php
## Medical Activity

if(isset($_GET['visite_form'])){
	if(isset($_GET['std_id'])){
		$std_id = safeGet($_GET['std_id']);
	} else {
		$std_id = false;
	}
	echo Medical::visitForm($std_id);
} elseif(isset($_GET['save_visit'])){
	echo Medical::saveVisit($_POST);
} elseif(isset($_GET['delete_visit'])){
	echo Medical::deleteVisit($_POST['visit_id']);
	
} elseif(isset($_GET['save_date'])){
	echo Medical::saveData($_POST);
} else {
	$std_id = $_GET['std_id'];
	
	$medical = new Medical($std_id);
	echo $medical->loadLayout();
}
?>