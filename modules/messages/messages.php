<?php
## MsgMS ##
## INBOX

require_once('scripts/msgms_functions.old.php');


	// ajax unread message response
if(isset($_GET['count_msg'])){
	$answer = array();
	$answer['error'] = "";
	$answer['count'] = 0;
	if($loged){
		if(!isset($_SESSION['last_msg_chk'])){
			 $_SESSION['last_msg_chk'] = time();
		} 
		$last_chk = $_SESSION['last_msg_chk'];
		$_SESSION['last_msg_chk'] = time();
		
		$messages = Messages::getList(0, 10, false, false);
		foreach($messages as $m){
			if($m->date > $last_chk){
				$answer['alerts'][] =  $m->title;
			}
		}
		$answer['count'] = count(Messages::getList(false, false, false, false));
	}
	echo json_encode($answer);

	// read MEssage
} elseif(isset($_GET['read'])){
	$message = new Messages(safeGet($_GET['msg_id']));
	echo $message->read();

// list  Message to normal layout;
} elseif(isset($_GET['list'])){
	$max = 10;
	$start = 0;
	if(isset($_GET['type'])){ // serve Mails, System alerts, News for future use;
		$cur_view = safeGet($_GET['type']);
	} else {
		// Mails view
		$cur_view = (isset($_GET['inbox']) ? 
			'inbox' 
		: 
			(isset($_GET['sent']) ? 
				'sent' 
			: 
				(isset($_GET['trash'])? 
					'trash' 
				: 
					'inbox'
				)
			)
		);
	}

	if(isset($_GET['page'])){
		$start = (safeGet($_GET['page']) -1) * $max;
	} 
	echo Messages::getlayoutList(isset($_GET['page']) ? safeGet($_GET['page']) : 1 , $cur_view);

// Home widget
} elseif(isset($_GET['widget'])){
	include('messages_widget.php');
	
// New Message
} elseif(isset($_GET['compose'])){
	echo Messages::_new();

// Submit compse message
}elseif(isset($_GET['send'])){
	$message = new Messages();
	$message->sender = $_POST['sender'];
	$message->date = time();
	$message->type = 'message';
	$message->title = $_POST['title'];
	$message->content = $_POST['content'];
	// recivers
	if(strpos($_POST['reciver_value'], ',') !== false){
		$ids = explode(',', $_POST['reciver_value']);
	} else {
		$ids = array($_POST['reciver_value']);
	}
	foreach($ids as $reciver){
		$r = explode('-', $reciver);
		$message->addReciver($r[0], $r[1]);
	}
	if( $message->send()){
		echo json_encode(array('error'=>''));
	} else {
		echo json_encode(array('error'=>'Cant send Message'));	
	}
	
// Delete Message	
} elseif(isset($_GET['delete'])){
	$ids = getIdsArray(safeGet($_GET["delete"]));
	foreach($ids as $id){
		$message = new Messages($id);
		$message->_delete();
	}
	echo json_encode(array('error'=>''));

// Restore Message From trash to inbox
} elseif(isset($_GET["restore"])){
	$ids = getIdsArray(safeGet($_GET["delete"]));
	foreach($ids as $id){
		$message = new Messages($id);
		$message->_restore();
	}
	echo json_encode(array('error'=>''));
}else if(isset($_GET["delsysmsg"])){
	$msg_ids = strpos($_GET["delsysmsg"], ',') !==false ? explode(',', $_GET["delsysmsg"]) : array($_GET["delsysmsg"]);
	foreach($msg_ids as $id){
		$dels[] = "(id=$id)";
	}
	$answer = array();
	if(do_query_edit("DELETE FROM messages WHERE type='system' AND (".implode(" OR ", $dels).")", MSG_Database)){
		$answer['error'] = "";
	} else {
		$answer['error'] = $error;
	}
	echo json_encode($answer);
} else {
	$cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
	if(isset($_GET['type'])){
		$folder = safeGet($_GET['type']);
		if($folder=="messages"){
			$folder = (isset($_GET['inbox']) ? 
				'inbox' 
			: 
				(isset($_GET['sent']) ? 
					'sent' 
				: 
					(isset($_GET['trash'])? 
						'trash' 
					: 
						'inbox'
					)
				)
			);
		} 
		echo Messages::getMessageTypeLayout($cur_page, $folder);
	} else {
		echo Messages::loadMainLayout();
	}
}
exit;
/**************** BODY ******************************/
$type = isset($_GET['type']) ? $_GET['type'] : 'messages';
$view_array = array('inbox'=>$lang['inbox'], 'sent'=>$lang['sent'], 'trash'=>$lang['trash']);
$cur_view = (isset($_GET['inbox']) ? 
	'inbox' 
: 
	(isset($_GET['sent']) ? 
		'sent' 
	: 
		(isset($_GET['trash'])? 
			'trash' 
		: 
			'inbox'
		)
	)
);
// default body
$user = Messages::getCurUser();
$msg_user_id = $user->user_msg_id;

$cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
$recperpage = 10;
$first_rec = $cur_page ==1 ? 0 : ( ($cur_page-1) * $recperpage ) - 1;
$messages_list = Messages::getListTable($first_rec, $recperpage, true);

if(isset($_GET['type'])){
	if($type == 'messages'){
		//include_once('messages_list.php');
		include_once('messages_messages.php');
		if(isset($_GET['list'])){
			echo $messages_list;
		} else {
			echo $messages_toolbox. $message_layout;
		}
	} elseif($type == 'system'){
		include_once('messages_system.php');
		if(isset($_GET['list'])){
			echo $messages_list;
		} else {
			echo $message_layout;
		}
	}
	exit;
}

include_once('messages_messages.php');

echo write_html('div', 'class="tabs"',
	write_html('ul', '',
		write_html('li', '', write_html('a', 'href="#messages_tab"',$lang['messages'])).
		write_html('li', '', write_html('a', 'href="index.php?module=messages&type=system"',$lang['system'])).
		write_html('li', '', write_html('a', 'href="index.php?module=messages&type=news"',$lang['news']))
	).
	write_html('div', 'id="messages_tab"',
		 $message_layout
	)
);
?>