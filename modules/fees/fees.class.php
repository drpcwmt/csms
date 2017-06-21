<?php
/** Fees 
*
*/

class Fees{	
	
	public function __construct($id, $sms=''){
		if($sms == '' ){
			global $sms;
		}
		if($id != ''){	
			$fees = do_query_obj("SELECT * FROM school_fees WHERE id=$id", $sms->database, $sms->ip);	
			if(isset( $fees->id )){
				foreach($fees as $key =>$value){
					$this->$key = $value;
				}
				$this->sms = $sms;
			}else {
				//echo $id.'-';
			}
		}		
	}
	
	public function getAccount(){
		if($this->main_code == ''){
		//	die("Error fees_id=".$this->id);
		}
		
		if(!isset($this->account)){
			$this->account = getAccount($this->main_code, $this->sub_code);
		}
		return $this->account;
	}
	
	public function getMainAccCode(){
		$this_acc = $this->getAccount();
		return substr($this_acc->main_code, 0, 2);			
	}
	
	static function loadNewFeesForm($con, $con_id){
		global $safems, $sms;
		$year = $_SESSION['year'];
		$form = new Layout();
		$form->template = 'modules/fees/templates/new_fees_form.tpl';
		$form->con = $con;
		$form->con_id = $con_id;
		$form->currency_opts = Currency::getOptions($safems->getSettings('def_currency'));
		$form->levels_values_trs = '';
		$school = new SMS($sms->id);
		$levels = $school->getLevelList();
		
		$form->payments_ths = '';
		$form->payments_tds = '';
		if($con == 'student'){
			$student = new Students($con_id, $sms);
			$dates = $student->getDates($year);
		} elseif ($con == 'profil'){
			$profil = new profils($con_id, $sms);
			$dates = $profil->getDates($year);
		} elseif ($con == 'level'){
			$level = new Levels($con_id, $sms);
			$dates = $level->getDates($year);
		} elseif ($con == 'bus' || $con == 'books'){
			//$bus = new RoutesGroup($con_id, $sms);
			$schoolFees = new SchoolFees($sms);
			$dates = $schoolFees->getDates($year);
		} 
		foreach($dates as $date){
			$form->payments_ths .= write_html('th', '', $date->title);
			$form->payments_tds .= write_html('td', '', '<input type="text" style="text-align:center" class="payment_values" name="payments[]" />');
		}

		foreach($levels as $level){
			$form->levels_values_trs .= write_html('tr', '', 
				write_html('td', 'class="lab"', $level->getName()).
				write_html('td', '', '<input type="checkbox" '.($con_id == $level->id ? 'checked':'').' name="value-'.$level->id.'" value="1" />')
			);
		}
		
		$form->applytohidden = $con != 'level' ? 'hidden' : '';
		
		return $form->_print();
	}
	
