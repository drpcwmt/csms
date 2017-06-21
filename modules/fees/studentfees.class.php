<?php
/** Student Fees
*
*/
class StudentFees {
	
	public function __construct($student){
		$this->student = $student;
		$this->sms = $student->sms;
		$this->safems = $this->sms->getSafems();
		$this->busms = $this->sms->getBusms();
	}

	public function loadFeesLayout($year=''){
		global $lang, $prvlg, $this_system;
		if($year == '') { $year = $_SESSION['year'];}
		$student = $this->student;
		$sms = $this->sms;
		$busms = $this->busms;
		$safems = $this->safems;
		$level = $this->student->getLevel();
		$layout = new Layout();
		$layout->id = $student->id;
		$layout->sms_id = $sms->id;
		$layout->student_name = $student->getName();
		$toolbox = new Layout($layout);
		$toolbox->sms_id = $this->sms->ccid;
		$toolbox->template = 'modules/fees/templates/student_fees_toolbox.tpl';
		$toolbox->prvlg_edit_fee = $prvlg->_chk('edit_std_fees') ? '' : 'hidden';
		$layout->toolbox = $toolbox->_print();
		if($student->getClass() != false){
			if( do_query_obj("SELECT * FROM school_fees WHERE std_id=$student->id AND year=$year AND cc=$sms->ccid LIMIT 1", $safems->database, $safems->ip) == false){
				$student->generatePayments($year);
			}
			$layout->level_name = $level!= false ? $level->getName():'';
			$layout->year_name = $year.' / '. ($year +1);
			$layout->currency = $sms->getSettings('def_currency');
			$notes = do_query_array("SELECT * FROM students_notes WHERE std_id=$student->id AND sms_id=$sms->ccid", $safems->database, $safems->ip);
			
			$layout->notes = '';
			if($notes != false && count($notes) > 0){
				foreach($notes as $note){
					$layout->notes .= write_html('fieldset', ' style="background-color:#3F0; font-weight:bold"',
						($_SESSION['user_id'] == $note->user_id || $_SESSION['group'] == 'superadmin' ?
							write_html('span', 'class="delete_note mini_circle_button ui-state-default hoverable rev_float" module="fees" action="deleteFeesNote" note_id="'.$note->id.'" sms_id="'.$sms->ccid.'"', write_icon('trash'))
						:'' ).
						$note->notes
						
					);
				}
			}
			
			if($prvlg->_chk('read_std_fees')){
				//$studentFees = new StudentFees($student);
					// Paid tabs summary
				$fess_summary = new Layout();
				$fess_summary->template = "modules/students/templates/student_fees_summary_table.tpl";
				$fess_summary->summary_tbody = $this->getPaidSummary($year);
				$fess_summary->incomes_table = $this->getIncomesTable($year);

				$layout->fees_summary = $fess_summary->_print();
					// paid list
				$layout->ingoing_table = $this->getPaidTable($year);
								
					// Payment tabs
				$layout->due_table = $this->getDueTable($year);
				
					// Profils
				$std_prof = $student->getProfil();
				$profils = Profils::getList();
				$profs = array('0'=> $lang['normal']);
				foreach($profils as $prof){
					$profs[$prof->id] = $prof->title;
				}
					// Load payment dates
				//$layout->payment_table = Fees$this->loadDatesLayout();
					// Privileges
				$layout->prvlg_edit_profil = $prvlg->_chk('edit_std_profil') ? '' : 'hidden';
				$layout->prvlg_edit_fee = $prvlg->_chk('edit_std_fees') ? '' : 'hidden';
				$layout->profils_opts =  write_select_options( $profs, ($std_prof != false ? $std_prof->id : ''), false);
				if($sms->getSettings('ig_mode') == '1'){
					$layout->ig_service_tab = write_html('li', '',
						write_html('a', 'href="#ig_reg_div"', $lang['registration_fees'])
					).
					write_html('li', '',
						write_html('a', 'href="index.php?module=fees&splitting&std_id='.$student->id.'&sms_id='.$sms->ccid.'"', $lang['split'])
					).
					write_html('li', '',
						write_html('a', 'href="index.php?module=fees&remarqing&std_id='.$student->id.'&sms_id='.$sms->ccid.'"', $lang['remarking'])
					);	
					$layout->ig_service_div = write_html('div', 'id="ig_reg_div"',
						$this->loadStudentRegistrationTable( $year)
					);
					if($this_system->type == 'sms'){
						$layout->ig_service_tab .= write_html('li', '',
							write_html('a', 'href="#ig_service_div"', $lang['materials'])
						);
						$layout->ig_service_div .= write_html('div', 'id="ig_service_div"',
							$this->loadStudentServicesFeesTable($year)
						);
					}
				}
				$layout->template = "modules/students/templates/student_fees_layout.tpl";
			} else {
				$layout->template = "modules/students/templates/student_fees_summary_table.tpl";
				$layout->summary_tbody = $this->getPaidSummary($year);
				$layout->income_fieldset = 'hidden';
			}
			if($prvlg->_chk('edit_std_fees')){
				$layout->fees_editable = 'hidden';
			}
			return $layout->_print();
		} else {
			$toolbox->prvlg_edit_fee = 'hidden';
			return $toolbox->_print().
			write_html('h2', 'class="title"', $student->getName()).
			write_html('h3', '', unixToDate($student->quit_date)).
			$layout->ingoing_table = $this->getPaidTable($year).
			write_error($lang['error_not_item_found']);
		}
	}

