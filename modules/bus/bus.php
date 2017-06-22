<?php
// Bus Details

if(isset($_GET['del_bus'])){
	echo Bus::delBus($_POST);

} elseif(isset($_GET['bus_id'])){
	$bus = new Bus(safeGet($_GET['bus_id']));
	echo $bus->loadLayout();

} elseif(isset($_GET['new_bus'])) {
	echo Bus::loadNewBusFrom($_POST);
} elseif(isset($_GET['save'])){
	echo Bus::saveBus($_POST);
} else {
	echo Bus::loadMainLayout();

}

?>