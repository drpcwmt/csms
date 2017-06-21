<?php
/** Books Fees
*
*/
class BookFees{

	public function __construct($sms){
		$this->sms = $sms;
	}
	
	public  function loadMainLayout(){
		$sms = $this->sms;
		$layout = new Layout();
		$layout->sms_id = $sms->id;
		$levels = $sms->getLevelList();
		$first_level = reset($levels);
		$layout->items_list ='';
		$first = true;
		foreach($levels as $level){
			 $layout->items_list .=write_html( 'li', 'sms_id="'.$sms->id.'" level_id="'.$level->id.'" class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openBooksFeesByLevel"', 
				write_html('text', '',
					$level->getName()
				)
			);
			$first = false;
		}
		$layout->level_layout = $this->getLevelFeesLayout($first_level, $_SESSION['year']);
		return fillTemplate("modules/fees/templates/books_main_layout.tpl", $layout);
	}

	public function getLevelFees($level_id, $year){
		$sms = $this->sms;
		$out = array();
		$fees= do_query_array("SELECT * FROM school_fees WHERE con='books' AND con_id=$level_id AND year=$year", $sms->database, $sms->ip);
		if($fees !=false && count($fees)>0){
			foreach($fees as $f){
				$out[] = new Fees($f->id, $sms);
			}
		}
		return $out;
	}
	
	public function getLevelFeesLayout($level, $year){
		$sms = $this->sms;
		$layout = new Layout();
		$layout->template = "modules/fees/templates/books_fees_layout.tpl";
		$layout->level_id = $level->id;
		$layout->sms_id = $sms->id;
		$fees = $this->getLevelFees($level->id, $year);
		$layout->books_fees_rows = '';
		foreach($fees as $f){
			$row = new Layout($f);
			$row->template = "modules/fees/templates/level_fees_rows.tpl";
			$row->value = $f->debit !=0 ? $f->debit : ($f->credit * -1);
			$row->disc_hidden = $f->discount=='1' ? '' : 'hidden';
			$row->annual_hidden = $f->increase=='1' ? '' : 'hidden';
			$row->main_code = Accounts::fillZero('main', $f->main_code);
			$row->sub_code = Accounts::fillZero('sub', $f->sub_code);
			$row->currency_opts = Currency::getOptions($f->currency);
			$row->sms_id =$sms->id;
			
			$layout->books_fees_rows .= $row->_print();
		}
		return $layout->_print();
	}
}
?>