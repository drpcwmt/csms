<?php
/** Salary  
*
*
*/

class Salary{

	public function __construct($emp=''){
		if($emp!= ''){
			$this->emp = $emp;
			$this->salary_basic = $emp->basic;
			$this->salary_var = $emp->var;
		}
	}

	public function getSheet($month){
		global $lang;
		$sheet = new Layout($this->emp);
		$sheet->template = 'modules/salary/templates/salary_sheet.tpl';
		$profil = $this->emp->getProfil();
		$elemts = $profil->getProfilElmnts();
		$calculator = new Calculator($this->emp);
		$dates = Absents::getDateInterval('month', $month);
		$begin = $dates['begin'];
		$end = $dates['end'];
		$calculator->begin = $begin;
		$calculator->end = $end;
		$total_credit = 0;
		$total_debit = 0;
		$credit_trs = array();
		$debit_trs = array();
		foreach($elemts['credit'] as $cs){
			$c = do_query_obj("SELECT * FROM salary_profil_elmnt WHERE id=$cs");
			$value = $calculator->calculate($c->value);
			if($value > 0){
				$credit_trs[] = write_html('tr', '', 	
					write_html('td', 'width="60"', numberToMoney($value)).
					write_html('td', 'width="60"', '&nbsp;').
					write_html('td', '', $c->name)					
				);
				$total_credit += $value;
			}
		}
		foreach($elemts['debit'] as $ds){
			$d = do_query_obj("SELECT * FROM salary_profil_elmnt WHERE id=$ds");
			$value = $calculator->calculate($d->value);
			if($value > 0){
				$debit_trs[] = write_html('tr', '', 	
					write_html('td', 'width="60"', '&nbsp;').
					write_html('td', 'width="60"',numberToMoney($value)).
					write_html('td', '', $d->name)					
				);
				$total_debit += $value;
			}
		}
		$sheet->emp_name = $this->emp->getName();
		$sheet->profil_name =$profil->title;
		$sheet->year = date("Y", $begin);
		$sheet->credit_trs = implode('', $credit_trs);
		$sheet->debit_trs = implode('', $debit_trs);
		$sheet->total_credit = numberToMoney($total_credit);
		$sheet->total_debit = numberToMoney($total_debit);
		$sheet->net = numberToMoney($total_credit - $total_debit);
		$sheet->month = $lang["months_$month"];
		
		return $sheet->_print();
	}
	
	static function loadMainLayout(){
		global $lang;
		$menu = new Layout();
		$menu->template = 'modules/salary/templates/salary_menu.tpl';
		$layout = new Layout();
		$layout->template = 'modules/salary/templates/main_layout.tpl';
		$layout->menu = $menu->_print();
		//$layout->report = Salary::loadSalaryReport();
		return $layout->_print();
	}

	static function loadSalaryReport($profil_id='', $month='', $cc='', $payment_mode='0'){
		global $lang;
		$profils = SalaryProfil::getList();
		$dates = Absents::getDateInterval('month', $month);
		$begin = $dates['begin'];
		$end = $dates['end'];
		$layout = new Layout();
		$layout->template = 'modules/salary/templates/salary_report.tpl';
		
		$first = true;
		$layout->profil_opts = SalaryProfil::getOptions($profil_id);
		$layout->cc_opts = write_select_options(CostcentersGroup::getListOpts(), $cc, true);
		$payment_modes = array('0'=>$lang['all'], 'cash'=>$lang['cash'], 'bank'=>$lang['bank']);
		$layout->payment_mode_select = write_html_select('name="payment_mode" class="combobox"', $payment_modes, $payment_mode);
		/*foreach($profils as $pr){
			$layout->profil_opts .= write_html( 'option', 'value="'.$pr->id.'" '.($first ? 'selected="selected"' : ''),
				$pr->title
			);
			$first = false;
		}*/
		$current_month = date('m');
		$year = getYear();
		$first_month = date('m', $year->begin_date);
		$months = array('0'=>$lang["months_0"]);
		for($i=0; $i<12; $i++){
			$m = $first_month+$i;
			$stamp = mktime(0,0,0, $m, 1, date('Y'));
			$nm = date('m', $stamp);
			$months[$nm] = $lang["months_$nm"];
		}
		$layout->months_select = write_select_options($months, $current_month);

		$profil = new SalaryProfil($profil_id);
		$layout->emps_table = $profil->getEmpTable($begin, $end, $cc, $payment_mode);
		return $layout->_print();		
	}
	
