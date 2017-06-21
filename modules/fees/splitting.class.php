<?php
/** Splitting Fees
*
*/
class Splitting {
	
	public function __construct($student){
		$this->student = $student;
		$this->sms = $student->sms;
		$this->safems = $this->sms->getSafems();
	}
	
	public function loadLayout(){
		global $prvlg;
		$layout = new Layout($this->student);
		$layout->sms_id = $this->sms->id;
		$layout->template = 'modules/fees/templates/splitting_layout.tpl';
		$layout->splitting_table = $this->getSplitList();
		if($prvlg->_chk('edit_std_fees') == false){
			$layout->prvlg_edit_fee = 'hidden';
		}
		return $layout->_print();
		
	}
	
	public function getSplitList(){
		global $this_system, $lang, $prvlg;
		$student = $this->student;
		$sms = $this->sms;
		$trs = array();
		$rows = do_query_array("SELECT * FROM splitting WHERE std_id=$student->id ORDER BY year DESC", $this->sms->database, $this->sms->ip);
		foreach($rows as $row){
			$split = new Layout($row);
			$split->template = 'modules/fees/templates/splitting_row.tpl';
			$services = explode(',', $row->service_id);
			$split->service_name = '';
			foreach($services as $ser){
				$service = new ServicesIg($ser);
				$split->service_name .= $service->getName().' - '. $service->lvl;
			}
			$split->year = $row->year .'/'.($row->year+1);
			if($this_system->type == 'sms' && $row->paid==''){
				$split->button = write_html('button', 'class="circle_button ui-state-default hoverable"  action="removeSplit" split_id="'.$row->id.'" sms_id="'.$sms->id.'" title="'.$lang['remove'].'"',
					write_icon('trash')
				);
			} elseif ($this_system->type == 'safems'){
				$split->button = ($row->paid!='' ? 
					 write_html('button', 'class="circle_button ui-state-default hoverable"  action="refundSplit" split_id="'.$row->id.'" sms_id="'.$sms->id.'"  title="'.$lang['pay'].'"',
						write_icon('arrowreturnthick-1-w')
					)
				:
					 write_html('button', 'class="circle_button ui-state-default hoverable"  action="paySplit" split_id="'.$row->id.'" sms_id="'.$sms->id.'" paid="'.($row->fees).'" title="'.$lang['pay'].'"',
						write_icon('plus')
					)
				);
			}
			
			$trs[] = $split->_print();
		}
		
		$layout= new Layout();
		$layout->template = 'modules/fees/templates/splitting_table.tpl';
		$layout->edit_split_prvlg = $prvlg->_chk('std_fees_edit')? '' : 'hidden';
		$layout->trs = implode('', $trs);
		$layout->student_name = $student->getName();
		return $layout->_print();
	}
	
	public function addForm(){
		$student = $this->student;
		$sms = $this->sms;
		$cur_year = $_SESSION['year'];
		$years = getYearsArray();
		$opts = array();
		foreach($years as $year){
			if(do_query_array("show tables like 'services_ig'", Db_prefix.$year, $sms->ip)!=false){
				$std_services=do_query_array("SELECT materials_std.services FROM materials_std, services_ig WHERE materials_std.std_id=$student->id AND materials_std.services=services_ig.id AND services_ig.lvl='AL'", Db_prefix.$year, $sms->ip);
				foreach($std_services as $ser){
					$ser = new ServicesIg($ser->services, $sms, $year);	
					$opts[] = write_html('li', 'class="ui-state-default hoverable list_item"',
						'<input type="checkbox" name="services[]" value="'.$ser->id.'" />'.
						$ser->getName().'-'.$ser->lvl.' '.$year.'/'.($year+1)
					);
				}
			}
		}
		$layout = new Layout();
		$layout->std_id = $student->id;
		$layout->sms_id = $sms->id;
		$layout->template = 'modules/fees/templates/splitting_form.tpl';
		$layout->opts = implode('', $opts);
		return $layout->_print();
	}
	
	static function _save($post){
		global $sms, $lang;
		$post['year'] = $_SESSION['year'];
		$std_id = $post['std_id'];
		$year = $post['year'];
		$service_id = implode(',', $post['services']);
		$post['service_id'] = $service_id;
		$chk = do_query_obj("SELECT * FROM splitting WHERE std_id=$std_id AND year=$year", $sms->database, $sms->ip);
		if($chk == false){
			$id= do_insert_obj($post, 'splitting', $sms->database, $sms->ip);
			if($id!=false){
				$split = new Layout($post);
				$split->template = 'modules/fees/templates/splitting_row.tpl';
				$service = new ServicesIg($service_id, $sms, $year);
				$split->service_name = $service->getName().' - '. $service->lvl;
				$split->year = $year .'/'.($year+1);
				$split->button = write_html('button', 'class="circle_button ui-state-default hoverable"  action="removeSplit" split_id="'.$id.'" sms_id="'.$sms->id.'" title="'.$lang['remove'].'"',
					write_icon('trash')
				);
	
				return array('error'=>'', "id"=>$id, "html"=>$split->_print());
			} else {
				return false;
			}
		} else {
			return array('error'=> $lang['item_allready_exists']);
		}
	}

