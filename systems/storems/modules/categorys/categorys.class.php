<?php
/** Categorys
*
*
*/

class Categorys{
	public function __construct($catCode){
		if($catCode != ''){	
			$cat = do_query_obj("SELECT * FROM products_category WHERE id=$catCode");	
			if(isset( $cat->id )){
				foreach($cat as $key =>$value){
					$this->$key = $value;
				}
			}	
		} 			
	}
	
	public function getName($other_lang = false){
		return $this->title;
	}
	
	public function getAccCode(){
		return  Products::fillZero('main', '14'.$this->id) . '00000';
	}
	
	public function loadLayout($view ='list'){
		global $prvlg;
		$layout = new Layout($this);
		$layout->template = "modules/categorys/templates/layout.tpl";
		$layout->cat_name = $this->getName();
		//$layout->items = $this->getProductTable($view);
		if($prvlg->_chk('category_edit') ==false){
			$layout->edit_hidden = 'hidden';
		}
		return $layout->_print();
		
	}
	
	public function getProducts(){
		$prods = do_query_array("SELECT id FROM products WHERE cat_id='$this->id%'", MySql_Database);	
		$out = array();
		foreach($prods as $prod){
			$out[] = new Products($prod->id);
		}
		return $out;
	}
	
	
	static function getList(){
		$out = array();
		$cats = do_query_array("SELECT id FROM products_category");
		foreach($cats as $cat){
			$out[] = new Categorys($cat->id);	
		}
		return $out;
	}
	
	public function getSubCat(){
		$out = array();
		$sql = "SELECT * FROM products_subcat WHERE cat_id='$this->id%' AND (sub_id IS NULL OR sub_id=0)";
		$subs = do_query_array($sql);
		foreach($subs as $sub){
			$out[] = new SubCategorys($sub->id);
		}
		return $out;
	}

	
	public function getSubTree(){
		global $lang, $prvlg;
		$out = array();
		$subs = $this->getSubcat();
		if(count($subs) > 1){
			foreach($subs as $sub){
				$out[] = write_html('h3', '', 
					write_html('a', 'action="openSubCat" sub_id="'.$sub->id.'"', 
						$sub->title.
						($prvlg->_chk('sub_category_add')?
							write_html('button', 'class="mini_circle_button ui-state-default hoverable mini_add_btn" module="categorys" action="newSubCat" cat_id="'.$this->id.'" sub_id="'.$sub->id.'" title="'.$lang['new'].'"', write_html('b', '','+'))
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
		if(!isset($post['parent_id']) &&( $post['id'] != 'new' && $post['id'] !='')){
			if( do_update_obj($post, 'id='.$post['old_id'], 'products_category') != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['parent_id'])){
			$result = do_insert_obj($post, 'products_category');
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
		$sql = "SELECT id, title AS name FROM products_category WHERE 
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
	
	static function loadTreeLayout(){
		global $prvlg, $lang;
		$layout = new stdClass();
		$cats = Categorys::getList();
		$tree = '';
		foreach($cats as $cat){
			 $tree .= write_html('h3', '', 
				write_html('a', 'action="openCategory" cat_id="'.$cat->id.'"', 
					$cat->title.
					($prvlg->_chk('sub_category_add')?
						write_html('button', 'class="mini_circle_button ui-state-default hoverable mini_add_btn" module="categorys" action="newSubCat" cat_id="'.$cat->id.'" title="'.$lang['new'].'"', write_html('b', '','+'))
					: '')
				)
			).
			write_html('div', '', 
				$cat->getSubTree()
			);		
		}
		$layout->tree = write_html('div', 'class="accordion"', $tree);
		return fillTemplate('modules/categorys/templates/tree_layout.tpl', $layout);	
	}

	static public function loadMainLayout(){
		$layout = new Layout();
		$cats = Categorys::getList();
		$layout->cats_list = Categorys::loadTreeLayout();
		$first_cat = reset($cats);
		$layout->cat_id = $first_cat->parent_id;
		$layout->cat_detail = $first_cat->loadLayout();
		return fillTemplate("modules/categorys/templates/main_layout.tpl", $layout);
	}
	
	static function printTree(){
		global $lang;
		$layout = new Layout();
		$cats = Categorys::getList();
		foreach($cats as $cat){
			$trs[] = write_html('tr', '',
				write_html('td', '', $cat->title).
				write_html('td', '', $cat->getAccCode())
			);
		}
		$answer['error'] = '';
		$answer['html'] = write_html('table', 'class="result"', 
			write_html('thead', '',
				write_html('tr', '',
					write_html('th', '', $lang['title']).
					write_html('th', '', $lang['code'])
				)
			).
			write_html('tbody', '',
				implode('', $trs)
			)
		);
		return json_encode($answer); 
	}
}