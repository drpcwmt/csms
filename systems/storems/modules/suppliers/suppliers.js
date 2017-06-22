function openSupplier($but){
	var supplierId = $but.attr('supplierid');
	var productModule = {
		name: 'suppliers',
		title: getLang('suppliers'),
		data: 'sup_id='+supplierId,
		type: 'GET',
		div: 'supplier_'+supplierId,
		callback: function(){
			 initSuppliers(supplierId);
		}
	}
	
	var dialogOpt = {
		width:700,
		height:600,
		title:getLang('suppliers'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveSupplier(supplierId);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			var $dialog = $('#supplier_'+supplierId);
			$dialog.dialog("option", "title", $('#supplier_'+supplierId+' form[name="suppliers"] input[name="name"]').val());
			$dialog.find('form .ena_auto').focus(function(){
				$(this).val('');
				$(this).autocomplete('search');
			});
		}
	}
	
	openAjaxDialog(productModule, dialogOpt)
}

function saveSupplier(supplierId){
	var $form = $('#supplier_'+supplierId+ ' form');
	if($form.find('input.this_form_modified').val() == 1){
		if(validateForm('#supplier_'+supplierId+ ' form')){
			var module = {
				name: 'suppliers',
				param: 'save',
				post: $form.serialize(),
				async:false,
				callback: function(answer){
					$('#supplier_'+supplierId).dialog('close');
					$(".label-supplier-"+supplierId).html($('#supplier_'+supplierId+' input[name="name"]').val())
				}
			}
			getModuleJson(module);
		} else {
			return false;
		}
	} else {
		$('#supplier_'+supplierId).dialog('close');
	}
}

function newSupplier(){
	var module = {
		name: 'suppliers',
		title: getLang('suppliers'),
		data: 'newform',
		type: 'GET',
		div: 'supplier_new'
	}
	
	var dialogOpt = {
		width:500,
		height:300,
		title:getLang('suppliers'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveSupplier('new');
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	
	openAjaxDialog(module, dialogOpt)
}

function initSuppliers(supplierId){		
	var $form = $('#supppliers_products_form-'+supplierId);
	
	$form.find('input[name="name[]"]').focus(function(){
		$(this).select();
		setProductsAutocomplete($(this));
	})
	
	$form.find('input[name="name[]"]').change(function(){
		if($(this).attr('term') && $(this).attr('term') !=''){
			var $tr = $(this).parents('tr').eq(0);
			$tr.find('input[name="item_id[]"]').val($(this).attr('term'));
			getSupplierProductData($(this));
		}
	});
	
	$form.find('input[name="quantity[]"], input[name="price[]"]').focus(function(){
		$(this).select();
	});
		
	
	// Shortcuts
	var $addTr = $form.find('fieldset tbody tr:first');
	$addTr.bind('keydown', function(e){
		if(e.which == 13) {
		//	addNewSupplierProduct($addTr.find('button'));
		//	return false;
		}
	});
	
	var $newItem = $form.find("input[value='']:visible:first");
	$newItem.focus();
	
	// Products list
	$form.find('.products_list input').change(function(){
		var $tr = $(this).parents('tr').eq(0);
		var data = {
			prod_id: $tr.find('input[name="item_id"]').val(),
			price: $tr.find('input[name="price"]').val(),
			barcode: $tr.find('input[name="barcode"]').val(),
			sup_id: $form.find('input[name="sup_id"]').val()
		}
		var module = {
			name: 'suppliers',
			param: 'addprod',
			post: $.param(data),
			muted: true,
			async:true
		}
		getModuleJson(module);
	});
}

function addNewSupplierProduct($but){
	var $tr = $but.parents('tr').eq(0);
	var $form = $but.parents('form').eq(0);
	var data = {
		prod_id: $tr.find('input[name="item_id"]').val(),
		price: $tr.find('input[name="price"]').val(),
		barcode: $tr.find('input[name="barcode"]').val(),
		sup_id: $form.find('input[name="sup_id"]').val()
	}

	var module = {
		name: 'suppliers',
		param: 'addprod',
		post: $.param(data),
		callback: function(answer){
			var $tbody = $form.find('.products_list tbody');
			$tbody.prepend(
				'<tr><td><button type="button" action="removeSupplierItem"  prodid="'+data.prod_id+'" class="ui-state-default ui-corner-all hoverable circle_button"><span class="ui-icon ui-icon-close"></span></button></td><td><button type="button" action="openProduct" prodid="'+data.prod_id+'" class="ui-state-default ui-corner-all hoverable circle_button"><span class="ui-icon ui-icon-close"></span></button></td><td><input type="hidden" name="item_id" value="'+data.prod_id+'" />'+$tr.find('input[name="item_id"]').val()+'</td><td>'+$tr.find('input[name="name"]').val()+'</td><td style="padding:0"><input type="text" name="price" class="input_half no-corner" value="'+$tr.find('input[name="price"]').val()+'" /></td><td style="padding:0"><input type="text" name="barcode" class="input_half no-corner" value="'+$tr.find('input[name="barcode"]').val()+'" /></td><td>0</td></tr>'
			)
			$tr.find('input').val('');
			$tr.find('input[name="item_id"]').focus().select();
			var $newRow = $tbody.find('tr:first');
			 $newRow.find('input').change(function(){
				var data = {
					prod_id: $newRow.find('input[name="item_id"]').val(),
					price: $newRow.find('input[name="price"]').val(),
					barcode: $newRow.find('input[name="barcode"]').val(),
					sup_id: $form.find('input[name="sup_id"]').val()
				}
				var module = {
					name: 'suppliers',
					param: 'addprod',
					post: $.param(data),
					muted: true,
					async:true
				}
				getModuleJson(module);
			});
		}
	}
	getModuleJson(module);
}

function getSupplierProductData($inp){
	var $form = $inp.parents('form').eq(0);
	var $tr = $inp.parents('tr').eq(0);
	var id = $tr.find('input[name="item_id"]').val();
	var module = {
		name: 'products',
		param: 'data&prod_id='+id,
		post: '',
		muted: true,
		async:true,
		callback: function(answer){
			$tr.find('input[name="name"]').val(configFile.uilang=='ar' ? answer.name_rtl : answer.name_ltr);
			$tr.find('input[name="price"]').val(answer.buy_price);
			$tr.find('input[name="code"]').focus().select();
			barcode.play();
		}
	}
	getModuleJson(module);
}

function removeSupplierItem($but){
	var prodId = $but.attr('prodid');
	var $form = $but.parents('form').eq(0);
	var supId= $form.find('input[name="sup_id"]').val();
	var module = {
		name: 'suppliers',
		param: 'delete_prod',
		post: 'prod_id='+prodId+'&sup_id='+supId,
		muted: true,
		callback: function(answer){
			var $tr = $but.parents('tr').eq(0);
			$tr.fadeOut().remove();
		}
	}
	getModuleJson(module);
}
