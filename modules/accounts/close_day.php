<?php
## Close safe day

$accms = $this_system->getAccms();

if(isset($_GET['date'])){
	$date = dateToUnix(safeGet('date'));
	$end_date = $date +( 60*60*24);
} else {
	$date = mktime(0,0,0, date('m'), date('d'), date('Y'));
	$end_date = mktime(0,0,0, date('m'), date('d')+1, date('Y'));
}

$year = date('Y', $this_system->getYearSetting('begin_date'));
/************************** INGOINGG *****************/

function builSettlementRow( $table, $trans_id='1', $dest, $end_date, $begin_date, $value='debit', $currency, $rate, $save=false){
	global $safems, $accms, $total_debit, $total_credit;
	$result = true;
	$rows = do_query_array("SELECT SUM(value) AS value, $dest"."_main_code, $dest"."_sub_code, currency, cc, GROUP_CONCAT(CONCAT(notes,' - ',id) SEPARATOR '<br/>') AS notes, GROUP_CONCAT(id SEPARATOR ',') AS ids
		FROM $table
		WHERE date>=$begin_date 
		AND date<$end_date
		AND currency LIKE '$currency'
		AND sync=0
		GROUP BY cc, $dest"."_main_code, $dest"."_sub_code", $safems->database, $safems->ip);
	$trs = array();
	
	foreach($rows as $row){
		${'total_'.$value} += $row->value;
		//$full_code = Accounts::fillZero('main', $row->{$dest.'_main_code'}). Accounts::fillZero('sub', $row->{$dest.'_sub_code'});
		$acc = getAccount($row->{$dest.'_main_code'}, $row->{$dest.'_sub_code'});//new Accounts($full_code);
		$ins = array(
			'trans_id' => $trans_id,
			$value=> $row->value,
			'main_code'=> $acc->main_code,
			'sub_code'=> $acc->sub_code,
			'cc'=> $row->cc,
			'notes'=> $row->notes,
			'year' =>$_SESSION['year'],
			'rate' => $rate
		);
		$ins['acc_title'] = $acc->title;
		$ins['full_code'] = $acc->full_code;
		//	print_r($ins);
		if($save){
			if(do_insert_obj($ins ,'transactions_rows', $accms->database, $accms->ip) != false){
			} else {
				$result =false;
			}
		} else {
			$trs[] = fillTemplate('modules/accounts/templates/settlements_row.tpl', $ins);
		}
		if($save && $result){
		//	do_update_obj(array('sync'=>'1'), "id=".str_replace(',', ' OR id=', $row->ids), $table, $safems->database, $safems->ip); 
		}
	}
	return $save? $result : $trs;
}

$curs=array();	
$in_curs = do_query_array("SELECT currency FROM ingoing WHERE date>=$date AND date<$end_date AND sync=0 UNION SELECT DISTINCT currency FROM outgoing WHERE date>=$date AND date<$end_date AND sync=0", $safems->database, $safems->ip);
foreach($in_curs as $cur){
	$curs[] = $cur->currency;
}
$total_debit = 0;
$total_credit = 0;
$filedset = array();

if(isset($_GET['save'])){
	if(isset($_POST['cur'])){
		for($i=0; $i<count($_POST['cur']); $i++){
			$rates[$_POST['cur'][$i]] = $_POST['rate'][$i];
		}
	}
}

// Handel to safe account
if( count($curs) > 0){
	foreach($curs as $cur){
		$rate = $cur=='EGP' ? 1 : (isset($rates) ? $rates[$cur] : Currency::convertRate($cur, 'EGP'));
		if(isset($_GET['save'])){
			$new_trans = do_insert_obj(array(
				'date'=>dateToUnix($_GET['date']),
				'currency' =>$cur,
				'user_id'=> '0',
				'approve'=> 0
				), 'transactions', $accms->database, $accms->ip);
			if($new_trans != false){
				
			}
		} else {
			$new_trans = 1;
		}
	
		$in_debit = builSettlementRow( 'ingoing', $new_trans, 'from', $end_date, $date, 'debit', $cur, $rate ,isset($_GET['save']));
		$in_credit = builSettlementRow( 'ingoing', $new_trans, 'to', $end_date, $date, 'credit', $cur, $rate, isset($_GET['save']));
		$out_debit = builSettlementRow( 'outgoing', $new_trans, 'from', $end_date, $date, 'debit', $cur, $rate, isset($_GET['save']));
		$out_credit = builSettlementRow( 'outgoing', $new_trans, 'to', $end_date, $date, 'credit', $cur, $rate, isset($_GET['save']));
		if(!isset($_GET['save'])){
			$filedset[] = write_html('fieldset', '',
				write_html('legend', '', $cur).
				($cur != 'EGP' ?
					write_html('div', 'class="ui-state-highlight ui-corner-all"',
						write_html('table', 'width="100%" cellspacing="1"',
							write_html('tr', '',
								write_html('td', 'width="120"',
									write_html('label', 'class="label ui-widget-header ui-corner-left reverse_align"', $lang['rate'])
								).
								write_html('td', '',
									'<input type="hidden" name="cur[]" value="'.$cur.'" />'.
									'<input type="text" name="rate[]" value="'.$rate.'" class="half_input" /> EGP' 
								)
							)
						)
					)
				: '').
				write_html('table', 'class="tablesorter {sortlist: [[0,1],[2,0]]}" ',
					write_html('thead', '',
						write_html('tr', '',
							write_html('th', 'width="80"', $lang['debit']).
							write_html('th', 'width="80"', $lang['credit']).
							write_html('th', 'width="80"', $lang['code']).
							write_html('th', 'width="80"', $lang['cc']).
							write_html('th', 'width="160"', $lang['title']).
							write_html('th', '', $lang['notes'])
						)
					).
					write_html('tbody', '',
						implode('', $in_debit).
						implode('', $in_credit).
						implode('', $out_debit).
						implode('', $out_credit)
						
					).
					write_html('tfoot', '',
						write_html('tr', '',
							write_html('th', 'width="80"', $total_debit).
							write_html('th', 'width="80"', $total_credit).
							write_html('th', '', '').
							write_html('th', 'colspan="3"', $lang['total'])
						)
					)
				)
			);
		}
	}
}

if(isset($_GET['save'])){
	if($in_debit != false && $in_credit != false && $out_debit != false && $out_credit != false){
		echo json_encode(array('error'=>''));
	} else {
		echo json_encode(array('error'=>'Error'));
	}
		

} else {
	if(count($filedset) > 0){
		echo write_html('form', 'id="close_day_form"',
			implode('', $filedset)
		);
	} else {
		echo write_error($lang['no_operation_to_save']);
	}
}
?>