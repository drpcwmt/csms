// books.js
/**************initiated **************/
function iniBookForm(){
	$("a.addClassBut" )
	.button({
		icons: {
			primary: "ui-icon-plus"
		},
		text: false
	})
	.removeClass( "ui-corner-all" )
		
	setAutorsAutocomplete('#author_name');
	setVendorAutocomplete('#vendor_name');
	setAutocompleteCat('#cat_name');
	setAutocompleteSubCat('#cat_sub_name')

	if($("#cat").val() != '') {
		var newSource = 'index.php?common=autocomplete&t=cats_sub&f=name,id&w=name&p=cat_id='+$("#cat").val();
		$('#cat_sub_name').autocomplete({ source : newSource});
	}
	
	$('#cat_name').blur(function(){
		if($("#cat").val() != '') {
			var newSource = 'index.php?common=autocomplete&t=cats_sub&f=name,id&w=name&p=cat_id='+$("#cat").val();
			$('#cat_sub_name').autocomplete({ source : newSource});
		} else {
			$('#cat_sub_name').autocomplete('destroy');
		}
	});
	
	$('#cat_sub_name').blur(function(){
		if($("#cat_sub").val() != ''){
			$('#cat_code').attr('disabled', 'disabled');
			MS_jsonRequest('books', 'codefromsub='+$("#cat_sub").val(), "$('#cat_code').val(ans.code)")
		} else {
			$('#cat_code').removeAttr('disabled');
			$('#cat_code').val('');
		}
	});
	
	$('#cat_name, #cat_sub_name, #author_name, #vendor_name').focus(function(){
		$(this).autocomplete('search');
	})
	if($("#book_id").val() != ''){
		reloadBookSerial($("#book_id").val());
	}
}


function initBookStatSliders(){
	$('#book_serial_table .slider').each(function(){
		var value = $(this).attr("value");
		var serial = $(this).attr("rel");
		var bookId = $('#book_detail_form #book_id').val();
		$(this).slider({
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
				MS_mysqlAjaxUpdate("LIBMS_Database", "book_serials", "stat="+ui.value, "serial="+serial+";book_id="+bookId, "");
			}
		});
	});
		
}

function getStat(st){
	if(st > 4){
		return new Array(getLang('perfect'), "#0c0");
	} else if(st > 3){
		return new Array(getLang('good'), "#CC3");
	} else if(st > 2){
		return new Array(getLang('average'), "#F63");
	} else if(st > 1){
		return new Array(getLang('bad'), "#C03");
	} else {
		return new Array(getLang('unuseable'), "#C03");
	}
}

function submitbookForm(){
	if(validateForm('#book_detail_form')){
		var data =$('#book_detail_form').serialize()
		MS_jsonRequest('books', data, "loadModule('books', 'book_id='+ans.id, getLang('book'), 'iniBookForm()');")
	} 
}


function addNewSerials(bookId){
	var count = $('#serials_count').val();	
	var data = 'addserials=&count='+count+'&book_id='+bookId;
	MS_jsonRequest('books', data, "reloadBookSerial("+bookId+")")
}

function reloadBookSerial(bookId){
	var module = {};
	module.name = 'books';
	module.title = getLang('books');
	module.data = 'serialtable&book_id='+bookId;
	module.div = '#book_serials_div';
	loadModuleToDiv(new Array(module), 'initBookStatSliders()')
}

