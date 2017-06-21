<?php
/** School Fees
*
*
*/
class SchoolFees {
	
	public function __construct($sms){
		$this->sms = $sms;
	}

	public function loadMainLayout(){
		$sms = $this->sms;
		$safems = $sms->getSafems();
		$layout = new Layout($sms);
		$layout->template = 'modules/fees/templates/add_form.tpl';
		$layout->sms_id = $sms->ccid;
		$busms = $sms->getBusms();
		$layout->ccid = $sms->getCC();
		$layout->currency_opts = Currency::getOptions($safems->getSettings('def_currency'));
		$layout->banks_opts = Banks::getOptions($safems->getSettings('def_bank'));
		$years= $sms->getYearsArray();
		$next_year = ($_SESSION['year'] +1);
		$year_selc = array($next_year=> $next_year .'/'. ($next_year +1));
		foreach($years as $year){
			$year_selc[$year] = $year .'/'. ($year +1);
		}
		$layout->year_opts = write_select_options($year_selc, $_SESSION['year']);
		$layout->addForm = $layout->_print();

		$layout->browse_fees = $this->browseFees();
		$layout->currency_opts = Currency::getOptions($sms->getSettings('def_currency'));
		
		// Load payment dates
		$layout->payment_table = Fees::loadDatesLayout($sms);
		return fillTemplate("modules/fees/templates/school_layout.tpl", $layout);
	}
	
	
	public function getFeesSettings(){
		$sms = $this->sms;
		$layout = new Layout($sms);
		$layout->template = 'modules/fees/templates/school_fees_settings.tpl';
		$levels = $sms->getLevelList();
		$layout->items_list = '';
		
		$first = true;
		foreach($levels as $item){
			if(isset($item->id) && $item->id != ''){
				 $layout->items_list .=write_html( 'li', 'itemid="'.$item->id.'" smsid="'.$sms->ccid.'" class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openLevelInfos"', 
					write_html('text', 'class="holder-level'.$item->id.'"',
						$item->getName()
					)
				);	
			}
			$first = false;
		}
		
		// load first level
		$first_level = reset($levels);
		$layout->level_fees_layout = $first_level->loadFeesLayout();
		return $layout->_print();
	}

	public function browseFees(){
		// Level List
		$sms = $this->sms;
		$layout = new Layout($sms);
		$layout->template = 'modules/fees/templates/school_fees_browse.tpl';
		$levels = $sms->getLevelList();
		$layout->levels_list = '';
		
		$first = true;
		foreach($levels as $item){
			if(isset($item->id) && $item->id != ''){
				$layout->levels_list .=write_html( 'li', 'itemid="'.$item->id.'" smsid="'.$sms->ccid.'" class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="browseLevel"', 
					write_html('text', 'class="holder-level-'.$item->id.'"',
						$item->getName()
					)
				);
			}
			$first = false;
		}
		
		// load first level
		$first_level = reset($levels);
		$layout->level_layout = $this->browseLevel($first_level->id);
		return $layout->_print();
	}

	public function browseLevel($level_id, $year=''){
		if($year == ''){
			$year = $_SESSION['year'];
		}
		$level = new Levels($level_id, $this->sms);
		$classes = $level->getClassList();		
		$layout = new Layout($level);
		$sms = $this->sms;
		$layout->template = 'modules/fees/templates/level_browse.tpl';
		$layout->tabs_lis = '';
		$first = true;
		foreach($classes as $class){
			$href = $first ? '#first_class' : 'index.php?module=fees&browse&con=class&con_id='.$class->id.'&sms_id='.$this->sms->ccid;
			$layout->tabs_lis .= write_html('li', '', 
				write_html('a', 'href="'.$href.'"', $class->getName())
			);
			if($first) { $layout->tabs_div = $this->browseClass($class->id, $year);}
			$first = false;
		}
		
		return $layout->_print();
	}
	
