<?php
/**Custody
*
*/

class Custodys{
	private $thisTemplatePath = 'modules/accms/templates';
	
	static function getList($sub=''){
		global $accms;
		$out = array();
		$custody = do_query_array("SELECT * FROM sub_codes WHERE main LIKE '163$sub%'", $accms->database, $accms->ip);
		foreach($custody as $c){
			$out[] = getAccount($c->main, $c->sub);
		}
		return $out;
	}
	
	static function getOptions($sub='', $selected=''){
		$custodys = Custodys::getList($sub);
		$out = '';
		foreach($custodys as $custody){
			$out .= write_html('option', 'value="'.$custody->full_code.'" '. ($selected==$custody->full_code ? 'selected="selected"' :'') , $custody->title);
		}
		return $out;
	}
}
		