	static function saveNewFees($post){
		global $sms;
		$year = $_SESSION['year'];
		$result = true;
		$values = array(
			'title'=> $post['title'],
			'main_code'=> Accounts::removeZero($post['main_code']),
			'con'=> $post['con'],
			'sub_code'=> $post['sub_code'],
			'currency'=> $post['currency'],
			'discount'=> $post['discount'],
			'increase'=> $post['increase'],
			/*'interest'=> $post['interest'],
			'interest_period'=> $post['interest_period'],*/
			'year'=> $_SESSION['year']
		); 
		if(isset($post['type']) && $post['type'] == 'debit'){
			$values['debit'] = $post['value'];
		} else {
			$values['credit'] = $post['value'];
		}

		if($post['con'] == 'level' ){
			$levels = $sms->getLevelList();
			foreach($levels as $level){
				if(isset($post['value-'.$level->id]) && $post['value-'.$level->id] != ''){
					$values['con_id'] = $level->id;
					$fees_id = do_insert_obj($values, 'school_fees', $sms->database, $sms->ip);
					if(!$fees_id){
						$result = false;
					} else {
							// set payments schedule
						$obj = $sms->getAnyObjById($post['con'], $level->id);
						$dates = $obj->getDates($year);
						$i = 0;
						foreach($dates as $date){
							$date_ins = new stdClass();
							$date_ins->value = $post['payments'][$i] != '' ? $post['payments'][$i] : '0';
							$date_ins->term = $date->id;
							$date_ins->fees_id = $fees_id;
							$date_ins->year = $_SESSION['year'];
							$date_ins->con = $post['con'];
							$date_ins->con_id = $level->id;
							do_insert_obj($date_ins, 'school_fees_payments', $sms->database, $sms->ip);
							$i++;
						}
					}						
				}
			}
		} else { // profil, BUS fees; books
			$values['con_id'] = $post['con_id'];
			$fees_id = do_insert_obj($values, 'school_fees', $sms->database, $sms->ip);
			if(!$fees_id){
				$result = false;
			} else {
					// set payments schedule
				if($post['con'] == 'bus' || $post['con'] == 'books'){
					$schoolFees = new SchoolFees($sms);
					$dates = $schoolFees->getDates($year);
				} else {
					$obj = $sms->getAnyObjById($post['con'], $post['con_id']);
					$dates = $obj->getDates($year);
				}
				$i = 0;
				foreach($dates as $date){
					$date_ins = new stdClass();
					$date_ins->value = $post['payments'][$i] != '' ? $post['payments'][$i] : '0';
					$date_ins->term = $date->id;
					$date_ins->fees_id = $fees_id;
					$date_ins->year = $_SESSION['year'];
					$date_ins->con = $post['con'];
					$date_ins->con_id = $post['con_id'];
					do_insert_obj($date_ins, 'school_fees_payments', $sms->database, $sms->ip);
					$i++;
				}
			}						
		}
		
		if($result!=false){
			$answer['error'] = "";
			$row = new Layout($values);
			$row->id = $fees_id;
			$row->value = $values['debit'] !=0 ? $values['debit'] : ($values['credit'] * -1);
			$row->main_code = Accounts::fillZero('main', $values['main_code']);
			$row->sub_code = Accounts::fillZero('sub', $values['sub_code']);
			$row->currency_opts = Currency::getOptions($values['currency']);
			$row->sms_id = $sms->id;
			$row->template = "modules/fees/templates/level_fees_rows.tpl";
			$answer['tr'] = $row->_print();
		} else {
			global $lang;
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function saveFees($con, $con_id, $post){
		global $sms, $lang;
		$result = true;
		for($i=0; $i<count($post['id']); $i++){
			$row = array();
			$row['title'] = $post['title'][$i];
			$row['debit'] = $post['value'][$i];
			$row['currency'] = $post['currency'][$i];
			$row['main_code'] = Accounts::removeZero( $post['main_code'][$i]);
			$row['sub_code'] = $post['sub_code'][$i];
			if(!do_update_obj($row, "id=".$post['id'][$i], 'school_fees', $sms->database, $sms->ip)){
				$result = false;
				
			} 
		}
		if($result!=false){
			$answer['error'] = "";
		} else {
			$answer['error'] = $lang['error_updating'];
		}
		return $answer;
	}

	static function deleteFees($id){
		global $lang, $sms, $safems;
		if(do_query_edit("DELETE FROM school_fees WHERE id=$id", $sms->database, $sms->ip)){
			do_query_edit("DELETE FROM school_fees_payments WHERE fees_id=$id AND con='level'", $sms->database, $sms->ip);
			do_query_edit("DELETE FROM school_fees WHERE fees_id=$id", $safems->database, $safems->ip);
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function addPayment($post, $recipt=true){
		global $sms, $safems;
		$ccid = $sms->getCC();
		$result = true;
		if($post['value'] > 0){
			$last_recept= '';
			$student = new Students($post['std_id'], $sms);	
			$year = $post['year'];
			$paid = $post['value'];		
				// Generate Student payment table if not exists
			if( do_query_obj("SELECT * FROM school_fees WHERE std_id=$student->id AND year=$year AND cc=$ccid LIMIT 1", $safems->database, $safems->ip) == false){
				$student->generatePayments($post['year']);
			}
			
				// case one fees
			$sql = "SELECT * FROM school_fees WHERE std_id=$student->id AND year=$year AND cc=$ccid";
			if($post['rel']!=''){
				$applied_fees = array();
				$std_fees = $student->getFees($year); 
				foreach($std_fees as $fee){
					if($fee->getMainAccCode() == substr($post['rel'], 0, 2)){
						$applied_fees[] = "fees_id=".$fee->id;
					}
				}
				$sql .= " AND (".implode(" OR ", $applied_fees).")"; 
			} 
			
				// specified term
			if(isset($post['dates']) && $post['dates'] != ''){
				$sql .= " AND date_id=".$post['dates'];
			}
			$sql .= " ORDER BY due_date ASC";
				// query selected fees
			$fs = do_query_array($sql, $safems->database, $safems->ip);
			foreach($fs as $f){
				if($paid > 0 && $f->value > $f->paid){
					$due = $f->value - $f->paid;
					if($paid <= $due){
						$v = $paid;
					} else {
						$v = $due;
					}
					$paid -= $v;
					$update = array(
						'paid'=> ($f->paid +$v) ,
						'paid_date'=>$post['date']
					);
	
					if(do_update_obj($update, "id=$f->id", 'school_fees', $safems->database, $safems->ip) != false){
						if($recipt!=false){
							$rec = Fees::getIngoingFromPayment($student, new Fees($f->fees_id, $sms), $f->date_id, $year, $v, $post['type'], $post['currency'], dateToUnix($post['date']), ($post['type']=='visa'? $post['bank'] : '')).'<hr style="border:none; border-top:1px dashed #000000" />';
							$last_recept .= $rec.$rec.'<hr style="page-break-after:always" />';
						}
					} else {
						$result = false;
					}
				}
			}
			
			return $result!= false ? $last_recept : false;
		} else {
			return false;
		}
	}
	
	static function getIngoingFromPayment($student, $fee, $date_id, $year, $value, $type, $cur, $cur_date, $bank=''){
		global $lang;
		$sms = $student->sms;
		$accms = $sms->getAccms();
		$safems = $sms->getSafems();
		$cc = $sms->getCC();
		$now = time();
		$level = $student->getLevel();
		if($date_id!=''){
			$date = do_query_obj("SELECT title FROM school_fees_dates WHERE id=$date_id", $sms->database, $sms->ip);
			$date_title = $date->title.' - ';
		} else {
			$date_title = '';
		}
		$student->getAccount(); // create student account if not exists;

		$ingoing = array();
		$ingoing['year'] = $year;
		$ingoing['from'] = $student->getAccCode();
		$ingoing['notes'] = $lang['student'].': '.$student->getName().' - '.$level->getName().' - '.$fee->title .' - '. $date_title. ' - '. $year.'/'.($year+1) .' - '. $sms->code  ;
		$ingoing['recete_notes'] = $lang['student'].': '.$student->getName().' - '.$level->getName().' - '.$fee->title .' - '. $date_title. ' - '. $year.'/'.($year+1) .' - '. $sms->code ;

		$to_acc = $fee->getAccount();
		$to_full_code = $to_acc->full_code;
		$cur_year = getNowYear();
		if($year > $cur_year->year && substr($to_acc->main_code, 0, 1) == 4){
			$adv_acc_code = '29'.substr($to_acc->main_code, 1, 5);
			$to_full_code = Accounts::fillZero('main', $adv_acc_code).Accounts::fillZero('sub', $to_acc->sub_code);
		}
		$ingoing['to'] = $to_full_code;
		$ingoing['type'] = $type;
		$ingoing['value'] = $value;
		$ingoing['currency'] = $cur;
		$ingoing['ccid'] = $sms->getCC();
		$ingoing['date'] = $cur_date;
		$ingoing['bank'] = $bank;
		$recete = Ingoing::addNewPayment($ingoing);
		return $recete->getRecete();
	}
	
	static function getPaymentsLayout($con, $con_id){  
		global $lang, $sms;	
		$year = $_SESSION['year'];
		$layout = new stdClass();
		$table = new stdClass();
		$layout->sms_id = $sms->id;
		$schoolFees = new SchoolFees($sms);
		if($con==""){		
			$dates = $schoolFees->getDates($year);
			$obj = $sms;
			$fees = $obj->getFees($year);
		} elseif( $con == "books"){
			$dates = $schoolFees->getDates($year);
			$obj = $sms;
			$bookFees = new BookFees($sms);
			$fees = $bookFees->getLevelFees($con_id, $year);
		} elseif( $con == "bus"){
			$dates = $schoolFees->getDates($year);
			$obj = new groupRoutes($con_id, $sms->getBusms());
			$busFees = new BusFees($sms);
			$fees = $busFees->getFees($con_id, $year);
		} else {
			$obj = $sms->getAnyObjById($con, $con_id);
			$dates = $obj->getDates($year);
			$fees = $obj->getFees($year);
		}
		
		if($dates != false && count($dates) > 0){
			// Thead
			$layout->payments_ths = '';
			foreach($dates as $d){
				$layout->payments_ths .= write_html('th', '', $d->title);
			}

			$date_currency = array();
			// fees division table
			$layout->payments_rows = '';
			if($fees != false && count($fees) > 0){
				foreach($fees as $fee){
					$tds = array();
					// Calc discount
					$discount = 0;
					$profil = false;
					if($con == 'student'){
						$profil = $obj->getProfil();
						if($profil != false && $fee->discount == '1'){
							if($fee->con == 'bus'){
								$discount = Profils::calcDiscount($fee->debit , $profil->bus_discount);
							} elseif($fee->con == 'books'){
								$discount = Profils::calcDiscount($fee->debit, $profil->lib_discount);
							} else {
								$discount = Profils::calcDiscount($fee->debit, $profil->discount, $profil->exclude) ;
							}
						} 
					}
					$fees_total = $fee->debit - $discount;
					krsort($dates);
					foreach($dates as $key=>$d){
						$pays = do_query_obj("SELECT * FROM school_fees_payments WHERE year=$year AND con='$con' AND con_id=$con_id AND term=$d->id AND fees_id=$fee->id", $sms->database, $sms->ip);
						if($pays == false){
							$pays = do_query_obj("SELECT * FROM school_fees_payments WHERE year=$year AND con='$fee->con' AND con_id=$fee->con_id AND term=$d->id AND fees_id=$fee->id", $sms->database, $sms->ip);
						}
						if(isset($pays->value)){
							$value = $pays->value;
						} else {
							$value = 0;
						}
						$dates_value[$key] = array('id'=>$d->id, 'value'=>$value);
					}
					ksort($dates_value);
					foreach($dates_value as $key=>$date){
						$tds[]= write_html('td', '', '<input type="text" name="value-'.$fee->id.'-'.$date['id'].'" value="'.$date['value'].'" />');
					}
					$layout->payments_rows .= write_html('tr', '', 
						 write_html('td', 'class"=lab"', $fee->title).
						 implode('', $tds).
						 write_html('td', 'class"=lab"', $fees_total. ' '. $fee->currency)
					);
				}
			} 
				// Totals
			$layout->payments_tfoot = '';
			foreach($date_currency as $cur){
				$tot = 0;
				$total_tds = '';
				foreach($dates as $d){
					$val = isset($d->{'total-'.$cur}) ? $d->{'total-'.$cur} : 0;
					$tot +=  $val;
					$total_tds .= write_html('th', '', $val);
				}
				$total_tds .= write_html('th', '', $tot);
				$layout->payments_tfoot .= write_html('tr', '', 
					write_html('th', '', $lang['total'].': '.$cur).
					$total_tds
				);
			}
			
				
			$layout->dates_table = Fees::loadDatesLayout($obj);
		
			return write_html('form', '', fillTemplate("modules/fees/templates/payments_layout.tpl", $layout));
		} else {
			/*$dates = new Layout();
			$dates->template = "modules/fees/templates/payments_dates_table.tpl";
			$dates->sms_id = $sms->id;
			$dates->dates_rows = '';
			for($i=0; $i<4; $i++){
				$dates->dates_rows .= fillTemplate("modules/fees/templates/payments_dates_rows.tpl", array());
			}*/
			return write_html('form', '', 
				write_html('h2', 'class="title"', $lang['times']).
				Fees::loadDatesLayout($sms)
			);
		}

	}
	
	static function loadDatesLayout($obj){
		global $sms;
		$year = $_SESSION['year'];
		$table = new Layout();
		$table->template = "modules/fees/templates/payments_dates_table.tpl";
		if(get_class($obj) == 'Students'){
			$table->con = 'student';
			$table->con_id = $obj->id;
			$table->sms_id=$obj->sms->id;
			$dates = $obj->getDates($year);
		} elseif(get_class($obj) == 'Levels'){
			$table->con = 'level';
			$table->con_id = $obj->id;
			$table->sms_id=$obj->sms->id;
			$dates = $obj->getDates($year);
		} else {
			$table->con = '';
			$table->con_id = '';
			$table->sms_id=$sms->id;
			$schoolFess = new SchoolFees($sms);
			$dates = $schoolFess->getDates($year);
		}
		$table->dates_rows = '';
		foreach($dates as $d){
			$row = new Layout($d);
			$row->template = "modules/fees/templates/payments_dates_rows.tpl";
			$row->from = unixToDate($d->from);
			$row->limit = unixToDate($d->limit);
			$table->dates_rows .= $row->_print();
		}
		return $table->_print();	
	}
	
	static function SavePaymentsValues($post, $toall= false){
		global $sms;
		$result = false;
		$year = $_SESSION['year'];
		$con= $post['con'];
		$con_id= $post['con_id'];
		$obj = $sms->getAnyObjById($con, $con_id);	
		if($con == 'bus' || $con =='books'){
			$schooFees = new SchoolFees($sms);
			$dates =$schooFees->getDates($year);
			if($con == 'bus'){
				$busFees = new BusFees($sms);
				$fees = $busFees->getFees($con_id,  $year);
			} else{
				$bookFees = new BookFees($sms);
				$fees = $bookFees->getLevelFees($con_id, $year);
			}
		} else {
			$dates = $obj->getDates($year);
			$fees = $obj->getFees($year);
		}
		do_delete_obj("con='$con' AND con_id=$con_id AND year=".$_SESSION['year'], 'school_fees_payments', $sms->database, $sms->ip);
		foreach($fees as $fee){
			foreach($dates as $date){
				$field = 'value-'.$fee->id.'-'.$date->id;
				if(isset($post[$field]) && $post[$field] != ''){
					$values = array(
						'con'=> $con,
						'con_id'=> $con_id,
						'fees_id'=>$fee->id,
						'term'=>$date->id,
						'year'=>$_SESSION['year'],
						'value'=> $post[$field]
					);
					if( !do_insert_obj($values, 'school_fees_payments', $sms->database, $sms->ip)){
						$result = false;
					}
				}
			}
		}
		if($result!=false){
			$answer['error'] = "";
		} else {
			if($con == 'student'){
				$student = new Students($con_id, $sms);
				$student->generatePayments();
			}
			global $lang;
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
						
	}
	
	static function SavePaymentsDate($post){
		global $sms;
		$result = true;
	//	print_r($post); exit;
		if(isset($post['id'])){
			for($i=0; $i<count($post['id']); $i++){
				if($post['from'][$i] != ''){
					if($post['id'][$i] != ''){
						$values = array(
							'title'=> $post['title'][$i],
							'from'=> dateToUnix($post['from'][$i]),
							'limit'=> dateToUnix($post['limit'][$i])
						); 
						if(!do_update_obj($values, 'id='.$post['id'][$i], 'school_fees_dates',  $sms->database, $sms->ip)){
							$result = false;
						}
					} else{
						if(isset($post['title'][$i]) && $post['title'][$i] !=''){
							$values = array(
								'title'=> $post['title'][$i],
								'con'=> $post['con'],
								'con_id'=> $post['con_id'],
								'from'=> dateToUnix($post['from'][$i]),
								'limit'=> dateToUnix($post['limit'][$i]),
								'year'=> $_SESSION['year']
							); 
							if(!do_insert_obj($values, 'school_fees_dates', $sms->database, $sms->ip)){
								$result = false;
							}
						}
						
					}
				}
			}
		}
		return $result;
	}
	
	static function do_refund($post){
		global $sms, $safems, $lang;
		$std_id = $post['std_id'];
		$sms_ccid = $post['cc'];
		$student = new Students($std_id, $sms);
		$year = $_SESSION['year'];
		$out = '';
		$fees_arr = array();
		foreach($post['refund'] as $refund){
			$r = explode('-', $refund);
			$fees_id = $r[0];
			$fee = new Fees($fees_id, $sms);
			$date_id = $r[1];
			$date = do_query_obj("SELECT title FROM school_fees_dates WHERE id=$date_id", $sms->database, $sms->ip);

			$where = "std_id=$std_id AND year=$year AND cc=$sms_ccid AND fees_id=$fees_id AND date_id=$date_id";
			// get the paid value
			$p = do_query_obj("SELECT paid, currency FROM school_fees WHERE $where", $safems->database, $safems->ip);
			
			// Outgoing
			$outgoing = array();
			$outgoing['type'] = 'cash';
			$outgoing['value'] = $p->paid;
			$outgoing['currency'] = $p->currency;
			$outgoing['ccid'] = $sms_ccid;

				// from
			$now = time();
			$from_acc = $fee->getAccount();
			$cur_year = do_query_obj("SELECT year FROM years WHERE begin_date<=$now AND end_date>=$now", $safems->database, $safems->ip);
			if($year > $cur_year->year){
				$adv_acc_code = '29'.substr($from_acc->main_code, 1, 5);
				$from_acc = new Accounts(Accounts::fillZero('main', $adv_acc_code).Accounts::fillZero('sub', $from_acc->sub_code));
			}
			$outgoing['from'] = $from_acc->full_code;	
				// To		
			$outgoing['to'] = $student->getAccCode();
			//$outgoing['to_main'] = '151'.$sms_ccid;
			
			$outgoing['notes'] = $lang['refund'].': '.$fee->title .' - '. $date->title. ' - '. $year.'/'.($year+1) ;
			$outgoing['recete_notes'] = $student->getName().' - '.$fee->title .' - '. $date->title. ' - '. $year.'/'.($year+1) ;
			
			$recete = Outgoing::addNewPayment($outgoing);
			$out .= $recete->getRecete();
			
			// clear from student fees table
			$update = array(
				'paid' => 0,
				'paid_date' => 0
			);
			do_update_obj($update, $where, 'school_fees', $safems->database, $safems->ip);
		}
		return $out;
	}
	
	static function getBankSheet($date_id=''){
		global $sms, $safems, $lang;
		$levels = $sms->getLevelList();
		$ccid = $sms->ccid;
		$year = $_SESSION['year'];
		if($date_id == ""){
			$schooFees = new SchoolFees($sms);
			$dates =$schooFees->getDates($year);
			$i=0;
			for($i=0; $i<count($dates); $i++){
				if($dates[$i]->limit<=time() && $dates[$i+1]->limit > time()){
					$date = $dates[$i+1];
					$date_id= $date->id;
					break;
				}
			}
		} else {
			$date = do_query_obj("SELECT * FROM school_fees_dates WHERE id=$date_id", $sms->database, $sms->ip);
		}
		if($date_id != ''){
			$students =array();
			foreach($levels as $level){
				$students = array_merge($students, $level->getStudents());
			}
			$trs = array();
			foreach($students as $student){
				$row = new Layout($student);
				$row->template = 'modules/fees/templates/bank_sheet_row.tpl';
				$row->acc_code = $student->getAccCode();
				$row->student_name = $student->getName();
				$dues = do_query_array("SELECT SUM(value-paid) AS due, currency FROM school_fees WHERE std_id=$student->id AND cc=$ccid AND year=$year AND date_id=$date_id GROUP BY currency", $safems->database, $safems->ip);
				if($dues != false && count($dues) > 0){
					foreach($dues as $due){
						if($due->due > 0){
							$row->due = $due->due;
							$row->currency = $due->currency;
							$trs[] = $row->_print();
						}
					}
				}
			}
			$table = new Layout($date);
			$table->limit = unixToDate($date->limit);
			$table->from = unixToDate($date->from);
			$table->template = 'modules/fees/templates/bank_sheet_table.tpl';
			$table->trs = implode('', $trs);
			return $table->_print();
		} else {
			return write_error($lang['no_dates_defined']);
		}
	}

}
?>