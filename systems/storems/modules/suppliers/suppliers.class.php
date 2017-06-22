<?php
/** Suppliers
*
*
*/

require_once('modules/products/products.class.php');
require_once('modules/buys/buys.class.php');
require_once('modules/transactions/transactions.class.php');

class Suppliers{
	public function __construct($id){
		if($id != ''){	
			$supplier = do_query_obj("SELECT * FROM suppliers WHERE id=$id", MySql_Database);	
			if(isset( $supplier->id )){
			
				foreach($supplier as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else {return false;}
			
	}
	
	static public function loadMainLayout(){
		$layout = new stdClass();
		$suppliers = Suppliers::getList();
		$count=0;
		$layout->suppliers_list = '';

		foreach($suppliers as $supplier){
			$layout->suppliers_list .= write_html('tr', '',
				write_html('td', '',
					write_html('button', 'class="ui-corner-all ui-state-default hoverable circle_button" action="openSupplier" supplierid="'.$supplier->id.'"', write_icon('person'))
				).
				write_html('td', '',
					write_html('text', 'class="label-supplier-'.$supplier->id.'"', $supplier->getName())
				).
				write_html('td', '',
					$supplier->getBalance()
				)
			);
		}
				
		
		return fillTemplate("modules/suppliers/templates/suppliers_main_layout.tpl", $layout);
	}

	public function getName(){
		return $this->name;
	}
	
	public function _toDetails(){
		$item = $this;
		$item->balance = $this->getBalance();
		$item->hidden_balance = 'nothidden';
		
		$products = $this->getProducts();
		$item->products_trs = '';
		foreach($products as $prod){
			$item->products_trs .= write_html('tr', '',
				write_html('td', 'style="padding:1px 2px" class="unprintable"',
					write_html('button', 'type="button" action="removeSupplierItem" prodid="'.$prod->id.'" class="ui-state-default ui-corner-all hoverable circle_button unprintable"', write_icon('close'))
				).
				write_html('td', 'style="padding:1px 2px" class="unprintable"',
					write_html('button', 'type="button" module="products" action="openProduct" prodid="'.$prod->id.'" class="ui-state-default ui-corner-all hoverable circle_button unprintable"', write_icon('extlink'))
				).
				write_html('td', '',
                    '<input type="hidden" name="item_id" value="'.$prod->id.'" />'.
					$prod->id
				).
                write_html('td', '',
					$prod->getName()
				).
                write_html('td', 'style="padding:0"',
                    '<input type="text" name="price" class="input_half no-corner" value="'.$prod->buy_price.'" />'
				).
                write_html('td', 'style="padding:0"',
                    '<input type="text" name="barcode" class="input_half no-corner" value="'.$prod->buy_barcode.'" />'
				).
                write_html('td', '',
					$prod->buy_quantity
				)
			);
		}

		$item->details_tab = fillTemplate("modules/suppliers/templates/suppliers_details.tpl", $item);
		
		$item->transactions_trs = Transactions::_toList($this->getTransactions());
		return filltemplate('modules/suppliers/templates/suppliers.tpl', $item);
	}
	
	public function getBalance(){
		// Payments	
		$paid = do_query_obj("SELECT SUM(amount) AS total FROM payments WHERE `to`='s' AND to_id=$this->id", MySql_Database);
		$recived = do_query_obj("SELECT SUM(amount) AS total FROM payments WHERE `from`='s' AND from_id=$this->id", MySql_Database);
		// all cuy command delivred at homes or at suppliers
		$buys =do_query_obj("SELECT SUM(total) AS total FROM buys WHERE sup_id=$this->id AND (status=2 OR (status=1 AND shipping=1))", MySql_Database);
		$refunds = do_query_obj("SELECT SUM(total) AS total FROM buys_refunds WHERE sup_id=$this->id AND (status=2 OR (status=1 AND shipping=1))", MySql_Database);
		
		return ($paid->total + $refunds->total) - ($recived->total + $buys->total);
	}
	
	public function getTransactions(){
		global $lang;
		$transactions = array();
		if($buys = do_query_array("SELECT id FROM buys WHERE sup_id=$this->id ORDER BY issue_date DESC", MySql_Database)){
			foreach($buys as $buy){
				$transactions[] = new Buys($buy->id);
			}
			//$transactions = array_merge($transactions, $buys);
		}
		if($refunds = do_query_array("SELECT * FROM buys_refunds WHERE sup_id=$this->id ORDER BY issue_date DESC", MySql_Database)){
			foreach($refunds as $refund){
				//$transactions[] = new BuyRefunds($refund->id);
			}
			//$transactions = array_merge($transactions, $refunds);
		}
		
		if(count($transactions) > 0){
			usort($transactions, function($a, $b){
				return strcmp($a->issue_date, $b->issue_date);
			});
		}
		
		return $transactions;
	}
	
	public function getPayments(){
		return do_query_array("SELECT * FROM payments WHERE (`to`='s' AND to_id=$this->id) OR (`from`='s' AND from_id=$this->id), ORDER BY issue_date DESC", MySql_Database);
	}
	
	public function getProducts(){
		$out = array();
		$prods = do_query_array("SELECT prod_id, barcode FROM suppliers_products WHERE sup_id=$this->id", MySql_Database);
		if($prods !=false && count($prods) >0){
			foreach($prods as $prod){
				$product = new Products($prod->prod_id);
				$quantity = do_query_obj("SELECT SUM(quantity) as quantity FROM transactions WHERE prod_id=$prod->prod_id AND `to`='s' AND to_id=$this->id", MySql_Database);
				
				
				$product->buy_quantity = isset($quantity->quantity) ? $quantity->quantity : 0;
				$product->buy_price = $product->getBuyPrice($this->id);
				$product->buy_barcode = isset($prod->barcode) ? $prod->barcode : '';
				$out[] = $product;
			}
		}
		return $out;
	}	
	
	static function getList(){
		$out = array();
		$suppliers = do_query_array("SELECT * FROM suppliers", MySql_Database);	
		foreach($suppliers as $supplier ){
			$out[] = new Suppliers($supplier->id);
		}
		return sortArrayOfObjects($out, getItemOrder('suppliers'), 'id');
	}
	
	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'suppliers', MySql_Database) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'suppliers', MySql_Database);
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

	static function saveProducts($post){
		$result = false;
		if(isset($post['prod_id']) && $post['prod_id'] != ''){
			$prod_id = $post['prod_id'];
			$sup_id = $post['sup_id'];
			if(!do_query_array("SELECT price FROM suppliers_products WHERE prod_id=$prod_id AND sup_id=$sup_id", MySql_Database)){
				if(do_insert_obj($post, 'suppliers_products', MySql_Database) != false){
					$id = $prod_id;
					$result = true;
				}
			} else {
				if( do_update_obj($post, "prod_id=$prod_id AND sup_id=$sup_id", 'suppliers_products', MySql_Database) != false){
					$result = true;
					$id = $prod_id;
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
	
	static function deleteProducts($post){
		$prod_id = $post['prod_id'];
		$sup_id = $post['sup_id'];
		if(do_query_edit("DELETE FROM suppliers_products WHERE prod_id=$prod_id AND sup_id=$sup_id", MySql_Database)){
			$answer['id'] = $prod_id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function newForm(){
		$form = new stdClass();
		$form->hidden_balance = 'hidden';
		return fillTemplate("modules/suppliers/templates/suppliers_details.tpl", $form);
	}

	static function getAutocomplete($value){
		$sql = "SELECT id, name FROM suppliers WHERE 
			(
				name ='$value' 
				OR name LIKE '$value%'
				OR name LIKE '".strtolower($value)."%'
				OR name LIKE '".ucfirst($value)."%'
				OR name LIKE '".ucwords($value)."%'
				OR name LIKE '".strtoupper($value)."%'
			)
			LIMIT 12";
		$suppliers = do_query_array( $sql, MySql_Database);
		return json_encode($suppliers);
	}
	
}
?>