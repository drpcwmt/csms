<?php
## message List
if(isset($_GET['sent'])){
	$sql = "SELECT * FROM messages WHERE type='messages' AND sender=$msg_user_id->id AND trash=0";
} elseif(isset($_GET['trash'])){
	$sql = "SELECT * FROM messages WHERE type='messages' AND (reciver=$msg_user_id OR sender=$msg_user_id) AND trash=1";
} else {
	$sql = "SELECT * FROM messages WHERE type='messages' AND reciver=$msg_user_id AND trash=0";
}


// NavBar
$count_all_msg = mysql_num_rows(do_query_resource($sql, MSG_Database));
$total_pages = ceil($count_all_msg/$recperpage);
$msg_navbar = write_html('form', 'id="msg_list_nav" class="toolbox"',
	($cur_page != 1 ?
		write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList(1, \'messages\')"', write_icon('seek-first')).
		write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList('. ($cur_page-1) .', \'messages\')"', write_icon('seek-prev'))
	: '').
	write_html('a', 'class="ui-state-default"', $lang['page'].': '.$cur_page.'/'.$total_pages).
	($cur_page != $total_pages ?
		write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList('. ($cur_page+1) .', \'messages\')"', write_icon('seek-next')).
		write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList('.$total_pages.', \'messages\')"', write_icon('seek-end'))
	: '')
);


// get Last Message 
$sql .= " ORDER BY date DESC LIMIT $first_rec,$recperpage";
$messages = do_query_resource($sql, MSG_Database);
$lastmsg = mysql_fetch_assoc($messages);


// Building list
$messages = do_query_resource($sql, MSG_Database);
if(mysql_num_rows($messages) > 0){
	$messages_list = $msg_navbar.
	'<table class="tablesorter">
		<thead>
			<tr>
				<th width="20px"><input type="checkbox" id="select-all" /></th>
				<th width="16px">&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>';
	while($msg = mysql_fetch_assoc($messages)){
		$messages_list .= write_html('tr', 'style="font-weight:'.($msg['seen'] == 0 ? 'bold' : '500' ).'"',
			write_html('td', ' valign="top"', '<input type="checkbox" name="select_mail" value="'.$msg['id'].'" />').
			write_html('td', ' valign="top"', '<span class="ui-icon '. ($msg['sender'] == 0 ? 'ui-icon-alert': 'ui-icon-mail-'. ($msg['seen'] == 0 ? 'closed' : 'open')) .'"></span>').
			write_html('td', ' valign="top" class="hand" '.($type != 'system' ? 'onclick="getMsgContent('.$msg['id'].')"' : ''), 
				write_html('span', 'class="rev_float"',date('D d/m/Y h:i a', $msg['date'])).
				$msg['title'].'<br>'.
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

?>