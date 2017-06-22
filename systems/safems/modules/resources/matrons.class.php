<?php
 /* Bums
 *
 *
 */

class Matrons extends Employers {
    public function __construct($id, $busms='') {
		if($busms == ''){
			global $busms;
		}
     	if($id!=''){
			$matron_info = do_query_obj("SELECT * FROM matrons WHERE id=$id", $busms->database, $busms->ip);
			parent::__construct($matron_info->emp_id, new HrMS($matron_info->hrms_id));
			foreach ($matron_info as $key => $value) {
				$this->$key = $value;
			}
		}
    }

    public function loadLayout() {
      $layout = new Layout($this);
      $layout->personel_info = parent::loadLayout();
      $layout->routes= 'Routes';
      $layout->template = 'modules/resources/templates/matrons_layout.tpl';
      return $layout->_print();
    }
	
	public function getTel($all=false){
		$rows = do_query_array("SELECT * FROM phonebook WHERE con='emp' AND con_id=$this->emp_id", $this->hrms->database, $this->hrms->ip);
		$rows = sortArrayOfObjects($rows, $this->hrms->getItemOrder('phonebook-emp-'.$this->emp_id), 'id');
		if( $all==false ){
			$first = reset($rows);
			return isset($first->tel) ? $first->tel : '';
		} else {
			$out = array();
			if($rows!=false && count($rows)>0){
				foreach($rows as $r){
					$out[] = $r->tel;
				} 
			}
			return $out;
		}
	}

    static function getList() {
		global $busms;
      $sql = do_query_array("SELECT * FROM matrons", $busms->database, $sms->ip);
      $out = array();
      foreach($sql as $row){
		$hrms = new HrMS($row->hrms_id);
        $out[] = new Matrons($row->id, $hrms);
      }
      return $out;
    }

    static function loadMainLayout() {
      $layout = new Layout();
      $layout->template = 'modules/resources/templates/matrons_main.tpl';
      $matrons = Matrons::getList();
      if (count($matrons) > 0) {
        $first_matron = reset($matrons);
        $layout->matron_layout = $first_matron->loadLayout();
      }
      $first = true;
      $layout->list_matrons = '';
      foreach($matrons as $matron) {
        $d = new Layout($matron);
        $d->template = 'modules/resources/templates/matron_list_item.tpl';
        if($first) $d->active = 'ui-state-active';
		$d->hrms_id = $matron->hrms_id;
		$d->name = $matron->getName();
        $layout->list_matrons .= $d->_print();
        $first = false;
      }
      return $layout->_print();
    }

    static function addMatron($post) {
      $matron_id = $post['matron_id'];
      if( do_insert_obj(array('id'=> $matron_id), 'matrons', MySql_Database)){
        $answer['id'] = $matron_id;
        $answer['error'] = "";
      } else {
        global $lang;
        $answer['id'] = "";
        $answer['error'] = $lang['error_updating'];
      }
      return json_encode($answer);
    }

    static function delMatron($post){
      $matron_id = $post['id'];
      if( do_query_edit("DELETE FROM matrons WHERE id=$matron_id", MySql_Database)){
        $answer['id'] = $matron_id;
        $answer['error'] = "";
      } else {
        global $lang;
        $answer['id'] = "";
        $answer['error'] = $lang['error_updating'];
      }
      return json_encode($answer);
    }
	
	static function importForm(){
		$layout = new Layout();
		$layout->template = 'modules/resources/templates/import_form.tpl';
		$hrmss = HrMS::getList();
		$layout->con='matrons';
		$layout->hrms_opts = write_select_options(objectsToArray($hrmss));
		return $layout->_print();
	}
	
	static function importMatrons($hrms_id){
		global $this_system, $lang;
		$hrms = new Hrms($hrms_id);
		$job_code = 2;
		$error = '';
		$sql = "SELECT id, name_ltr, name_rtl FROM employer_data WHERE job_code=$job_code";
		$emps = do_query_array( $sql, $hrms->database, $hrms->ip);

		$num = 0;
		if(count($emps) > 0){
			foreach($emps as $emp){
				$emp_id = $emp->id;
				$emp_rtl = $emp->name_rtl;
				$emp_ltr = $emp->name_ltr;
				$chk = do_query("SELECT id FROM matrons WHERE emp_id=".$emp->id." AND hrms_id=$hrms_id", $this_system->database, $this_system->ip);
				if( $chk  == false){
					if(!do_insert_obj(array('emp_id'=>$emp_id, 'hrms_id'=> $hrms_id, 'name_ltr'=>$emp_ltr, 'name_rtl'=>$emp_rtl), "matrons",  $this_system->database, $this_system->ip)){
						$error = $lang['error_updating'];
					} else {
						$num++;
					}
				}  else {
					if(!do_update_obj(array('name_ltr'=>$emp_ltr, 'name_rtl'=>$emp_rtl), "emp_id=$emp_id AND hrms_id= $hrms_id", "matrons",  $this_system->database, $this_system->ip)){
						$error = $lang['error_updating'];
					} 
				}
			}
		} else {
			$error = $lang['error-employers_not_found'];
		}
		return "{\"error\" : \"$error\", \"num\" : \"$num\"}";
	}

	static function getAutocompleteMatron( $value){
		global $lang, $busms;
		$field = "name_".$_SESSION['dirc'];
		$Arabic = new I18N_Arabic('KeySwap');
		$params = array("name_rtl = '$value' ",
			"name_rtl LIKE '$value%' ",
			"name_rtl LIKE '".addslashes($Arabic->swapEa($value))."%' ",
			"LOWER(name_ltr) LIKE LOWER('$value') ",
			"LOWER(name_ltr) LIKE LOWER('$value%') ",
			"LOWER(name_ltr) LIKE LOWER('".addslashes($Arabic->swapAe($value))."%')"
		);
			
		$sql = "SELECT * FROM matrons WHERE (". implode(" OR ", $params) .")";
//echo $sql;
		$out = array();
		$matrons = do_query_array( $sql);
		if($matrons != false && count($matrons)>0){
			foreach($matrons as $matron ){
					$label = str_replace($value, write_html('b', 'style="color:red"', $value), $matron->$field);
					$row = array('id'=>$matron->id, 'name'=> $matron->$field, 'label'=> $matron->$field);
					$out[] = $row;
			}
		} else {
			$out[] = array('error' => $lang['cant_find_item']);	
		}
		return json_encode($out);
	}

  }
?>