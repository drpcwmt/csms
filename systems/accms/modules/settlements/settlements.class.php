<?php
/** Settlement
*
*/
class Settlements{
	public $title, $date, $currency, $user;
	public $rows = array();

	public function __construct($id=''){
		global $lang;
		$this->exists = false;
		if($id != ''){	
			$settl = do_query_obj("SELECT * FROM transactions WHERE id=$id", MySql_Database);	
			if(isset( $settl->id )){
				foreach($settl as $key =>$value){
					$this->$key = $value;
				}
				$this->exists = true;
			}	
		}	
	}
		
	public function getRows(){
		if(count($this->rows) == 0){
			$this->rows = do_query_array("SELECT * FROM transactions_rows WHERE trans_id=$this->id", MySql_Database);	
		} 
		return $this->rows;
	}
	
	static function loadLayout($id){
		global $this_system, $lang;
		$user = new Users($_SESSION['group'], $_SESSION['user_id']);
		$cc_list = CostCentersGroup::getListOpts();
		$add_tr = write_html('tr','', 
			write_html('td', 'class="unprintable"',
				write_html('button', 'type="button" class="ui-state-default hoverable circle_button" action="addTrans"', write_icon('plus'))
			).
			write_html('td', 'colspan="6"', '&nbsp;')
		);
		if($id>0){
			$settl = new Settlements($id);
			if($settl->exists == true){
				if($settl->currency == '0'){
					return Settlements::openExchange($settl);
				} 
				$user = new Users('', $settl->user_id);
				$settl->user_name = $user->getRealName();
				$rows = $settl->getRows();
				$settl->settlement_rows = '';
				$total_trans = 0;
				foreach($rows as $r){
					$layout = new Layout($r);
					$layout->template = "modules/settlements/templates/settlements_row.tpl";
					$acc = new Accounts(Accounts::fillZero('main', $r->main_code).Accounts::fillZero('sub', $r->sub_code));
					$layout->sub_code = Accounts::fillZero('sub', $r->sub_code);
					$layout->main_code = Accounts::fillZero('main', $r->main_code);
					$layout->notes = str_replace('<br/>', '&#13;&#10;', $r->notes);
					$layout->title = $acc->title;
					//$layout->debit = numberToMoney($r->debit);
					//$layout->credit = numberToMoney($r->credit);
					$layout->cc_opts = write_select_options($cc_list, $r->cc, false);
					$settl->settlement_rows .= $layout->_print();
					$total_trans += $r->debit;
				}
				$settl->total_trans = numberToMoney($total_trans);
				$settl->rate = $r->rate;
				$settl->approve_on = $settl->approve > 0 ? 'checked="checked"' : '';
				$settl->approve_off = $settl->approve == '0' ? 'checked="checked"' : '';	
				$settl->rate_hidden = $settl->currency =='EGP' ? 'hidden' : '';	
				if($settl->approve == '0' ){
					$settl->settlement_rows .=$add_tr;
				}
				$settl->approve = $settl->approve != 0 ? $settl->approve : $_SESSION['user_id'] ;
			} else {
				return write_error($lang['result_not_found']);
			}
		} else {
			$settl = new stdClass();
			$settl->id= 'new';
			$settl->id_hidden = 'hidden';
			$settl->approve = $_SESSION['user_id'] ;
			$settl->user_id = $_SESSION['user_id'];
			$settl->user_name = $user->getRealName();
			$settl->date = unixToDate(time());
			$settl->settlement_rows = '';
			for($i=1; $i<=5; $i++){
				$layout = new layout();
				$layout->cc_opts = write_select_options($cc_list, '', false);
				$layout->template = "modules/settlements/templates/settlements_row.tpl";
				$settl->settlement_rows .= $layout->_print();
			}
			$settl->currency = $this_system->getSettings('def_currency');
			$settl->approve_off = 'checked="checked"';
			$settl->rate_hidden = 'hidden';
			$settl->rate = 1;
			$settl->settlement_rows .= $add_tr;
		}
			
		
		if($user->getPrvlg('approve_transaction')){
			$settl->approve_opt = 'hidden';
		}
		
		$settl->currency_options = Currency::getOptions($settl->currency);	
		
		return fillTemplate("modules/settlements/templates/settlements.tpl", $settl);
	}
	
