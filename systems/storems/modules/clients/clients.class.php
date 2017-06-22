<?php
/** Clients
*
*
*/


class Clients{
	public function __construct($id){
		if($id != ''){	
			$client = do_query_obj("SELECT * FROM clients WHERE id=$id");	
			if(isset( $client->id )){
			
				foreach($client as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else {return false;}
			
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function _toDetails(){
		$item = $this;
		$groups = Clients::getClientsGroup();
		$item->balance = $this->getBalance();
		$item->groups_options = write_select_options($groups, $this->group_id);
		
		$item->details_tab = fillTemplate("modules/clients/templates/clients_details.tpl", $item);
		
		$item->transactions_trs = $this->getTransactions();
		return filltemplate('modules/clients/templates/clients.tpl', $item);
	}
	
	static public function getClientsGroup(){
		$groups = do_query_array("SELECT * FROM clients_group ");	
		foreach($groups as $group ){
			$out[$group->id] = $group->name;
		}
		return $out;
	}

	static public function loadMainLayout(){
		$layout = new stdClass();
		$ccs = CostcentersGroup::getList();
		$count=0;
		$layout->groups_list ='';
		foreach($ccs as $group_id=>$group_name){
			if($count == 0 ){
				$first_group_id = $group_id;
			}
			$groups_list[] = write_html('li', 'class="hoverable clickable ui-stat-default ui-corner-all ui-state-default '.($count==0 ? 'ui-state-active' : '').'" action="openGroup" groupid="'.$group_id.'"', $group_name );
			$count++;
		}
		
		$layout->groups_list .= write_html('div', '', 
			write_html('ul', 'class="list_menu listMenuUl sortable" id="groups_list"', 
				implode('', $groups_list)
			)
		);	
		
		$clients = Clients::getList($first_group_id);
		$layout->clients_trs = '';
		foreach($clients as $client){
			$layout->clients_trs .= write_html('tr', '',
				write_html('td', '',
					write_html('button', 'class="ui-corner-all ui-state-default hoverable circle_button" action="openClient" clientid="'.$client->id.'"', write_icon('person'))
				).
				write_html('td', '',
					write_html('text', 'class="label-client-'.$client->id.'"', $client->getName())
				).
				write_html('td', '',
					$client->getBalance()
				)
			);
		}
		$layout->clients_list = fillTemplate("modules/clients/templates/clients_list.tpl", $layout);
		
		return fillTemplate("modules/clients/templates/clients_main_layout.tpl", $layout);
	}
	
	public function getBalance(){
		$payments = do_query_obj("SELECT SUM(amount) AS total FROM payments WHERE `to`='c' AND to_id=$this->id");
		
		$commands = do_query_obj("SELECT SUM(total) AS total FROM commands WHERE client_id=$this->id AND status=2");
		
		return $payments->total - $commands->total;
	}
	
	public function getTransactions(){
		global $lang;
		$payments = do_query_array("SELECT * FROM payments WHERE client_id=$this->id ORDER By date DESC");
		$commands = do_query_array("SELECT * FROM commands WHERE client_id=$this->id AND status=2 ORDER By delivery_date DESC");
		$array = array();
		foreach($payments as $payment){
			$array[$payment->date] = $payment;
		}
		foreach($commands as $command){
			$array[$command->delivery_date] = $command;
		}
		
		ksort($array);
		$trs = array();
		foreach($array as $date=>$trans){
			if(isset($trans->status)){
				$trs[] = write_html('tr', '',
					write_html('td', '', 
						write_html('button', 'class="ui-state-default ui-corner-all hoverable circle_button" module="orders" action="openOrder" orderid="'.$trans->id.'"', write_icon('extlink'))
					).
					write_html('td', '', '&nbsp;').
					write_html('td', '', $trans->tot).
					write_html('td', '', $lang['order_no'].': '.$trans->id)
				);
			} else {
				$trs[] = write_html('tr', '',
					write_html('td', '', '&nbsp;').
					write_html('td', '', '&nbsp;').
					write_html('td', '', $trans->amount).
					write_html('td', '', $trans->type)
				);
			}
		}
		
		return implode('', $trs);
	}
		
	static public function getList($group_id){
		$clients = do_query_array("SELECT * FROM clients WHERE group_id=$group_id");	
		foreach($clients as $client ){
			$out[] = new Clients($client->id);
		}
		return sortArrayOfObjects($out, getItemOrder('clients'), 'id');
	}
	
	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'clients') != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'clients');
			$id = $result;
		}

		if($result!=false){
			$answer['id'] = $id;
			$answer['title'] = $post['name'];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}

	static public function getAutocomplete($value){
		$sql = "SELECT id, name FROM clients WHERE 
			(
				name ='$value' 
				OR name LIKE '$value%'
				OR name LIKE '".strtolower($value)."%'
				OR name LIKE '".ucfirst($value)."%'
				OR name LIKE '".ucwords($value)."%'
				OR name LIKE '".strtoupper($value)."%'
			)
			LIMIT 12";
		$clients = do_query_array( $sql);
		return json_encode($clients);
	}
	
	static function getGroupByCC($ccid){
		return do_query_array("SELECT * FROM clients_group WHERE ccid=$ccid");	
	}
}
?>