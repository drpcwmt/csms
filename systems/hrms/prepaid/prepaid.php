<?php

## Prepaid

if(isset($_GET['new'])){
	echo Prepaid::newPrepaid();
} else {
	echo Prepaid::loadMainLayout();
}
?>