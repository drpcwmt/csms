<?php
## MsgMS ##
## compose

$reciver_name = '';
$reciver_value = '';
$msg_content = '';
$subject = '';

if(isset($_GET['reply']) || isset($_GET['forward'])){
	$msg = do_query("SELECT * FROM messages WHERE id=".( isset($_GET['reply']) ? $_GET['reply'] : $_GET['forward']), MSG_Database);
	if(isset($_GET['reply'])){
		$reciver_name ='<span class="reciver ui-state-default">'.getMsgUserNameFromId($msg['sender']).'</span>' ;
		$g= do_query("SELECT user_group FROM users WHERE user_id=".$msg['sender'], MSG_Database);
		$reciver_value = $g['user_group'].'-'.$msg['sender'];
		$subject = 'Re: '.$msg['title'];
	} 
	if(isset($_GET['forward'])){
		$msg_content = $msg['content'];
		$subject = 'Fw: '.$msg['title'];
	} 
}

if(isset($_GET['reciver'])){
	$group = $_GET['group'];
	$reciver_val_arr = array();
	$reciver_name_arr = array();
	
	if(strpos($_GET['reciver'], ',') !== false){
		$ids = explode( ',', $_GET['reciver']);
	} else {
		$ids = array($_GET['reciver']);
	}
	foreach($ids as $id){
		$user_id = getUserId($id, $group, $MS_settings['server_name']);
		$reciver_val_arr[] = $user_id;
		$reciver_name_arr[] = getMsgUserNameFromId($user_id);
	}
	$reciver_value = implode(',', $reciver_val_arr);
	$reciver_name = implode(',', $reciver_name_arr);
}

/********************** Submit message *************************/
if(isset($_GET['submit'])){
	$title = $_POST['title'];
	$content = $_POST['content'];
	$user_id = getMsgUserId();
	$date = time();
	
	if(strpos($_POST['reciver_value'], ',') !== false){
		$ids = explode(',', $_POST['reciver_value']);
	} else {
		$ids = array($_POST['reciver_value']);
	}
	
	$error = false;
	foreach($ids as $id){
		$d = explode('-', $id);
		$recip_id = $d[1];
		$recip_group = $d[0];
		$msg_id = getUserId($recip_id, $recip_group, $MS_settings['server_name']);
		if(do_query_edit("INSERT INTO messages (type,sender, reciver, title, content, date, seen) VALUES ('messages', $user_id, $msg_id, '$title', '$content', $date, 0)", MSG_Database)){
			$error ? true : false;
		}
	}
	if(!$error){
		$answer['id'] = $user_id;
		$answer['error'] = "";
	} else {
		$answer['id'] = "";
		$answer['error'] = $error;
	}
	print json_encode($answer);
	exit;		
}

/********************** New message Form *************************/
$toolbox = array(array(
		"tag" => "span",
		"attr"=> 'style="padding:5px"',
		"text"=> $lang['add_recipient'].': ',
		"icon"=> ""
));
foreach($allowed_recivers as $recv){
	$toolbox[] = $recv;
}

echo write_html('table', 'width="100%" cellspacing="0" cellpadding="0"', 
	write_html('tr','',
		write_html('td', '', 
			write_html('form', 'id="compose_form"',
				createToolbox($toolbox).
				write_html('div', 'class="ui-corner-top ui-state-highlight" style="padding:5px"',
					write_html('table', 'width="100%" border="0", cellspacing="0"',
						write_html('tr', '', 
							write_html('td', 'valign="top" class="reverse_align" width="100"', 
								write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['to'])
							).
							write_html('td', '', 
								'<input type="hidden" name="reciver_value" id="reciver_value" value="'.$reciver_value.'"/>'.
								write_html('div', 'class="fault_input ui-widget-content ui-corner-right" id="reciver_name" style="width:100%; height:inherit; min-height:22px; padding:0px"', $reciver_name)
							)
						).
						write_html('tr', '', 
							write_html('td', 'class="reverse_align"', 
								write_html('label', 'class="label ui-widget-header ui-corner-left"', $lang['subject'])
							).
							write_html('td', '', '<input type="text" name="title" style="width:99%" value="'.$subject.'" />')
						)
					)
				).
				write_html('div', 'class="ui-corner-bottom ui-widget-content" style="padding:5px; overflow:auto"',
					write_html('textarea', 'name="content"  class="tinymce" style="width:99%; min-height:300px"', $msg_content)
				)
			)
		)
	)
);

?>
