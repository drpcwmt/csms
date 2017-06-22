<?php
/** Refund school fees
*
*/
class RefundSchoolFees {

	public function __construct($sms){
		$this->sms = $sms;
	}
	
	public function loadMainLayout(){
		$layout = new Layout($this->sms);
		$layout->template = 'modules/outgoing/templates/refund.tpl';
		$layout->sms_id = $this->sms->id;
		$layout->school_name = $this->sms->getName();
		$year = getNowYear();
		$layout->year_opts = write_html('option', 'value="'.$year->year.'"', $year->year.'/'.($year->year+1)). 
			write_html('option', 'value="'.($year->year+1).'"', ($year->year+1).'/'.($year->year+2));
		return $layout->_print();
	}
	
	public function loadRefund($std_id, $year){
		$layout = new Layout();
		$layout->template = 'modules/outgoing/templates/student_refund_table.tpl';
		$layout->refund_fees_table = $this->loadRefundFees($std_id, $year);
		$layout->refund_items_table = $this->loadRefundItem($std_id, $year);
		return $layout->_print();
	}

	public function loadRefundFees($std_id, $year){
		$sms = $this->sms;
		$student = new Students($std_id, $sms);
		$safems = $sms->getSafems();
		$table = new layout($this);
		$table->template = 'modules/outgoing/templates/refund_table.tpl';
		$table->cc = $sms->ccid;
		$table->trs = '';

		$rows = do_query_array("SELECT * FROM school_fees WHERE std_id=$student->id AND year=$year AND paid>0 AND cc=$sms->ccid", $safems->database, $safems->ip);
		$accs = array();
		foreach($rows as $r){
			$fees = new Fees($r->fees_id);
			$acc = $fees->getAccount();	
			if(!array_key_exists($acc->full_code, $accs)){		
				$acc->paid = $r->paid;
				$acc->currency = $r->currency;
				$accs[$acc->full_code] = $acc;
			} else {
				$accs[$acc->full_code]->paid +=$r->paid;
			}
			
		}
		foreach($accs as $code=> $acc){
			$r = new Layout($acc);
			$r->template = 'modules/outgoing/templates/refund_table_row.tpl';
			$r->std_id = $student->id;
			$table->trs .= $r->_print();
		}

		return $table->_print();
	}
	
