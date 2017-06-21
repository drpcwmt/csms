<?php
/** Account calculator
* get the pre defined account codes for: 
* sms->student as client, 
* sms->busms as bus incomes
* hrms->emp as salarys
* libms as lib incomes
* fess late intrest code
*/

class AccountCalc{
	
	public $codes = array(
		'clients' => 15,
		'students'=> 151,
		'employes' => 152,
		'fees_incomes' => 410000000,
		'bus_incomes' => 420000000,
		'libms_incomes' => 430000000,
		'app_incomes' => 460000000,
		'admission_incomes' => 440000000,
		'fees_interest' => 471000000
	);
	
	public function __construct($acc_name, $ms=''){
		$code = $ms!='' ? $ms->getCC() : '';
		$this->code = $this->codes[$acc_name].$code;
	}
	public function getCode(){
		return $this->code;
	}
	
	static function getCCNameById($ccid){
		$accms= new AccMS();
		$cc = do_query_obj("SELECT title FROM cc WHERE id=$ccid", ACCMS_Database, $accms->ip);
		return  $cc->title;
	}
}
?>	