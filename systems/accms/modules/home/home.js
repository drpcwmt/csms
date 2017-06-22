// Home.js
function initiateHomeScreen(){
	var toLoad = new Array;
		// Messages
	if($("#home_content").find("#currency_widget").length > 0){
		getCurrencyWidgetData();
	}
}


function getCurrencyWidgetData(){
	currency = {
		name: 'currency',
		param: 'widget_data',
		async:true,
		mute:true,
		callback: function(answer){
			if(!answer){
				MS_alert("<h2>Can't retrive currencys rates..</h2>");
				setTimeout('getCurrencyWidgetData()', 3000000);
			} else {
				("#currency_widget").find('span.usdegp').html(answer.usdegp	);
				("#currency_widget").find('span.euregp').html(answer.euregp	);
				("#currency_widget").find('h5.last_sync').html(answer.last_sync	);
			}
		}
	}
	getModuleJson(currency);
}