	public function getPaidSummary($year){
		global $lang, $prvlg;
		$student = $this->student;
		$sms = $this->sms;
		$safems = $this->safems;
		$dates = $student->getDates($year);
		$fees = $student->getFees($year);
		$total_dues = array();
		$total_paid = array();
		$trs = array();
		$tfoot= array();
		foreach($dates as $date){
			$sql = "SELECT SUM(value) as value, SUM(paid) AS paid, currency FROM school_fees WHERE year=$year AND std_id=$student->id AND cc=$sms->ccid AND date_id=$date->id GROUP BY currency ORDER by date_id ASC";
			$rows = do_query_array($sql, $safems->database, $safems->ip);
			$first = true;
			foreach($rows as $row){
				$diff = $row->value- $row->paid;
				if(!isset($total_dues[$row->currency])){
					$total_dues[$row->currency] = 0;
					$total_paid[$row->currency] = 0;
				}
				$total_dues[$row->currency] += $row->value;
				$total_paid[$row->currency] += $row->paid;
				$span = $date->from < time() ? 'red_item' :'';
				$trs[] = write_html('tr', '',
					($first ? 
						write_html('td', 'rowspan="'.count($rows).'"', $date->title)
					:'').
					write_html('td', '', $row->currency ).
					write_html('td', '', 
						($prvlg->_chk('read_std_fees_stat')?
							($row->value > 0 ? write_icon('check') : '&nbsp;' )
						: 
							numberToMoney($row->value)
						)
					).
					write_html('td', '', 
						($prvlg->_chk('read_std_fees_stat')?
							($row->paid > 0 ? write_icon('check') : '&nbsp;' )
						: 
							numberToMoney($row->paid)
						)
					).
					write_html('td', '', 
						($prvlg->_chk('read_std_fees_stat')?
							($diff > 0 ? write_icon('check') : '&nbsp;' )
						: 
							write_html('span', 'class="'.$span.'"', numberToMoney($diff))
						)
					)
				);
				$first = false;
			}
			//$interest = SchoolFees::getInterest($date->total, $fee, $profil, $date);
		}

		$first = true;
		foreach($total_dues as $cur=>$value){
			$tfoot[] = write_html('tr', '',
				($first ? 
					write_html('th', 'rowspan="'.count($total_dues).'"', $lang['total'])
				:'').
				write_html('th', '', $cur ).
				write_html('th', '', 
					($prvlg->_chk('read_std_fees_stat')?
						($value > 0 ? write_icon('check') : '&nbsp;' )
					: 
						write_html('span', 'class="'.$span.'"', numberToMoney($value))
					)
				).
				write_html('th', '', 
					($prvlg->_chk('read_std_fees_stat')?
						($total_paid[$cur] > 0 ? write_icon('check') : '&nbsp;' )
					: 
						write_html('span', 'class="'.$span.'"', numberToMoney($total_paid[$cur]))
					)
				).
				write_html('th', '', 
					($prvlg->_chk('read_std_fees_stat')?
						($value - $total_paid[$cur] > 0 ? write_icon('check') : '&nbsp;' )
					: 
						write_html('span', 'class="'.$span.'"', numberToMoney($value - $total_paid[$cur]))
					)
				)
			);
			$first = false;
		}
		return write_html('tbody', '',
			implode('', $trs)
		).
		write_html('tfoot', '',
			implode('', $tfoot)
		);
	}
	
