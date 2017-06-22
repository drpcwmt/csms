<?php
/** Prodcuts
*
*
*/

class Products{
	private $thisTemplatePath = 'modules/products/templates';
	
	
	public function __construct($id){
		if($id != ''){	
			$prod = do_query_obj("SELECT * FROM products WHERE id=$id", MySql_Database);	
			if(isset( $prod->id )){
				foreach($prod as $key =>$value){
					$this->$key = $value;
				}
				$this->buy_price = $this->getBuyPrice();
				if($this->ser == '0'){
					$count = count(do_query_array("SELECT ser FROM products WHERE ser!=0 AND sub_id=$this->sub_id"));
					do_update_obj(array('ser'=>($count+1)), "id=$id", 'products');
				}
				return $this;
			} else {
				return false;
			}	
		} else {return false;}
			
	}
	
	public function getName($other_lang = false){
		if(isset($this->name_rtl)){
			if($other_lang == false){
				return $_SESSION['lang'] == 'ar' ? $this->name_rtl : $this->name_ltr ;
			} else {
				return $_SESSION['lang'] == 'ar' ? $this->name_ltr : $this->name_rtl ;
			}
		} else {
			return false;
		}
	}
	
	public function getAccCode(){
		return Products::fillZero('main', $this->sub_id).Products::fillZero('sub', $this->id);
	}
	
	public function getCode(){
		$sub  = new SubCategorys($this->sub_id);
		return Products::fillZero('main', $sub->getCode()).Products::fillZero('sub', $this->ser);
	}
	
	public function _toIcon(){
		$item = $this;
		$item->name = $this->getName();
		$item->icon_path = file_exists('attachs/product/'.$this->id.'/thumb.png') ? 'attachs/product/'.$this->id.'/thumb.png' : 'assets/img/product.png';
		
		return filltemplate('modules/products/templates/product_icon.tpl', $item);
	}
	
	public function _toList($ser=''){
		$item = $this;
		$this->code = $this->getCode();
		$item->name = $this->getName();
		$item->ser = $ser;
		$item->totalStock = $this->getStocks();
		$item->icon_path = file_exists('attachs/product/'.$this->id.'/thumb.png') ? 'attachs/product/'.$this->id.'/thumb.png' : 'assets/img/product.png';
		return filltemplate('modules/products/templates/product_list.tpl', $item);
	}
	
	public function loadLayout(){
		global $lang;
		/*$pricingMethods = array(
			'1'=>$lang['first_to_go'], 
			'2'=>$lang['last_to_go'], 
			'3'=>$lang['avrage'], 
			'4'=>$lang['last_price']
		);*/
		
		$units = array(
			'unit'=>$lang['unit'], 
			'kg'=>$lang['kg'], 
			'litre'=>$lang['litre']
		);
		$item = new Layout($this);
		// Details
		$item->name = $this->getName();
		$item->icon_path = file_exists('attachs/product/'.$this->id.'/thumb.png') ? 'attachs/product/'.$this->id.'/thumb.png' : 'assets/img/product.png';
		$sub = new SubCategorys($this->sub_id);
		$cat = new Categorys($sub->cat_id);
		$item->cat_id = $sub->cat_id;
		$item->cat_name = $cat->getName();
		$item->sub_id = $this->sub_id;
		$item->sub_name = $sub->getName();

		$item->code = $this->getCode();
		$item->barcode = $this->barcode != '' ? $this->barcode : $this->getCode();
		$item->units_opts = write_select_options($units, $this->unit);
		$item->details_tab = fillTemplate("modules/products/templates/products_details.tpl", $item);

		// Stocks
		$item->totalStock = $this->getStocks();
		$stocks_trs = array();
		$wars = Warhouses::getList();
		foreach($wars as $war){
			$stocks_trs[] = write_html('tr', '',
				write_html('td', '', '<img src="/assets/img/warehouse-512.png" width="24"').
				write_html('td', '', 
					write_html('text', 'class="label-war-'.$war->id.'"', $war->getName())
				).
				write_html('td', '', $war->getProductStock($this->id))
			);
		}
		/*$stores = Stores::getList();
		foreach($stores as $store){
			$stocks_trs[] = write_html('tr', '',
				write_html('td', '', '<img src="/assets/img/ponit_of_sale.png" width="24"').
				write_html('td', '', $store->getName()).
				write_html('td', '', $store->getProductStock($this->id))
			);
		}*/
		$item->stocks_trs = implode('', $stocks_trs);
		$item->stocks_tab = fillTemplate("modules/products/templates/product_stocks.tpl", $item);
		
		return filltemplate('modules/products/templates/product.tpl', $item);
	}
	
	
	public function getStocks($war_id = '', $store_id=''){
		$sql = "SELECT SUM(stock) AS stock FROM stocks WHERE prod_id=$this->id";
		if($war_id != ''){
			$sql .= " AND war_id=$war_id";
		} elseif($store_id != ''){
			$sql .= " AND store_id=$store_id";
		}
		$stock = do_query_obj($sql, MySql_Database);	
		if(isset($stock->stock) && $stock->stock > 0){
			return $stock->stock;
		} else {
			return 0;
		}
	
	}
	
