<?php
## system messages layout

// clean up old message
if($MS_settings['remove_system_alert_after'] > 0){
	$min_date = time() - $MS_settings['remove_system_alert_after'] * 86400;
	do_query_edit("DELETE FROM messages WHERE type='system' AND date<$min_date", MSG_Database);
}


// LAyout
$where = array( "reciver=".getUserId('0', 'school', $MS_settings['server_name']));			
if($_SESSION['group'] == 'student'){
	$parents = getParentsArr('student', $_SESSION['user_id'] );
	if($parents != false){
		foreach($parents as $array){
			$par_con =$array[0];
			$par_id= $array[1];
			$where[] = "reciver=".getUserId($par_id, $par_con, $MS_settings['server_name']);			
		}
	}
} elseif($_SESSION['group'] == 'superadmin'){
	$where[] = "reciver=".getUserId(0, 'superadmin', $MS_settings['server_name']);
	$where[] = "reciver=".getUserId(0, 'admin', $MS_settings['server_name']);
} else{
	$where[] = "reciver=".getUserId(0, $_SESSION['group'], $MS_settings['server_name']);
}

$sql = "SELECT * FROM messages 
WHERE type='system' 
AND ( 
	reciver=$msg_user_id 
	OR (".
		implode(' OR ', $where).
	")
)"; 

$count_all_msg = mysql_num_rows( do_query_resource($sql, MSG_Database));
$total_pages = ceil($count_all_msg/$recperpage);
$msg_navbar = write_html('form', 'id="msg_list_nav" class="toolbox"',
	($cur_page != 1 ?
		write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList(1, \'system\')"', write_icon('seek-first')).
		write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList('. ($cur_page-1) .', \'system\')"', write_icon('seek-prev'))
	: '').
	write_html('a', 'class="ui-state-default"', $lang['page'].': '.$cur_page.'/'.$total_pages).
	($cur_page != $total_pages ?
		write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList('. ($cur_page+1) .', \'system\')"', write_icon('seek-next')).
		write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList('.$total_pages.', \'system\')"', write_icon('seek-end'))
	: '')
);

$sql .= " ORDER BY date DESC LIMIT $first_rec,$recperpage";
$messages = do_query_resource($sql, MSG_Database);


if(mysql_num_rows($messages) > 0){
	$messages_list = $msg_navbar.
	'<table class="result">
		<thead>
			<tr>
				<th width="16px">&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>';
	while($msg = mysql_fetch_assoc($messages)){
		$messages_list .= write_html('tr', 'style="font-weight:'.($msg['seen'] == 0 ? 'bold' : '500' ).'"',
			write_html('td', ' valign="top"', write_icon('alert')).
			write_html('td', ' valign="top" '.($type != 'system' ? 'onclick="getMsgContent('.$msg['id'].')"' : ''), 
				write_html('span', 'class="rev_float"',date('D d/m/Y h:i a', $msg['date'])).
				write_html('span', '',$msg['title']).'<br>'.
				($type == 'system' ?
					$msg['content']
				:
					(isset($_GET['sent']) ? $lang['to'] : $lang['from']).': '. 
					getMsgUserNameFromId((isset($_GET['sent']) ? $msg['reciver'] : $msg['sender'])).'<br>'
				)
				 
			)
		);
	}
	$messages_list .= '</tbody>
	</table>';
} else {
	$messages_list =write_html('div', 'class="ui-corner-all ui-state-error" style="padding:10px"', $lang['no_msg']);
}

$message_layout = write_html('table', 'width="100%" cellspacing="0" cellpadding="0"', 
	write_html('tr','',
		write_html('td', ' valign="top"', 
			write_html('div', 'class="ui-corner-bottom ui-widget-content"',
				write_html('div', 'id="msg_list-system" style="max-height:400px; overflow:auto;padding:5px"', $messages_list)
			)
		)
	)
);


?>