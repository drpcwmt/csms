<?php
/** Search Engine
*
*/

function getTableNameFromField($field){
	if(strpos($field, '.') !== false){
		$fs = explode('.', $field);
		return $fs[0].'.'.$fs[1];
	} else {
		echo 'ERROR : '.$field;
		return false;
	}
}

function getFieldShortName($field){
	if(strpos($field, ' AS ') !== false){
		$fs = explode(' AS ', $field);
		return $fs[1];
	} elseif(strpos($field, '.') !== false){
		$fs = explode('.', $field);
		return $fs[2];
	} else {
		return $field;
	}	
}

function addElmntToArr($elmnt, $array){
	if(!in_array($elmnt, $array)) { 
		$array[] = $elmnt;
	}
	return $array;
}

class SearchEngine{
	public $hiddden_array = array();
	
	public function __construct(){
		
	}
	
	public function loadDataOutForm(){
		global $this_system, $lang;
		if(!isset($this->table)){
			return write_error('must specify table!');
		}
		
		$fields = getTableFields($this->table);
		foreach($fields as $field){
			if(!in_array($field, $this->$idden_array)){
				$student_li[] = write_html('li', '',
					write_html('label', '', 
						'<input type="checkbox" name="fields[]" value="'.$this->main_db.'.student_data.'.$field.'" />'.
						$lang[$field]
					)
				);
			}
		}
	}
	
	public function loadOrderForm(){
		$form = new Layout();
		$form->template = 'modules/reports/templates/engine_order.tpl';
		$form->order_by = write_select_options($this->getOrders());
		return $form->_print();
	}

	/*public function loadDataOutForm(){
		$layout = new Layout();
		$layout->name_dirc = 'name_'.$_SESSION['dirc'];
		$layout->template = 'modules/reports/templates/engine_data_out.tpl';
		return $layout->_print();
	}*/
	
	
	public function createWizard(){
		$div1 = write_html('div', 'class="item"', $this->loadDataOutForm());
		$div2 = write_html('div', 'class="item"', $this->loadFilterForm());
		$div3 = write_html('div', 'class="item"', $this->loadOrderForm());
		$div4 = write_html('div', 'class="item"', $this->loadLayoutForm());
		
		return write_html('form', 'id="list_content"', $div1 .$div2.$div3.$div4 );
	}
	
	public function loadLayoutForm(){
		$layout = new Layout();
		$layout->template = 'modules/reports/templates/engine_layout.tpl';
		return $layout->_print();
	}
	
	public function loadFilterForm(){
		$form = new Layout();
		$form->template = 'modules/reports/templates/engine_filter.tpl';
		$form->filter_opts = $this->getFilters();
		$form->status_opts = $this->getStatusFilter();
		return $form->_print();
	}
	
	public function getSavedRequest(){
		global $lang;
		$querys = do_query_array("SELECT name, id FROM list_procudures ORDER BY name ASC");
		$trs = array();
		foreach($querys as $q){
			$trs[] = write_html('tr', '',
				write_html('td', 'width="20"', write_html('button', 'type="button", action="openQuery" query_id="'.$q->id.'" class="circle_button hoverable ui-state-default"', write_icon('extlink'))
				).
				write_html('td', 'width="20"', write_html('button', 'type="button", action="deleteSavedProced" query_id="'.$q->id.'" class="circle_button hoverable ui-state-default"', write_icon('trash'))
				). 
				write_html('td', '', $q->name)
			);
		}
		if(count($trs) > 0){
			return write_html('table', 'class="result"',
				implode('', $trs)
			);
		} else {
			return write_error($lang['result_not_found']);
		}
	}
	
	static function saveQuery($post){
		global $this_system, $lang;
		$name = $post['savereq'];
		$sql = addslashes($_SESSION['last_list_requet']);
		$extras = $_SESSION['last_list_extra'];
		$sql = addslashes($_SESSION['last_list_requet']);
		$main_order = $_SESSION['last_list_main_order'];
		$grouped = $_SESSION['last_list_grouped'];
		$select = $_SESSION['last_list_selected'];
		$template = $_SESSION['last_list_template'];
		if(isset($this_system->db_year)){
			$sql = addslashes($_SESSION['last_list_requet']);
			$main_order = str_replace($this_system->db_year, '[@db_year]', $sql);
			$grouped = str_replace($this_system->db_year, '[@db_year]', $grouped);
			$select = str_replace($this_system->db_year, '[@db_year]', $select);
		}
		$error = false;
		$chk = do_query_obj("SELECT id FROM list_procudures WHERE name='$name'", $this_system->database, $this_system->ip);
		$row = array(
			'name'=>$name,
			'sql'=>$sql,
			'select'=>$select,
			'order'=> $main_order,
			'extras'=>$extras,
			'grouped'=>$grouped
		);
		if(isset($chk->id)){
			if(!do_update_obj($row, "id=$chk->id", "list_procudures", $this_system->database, $this_system->ip)){
				$error = $lang['error'];
			} 
		} else {
			if(!do_insert_obj($row, "list_procudures",$this_system->database, $this_system->ip)){
				$error = $lang['error'];
			} 
		}
		
		$answer =array();
		if($error != false){
			$answer['id'] = "";
			$answer['error'] = $error ;
		} else {
			$answer['id'] = $name;
			$answer['error'] = "";
		}
		return $answer;
	}
	
