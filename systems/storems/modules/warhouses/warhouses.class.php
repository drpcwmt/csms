<?php
/** Warhouses
*
*
*/

require_once('modules/products/products.class.php');


class Warhouses{
	public function __construct($id){
		if($id != ''){	
			$war = do_query_obj("SELECT * FROM warhouses WHERE id=$id", MySql_Database);	
			if(isset( $war->id )){
			
				foreach($war as $key =>$value){
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
	
	public function laodLayout(){
		$item = $this;
		$item->details_tab = fillTemplate("modules/warhouses/templates/warhouse_infos.tpl", $item);
		$item->recive_tab = $this->recivingForm();
		return filltemplate('modules/warhouses/templates/warhouse.tpl', $item);
	}
	
	static function newForm(){
		$layout = new Layout();
		$layout->template = 'modules/warhouses/templates/warhouse_infos.tpl';
		return $layout->_print();	
	}

	static public function loadMainLayout(){
		$layout = new stdClass();
		$wars = Warhouses::getList();
		$count=0;
		$layout->wars_list = '';
		foreach($wars as $war){
			if($count == 0 ){
				$first_war = $war;
			}
			$wars_list[] = write_html('li', 'class="hoverable clickable ui-stat-default ui-corner-all ui-state-default '.($count==0 ? 'ui-state-active' : '').'" action="openWarhouse" warid="'.$war->id.'"', $war->name );
			$count++;
		}
		
		$layout->wars_list .= write_html('div', '', 
			write_html('ul', 'class="list_menu listMenuUl sortable" rel="warhouses"', 
				implode('', $wars_list)
			)
		);	
		
		
		$layout->warhouse_details = $first_war->laodLayout();
		
		return fillTemplate("modules/warhouses/templates/warhouses_main_layout.tpl", $layout);
	}
	
	public function recivingForm(){
		$form = new stdClass();
		$form->war_id = $this->id;
		$warhouses = objectsToArray(Warhouses::getList());
		$form->warhouses_opts = write_select_options($warhouses);
		return fillTemplate("modules/warhouses/templates/reciving_form.tpl", $form);
	}
	
	static public function getList(){
		$wars = do_query_array("SELECT * FROM warhouses", MySql_Database);	
		foreach($wars as $war ){
			$out[] = new Warhouses($war->id);
		}
		return sortArrayOfObjects($out, getItemOrder('warhouses'), 'id');
	}
	
	public function getProductStock($prod_id){
		$stock = do_query_obj("SELECT stock FROM stocks WHERE war_id=$this->id AND prod_id=$prod_id", MySql_Database);	
		if(isset($stock->stock) && $stock->stock > 0){
			return $stock->stock;
		} else {
			return 0;
		}
		
	}

	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'warhouses', MySql_Database) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(isset($post['id'])){
			$result = do_insert_obj($post, 'warhouses', MySql_Database);
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
	
}
?>