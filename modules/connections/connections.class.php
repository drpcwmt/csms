<?php
/** Accounts
*
*/
class Connections{
	
	public function __construct($id){
		$sql = "SELECT * FROM connections WHERE id=$id";
		$conx = do_query_obj($sql);
		if(isset($conx->id)){ 
			foreach($conx as $key =>$value){
				$this->$key = $value;
			}
		}
	}
	
	public function getConnections(){
		$layout = new Layout($this);
		$servers = do_query_array("SELECT * FROM connections WHERE id=$this->id ORDER BY type ASC", $this_system->database, $this_system->ip);
		$layout->servers_trs = '';
		foreach($servers as $s){
			$layout->servers_trs .= write_html('tr', '',
				write_html('td', '', $s->type).
				write_html('td', '', $s->ip).
				write_html('td', '', $s->url).
				write_html('td', '', $s->notes).
				write_html('td', '', 
					write_html('button', 'class="ui-state-default hoverable circle_button" action="syncServer" rel="'.$s->id.'"',
						write_icon("refresh")
					)
				)
				
			);
		}
		return fillTemplate('modules/connections/templates/costcenter_connection.tpl', $layout);	
	}
	static function LoadNewConection($type=''){
		$layout = new stdClass();
		if($type == ''){
			$systems = array(
				"sms"=>"SMS",
				"hrms"=>"HrMS",
				"storems"=>"StoreMS",
				"libms"=>"LibMS",
				"busms"=>"BusMS",
				"safems"=>"SafeMS",
				"storems"=>"StoreMS"
			);
			$layout->system_type_inp = write_html_select('name="type" class="combobox"', $systems);
		} else {
			$layout->system_type_inp = '<input type="hidden" name="type" value="'.$type.'" />';
			$layout->tr_type_hidden = 'hidden';
		}
		$layout->type = $type;
		return fillTemplate('modules/connections/templates/new_connection.tpl', $layout);	
	}
	
	static function loadConxTable($systems){
		$table = new Layout;
		$table->template =  "modules/connections/templates/connections_table.tpl";			
		if($systems != false){
			$table->servers_trs = '';
			foreach($systems as $s){
				$table->servers_trs .= write_html('tr', '',
					write_html('td', '', $s->type).
					write_html('td', '', $s->ip).
					write_html('td', '', $s->url).
					write_html('td', '', $s->notes).
					write_html('td', '', 
						write_html('button', 'class="ui-state-default hoverable circle_button" module="connections" action="syncServer" rel="'.$s->id.'"',
							write_icon("refresh")
						)
					)
					
				);
			}
		}
		return $table->_print();
	}
	
	static function saveConnection($post){
		global $lang, $this_system;
		$result = false;
		if(isset($post['id']) && $post['id'] != ''){
			if( do_update_obj($post, 'id='.$post['id'], 'connections', $this_system->database, $this_system->ip) != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(!isset($post['id'])|| $post['id'] == ''){
			$result = do_insert_obj($post, 'connections', $this_system->database, $this_system->ip);
			$id = $result;
		}
		
		if($result!= false){
			$cc = do_query_obj("SELECT title FROM cc WHERE id=".$post['ccid'], $this_system->database, $this_system->ip);
			$name = $cc->title;
			// SMS connection afters
			if($post['type'] == 'sms'){					
				$code = '151'.$post['ccid'];
				$chk_exist = do_query_array("SELECT code FROM codes WHERE code ='$code'", $this_system->database, $this_system->ip);
				if($chk_exist == false ){
					do_insert_obj(array(
						'title'=>$name,
						'code'=>$code,
						'level'=> 4,
						'notes'=> $lang['auto_generated']
					), 'codes', $this_system->database, $this_system->ip);
				}
			} elseif($post['type'] == 'hrms'){
				$name = do_query_obj("SELECT title FROM cc WHERE id=".$post['ccid'], $this_system->database, $this_system->ip);	
				$code = '251'.$post['ccid'];
				$chk_exist = do_query_array("SELECT code FROM codes WHERE code ='$code'", $this_system->database, $this_system->ip);
				if($chk_exist == false ){
					do_insert_obj(array(
						'title'=>$name,
						'code'=>$code,
						'level'=> 4,
						'notes'=> $lang['auto_generated']
					), 'codes', $this_system->database, $this_system->ip);
				}
			}
		}
		
		if($result!=false){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function syncServer($id){
		global $MS_settings, $lang;
		$results = 0;
		$conx = do_query_obj("SELECT * FROM connections WHERE id=$id", MySql_Database);
		 
		if($conx->type == 'sms'){
			include_once('modules/connections/sync.php');
			return syncSMS($conx);
		}					
	}
}