	static function deleteQuery($id){
		global $sms;
		if(do_delete_obj("id=$id", "list_procudures",  $sms->database, $sms->ip)){
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updaing'];
		}
		return $answer;
	}
	
	public function loadQuery($id){
		global $sms;
		$proc = do_query_obj("SELECT * FROM  list_procudures WHERE id=$id", $sms->database);
		$sql =stripslashes($proc->sql);
		if(isset($_SESSION['year'])){
			$sql = str_replace('[@db_year]', Db_prefix.$_SESSION['year'], $sql);
		}
		$main_order = $proc->order;
		$extras = array();
		if($proc->extras != ''){
			$extras = explode(',', $proc->extras);
		}
		$grouped = $proc->grouped == 1 && $main_order != '' ? true : false;
		$selfields = explode(',', $proc->select);
		$show_ser = false;
		return write_html('div', 'class="ui-corner-all ui-state-highlight" style="padding:5px"',
			write_html('h2', '', $proc->name)
		).
		$this->loadQueryResult($sql, $selfields, $main_order, $grouped, $show_ser, $extras, $proc->template);
	}

	public function loadQueryResult($sql, $selfields, $main_order, $grouped=0, $show_ser, $extras, $template=''){
		global $this_system, $lang;
		// storing the requet to the session
		$_SESSION['last_list_requet'] = $sql;
		$_SESSION['last_list_main_order'] =  $main_order ;
		$_SESSION['last_list_extra'] =  implode(',', $extras);
		$_SESSION['last_list_grouped'] = $grouped;
		$_SESSION['last_list_selected'] = implode(',',$selfields);
		$_SESSION['last_list_template'] = $template;
		
		if($template != ''){
			$this->list_template = $template;
		}
		$items = do_query_array($sql);
		$total_items = count($items);
		$serial = 0;
		if( $items !=false && $total_items > 0){		
			//TH
			$ths = array();
			if($show_ser){
				$ths[] = 'serial';
			}
			foreach($selfields as $col){
				$title = getFieldShortName($col);
				$ths[] = $title;
			}
			if(is_array($extras)){
				foreach($extras as $col){
					$ths[] = $col;
				}
			}
			
			// TDs
			if($grouped == false){
				return $this->createTable($items, $ths);
			} else {
				$out = '';
				$tab = explode('.', getTableNameFromField($main_order));
				foreach($items as $std){
					if($tab[1] == 'levels') { $key = $std->level_name;}
					elseif($tab[1] == 'classes') { $key = $std->class_name;}
					elseif($tab[1] == 'groups') { $key = $std->group_name;}
					elseif($tab[1] == 'classes_std') { $key = $std->new_stat;}
					else {
						if(isset($selectableFields[getFieldShortName($main_order)])){
							$key = $selectableFields[getFieldShortName($main_order)][$std[getFieldShortName($main_order)]];
						} elseif(strpos(getFieldShortName($main_order), '_date') !== false){
							$key = unixToDate($std[getFieldShortName($main_order)]);
						} else {
							$key = $std[$short_field];
						}
					} 
					$trs[$key][] = $std;
				}
				foreach($trs as $key=>$stds){
					$out .= write_html('fieldset', 'style="page-break-after:always"',
						write_html('legend', '', $key).
						$this->createTable($stds, $ths)
					);
				}
				return $out;
			}
		} else {
			return write_error(write_html('h2', '', $lang['result_not_found']));
		}
			
	}
	
	public function loadMainLayout($content){
		$layout = new Layout();
		$layout->template = 'modules/reports/templates/engine_main_layout.tpl';
		$layout->content = $content;
		return $layout->_print();
	}
}