	static function _delete($post){
		global $sms;
		$id = $post['split_id'];
		$result = do_delete_obj("id=$id", 'splitting', $sms->database, $sms->ip);
		if($result!=false){
			return array('error'=>'', "id"=>$id);
		} else {
			return false;
		}
	}
	
	static function payForm($split_id){
		global $sms, $safems;
		$split = do_query_obj("SELECT * FROM splitting WHERE id=$split_id", $sms->database, $sms->ip);
		$year = $split->year;

		$layout = new Layout($split);
		$layout->split_id = $split->id;
		$layout->sms_id = $sms->id;
		$services = explode(',', $split->service_id);
		$layout->service_name = '';
		foreach($services as $ser){
			$service = new ServicesIg($ser, $sms, $year);
			$layout->service_name .= $service->getName().' - '. $service->lvl;
		}
		$layout->template = 'modules/fees/templates/splitting_pay_form.tpl';
		$layout->currency = $sms->getSettings('def_currency');	
		$layout->banks_opts = Banks::getOptions($safems->getSettings('def_bank'));
		return $layout->_print();
	}

	static function savePay($post){
		global $sms, $lang;
		if($post['paid'] > 0){
			$update = array('paid'=>$post['paid']);
			$split_id = $post['split_id'];
			$result = do_update_obj($update, "id=$split_id", 'splitting', $sms->database, $sms->ip);
			echo $result;

			if($result!=false){
				$year = $_SESSION['year'];
				$split = do_query_obj("SELECT * FROM splitting WHERE id=$split_id", $sms->database, $sms->ip);
				print_r($split);
				$student = new Students($split->std_id, $sms);
				$ingoing = array();
				$ingoing['from'] = $student->getAccCode();
				$ingoing['to'] = getAccount(BC_account);		
				$ingoing['type'] = $_POST['payment_mode'];		
				$ingoing['currency'] =  $_POST['currency'];
				$ingoing['ccid'] = $sms->getCC();
				$ingoing['date'] = dateToUnix($_POST['date']);
				$ingoing['bank'] = $_POST['bank'];	
				$ingoing['notes'] = $lang['split'].': '.$student->getName(). ' - '. $year.'/'.($year+1) .' - '. $sms->code  ;
				$ingoing['recete_notes'] =  $lang['split'].': '.$student->getName(). ' - '. $year.'/'.($year+1) .' - '. $sms->code  ;
	
				$ingoing = Ingoing::addNewPayment($ingoing);
				return array('error'=>'', "id"=>$split_id, "recete"=>$ingoing->getRecete());
			}
		}
		return false;
	}



	static function saveRefund($post){
		global $sms, $lang;
		if($post['value'] > 0){
			$update = array('paid'=>$post['paid']);
			$split_id = $post['split_id'];
			$result = do_update_obj($update, "id=$split_id", 'splitting', $sms->database, $sms->ip);
			echo $result;

			if($result!=false){
				$year = $_SESSION['year'];
				$split = do_query_obj("SELECT * FROM splitting WHERE id=$split_id", $sms->database, $sms->ip);
				print_r($split);
				$student = new Students($split->std_id, $sms);
				$ingoing = array();
				$ingoing['from'] = $student->getAccCode();
				$ingoing['to'] = getAccount(BC_account);		
				$ingoing['type'] = $_POST['payment_mode'];		
				$ingoing['currency'] =  $_POST['currency'];
				$ingoing['ccid'] = $sms->getCC();
				$ingoing['date'] = dateToUnix($_POST['date']);
				$ingoing['bank'] = $_POST['bank'];	
				$ingoing['notes'] = $lang['split'].': '.$student->getName(). ' - '. $year.'/'.($year+1) .' - '. $sms->code  ;
				$ingoing['recete_notes'] =  $lang['split'].': '.$student->getName(). ' - '. $year.'/'.($year+1) .' - '. $sms->code  ;
	
				$ingoing = Ingoing::addNewPayment($ingoing);
				return array('error'=>'', "id"=>$split_id, "recete"=>$ingoing->getRecete());
			}
		}
		return false;
	}
}
		
