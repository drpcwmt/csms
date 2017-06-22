<?php
/** Banks main Class
*
*/
define('safes_code', 162);

class Safes {
				
	static function getList(){
		global $this_system;
		$conx = do_query_array("SELECT * FROM connections WHERE type='safems'", $this_system->database, $this_system->ip);
		$out = array();
		foreach($conx as $con){
			$out[] = new SafeMS($con->id);
		}
		return $out;
	}
	
	static function loadMainLayout(){		
		$menu = new Layout();
		$menu->template = 'modules/safes/templates/safes_menu.tpl';
		$safems = Safes::getList();
		$menu->safes_lis = '';
		foreach($safems as $safe){
			$acc = $safe->getAccount();
			$menu->safes_lis .= write_html('li', '',
				write_html('a', 'module="safems" action="openSafe" safems_id="'.$safe->id.'" class="ui-state-default hoverable"', $acc->title)
			);	
		}
		$layout = new Layout();
		$layout->template = 'modules/safes/templates/main_layout.tpl';
		$layout->menu = $menu->_print();		
		return $layout->_print();		
	}

}