// Ingoing


/*function openInApp($but){
	var module ={};
	module.name = 'ingoing';
	module.data = "applications";
	module.title = getLang('application');
	module.div = '#ingoingMainDiv';
	module.callback = function(){
		
	}
	loadModule(module);
}
*/
function openIncomes($but){
	var school_id = $but.attr('school_id');
	var $form = $but.parents('form');
	var module ={};
	module.name = 'ingoing';
	module.data = "type="+$but.attr('rel');
	module.title = getLang('incomes');
	module.div = '#ingoingMainDiv';
	module.callback = function(){
		if($but.attr('rel') != 'applications'){
			loadModuleJS('students');
			setStudentAutocomplete('#newIncome input[name="name"]');
			changeIncomeCC($('#newIncome select[name="ccid"]'));
		}
	}
	loadModule(module);
}

function changeIncomeCC($select){
	var $form = $select.parents('form');
	var $sugInp = $form.find('input[name="name"]');
	$form.find('input[name="from_main"]').val('151'+$select.val());
	if($sugInp && $sugInp.hasClass('ui-autocomplete-input')){
		$sugInp.attr('sms_id', $select.val());
			loadModuleJS('students');
			setStudentAutocomplete('#newIncome input[name="name"]', '0,1,3,2');
	}
}

function openInOthers($but){
	var module ={};
	module.name = 'ingoing';
	module.data = "others";
	module.title = getLang('incomes');
	module.div = '#ingoingMainDiv';
	module.callback = function(){
		loadModuleJS('accounts');
		formatAccountCode($('#add_others_form .account_code'));
		setAccDescAutocomplete('#add_others_form input[name="from_name"]');
	}
	loadModule(module);
}	

function submitIncomes($but){
	var $form = $but.parents('form');
	if(!validateForm($form)){
		return false;
	}
	var data =  $form.serialize();
	$form.find('input[name="name"], input[name="value"]').val('');
	var savePayment = {
		name: 'ingoing',
		param: 'incomes&save',
		post: data,
		async:false,
		callback: function(answer){
			dialogOpt = {
				buttons: [{ 
					text: getLang('print'), 
					click: function() { 
						printDialog($(this));
			
					}
				}, { 
					text: getLang('close'), 
					click: function() { 
						$(this).dialog('close');
					}
				}],
				width:600,
				height:400,
				minim:false,
				div: 'dialog_recete',
				title: 'recete'
			}
			openHtmlDialog(answer.recete, dialogOpt)
		}
	}
	getModuleJson(savePayment);
}

function setOthersAutocomplete(input){
	var source = 'index.php?module=ingoing&others&autocomplete';
	$(input).autocomplete({
		source: source,	
		minLength: 2,
		select: function(event, ui) {
			var title = ui.item.title ? ui.item.title : '';
			$(input).val(title);
			var $tr = $(input).parents('tr').eq(0);
			var $accCode = $tr.find('.account_code');
			$accCode.find('input.main_code').val(ui.item.main_code);
			$accCode.find('input.sub_code').val(ui.item.sub_code);
			//$accCode.find('input.cc').val(ui.item.cc);
			return false;
		},	
		search: function(event, ui) {
			$(input).attr('term', '');	
		}
	}).data( "autocomplete" )._renderItem = function( ul, item ) {
		if(item.error){
			MS_alert('<h3 class="title_white"><img src="assets/img/error.png" />'+item.error+'</h3>');
			return $( '<li class="ui-state-error ui-corner-all"></li>' )
				.data( "item.autocomplete", item )
				.append( '<a>' + item.error+"</a>" )
				.appendTo( ul );
			//return false;
		} else {
			var name = item.title ;
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( '<a>' + name+"</a>" )
				.appendTo( ul );
		}
	};
	
	$(input).after('<span style="position:absolute; z-index:100; margin: 4px -18px;" class="ui-icon ui-icon-arrowrefresh-1-w hand hidden" titel="'+getLang('clear')+'" onclick="clearSugField(this)" ></span>');

	
	$(input).blur(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeOut().remove();
	});
	$(input).keypress(function(){
		$(input).next('span.ui-icon-arrowrefresh-1-w').fadeIn();
	});

}

function submitOtherIngoing($but){
	var $form = $but.parents('form');
	if(!validateForm($form)){
		return false;
	}
	var savePayment = {
		name: 'ingoing',
		param: 'others&save&sms_id='+$but.attr('sms_id'),
		post: $form.serialize(),
		callback: function(answer){
			dialogOpt = {
				buttons: [{ 
					text: getLang('print'), 
					click: function() { 
						printDialog($(this));
			
					}
				}, { 
					text: getLang('close'), 
					click: function() { 
						$(this).dialog('close');
					}
				}],
				width:600,
				height:400,
				minim:false,
				div: 'dialog_recete',
				title: 'recete'
			}
			openHtmlDialog(answer.recete, dialogOpt)
		}
	}
	getModuleJson(savePayment);
}

function submitIncomeList($but){
	var $form =$but.parents('form');
	var module = {
		name : 'ingoing',
		data : 'incomes&list&'+ $form.serialize(),
		div : '#app_list_tbody',
		title: getLang('applications'),
		callback : function(){
			var total = 0;
			$('#app_list_tbody tr').each(function() {
				var $td = $(this).find('td:last');
				total =  total + parseInt($td.html());
			});
			$('#applications_total').html(total);
		}
	}
	loadModule(module);
}
		
function toogleBanksOpts($select){
	var $form = $select.parents('form');
	var $bankSelectDiv = $form.find('.banks_opts');
	if($select.val() == 'cash'){
		$bankSelectDiv.hide();
	} else {
		$bankSelectDiv.show();
	}
}