	public function browseClass($class_id, $year=''){
		global $lang, $this_system, $prvlg;
		if($year == ''){
			$year = $_SESSION['year'];
		}
		$class = new Classes($class_id, $year, $this->sms);
		$layout = new Layout($class);
		$layout->template = 'modules/fees/templates/class_fees.tpl';
		$safems = $this->sms->getSafems();
		$layout->class_name = $class->getName();
		$students = $class->getStudents(array('1', '3'));
		$dates = $class->getLevel()->getDates($year);
		$row = array();
		$profils = array('0'=> $lang['normal']);
		$profils_arr = Profils::getList();
		if(count($profils_arr) > 0){
			foreach($profils_arr as $prof){
				$profils[$prof->id] = $prof->title;
			}
		}
		foreach($students as $std){
			$total = 0;
			$profil = $std->getProfil();
		//	print_r($profil);
			$profil_id = $profil != false && isset($profil->id) ? $profil->id :'';
			$parent = $std->getParent();
			$son_of_employe = ($parent->father_emp == '1' || $parent->mother_emp =='1') ? true : false;
			//$account = new Accounts($std->getAccCode());
			$ccid = $this->sms->getCC();
			$sql = "SELECT SUM(value) AS total, SUM(paid) as paid FROM school_fees WHERE std_id='$std->id' AND cc=$ccid AND year = $year AND currency='".$this->sms->getSettings('def_currency')."'";
			$paid = do_query_obj($sql, $safems->database, $safems->ip);
			$bros = do_query_array("SELECT id FROM student_data WHERE parent_id='$std->parent_id' AND id!=$std->id AND status=1", $this->sms->database, $this->sms->ip);
			$bus_code = $std->getBus();
			$tr = write_html('td', 'class="unprintable"', 
				write_html('button', 'class="circle_button ui-state-default hoverable" action="openStudent" std_id="'.$std->id.'" sms_id="'.$this->sms->id.'"',
					write_icon('person')
				)
			).
			write_html('td', '', $std->getName()).
			write_html('td', '', unixToDate($std->join_date)).
			write_html('td', 'align="center"', 
				$son_of_employe ? write_icon('check') : ''
			).
			write_html('td', 'align="center"', 
				count($bros)>0 ? count($bros) : ''
			).
			write_html('td', 'align="center"', 
				$std->locker!='' ? write_icon('check') : ''
			).
			write_html('td', 'align="center"', 
				$bus_code!='' ? $bus_code : ''
			).
			write_html('td', '',
				write_html_select('name="profil[]" rel="'.$std->id.'" sms_id="'.$this->sms->id.'" module="fees" update="updateStdProfil" class="combobox" '.($prvlg->_chk('edit_std_fees')?'':'disabled="disabled"'), $profils, $profil_id)
			);			
			$std_total = $prvlg->_chk('read_std_fees_stat') && $paid->total>0 ? write_icon('check') : numberToMoney($paid->total);
			$std_paid = $prvlg->_chk('read_std_fees_stat')&& $paid->paid>0 ? write_icon('check') : numberToMoney($paid->paid);
			//$std_paid .= $prvlg->_chk('read_std_fees_stat')&& ($paid->paid==$paid->total) ? write_icon('check') : '';
			$std_diff = $paid->paid<=$paid->total ?
				($paid->total-$paid->paid)>0 ? 
					$prvlg->_chk('read_std_fees_stat') ? write_icon('check'): numberToMoney($paid->total- $paid->paid )
				: ''
			: '';
			
			$tr .= write_html('td', 'align="center" class="total"', write_html('b', '',  $std_total)).
				write_html('td', 'align="center" class="paid"', write_html('b', '',  $std_paid)).
				write_html('td', 'align="center" class="diff"', write_html('b', '', $std_diff)).
				write_html('td', 'class="unprintable"', 
				($prvlg->_chk('edit_std_fees') ? 
					write_html('button', 'class="circle_button ui-state-default hoverable" action="calcFees" std_id="'.$std->id.'" sms_id="'.$this->sms->id.'"',
						write_icon('refresh')
					)
				: '')
				);
			$row[] = write_html('tr', '', $tr);
		}
		$layout->student_rows = implode('', $row);
		
		$layout->payments_ths ='';
		foreach($dates as $d){
			$layout->payments_ths .= write_html('th', '', $d->title);
		}
		
		$layout->total_students = count($students);
		$layout->class_name = $class->getName();
				
		return $layout->_print();
	}
	
