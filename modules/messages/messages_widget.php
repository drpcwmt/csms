<?php
## Message Widget
$widget = '';
$begin_time = time() -( 7* 24* 60 *60);
$msg = Messages::getListTable(0, 8, false, false, $begin_time);
if($msg != false){
	$widget = write_html('fieldset', 'class="msg_list-messages"', 
		write_html('legend', '', $lang['messages']).
		$msg
	);
}