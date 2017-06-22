<?php

  /* Busms
  *
  *
  */

  class Bus {
	public function __construct($id, $busms=''){
		if($busms == ''){
			global $busms;
		}
      $bus_info = do_query_obj("SELECT * FROM bus WHERE id=$id", $busms->database, $busms->ip);
      foreach ($bus_info as $key => $value) {
        $this->$key = $value;
      }
    }
	
	public function getName(){
		return $this->code;
	}

    public function loadLayout() {
      $layout = new Layout($this);
      $layout->routes= 'Routes';

      $layout->owned_check_1 = $this->owned ?: "checked";
      $layout->owned_check_2 = !$this->owned ?: "checked";
	  $layout->bus_info_form = fillTemplate('modules/resources/templates/bus_infos.tpl', $layout);
      $layout->template = 'modules/resources/templates/bus_layout.tpl';
      return $layout->_print();
    }

    static function getList() {
		global $busms;
      $sql = do_query_array("SELECT * FROM bus", $busms->database, $busms->ip);
      $out = array();
      foreach($sql as $row){
        $out[] = new Bus($row->id);
      }
      return $out;
    }
	
	static function loadNewBusFrom(){
		return fillTemplate('modules/resources/templates/bus_infos.tpl', array('new'=> '-new'));
	}

    static function loadMainLayout(){
      $layout = new Layout();
      $layout->template = 'modules/resources/templates/bus_main.tpl';
      $buss = Bus::getList();
      if(count($buss) > 0) {
        $first_bus = $buss[0];
        $layout->bus_layout = $first_bus->loadLayout();
      }
      $first = true;
      $layout->list_buss = '';
      foreach($buss as $bus){
        $d = new Layout($bus);
        $d->template = 'modules/resources/templates/bus_list_item.tpl';
        if($first) $d->active = 'ui-state-active';
        $layout->list_buss .= $d->_print();
        $first = false;
      }
      return $layout->_print();
    }

    static function saveBus($post) {
		global $busms;
      $bus_id = $post['id'];
	  if($bus_id) {
		  $result = do_update_obj($post, "id=$bus_id", 'bus', $busms->database, $busms->ip);
	  } else {
		  $result = do_insert_obj($post, 'bus', $busms->database, $busms->ip);
		  $bus_id = $result;
	  }
	 
	  if($result ){
		$answer['id'] = $bus_id;
		$answer['error'] = "";
	  } else {
		global $lang;
		$answer['id'] = "";
		$answer['error'] = $lang['error_updating'];
	  }
	  return json_encode($answer);
    }

    static function delBus($post){
		global $busms;
      $bus_id = $post['id'];
      if( do_query_edit("DELETE FROM bus WHERE id=$bus_id", $busms->database, $busms->ip)){
        $answer['id'] = $bus_id;
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