	static function getCcTransaction($cc, $begin_date='', $end_date=''){
		$sql = "SELECT * FROM transactions_rows	WHERE cc=$cc";
		if($begin_date=="" ){
			$sql .= " AND year =".$_SESSION['year'];
		} else {
			$sql .= " AND date>= $begin_date AND date <=$end_date";
		}
		$rows = do_query_obj($sql, MySql_Database);
		return filleMergedTemplate("modules/settlements/templates/settlements_row.tpl", $rows);
		
	}
	
	static function _save($post){
		$result = false;
		if(isset($post['id']) && $post['id'] != 'new'){
			if(isset($post['date'])){
				$post['date'] = dateToUnix($post['date']);
			}
			if( do_update_obj($post, 'id='.$post['id'], 'transactions') != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(!isset($post['id']) || $post['id'] == 'new'){
			unset($post['id']);
			$result = do_insert_obj($post, 'transactions');
			$id = $result;
		} elseif( isset($post['approve']) && $post['approve'] == '0'){
			do_query_edit("UPDATE transactions SET approve=0 WHERE trans_id=$id");
			
		}
		
		if($result!=false ){
			if( isset($post['acc_code_main'])){
				$post['trans_id'] = $id;
				do_query_edit("DELETE FROM transactions_rows WHERE trans_id=$id");
				for($i=0; $i<count($post['acc_code_main']); $i++){
					if($post['acc_code_main'][$i] != ''){
						$ins = array(
							'trans_id'=> $id,
							'approve'=> $post['approve'],
							'debit'=> $post['debit'][$i],
							'credit'=> $post['credit'][$i],
							'main_code'=> Accounts::removeZero( $post['acc_code_main'][$i]),
							'sub_code'=> $post['acc_code_sub'][$i],
							'cc'=> $post['acc_code_cc'][$i]!= '' ? $post['acc_code_cc'][$i] : 0,
							'notes'=> $post['notes'][$i],
							'year'=> $_SESSION['year'],
							'currency'=> $post['currency'],
							'rate'=> $post['currency'] == 'EGP' ? 1 : $post['rate']
						);
						do_insert_obj($ins, 'transactions_rows');	
					}
				}
			} elseif( isset($post['approve']) && $post['approve'] == 0){
				do_query_edit("UPDATE transactions_rows SET approve=0 WHERE trans_id=$id");
				
			}
			$trans = new Settlements($id);			
			$row = new Layout($trans);
			$row->lock = $trans->approve == 0 ? 'unlocked' : 'locked';
			$trans_user = new Users('', $trans->user_id);
			$row->user = $trans_user->getRealName();
			
			$descrip_tr = array();
			$total = 0;
			$details = $trans->getRows();
			foreach($details as $r){
				$acc = new Accounts(Accounts::fillZero('main', $r->main_code).Accounts::fillZero('sub', $r->sub_code));
				$total += $r->debit;
				$row->total = numberToMoney($total);
				$descrip_tr[] = write_html('tr', '',
					write_html('td', 'width="80"', numberToMoney($r->debit)).
					write_html('td', 'width="80"', numberToMoney($r->credit)).
					write_html('td', '', isset($acc->title) ?  $acc->title: Accounts::fillZero('main', $r->main_code).Accounts::fillZero('sub', $r->sub_code))
				);
			}
			$row->descript = write_html('table', 'cellspacing="1" cellpadding="0" bgcolor="#CDCDCD" width="100%"', implode('', $descrip_tr));
			$row->date_str = date('d/m/Y h:m', $row->date);
			$row->template = 'modules/settlements/templates/settlements_list_row.tpl';
			$answer['tr'] = $row->_print();
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function openExchange($settl){
		$user = new Users('', $settl->user_id);
		$settl->user_name = $user->getRealName();
		$rows = $settl->getRows();
		$settl->settlement_rows = '';
		foreach($rows as $r){
			$layout = new Layout($r);
			$layout->template = "modules/settlements/templates/settlements_exchange_row.tpl";
			$acc = new Accounts(Accounts::fillZero('main', $r->main_code).Accounts::fillZero('sub', $r->sub_code));
			$layout->sub_code = Accounts::fillZero('sub', $r->sub_code);
			$layout->main_code = Accounts::fillZero('main', $r->main_code);
			$layout->title = $acc->title;
			$layout->currency_options = Currency::getOptions($r->currency);	
			$settl->settlement_rows .= $layout->_print();
		}
		$settl->rate = $r->rate;
		$settl->approve_on = $settl->approve > 0 ? 'checked="checked"' : '';
		$settl->approve_off = $settl->approve == '0' ? 'checked="checked"' : '';	
		$settl->rate_hidden = $settl->currency =='EGP' ? 'hidden' : '';	
		$settl->approve = $settl->approve != 0 ? $settl->approve : $_SESSION['user_id'] ;
		
		$layout = new Layout($settl);
		$layout->template = "modules/settlements/templates/settlements_exchange.tpl";
		return $layout->_print();
	}
	
	static function newExchange(){
		global $this_system, $lang;
		$user = new Users($_SESSION['group'], $_SESSION['user_id']);
		$settl = new Layout();
		$settl->template = 'modules/settlements/templates/settlements_exchange.tpl';
		$settl->id= '';
		$settl->id_hidden = 'hidden';
		$settl->approve = $_SESSION['user_id'] ;
		$settl->user_id = $_SESSION['user_id'];
		$settl->user_name = $user->getRealName();
		$settl->date = unixToDate(time());
		$settl->settlement_rows = '';
		
		for($i=1; $i<=5; $i++){
			$layout = new layout();
			$layout->template = "modules/settlements/templates/settlements_exchange_row.tpl";
			$layout->currency_options = write_html('option', '', ' ').Currency::getOptions();
			$settl->settlement_rows .= $layout->_print();
		}
		$settl->currency = $this_system->getSettings('def_currency');
		$settl->approve_off = 'checked="checked"';
		$settl->rate_hidden = 'hidden';
		$settl->rate = 1;
		return $settl->_print();
	}
	
	static function saveExchange($post){
		$result = false;
		$post['currency'] = 0;
		if(isset($post['id']) && $post['id'] != ''){
			if(isset($post['date'])){
				$post['date'] = dateToUnix($post['date']);
			}
			if( do_update_obj($post, 'id='.$post['id'], 'transactions') != false){
				$result = true;
				$id = $post['id'];
			}
		} elseif(!isset($post['id']) || $post['id'] == ''){
			$result = do_insert_obj($post, 'transactions');
			$id = $result;
		} elseif( isset($post['approve']) && $post['approve'] == '0'){
			do_query_edit("UPDATE transactions SET approve=0 WHERE trans_id=$id");
			
		}
		
		if($result!=false ){
			if( isset($post['acc_code_main'])){
				$post['trans_id'] = $id;
				do_query_edit("DELETE FROM transactions_rows WHERE trans_id=$id");
				for($i=0; $i<count($post['acc_code_main']); $i++){
					if($post['acc_code_main'][$i] != ''){
						$ins = array(
							'trans_id'=> $id,
							'approve'=> $post['approve'],
							'debit'=> $post['debit'][$i],
							'credit'=> $post['credit'][$i],
							'main_code'=> Accounts::removeZero( $post['acc_code_main'][$i]),
							'sub_code'=> $post['acc_code_sub'][$i],
							'cc'=> 17,
							'notes'=> $post['notes'],
							'year'=> $_SESSION['year'],
							'currency'=> $post['currency'][$i],
							'rate'=> ($post['currency'][$i]=='EGP' ? '1.00' : $post['rate'][$i])
						);
						do_insert_obj($ins, 'transactions_rows');	
					}
				}
			} elseif( isset($post['approve']) && $post['approve'] == 0){
				do_query_edit("UPDATE transactions_rows SET approve=0 WHERE trans_id=$id");
				
			}
			$trans = new Settlements($id);			
			$row = new Layout($trans);
			$row->lock = $trans->approve == 0 ? 'unlocked' : 'locked';
			$trans_user = new Users('', $trans->user_id);
			$row->user = $trans_user->getRealName();
			
			$descrip_tr = array();
			$total = 0;
			$details = $trans->getRows();
			foreach($details as $r){
				$acc = new Accounts(Accounts::fillZero('main', $r->main_code).Accounts::fillZero('sub', $r->sub_code));
				$total += $r->debit;
				$row->total = numberToMoney($total);
				$descrip_tr[] = write_html('tr', '',
					write_html('td', 'width="80"', numberToMoney($r->debit)).
					write_html('td', 'width="80"', numberToMoney($r->credit)).
					write_html('td', '', isset($acc->title) ?  $acc->title: Accounts::fillZero('main', $r->main_code).Accounts::fillZero('sub', $r->sub_code))
				);
			}
			$row->descript = write_html('table', 'cellspacing="1" cellpadding="0" bgcolor="#CDCDCD" width="100%"', implode('', $descrip_tr));
			$row->date_str = date('d/m/Y h:m', $row->date);
			$row->template = 'modules/settlements/templates/settlements_list_row.tpl';
			$answer['tr'] = $row->_print();
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}
	
	static function getList($post){
		global $lang;
		$trs = array();
		$layout = new Layout();
		$layout->template = 'modules/settlements/templates/settlements_list.tpl';
		if(!isset($post['date'])){
			$layout->form_hidden='hidden';
		} else {
			$layout->today = $post['date'];
		}

		$sql = "SELECT transactions.* FROM transactions, transactions_rows WHERE transactions.id=transactions_rows.trans_id";
		// dates
		if(isset($post['date'])){
			$begin = dateToUnix($post['date']);
			$end = $begin + ( 60*60*24);
			$sql .= " AND (transactions.date>=$begin AND transactions.date<$end)";
		} elseif(isset($post['begin_date']) && $post['begin_date']!=''){
			$begin = dateToUnix($post['begin_date']);
			if($post['end_date']!= ''){
				$end = dateToUnix($post['end_date']);
			} else {
				$end = mktime(0,0,0, date('m'), date('d'), date('Y'))+( 60*60*24);
			}
			$sql .= " AND (transactions.date>=$begin AND transactions.date<$end)";
		} else {
			$begin = getYearSetting('begin_date');
			$end = getYearSetting('end_date');
			$sql .= " AND (transactions.date>=$begin AND transactions.date<$end)";
		}
			// Users
		if(isset($post['user_id']) && $post['user_id']!=''){
			$sql .= " AND transactions.user_id=".$post['user_id'];
		}
			// Currency
		if(isset($post['currency']) && trim($post['currency'])!=''){
			$sql .= " AND transactions.currency='".$post['currency']."'";
		}
		
			// Account
		if(isset($post['main_code']) && $post['main_code']!=''){
			$sql .= " AND transactions_rows.main_code=".Accounts::removeZero($post['main_code']);
		}
		if(isset($post['sub_code']) && $post['sub_code']!='' && $post['sub_code']!='00000'){
			$sql .= " AND transactions_rows.sub_code=".intval($post['sub_code']);
		}
		
			// Cost cender
		if(isset($post['cc']) && $post['cc']!=' ' && $post['cc']!=''){
			$sql .= " AND transactions_rows.cc=".$post['cc'];
		}

			// Status
		if(isset($post['approved']) && trim($post['approved'])!=''){
			if($post['approved'] == 0 ){
				$sql .= " AND transactions.approve=0";
			} else {
				$sql .= " AND transactions.approve>0";
			}
		}
		
		$sql .= " GROUP BY transactions_rows.trans_id";
		
			// value
		if(isset($post['total']) && $post['total']!=''){
			$sql .= " HAVING SUM(transactions_rows.debit) ".html_entity_decode($post['pram_select']).$post['total'];
		}
		$sql .= " ORDER BY transactions.date DESC ";
		//echo $sql;
		$transactions = do_query_array($sql, MySql_Database);
		if($transactions!= false && count($transactions) >0){
			foreach($transactions as $t){
				$trans = new Settlements($t->id);			
				$row = new Layout($trans);
				$row->lock = $trans->approve == 0 ? 'unlocked' : 'locked';
				$trans_user = new Users('', $trans->user_id);
				$row->user = $trans_user->getRealName();
				
				$descrip_tr = array();
				$total = 0;
				$details = $trans->getRows();
				foreach($details as $r){
					$acc = new Accounts(Accounts::fillZero('main', $r->main_code).Accounts::fillZero('sub', $r->sub_code));
					$total += $r->debit;
					$row->total = numberToMoney($total);
					$descrip_tr[] = write_html('tr', '',
						write_html('td', 'width="80"', numberToMoney($r->debit)).
						write_html('td', 'width="80"', numberToMoney($r->credit)).
						write_html('td', '', isset($acc->title) ?  $acc->title: Accounts::fillZero('main', $r->main_code).Accounts::fillZero('sub', $r->sub_code))
					);
				}
				$row->descript = write_html('table', 'cellspacing="1" cellpadding="0" bgcolor="#CDCDCD" width="100%"', implode('', $descrip_tr));
				$row->date_str = date('d/m/Y h:m', $row->date);
				$row->template = 'modules/settlements/templates/settlements_list_row.tpl';
				$trs[] = $row->_print();
			}
			
			$layout->trs = implode('', $trs);
			
		} else {
			$layout->error_nothing_match =  write_error($lang['result_not_found']);
		}
		return $layout->_print();
	}
	
	static function loadMainLayout(){
		$user = new Users();
		$layout = new layout();
		$menu = new layout();
		$menu->read_transactions = !$user->getPrvlg('read_transactions') ? 'hidden' : '';
		$menu->insert_transactions = !$user->getPrvlg('insert_transactions') ? 'hidden' : '';
		$menu->capital_transactions = !$user->getPrvlg('capital_transactions') ? 'hidden' : '';
		$menu->template = 'modules/settlements/templates/settlements_menu.tpl';
		
		$layout->menu = $menu->_print();
		$layout->template = 'modules/settlements/templates/main_layout.tpl';
		return $layout->_print();
	}
	
	static function loadsearchFrom(){
		global $lang;
		$layout = new layout();
		$layout->begin_date = getYearSetting('begin_date');
		$end_date = getYearSetting('end_date');
		$layout->end_date = unixToDate(time()< $end_date ? time() : $end_date);
		$layout->currency_opts = write_html('option', 'value=" "', $lang['all']).Currency::getOptions();
		$ccs_arr =array();
		$layout->ccs_opts = write_select_options( CostCentersGroup::getListOpts(), '', true);
		$layout->template = 'modules/settlements/templates/settlements_search.tpl';
		$users = do_query_array("SELECT user_id, name FROM users", MySql_Database);
		$users_arr = array();
		foreach($users as $user){
			$users_arr[$user->user_id] = $user->name;
		}
		$layout->users_opts = write_select_options($users_arr, '', true);
		return $layout->_print();
	}
}