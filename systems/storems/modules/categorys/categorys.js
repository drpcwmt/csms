function printTree(){
	var module = {
		name: 'categorys',
		param: 'printtree',
		post: '',
		callback: function(answer){
			var $div = $('<div></div>');
			$div.html(answer.html);
			printTag($div);
		}
	}
	getModuleJson(module);
}

function newCat($btn){
	var html = '<form><table cellspacing="0"><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('title')+': </label></td><td><input id="cat_name"  name="title" type="text" class="input_double required" /></td></tr><tr><td><label class="label reverse_align" style="width:120px; float:left">'+getLang('code')+': </label></td><td><input name="id" type="text" class="input_half" /></td></tr></table></form>';

	var dialogOpt = {
		width:470,
		height:200,
		div:'new_category_dialog',
		title:getLang('new_cat'),
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var $form = $('#new_category_dialog form');
				if(validateForm($form)){
					var module = {
						name: 'categorys',
						param: 'save',
						post: $('#new_category_dialog form').serialize(),
						async:false,
						callback: function(answer){
							$('#new_category_dialog').dialog('close');
						}
					}
					getModuleJson(module);
				} else {
					return false;
				}
			}
		},{ 
			text: getLang('cancel'), 
			click: function() { 
				$(this).dialog('close');
			}
		}]
	}
	openHtmlDialog(html, dialogOpt);
	initiateJquery();
	setCatAutocomplete($('#new_category_dialog #cat_name'));
}

function changeView($but){
	var $subButton = $('#cats_list li.ui-state-active');
	openCategory($subButton)
}

function openCategory($but){
	var view = $('a[action="changeView"]').attr('rel');
	var catId = $but.attr('cat_id');
	var module = {
		name: 'categorys',
		data: 'cat_id='+catId+(view ? '&view='+view : ''),
		title: getLang('category'),
		div: 'category_details_div'
	}
	loadModule(module);
}

function saveCat($btn){
	var module = {
		name: 'categorys',
		param: 'save',
		post: $('#category_details_div form').serialize(),
		title: getLang('category')
	}
	getModuleJson(module);
}

function openSubCat($btn){
	var view = $('a[action="changeView"]').attr('rel');
	var subId = $btn.attr('sub_id');
	var module = {
		name: 'categorys',
		data: 'sub_id='+subId+(view ? '&view='+view : ''),
		title: getLang('category'),
		div: 'category_details_div'
	}
	loadModule(module);
}

function setSubcatOptions($select){
	var catId = $select.val();
	var $form = $select.parents('form');
	var module = {
		name: 'categorys',
		data: 'sublist&cat_id='+catId,
		title: getLang('category'),
		div: $form.find('select[name="sub_id"]')
	}
	loadModule(module);
}

function newSubCat($btn){
	var catId = $btn.attr('cat_id');
	var module = {
		name: 'categorys',
		title: getLang('categorys'),
		data: 'newsub&cat_id='+catId+($btn.attr('sub_id') ? '&sub_id='+$btn.attr('sub_id') : ''),
		type: 'GET',
		div: 'new_sub'
	}
	
	var dialogOpt = {
		width:600,
		height:400,
		title:getLang('new'),
		maxim:true,
		minim:true,
		buttons: [{ 
			text: getLang('save'), 
			click: function() { 
				var savemodule = {
					name: 'categorys',
					param: 'savesub',
					post: $('#new_sub form').serialize(),
					title: getLang('category')
				}
				getModuleJson(savemodule);
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
	