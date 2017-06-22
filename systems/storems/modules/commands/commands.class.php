<?php
/** Commands
*
*
* Order status
* 0 untrated
* 1 executed
* 2 delivered
* 3 reserved

* Exectucion 
* 0 unexcuted
* other id of the user who make the execute order

* Methods
* 0 default used by manuel user
* 1 web clients

*/

class Commands{
	public function __construct($id){
		if($id != ''){	
			$command = do_query_obj("SELECT * FROM commands WHERE id=$id", MySql_Database);	
			if(isset( $command->id )){
			
				foreach($command as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else {return false;}
			
	}
	
	public function getItems(){
		if(!isset($this->items)){
			$this->items = do_query_array("SELECT * FROM commands_items WHERE command_id=$this->id", MySql_Database);
		}
		return $this->items;
	}
	
	public function getCountItems(){
		return count($this->getItems());		
	}
	
	public function getStatus(){
		global $lang;
		switch($this->status){
			case '0':  
				return $lang['command_untrated'];
			break;
			case '1':  
				return $lang['command_executed'];
			break;
			case '2':  
				return $lang['command_delivered'];
			break;
			case '3':  
				return $lang['command_reserved'];
			break;
		}
	}
			
	public function _toDetails(){
		$item = $this;
		$client = new Clients($this->client_id);
		$item->client_id = $client->id;
		$item->client_name = $client->name;
		$item->date = unixToDate($item->date);;
		$item->disabled_prvlg = getPrvlg('edit_commands_prices') ? '' : 'disabled';
		$item->search_hidden = 'hidden';
		$item->but_pay_hidden = $this->status == '1' ? 'hidden' :'';
		$item->but_deliver_hidden = $this->status == '2' ? 'hidden' :'';
		$item->but_reset_hidden = in_array($this->status , array(1,2)) ? 'hidden' :'';
		$item->new_command_tr = in_array($this->status , array(1,2)) ? 'hidden' :'';
		
		$items= $this->getItems();
		$item->command_items_trs = '';
		foreach($items as $itm){
			$prod = new Products($itm->prod_id);
			$item->command_items_trs .= write_html('tr', '',
				write_html('td', 'style="padding:1px 2px" class="unprintable"',
					write_html('button', 'type="button" action="removeCommandItem" class="ui-state-default ui-corner-all hoverable circle_button"', write_icon('close'))
				).
				write_html('td', 'style="padding:0"',
                    '<input type="text" name="item_id[]" class="input_half no-corner" update="getItemData" value="'.$prod->id.'" />'
				).
                write_html('td', 'style="padding:0"',
					'<input type="text" name="name[]" class="item_name input_double no-corner" value="'.$prod->getName().'" />
					<input class="autocomplete_value" type="hidden" value="'.$prod->id.'" />'.
					write_html('button', 'type="button" module="products" action="openProduct" prodid="'.$prod->id.'" class="ui-state-default ui-corner-all hoverable circle_button unprintable"', write_icon('extlink'))
				).
                write_html('td', 'style="padding:0"',
                    '<input type="text" name="quantity[]" class="input_half no-corner" value="'.$itm->quantity.'" />'
				).
                write_html('td', 'style="padding:0"',
                    '<input type="text" name="price[]" class="input_half no-corner" value="'.$itm->price.'" />'
				).
                write_html('td', 'style="padding:0"',
                    '<input type="text" name="total[]" class="input_half no-corner" disabled  value="'.($itm->quantity * $itm->price).'" />'
				)
			);
		}
		unset($item->items);
		return fillTemplate("modules/commands/templates/commands.tpl", $item);

	}
				
	static function _save($post){
		$result = false;
		$post['user'] = $_SESSION['user_id'];
		$post['date'] = dateToUnix($post['date']);
		
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'commands', MySql_Database) != false){
				$result = true;
				$id = $post['id'];
				do_query_edit("DELETE FROM commands_items WHERE command_id=$id",MySql_Database); 
				for($i=0; $i<count($post['item_id']); $i++){
					if($post['item_id'][$i] != ''){
						$item = array(
							'command_id' =>$id,
							'prod_id' =>$post['item_id'][$i],
							'quantity'=>$post['quantity'][$i],
							'price'=>$post['price'][$i]
						);
						do_insert_obj($item, 'commands_items', MySql_Database);
					}
				}
			}
		} elseif(isset($post['id'])){
			if($result = do_insert_obj($post, 'commands', MySql_Database)){
				$id = $result;
				for($i=0; $i<count($post['item_id']); $i++){
					if($post['item_id'][$i] != ''){
						$item = array(
							'command_id' =>$id,
							'prod_id' =>$post['item_id'][$i],
							'quantity'=>$post['quantity'][$i],
							'price'=>$post['price'][$i]
						);
						do_insert_obj($item, 'commands_items', MySql_Database);
					}
				}
			}
		}

		if($result!=false){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static public function newForm(){
		$form = new stdClass();
		$form->hidden = 'hidden';
		$form->date = unixToDate(time());
		$form->total = 0;
		$form->id = 'new';
		$form->search_hidden = '';
		$form->but_deliver_hidden = '';
		$form->but_pay_hidden = '';
		$form->but_reset_hidden = '';
		$form->new_command_tr = '';
		
		$client = new Clients(1);
		$form->client_id = $client->id;
		$form->client_name = $client->name;
		$form->disabled_prvlg = getPrvlg('edit_commands_prices') ? '' : 'disabled';
		
		return fillTemplate("modules/commands/templates/commands.tpl", $form);
	}
	
	static function _searchForm(){
		return fillTemplate("modules/commands/templates/command_search.tpl", array());
	}
	
	static public function searchCommands($searchOpts){
		
		$sql = "SELECT id FROM commands ";
		$where = array();
		// client
		if(isset($searchOpts['client_id']) && $searchOpts['client_id'] != ''){
			$where[] = 'client_id='.$searchOpts['client_id'];
		}

		// Date
		if(isset($searchOpts['date']) && $searchOpts['date'] != ''){
			$where[] = 'date='.dateToUnix($searchOpts['date']);
		}

		// delivery Date
		if(isset($searchOpts['delivery_date']) && $searchOpts['delivery_date'] != ''){
			$where[] = 'delivery_date='.$searchOpts['delivery_date'];
		}
		
		// date interval
		if(isset($searchOpts['begin_date']) && $searchOpts['client_id'] != ''){
			$date_field = $searchOpts['date_opts'] == 'delivery' ? 'delivery_date' : 'date';
			$begin_date = dateToUnix($searchOpts['begin_date']);
			$end_date = isset($searchOpts['end_date']) && $searchOpts['end_date'] != '' ? dateToUnix($searchOpts['end_date']) : time();
			$where[] = "$date_field>=$begin_date AND $date_field<=$end_date";
		}
		
		if(count($where) > 0){
			$sql .= "WHERE ".implode(' AND ', $where)." ORDER BY date DESC ";
		}
		
		if(isset($searchOpts['q_limit']) && $searchOpts['q_limit']!= ''){
			if(isset($searchOpts['q_page']) && $searchOpts['q_page']!= ''){
				$limit_begin = $searchOpts['q_limit'] * ($searchOpts['q_page']-1);
				$limit_end = $searchOpts['q_limit'] * $searchOpts['q_page'];
			} else {
				$limit_begin = 0;
				$limit_end = $searchOpts['q_limit'] ;
			}
			$sql .= "LIMIT $limit_begin, $limit_end ";
		}
				
		$out = array();
		$commands = do_query_array($sql, MySql_Database);
		foreach($commands as $com){
			$out[] = new Commands($com->id);
		}
		return $out;
	}
	
	static function _toList($commands){
		global $lang;
		$trs = array();
		foreach($commands as $command){
			$trs[] = write_html('tr', '',
				write_html('td', '', 
					write_html('button', 'type="button" action="openSearchCommand" comid="'.$command->id.'" class="ui-corner-all ui-state-default hoverable circle_button"', write_icon('extlink'))
				).
				write_html('td', '', $command->id).
				write_html('td', '', unixToDate($command->date)).
				write_html('td', '', unixToDate($command->delivery_date)).
				write_html('td', '', $command->getStatus()).
				write_html('td', '', $command->getCountItems()).
				write_html('td', '', $command->total)
			);
		}
		
		$out = write_html('table', ' class="tablesorter"', 
			write_html('thead', '',
				write_html('tr', '', 
					write_html('th', 'style="background-image:none" class="unprintable" width="24"', '&nbsp;').
					write_html('th', '', $lang['command_no']).
					write_html('th', '', $lang['date']).
					write_html('th', '', $lang['delivery_date']).
					write_html('th', '', $lang['status']).
					write_html('th', '', $lang['count']).
					write_html('th', '', $lang['total'])
				)
			).
			write_html('tbody', '',
				implode('', $trs)
			)
		);
		return $out;
	}
	
	static function savePayment($post){
		$post['date'] = dateToUnix($post['date']);
		if(isset($post['status'])){
			$command = array('status'=> $post['status']);
			do_update_obj($command, 'id='.$post['command_id'], 'commands', MySql_Database);
		}
		$result = do_insert_obj($post, 'payments', MySql_Database);
		$answer = array();
		if($result!==false){
			$answer['id'] = $result;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
}
?>