	public function getBuyPrice($sup_id=false){
		$prod = do_query_obj("SELECT price FROM transactions WHERE `from`='s' ".($sup_id != false ? " AND from_id=$sup_id" : '')." ORDER BY delivery_date DESC LIMIT 1", MySql_Database);
		if($prod != false && isset($prod->price)){
			return $prod->price;
		} else {
			$prod =  do_query_obj("SELECT price FROM suppliers_products WHERE prod_id=$this->id ".($sup_id != false ? " AND sup_id=$sup_id" : '')." ORDER BY price ASC LIMIT 1", MySql_Database);
			if($prod != false && isset($prod->price)){
				return $prod->price;
			} else {
				return 0;
			}
		}

	}
	static public function loadMainLayout(){
		$layout = new stdClass();
		$cats = Categorys::getList();
		$layout->cats_list = Categorys::getTree(0, false, $cats);
		$first_cat = $cats[0];
		$layout->cat_id = $first_cat->parent_id;
		$layout->product_list = $first_cat->loadLayout();
		return fillTemplate("modules/products/templates/products_main_layout.tpl", $layout);
	}
	
	
	
	static public function newForm($sub_id){
		global $lang;
		$form = new Layout();
		$form->icon_path = 'assets/img/product.png';
		$sub = new SubCategorys($sub_id);
		$cat = new Categorys($sub->cat_id);
		$form->cat_id = $sub->cat_id;
		$form->cat_name = $cat->getName();
		$form->sub_id = $sub_id;
		$form->sub_name = $sub->getName();
		$form->vol = 1;
		$units = array(
			'unit'=>$lang['unit'], 
			'kg'=>$lang['kg'], 
			'litre'=>$lang['litre']
		);
		$form->units_opts = write_select_options($units, '' );
		return fillTemplate("modules/products/templates/products_details.tpl", $form);
	}
	
	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'products', MySql_Database) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id'])){
			$count = count(do_query_array("SELECT ser FROM products WHERE ser!=0 AND sub_id=".$post['sub_id']));
			$post['ser'] = $count+1;
			$result = do_insert_obj($post, 'products', MySql_Database);
			$id = $result;
		}

		if($result!=false){
			$answer['id'] = $id;
			$answer['title'] = $post['title'];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}

	static function _delete($id){
		if(do_query_edit("DELETE FROM products WHERE id=$id", MySql_Database)){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static public function getAutocomplete($value){
		$name = $_SESSION['lang'] == 'ar' ? 'name_rtl' : 'name_ltr' ;
		$sql = "SELECT id, $name AS name, sub_id FROM products WHERE 
			(
				name_rtl = '$value' 
				OR name_rtl LIKE '$value%'
				OR name_ltr ='$value' 
				OR name_ltr LIKE '$value%'
				OR name_ltr LIKE '".strtolower($value)."%'
				OR name_ltr LIKE '".ucfirst($value)."%'
				OR name_ltr LIKE '".ucwords($value)."%'
				OR name_ltr LIKE '".strtoupper($value)."%'
			)
			ORDER BY sub_id LIMIT 12";
		$prods = do_query_array( $sql, MySql_Database);
		foreach($prods as $prod){
			$cat = new Categorys($prod->cat_id);
			$prod->category = $cat->getName();
		}
		return json_encode($prods);
	}

	static function fillZero($type, $value){
		$count = strlen($value);
		for($i=0; $i<(5-$count); $i++){
			$value = $type=='main'? $value.'0' : '0'.$value;
		}
		return $value;
	}
	
}
?>