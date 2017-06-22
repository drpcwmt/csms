<?php
/** Sub Categorys
*
*
*/

require_once('products.class.php');


class ProductSubgroups{
	public function __construct($id){
		if($id != ''){	
			$sub = do_query_obj("SELECT * FROM products_subgroups WHERE id=$id", MySql_Database);	
			if(isset( $sub->id )){
			
				foreach($sub as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else {return false;}
			
	}
	
	public function getName($other_lang = false){
		if(isset($this->name_ltr)){
			if($other_lang == false){
				return $_SESSION['lang'] == 'ar' ? $this->name_rtl : $this->name_ltr ;
			} else {
				return $_SESSION['lang'] == 'ar' ? $this->name_ltr : $this->name_rtl ;
			}
		} else {
			return false;
		}
	}
	
	public function getProducts(){
		$sql = "SELECT * FROM products WHERE sub_id=".$this->id;
		$prods = do_query_array($sql, MySql_Database);	
		$out = array();
		foreach($prods as $prod ){
			$out[] = new Products($prod->id);
		}
		return sortArrayOfObjects($out, getItemOrder('sub-'.$this->id), 'id');
	}
	
	public function getProductHtml($view = 'icon'){
		$prods = $this->getProducts();
		$prodsItems = array();
		foreach($prods as $prod){
			if($view == 'icon'){
				$prodsItems[] = $prod->_toIcon();
			} else {
				$prodsItems[] = $prod->_toList();
			}
		}
		if($view == 'icon'){
			return write_html('div', 'class="ui-corner-all ui-widget-default" style="padding:10px;"',
				implode('', $prodsItems)
			);
		} elseif($view == 'list'){
			return write_html('table', 'class="tablesorter"',
				implode('', $prodsItems)
			);
		}
	}
	
	static function getNameById($id){
		$name = $_SESSION['lang'] == 'ar' ? 'name_rtl' : 'name_ltr' ;
		$sub = do_query_obj("SELECT $name AS name FROM products_subgroups WHERE id=$id", MySql_Database);
		return isset($sub->name) ? $sub->name : false;
	}
	
	public function getAutocompleteSub($value){
		$name = $_SESSION['lang'] == 'ar' ? 'name_rtl' : 'name_ltr' ;
		$sql = "SELECT id, $name AS name FROM products_subgroups WHERE 
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
			WHERE cat_id= $this->id
			LIMIT 12";
		$cats = do_query_array( $sql, MySql_Database);
		return json_encode($cats);
	}

}
?>