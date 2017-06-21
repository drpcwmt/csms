<?php
/** Messages
*
* Groups : admins pricipals, profs, parents, students, group, class, level, etab
*
*/

class Messages{
	
	public $id = '',
	$title = '',
	$content = '',
	$sender= '',
	$recivers = array(),
	$type='',
	$date='';
	
	
	private $thisTemplatePath = 'modules/messages/templates';

	
	public function __construct($id=''){
		$msgms = new MsgMS();
		$this->msgms = $msgms;
		if($id!= ''){
			$message = do_query_obj("SELECT * FROM messages WHERE id=$id", $this->msgms->database, $this->msgms->ip);	
			if($message->id != ''){
				foreach($message as $key =>$value){
					$this->$key = $value;
				}
				$this->recivers[$message->reciver_group] = array($message->reciver);
				return $this;
			} else {
				throw new Exception('Message id not found');
			}
		} else {
			$this->sender = Messages::getCurUser();
		}
	}
	
	public function read(){
		
		if($this->seen != '1'){
			do_query_edit("UPDATE messages SET seen=1 WHERE id=".$this->id, $this->msgms->database, $this->msgms->ip);
		}
		$message = $this;
		$message->day_str =  date('D d/m/Y h:i a', $message->date);
		$message->sender_name =Messages::getMsgSenderName();
		$message->reciver_name = $this->getMsgReciverName();
		unset($message->recivers);
		return fillTemplate("$this->thisTemplatePath/message.tpl", $message);
	}
	
	public function addReciver($group, $id){
		if(!isset($this->recivers[$group]) || !in_array($id, $this->recivers[$group])){
			$this->recivers[$group][] = $id;	
		}
	}	
	
	public function send(){
		$message = $this;
		if($message->content == '' || count($message->recivers) == 0){
			return false;
		}
		if($message->sender == ''){
			$cur_user = Messages::getCurUser();
			$message->sender = $cur_user->user_msg_id;
		}
		
		if($message->type == ''){
			$message->type = 'message';
		}
		
		if($message->date == ''){
			$message->date = time();
		}
		
		$result = true;
		foreach($message->recivers as $group => $users_id){
			$message->reciver_group = $group;
			foreach($users_id  as $user_id){
				$message->reciver = $user_id;
				if(!do_insert_obj((array)$message, 'messages', $this->msgms->database, $this->msgms->ip)){
					$result = false;
				}
			}
		}
		return $result;
	}
	
	
	public function getMsgSenderName(){
		global $lang, $this_system;
		if($this->sender == '0'){
			return $lang['system'];
		} else {
			$user = do_query_obj("SELECT * FROM users WHERE user_id=$this->sender", $this->msgms->database, $this->msgms->ip);
			return $this_system->getAnyNameById($user->user_group, $user->server_user_id);
		}
	}
	
	
	public function getMsgReciverName(){
		global $lang, $this_system;
		if($this->reciver_group == '0'){
			return $lang['school'];
		} else {
			if($this->reciver == '0'){
				return $lang[$this->reciver_group];
			} else {
				return $this_system->getAnyNameById($this->reciver_group, $this->reciver);
			}
		}
	}

		// Current Session user
	static function getCurUser(){
		global $this_system;
		$msgms = new MsgMS();
		$user_id = $_SESSION['user_id'];
		$group = $_SESSION['group'];
		$department = $this_system->getSettings('server_name');
		try{
			$user = new Users($group, $user_id);
		} catch(Exception $e){
			echo $e;
		}
		$user->departement = $department;

		$user_q = do_query_obj("SELECT users.user_id FROM users, connections WHERE users.server_user_id='$user_id' AND `users`.`user_group`='$group' AND users.conx_id=connections.id AND connections.code='".$this_system->getSettings('sch_code'). "' AND connections.type='".$this_system->type."'", $msgms->database, $msgms->ip);
		if(!isset($user_q->user_id )){
			$user_data = new stdClass();
			$user_data->server_user_id = $user_id;
			$user_data->user_group = $group;
			$user_data->server_name = $department;
			$new_user_id = do_insert_obj($user_data, 'users', $msgms->database, $msgms->ip);
			if($new_user_id != false){
				$user->user_msg_id = $new_user_id;

			} else {
				return false;
			}
		} else {
			$user->user_msg_id = $user_q->user_id;
		}

		return $user;
	}
	