	public function getIncomesTable($year){
		global  $lang, $prvlg;
		$student = $this->student;
		$sms = $this->sms;
		$safems = $this->safems;
		$dates = $student->getDates($year);
		$fees = $student->getFees($year);
		$profil = $student->getProfil();
		$accs = array();
		$paids = array();
		foreach($fees as $f){
			$acc_code = substr($f->main_code, 0, 2);
			if(!isset($accs[$acc_code])){
				$accs[$acc_code] = array();
			}
			if(!isset($accs[$acc_code][$f->currency])){
				$accs[$acc_code][$f->currency] = 0;
			}
			
			$value = $f->debit;
			if($profil != false && $f->discount==1){
				if($f->con == 'bus'){
					$value = $value - Profils::calcDiscount($value , $profil->bus_discount);
				} elseif($f->con == 'books'){
					$value =$value -  Profils::calcDiscount($value , $profil->lib_discount);
				} else {
					$value = $value - Profils::calcDiscount($value , $profil->discount, $profil->exclude);
				}
			}
			$accs[$acc_code][$f->currency] += $value;
			
			$paid = do_query_obj("SELECT SUM(paid) AS paid, currency FROM school_fees WHERE year= $year AND std_id=$student->id AND cc=$sms->ccid AND fees_id=$f->id", $safems->database, $safems->ip);
			if(!isset($paids[$acc_code][$f->currency])){
				$paids[$acc_code][$f->currency] = 0;
			}
			$paids[$acc_code][$f->currency] += $paid->paid;
		}

		$trs = array();
		
		foreach($accs as $code=>$cur_arr){
			$tds = array();
			$first_income = true;
			$account = new Accounts($code);
			foreach($cur_arr as $cur=>$value){
				$span = $paids[$code] == 0 ? 'red_item' : ($paids[$code] < $value ? 'orange_item' : 'blue_item');
				$trs[] = write_html('tr', '',
					($first_income ? 
						write_html('td', 'rowspan="'.count($cur_arr).'"', $account->title)
					: '').
					write_html('td', '', $cur).
					write_html('td', '', 
						($prvlg->_chk('read_std_fees_stat')?
							($value > 0 ? write_icon('check') : '&nbsp;' )
						: 
							numberToMoney($value)
						)
					).
					write_html('td', '', 
						($prvlg->_chk('read_std_fees_stat')?
							($paids[$code][$cur] > 0 ? write_icon('check') : '&nbsp;' )
						: 
							 write_html('span', 'class="'.$span.'"', numberToMoney($paids[$code][$cur]))
						)
					)
				);
				$first_income = false;
			}
			
		}
		$layout = new Layout();
		$layout->incomes_tbody = implode('', $trs);
		$layout->template = "modules/students/templates/student_income_table.tpl";
		return $layout->_print();
	}

	
	public function getDueTable($year){
		global  $lang;
		$student = $this->student;
		$sms = $this->sms;
		$safems = $this->safems;
		$layout = new Layout();
		$dates = $student->getDates($year);	
		$profil = $student->getProfil();
		$fees = $student->getFees($year);
			// the thead
		$layout->payments_ths = '';
		foreach($dates as $date){
			$layout->payments_ths .= write_html('th', 'align="center"', $date->title ." ".write_html('span', 'class="mini"', unixToDate($date->limit)));
		}
		
			// The Tbody
		$layout->payments_tbody = '';
		$date_currency = array();
		foreach($fees as $fee){ 
			$fee_interest = 0;
			$total_fees = 0;
			$total_paid = 0;
			$debit_multipler = $fee->debit > 0 ? 1 : -1;
			$count_dates = 0;
			$dates_value = array();

			// Calc discount
			$discount = 0;
			if($profil != false && $fee->discount == '1'){
				if($fee->con == 'bus'){
					$discount = Profils::calcDiscount($fee->debit , $profil->bus_discount);
				} elseif($fee->con == 'books'){
					$discount = Profils::calcDiscount($fee->debit, $profil->lib_discount);
				} else {
					$discount = Profils::calcDiscount($fee->debit, $profil->discount, $profil->exclude) ;
				}
			} 
			
			// LOOP to create reversed value of dates
			krsort($dates);
			foreach($dates as $key=>$date){	
				$pays = $student->getFeesBydate($fee, $date);							
				//$pays = do_query_obj("SELECT * FROM school_fees_payments WHERE year=$year AND ((con='student' AND con_id=$student->id) OR (con='$fee->con' AND con_id=$fee->con_id)) AND term=$date->id AND fees_id=$fee->id", $sms->database, $sms->ip);
				
				if($pays != false){
					if($pays>=$discount){
						$value = $pays - $discount;
						$discount-= $value;
					} else {
						$value = 0;
						$discount -= $pays;
					}
					if($discount<0){
						$discount=0;
					}
				
					$paid = do_query_obj("SELECT SUM(paid) AS paid FROM school_fees WHERE std_id=$student->id AND cc=$sms->ccid AND year=$year AND fees_id=$fee->id AND date_id=$date->id", $safems->database, $safems->ip);
					$dates_value[$key] = array('value'=>$value, 'paid'=>$paid->paid);
					$total_fees +=  $value;
					$total_paid += $paid->paid;
				} else {
					$dates_value[$key] = array('value'=>'', 'paid'=>'');
				}
			}
				// collecting trs
			$td = array();
			ksort($dates_value);
			foreach($dates_value as $key=>$date){
				$span = $date['paid'] == 0 ? 'red_item' : ( $date['paid'] < $date['value'] ? 'orange_item' : 'blue_item');
				$td[]= write_html('td', '', 
					write_html('span','class="'.$span.'"', numberToMoney($date['value']))
				);
			}
			// total _tr
			$td[]= write_html('td', '',  
				write_html('span','class="'.( $total_paid < $total_fees ? 'red_item' : 'green_item').'"',
					numberToMoney($total_fees)
				)
			).
			write_html('td', '', $fee->currency);
			
			$layout->payments_tbody .= write_html('tr', '', 
				write_html('td', '', $fee->title).
				implode('', $td)
			);
		}
		
		
			// Total Tfoot
		/*$layout->payments_tfoot = '';
		foreach($date_currency as $cur){
			$tot = 0;
			$total_tds = '';
			foreach($dates as $d){
				$val =  isset($d->{'total-'.$cur}) ? $d->{'total-'.$cur} : 0;
				$tot += $val;
				$total_tds .= write_html('th', '', numberToMoney($val));
			}
			$total_tds .= write_html('th', '', numberToMoney($tot));
			$layout->payments_tfoot .= write_html('tr', '', 
				write_html('th', '', $lang['total']).
				$total_tds.
				write_html('th', '', $cur)
			);
		}*/
		
		return fillTemplate("modules/students/templates/student_due_table.tpl", $layout);
	}
	
