<?php
// Fees Privilegs
if($this_system->type == 'sms' || $this_system->type == 'safems'){
	$privilges = array(
		'read_std_fees',
		'read_std_fees_stat',
		'edit_std_fees',
		'edit_std_profil'
	);
}  elseif( $this_system->type == 'accms'){
	$privilges = array('read_std_fees');
}
?>