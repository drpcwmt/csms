<?php
/** BusMS settings
*   unique file for SafeMS
*/

class Bus {
	
	static function loadMainLayout(){
		global $busms, $sms;
		$layout = new stdClass();
		$layout->busms_id = $busms->id;
		$groups = Bus::getGroups();
		$first_group = $groups[0];
		$layout->items_list ='';
		$first = true;
		foreach($groups as $group){
			 $layout->items_list .=write_html( 'li', 'itemid="'.$group->id.'" busms_id="'.$busms->id.'"  sms_id="'.$sms->id.'" class="hoverable clickable ui-stat-default ui-corner-all '.($first==true? 'ui-state-active': '').'" action="openBusGroup"', 
				write_html('text', '',
					$group->title
				)
			);
			$first = false;
		}
		$layout->bus_layout = Bus::loadGroupLayout($first_group->id);
		return fillTemplate("modules/bus/templates/main_layout.tpl", $layout);
	}
	
	static function getGroups(){
		global $busms, $sms;
		return do_query_array("SELECT * FROM fees_group", BUSMS_Database, $busms->ip); 
	}
	
	static function getRoutes($group_id){
		global $busms;
		return do_query_array("SELECT * FROM bus_routes WHERE group_id=$group_id",  BUSMS_Database, $busms->ip); 
	}
	
	static function getRouteFees($route_id, $sms){
		$group_id = Bus::getGroupFromRoute($route_id, $sms);
		return Bus::getFees($group_id, $sms);
	}
	
	static function getFees($group_id, $sms){
		$result = do_query_array("SELECT * FROM school_fees WHERE con='bus' AND con_id=$group_id",  SMS_Database, $sms->ip);
		return $result !=false ? $result : array();
	}
	static function getGroupFromRoute($route_id, $sms){
		$group = do_query_obj("SELECT fees_group FROM routes WHERE id=$route_id", BUSMS_Database, $sms->getSettings('busms_server_ip')); 
		return $group->fees_group;
	}
	
	static function loadGroupLayout($group_id){
		global $MS_settings, $busms, $sms;
		$layout = new stdClass;
		$layout->busms_id = $busms->id;
		$layout->sms_id = $sms->id;
		$layout->group_id = $group_id;
		$year = $_SESSION['year'];		
		
		$fees = Bus::getFees($group_id, $sms);
		$layout->bus_fees_rows = '';
		foreach($fees as $f){
			$row = $f;
			$row->value = $f->debit !=0 ? $f->debit : ($f->credit * -1);
			$row->discount_on = $f->discount==1 ? 'checked' : '';
			$row->discount_off = $f->discount!='1' ? 'checked' : '';
			$row->main_code = Accounts::fillZero('main', $f->main_code);
			$row->sub_code = Accounts::fillZero('sub', $f->sub_code);
			$row->currency_opts = Currency::getOptions($f->currency);
			$row->busms_id =$busms->id;
			$row->sms_id =$sms->id;
			
			$layout->bus_fees_rows .= fillTemplate("modules/bus/templates/bus_fees_rows.tpl", $row);
		}
		
		return fillTemplate("modules/bus/templates/group_layout.tpl", $layout);
		
	}

	static function loadNewFeesForm($group_id){
		global $MS_settings, $sms;
		$form = new stdClass();
		$form->group_id = $group_id;
		$form->currency_opts = Currency::getOptions($MS_settings['def_currency']);
		$form->groups_values_trs = '';
		$groups = Bus::getGroups();
		// payments
		$school = new SMS($sms->id);
		$dates = $school->getDates();
		$form->payments_ths = '';
		$form->payments_tds = '';
		foreach($dates as $date){
			$form->payments_ths .= write_html('th', '', $date->title);
			$form->payments_tds .= write_html('td', '', '<input type="text" style="text-align:center" class="payment_values" name="payments-'.$date->id.'" />');
		}
		
		foreach($groups as $group){
			$form->groups_values_trs .= write_html('tr', '', 
				write_html('td', 'class="lab"', $group->title).
				write_html('td', '', '<input type="checkbox" name="value-'.$group->id.'" '.($group->id == $group_id ? 'checked': '').' value="'.$group->id.'" />')
			);
		}
		
		return fillTemplate('modules/bus/templates/new_fees_form.tpl', $form);
	}
	