	static function getList($from=0, $max=10, $seen=true, $folder="inbox", $begin_date=''){
		$msgms = new MsgMS();
		$user = Messages::getCurUser();
		$user_msg_id = $user->user_msg_id;
		
		$group = $user->group;
		$sql = "SELECT id FROM messages WHERE ";
		if($begin_date != ''){
			$sql .= " date>=$begin_date AND";
		}
		$sql .= ($seen == false ? " seen=0 AND" : '');
		
		if($folder == 'sent'){
			$sql .= " sender=$user_msg_id AND trash=0 ";
		} else{
			$where[] = "(reciver_group='$group' AND reciver=$user->user_id)";
			$where[] = "(reciver_group='$group' AND reciver=0)";
			if($group == 'student'){
				$parents =  getParentsArr($user->user_id, $user->user_id);
				foreach($parents as $array){
					$pcon =$array[0];
					$pcon_id= $array[1];
					$where[] = "(reciver_group='$pcon' AND reciver=$pcon_id)";
				}
				
			} elseif(!in_array($group, array('parent', 'student', 'supervisor', 'prof'))){
				$where[] = "(reciver_group='admin' AND reciver=$user->user_id)";
				$where[] = "(reciver_group='admin' AND reciver=0)";
			}
			$sql .="(".implode(' OR ', $where).")";
			
			if($folder == false) {// not all message
				$sql .= " AND trash=0 ";
			} elseif($folder == 'system'){
				$sql .= " AND sender=0 AND trash=0 ";
			} else{
				$sql .= " AND sender!=0";
				if($folder=='inbox'){
					$sql .= " AND trash=0";
				} elseif('trash'){
					$sql .= " AND trash=1";
				}
			}
		}
		$sql .= " ORDER BY date DESC";
		
		if($max != false){
			$sql .= " LIMIT $from, $max";
		}
		//echo $sql;
		//exit;
		$messages = do_query_array($sql, $msgms->database, $msgms->ip);
		$out = array();
		foreach($messages as $message){
			$out[] = new Messages($message->id);
		}
		return $out;
	}

	static function countUnread(){
		$answer['error'] = "";
		$answer['count'] =  count(Messages::getList(false, false, false, false)); //all message unseened
		return json_encode($answer);
	}
	
	static function getListTable($from=0, $max=10, $seen=true, $folder="inbox", $begin_date=''){
		global $lang;
		$messages = Messages::getList($from, $max, $seen, $folder, $begin_date);
		$lis = array();
		if($messages != false && count($messages) > 0){
			foreach($messages as $message){
				$message->day_str =  date('D d/m/Y h:i a', $message->date);
				$message->sender_name =$message->getMsgSenderName();
				$message->icon_stat = $message->seen == 1 ? 'open' : 'closed';
				$message->font_stat = $message->seen == 1 ? '500' : 'bold';
				$message->readed_class =$message->seen == 1 ? 'ui-widget-content': 'ui-state-default';
				unset($message->recivers);
				$lis[] = fillTemplate("modules/messages/templates/message_list.tpl", $message);
			}
			return write_html('ul', 'class="msg_ul" data-role="listview"',
				implode('', $lis)
			);
		} else {
			return false;
			//return write_html('div', 'class="ui-corner-all ui-state-error" style="padding:10px"', $lang['no_msg']);
		}
	}
	
		
	
	static function sendAlert($page, $obj){
		$file = "modules/messages/alerts/$page.tpl";
		$lang = $_SESSION['lang'];
		if(!file_exists("modules/messages/alerts/$page-$lang.tpl")){
			$lang = 'en';
		}
		$message = new Messages();
		$message->sender = 0;
		$message->title = $lang['alert'];
		$message->type = 'system';
		$message->content = fillTemplate("modules/messages/alerts/$page-$lang.tpl", $message);	
		return $message->send();
	}
	
	static function _new(){
		
		$compose = new Layout();;
		$compose->sender = messages::getCurUser()->user_msg_id;
		$compose->toolbox = createToolbox(Messages::getAllowedRecivers());
		return fillTemplate("modules/messages/templates/message_compose.tpl", $compose);		
	}
	
	static function getAllowedRecivers(){
		global $lang;
		$allowed_recivers = array();
		if(getPrvlg('message_student')){
			$allowed_recivers[] =array(
				"tag" => "a",
				"attr"=> 'onclick="addReciver(\'student\')"',
				"text"=> $lang['students'],
				"icon"=> "person"
			);
		}
		if(getPrvlg('message_prof')){
			$allowed_recivers[] =array(
				"tag" => "a",
				"attr"=> 'onclick="addReciver(\'prof\')"',
				"text"=> $lang['prof'],
				"icon"=> "person"
			);
		}
		if(getPrvlg('message_admin')){
			$allowed_recivers[] =array(
				"tag" => "a",
				"attr"=> 'onclick="addReciver(\'admin\')"',
				"text"=> $lang['admin'],
				"icon"=> "person"
			);
		}
		if(getPrvlg('message_parent')){
			$allowed_recivers[] =array(
				"tag" => "a",
				"attr"=> 'onclick="addReciver(\'parent\')"',
				"text"=> $lang['parents'],
				"icon"=> "person"
			);
		}
		
		return 	$allowed_recivers;
	}
	
