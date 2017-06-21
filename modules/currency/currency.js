// JavaScript Document
function convertCurrency($but){
	var value = $('#widget_currency_value').val();
	var from = $('#widget_currency_from').val();
	var to = $('#widget_currency_to').val();
	var currency = {
		name: 'currency',
		param: 'convert='+value+'&from='+from+'&to='+to,
		callback: function(answer){
			$('#convert_result').html(answer.result);
		}
	}
	getModuleJson(currency);
}