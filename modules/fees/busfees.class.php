<?php
/** Bus Fees
*
*/
class BusFees{

	public function __construct($sms){
		$this->sms = $sms;
		$this->busms = $sms->getBusms();
	}
	
	public  function loadMainLayout(){
		$sms = $this->sms;
		$busms=$this->busms;
		$year = $_SESSION['year'];
		$layout = new Layout();
		$layout->template = "modules/fees/templates/bus_main_layout.tpl";
		$groups = $busms->getGroups();
		$first_group = reset($groups);
		$layout->items_list ='';
		$first = true;
		foreach($groups as $group){
			 $layout->items_list .=write_html( 'li', 'itemid="'.$group->id.'" sms_id="'. $sms->id.'" class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openRouteGroupFees"', 
				write_html('text', 'class="holder-routegroup-'.$group->id.'"',
					$group->title
				)
			);
			if($first){
				$layout->group_layout = $this->getGroupFeesLayout($group->id, $year);
				$first = false;
			}
		}
		
		return $layout->_print();
		
	}
	
	public function getFees($group_id, $year){
		$groupRoute = new GroupRoutes($group_id, $this->busms); 
		return $groupRoute->getFees($this->sms, $year);
	}
	
	public function getGroupFeesLayout($group_id, $year){
		global $prvlg;
		$sms = $this->sms;
		$groupRoute = new GroupRoutes($group_id);
		if($prvlg->_chk('group_fees_read')){
			$fees_form = new Layout($groupRoute);
			$fees_form->template = "modules/routes/templates/group_fees_layout.tpl";
			$fees_form->sms_id = $sms->id;
			$fees_form->group_id = $group_id;
			$fees_form->group_name = $groupRoute->getName();
			$fees_form->bus_fees_rows = '';
			if($prvlg->_chk('group_fees_edit') == false){
				$fees_form->prvlg_group_fees_edit= 'hidden';
			}
			
			$fees = $this->getFees($group_id, $year);
			foreach($fees as $f){
				$row = new Layout($f);
				$row->template = "modules/fees/templates/level_fees_rows.tpl";
				$row->value = $f->debit !=0 ? $f->debit : ($f->credit * -1);
				$row->discount_on = $f->discount==1 ? 'checked' : '';
				$row->discount_off = $f->discount!='1' ? 'checked' : '';
				$row->main_code = Accounts::fillZero('main', $f->main_code);
				$row->sub_code = Accounts::fillZero('sub', $f->sub_code);
				$row->currency_opts = Currency::getOptions($f->currency);
				$row->sms_id =$sms->id;			
				$fees_form->bus_fees_rows .= $row->_print();
			}
			return $fees_form->_print();
		}
	}
}
?>