	static function getMessageToolBox($folder){
		global $lang;
		$view_array = array('inbox'=>$lang['inbox'], 'sent'=>$lang['sent'], 'trash'=>$lang['trash']);
		return write_html('form', 'id="msg_nav" class="toolbox"',
			write_html('span', 'style="margin:0px 30px"',
				write_html('label', 'class="label ui-state-default ui-corner-left" style="height:14px"',
					$lang['goto'].': '
				).
				write_html_select('id="view_select" class="combobox" update="reloadView"',$view_array, $folder)
			).			
			(count(Messages::getAllowedRecivers()) > 0 ?
				write_html('a', 'class="ui-state-default hoverable" action="loadCompose"',
					write_html('span', 'class="ui-icon ui-icon-document"','').
					$lang['new']
				)
			: '').
			write_html('a', 'class="ui-state-default hoverable" onclick="loadCompose(\'\', \'forward\')"',
				write_html('span', 'class="ui-icon ui-icon-arrowreturnthick-1-e"','').
				$lang['forward']
			).
			($folder == 'inbox' ?
				write_html('a', 'class="ui-state-default hoverable" onclick="loadCompose(\'reply\')"',
					write_html('span', 'class="ui-icon ui-icon-arrowreturnthick-1-w"','').
					$lang['reply']
				)
			:'').
			write_html('a', 'class="ui-state-default hoverable" action="print_pre" rel="#msg_content" plugin="print"',
				write_html('span', 'class="ui-icon ui-icon-print"','').
				$lang['print']
			).
			($folder =='trash' ? 
				write_html('a', 'class="ui-state-default ui-corner-right hoverable" onclick="restoreMsg()"',
					write_html('span', 'class="ui-icon ui-icon-refresh"','').
					$lang['restore']
				) :
				write_html('a', 'class="ui-state-default ui-corner-right hoverable" onclick="deleteMsg()"',
					write_html('span', 'class="ui-icon ui-icon-trash"','').
					$lang['delete']
				) 
			)
		);
	}
	
	static function loadMainLayout(){
		global $lang;
		return write_html('div', 'class="tabs" style="height:auto"',
			write_html('ul', '',
				write_html('li', '', 
					write_html('a', 'href="#messages_tab"',
						$lang['messages'].
						' ('.write_html('span', 'id="message_messages_counter" title="'.$lang['unread'].'"',count( Messages::getList(0, false, false, 'inbox'))).')'
					)
				).
				write_html('li', '', 
					write_html('a', 'href="index.php?module=messages&type=system"',
						$lang['system'].
						' ('.write_html('span', 'id="message_system_counter" title="'.$lang['unread'].'"', count( Messages::getList(0, false, false, 'system'))).')'
					)
				)
			).
			write_html('div', 'id="messages_tab"',
				  Messages::getMessageTypeLayout()
			)
		);
		
	}
	
	static function getMessageTypeLayout($page=1, $folder="inbox"){ // folders : alerts, messages, new...
		global $lang;
		$messages_toolbox = '';
		$content = '';
		if(in_array($folder, array("inbox", "sent", "trash"))){
			$messages_toolbox = Messages::getMessageToolBox($folder);
		} 
		$start = ($page-1) * 10;
		$last_message_arr = Messages::getList($start, 1, true, $folder);
		if($last_message_arr != false && count($last_message_arr)){
			$last_message = $last_message_arr[0];
			$content = $last_message->read();
		} else {
			$content = write_html('div', 'class="ui-corner-all ui-state-error" style="padding:10px"', $lang['no_msg']);
		}
		$layout = write_html('table', 'width="100%" cellspacing="0" cellpadding="0"', 
			write_html('tr','',
				write_html('td', 'width="30%" valign="top"', 
					write_html('div', 'class="ui-corner-bottom ui-widget-content"',
						write_html('div', 'class="msg_list-messages"', 
							Messages::getlayoutList($page, $folder)
						)
					)
				).
				write_html('td', ' valign="top" style="padding:4px"', 
					$messages_toolbox.
					write_html('div', 'id="msg_content" style="padding:5px"',
						$content
					)
				)
			)
		);
		return $layout;
	}
	
	static function getlayoutList($cur_page=1, $folder="inbox"){
		global $lang;
		$max = 10;
		$start = ($cur_page-1) * $max;
		$total = count(Messages::getList(0, false, true, $folder));
		$total_pages = ceil ($total / $max );
		$list_table = Messages::getListTable($start, $max, true, $folder);
		$msg_navbar = write_html('ul', 'class="msg_list_nav ui-widget-header ui-corner-all" align="center"',
			($cur_page > 1 ?
				write_html('li', '',
					write_html('a', 'class="ui-state-default hoverable  ui-corner-left" onclick="loadMsgList(this, 1, \''.$folder.'\')"', write_icon('seek-first'))
				).
				write_html('li', '',
					write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList(this, '. ($cur_page-1) .', \''.$folder.'\')"', write_icon('seek-prev'))
				)
			: '').
			write_html('li', '',
				write_html('a', 'class="ui-state-default"', $lang['page'].': '.$cur_page.'/'.$total_pages)
			).
			
			($cur_page < $total_pages ?
				write_html('li', '',
					write_html('a', 'class="ui-state-default hoverable" onclick="loadMsgList(this, '. ($cur_page+1) .', \''.$folder.'\')"', write_icon('seek-next'))
				).
				write_html('li', '',
					write_html('a', 'class="ui-state-default hoverable ui-corner-right" onclick="loadMsgList(this, '.$total_pages.', \''.$folder.'\')"', write_icon('seek-end'))
				)
			: '')
		);
		return $msg_navbar.$list_table;
	}
		
}
?>