	static function saveNewFees($post){
		global $sms;
		$result = true;
		$values = array(
			'title'=> $post['title'],
			'con'=>'bus',
			'con_id'=> $post['route_group_id'],
			'main_code'=> Accounts::removeZero($post['main_code']),
			'sub_code'=> $post['sub_code'],
			'currency'=> $post['currency'],
			'discount'=> $post['discount'],
			/*'interest'=> $post['interest'],
			'interest_period'=> $post['interest_period'],*/
			'year'=> $_SESSION['year']
		); 
		if(isset($post['type']) && $post['type'] == 'debit'){
			$values['debit'] = $post['value'];
		} else {
			$values['credit'] = $post['value'];
		}
		
		$groups = Bus::getGroups();
		foreach($groups as $group){
			if(isset($post['value-'.$group->id]) && $post['value-'.$group->id] != ''){
				$values['con_id'] = $group->id;
				$fees_id = do_insert_obj($values, 'school_fees', SMS_Database, $sms->ip);
				if(!$fees_id){
					$result = false;
				} else {
					$school = new SMS($sms->id);
						// set payments schedule
					$dates = $school->getDates();
					foreach($dates as $date){
						$date_ins = new stdClass();
						$date_ins->value = $post['payments-'.$date->id] != '' ? $post['payments-'.$date->id] : 0;
						$date_ins->term = $date->id;
						$date_ins->fees_id = $fees_id;
						$date_ins->con = 'bus';
						$date_ins->con_id = $group->id;
						$date_ins->year = $_SESSION['year'];
						do_insert_obj($date_ins, 'school_fees_payments', SMS_Database, $sms->ip);
					}
				}						
			}
		}
		
		if($result!=false){
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}

	static function deleteFees($id){
		global $lang, $sms;
		if(do_query_edit("DELETE FROM school_fees WHERE id=$id", SMS_Database, $sms->ip)){
			do_query_edit("DELETE FROM school_fees_payments WHERE fees_id=$id AND con='bus' AND year=".$_SESSION['year'], SMS_Database, $sms->ip);
			$answer['id'] = $id;
			$answer['error'] = "";
		} else {
			global $lang;
			$answer['id'] = "";
			$answer['error'] = $lang['error_updating'];
		}
		return json_encode($answer);
	}

	static function getBusDuePerTerm($student, $fee, $profil, $date_id, $std_dates){
		global $sms;
		$year = $_SESSION['year'];
		$school = new SMS($sms->id);
		$bus_dates =  $school->getDates();

		if($profil != false){
				// get payment for profil 
			$pays = do_query_obj("SELECT SUM(value) AS value FROM school_fees_payments WHERE year=$year AND term=$date_id AND fees_id=$fee->id AND con='bus'",  SMS_Database, $sms->ip);
				// get new payment if fail to load profil bus payments
			if($pays != false){
				$value = $pays->value;
			} else {
				$fees = Bus::getRouteFees($student->getBus(), $sms);
				$total= 0;
				foreach($fees as $f){
					$total += $f->debit;
				}
				if(count($std_dates) < count($bus_dates)){ 
					$value = $total / count($std_dates);
				}
			}
		} 
		if(!isset($value)) {
			$count = 0;
			foreach($std_dates as $date){
				if($date->id == $date_id){
					$count_date = $count;
				}
				$count++;
			}
			if(isset($bus_dates[$count_date])){
				$default_date = $bus_dates[$count_date];
					// get payment based by no from main bus payment dates
				$pays = do_query_obj("SELECT * FROM school_fees_payments WHERE year=$year AND term=$default_date->id AND fees_id=$fee->id AND con='bus'",  SMS_Database, $sms->ip);
				$value = $pays->value;
			}
		}
		if(isset($value)){
			return $value;
		} else {
			return 0;
		}

	}	

}
?>