	// generate table from safems with student payment from ingoing and Outgoing
	public function getPaidTable($year){ // FROM SAFEMS Databases
		global  $sms, $lang;
		$student = $this->student;
		$sms = $this->sms;
		$safems = $this->safems;
		$dates = $student->getDates($year);
		$fees = $student->getFees($year);
		$account = new Accounts($student->getAccCode());
		$school_code = $sms->getSettings('this_main_code');
		$trs = array();
		$totals = array();
		$select_accs =array();
		$byear = time() - 365*24*60*60;
	
		$date = array();
		$dates = $student->getDates($year);
		foreach($dates as $d){
			$date[$d->id] = $d->title;
		}

		$sql = "(SELECT * FROM ingoing WHERE from_main_code='$account->main_code' AND from_sub_code=$account->sub_code AND date>=$byear )
		UNION ALL
		 (SELECT * FROM outgoing WHERE to_main_code='$account->main_code' AND to_sub_code=$account->sub_code AND date>=$byear)
		 ORDER BY date DESC";
		$all = do_query_array($sql, $safems->database, $safems->ip);
		
		foreach($all as $p){
			if(!array_key_exists($p->currency, $totals)){
				$totals[$p->currency] = 0;
			}
			if($p->from_main_code==$account->main_code && $p->from_sub_code==$account->sub_code){
				$totals[$p->currency] += $p->value;
			} else {
				$totals[$p->currency] -= $p->value;
			}
			
			$date = $p->date;
			$maim_acc_code = substr($p->to_main_code,0,5);
			if(!array_key_exists($maim_acc_code, $select_accs)){
				$main_acc = new Accounts($maim_acc_code);
				$select_accs[$maim_acc_code] = $main_acc->title;
			}
			ksort($select_accs);

			$user = new Users( '', $p->user_id, $safems);
			$trs[]= write_html('tr', ' class="'.$maim_acc_code.'"',
				write_html('td', 'style="vertical-align:top"', ($p->from_main_code==$account->main_code && $p->from_sub_code==$account->sub_code ? $p->value.' '.$p->currency : '&nbsp;')).
				write_html('td', '', ($p->to_main_code==$account->main_code && $p->to_sub_code==$account->sub_code? $p->value.' '.$p->currency : '&nbsp;')).
				write_html('td', 'style="vertical-align:top"', write_html('b', '', unixToDate($p->date))).
				write_html('td', 'style="vertical-align:top"', $p->id).
				write_html('td', 'style="vertical-align:top"', $p->type).
				write_html('td', 'class="{sorter:false}"', $p->notes).
				write_html('td', 'style="vertical-align:top"', $user->getRealName())
			);
		}
		
		
		$out = new Layout();
		$out->total_paid = '';
		foreach($totals as $cur=>$value){
			  $out->total_paid .= write_html('span', '', numberToMoney($value).' '.$cur).'<br />';
		}
		$out->template = "modules/fees/templates/student_ingoing_table.tpl";
		$out->trs = implode('', $trs);
		$out->fees_accs_opts = write_select_options($select_accs,'', true);
		//$out->total = $total;
		return $out->_print();
			
	}

