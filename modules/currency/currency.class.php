<?php
/** Currency
*
*/


class Currency {
	
	static function convertRate($from, $to, $date=false){
		if($from == $to){
			return 1;
		}
		global $MS_settings;
		$main_currency = array("USD", "EUR", "GBP");
		$from = ucwords($from);
		$to = ucwords($to);
		if((in_array($from, $main_currency) && $to == 'EGP') || (in_array($to, $main_currency)&& $from == 'EGP')){
			$date = $date != false ? $date : mktime(0,0,0 , date('m'), date('d'), date('Y'));
			$cur = $to == 'EGP' ? $from : $to;				
			$sql = "SELECT rate FROM currency_rate WHERE currency='$cur' AND date=$date" ;
			$currency = do_query_obj($sql, MySql_Database);
			if($currency != false){
				$out = !in_array($from, $main_currency) ? 1/$currency->rate : $currency->rate;
			} else {
				$out = Currency::getRate($from, $to, $date);
			}
		} else {
			$out = Currency::getRate($from, $to, $date);
			
		}
		return round($out,2);
	}
	
	static function getRate($from, $to){
		$result = 0;
		$opts = array('http' =>
			array(
				'method'  => 'GET',
				'timeout' => 10 
			)
		);
		
			// Yahoo Url
		$url = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22$from$to%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=";
			
			// http://rate-exchange.herokuapp.com
		//$url = "http://rate-exchange.herokuapp.com/fetchRate?from=$from&to=$to";	
		
		$context  = stream_context_create($opts);
		$url_data = @file_get_contents($url, false, $context);	
		$data = json_decode($url_data);
			// Yahoo result
		//	print_r($data);
		$result = $url_data!= false && $data!=false ? $data->query->results->rate->Rate : 'N.A';

			// http://rate-exchange.herokuapp.com Results
		//$result = isset($data->Rate) ? $data->Rate : 0;
		
		if($result != 0){
			$row = new stdClass();
			$row->date = mktime(0,0,0 , date('m'), date('d'), date('Y'));;
			if(ucwords($to) == 'EGP'){
				$row->rate = $result;
				$row->currency = ucwords($from);
			} elseif(ucwords($from) == 'EGP'){
				$row->rate = 1/ $result;	
				$row->currency = ucwords($to);
			}
			
			do_insert_obj($row, 'currency_rate', MySql_Database);
		}
		return $result;
	}
	
	static function getList(){
		$curs = do_query_array("SELECT * FROM currency ORDER BY code ASC", MySql_Database);
		$out = array();
		foreach($curs as $cur){
			//$value = $cur->{'name_'.$_SESSION['dirc']} != 0 ? $cur->{'name_'.$_SESSION['dirc']} : $cur->name_ltr;
			$out[$cur->code] = $cur->code;
		}
		return $out;
	}
	
	static function getOptions($selected=''){
		$curs = Currency::getList();
		return write_select_options( $curs, $selected, false);
	}
}

?>