	public function getDates($year=''){
		return do_query_array("SELECT * FROM school_fees_dates WHERE con IS NULL AND con_id IS NULL AND year=$year ORDER By `from` ASC", $this->sms->database, $this->sms->ip);
	}
	
		
	public function getTotals($year='', $profil=''){
		global $lang;
		if($year == ''){$year = $_SESSION['year'];}
		$sms = $this->sms;
		$safems = $sms->getSafems();
		$levels = $sms->getLevelList();
		$first_lvl = true;		
		$tfoot_total = array();
		$tfoot_paid = array();
		$main_accounts = array();
		$trs = array();
		$all_std_count = 0;
		foreach($levels as $level){			
			$stds_id = array();
			$students = $level->getStudents(array('1','3'));
			foreach($students as $student){
				if($profil == ''){
					$stds_id[] = $student->id;
				} else {
					if( $profil == $student->getProfil()){
						$stds_id[] = $student->id;
					}
				}
			}
			if(count($stds_id)){
				$where = "(std_id=".implode(" OR std_id=", $stds_id).")";
				$totals = array();
				$paids = array();
				$counts = array();
				$sql = "SELECT SUM(value) AS value, SUM(paid) AS paids, COUNT(DISTINCT(std_id)) AS tot_std, currency, fees_id FROM school_fees WHERE cc=$sms->ccid AND year=$year AND $where GROUP BY fees_id, currency";
				$results = do_query_array($sql, $safems->database, $safems->ip);
				foreach($results as $result){
					$fees = new Fees($result->fees_id, $sms);
					$main_acc_code = $fees->getMainAccCode();
					if(!in_array($main_acc_code, $main_accounts)){
						$main_accounts[] = $main_acc_code;
					}
					if(!isset($totals[$main_acc_code][$result->currency])){
						$totals[$main_acc_code][$result->currency] = 0;
						$paids[$main_acc_code][$result->currency] = 0;
						$counts[$main_acc_code] = 0;
						$tfoot_total[$main_acc_code][$result->currency] = 0;
						$tfoot_paid[$main_acc_code][$result->currency] = 0;
					} 
					$totals[$main_acc_code][$result->currency] += $result->value;
					$paids[$main_acc_code][$result->currency] += $result->paids;
					$counts[$main_acc_code] += $result->tot_std;
					if(!isset($tfoot_total[$main_acc_code][$result->currency])){
						$tfoot_total[$main_acc_code][$result->currency] = 0;
					} 
					$tfoot_total[$main_acc_code][$result->currency] += $result->value;					
					$tfoot_paid[$main_acc_code][$result->currency] += $result->paids;
					$all_std_count += $result->tot_std;
				}
				foreach($totals as $acc_code=>$cur_array){
					$acc = new Accounts($acc_code);
					$first_fees = true;
					foreach($cur_array as $cur=>$value){
						$rowspan = count($cur_array)-1 +  count($totals);
						$trs[] = write_html('tr', '',
							($first_lvl ? 
								write_html('td', 'rowspan="'.$rowspan.'"', $level->getName())
							:'').
							($first_fees ? 
								write_html('td', 'rowspan="'.count($cur_array).'"', $acc->title)
							:'').
							write_html('td', 'align="center"', $cur).
							write_html('td', 'align="center"', $counts[$acc_code]).
							write_html('td', '', numberToMoney($value)).
							write_html('td', '', isset($paids[$acc_code][$cur]) ? numberToMoney($paids[$acc_code][$cur]) : 0).
							write_html('td', '', numberToMoney($value- (isset($paids[$acc_code][$cur]) ? $paids[$acc_code][$cur]: 0)))
						);
						$first_fees = false;
						$first_lvl = false;
					}
				}
				$first_lvl = true;
			}
		}
		
			// tfoot
		$tfoot_trs = array();
		foreach($main_accounts as $acc_code){
			$acc = new Accounts($acc_code);
			$first_tr = true;
			foreach($tfoot_total[$acc_code] as $curr=>$value){
				$tfoot_trs[] = write_html('tr', '',
					($first_tr ? 
						write_html('th', 'rowspan="'.count($tfoot_total[$acc_code]).'"', ($first_tr ? $lang['total'] : '&nbsp;')).
						write_html('th', 'rowspan="'.count($tfoot_total[$acc_code]).'"', $acc->title)
					: '').
					write_html('th', '', $curr).
					write_html('th', '', $all_std_count).
					write_html('th', '', numberToMoney($value)).
					write_html('th', '', numberToMoney($tfoot_paid[$acc_code][$curr])).
					write_html('th', '', numberToMoney($value - $tfoot_paid[$acc_code][$curr]))
				);
				$first_tr = false;
			}
		}
		$layout = new Layout($sms);
		$layout->school_name = $sms->getName();
		$layout->year_name = $year.'/'.($year+1);
		$layout->today = unixToDate(time());
		$layout->template = "modules/fees/templates/school_fees_incomes.tpl";
		$layout->tbody_trs = implode('', $trs);
		$layout->tfoot_trs = implode('', $tfoot_trs);
		return $layout->_print();		
	}
	
