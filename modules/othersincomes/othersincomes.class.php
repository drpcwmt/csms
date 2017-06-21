<?php
/** Other Incomes
*
*/
class OthersIncomes{
	
	public static $expenses_main_acc = '39';
	public static $incomes_main_acc = '49';
	
	
	public function __construct($id=''){
		global $lang;
		$this->exists = false;
		if($id != ''){	
			$income = do_query_obj("SELECT * FROM others_incomes WHERE id=$id");	
			if(isset( $income->id )){
				foreach($income as $key =>$value){
					$this->$key = $value;
				}
				$this->exists = true;
				$this->expenses_acc = new Accounts($this->expenses_acc);
				$this->incomes_acc = new Accounts($this->incomes_acc);
			}	
		}	
	}

	public function loadLayout(){
		global $lang, $this_system;
		$layout = new Layout($this);
		$layout->settings_tab = $this->getSettingsTab();			
		if($this_system->type == 'accms'){
			$layout->template = "modules/othersincomes/templates/income_layout.tpl";
			$layout->summary_table = $this->getSummarryTable();
			$layout->transaction_table = $this->getTransactionTable();
		} else {
			$layout->template = "modules/othersincomes/templates/safems_layout.tpl";
			$layout->incomes_acc_title = $this->incomes_acc->title;			
			$layout->expenses_acc_title = $this->expenses_acc->title;	
			$layout->members_trs = $this->getMembersTable();
			$prices = $this->getPrices();
			$ths = array();
			foreach($prices as $cur=>$value){
				$ths[] = write_html('th', 'width="80"', numberToMoney($value)." ". $cur);	
			}
			$layout->count_curs = count($prices);
			$layout->cur_trs = implode('', $ths);
		}
		return $layout->_print();
	}
	
	public function getPricesTable(){
		global $safems;
		$layout= new Layout();
		$layout->template = 'modules/othersincomes/templates/prices_table.tpl';
		$ps = do_query_array("SELECT * FROM others_incomes_prices WHERE act_id=$this->id", $safems->database, $safems->ip);
		$curs = Currency::getList();	
		for($i=1; $i<=3; $i++){
			$layout->{"value-$i"} = $ps != false && isset($ps[$i-1]) ? $ps[$i-1]->value : '';
			$layout->{"currency-$i"} = write_select_options($curs, ($ps != false && isset($ps[$i-1]) ? $ps[$i-1]->currency : $safems->getSettings('def_currency')));
			$layout->{"title-$i"} = $ps != false && isset($ps[$i-1]) ? $ps[$i-1]->title: '';
		}
		return $layout->_print();
	}
	
