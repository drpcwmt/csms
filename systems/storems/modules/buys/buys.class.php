<?php
/** Buys
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

require_once('modules/suppliers/suppliers.class.php');
require_once('modules/products/products.class.php');
require_once('modules/transactions/transactions.class.php');

class Buys{
	public function __construct($id){
		if($id != ''){	
			$buy = do_query_obj("SELECT * FROM buys WHERE id=$id", MySql_Database);	
			if(isset( $buy->id )){
			
				foreach($buy as $key =>$value){
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
			$this->items = do_query_array("SELECT * FROM transactions WHERE trans_id=$this->id AND `from`='s' AND from_id=$this->sup_id", MySql_Database);
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
				return $lang['command_shipping'];
			break;
			case '3':  
				return $lang['command_delivered'];
			break;
			case '4':  
				return $lang['command_reserved'];
			break;
		}
	}
	
	public function getPaid(){
		$paid = do_query_obj("SELECT SUM(amount) AS total FROM payments WHERE trans_id=$this->id AND `to`='s' AND to_id=$this->sup_id", MySql_Database);
		return isset($paid->total) ? $paid->total : 0;
	}
			
	public function _toDetails(){
		global $lang, $MS_settings;
		$buy_opt = $this;
		$supplier = new Suppliers($this->sup_id);
		

		$buy_opt->transaction_type = 'buy';

		$buy_opt->title = $lang['buy_order'];
		$buy_opt->from_title = $lang['supplier'];
		$buy_opt->from = 's';
		$buy_opt->from_id = $supplier->id;
		$buy_opt->from_name = '<input type="text" class="input_double required" name="from_name" value="'.$supplier->getName().'" />'; 

		// get the destionation from transactions table
		$trans = do_query_obj("SELECT `to`, `to_id` FROM transactions WHERE trans_id=$this->id AND `from`='s' AND from_id=$supplier->id LIMIT 1", MySql_Database);
		$buy_opt->to_title = $lang['to'];
		$buy_opt->to = isset($trans->to) ? $trans->to : '';
		$buy_opt->to_id = isset($trans->to_id) ? $trans->to_id : '';
		$buy_opt->to_name = write_html('select', 'name="to_select" class="combobox required" update="updateTransactionTo"', 
			(isset($trans->to) ? '' : write_html('option', '', '')).
			getPointOfSales((isset($trans->to) ? $trans->to : ''), (isset($trans->to_id) ? $trans->to_id : ''))
		);
		//$buy_opt->to_hidden = ($trans->to != false ? 'hidden' : '');
		
		$buy_opt->status = $this->getStatus();
		$buy_opt->paid = $this->getPaid();
		$buy_opt->new_command_tr = ($this->status > 0 ? 'hidden' : '');
		$buy_opt->items_trs = Transactions::itemsToList($this->getItems());
		$buy_opt->shipping_check = $this->shipping == 1 ? 'checked="checked"' : '';

		unset($buy_opt->items);
				
		return Transactions::newForm($buy_opt);
	
	}
	
	static function newBuy($sup_id=false, $to=false, $to_id=false){
		global $lang, $MS_settings;
		$buy_opt =  new stdClass();
		$buy_opt->transaction_type = 'buy';
		$buy_opt->id = 'new';
		$buy_opt->total = '0';
		
		$buy_opt->title = $lang['buy_order'];
		$buy_opt->from_title = $lang['supplier'];
		$buy_opt->from = 's';
		$buy_opt->from_id = $sup_id;
		if($sup_id!=''){
			$supplier = new Suppliers($sup_id);
			$supplier_name = $supplier->getName();
		} else {
			$supplier_name ='';
		}
		$buy_opt->from_name = '<input type="text" class="input_double required" name="from_name" value="'.$supplier_name.'" />'; 

		$buy_opt->to_title = $lang['to'];
		$buy_opt->to = $to;
		$buy_opt->to_id = $to_id;
		$buy_opt->to_name = write_html('select', 'name="to_select" class="combobox required" update="updateTransactionTo"', 
			write_html('option', '', '').
			getPointOfSales($to, $to_id)
		);
		$buy_opt->to_hidden = ($to != false ? 'hidden' : '');
		
		$buy_opt->issue_date = unixToDate(time());
		$buy_opt->delivery_date = unixToDate(time() + ($MS_settings['delivery_after'] * (60*60*24))); 
		$buy_opt->status = $lang['command_untrated'];
		$buy_opt->new_command_tr = '';
		
		$buy_opt->shipping_check = $MS_settings['shipping_from_supplier'] == 1 ? 'checked="checked"' : '';
				
		return Transactions::newForm($buy_opt);
	}
	
			
	static function _save($post){
		$result = false;
		$post['user'] = $_SESSION['user_id'];
		$post['sup_id'] = $post['from_id'];
		$post['shipping'] = isset($post['shipping']) ? $post['shipping'] : 0;
		if(isset($post['id']) && $post['id'] != 'new'){
			if( do_update_obj($post, 'id='.$post['id'], 'buys', MySql_Database) != false){
				$result = true;
				$id = $post['id'];
				do_query_edit("DELETE FROM transactions WHERE trans_id=$id",MySql_Database); 
				for($i=0; $i<count($post['item_id']); $i++){
					if($post['item_id'][$i] != ''){
						$transation = array(
							'trans_id' =>$id,
							'prod_id' =>$post['item_id'][$i],
							'quantity'=>$post['quantity'][$i],
							'price'=>$post['price'][$i],
							'from'=>$post['from'],
							'from_id'=>$post['from_id'],
							'to'=>$post['to'],
							'to_id'=>$post['to_id']
						);
						do_insert_obj($transation, 'transactions', MySql_Database);
					}
				}
			}
		} elseif(isset($post['id'])){
			if($result = do_insert_obj($post, 'buys', MySql_Database)){
				$id = $result;
				for($i=0; $i<count($post['item_id']); $i++){
					if($post['item_id'][$i] != ''){
						$transation = array(
							'trans_id' =>$id,
							'prod_id' =>$post['item_id'][$i],
							'quantity'=>$post['quantity'][$i],
							'price'=>$post['price'][$i],
							'from'=>$post['from'],
							'from_id'=>$post['from_id'],
							'to'=>$post['to'],
							'to_id'=>$post['to_id']
						);
						do_insert_obj($transation, 'transactions', MySql_Database);
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
		
	static function _searchForm(){
		return fillTemplate("modules/buys/templates/buys_search.tpl", array());
	}
	
	static public function searchBuys($searchOpts){
		
		$sql = "SELECT id FROM buys ";
		$where = array();
		// client
		if(isset($searchOpts['sup_id']) && $searchOpts['sup_id'] != ''){
			$where[] = 'sup_id='.$searchOpts['sup_id'];
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
		if(isset($searchOpts['begin_date']) && $searchOpts['sup_id'] != ''){
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
	
	static function _toList($buys){
		global $lang;
		$trs = array();
		foreach($buys as $buy){
			$trs[] = write_html('tr', '',
				write_html('td', '', 
					write_html('button', 'type="button" action="openSearchBuys" comid="'.$command->id.'" class="ui-corner-all ui-state-default hoverable circle_button"', write_icon('extlink'))
				).
				write_html('td', '', $buy->id).
				write_html('td', '', unixToDate($buy->date)).
				write_html('td', '', unixToDate($buy->delivery_date)).
				write_html('td', '', $buy->getStatus()).
				write_html('td', '', $buy->getCountItems()).
				write_html('td', '', $buy->total)
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
}
?>