	public function getLateList($level_id='', $fee_acc='', $tr_only=false, $year='', $none_paid=false, $date=''){
		global $prvlg, $lang;
		$sms=$this->sms;
		$safems= $sms->getSafems();
		$trs = array();
		$all_levels = $sms->getLevelList();
		$dates = $this->getDates($year);
		if($date==''){
			$first_date = reset($dates);
			$now = $first_date->limit;
		} else {
			$now = $date;
		}

		if($fee_acc != '' && $fee_acc != ' '){
			$where_fees = array();
			$acc = new Accounts($fee_acc);
			$fees = do_query_array("SELECT id FROM school_fees WHERE main_code='$acc->main_code' AND year=$year", $sms->database, $sms->ip);
			$where_fees =array();
			foreach($fees as $f){
				$where_fees[] = "fees_id=$f->id";
			}
			$sql = "SELECT SUM(value-paid) AS diff, SUM(paid) as total_paid, SUM(value) AS total, std_id, currency, fees_id FROM school_fees 
				WHERE year=$year 
				AND cc = $sms->ccid
				AND due_date<=$now ".
				($none_paid ? "AND paid=0 " : "AND paid < value ").
				(count($where_fees)>0 ? " AND(".implode(' OR ', $where_fees).") " : '').
				"GROUP BY std_id, currency ORDER BY std_id";
				
		} else {
			$sql = "SELECT SUM(value-paid) AS diff, SUM(paid) as total_paid, SUM(value) AS total, std_id, currency, fees_id FROM school_fees 
				WHERE year=$year 
				AND cc = $sms->ccid
				AND due_date<=$now ".
				($none_paid ? "AND paid=0 " : "AND paid<value ").
				"GROUP BY std_id, currency  ORDER BY std_id";
		}

		$rows = do_query_array($sql, $safems->database, $safems->ip);
		$ser = 1;
		
		foreach($rows as $row){
			$fees = new Fees($row->fees_id, $sms);
		//	if(!isset($fees->id)) {print_r($row); exit;}
		//	print_r($fees);
			$main_acc_code = $fees->getMainAccCode();
			if($fee_acc == '' || $main_acc_code == Accounts::removeZero($fee_acc)){
				$student = new Students($row->std_id, $sms);
				if(in_array($student->getStatus(), array('1', '3'))){
					$level = $student->getlevel();
					if($level_id == '' || ($level!= false && $level->id==$level_id)){
						$trs[] = write_html('tr', '',
							($prvlg->_chk('std_read')?
								write_html('td', 'class="unprintable"', 
									write_html('button', 'class="circle_button ui-state-default hoverable" module="students" action="openStudent" std_id="'.$row->std_id.'" sms_id="'.$sms->ccid.'"', write_icon('person'))
								)
							:'').
							write_html('td', '', $ser).
							write_html('td', '', $student->getName()).
							write_html('td', '', ($level!=false ? $level->getName(): '')).
							write_html('td', '', ($none_paid ?'':  $fees->title)).
							write_html('td', 'align="center"', $row->currency).
							write_html('td', 'align="center"', 
								$prvlg->_chk('read_std_fees_stat')?
									($row->total > 0 ? write_icon('check') : '&nbsp;' )
								: 
									numberToMoney($row->total)
							).
							write_html('td', 'align="center"', 
								$prvlg->_chk('read_std_fees_stat')?
									($row->paid > 0 ? write_icon('check') : '&nbsp;' )
								: 
									numberToMoney($row->total_paid)
							).
							write_html('td', 'align="center"', 
								$prvlg->_chk('read_std_fees_stat')?
									($row->diff > 0 ? write_icon('check') : '&nbsp;' )
								: 
									numberToMoney($row->diff)
							)
						);
						$ser++;
					}
				}
				/*$fees = new Fees($row->fees_id);
				$main_acc_code = $fees->getMainAccCode();
				if(!array_key_exists($main_acc_code, $fees_acc)){
					$main_acc = new Accounts($main_acc_code);
					$fees_acc[$main_acc_code] = $main_acc->title;
				}*/
			}
		}
		if($tr_only){
			return implode('', $trs);
		}

		$accounts = do_query_array("SELECT DISTINCT(CONCAT(main_code, '-', sub_code)) as acc_code, main_code, sub_code FROM school_fees GROUP BY CONCAT(main_code, '-', sub_code)", $sms->database, $sms->ip);
		foreach($accounts as $acc){
			$main_acc = new MainAccounts($acc->main_code);
			$all_fees_acc[$main_acc->full_code] = $main_acc->title;	
		}
		$layout = new Layout($sms);
		$layout->sms_id = $sms->ccid;
		$layout->prvlg_std_read = $prvlg->_chk('std_read')? '' : 'hidden';
		$layout->school_name = $sms->getName();
		$layout->year_name = $year.'/'.($year+1);
		$layout->today = unixToDate(time());
		$layout->template = "modules/fees/templates/school_fees_late_list.tpl";
		$layout->tbody_trs = implode('', $trs);
//		$layout->tfoot_trs = implode('', $tfoot_trs);
//		$levels = $sms->getLevelList();
		$layout->levels_opts = write_select_options(objectsToArray($all_levels), $level_id, true);
		ksort($all_fees_acc);
		$layout->fees_opts = write_select_options($all_fees_acc, '', true);
		
		// dates
		$layout->dates_opts = '';
		foreach($dates as $date){
			$layout->dates_opts .= write_html('option', 'value="'.$date->limit.'"', $date->title);
		}
		return $layout->_print();
	}