	static function loadInsurReport($job_id='', $month=''){
		global $lang;
		$jobs = Jobs::getList();
		if($job_id == ''){
			$job_id = $jobs[0]->id;
		}
		if($month == '') {$month = date('m');}
		$dates = Absents::getDateInterval('month', $month);
		$begin = $dates['begin'];
		$end = $dates['end'];
		$layout = new Layout();
		$layout->template = 'modules/salary/templates/insur_report.tpl';
		
		$first = true;
		$layout->job_opts = '';
		foreach($jobs as $job){
			$layout->job_opts .= write_html( 'option', 'value="'.$job->id.'" '.($job->id==$job_id ? 'selected="selected"' : ''),
				$job->getName()
			);
			$first = false;
		}
		$current_month = date('m');
		$first_month = 9;
		$months = array('0'=>$lang["months_0"]);
		for($i=0; $i<12; $i++){
			$m = $first_month+$i;
			$stamp = mktime(0,0,0, $m, 1, date('Y'));
			$nm = date('m', $stamp);
			$months[$nm] = $lang["months_$nm"];
		}
		$layout->months_select = write_select_options($months, $month!= '' ? $month : $current_month);

		$job = new Jobs($job_id);
		$emps = $job->getEmps();
		$trs = array();
		$total = 0;
		$total_soc = 0;
		$total_emps =0;
		foreach($emps as $emp){
			if($emp->insur_no != ''){
				$row = new Layout($emp);
				$row->template = 'modules/salary/templates/insur_report_row.tpl';
				$row->name = $emp->getName();
				$row->code = $emp->getAccCode();
				$calc = new Calculator($emp);
				$calc->begin = $begin;
				$calc->end = $end;
				$insur_soc_share = $calc->calcInsur('soc');
				$insur_emp_share = $calc->calcInsur('emp');
				$insur_total = $calc->calcInsur();
				$total += $insur_total;
				$total_soc += $insur_soc_share;
				$total_emps += $insur_emp_share;
				$row->insur_soc_share = numberToMoney($insur_soc_share);
				$row->insur_emp_share = numberToMoney($insur_emp_share);
				$row->insur_total = numberToMoney($insur_total);
				$trs[] = $row->_print();
			}
		}
		$layout->total = numberTomoney($total);
		$layout->total_soc = numberTomoney($total_soc);
		$layout->total_emps = numberTomoney($total_emps);
		$layout->report_trs = implode('', $trs);
		return $layout->_print();
	}
	
	static function salaryEditor($job_id, $cc=''){
		global $this_system;
		$def_cur = $this_system->getSettings('def_currency');
		$job = new Jobs($job_id);
		$emps = $job->getEmps($cc);
		$curs = Currency::getList();
		$profils = SalaryProfil::getList();
		$trs = array();
		foreach($emps as $emp){
			$row = new Layout($emp);
			$row->template = 'modules/salary/templates/salary_editor_row.tpl';
			$row->name = $emp->getName();
			$row->join_date = unixToDate($emp->join_date);
			$row->code = $emp->getAccCode();
			$row->basic_cur_opts = write_select_options( $curs, ($emp->basic_cur!= '' ? $emp->basic_cur : $def_cur), false);
			$row->var_cur_opts = write_select_options( $curs, ($emp->var_cur!= '' ? $emp->var_cur : $def_cur), false);
			$row->allowances_cur_opts = write_select_options( $curs, ($emp->allowances_cur!= '' ? $emp->allowances_cur : $def_cur), false);
			$row->profil_opts = SalaryProfil::getOptions($emp->profil_id);
			$trs[] = $row->_print();
		}
		$layout = new Layout($job);
		$layout->job_name = $job->getName();
		$jobs = Jobs::getList();
		$layout->job_opts = '';
		foreach($jobs as $j){
			$layout->job_opts .= write_html( 'option', 'value="'.$j->id.'" '.($j->id == $job_id ? 'selected="selected"' : ''),
				$j->getName()
			);
			$first = false;
		}
		$layout->ccs_opts = write_select_options(CostcentersGroup::getListOpts(), $cc, true);
		$layout->count_emps = count($emps);
		$layout->template = 'modules/salary/templates/salary_editor.tpl';
		$layout->trs = implode('', $trs);
		
		return $layout->_print();
	}
	
	static function saveSalaryEditor($post){
		$succses = true;
		if(isset($post['emp_id'])){
			for($i=0;  $i<count($post['emp_id']); $i++){
				$emp_id = $post['emp_id'][$i];
				$row = array(
					'profil_id'=>$post['profil_id'][$i]
				);
				if($post['basic'][$i] != ''){
					$row['basic']= $post['basic'][$i];
					$row['basic_cur']= $post['basic_cur'][$i];
				}
				if($post['var'][$i] != ''){
					$row['var']= $post['var'][$i];
					$row['var_cur']= $post['var_cur'][$i];
				}
				if($post['allowances'][$i] != ''){
					$row['allowances']= $post['allowances'][$i];
					$row['allowances_cur']= $post['allowances_cur'][$i];
				}
				if(do_query_obj("select profil_id FROM salary WHERE emp_id=$emp_id") != false){
					$result = do_update_obj($row, "emp_id=$emp_id", "salary");
				} else {
					$row['emp_id'] = $emp_id;
					$result = do_insert_obj($row, 'salary');
				}
				if($result == false){
					$succses = false;
				}
			}
		} else {
			$succses = false;
		}
		return json_encode_result($succses);
	}
	
	static function TotalTable(){
		
		$accs = do_query_array("SELECT main_code, sub_code FROM salary_profil_elmnt GROUP BY main_code, sub_code");
		foreach( $accs as $acc){
			$elmnts_debit = do_query_array(
				"SELECT salary_profil_elmnt,* FROM salary_profil_elemt, salary_pr_el 
				WHERE salary_profil_elemnt.main_code=$acc->main_code
				AND salary_profil_elemnt.sub_code=$acc->sub_code
				AND salary_profil_elmnt.id=salary_pr_el.elmnt_id
				AND salary_pr_el.type='debit'");
		}
	}
}
				