// history
function openHistoryDialog(bookId, serial){
	var module ={};
	module.name= 'borrow';
	module.title = getLang('borrow_list');
	module.data= 'borrowlist&book_id='+bookId+'&serial='+serial;
	module.div = 'MS_dialog_'+module.name;
	var buttons = [{ 
		text: getLang('print'), 
		click: function() { 
			print_pre('#MS_dialog_'+module.name);
		}
	},{ 
		text: getLang('close'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createAjaxDialog(module, buttons, false, 800, 500, true, '')
}

function openBorrowDialog(bookId, serial){
	var module ={};
	module.name= 'borrow';
	module.title = getLang('borrow');
	module.data= 'new_borrow&book_id='+bookId+'&serial='+serial;
	module.div = 'MS_dialog_'+module.name;
	var buttons = [{ 
		text: getLang('save'), 
		click: function(){submitBorrowForm('$("#MS_dialog_'+module.name+'").dialog("close");reloadBookSerial('+bookId+')');}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	
	createAjaxDialog(module, buttons, false, 800, 450, false, "iniBorrowForm()")
}

// delete serial
function deleteSerial(bookId, serial){
	MS_mysqlAjaxDelete('LIBMS_Database', 'book_serials', 'book_id='+bookId+'&serial='+serial, 'reloadBookSerial('+bookId+')');	
}

// categorys
function deleteCat(catId){
	var html = '<h3>'+getLang('sure_to_delete')+'</h3>';

	var buttons = [{ 
		text: getLang('delete'), 
		click: function() { 
			MS_mysqlAjaxDelete('LIBMS_Database', 'cats', 'id='+catId, '$("#cat_name_'+catId+', #cat_div_'+catId+'").fadeOut().remove(); $("#MS_dialog_delete_cat").dialog("close")')
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('delete_cat', getLang('delete'),  html, 300, 200, buttons, true);
	iniMsUi();
}

function deleteSub(subId){
	var html = '<h3 class="ui-corner-all ui-state-highlight">'+getLang('sure_to_delete')+'</h3>';

	var buttons = [{ 
		text: getLang('delete'), 
		click: function() { 
			MS_mysqlAjaxDelete('LIBMS_Database', 'cats_sub', 'id='+subId, '$("#sub_tr_'+subId+'").fadeOut().remove(); $("#MS_dialog_delete_cat").dialog("close")')
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('delete_cat', getLang('delete'),  html, 300, 200, buttons, true);
	iniMsUi()
}

function renameCat(catId){
	var html = '<fieldset><legend>'+getLang('new_name')+'</legend><input type="text" name="name" id="rename_input" /></fieldset>';

	var buttons = [{ 
		text: getLang('rename'), 
		click: function() { 
			MS_mysqlAjaxUpdate('LIBMS_Database', 'cats', 'name='+$('#rename_input').val(), 'id='+catId, 
				'$("#cat_name_'+catId+'").html(\'<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>'+$('#rename_input').val()+'\'); $("#MS_dialog_rename_cat").dialog("close")')
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('rename_cat', getLang('rename'),  html, 300, 200, buttons, true);
	iniMsUi();
}

function renameSub(subId, code, name){
	var html = '<form><table border="0" cellspacing="0"><tr><td><label class="label">'+getLang('name')+'</label></td><td><input id="sub_name" name="name" type="text" value="'+name+'" /></td></tr><tr><td><label class="label">'+getLang('code')+'</label></td><td><input name="code" type="text" id="sub_code" value="'+code+'"/></td></tr></form>';

	var buttons = [{ 
		text: getLang('rename'), 
		click: function() { 
			MS_mysqlAjaxUpdate('LIBMS_Database', 'cats_sub', $('#MS_dialog_rename_cat form').serialize(), 'id='+subId, '$("#sub_tr_'+subId+' .name_td").html("'+$('#sub_name').val()+'");$("#sub_tr_'+subId+' .code_td").html("'+$('#sub_code').val()+'"); $("#MS_dialog_rename_cat").dialog("close")')
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('rename_cat', getLang('rename'),  html, 300, 200, buttons, true);
}

function newCategorysCat(){
	var html = '<form><table border="0" cellspacing="0"><tr><td><label class="label">'+getLang('name')+'</label></td><td><input id="new_cat_name" name="name" type="text" role="id" class="autocomplete" data="cat"/></td></tr><tr><td><label class="label">'+getLang('borrow_limit')+'</label></td><td><input id="def_borrow_limit" name="def_borrow_limit" type="text" /></td></tr></form>';
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
		//alert('ok')
			if($('#new_cat_name').attr('term') && $('#new_cat_name').attr('term') != ''){
				$('#MS_dialog_new_cat .ui-state-error').hide();
				$('#MS_dialog_new_cat').append('<div class="ui-state-error ui-corner-all">'+getLang('allready_exists')+'</div>');
			} else {
				MS_mysqlAjaxInsert('LIBMS_Database', 'cats', $('#MS_dialog_new_cat form').serialize(), 'appendInsertedCat(ans.id); $("#MS_dialog_new_cat").dialog("close")');
			}
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('new_cat', getLang('new_cat'), html, 300, 220, buttons, true);
	iniAutoComplete();
	//setAutocompleteCat("#new_cat_name", '#new_cat_id');
}

function appendInsertedCat(catId){
	module = {};
	module.name = 'books'
	module.div = '';
	module.data = 'newinsertedcat='+catId;
	module.title = getLang('categorys')
	MS_aJaxRequest(module, 'GET', false, 'openCategorys()');
}

function newCategorysSub(catId){
	var html = '<form><input type="hidden" name="cat_id" value="'+catId+'" /><table border="0" cellspacing="0"><tr><td width="120"><label class="label">'+getLang('name')+'</label></td><td><input id="new_sub_name" name="name" type="text" /></td></tr><tr><td><label class="label">'+getLang('code')+'</label></td><td><input name="code" type="text" id="sub_code" /></td></tr></form>';
	var buttons = [{ 
		text: getLang('save'), 
		click: function() { 
			MS_mysqlAjaxInsert('LIBMS_Database', 'cats_sub', $('#MS_dialog_new_cat form').serialize(), 'appendInsertedSub("'+catId+'", ans.id); $("#MS_dialog_new_cat").dialog("close")');
		}
	},
	{ 
		text: getLang('cancel'), 
		click: function() { 
			$(this).dialog('close');
		}
	}];
	createHtmlDialog('new_cat', getLang('new_cat_sub'), html, 300, 200, buttons, true);
	setAutocompleteSubCat("#new_sub_name");
	iniMsUi();
}

function appendInsertedSub(catId, subId){
	var tr = '<tr id="sub_tr_'+catId+'"><td ><a title="Rename" onclick="renameSub('+subId+')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"><span class="ui-icon ui-icon-pencil"></span></a></td><td ><a title="Delete" onclick="deleteSub('+subId+')" class="ui-corner-all ui-button-icon-only ui-button hoverable ui-state-default" style="height: 20px;"><span class="ui-icon ui-icon-close"></span></a></td><td class="code_td">'+$("#MS_dialog_new_cat #sub_code").val()+'</td><td class="name_td">'+$("#MS_dialog_new_cat #new_sub_name").val()+'</td><td >0</td><td >0</td><td >0</td><td >0</td><td >0</td></tr>';
	$('#cat_table_'+catId+' tbody').append(tr);
}