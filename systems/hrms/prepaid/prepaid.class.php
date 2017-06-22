<?php
/** Prepaid 
*
*
*/

class Prepaid{
	
	public function __construct($id, $hrms=''){
		
	}
	static function loadMainLayout(){
		$layout = new Layout();
		$layout->template = "modules/prepaid/templates/main_layout.tpl";
		$layout->menu = fillTemplate("modules/prepaid/templates/prepaid_menu.tpl", array());
		return $layout->_print();
	}
	
	static function loadJobList(){
		$layout = new Layout();
		$layout->job_list = '';
		$first = true;
		$jobs = Jobs::getList();
		foreach($jobs as $job){
			$layout->job_list .=write_html( 'li', 'job_id="'.$job->id.'"  class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openJobPrepaid"', 
				write_html('text', 'class="holder-job-'.$job->id.'"',
					$job->getName()
				)
			);
			$first = false;
		}
		$layout->template = 'modules/prepaid/templates/main_layout.tpl';
		
		return $layout->_print();
	}
	
	static function newPrepaid(){
		global $lang;
		$layout = new Layout();
		$layout->template = "modules/prepaid/templates/prepaid_form.tpl";
		$current_month = date('m');
		$months = array();
		for($i=0; $i<12; $i++){
			$m = $current_month+$i;
			$stamp = mktime(0,0,0, $m, 1, date('Y'));
			$nm = date('m', $stamp);
			$months[$stamp] = $lang["months_$nm"];
		}
		$layout->months_select = write_select_options($months, $current_month);
		
		foreach($months as $stamp=>$name){
			$ths[] = write_html('th', 'align="center"', $name);
			$tds[] = write_html('td', '', 
				'<input type="hidden" name="stamps[]" value="'.$stamp.'" />'.
				'<input type="text" name="stamp_val[]" style="with:25px; text-align:center" />'
			);
		}
		$layout->ths = implode('', $ths);
		$layout->tds = implode('', $tds);
		$layout->today = unixToDate(time());
		return $layout->_print();
	}
}