	// return Assoc array[currency][paid & total] to student has to pay per term
	public function getPayPerTerm($date){ // FROM SAFEMS Databases
		global  $sms;
		$student = $this->student;
		$sms = $this->sms;
		$safems = $this->safems;
		$profil =$this->getProfil();
		$cur =  $student->getSettings('def_currency');
		$fees = do_query_array("SELECT SUM(value) AS value, SUM(paid) AS paid, currency FROM school_fees WHERE date_id=$date->id AND std_id=$student->id AND cc=$sms->ccid", $safems->database, $safems->ip);
		
		$out = array();
		if($fees != false){
			foreach($fees as $fee){
				if(!array_key_exists($fee->currency, $out) ){
					$out[$fee->currency]['total'] = 0;
					$out[$fee->currency]['paid'] = 0;
				}
				$out[$fee->currency]['total'] += $fee->value;
				$out[$fee->currency]['paid'] += $fee->paid;
			}
		}
		return $out;
	}
	
	
	// Common Function IG exclusive
	// genearte new row in sms school fees for jun school fee
	// all other fees will be saved in year->materials_std
	public function generateServiceFees($year){
		global $sms, $ig_services_fees_name, $ig_payments, $ig_reg_account, $ig_mode_exams;
		$result = true;
		$student = $this->student;
		$std_id = $student->id;
		$sms = $this->sms;
		$level = $student->getLevel();
		$dates = $student->getDates($year);
		$sf_acc = getAccount(SF_account);
		$level_payment = $ig_payments[$level->id];		
		if(do_delete_obj("con='services' AND con_id=$std_id AND year=$year",'school_fees',$sms->database,$sms->ip) != false){
			$fees = $this->getFeesByExam( $year, 'jun');
			$jun_school_fees = $fees['total_fees'];
			$jun_reg_fees = $fees['total_reg'];
			$jun_fees_row = array(
				'title'=> $ig_services_fees_name,
				'con'=>'services',
				'con_id'=>$std_id,
				'year'=>$year,
				'debit'=>$jun_school_fees,
				'currency'=>'EGP',
				'main_code'=>$sf_acc->main_code,
				'sub_code'=>$sf_acc->sub_code,
				'increase'=>'0'
			);
		//	print_r($jun_fees_row);
			$jun_fees_id = do_insert_obj($jun_fees_row, 'school_fees', $sms->database, $sms->ip);
			if($jun_fees_id != false){
				$i = 0;
				foreach($dates as $date){
					if($jun_school_fees>0){
						if( $jun_school_fees <=$level_payment[$i]){
							$val = $jun_school_fees;
						} else {
							$val = $level_payment[$i];						
						}
						$jun_school_fees-=$val;
						$jun_values = array(
							'con'=> 'student',
							'con_id'=> $std_id,
							'fees_id'=>$jun_fees_id,
							'term'=>$date->id,
							'year'=>$year,
							'value'=> $val
						);
						if( !do_insert_obj($jun_values, 'school_fees_payments', $sms->database, $sms->ip)){
							$result = false;
						}						
					}
					$i++;
				}
				return $result;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// Common Function IG exclusive
	// Display per session selected materials and pyment summary
	// and tools to pay fees, remove fees, preparing for refund except for jun school fees
	public function loadStudentRegistrationTable( $year){
		global $this_system, $ig_mode_exams, $lang;
		$student = $this->student;
		$std_id = $student->id;
		$sms = $this->sms;
		$layout = new layout();
		$layout->template = "modules/fees/templates/student_registration.tpl";
		$layout->nov_trs = '';
		$layout->jan_trs = '';
		$layout->jun_trs = '';
		$layout->nov_total_fees = 0;
		$layout->jan_total_fees = 0;
		$layout->jun_total_fees = 0;
		/*$layout->nov_total_reg = 0;
		$layout->jan_total_reg = 0;
		$layout->jun_total_reg = 0;*/
		$layout->nov_reg_edex = 0;
		$layout->jan_reg_edex = 0;
		$layout->jun_reg_edex = 0;
		$layout->nov_reg_camb = 0;
		$layout->jan_reg_camb = 0;
		$layout->jun_reg_camb = 0;
		$layout->nov_reg_paid_edex = 0;
		$layout->jan_reg_paid_edex = 0;
		$layout->jun_reg_paid_edex = 0;
		$layout->nov_reg_paid_camb = 0;
		$layout->jan_reg_paid_camb = 0;
		$layout->jun_reg_paid_camb = 0;
		$layout->nov_total_fees_paid = 0;
		$layout->jan_total_fees_paid = 0;
		$layout->jun_total_fees_paid = 0;
		/*$layout->nov_total_reg_paid = 0;
		$layout->jan_total_reg_paid = 0;
		$layout->jun_total_reg_paid = 0;*/
		foreach($ig_mode_exams as $exam){
			$rows = do_query_array("SELECT * FROM materials_std WHERE std_id=$std_id AND exam='$exam'", Db_prefix.$year, $sms->ip);
			$trs = array();
			if($rows!= false && count($rows>0)){
				foreach($rows as $row){
					$fees = $this->getStdServiceFeesByExam($row->services, $exam, $year);
					$service = new ServicesIG($row->services, $sms, $year);
					$group = $service->getGroup();
					$group_name = strtolower($group->{'name_'.$_SESSION['dirc']});
					if($this_system->type == 'sms'){
						$button_action = ($row->fees_paid>0 || $row->reg_paid>0 ? 'refundServicesFees' : 'removeServiceIG');
						$button = write_html('button', 'class="circle_button ui-state-default hoverable"  action="'.$button_action.'" exam="'.$exam.'" std_id="'.$std_id.'" sms_id="'.$sms->id.'" service_id="'.$service->id.'" title="'.($row->fees_paid>0 || $row->reg_paid>0 ? $lang['refund'] : $lang['remove']).'"',
							write_icon($row->fees_paid>0 || $row->reg_paid>0 ? 'arrowreturnthick-1-w' :'trash')
						);
					} elseif ($this_system->type == 'safems'){
						$button = ( $row->reg_paid>0 ? 
							""
						:
							 write_html('button', 'class="circle_button ui-state-default hoverable"  action="paySessionFees" exam="'.$exam.'" std_id="'.$std_id.'" sms_id="'.$sms->id.'" paid="'.($fees['fees'] + $fees['reg']).'" service_id="'.$service->id.'" title="'.$lang['pay'].'"',
								write_icon('plus')
							)
						);
					}
					$layout->{$exam.'_trs'} .= write_html('tr', '',
						write_html('td', '', $service->getName().'-'.$service->lvl).
						($exam != 'jun' ?
							write_html('td', '', $fees['fees'])
						: '').
						( $group_name == 'edex' ?
							write_html('td', '', numberToMoney($fees['reg'])).
							write_html('td', '', '')
						: 
							write_html('td', '', '').
							write_html('td', '', numberToMoney($fees['reg']))
						).
						write_html('td', '',
							$button
						)
					);
					$layout->{$exam.'_total_fees'} += $fees['fees'];
					$layout->{$exam.'_total_fees_paid'} += $row->fees_paid;
//					$layout->{$exam.'_total_reg_paid'} += $row->reg_paid;
//					$layout->{$exam.'_total_reg'} += $fees['reg'] ;
					$layout->{$exam.'_reg_'.$group_name} += $fees['reg'];
					$layout->{$exam.'_reg_paid_'.$group_name} += $row->reg_paid;
				}
				
//				$layout->{$exam.'_total_fees_icon'} = $layout->{$exam.'_total_fees_paid'};
//				$layout->{$exam.'_total_reg_icon'} = $layout->{$exam.'_total_reg_paid'};
				if($this_system->type == 'safems' ){
					$layout->{$exam.'_safems_tr'} = write_html('tr', '',
						write_html('th', '', $lang['pay']).
						write_html('th', 'align="center"',
							($exam != 'jun' && $layout->{$exam.'_total_fees'}> $layout->{$exam.'_total_fees_paid'}?
								write_html('button', 'type="button" class="ui-state-default hoverable"  module="fees" action="paySessionFees" fees="fees" exam="'.$exam.'" std_id="'.$std_id.'" sms_id="'.$sms->id.'"  paid="'.($layout->{$exam.'_total_fees'} - $layout->{$exam.'_total_fees_paid'}).'"', ($layout->{$exam.'_total_fees'} - $layout->{$exam.'_total_fees_paid'}))
							: '')
						).
						write_html('th',' align="center"',
							( $layout->{$exam.'_reg_edex'} > $layout->{$exam.'_reg_paid_edex'} ?
								write_html('button', 'type="button" class="ui-state-default hoverable" module="fees" action="paySessionFees"  fees="reg" group="edex" exam="'.$exam.'" std_id="'.$std_id.'" sms_id="'.$sms->id.'" paid="'.($layout->{$exam.'_reg_edex'} - $layout->{$exam.'_reg_paid_edex'}).'"', ($layout->{$exam.'_reg_edex'} - $layout->{$exam.'_reg_paid_edex'}))
							: '')
						).
						write_html('th',' align="center"',
							( $layout->{$exam.'_reg_camb'} > $layout->{$exam.'_reg_paid_camb'} ?
								write_html('button', 'type="button" class="ui-state-default hoverable" module="fees" action="paySessionFees"  fees="reg" group="camb" exam="'.$exam.'" std_id="'.$std_id.'" sms_id="'.$sms->id.'" paid="'.($layout->{$exam.'_reg_camb'} - $layout->{$exam.'_reg_paid_camb'}).'"', ($layout->{$exam.'_reg_camb'} - $layout->{$exam.'_reg_paid_camb'}))
							: '')
						).
						($exam != 'jun' ?
							write_html('th', '', '&nbsp;')
						: '')
					);
				}
			}
		}
		return $layout->_print();
	}
	
	// Common Function IG exclusive
	// get the student dues by student in exam for a selected service
	// Output = Assoc array(fees, reg)
	public function getStdServiceFeesByExam($service_id, $exam, $year, $type=''){
		$student = $this->student;
		$std_id = $student->id;
		$sms = $this->sms;
		$std = do_query_obj("SELECT * FROM materials_std WHERE std_id=$std_id AND services=$service_id AND exam='$exam'", Db_prefix.$year, $sms->ip);
		$type = $std!=false ? $std->type : $type;
		$ser = do_query_obj("SELECT $exam, ".$exam."_reg FROM servicesig_fees WHERE service_id=$service_id AND type='$type'", Db_prefix.$year, $sms->ip);
		if($std!=false && $std->fees > 0){
			$out['fees'] = $std->fees;
		} else {
			$out['fees'] = $ser->$exam;
		}
		if($std!=false && $std->reg > 0){
			$out['reg'] = $std->reg;
		} else {
			$out['reg'] = $ser->{$exam.'_reg'};
		}
		return $out;
	}
	
	// Common Function IG exclusive
	// return an Assoc array(total_fees, total, reg, html) 
	// html load TR to be displayed in service fees table
	public function getFeesByExam($year, $exam){
		global $ig_mode_exams;
		$student = $this->student;
		$std_id = $student->id;
		$sms = $this->sms;
		$total_fees = 0;
		$total_reg = 0;
		$rows = do_query_array("SELECT services, type FROM materials_std WHERE std_id=$std_id AND exam='$exam'", Db_prefix.$year, $sms->ip);
		$trs = array();
		if($rows!= false && count($rows>0)){
			foreach($rows as $row){
				$fees = $this->getStdServiceFeesByExam($row->services, $exam, $year);
				$service = new ServicesIg($row->services);
				$trs[] = write_html('tr', '',
					write_html('td', '', $service->getName()).
					write_html('td', '', $service->lvl).
					write_html('td', '', $row->type).
					write_html('td', '', '<input type="text" std_id="'.$std_id.'" exam="'.$exam.'" service_id="'.$service->id.'" name="fees" value="'.$fees['fees'].'" module="fees" update="updateStdServiceFees" />').
					write_html('td', '', '<input type="text" std_id="'.$std_id.'" exam="'.$exam.'" service_id="'.$service->id.'" name="reg" value="'.$fees['reg'].'" module="fees" update="updateStdServiceFees" />')
				);
				$total_fees += $fees['fees'];
				$total_reg += $fees['reg'];
			}
		}
		$layout = new Layout();
		$layout->template = 'modules/fees/templates/student_ig_fees_exam.tpl';
		$layout->trs = implode('', $trs);
		$layout->total_fees = $total_fees;
		$layout->total_reg = $total_reg;		
		return array(
			'total_fees'=>$total_fees,
			'total_reg'=>$total_reg,
			'html'=>$layout->_print()
		);
	}
	
	/*********************** SMS Exclusives *************************************/
	// load the year service fees and reg table for ig mode 
	// and tools for updating student service fees
	// apear in student fees layout
	public function loadStudentServicesFeesTable($year, $exam=''){
		global $ig_mode_exams;
		if($exam!= ''){
			$table = $this->getFeesByExam($year, $exam);
			return $table['html'];
		} else {
			foreach($ig_mode_exams as $exam){
				$table = $this->getFeesByExam($year, $exam);
				if($table['total_fees'] >0){
					$fieldset[$exam] = write_html('fieldset', '',
						write_html('legend', '', strtoupper($exam)).
						$table['html']
					);
				}
			}
			return write_html('table', 'width="100%"',
				write_html('tr','', 
					write_html('td', 'valign="top"',
						(isset($fieldset['nov']) ? $fieldset['nov']: '').
						(isset($fieldset['jan']) ? $fieldset['jan']: '')
					).
					write_html('td', 'valign="top"',
						isset($fieldset['jun']) ? $fieldset['jun']: ''
					)
				)
			);
		}
		
	}

	// Display a Refund form to Refund service 
	// this work to IG mode only
	public function newRefundForm($service_id='', $exam){
		global $this_system, $lang;
		$student = $this->student;
		$std_id = $student->id;
		$sms = $this->sms;
		$service = new ServicesIG($service_id);
		$row = do_query_obj("SELECT * FROM materials_std WHERE std_id=$std_id AND exam='$exam' AND services=$service_id", $sms->db_year, $sms->ip);

		if($row != false){
			$layout = new Layout($row);
			$layout->template = 'modules/fees/templates/refund_ig_service.tpl';
			$layout->pay_name = $service->getName(). ' - '.$exam;
			$layout->service_id=$service->id;
			$layout->std_id=$std_id;
			$layout->exam=$exam;	
			$layout->total = $row->fees + $row->reg;
			$layout->total_paid = $row->fees_paid + $row->reg_paid;
			$layout->date = unixToDate(time());
			return $layout->_print();
		} else {
			return write_error('Exam not setted to the student.');
		}
	}
	
	// submit refund
	// prepare to the safems to refund this service
	// delete the mterial student row
	public function refundIgPayment($post){
		$safems = $this->sms->getSafems();
		$service_id = $post['service_id'];
		$exam = $post['exam'];
		$year = isset($post['year']) ? $post['year'] : $_SESSION['year']; 
		$fees = $post['fees'];
		$reg = $post['reg'];
		$std_id =$this->student->id;
		$service = do_query_obj("SELECT * FROM materials_std WHERE std_id=$std_id AND services=$service_id AND exam='$exam'", Db_prefix.$year, $this->sms->ip);
		
		if($service!= false){
			$refund = clone $service;
			unset($refund->id);
			$refund->fees_refund = $fees;
			$refund->reg_refund = $reg;
			$refund->year = $year;
			
			if( do_insert_obj($refund, 'refund_services', $safems->database, $safems->ip)!= false){
				return do_delete_obj("id=$service->id", 'materials_std',  Db_prefix.$year, $this->sms->ip);
			}
		} 
		return false;
	}
	/*********************** SafeMS Exclusives *************************************/
	
	// Display a payment form to pay fees or registation per session Or hole session
	// this work to IG mode only
	public function newPayForm($type='', $service_id='', $exam, $paid, $group=''){
		global $this_system, $lang;
		$student = $this->student;
		$std_id = $student->id;
		$sms = $this->sms;
		if($this_system->type != 'safems'){
			return write_error('This function can only be excuted from SafeMS');
		} else {
			$safems= $this_system;
		}
		if($service_id == ''){
			$row = do_query_obj("SELECT SUM($type) AS $type, SUM(".$type."_paid) AS paid FROM materials_std WHERE std_id=$std_id AND exam='$exam'", $sms->db_year, $sms->ip);
		} else {
			$type = 'all_fees';
			$row = do_query_obj("SELECT (fees+reg) AS $type,(fees_paid+reg_paid) AS paid FROM materials_std WHERE std_id=$std_id AND exam='$exam' AND services=$service_id", $sms->db_year, $sms->ip);
		}
		if($row != false){
			$layout = new Layout();
			$layout->template = 'modules/fees/templates/new_reg_pay.tpl';
			if($service_id != ''){
				$service = new ServicesIG($service_id);
				$layout->pay_name = $service->getName(). ' - '.$exam;
			} else {
				$layout->pay_name = ($type == 'fees' ? $lang['school_fees'] : $lang['registration_fees']) .' - '.$exam;
			}
			$layout->service_id=$service_id;
			$layout->std_id=$std_id;
			$layout->fees = $type;
			$layout->exam=$exam;
			$layout->group=$group;		
			$layout->value = $paid;
			$layout->date = unixToDate(time());
			$layout->curs_opts =Currency::getOptions($safems->getSettings('def_currency'));	
			$layout->banks_opts = Banks::getOptions($safems->getSettings('def_bank'));
			return $layout->_print();
		} else {
			return write_error('Exam not setted to the student.');
		}
	}

	//this function to submit student payment for reg or fee in session or hole session
	public function saveIgPayment($exam, $service_id, $value, $date, $bank, $type, $group='', $year=''){
		global $lang;
		$student = $this->student;
		$std_id = $student->id;
		$sms = $this->sms;
		if($year == ''){
			$year = $_SESSION['year'];
		}
		$result = true;
		$error = '';
		$student = new Students($std_id, $sms);
		$level = $student->getLevel();
		$ingoing = array();
		$ingoing['year'] = $year;
		$ingoing['from'] = $student->getAccCode();		
		$ingoing['type'] = $bank=='' ? 'cash' : 'visa';		
		$ingoing['currency'] =  $_POST['currency'];
		$ingoing['ccid'] = $sms->getCC();
		$ingoing['date'] = dateToUnix($_POST['date']);
		$ingoing['bank'] = $bank;	
		$notes = $lang['student'].': '.$student->getName().' - '.($level!=false ?$level->getName() :'').' - '. $exam. ' - %s - '. $year.'/'.($year+1) .' - '. $sms->code  ;
		$recete_notes = $lang['student'].': '.$student->getName().' - '.($level!=false ?$level->getName() :'').' - '. $exam. ' - %s - '. $year.'/'.($year+1) .' - '. $sms->code ;
			//multiple serive
		if(trim($_POST['service_id']) == ''){
			$field = $type.'_paid';
			$dues = do_query_array("SELECT * FROM materials_std WHERE std_id=$std_id AND exam='$exam' AND ".$type."_paid<$type", Db_prefix.$year, $sms->ip);
			if($dues != false && count($dues>0)){
				foreach($dues as $due){
					$service = new ServicesIG($due->services);
					$service_group = $service->getGroup();
					$group_name = $service_group->{'name_'.$_SESSION['dirc']};
					if($type=='fees' || strtolower($group_name) == $group){
						if(do_update_obj(array($field=>$due->$type), "id=$due->id", 'materials_std', Db_prefix.$year, $sms->ip) == false){
							$result = false;
						}
					}
				}
				if($result == true){
					// recete
					$ingoing['value'] =  $value;
					$ingoing['notes'] = sprintf($notes, ($type == 'fees' ? $lang['school_fees'] : $lang['registration_fees'].' - '.$group_name));
					$ingoing['recete_notes'] = sprintf($recete_notes, ($type == 'fees' ? $lang['school_fees'] : $lang['registration_fees'].' - '.$group_name));
					$to_acc = new Accounts($type =='fees' ? SF_account : BC_account);
					$to_full_code = $to_acc->full_code;
					$cur_year = getNowYear();
					if($year > $cur_year->year && substr($to_acc->main_code, 0, 1) == 4){
						$adv_acc_code = '29'.substr($to_acc->main_code, 1, 5);
						$to_full_code = Accounts::fillZero('main', $adv_acc_code).Accounts::fillZero('sub', $to_acc->sub_code);
					}
					$ingoing['to'] = $to_full_code;
					$ingoing = Ingoing::addNewPayment($ingoing);
					if($ingoing != false){
						$recete = $ingoing->getRecete();
					} else {
						$result = false;
						$error = "Can't print recete";
					}
				}
			} else {
				$result = false;
				$error = "Can't find anything to pay";
			}
			// one service
		} else {
			$due = do_query_obj("SELECT * FROM materials_std WHERE std_id=$std_id AND services=$service_id AND exam='$exam'", $sms->db_year, $sms->ip);
			if(($due->fees+$due->reg) == $value){
				$update = array('fees_paid'=>$due->fees, 'reg_paid'=>$due->reg);
				if( do_update_obj($update, "std_id=$std_id AND services=$service_id AND exam='$exam'", 'materials_std', Db_prefix.$year, $sms->ip)== false){
					$result = false;
				}
			}
			if($result == true){
				// schoolfees recete;
				$ingoing['value'] =  $due->fees;
				$ingoing['notes'] = sprintf($notes, $lang['fees']);
				$ingoing['recete_notes'] = sprintf($recete_notes, $lang['fees']);
				$to_acc = new Accounts( SF_account);
				$to_full_code = $to_acc->full_code;
				$cur_year = getNowYear();
				if($year > $cur_year->year && substr($to_acc->main_code, 0, 1) == 4){
					$adv_acc_code = '29'.substr($to_acc->main_code, 1, 5);
					$to_full_code = Accounts::fillZero('main', $adv_acc_code).Accounts::fillZero('sub', $to_acc->sub_code);
				}
				$ingoing['to'] = $to_full_code;
				$fees_ingoing = Ingoing::addNewPayment($ingoing);
				if($fees_ingoing != false){
					$recete = $fees_ingoing->getRecete();
				} else {
					$result = false;
					$error = "Can't print recete";
				}
				// registration recete;
				$ingoing['value'] =  $due->reg;
				$ingoing['notes'] = sprintf($notes, $lang['registration_fees']);
				$ingoing['recete_notes'] = sprintf($recete_notes, $lang['registration_fees']);
				$to_acc =  new Accounts(BC_account);
				$to_full_code = $to_acc->full_code;
				$cur_year = getNowYear();
				if($year > $cur_year->year && substr($to_acc->main_code, 0, 1) == 4){
					$adv_acc_code = '29'.substr($to_acc->main_code, 1, 5);
					$to_full_code = Accounts::fillZero('main', $adv_acc_code).Accounts::fillZero('sub', $to_acc->sub_code);
				}
				$ingoing['to'] = $to_full_code;
				$reg_ingoing = Ingoing::addNewPayment($ingoing);
				if( $reg_ingoing != false){
					$recete .= $reg_ingoing->getRecete();
				} else {
					$result = false;
					$error = "Can't print recete";
				}
				
			}
		}
		return array(
			'error' => $error,
			'recete'=>$recete.$recete
		);
	}
	

}



