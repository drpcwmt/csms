// borrow.js
function iniBorrowForm(){
	if($('#borrow_form').length > 0){
		setBookAutocomplete('#borrow_form #book_name');
		if($('#borrow_form #con_name').val() == ''){
			setStudentAutocomplete('#borrow_form #con_name');
		}
		var val = parseInt($('#serial_list .serial_stat').attr('value'));
		$('#serial_list .serial_stat').progressbar({ value: val });
		//for direct borrow from book
		$('#con_name').focus(function(){
			refreshConId();
		});
	}
}

function refreshConId(){
	//$('#con_name').autocomplete( "destroy" );
	var con = $('#con').val();
	$('#con_name').val( "" );
	if(con == 'std'){
		setStudentAutocomplete('#con_name');
	} else if(con == 'emp'){
		setEmployerAutocomplete('#con_name');
	}
}

function searchBookInfos(){
	if($('#book_id').val() != ''){
		MS_jsonRequest('books', 'book_info=&book_id='+$('#book_id').val(), 'formatBookInfo(ans); ');
	} else if($('#isbn').val() != ''){
		MS_jsonRequest('books', 'book_info=&isbn='+$('#isbn').val(), 'formatBookInfo(ans)');
	} else {
		MS_alert('<h2>'+getLang('book_name_error')+'</h2>');
		return false;
	}
}

function formatBookInfo(infos){
	$('#borrow_form #book_name').val(infos.name);
	$('#borrow_form #isbn').val(infos.isbn);
	$('#borrow_form #book_id').val(infos.id);
	$('#borrow_form #book_code').val(infos.id+'-');
	var catStr = infos.cat_name;
	if(infos.cat_sub_name != '') {
		 catStr += ' > '+infos.cat_sub_name;
	}
	$('#borrow_form #book_cat').val(catStr);

	var limit = infos.def_borrow_limit;
	var today  = new Date();
    var curr_date = today.getDate();
    var curr_month = today.getMonth(); 
    var curr_year = today.getFullYear();

	var returnDate =new Date();
	returnDate.setFullYear(curr_year , curr_month , (curr_date+parseInt(limit)));
	$('#borrow_form #max_date').val(dateFormat(returnDate, 'dd/mm/yyyy'));
	
	// load Serials
	var module= {};
	module.name = 'borrow';
	module.title = getLang('borrow');
	module.data = 'book_serial&book_id='+infos.id;
	module.div = '#serial_div';
	loadModuleToDiv(new Array(module), 'initSerialStat()')
}


function initSerialStat(){
	$('#borrow_form #serial_list li.ui-state-default:first').click();
	$('#borrow_form .serial_stat').each(function(){
		var val = parseInt($(this).attr('value'));
		$(this).progressbar({ value: val });
	});
}

function selectSerial(li){
	$(li).find('input[type="radio"]').each(function(){
		if($(this).attr('checked') && $(this).attr('checked') == 'checked'){
			$(this).removeAttr('Checked'); 
		} else {
			$(this).attr('checked','checked'); 
		}
	})
}

function searchBookCode(){
	var bookCode = $('#book_code').val();
	if(bookCode.indexOf('-') > 0 ){
		var b = bookCode.split('-');
		var bookId = b[0];
		var serial = b[1];
		var module ={};
		module.name = 'borrow';
		module.div = '#module_borrow';
		module.data = 'new_borrow&book_id='+bookId+'&serial='+serial;
		loadModuleToDiv(new Array(module), 'iniBorrowForm()');
	} else {
		MS_alert('<h2>'+getLang('book_code_error')+'</h2>');
		return false;
	}
}

function submitBorrowForm(callback){
	if(!validateForm('#borrow_form')){
		return false;
	} else {
		var data =$('#borrow_form').serialize()
		MS_jsonRequest('borrow', data, callback)
	}
}

// Return 
function iniReturnForm(){
	var value = $('#return_stat_slider').attr("value");
	var bookCode =  $('#return_stat_slider').attr("rel");
	var b = bookCode.split('-');
	var bookId = b[0];
	var serial = b[1];
	$('#return_stat_slider').slider({
		orientation: "horizontal",
		value: value,
		range: "min",
		min: 0,
		max: 5,
		step: 1,
		animate: true,
		slide: function( event, ui ) {
			var stat = getStat(ui.value );
			$( this ).css("background-color", stat[1]);
			$( this ).next( ".stat_span" ).css("color", stat[1]).html(stat[0] + " " + (ui.value*20) + "%");
		},
		change: function(event, ui) {
			$('#return_stat').val(ui.value);
			MS_mysqlAjaxUpdate("LIBMS_Database", "book_serials", "stat="+ui.value, "serial="+serial+"&book_id="+bookId, "");
		}
	})
}

function submitReturnForm(borrowId){
	MS_jsonRequest('borrow', $('#borrow_return_form').serialize(), "$('#MS_dialog-return_book').dialog('close')")
}

function returnBookByCode(borrowId){
	var module= {};
	module.name='borrow';
	module.data='borrowreturn&borrow_id='+borrowId;
	module.title=getLang('return_book');
	module.div = 'MS_dialog-return_book';
	var buts = [{
		text:getLang('save'),
		click: function(){
			submitReturnForm(borrowId);
		}
	},{
		text: getLang('close'),
		click: function(){
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buts, false, 500, 400, true, 'iniReturnForm()')
}
	