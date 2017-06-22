// Product JS

function newProduct($btn){
	var subId = $btn.attr('cat_id');
	var productModule = {
		name: 'products',
		title: getLang('item'),
		data: 'newform&sub_id='+subId,
		type: 'GET',
		div: 'product_new'
	}
	
	var dialogOpt = {
		width:800,
		height:400,
		title:getLang('item'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveProduct('new');
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
	}
	
	openAjaxDialog(productModule, dialogOpt)
}


	
	
function openProduct($but){
	var prodId = $but.attr('prodid');
	var productModule = {
		name: 'products',
		title: getLang('item'),
		data: 'prod_id='+prodId,
		type: 'GET',
		div: 'product_'+prodId
	}
	
	var dialogOpt = {
		width:800,
		height:400,
		title:getLang('item'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				saveProduct(prodId);
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			var $dialog = $('#product_'+prodId);
			$dialog.dialog("option", "title", $('#product_'+prodId+' input[name="name_ar"]').val());
			$dialog.find('form .ena_auto').focus(function(){
				setDefAutocomplete($(this), configFile.MySql_Database, 'products');	
				$(this).autocomplete('search');
			});
		}
	}
	
	openAjaxDialog(productModule, dialogOpt)
}

function saveProduct(prodId){
	if(validateForm('#product_'+prodId+ ' form')){
		var module = {
			name: 'products',
			param: 'save',
			post: $('#product_'+prodId+ ' form').serialize(),
			async:false,
			callback: function(answer){
				$('#product_'+prodId).dialog('close');
				$(".label-prod-"+prodId).html($('#product_'+prodId+' input[name="name_ar"]').val())
			}
		}
		getModuleJson(module);
	} else {
		return false;
	}
}