	public function loadReservationTable(){
		$students = $this->sms->getReservedStudents();
		$safems = $this->sms->getSafems();
		$trs = array();
		$year = getYear();
		$i = 1;
		foreach($students as $std){
			$student = new Students($std->id, $this->sms);
			$fees = do_query_array("SELECT SUM(value) AS total, SUM(paid) AS tot_paid, currency FROM school_fees WHERE year=$year->year AND std_id=$student->id GROUP BY currency", $safems->database, $safems->ip);
			$first = true;
			foreach($fees as $f){
				$row = new Layout($student);
				$row->sms_id = $this->sms->ccid;
				$row->dues = numberToMoney($f->total);
				$row->paid = numberToMoney($f->tot_paid);
				$row->reste = numberToMoney($f->total - $f->tot_paid);
				$row->currency = $f->currency;
				if($f->tot_paid == 0){
					$row->refund_hidden = 'hidden';
				}
				if($first ){
					$row->template = 'modules/fees/templates/reservation_row.tpl';
					$row->row_span = count($fees);
					$row->name = $student->getName();
					$row->ser = $i;
					$level = $student->getLevel();
					$row->level = $level!=false ? $level->getName():'';
					$i++;
				} else {
					$row->template = 'modules/fees/templates/reservation_row_fees.tpl';
				}
				$trs[] = $row->_print();
				$first = false;
			}
		}
		$layout = new Layout($this->sms);
		$layout->trs = implode('', $trs);
		$layout->template = 'modules/fees/templates/reservation_table.tpl';
		return $layout->_print();
	}
	
			
}