<?php
/** Profil 
*
*/

class Profils{	
	
	public function __construct($id, $sms){
		$this->sms = $sms;
		if($id != ''){	
			$profil = do_query_obj("SELECT * FROM school_fees_profils WHERE id=$id", $sms->database, $sms->ip);	
			if(isset( $profil->id )){
				foreach($profil as $key =>$value){
					$this->$key = $value;
				}
				
			}	
		}			
	}
	
	public function getFees($year){
		if(!isset($this->fees)){
			$this->fees = array();
			$fees = do_query_array("SELECT id FROM school_fees WHERE year=$year AND con_id=$this->id AND con='profil'", SMS_Database, $this->sms->ip);
			foreach($fees as $f){
				$this->fees[] = new Fees($f->id, $this->sms);
			}
		}
		return $this->fees;
	}
	
	public function getDates($year){
		$schoolFees = new SchoolFees($this->sms);
		$dates = $schoolFees->getDates($year);
		return $dates;
		
	}
	
	public function loadLayout(){
		global $sms, $this_system;
		$year = $_SESSION['year'];
		$profil = new Layout($this);
		$dates_table = new Layout();
		$dates_table->template = "modules/fees/templates/payments_dates_table.tpl";
		$profil->template = "modules/fees/templates/profil_layout.tpl";
		$profil->currency = $this_system->getSettings('def_currency');		
		$profil->sms_id = $sms->id;
		
		$fees = $this->getFees($year);
		$profil->profil_fees_rows = '';
		$profil->interest_on = $profil->interest=='1' ? 'checked' : '';
		$profil->interest_off = $profil->interest !=1 ? 'checked="checked"' : '';
		if($fees != false){
			foreach($fees as $f){
				$row = $f;
				$row->value = $f->debit !=0 ? $f->debit : ($f->credit * -1);
				$row->main_code = Accounts::fillZero('main', $f->main_code);
				$row->sub_code = Accounts::fillZero('sub', $f->sub_code);
				$row->currency_opts = Currency::getOptions($f->currency);
				$row->sms_id = $sms->id;
				$profil->profil_fees_rows .= fillTemplate("modules/fees/templates/level_fees_rows.tpl", $row);
			}
		}
			//Payment Table
		$dates = $this->getDates($year);
		if($dates != false && count($dates)>0 && $dates[0]->con =='profil'){
			$profil->payments_type_on = 'checked';
			$dates_table->dates_rows = '';
			foreach($dates as $d){
				$d->from = unixToDate($d->from);
				$d->limit = unixToDate($d->limit);
				$dates_table->dates_rows .= fillTemplate("modules/fees/templates/payments_dates_rows.tpl", $d);
			}
		} else {
			$dates_table->dates_table_hidden = 'hidden';
			$profil->payments_type_off = 'checked';
		}
		if(!isset($dates_table->dates_rows) || $dates_table->dates_rows ==''){
			$settlements = array();			
			for($i=1; $i<=12; $i++){
				$s = new stdClass();
				$s->title = $i;
				$settlements[] = $s;
			}
			$dates_table->dates_rows = filleMergedTemplate("modules/fees/templates/payments_dates_rows.tpl", $settlements);
		}
		$profil->dates_table = $dates_table->_print();
		return $profil->_print();
	}
	
