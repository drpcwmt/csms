<?php

  /* Bus
  *
  *
  */


  class Bus {
    function __construct($id) {
      global $busms;
      $bus_info = do_query_obj("SELECT * FROM bus WHERE id=$id", $busms->database, $busms->ip);
      foreach ($bus_info as $key => $value) {
        $this->$key = $value;
      }
    }
	
	public function getName(){
		return $this->id;
	}

    public function loadLayout() {
      $layout = new Layout($this);
      $layout->routes= 'Routes';

      $layout->owned_check_1 = $this->owned=='1' ? 'checked="checked"' :'';
      $layout->owned_check_0 = $this->owned=='0' ?  'checked="checked"' :'';
	  $layout->bus_info_form = fillTemplate('modules/bus/templates/bus_infos.tpl', $layout);
      $layout->template = 'modules/bus/templates/bus_layout.tpl';
      return $layout->_print();
    }

    static function getList() {
      global $busms;
      $sql = do_query_array("SELECT * FROM bus",  $busms->database, $busms->ip);
      $out = array();
      foreach($sql as $row){
        $out[] = new Bus($row->id);
      }
      return $out;
    }
	
	static function loadNewBusFrom(){
		return fillTemplate('modules/bus/templates/bus_infos.tpl', array('new'=> '-new'));
	}

    static function loadMainLayout(){
      $layout = new Layout();
      $layout->template = 'modules/bus/templates/bus_main.tpl';
      $buss = Bus::getList();
      if(count($buss) > 0) {
        $first_bus = $buss[0];
        $layout->bus_layout = $first_bus->loadLayout();
      }
      $first = true;
      $layout->list_buss = '';
      foreach($buss as $bus){
        $d = new Layout($bus);
        $d->template = 'modules/bus/templates/bus_list_item.tpl';
        if($first) $d->active = 'ui-state-active';
        $layout->list_buss .= $d->_print();
        $first = false;
      }
      return $layout->_print();
    }

    static function saveBus($post) {
      global $busms;
      $bus_id = $post['id'];
  	  if($bus_id!='') {
  		  $result = do_update_obj($post, "id=$bus_id", 'bus',  $busms->database, $busms->ip);
  	  } else {
  		  $result = do_insert_obj($post, 'bus',  $busms->database, $busms->ip);
  		  $bus_id = $result;
  	  }
  	 
  	  if($result!==false ){
    		$bus = new Bus($bus_id);
    		$answer['id'] = $bus_id;
    		$answer['code'] = $bus->code!='' ? $bus->code : $bus->id;
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
      if( do_query_edit("DELETE FROM bus WHERE id=$bus_id",  $busms->database, $busms->ip)){
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