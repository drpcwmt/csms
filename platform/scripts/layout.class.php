<?php
/** Layout
*
*
*/

class Layout{
	public $pro_option='',
	$elearn_option='',
	$template ='';
	
	public function __construct($object =''){
		if(MS_codeName == 'sms_basic'){
			$this->pro_option = 'hidden';
			$this->elearn_option = 'hidden';
		} elseif(MS_codeName == 'sms_pro'){
			$this->elearn_option = 'hidden';
		}
		$year = getNowYear();
		if(!isset($this->cur_year))	{ $this->cur_year = $year->year.'/'.($year->year+1) ;}
		if(!isset($this->today)) $this->today = unixToDate(time());

		if($object != ''){
			foreach($object as $key=>$value){
				$this->$key = $value;	
			}
		}
	}
	public function _print(){
		if($this->template != ''){
			$template = new Template($this->template); 
			foreach ($this as $key => $value) {
				if(!is_object($value) && !is_array($value)){
					$template->set($key, $value);
				}
			}
			return $template->output();
		//	return fillTemplate($this->template, $this);
		}
	}
}
?>