	public function getSummarryTable(){
		$year = getYear();
		$rows = array();
		$expenses_acc = $this->expenses_acc;
		$incomes_acc = $this->incomes_acc;
		$total_expenses = do_query_array("SELECT SUM(transactions_rows.debit * transactions_rows.rate) AS egp_debit, 
			SUM(transactions_rows.credit * transactions_rows.rate) AS egp_credit, 
			SUM(transactions_rows.debit) AS debit, 
			SUM(transactions_rows.credit) AS credit,
			transactions.currency
			FROM transactions, transactions_rows 
			WHERE transactions_rows.trans_id=transactions.id
			AND transactions_rows.year>=$year->year 
			AND transactions_rows.main_code = '$expenses_acc->main_code'
			AND transactions_rows.sub_code = '$expenses_acc->sub_code'
			GROUP BY transactions.currency");
		foreach($total_expenses as $tot){
			$rows[$tot->currency]['expense'] = $tot->debit - $tot->credit;
		}

		$total_incomes = do_query_array("SELECT SUM(transactions_rows.debit * transactions_rows.rate) AS egp_debit, 
			SUM(transactions_rows.credit * transactions_rows.rate) AS egp_credit, 
			SUM(transactions_rows.debit) AS debit, 
			SUM(transactions_rows.credit) AS credit,
			transactions.currency
			FROM transactions, transactions_rows 
			WHERE transactions_rows.trans_id=transactions.id
			AND transactions_rows.year>=$year->year 
			AND transactions_rows.main_code = '$incomes_acc->main_code'
			AND transactions_rows.sub_code = '$incomes_acc->sub_code'
			GROUP BY transactions.currency");
		foreach($total_incomes as $tot){
			$rows[$tot->currency]['income'] = $tot->credit - $tot->debit;
		}
		$trs = array();
		foreach($rows as $cur=>$array){
			$exp = isset($array['expense']) ? $array['expense'] : 0;
			$inc = isset($array['income']) ? $array['income'] : 0;
			$trs[] = write_html('tr', '',
				write_html('td', '', $cur).
				write_html('td', '', $exp).
				write_html('td', '', $inc).
				write_html('td', '', $inc - $exp)
			);
		}
		
		$layout = new Layout($this);
		$layout->template = "modules/othersincomes/templates/summary_table.tpl";
		$layout->totals_trs = implode('', $trs);		
		return $layout->_print();
	}
	
	public function getTransactionTable(){
		$layout = new Layout($this);
		$layout->template = "modules/othersincomes/templates/transactions_table.tpl";
		$expsense_trs = $this->expenses_acc->getTransactionsTable(unixToDate($year->begin_date), unixToDate($year->end_date), true);
		$incomes_trs = $this->incomes_acc->getTransactionsTable(unixToDate($year->begin_date), unixToDate($year->end_date), true);
		$layout->transactions_trs = (strpos($expsense_trs, 'ui-state-error')===false ? $expsense_trs: '').(strpos( $incomes_trs, 'ui-state-error')===false ? $incomes_trs: '');
		return $layout->_print();		
	}
	
	public function getSettingsTab(){
		global $this_system, $prvlg;
		$details = new Layout($this);
		$details->template = "modules/othersincomes/templates/details.tpl";
		$parent_code = substr($this->expenses_acc->full_code,2,3);
		$details->type_opts = write_select_options(OthersIncomes::getTypes(), '49'.$parent_code, false);
		$details->cc_opts = write_select_options(CostCentersGroup::getListOpts(), $this->cc, false);
		$details->expenses_code_main = Accounts::fillZero('main', $this->expenses_acc->main_code);
		$details->expenses_code_sub = Accounts::fillZero('sub', $this->expenses_acc->sub_code);
		$details->expenses_name = $this->expenses_acc->title;
		$details->incomes_code_main = Accounts::fillZero('main', $this->incomes_acc->main_code);
		$details->incomes_code_sub = Accounts::fillZero('sub', $this->incomes_acc->sub_code);
		$details->incomes_name = $this->incomes_acc->title;
		$details->hidden = $prvlg->_chk('edit_activity') ? '' : 'hidden';
		if($this_system->type == 'safems') {
			$details->prices_table = $this->getPricesTable();
		}
		return $details->_print();
	}
	
	public function getMembers(){
		global $safems;
		$out = array();
		$membs = do_query_array("SELECT * FROM others_incomes_members WHERE act_id=$this->id", $safems->database, $safems->ip);
		if($membs != false){
			foreach($membs as $memb){
				$out[] = new Students($memb->std_id, new SMS($memb->cc_id));
			}
		}
		return $out;
	}
	
	public function getMembersTable(){
		global $lang, $safems;
		$members = $this->getMembers();
		$trs = array();
		foreach($members as $mem){
			$trs[] = $this->getTableMemberRow($mem);
		}
		// totals
		$prices = $this->getPrices();
		$incomes_acc = $this->incomes_acc;
		$total_td = array();
		foreach($prices as $cur=>$price){
			$in = do_query_obj("SELECT SUM(value) AS val FROM ingoing WHERE to_main_code=$incomes_acc->main_code AND to_sub_code=$incomes_acc->sub_code AND currency='$cur'", $safems->database, $safems->ip);
			$out = do_query_obj("SELECT SUM(value) AS val FROM outgoing WHERE from_main_code=$incomes_acc->main_code AND from_sub_code=$incomes_acc->sub_code AND currency='$cur'", $safems->database, $safems->ip);
			$total_td[] = write_html('td', ' align="center"',  
				write_html('b', '',  $in->val - $out->val)
			);
		}
		$trs[] = write_html('tr', '',
			write_html('td', 'colspan="5"', $lang['total']).
			implode('', $total_td)
		);
		return implode('', $trs);
	}
	
	public function getTableMemberRow($mem){
		global $lang;
		$sms = $mem->sms;
		$safems = $sms->getSafems();
		$prices = $this->getPrices();
		$incomes_acc = $this->incomes_acc;
		$paing = false;
		$paid_infull = true;
		$prices_td = array();
		$std_acc = $account = new Accounts($mem->getAccCode());;
		foreach($prices as $cur=>$price){
			$in = do_query_obj("SELECT SUM(value) AS val FROM ingoing WHERE from_main_code=$std_acc->main_code AND from_sub_code=$std_acc->sub_code AND to_main_code=$incomes_acc->main_code AND to_sub_code=$incomes_acc->sub_code AND currency='$cur'", $safems->database, $safems->ip);
			$out = do_query_obj("SELECT SUM(value) AS val FROM outgoing WHERE from_main_code=$incomes_acc->main_code AND from_sub_code=$incomes_acc->sub_code AND to_main_code=$std_acc->main_code AND to_sub_code=$std_acc->sub_code AND currency='$cur'", $safems->database, $safems->ip);
			$paid = $in->val - $out->val;
			$prices_td[] = write_html('td', 'align="center"', 
				write_html('span', 'class="'.($paid<$price ? 'ui-state-error': '').'" style="padding:0px 2px"', numberToMoney($paid))
				 
			);
			if($paid>0 ){$paing= true;}
			if($paid<$price || $paid==0){
				$paid_infull = false;
			}
		}
		$class = $mem->getClass();
		$tr = write_html('tr', '',
			write_html('td', '',
				($paing == false ? write_html('button', 'class="circle_button ui-state-default hoverable" action="removeMember" act_id="'.$this->id.'" std_id="'.$mem->id .'" cc_id="'.$mem->sms->ccid.'" title="'.$lang['remove'].'"', write_icon('trash')): '&nbsp;')
			).
			write_html('td', '',
				($paing == true ? write_html('button', 'class="circle_button ui-state-default hoverable" action="refundActivity" act_id="'.$this->id.'" std_id="'.$mem->id .'" cc_id="'.$mem->sms->ccid.'" title="'.$lang['refund'].'"', write_icon('arrowreturnthick-1-w')): '&nbsp;')				
			).
			write_html('td', '',
				($paid_infull == false ? write_html('button', 'class="circle_button ui-state-default hoverable" action="payActivity" act_id="'.$this->id.'" std_id="'.$mem->id .'" cc_id="'.$mem->sms->ccid.'" title="'.$lang['add'].'"', write_icon('plus')): '&nbsp;')
			
			).
			write_html('td', '', $mem->getName()).
			write_html('td', '', $class!= false ? $class->getName() : '').
			implode('', $prices_td)
		);
		return $tr;
	}
	
	public function getPrices(){
		global $safems;
		$out = array();
		$ps = do_query_array("SELECT * FROM others_incomes_prices WHERE act_id=$this->id", $safems->database, $safems->ip);
		if($ps != false){
			foreach($ps as $p){
				$out[$p->currency]=$p->value;
			}
		} else {
			$out['EGP'] =0;
		}
		return $out;
	}
	
	static function getTypes(){
		$out = array();
		$main_acc = OthersIncomes::$incomes_main_acc;
		$accs = do_query_array("SELECT * FROM codes WHERE code LIKE '$main_acc%' AND level=2");
		if($accs != false && count($accs) > 0){
			foreach($accs as $acc){
				$out[$acc->code] = $acc->title;
			}
		}
		return $out;
	}

	static function getList(){
		return do_query_array("SELECT * FROM others_incomes WHERE status=1");
		
	}
	
	static function loadMainLayout(){
		global $prvlg, $this_system, $lang;
		$layout = new Layout();
		$layout->template = "modules/othersincomes/templates/main_layout.tpl";
		
		$layout->incomes_list = '';
		$layout->hidden = $prvlg->_chk('add_activity') ? '' : 'hidden';
		if($this_system->type == 'safems'){
			$layout->sync_button = write_html('a', 'action="syncActivitys"', $lang['sync']. write_icon('refresh'));
		}
		$incomes = OthersIncomes::getList();
		if($incomes != false){
			$first = true;
			foreach($incomes as $inc){
				$layout->incomes_list .= write_html( 'li', 'income_id="'.$inc->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openIncome"', 
					write_html('text', 'class="holder-income'.$inc->id.'"',
						$inc->title
					)
				);	
				if($first){
					$first_income = new OthersIncomes($inc->id);
					$layout->incomes_content = $first_income->loadLayout();
					$first = false;
				}
			}
		}
		return $layout->_print();
	}
	
	static function newIncomeForm(){
		$layout = new Layout();
		$layout->type_opts = write_select_options(OthersIncomes::getTypes(), '', false);
		$layout->cc_opts = write_select_options(CostCentersGroup::getListOpts(), '17', false);
		$layout->template = "modules/othersincomes/templates/details.tpl";
		return $layout->_print();
	}

	static function _save($post){
		global $lang, $prvlg, $safems;
		$result = new stdClass();
		if($prvlg->_chk('edit_activity') == false && $prvlg->_chk('edit_activity_price') == false){
			$result->error = $lang['no_privileges'];
		} else {
			if(isset($post['id']) && $post['id'] != ''){
				$post['expenses_acc'] =$post['expenses_code_main'].$post['expenses_code_sub'];
				$post['incomes_acc'] =$post['incomes_code_main'].$post['incomes_code_sub'];
				if($prvlg->_chk('edit_activity')){
					if( do_update_obj($post, 'id='.$post['id'], 'others_incomes') != false){
						$result->error = '';
						$id = $post['id'];
					}
				}
				if($prvlg->_chk('edit_activity_price')){
					do_delete_obj("act_id=".$post['id'], 'others_incomes_prices', $safems->database, $safems->ip);
					for($i=0; $i<count($post['value']); $i++){
						if($post['value'][$i]!='' && $post['value'][$i]!=0){
							do_insert_obj(
								array('act_id'=>$post['id'], 
									'value'=>$post['value'][$i],
									'currency'=>$post['currency'][$i],
									'title'=>$post['notes'][$i]
								), 'others_incomes_prices', $safems->database, $safems->ip);
						}
					}
					$result->error = '';
				}
			} elseif(!isset($post['id']) || $post['id'] == ''){
				// without accounts
				if($post['expenses_code_main'] == ''){
					$sub = do_query_obj("SELECT sub FROM sub_codes WHERE main LIKE '".$post['parent']."%'  ORDER BY sub DESC LIMIT 1");
					if(isset($sub->sub)){
						$new_sub = $sub->sub +1;
					} else {
						$new_sub = 1;
					}
					$expenses_main_acc = substr_replace($post['parent'], '39',0,2);
					$expenses_acc = array('precode'=> Accounts::removeZero( $expenses_main_acc),
						'acc_code_sub'=> $new_sub,
						'title'=> $lang['expenses'].' / '.$post['title'],
						'group_id'=> $post['cc']
					);
					$result = json_decode(Accounts::saveNewAcc($expenses_acc));
					$incomes_main_acc = $post['parent'];
					if($result->error == ''){
						$incomes_acc = array('precode'=> Accounts::removeZero( $incomes_main_acc),
							'acc_code_sub'=> $new_sub,
							'title'=> $lang['incomes'].' / '.$post['title'],
							'group_id'=> $post['cc']
						);
						$result = json_decode(Accounts::saveNewAcc($incomes_acc));
					}
					$post['expenses_acc'] = Accounts::fillZero('main', $expenses_main_acc).Accounts::fillZero('sub', $new_sub);
					$post['incomes_acc'] =  Accounts::fillZero('main', $incomes_main_acc).Accounts::fillZero('sub', $new_sub);
				} else {
					$post['expenses_acc'] =$post['expenses_code_main'].$post['expenses_code_sub'];
					$post['incomes_acc'] =$post['incomes_code_main'].$post['incomes_code_sub'];
				}
				if($id = do_insert_obj($post, 'others_incomes')){
					$result->error = '';
				} else {
					$result->error = $lang['error'];
				}
			}
		}

		if($result->error == ''){
			$answer['id'] = $id;
			$answer['title'] = $post['title'];
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function newMember($act_id){
		$form = new Layout();
		$form->template = 'modules/othersincomes/templates/new_member.tpl';
		$form->act_id = $act_id;
		$ccs = SMS::getList();
		$form->schools_opts = '';
		$first = true;
		foreach($ccs as $cc){
			$form->schools_opts .= write_html('option', 'value="'.$cc->id.'"', $cc->code);
			$form->sms_id = $cc->id;
		}
		return $form->_print();	
	}
	static function saveMember($post){
		global $lang;
		$std_id = $post['std_id'];
		$act_id = $post['act_id'];
		//if(!do_query_obj("SELECT std_id FROM others_incomes_members WHERE std_id=$std_id AND act_id=$act_id")){
			if(do_insert_obj($post, 'others_incomes_members')){
				$answer = array();
				$member = new Students($std_id, new SMS($post['cc_id']));
				$incomes = new OthersIncomes($act_id);
				$answer['html'] = $incomes->getTableMemberRow($member);
				$answer['error'] = '';
				return $answer;
			} else {
				return false;
			}	
		/*} else {
			$answer['error'] = $lang['allready_exists'];
			return $answer;
		}*/
	}
	
	static function removeMember($post){
		global $safems;
		return do_delete_obj("act_id=".$post['act_id']." AND std_id=".$post['std_id']." AND cc_id=".$post['cc_id'], 'others_incomes_members', $safems->database, $safems->ip);
	}
	
	static function newPayForm($act_id, $std_id, $cc_id){
		global $safems;
		$layout = new Layout();
		$layout->template = 'modules/othersincomes/templates/newpay.tpl';
		$layout->act_id=$act_id;
		$layout->std_id=$std_id;
		$layout->cc_id=$cc_id;		
		$layout->date = unixToDate(time());
		$layout->curs_opts =Currency::getOptions($safems->getSettings('def_currency'));	
		return $layout->_print();
	}
	
	static function savePay($post){
		$activity = new OthersIncomes($post['act_id']);
		$student = new Students($post['std_id'], new SMS($post['cc_id']));
		$in_array = array(
			'from' => $student->getAccCode(),
			'to' => $activity->incomes_acc->full_code,
			'ccid' => $post['cc_id'],
			'value' => $post['value'],
			'date' => dateToUnix($post['date']),
			'currency' => $post['currency'],
			'type' => 'cash',
			'notes' => $activity->title
		);
		$ingoing = Ingoing::addNewPayment($in_array);
		if($ingoing != false){
			$recete = $ingoing->getRecete();
			echo json_encode(array('error'=>'', 'recete' => $recete));
		} else {
			echo json_encode_result(false);	
		}
	}
	
	static function refundPay($post){
		global $lang;
		$activity = new OthersIncomes($post['act_id']);
		$student = new Students($post['std_id'], new SMS($post['cc_id']));
		$in_array = array(
			'to' => $student->getAccCode(),
			'from' => $activity->incomes_acc->full_code,
			'ccid' => $post['cc_id'],
			'value' => $post['value'],
			'currency' => $post['currency'],
			'type' => 'cash',
			'notes' => $lang['refund'].": ".$activity->title
		);
		$outgoing = Outgoing::addNewPayment($in_array);
		if($outgoing != false){
			$recete = $outgoing->getRecete();
			echo json_encode(array('error'=>'', 'recete' => $recete));
		} else {
			echo json_encode_result(false);	
		}
	}
	
	static function syncActivitys(){
		global $accms, $safems;
		$acc_activitys = do_query_array("SELECT * FROM others_incomes", $accms->database, $accms->ip);
		if($acc_activitys!=false){
			foreach($acc_activitys as  $act){
				if(do_query_obj("SELECT id FROM others_incomes WHERE id=$act->id", $safems->database, $safems->ip) ==false){
					do_insert_obj($act, 'others_incomes', $safems->database, $safems->ip);
				} else {
					do_update_obj($act, "id=$act->id", 'others_incomes', $safems->database, $safems->ip);					
				}
			}
		}
		return true;
	}
}
