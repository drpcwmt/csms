//Transaction JS

function initTransactionForm($form){
	initAutocompleteToFrom($form);
	
	// Focus
	$form.find('input[name="name[]"]').focus(function(){
		$(this).select();
		setProductsAutocomplete($(this));
	})
	
	$form.find('input[name="quantity[]"]').focus(function(){
		$(this).select();
	});
	$form.find('input[name="price[]"]').focus(function(){
		$(this).select();
	});
		
	$form.find('input[name="name[]"]').change(function(){
		if($(this).attr('term') && $(this).attr('term') !=''){
			var $tr = $(this).parents('tr').eq(0);
			$tr.find('input[name="item_id[]"]').val($(this).attr('term'));
			getProductData($(this));
		}
	});
	
	// Update
	$form.find('input[name="quantity[]"]').change(function(){
		recalculateTransaction($form);
	});
	$form.find( 'input[name="price[]"]').change(function(){
		recalculateTransaction($form);
	});
	
	
		// Keyboard shortcuts
	setKeyboardShortcuts('f10', function($focus){addPayment($focus.parents('form.transaction_form'))});
	setKeyboardShortcuts('return', function($focus){setItemFocus($focus)});
	
	var $newItem = $form.find("input[value='']:visible:first");
	$newItem.focus();
}

function setItemFocus($but){
	var $table = $but.parents('.items_list');
	var $newItem = $table.find("input[value='']:visible:first");
	$newItem.focus();
}

function initAutocompleteToFrom($form){
	var $toNameInp = $form.find('input[name="to_name"]');
	if($toNameInp.length && $toNameInp.hasClass('hidden') == false){
		if($toNameInp.is(':data(autocomplete)')){
			$toNameInp.autocomplete('destroy');
		}
		if($form.find('input[name="to"]').val() == 'c'){
			setClientsAutocomplete($toNameInp);
		} else if($form.find('input[name="to"]').val() == 'p'){
			setStoresAutocomplete($toNameInp);
		} else if($form.find('input[name="to"]').val() == 'w'){
			setWarhousesAutocomplete($toNameInp);
		} else if($form.find('input[name="to"]').val() == 's'){
			setSuppliersAutocomplete($toNameInp);
		} 

		$toNameInp.focus(function(){
			$(this).val('');
			$FromNameInp.next('input[name="to_id"]').val('');
			$(this).autocomplete('search');
		});
	}
	
	var $FromNameInp = $form.find('input[name="from_name"]');

	if($FromNameInp.length && $FromNameInp.hasClass('hidden') == false){
		if($FromNameInp.is(':data(autocomplete)')){
			$FromNameInp.autocomplete('destroy');
		}
		if($form.find('input[name="from"]').val() == 'c'){
			setClientsAutocomplete($FromNameInp);
		} else if($form.find('input[name="from"]').val() == 'p'){
			setStoresAutocomplete($FromNameInp);
		} else if($form.find('input[name="from"]').val() == 'w'){
			setWarhousesAutocomplete($FromNameInp);
		} else if($form.find('input[name="from"]').val() == 's'){
			setSuppliersAutocomplete($FromNameInp);
		} 

		$FromNameInp.focus(function(){
			$(this).val('');
			$FromNameInp.next('input[name="to_id"]').val('');
			$(this).autocomplete('search');
		});
	}
}

function getProductData($inp){
	var $form = $inp.parents('form.transactions_form').eq(0);
	var $tr = $inp.parents('tr').eq(0);
	var id = $tr.find('input[name="item_id[]"]').val();
	var module = {
		name: 'products',
		param: 'data&prod_id='+id,
		post: '',
		muted: true,
		async:true,
		callback: function(answer){
			var price = ($form.find('input[name="to"]').val()=='s' || $form.find('input[name="from"]').val()=='s') ? answer.buy_price : answer.price;
			$tr.find('input[name="name[]"]').val(configFile.uilang=='ar' ? answer.name_rtl : answer.name_ltr);
			$tr.find('input[name="quantity[]"]').val(configFile.selling_item=='item' ? 1 : answer.contener);
			$tr.find('input[name="price[]"]').val(price);
			$tr.find('input[name="total[]"]').val(configFile.selling_item=='item' ? price : (price * answer.contener));
			$tr.find('input[name="quantity[]"]').focus().select();
			$tr.find('button[action="openProduct"]').attr('prodid', answer.id);
			$tr.find('button[action="openProduct"]').show();
			barcode.play();
			recalculateTransaction($form);
		}
	}
	getModuleJson(module);
}

function recalculateTransaction($form){
	var total = 0;
	var $tbody = $form.find('.items_list tbody');
	
	$tbody.find('tr').each(function(){
		$tr = $(this);
		if($tr.find('input[name="item_id[]"]').val() != ""){
			var quantity = $tr.find('input[name="quantity[]"]').val();
			var price = $tr.find('input[name="price[]"]').val();
			var itemTotal = parseInt(quantity) * parseInt(price);
			$tr.find('input[name="total[]"]').val(itemTotal);
			total = total + itemTotal;
		}
	})
	
	$form.find('input[name="total"]').val(total);
	
}

function nextTransactionField(){
//	setItemFocus();
	return false;
}

function addNewItem(inp){
	if(inp instanceof jQuery){
		$inp = inp;
	} else {
		var $inp = $(inp);
	}
	var $tr = $inp.parents('tr').eq(0);
	$tr.find('button[action="removeTransactionItem"]').show();
	var $tbody = $inp.parents('tbody').eq(0);
	var $form = $inp.parents('form').eq(0);

	if($tr.index() +1  == $tbody.find('tr').length){
		$tbody.append( '<tr>'+$tr.html()+'</tr>');
		var $nextTr = $tr.next('tr');
		$nextTr.find('input').removeClass('MS_formed_update');
		$nextTr.find('button').removeClass('MS_formed');
		
			// Name				
		setProductsAutocomplete($nextTr.find('input[name="name[]"]'));
			
			//  Update
		$nextTr.find('input[name="quantity[]"]').change(function(){
			recalculateTransaction($form);
		});
		$nextTr.find('input[name="price[]"]').change(function(){
			recalculateTransaction($form);
		});
			
			// focus
		$nextTr.find('input[name="quantity[]"]').focus(function(){
			$(this).select();
		});
		$nextTr.find('input[name="price[]"]').focus(function(){
			$(this).select();
		});
		
	} 
	initiateJquery();
}

function removeTransactionItem($but){
	var $form = $but.parents('form').eq(0);
	var $tr = $but.parents('tr').eq(0);
	$tr.fadeOut().remove();
	recalculateTransaction($form);
}

function updateTransactionTo($select){
	var $opt = $select.find('option:selected');
	$select.nextAll('input[name="to"]').val($opt.attr('con'));
	$select.nextAll('input[name="to_id"]').val($opt.attr('conid'));
}
	
function updateTransactionFrom($select){
	var $opt = $select.find('option:selected');
	$select.nextAll('input[name="from"]').val($opt.attr('con'));
	$select.nextAll('input[name="from_id"]').val($opt.attr('conid'));
}
	