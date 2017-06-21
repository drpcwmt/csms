<?php
/** Banks
*
*/

class Banks{
	private $thisTemplatePath = 'modules/accms/templates';
	
	static function getList(){
		$accms = new AccMS();
		$banks = $accms->getBanks();
		return $banks;
	}
	
	static function getOptions($selected=''){
		$banks = Banks::getList();
		$out = '';
		foreach($banks as $bank){
			$full_code = Accounts::fillZero('main', $bank->main).Accounts::fillZero('sub', $bank->sub);
			$out .= write_html('option', 'value="'.$full_code.'" '.($selected==$full_code ? 'selected="selected"' :'') , $bank->title);
		}
		return $out;
	}
}
		