	static function newProfil($std_id=''){
		global $sms, $MS_settings;
		$profil = new Layout();
		$year = $_SESSION['year'];
		$profil->template = "modules/fees/templates/profil_layout.tpl";
		if($std_id != ''){		
			$student = new Students($std_id);
			$dates = $student->getDates($year);
		}
		$settlements = array();
		for($i=1; $i<=12; $i++){
			$s = new stdClass();
			if(isset($dates[$i-1])){
				$s->title = $dates[$i-1]->title;
				$s->from = unixToDate($dates[$i-1]->from);
				$s->limit = unixToDate($dates[$i-1]->limit);
			} else {
				$s->title = $i;
			}
			$settlements[] = $s;
		}
		$table = new Layout();
		$table->dates_table_hidden = 'hidden';
		$table->dates_rows = filleMergedTemplate("modules/fees/templates/payments_dates_rows.tpl", $settlements);
		
		$profil->dates_table = fillTemplate("modules/fees/templates/payments_dates_table.tpl", $table);		
		$profil->currency = $MS_settings['def_currency'];		
		$profil->sms_id = $sms->id;
		$profil->interest_off = 'checked';	
		$profil->discount = 0;
		$profil->exclude = 0;
		$profil->bus_discount = 0;
		$profil->lib_discount = 0;
		$profil->id = 'new';
		$profil->std_id = $std_id;
		$profil->admission = $sms->getSettings('def_admission');
		$profil->book_ins = $sms->getSettings('book_insuance');
		$profil->payments_type_off = 'checked';
		$dates_table_hidden = 'hidden';
		return $profil->_print();
	}
	
	static function getList(){
		global $sms;
		return do_query_array("SELECT * FROM school_fees_profils", SMS_Database, $sms->ip);
	}

	static function calcDiscount($value, $discount, $exclude=0){
		if(strpos($discount, '%') !== false){
			$discount = trim(str_replace('%', '',$discount));
			$val = ($value-$exclude) * ($discount / 100);
			return floor($val / 10) * 10;
		} else {
			return $discount;
		}
	}
	
	static function saveProfil($post){
		global $sms;
		unset($post['title']);
		$result = true;
		$post['title'] = $post['profil_title'];
		$post['con'] = 'profil';
		
		if($post['profil_id'] == 'new'){
			$new_profil = $post;
			unset($new_profil['id']);
			$profil_id = do_insert_obj($new_profil, 'school_fees_profils', $sms->database, $sms->ip);
			if($profil_id!= false){
				$result = $profil_id;
				$post['con_id'] = $profil_id;
				/*if($post['payments_type'] == '1'){
					if(Fees::SavePaymentsDate($post) == false){
						$result = false;
					}
				}*/
			} else {
				$result = false;
			}
		} else {
			$profil_id = $post['profil_id'];
			$profil = $post;
			unset($profil['id']);
			if($result = do_update_obj($profil, "id=$profil_id", 'school_fees_profils', $sms->database, $sms->ip)){
				$post['con_id'] = $profil_id;
				// save dates
				$dates_post = $post;
				$dates_post['con'] = 'profil';
				$dates_post['con_id'] = $profil_id;
				$year = $_SESSION['year'];
				/*if($post['payments_type'] == '1'){
					do_query_edit("DELETE FROM school_fees_payments WHERE con='profil' AND con_id=$profil_id AND year=$year", $sms->database, $sms->ip);
					if(Fees::SavePaymentsDate($post) == false){
						$result = false;
					}
				}*/
			}
		}		
		if($result){
			$stds = do_query_array("SELECT std_id FROM school_fees_profil_std WHERE profil_id=$profil_id", $sms->database, $sms->ip);
			if($stds != false){
				foreach($stds as $std){
					$student = new Students($std->std_id, $sms);
					$student->generatePayments();
				}
			}
			return  array('error'=>'', 'profil_id'=>$profil_id, 'title'=>$post['title']);
		} else {
			return false;
		}
	}
	
	static function _delete($profil_id){
		global $sms;
		$result = false;
		if(do_delete_obj("id=$profil_id", 'school_fees_profils', $sms->database, $sms->ip)){
			if(do_delete_obj("con='profil' AND con_id=$profil_id", 'school_fees', $sms->database, $sms->ip)){
				if(do_delete_obj("profil_id=$profil_id", 'school_fees_profil_std', $sms->database, $sms->ip)){
					$result = true;
				}
			}
		}
		return array('error'=> $result ? '' : $lang['error_updating']);
	}
}
	