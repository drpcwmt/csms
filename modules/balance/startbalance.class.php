<?php
/** Start Balance
*
*/

class StartBalance{	

	static function loadMainLayout(){
		$layout = new stdClass();
		$layout->tree = AccountsTree::getTreeList(0, array('1','2'), 'openStartBal');
		return fillTemplate('modules/balance/templates/start_balance_main.tpl', $layout);
	}
	
	static function loadAccount($main){
		$layout = new Layout();
		$layout->template = 'modules/balance/templates/start_balance.tpl';
		$layout->rows = '';
		$main_acc = new MainAccounts($main);
		$subs = $main_acc->getSubs();
		foreach($subs as $sub){
			$acc = new Accounts(Accounts::fillZero('main', $sub->main).Accounts::fillZero('sub', $sub->sub));
			$currencys = $acc->getCurrencys();
			foreach($currencys as $cur){
				$start = do_query_obj("SELECT * FROM start_balance WHERE year=".$_SESSION['year']." AND main_code='$acc->main_code' AND sub_code='$acc->sub_code' AND currency='$cur'",  MySql_Database);
				$acc->debit = $start != false ? $start->debit: '';
				$acc->credit = $start != false ? $start->credit: '';
				$acc->currency = Currency::getOptions($cur);
				$layout->rows .= fillTemplate("modules/balance/templates/start_balance_row.tpl", $acc);
			}
		}	
		return $layout->_print();
	}
	
	static function _save($post){
		global $lang;
		$result = true;
		for($i=0; $i<count($post['acc']); $i++){
			$account = new Accounts($post['acc'][$i]);
			$cur = $post['currency'][$i];
			$chk = do_query_obj("SELECT year FROM start_balance WHERE sub_code=$account->sub_code AND main_code=$account->main_code AND year=".$_SESSION['year']. " AND currency='$cur'");
			$rows = array(
				'year'=> $_SESSION['year'],
				'debit'=> $post['debit'][$i],
				'credit'=> $post['credit'][$i]
			);
			if($chk != false && $chk->year == $_SESSION['year']){
				if(!do_update_obj($rows, 'sub_code='.$account->sub_code. ' AND main_code='.$account->main_code.' AND year='.$_SESSION['year']. " AND currency='$cur'", 'start_balance')){
					$result = false;
				}
			} else {
				$rows['sub_code'] = $account->sub_code;
				$rows['main_code'] = $account->main_code;
				$rows['currency'] = $post['currency'][$i];
				if(!do_insert_obj($rows, 'start_balance')){
					$result = false;
				}
			}
		}
		if( $result){
			return array('error'=>'');
		} else {
			return array('error'=> $lang['error_updating']);
		}
	}
}