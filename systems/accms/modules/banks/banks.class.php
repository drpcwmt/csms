<?php
/** Banks main Class
*
*/
define('banks_code', 1611);

class Banks{
	
	public function __construct($code){
		$this->acc = new Accounts($code);
	}
	
	public function getAccounts(){
		if(!isset($this->subs)){
			$subs = $this->acc->getSubs();
			$this->subs = array();
			foreach($subs as $sub){
				$this->subs[] = new Accounts(Accounts::fillZero($sub->main_code));
			}
		}
		return $this->subs;
	}
	
	public function loadLayout(){
		$layout = new layout($this);
		$main = new Layout($this->acc);
		$main->main_code = Accounts::fillZero('main', $this->acc->main_code);
		$main->sub_code = Accounts::fillZero('sub', $this->acc->sub_code);
		$main->account_balance = $this->acc->getBalance();
		$main->template ='modules/accounts/templates/accounts_main_layout.tpl';
		
		$layout->main = $main->_print();
		$layout->template= 'modules/banks/templates/bank_layout.tpl';
		return $layout->_print();
	}
	
	static function getList(){
		$code = banks_code;
		$out = array();
		$accs = do_query_array("SELECT code FROM codes WHERE code LIKE '$code%' AND code != '$code'", MySql_Database);
		foreach($accs as $bank){
			$out[] = new Banks($bank->code);
		}
		return $out;
	}
	
	static function loadMainLayout(){		
		$layout = new Layout();
		$banks = Banks::getList();

		$layout->list = '';
		foreach($banks as $bank){
			$layout->list .= write_html('li', 'action="openBank" bank_code="'.$bank->acc->main_code.'" class="clickable hoverable ui-state-default ui-corner-all"', $bank->acc->title);
		}
		$layout->template = 'modules/banks/templates/main_layout.tpl';
		return $layout->_print();
		
	}

}