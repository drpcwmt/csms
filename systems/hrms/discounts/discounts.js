function openDiscounts(){
	var module = {
		name: 'discounts',
		data: '',
		title: getLang('discounts'),
		div: '#home_content'
	}
	loadModule(module);
}

function openDiscountByJob($btn){
	var jobId = $btn.attr('job_id') ? $btn.attr('job_id') : $('#discount_job_id').val();
	var module = {
		name:'discounts',
		data: 'discountbyjob='+jobId+'&'+$('#discount_form').serialize(),
		title:getLang('discounts'),
		div: '#discount_daily_tbody',
		callback: function(){
			$('#discount_job_id').val(jobId);
		}
	}
	loadModule(module);
}

function addDiscount($btn){
	var jobId = $('#discount_job_id').val();
	var module ={};
	module.name= 'discounts';
	module.title = getLang('discounts');
	module.data= 'new_form';
	module.type= 'GET';
	module.cache= false;
	module.div = 'add_discount_dialog';
	var dialogOpt = {
		width:600,
		height: 460,
		modal:false,
		maxim:true,
		minim:true,
		cache:false,
		modal:false,
		buttons: [{ 
			text: getLang('add'), 
			click: function() { 
				var module = {
					name: 'discounts',
					param: 'save',
					post: $('#discount_add_form').serialize(),
					callback: function(){
						var $vBut = $('<button>').attr('job_id', jobId);
						openDiscountByJob($vBut)
					}
				}
				getModuleJson(module);
				 
			}
		}, { 
			text: getLang('close'), 
			click: function() { 
				$(this).dialog('close');
			}
		}],
		callback: function(){
			setEmployerAutocomplete('#discount_add_form input[name="emp_name"]', 'job_code='+jobId);
			setDefAutocomplete($('#discount_add_form input[name="comments"]'), configFile.database, 'discounts');
		}
	}
	openAjaxDialog(module, dialogOpt)
}

function deleteDiscount($btn){
	var disId = $btn.attr('dis_id');
	var module = {
		name: 'discounts',
		param: 'delete',
		post: 'id='+disId,
		callback: function(){
			var $tr = $btn.parents('tr').eq(0);
			$tr.fadeOut().remove();	
		}
	}
	getModuleJson(module);
}

function updateDiscountsReport($select){
	var $form = $select.parents('form');
	var module= {
		name:'discounts',
		data:'report&'+$form.serialize(),
		div: '#discounts_list_tbody',
		title:getLang('absents')
	}
	loadModule(module);
}