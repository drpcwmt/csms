<?php
/** Sub Category
*
*
*/

class SubCategorys{
	
	public function __construct($sub_id){
		if($sub_id != ''){	
			$sub = do_query_obj("SELECT * FROM products_subcat WHERE id=$sub_id");	
			if(isset( $sub->id )){
				foreach($sub as $key =>$value){
					$this->$key = $value;
				}
				if($this->code == '0'){
					$s = 
					$count = count(do_query_array("SELECT code FROM products_subcat WHERE code!=0 AND cat_id=$this->cat_id AND ".($this->sub_id != '' ? "sub_id='$this->sub_id'" : "sub_id IS NULL")));
					do_update_obj(array('code'=>($count+1)), "id=$sub_id", 'products_subcat');
				}
			}	
		} 			
	}
	
	public function getName($other_lang = false){
		return $this->title;
	}
	
	public function getAccCode(){
		return  Products::fillZero('main', '14'.$this->cat_id) . '00000';
	}
	
	public function getCode(){
		return $this->cat_id.$this->code;
	}
	
	public function loadLayout($view ='list'){
		global $prvlg;
		$layout = new Layout($this);
		$layout->template = "modules/categorys/templates/layout.tpl";
		$layout->cat_name = $this->getName();
		$layout->full_code = $this->getCode();
		$layout->items = $this->getProductTable($view);
		if($prvlg->_chk('category_edit') ==false){
			$layout->edit_hidden = 'hidden';
		}
		return $layout->_print();
		
	}
	public function getProducts(){
		$prods = do_query_array("SELECT id FROM products WHERE sub_id='$this->id%'", MySql_Database);	
		$out = array();
		foreach($prods as $prod){
			$out[] = new Products($prod->id);
		}
		return $out;
	}
	
	public function getProductTable($view='list'){
		$layout = new layout();
		$prods = $this->getProducts();
		$trs = array();
		$ser = 1;
		foreach($prods as $prod){
			if($view == 'list'){
				$trs[] = $prod->_toList($ser);
			} else {
				$trs[] = $prod->_toIcon();
			}
			$ser++;
		}
		if($view == 'list'){
			$layout->template = "modules/categorys/templates/list_table.tpl";
		} else {
			$layout->template = "modules/categorys/templates/list_icon.tpl";
		}
		$layout->trs = implode('', $trs);
		return $layout->_print();
	}
	

	public function getSubCat(){
		$out = array();
		$sql = "SELECT * FROM products_subcat WHERE sub_id='$this->id%'";
		$subs = do_query_array($sql);
		foreach($subs as $sub){
			$out[] = new SubCategorys($sub->id);
		}
		return $out;
	}
	public function getSubTree(){
		global $lang, $prvlg;
		$out = array();
		// as sub cat as cat
		$subs = $this->getSubCat();
		if(count($subs) > 1){
			foreach($subs as $sub){
				$out[] = write_html('h3', '', 
					write_html('a', 'action="openSubCat" sub_id="'.$sub->id.'"', 
						$sub->title.
						($prvlg->_chk('sub_category_add')?
							write_html('button', 'class="mini_circle_button ui-state-default hoverable mini_add_btn" module="categorys" action="newSubCat" cat_id="'.$this->cat_id.'" sub_id="'.$sub->id.'" title="'.$lang['new'].'"', write_html('b', '','+'))
						: '')
					)
				).
				write_html('div', '', $sub->getSubTree());
			}
		}
		return count($out) > 0 ?
			write_html('div', 'class="accordion"', 
				implode('', $out)
			)
		: '';
	}
	static function _save($post){
		$result = false;
		if(!isset($post['cat_id']) &&( $post['id'] != 'new' && $post['id'] !='')){
			if( do_update_obj($post, 'id='.$post['old_id'], 'products_subcat') != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['cat_id'])){
			$result = do_insert_obj($post, 'products_subcat');
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

	static public function getAutocompleteCats($value){
		$sql = "SELECT id, title AS name FROM products_subcat WHERE 
			(
				title = '$value' 
				OR title LIKE '$value%'
				OR title ='$value' 
				OR title LIKE '$value%'
				OR title LIKE '".strtolower($value)."%'
				OR title LIKE '".ucfirst($value)."%'
				OR title LIKE '".ucwords($value)."%'
				OR title LIKE '".strtoupper($value)."%'
			)
			LIMIT 12";
		$cats = do_query_array( $sql, MySql_Database);
		return json_encode($cats);
	}
	
	static function newSubCat($cat_id, $sub_id=''){
		$layout = new Layout();
		$layout->template = 'modules/categorys/templates/sub_cat.tpl';
		$layout->sub_id = $sub_id;
		$layout->cat_id = $cat_id;
		$layout->id = 'new';
		return $layout->_print();
	}
}