<?php 
## application inter view
if(isset($_GET['getlist'])){
	echo Applications::getList(safeGet('getlist'));
} else {
	echo Applications::LoadMainLayout();
}
?>