<?php

	// Load accounting tree
if(isset($_GET['tree'])){
	if($prvlg->_chk('tree_read')){
		echo AccountsTree::loadTreeLayout();
	} else {
		echo write_error($lang['error_no_privilege']);
	}
} elseif(isset($_GET['printtree'])){
	if($prvlg->_chk('tree_read')){
		echo AccountsTree::printTree();
	} else {
		echo write_error($lang['error_no_privilege']);
	}

} elseif(isset($_GET['searchform'])){
	if($prvlg->_chk('tree_read')){
		echo AccountsTree::loadSearchLayout();
	} else {
		echo write_error($lang['error_no_privilege']);
	}

} elseif(isset($_GET['subs'])){
	if($prvlg->_chk('sub_acc_read_trans')){
		$account = new MainAccounts(safeGet($_GET['acc']));
		echo $account->getSubsTable();
	} else {
		echo write_error($lang['error_no_privilege']);
	}
	
} elseif(isset($_GET['totals'])){
	if($prvlg->_chk('sub_acc_read_trans')){
		echo AccountsTree::getTotalBalances();
	} else {
		echo write_error($lang['error_no_privilege']);
	}
	
	// Accounta Data & transaction 
} elseif(isset($_GET['transactions'])){
	if($prvlg->_chk('sub_acc_read_trans')){
		if(isset($_GET['acc'])){
			$acc = new Accounts(safeGet($_GET['acc']));
			if(isset($_GET['begin_date']) && $_GET['begin_date'] != ''){
				echo $acc->getTransactionsTable(safeGet($_GET['begin_date']), safeGet($_GET['end_date']), true);
			} else {
				echo $acc->getTransactionsTable();
			}
		} else {
			echo write_error($lang['error']);
		}
	} else {
		echo write_error($lang['error_no_privilege']);
	}

} elseif(isset($_GET['openacc'])){
	if($prvlg->_chk('tree_read')){
		$account = new Accounts(safeGet($_GET['openacc']));
		echo $account->loadLayout();
	} else {
		echo write_error($lang['error_no_privilege']);
	}
	
} elseif(isset($_GET['newacc'])){
	if($prvlg->_chk('sub_acc_add')){
		echo Accounts::newAccount(safeGet('parent'));
	} else {
		echo write_error($lang['error_no_privilege']);
	}

} elseif(isset($_GET['newmain'])){
	if($prvlg->_chk('main_acc_add')){
		echo MainAccounts::newMainCode(safeGet('parent'), safeGet('level'));
	} else {
		echo write_error($lang['error_no_privilege']);
	}
	
} elseif(isset($_GET['savecode'])){
	$account = new Accounts($_POST['code']);
	if($account->type == 'main'){
		if($prvlg->_chk('main_acc_edit')){
			echo MainAccounts::saveMainCode($_POST);
		} else {
			print(json_encode(array('error'=>$lang['no_privilege'])));
		}
	} else {
		if($prvlg->_chk('sub_acc_edit')){
			echo $account->saveSubCode($_POST);	
		} else {
			print(json_encode(array('error'=>$lang['no_privilege'])));
		}
	}
} elseif(isset($_GET['savenewmain'])){
	print(json_encode( MainAccounts::saveNewMain($_POST)));

} elseif(isset($_GET['savenewacc'])){
	echo Accounts::saveNewAcc($_POST);
	
} elseif(isset($_GET['del_acc'])){
	$account = new Accounts($_POST['code']);
	if($account->type == 'main'){
		if($prvlg->_chk('main_acc_edit')){
			$result = $account->_delete();
			if($result === true){
				print(json_encode(array('error'=>'')));
			} else {
				print(json_encode(array('error'=>$result)));
			}
		} else {
			print(json_encode(array('error'=>$lang['no_privilege'])));
		}
	} else {
		if($prvlg->_chk('sub_acc_edit')){
			$result = $account->_delete();
			if($result === true){
				print(json_encode(array('error'=>'')));
			} else {
				print(json_encode(array('error'=>$result)));
			}
		} else {
			print(json_encode(array('error'=>$lang['no_privilege'])));
		}
	}
	
} elseif(isset($_GET['closeday'])){
	include('close_day.php');

} elseif(isset($_GET['getname'])){
	$acc = new Accounts($_POST['acc']);
	if($acc->exists == false){
		print_r(json_encode(array('error'=> $lang['account_dont_exists'])));
	} else {
		$main_code = substr($acc->main_code,0,2);
		$main_acc = new Accounts($main_code);
		$out[] = array();
		print_r(json_encode(array('error'=>'', 
			'title'=>$acc->title.' - '. $main_acc->title)));
	}

	// Auto completer
} elseif(isset($_GET['autocomplete'])){
	$out = array();
	$term = safeGet($_GET['term']);
	setJsonHeader();
	$param = isset($_GET['main_code']) ? " AND main LIKE '". safeGet($_GET['main_code'])."%'" : '';
	print Accounts::autocomplete($term, $param);

} else {
	echo AccountsTree::loadMainlayout();	
}
?>