	public function loadRefundItem($std_id, $year){
		$student = new Students($std_id, $this->sms);
		$safems = $this->sms->getSafems();
		$safe_acc = $safems->getAccount();
		$sch_code = $this->sms->getAccCode();
		$accs = array(
			new Accounts($safems->getSettings('admission_acc')),
			new Accounts($safems->getSettings('admission_acc_adv')),
			new Accounts($safems->getSettings('insur_book_acc')),
			new Accounts($safems->getSettings('insur_locker_acc'))
		);
		$trs = array();
		foreach($accs as $acc){
			$debits = do_query_array("SELECT SUM(value) AS val, currency, date FROM ingoing WHERE from_main_code=$sch_code AND from_sub_code=$std_id 
				AND to_main_code=$acc->main_code AND to_sub_code=$acc->sub_code GROUP BY currency", $safems->database, $safems->ip);
			if($debits!=false && count($debits)>0){
				foreach($debits as $debit){
					$credit = do_query_obj("SELECT SUM(value) AS val FROM outgoing WHERE from_main_code=$acc->main_code AND from_sub_code=$acc->sub_code AND to_main_code=$sch_code AND to_sub_code=$std_id  AND currency='$debit->currency'", $safems->database, $safems->ip);
					$refund_amount = $debit->val - ($credit!=false ? $credit->val : 0);
					if($refund_amount > 0){
						$trs[] = write_html('tr', '',
							write_html('td', 'width="20" class="unprintable"', 
								write_html('button', 'class="circle_button ui-state-default hoverable" action="refundItem" main_code="'.$acc->main_code.'" sub_code="'.$acc->sub_code.'"', write_icon('arrowreturnthick-1-w'))
							).
							write_html('td', 'align="center"', $refund_amount).
							write_html('td', 'align="center"', $debit->currency).
							write_html('td', '', $acc->title).
							write_html('td', 'align="center"', unixToDate($debit->date))
						);
					}
				}
			}
		}
		
		// IG refund Services
		$level = $student->getLevel();
		if($level!=false && $level->ig_mode == '1'){
			$refunds = do_query_array("SELECT * FROM refund_services WHERE std_id=$std_id AND refunded=0 AND year=$year", $safems->database, $safems->ip);
			foreach($refunds as $refund){
				$service = new ServicesIG($refund->services, $this->sms, $refund->year);
				$trs[] = write_html('tr', '',
					write_html('td', 'width="20" class="unprintable"', 
						write_html('button', 'class="circle_button ui-state-default hoverable" action="refundService" service_id="'.$refund->services.'" std_id="'.$refund->std_id.'" exam="'.$refund->exam.'"', write_icon('arrowreturnthick-1-w'))
					).
					write_html('td', 'align="center"', ($refund->fees_refund +$refund->reg_refund)).
					write_html('td', 'align="center"', $this->sms->getSettings('def_currency')).
					write_html('td', '', $service->getName().'-'.$service->lvl.'-'.$refund->exam.'-'.$refund->type.'-'.$refund->year).				
					write_html('td', 'align="center"', '')
				);	
			}
		}
		$layout = new Layout();
		$layout->template = 'modules/outgoing/templates/refund_items_table.tpl';
		$layout->trs = implode('', $trs);
		return $layout->_print();
	}

	static function doRefundItem($post){
		global $sms, $safems, $lang;
		$std_id = $post['std_id'];
		$student = new Students($std_id, $sms);
		$acc = getAccount( $post['main_code'],  $post['sub_code']);

		// office_fees_refund
		if($post['discount'] > 0){
			$refund_safe = array();
			$refund_safe['type'] = 'cash';
			$refund_safe['value'] = $post['discount'];
			$refund_safe['date'] = dateToUnix($post['date']);
			$refund_safe['currency'] = $post['currency'];
			$refund_safe['ccid'] = $post['ccid'];
			$refund_safe['from'] = Accounts::fillZero('main', $post['main_code']).	Accounts::fillZero('sub', $post['sub_code']);
			$refund_safe['to'] = $safems->getSettings('this_acc_code');
			$refund_safe['notes'] = $lang['office_fees_refund'].': '.$acc->title.' - '.$student->getName().' - '. $sms->code.' - '. $post['year'];
			Outgoing::addNewPayment($refund_safe);
	
			$office_fees_income = array();
			$office_fees_income['type'] = 'cash';
			$office_fees_income['value'] = $post['discount'];
			$office_fees_income['date'] = dateToUnix($post['date']);
			$office_fees_income['currency'] = $post['currency'];
			$office_fees_income['ccid'] = $post['ccid'];
			$office_fees_income['to'] = $safems->getSettings('office_fees_refund_acc');
			$office_fees_income['from'] = $safems->getSettings('this_acc_code');
			$office_fees_income['notes'] = $lang['office_fees_refund'].': '.$acc->title.' - '.$student->getName().' - '. $sms->code.' - '. $post['year'];
			Ingoing::addNewPayment($office_fees_income);
		}

		$outgoing = array();
		$outgoing['type'] = 'cash';
		$outgoing['value'] = $post['refund'];
		$outgoing['date'] = dateToUnix($post['date']);
		$outgoing['currency'] = $post['currency'];
		$outgoing['ccid'] = $post['ccid'];
		$outgoing['from'] = Accounts::fillZero('main', $post['main_code']).	Accounts::fillZero('sub', $post['sub_code']);
		$outgoing['to'] = $student->getAccCode();
		$outgoing['notes'] = $lang['refund'].': '.$acc->title .' - '.$student->getName().' - '. $sms->code .' - '. $post['year'] ;
		
		// reduce school fee paid in the safems
		if(!in_array($acc->full_code, array(
			$safems->getSettings('admission_acc'),
			$safems->getSettings('admission_acc_adv'),
			$safems->getSettings('insur_book_acc'),
			$safems->getSettings('insur_locker_acc')
		))){	
			$fees = $student->getFees($post['year']);
			$refund_fees = array();
			foreach($fees as $f){
				$adv_main_code =  '29'.substr($f->main_code, 1, 5);
				$adv_post_code =  '29'.substr($post['main_code'], 1, 5);
				if( $post['main_code'] == $f->main_code && $f->sub_code == $post['sub_code']&& $f->currency==$post['currency']){
					$refund_fees[] = $f->id;
				} 
			}
			$value = $post['refund'] + $post['discount'];
			foreach($refund_fees as $f_id){
				$fee = do_query_array("SELECT id, paid FROM school_fees WHERE std_id=$student->id AND year=".$post['year']." AND cc=$sms->ccid AND fees_id=$f_id AND paid>0 AND currency='".$post['currency']."' ORDER BY due_date DESC", $safems->database, $safems->ip);
				foreach($fee as $f){
					if( $value > 0){
						if($value > $f->paid){
							$v = $f->paid;
						} else {
							$v = $value;
						}
						$value = $value - $v;
						$paid = $f->paid - $v;
						do_update_obj(array('paid'=>$paid), "id=$f->id", 'school_fees', $safems->database, $safems->ip);
					}
				}
			}
		} 
		return Outgoing::addNewPayment($outgoing);
	}

	static function doRefundService($post){
		global $sms, $safems, $lang;
		$error = '';
		$result = true;
		$std_id = $post['std_id'];
		$year = $post['year'];
		$service_id = $post['service_id'];
		$service = new ServicesIg($service_id, $sms, $year);
		$exam = $post['exam'];
		$student = new Students($std_id, $sms);
		$level = $student->getLevel();
		$update_refund = array();
		$refund = do_query_obj("SELECT * FROM refund_services WHERE std_id=$std_id AND year=$year AND services=$service_id AND exam='$exam'", $safems->database, $safems->ip);
		if($refund!= false){
			$recete = '';
			$outgoing = array();
			$outgoing['type'] = 'cash';
			$outgoing['date'] = dateToUnix($post['date']);
			$outgoing['currency'] = $post['currency'];
			$outgoing['ccid'] = $post['ccid'];			
			$outgoing['notes'] = $post['note'] ;
			$notes = $lang['refund'].': '.$student->getName().' - '.($level!=false ?$level->getName() :'').' - '. $service->getName().' - '. $exam. ' - %s - '. $year.'/'.($year+1) .' - '. $sms->code  ;
		
			// Registration
			if($refund->reg_refund >0){
				//$BC_acc = getAccount(BC_account);	
				$outgoing['from'] = BC_account;
				$outgoing['value'] =  $refund->reg_refund;
				$outgoing['notes'] = sprintf($notes, $lang['registration_fees']);
				$outgoing_reg = Outgoing::addNewPayment($outgoing);
				if($outgoing_reg != false){
					$recete .= $outgoing_reg->getRecete();
				} else {
					$result = false;
					$error = "Can't print recete";
				}
			}
			
			// School fess
			if($refund->fees_refund >0){
				$cur_year = getNowYear();
				$SF_acc = getAccount(SF_account);
				
				// office_fees_refund
				if($refund->fees_paid  - $refund->fees_refund  > 0){			
					$refund_safe = array();
					$refund_safe['type'] = 'cash';
					$refund_safe['value'] = $refund->fees_paid  - $refund->fees_refund;
					$refund_safe['date'] = dateToUnix($post['date']);
					$refund_safe['currency'] = $post['currency'];
					$refund_safe['ccid'] = $post['ccid'];
					if($year > $cur_year->year ){
						$adv_acc_code = '29'.substr($SF_acc->main_code, 1, 5);
						$refund_safe['from'] =  Accounts::fillZero('main', $adv_acc_code).Accounts::fillZero('sub', $SF_acc->sub_code);
					} else {
						$refund_safe['from'] = SF_account;
					}
					$refund_safe['to'] = $safems->getSettings('this_acc_code');
					$refund_safe['notes'] = $lang['office_fees_refund'].': '. $service->getName().' - '.$student->getName().' - '. $refund->exam.' - '. $sms->code.' - '. $sms->code.' - '. $post['year'];
					Outgoing::addNewPayment($refund_safe);
			
					$office_fees_income = array();
					$office_fees_income['type'] = 'cash';
					$office_fees_income['value'] = $refund->fees_paid  - $refund->fees_refund;
					$office_fees_income['date'] = dateToUnix($post['date']);
					$office_fees_income['currency'] = $post['currency'];
					$office_fees_income['ccid'] = $post['ccid'];
					$office_fees_income['to'] = $safems->getSettings('office_fees_refund_acc');
					$office_fees_income['from'] = $safems->getSettings('this_acc_code');
					$office_fees_income['notes'] = $lang['office_fees_refund'].': '. $service->getName().' - '.$student->getName().' - '. $refund->exam.' - '. $post['year'];
					Ingoing::addNewPayment($office_fees_income);
				}

				if($year > $cur_year->year ){
					$adv_acc_code = '29'.substr($SF_acc->main_code, 1, 5);
					$outgoing['from'] =  Accounts::fillZero('main', $adv_acc_code).	Accounts::fillZero('sub', $SF_acc->sub_code);
				} else {
					$outgoing['from'] = SF_account;
				}
				$outgoing['to'] = $student->getAccCode();
				$outgoing['value'] =  $refund->fees_refund;
				$outgoing['notes'] = sprintf($notes, $lang['school_fees']);
				$outgoing_fees = Outgoing::addNewPayment($outgoing);
				if($outgoing_fees != false){
					$recete .= $outgoing_fees->getRecete();
				} else {
					$result = false;
					$error = "Can't print recete";
				}
			}
			
			// Update refund service table
			if($result){
				do_update_obj(array('refunded'=>1), "id=$refund->id", 'refund_services', $safems->database, $safems->ip);
			}
		}
		
		return array(
			'error' => $error,
			'recete'=>$recete.$recete
		);

	}
		
	/*static function doRefundFees($post){
		global $sms, $safems, $lang;
		$std_id = $post['std_id'];
		$sms_ccid = $post['ccid'];
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
	}*/

}
?>