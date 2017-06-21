<?php
## currency widget

$currency_widget = new Layout();
$currency_widget->currency_opts = Currency::getOptions($this_system->getSettings('def_currency'));	
$currency_widget->usd_to_egp = Currency::convertRate('USD', "EGP");
$currency_widget->eur_to_egp = Currency::convertRate('EUR', "EGP");

if(!in_array($this_system->type, array('sms', 'hrms'))){
	if($rates = do_query_obj("SELECT date FROM currency_rate ORDER BY date DESC LIMIT 1")){
		$currency_widget->last_sync = unixToDate($rates->date);
	}
}

$widget = fillTemplate('modules/currency/templates/widget.tpl', $currency_widget);